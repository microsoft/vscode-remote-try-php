<?php declare(strict_types = 1);

namespace MailPoet\Migrator;

if (!defined('ABSPATH')) exit;


/**
 * @phpstan-import-type MigrationDefinition from Migrator
 */
interface Logger {
  /** @param MigrationDefinition[] $status */
  public function logBefore(array $status): void;

  /** @param MigrationDefinition $migration */
  public function logMigrationStarted(array $migration): void;

  /** @param MigrationDefinition $migration */
  public function logMigrationCompleted(array $migration): void;

  public function logAfter(): void;
}
