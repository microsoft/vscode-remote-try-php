<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Migrations\App\AppMigrationTemplate;
use MailPoet\Migrations\Db\DbMigrationTemplate;
use Throwable;

class Runner {
  /** @var ContainerWrapper */
  private $container;

  /** @var Store */
  private $store;

  public function __construct(
    ContainerWrapper $container,
    Store $store
  ) {
    $this->container = $container;
    $this->store = $store;
  }

  public function runMigration(string $name, string $level): void {
    $className = $this->getClassName($name, $level);

    try {
      /** @var DbMigration|AppMigration $migration */
      $migration = new $className($this->container);
      $this->store->startMigration($name);
      $migration->run();
      $this->store->completeMigration($name);
    } catch (Throwable $e) {
      $this->store->failMigration($name, (string)$e);
      throw MigratorException::migrationFailed($className, $e);
    }
  }

  private function getClassName(string $name, string $level): string {
    $templateClass = $level === Repository::MIGRATIONS_LEVEL_DB ? DbMigrationTemplate::class : AppMigrationTemplate::class;
    $className = $this->getNamespace($templateClass) . '\\' . $name;
    if (!class_exists($className)) {
      throw MigratorException::migrationClassNotFound($className);
    }

    $parentClass = $level === Repository::MIGRATIONS_LEVEL_DB ? DbMigration::class : AppMigration::class;
    if (!is_subclass_of($className, $parentClass)) {
      throw MigratorException::migrationClassIsNotASubclassOf($className, $parentClass);
    }
    return $className;
  }

  private function getNamespace(string $className): string {
    $parts = explode('\\', $className);
    return implode('\\', array_slice($parts, 0, -1));
  }
}
