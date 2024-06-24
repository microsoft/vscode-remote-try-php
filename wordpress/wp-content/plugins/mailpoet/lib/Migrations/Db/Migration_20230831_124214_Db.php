<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20230831_124214_Db extends DbMigration {
  public function run(): void {
    global $wpdb;
    $migrationsTable = $wpdb->prefix . 'mailpoet_migrations';
    $automationRunLogs = $wpdb->prefix . 'mailpoet_automation_run_logs';
    $statisticsNewslettersTable = $this->getTableName(StatisticsNewsletterEntity::class);

    // fix unintended "ON UPDATE current_timestamp()" on some timestamp columns
    $this->connection->executeStatement("ALTER TABLE $migrationsTable CHANGE `started_at` `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
    $this->connection->executeStatement("ALTER TABLE $automationRunLogs CHANGE `started_at` `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
    $this->connection->executeStatement("ALTER TABLE $statisticsNewslettersTable CHANGE `sent_at` `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP");
  }
}
