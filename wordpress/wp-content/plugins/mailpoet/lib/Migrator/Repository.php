<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Repository {
  const MIGRATIONS_LEVEL_APP = 'app';
  const MIGRATIONS_LEVEL_DB = 'db';

  /** @var string */
  private $migrationsDir;

  /** @var string */
  private $templateFile;

  public function __construct() {
    $this->migrationsDir = __DIR__ . '/../Migrations';
    $this->templateFile = __DIR__ . '/{level}MigrationTemplate.php';
  }

  public function getMigrationsDir(): string {
    return $this->migrationsDir;
  }

  /** @return array{name: string, path: string} */
  public function create(string $level): array {
    if (!in_array($level, [self::MIGRATIONS_LEVEL_APP, self::MIGRATIONS_LEVEL_DB], true)) {
      throw MigratorException::invalidMigrationLevel($level);
    }
    $ucFirstLevel = ucfirst($level);
    $templateFile = str_replace('{level}', $ucFirstLevel, $this->templateFile);
    $template = @file_get_contents($templateFile);
    if (!$template) {
      throw MigratorException::templateFileReadFailed($templateFile);
    }
    $name = $this->generateName($level);
    $migration = str_replace('{level}', $ucFirstLevel, 'class {level}MigrationTemplate ');
    $migration = str_replace($migration, "class $name ", $template);
    $path = "$this->migrationsDir/$ucFirstLevel/$name.php";
    $result = @file_put_contents($path, $migration);
    if (!$result) {
      throw MigratorException::migrationFileWriteFailed($path);
    }
    return [
      'name' => $name,
      'path' => $path,
    ];
  }

  /**
   * Array of migration filenames and types.
   * Db migrations are loaded first, then app migrations. This ensures that Db migrator is run before app migrations
   * @return array<array{level: string, name: string}>
   */
  public function loadAll(): array {
    $migrations = array_merge(
      $this->loadForLevel(self::MIGRATIONS_LEVEL_DB),
      $this->loadForLevel(self::MIGRATIONS_LEVEL_APP)
    );
    $migrationNames = array_column($migrations, 'name');
    $duplicateNames = array_diff_assoc($migrationNames, array_unique($migrationNames));
    if (!empty($duplicateNames)) {
      throw MigratorException::duplicateMigrationNames($duplicateNames);
    }
    return $migrations;
  }

  /**
   * @return array<array{level: string, name: string}>
   */
  private function loadForLevel(string $level): array {
    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->migrationsDir . '/' . ucfirst($level), RecursiveDirectoryIterator::SKIP_DOTS)
    );

    $migrations = [];
    foreach ($files as $file) {
      if (!$file instanceof SplFileInfo || !$file->isFile()) {
        continue;
      }
      if (strtolower($file->getFilename()) === 'index.php') {
        continue;
      }
      if (strtolower($file->getExtension()) === 'php') {
        $migrations[] = $file->getBasename('.' . $file->getExtension());
      }
    }
    sort($migrations);
    return array_map(function ($migration) use ($level) {
      return [
        'level' => $level,
        'name' => $migration,
      ];
    }, $migrations);
  }

  private function generateName(string $level): string {
    return 'Migration_' . gmdate('Ymd_His') . '_' . ucfirst($level);
  }
}
