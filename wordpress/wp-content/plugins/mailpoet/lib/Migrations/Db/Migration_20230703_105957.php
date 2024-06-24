<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Migrator\DbMigration;

class Migration_20230703_105957 extends DbMigration {
  public function run(): void {
    $this->migrateLogTable();
    $this->migrateRunTable();
  }

  public function migrateLogTable(): void {
    global $wpdb;

    $table = $wpdb->prefix . 'mailpoet_automation_run_logs';
    if (!$this->indexExists($table, 'status')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `status` (`status`)");
    }
    if (!$this->indexExists($table, 'step_id')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `step_id` (`step_id`)");
    }
  }

  public function migrateRunTable(): void {
    global $wpdb;

    $table = $wpdb->prefix . 'mailpoet_automation_runs';
    if (!$this->indexExists($table, 'created_at')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `created_at` (`created_at`)");
    }
    if (!$this->indexExists($table, 'version_id')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `version_id` (`version_id`)");
    }
    if (!$this->indexExists($table, 'status')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `status` (`status`)");
    }
    if (!$this->indexExists($table, 'next_step_id')) {
      $this->connection->executeStatement("ALTER TABLE $table ADD INDEX `next_step_id` (`next_step_id`)");
    }
  }
}
