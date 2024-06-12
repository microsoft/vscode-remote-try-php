<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Migrator\AppMigration;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Doctrine\DBAL\Connection;

/**
 * Due to a bug https://mailpoet.atlassian.net/browse/MAILPOET-5719 we need to fix already existing data.
 * The performance optimization we changed the method for updating counts in the sending queue after finishing the scheduled task.
 * This change affected counts in automatic emails, because the value of processed emails has min and max value calculated from the total count.
 */
class Migration_20231128_120355_App extends AppMigration {
  public function run(): void {
    $wooCommerceHelper = $this->container->get(Helper::class);

    // If Woo is not active and the table doesn't exist, we can skip this migration
    if (!$wooCommerceHelper->isWooCommerceActive()) {
      return;
    }

    $connection = $this->container->get(Connection::class);

    // Fix data for completed tasks
    $sendingQueuesTable = $this->getTableName(SendingQueueEntity::class);
    $scheduledTasksTable = $this->getTableName(ScheduledTaskEntity::class);
    $newslettersTable = $this->getTableName(NewsletterEntity::class);
    $newsletterTypes = [NewsletterEntity::TYPE_AUTOMATIC, NewsletterEntity::TYPE_WELCOME];
    $statusCompleted = ScheduledTaskEntity::STATUS_COMPLETED;
    $connection->executeStatement("
      UPDATE {$sendingQueuesTable}
      JOIN {$scheduledTasksTable} ON {$scheduledTasksTable}.id = {$sendingQueuesTable}.task_id
      JOIN {$newslettersTable} ON {$newslettersTable}.id = {$sendingQueuesTable}.newsletter_id
      SET {$sendingQueuesTable}.count_total = 1,
      {$sendingQueuesTable}.count_processed = 1,
      {$sendingQueuesTable}.count_to_process = 0
      WHERE {$newslettersTable}.type IN (:newsletterTypes)
      AND {$scheduledTasksTable}.status = :taskStatus
    ", [
      'newsletterTypes' => $newsletterTypes,
      'taskStatus' => $statusCompleted,
    ], [
      'newsletterTypes' => Connection::PARAM_STR_ARRAY,
    ]);

    // Fix data for scheduled tasks
    $statusScheduled = ScheduledTaskEntity::STATUS_SCHEDULED;
    $connection->executeStatement("
      UPDATE {$sendingQueuesTable}
      JOIN {$scheduledTasksTable} ON {$scheduledTasksTable}.id = {$sendingQueuesTable}.task_id
      JOIN {$newslettersTable} ON {$newslettersTable}.id = {$sendingQueuesTable}.newsletter_id
      SET {$sendingQueuesTable}.count_total = 1,
      {$sendingQueuesTable}.count_processed = 0,
      {$sendingQueuesTable}.count_to_process = 1
      WHERE {$newslettersTable}.type IN (:newsletterTypes)
      AND {$scheduledTasksTable}.status = :taskStatus
    ", [
      'newsletterTypes' => $newsletterTypes,
      'taskStatus' => $statusScheduled,
    ], [
      'newsletterTypes' => Connection::PARAM_STR_ARRAY,
    ]);
  }

  /**
   * @param class-string $entityClassName
   */
  private function getTableName(string $entityClassName): string {
    return $this->entityManager->getClassMetadata($entityClassName)->getTableName();
  }
}
