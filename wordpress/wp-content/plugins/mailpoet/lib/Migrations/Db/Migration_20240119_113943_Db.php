<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20240119_113943_Db extends DbMigration {
  public function run(): void {
    $table = $this->getTableName(StatisticsWooCommercePurchaseEntity::class);

    if (!$this->tableExists($table)) {
      return;
    }

    // make "newsletter_id" nullable
    $this->connection->executeStatement("ALTER TABLE $table CHANGE newsletter_id newsletter_id int(11) unsigned NULL");

    // update data
    $this->connection->executeStatement("UPDATE $table SET newsletter_id = NULL WHERE newsletter_id = 0");
  }
}
