<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20230824_054259_Db extends DbMigration {

  public const DEFAULT_STATUS = 'unknown';

  public function __construct(
    ContainerWrapper $container
  ) {
    parent::__construct($container);
  }

  public function run(): void {
    $this->createStatusColumn();
  }

  private function createStatusColumn(): void {
    $revenueTable = $this->getTableName(StatisticsWooCommercePurchaseEntity::class);
    if (!$this->tableExists($revenueTable) || $this->columnExists($revenueTable, 'status')) {
      return;
    }
    $this->connection->executeQuery(
      "ALTER TABLE `" . $revenueTable . "`
        ADD COLUMN `status` VARCHAR(40) NOT NULL DEFAULT '" . self::DEFAULT_STATUS . "',
        ADD INDEX `status` (`status`)"
    );
  }
}
