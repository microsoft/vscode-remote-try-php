<?php declare(strict_types = 1);

namespace MailPoet\Migrations\Db;

if (!defined('ABSPATH')) exit;


use MailPoet\Migrator\DbMigration;

class DbMigrationTemplate extends DbMigration {
  public function run(): void {
    /*
     * TODO: Implement the migration logic here and remove this comment.
     *
     * DB Level Migrations are intended for DB structure changes and low level data migrations.
     * If you need more complex logic/services use App Level migrations.
     *
     * You can use:
     *   $this->connection      For SQL queries using Doctrine DBAL.
     *   global $wpdb           For SQL queries using WordPress $wpdb.
     */
  }
}
