<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


/**
 * @phpstan-type MigrationDefinition array{name: string, level: string|null, status: string, started_at: string|null, completed_at: string|null, retries: int|null, error: string|null, unknown: bool}
 */
class Migrator {
  const MIGRATION_STATUS_NEW = 'new';
  const MIGRATION_STATUS_STARTED = 'started';
  const MIGRATION_STATUS_COMPLETED = 'completed';
  const MIGRATION_STATUS_FAILED = 'failed';

  /** @var Repository */
  private $repository;

  /** @var Runner */
  private $runner;

  /** @var Store */
  private $store;

  public function __construct(
    Repository $repository,
    Runner $runner,
    Store $store
  ) {
    $this->repository = $repository;
    $this->runner = $runner;
    $this->store = $store;
  }

  public function run(Logger $logger = null): void {
    $this->store->ensureMigrationsTable();
    $migrations = $this->getStatus();

    if ($logger) {
      $logger->logBefore($migrations);
    }

    foreach ($migrations as $migration) {
      if (!$migration['level'] || $migration['unknown'] || $migration['status'] === self::MIGRATION_STATUS_COMPLETED) {
        continue;
      }

      if ($logger) {
        $logger->logMigrationStarted($migration);
      }

      $this->runner->runMigration($migration['name'], $migration['level']);

      if ($logger) {
        $logger->logMigrationCompleted($migration);
      }
    }

    if ($logger) {
      $logger->logAfter();
    }
  }

  /**
   * Array with of migration status data.
   * Ordering:
   * 1. Db migrations ordered by filename
   * 2. App migrations ordered by filename
   * 3. Unknown migrations (saved in store but not in repository e.g., renamed or deleted)
   * @return MigrationDefinition[]
   */
  public function getStatus(): array {
    $defined = $this->repository->loadAll();
    $definedMap = array_combine(array_column($defined, 'name'), $defined) ?: [];
    $processed = $this->store->getAll();
    $processedMap = array_combine(array_column($processed, 'name'), $processed) ?: [];
    $all = array_unique(array_merge(array_keys($definedMap), array_keys($processedMap)));

    $status = [];
    foreach ($all as $name) {
      $data = $processedMap[$name] ?? [];
      $status[] = [
        'name' => $name,
        'level' => $definedMap[$name]['level'] ?? null,
        'status' => $data ? $this->getMigrationStatus($data) : self::MIGRATION_STATUS_NEW,
        'started_at' => $data['started_at'] ?? null,
        'completed_at' => $data['completed_at'] ?? null,
        'retries' => isset($data['retries']) ? (int)$data['retries'] : null,
        'error' => $data && $data['error'] ? mb_strimwidth($data['error'], 0, 20, '…') : null,
        'unknown' => !isset($definedMap[$name]),
      ];
    }
    return $status;
  }

  private function getMigrationStatus(array $data): string {
    if (!isset($data['completed_at'])) {
      return self::MIGRATION_STATUS_STARTED;
    }
    return isset($data['error']) ? self::MIGRATION_STATUS_FAILED : self::MIGRATION_STATUS_COMPLETED;
  }
}
