<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20230605_174836 extends DbMigration {
  public function run(): void {
    $subscribersTable = $this->getTableName(SubscriberEntity::class);
    $newColumns = [
      'last_sending_at',
      'last_open_at',
      'last_click_at',
      'last_purchase_at',
      'last_page_view_at',
    ];
    foreach ($newColumns as $column) {
      if ($this->columnExists($subscribersTable, $column)) {
        continue;
      }

      $this->connection->executeQuery(
        "ALTER TABLE `{$subscribersTable}`
          ADD COLUMN `{$column}` TIMESTAMP NULL DEFAULT NULL,
          ADD INDEX `{$column}` (`{$column}`)"
      );
    }
  }
}
