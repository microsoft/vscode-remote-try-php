<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Migrator\AppMigration;

class AppMigrationTemplate extends AppMigration {
  public function run(): void {
    /*
     * TODO: Implement the migration logic here and remove this comment.
     *
     * App Level migration are intended for data migrations that use application level services.
     * The application level services require the DB structure to be up to date so they run after all DB migrations.
     *
     * Do not make changes in the DB structure in App Level migrations!
     *
     * You can use:
     *   $this->entityManager   For operations using Doctrine Entity Manager.
     *   $this->container       For accessing any needed service.
     */
  }
}
