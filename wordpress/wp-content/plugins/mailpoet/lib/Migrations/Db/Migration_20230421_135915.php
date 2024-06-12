<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Migrator\DbMigration;

class Migration_20230421_135915 extends DbMigration {
  public function run(): void {
    $newslettersTable = $this->getTableName(NewsletterEntity::class);
    $this->connection->executeQuery("
      ALTER TABLE $newslettersTable
      CHANGE type type varchar(150) NOT NULL DEFAULT 'standard'
    ");
    $this->connection->executeQuery("
      UPDATE $newslettersTable
      SET type = 'automation_transactional'
      WHERE type = 'transactional'
    ");
  }
}
