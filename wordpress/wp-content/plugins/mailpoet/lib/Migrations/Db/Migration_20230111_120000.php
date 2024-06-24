<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SettingEntity;
use MailPoet\Migrator\DbMigration;
use MailPoetVendor\Doctrine\DBAL\Connection;

class Migration_20230111_120000 extends DbMigration {
  public function run(): void {
    $segmentsTable = $this->getTableName(SegmentEntity::class);
    $settingsTable = $this->getTableName(SettingEntity::class);
    $columnName = 'display_in_manage_subscription_page';

    if ($this->columnExists($segmentsTable, $columnName)) {
      return;
    }

    $this->connection->executeStatement("
      ALTER TABLE {$segmentsTable}
      ADD {$columnName} tinyint(1) NOT NULL DEFAULT 0
    ");

    $subscriptionSetting = $this->connection->fetchOne("
      SELECT value
      FROM {$settingsTable}
      WHERE name = ?", ['subscription']);
    $subscriptionSetting = is_string($subscriptionSetting) ? unserialize($subscriptionSetting) : [];
    $subscriptionSetting = is_array($subscriptionSetting) ? $subscriptionSetting : [];
    $segmentIds = $subscriptionSetting['segments'] ?? [];
    if ($segmentIds) {
      // display only segments from settings.subscription.segments
      $this->connection->executeStatement("
        UPDATE {$segmentsTable}
        SET {$columnName} = 1
        WHERE id IN (?)
      ", [$segmentIds], [Connection::PARAM_INT_ARRAY]);

      $subscriptionSetting['segments'] = [];
      $this->connection->executeStatement(
        "
        UPDATE {$settingsTable}
        SET value = ?
        WHERE name = ?",
        [
          serialize($subscriptionSetting),
          'subscription',
        ]
      );
    } else {
      $this->connection->executeStatement("
        UPDATE {$segmentsTable}
        SET {$columnName} = 1
        WHERE type = ?
      ", [SegmentEntity::TYPE_DEFAULT]);
    }
  }
}
