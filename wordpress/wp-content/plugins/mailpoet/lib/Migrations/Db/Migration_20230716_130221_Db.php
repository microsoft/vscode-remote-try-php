<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Migrator\DbMigration;

/**
 * This migration was created on 2023/06/16 but needs to be renamed to precede 20230419 which fails when the column is missing.
 */
class Migration_20230716_130221_Db extends DbMigration {
  public function run(): void {
    $tableName = $this->getTableName(NewsletterEntity::class);
    if (!$this->columnExists($tableName, 'wp_post_id')) {
      $this->connection->executeStatement("
        ALTER TABLE {$tableName}
        ADD wp_post_id int NULL
      ");
    }

    if (!$this->indexExists($tableName, 'wp_post_id')) {
      $this->connection->executeQuery(
        "ALTER TABLE `{$tableName}`
          ADD INDEX `wp_post_id` (`wp_post_id`)"
      );
    }
  }
}
