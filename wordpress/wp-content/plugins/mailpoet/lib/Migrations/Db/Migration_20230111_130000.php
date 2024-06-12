<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20230111_130000 extends DbMigration {
  public function run(): void {
    $tableName = $this->getTableName(StatisticsUnsubscribeEntity::class);
    if (!$this->columnExists($tableName, 'method')) {
      $this->connection->executeStatement("
        ALTER TABLE {$tableName}
        ADD method varchar(40) NOT NULL DEFAULT 'unknown'
      ");
    }
  }
}
