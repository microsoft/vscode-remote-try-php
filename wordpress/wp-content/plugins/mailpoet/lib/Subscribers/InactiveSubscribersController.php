<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class InactiveSubscribersController {

  const UNOPENED_EMAILS_THRESHOLD = 3;
  const LIFETIME_EMAILS_THRESHOLD = 10;

  private $processedTaskIdsTableCreated = false;

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
  }

  public function markInactiveSubscribers(int $daysToInactive, int $batchSize, ?int $startId = null, ?int $unopenedEmails = self::UNOPENED_EMAILS_THRESHOLD) {
    $thresholdDate = $this->getThresholdDate($daysToInactive);
    return $this->deactivateSubscribers($thresholdDate, $batchSize, $startId, $unopenedEmails);
  }

  public function markActiveSubscribers(int $daysToInactive, int $batchSize): int {
    $thresholdDate = $this->getThresholdDate($daysToInactive);
    return $this->activateSubscribers($thresholdDate, $batchSize);
  }

  public function reactivateInactiveSubscribers(): void {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $reactivateAllInactiveQuery = "
      UPDATE {$subscribersTable} SET status = :statusSubscribed WHERE status = :statusInactive
    ";
    $this->entityManager->getConnection()->executeQuery($reactivateAllInactiveQuery, [
      'statusSubscribed' => SubscriberEntity::STATUS_SUBSCRIBED,
      'statusInactive' => SubscriberEntity::STATUS_INACTIVE,
    ]);
  }

  private function getThresholdDate(int $daysToInactive): Carbon {
    $now = new Carbon();
    return $now->subDays($daysToInactive);
  }

  /**
   * @return int
   */
  private function deactivateSubscribers(Carbon $thresholdDate, int $batchSize, ?int $startId = null, ?int $unopenedEmails = self::UNOPENED_EMAILS_THRESHOLD) {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $scheduledTasksTable = $this->entityManager->getClassMetadata(ScheduledTaskEntity::class)->getTableName();
    $scheduledTaskSubscribersTable = $this->entityManager->getClassMetadata(ScheduledTaskSubscriberEntity::class)->getTableName();
    $sendingQueuesTable = $this->entityManager->getClassMetadata(SendingQueueEntity::class)->getTableName();
    $connection = $this->entityManager->getConnection();

    $thresholdDateIso = $thresholdDate->toDateTimeString();
    $dayAgo = new Carbon();
    $dayAgoIso = $dayAgo->subDay()->toDateTimeString();

    // Temporary table with processed tasks from threshold date up to yesterday
    $processedTaskIdsTable = 'inactive_task_ids';
    if (!$this->processedTaskIdsTableCreated) {
      $processedTaskIdsTableSql = "
        CREATE TEMPORARY TABLE IF NOT EXISTS {$processedTaskIdsTable}
        (INDEX task_id_ids (id), PRIMARY KEY (`id`))
        SELECT DISTINCT task_id as id FROM {$sendingQueuesTable} as sq
          JOIN {$scheduledTasksTable} as st ON sq.task_id = st.id
          WHERE st.processed_at > :thresholdDate
          AND st.processed_at < :dayAgo
      ";
      $connection->executeQuery($processedTaskIdsTableSql, [
        'thresholdDate' => $thresholdDateIso,
        'dayAgo' => $dayAgoIso,
      ]);
      $this->processedTaskIdsTableCreated = true;
    }

    // Select subscribers who received at least a number of emails after threshold date and subscribed before that
    $startId = (int)$startId;
    $endId = $startId + $batchSize;
    $lifetimeEmailsThreshold = self::LIFETIME_EMAILS_THRESHOLD;
    $inactiveSubscriberIdsTmpTable = 'inactive_subscriber_ids';
    $connection->executeQuery(
      "
      CREATE TEMPORARY TABLE IF NOT EXISTS {$inactiveSubscriberIdsTmpTable}
      (UNIQUE subscriber_id (id), PRIMARY KEY (`id`))
      SELECT s.id FROM {$subscribersTable} as s
        JOIN {$scheduledTaskSubscribersTable} as sts USE INDEX (subscriber_id) ON s.id = sts.subscriber_id
        JOIN {$processedTaskIdsTable} task_ids ON task_ids.id = sts.task_id
      WHERE s.last_subscribed_at < :thresholdDate
        AND s.status = :status
        AND s.id >= :startId
        AND s.id < :endId
        AND s.email_count >= {$lifetimeEmailsThreshold}
      GROUP BY s.id
      HAVING count(s.id) >= :unopenedEmailsThreshold
    ",
      [
        'thresholdDate' => $thresholdDateIso,
        'status' => SubscriberEntity::STATUS_SUBSCRIBED,
        'startId' => $startId,
        'endId' => $endId,
        'unopenedEmailsThreshold' => $unopenedEmails,
      ]
    );

    $result = $connection->executeQuery("
      SELECT isi.id FROM {$inactiveSubscriberIdsTmpTable} isi
        LEFT OUTER JOIN {$subscribersTable} as s ON isi.id = s.id AND GREATEST(
          COALESCE(s.last_engagement_at, '0'),
          COALESCE(s.last_subscribed_at, '0'),
          COALESCE(s.created_at, '0')
        ) > :thresholdDate
      WHERE s.id IS NULL
    ", [
      'thresholdDate' => $thresholdDateIso,
    ]);
    $idsToDeactivate = $result->fetchAllAssociative();

    $connection->executeQuery("DROP TABLE {$inactiveSubscriberIdsTmpTable}");

    $idsToDeactivate = array_map(
      function ($id) {
        return (int)$id['id'];
      },
      $idsToDeactivate
    );
    if (!count($idsToDeactivate)) {
      return 0;
    }
    $connection->executeQuery("UPDATE {$subscribersTable} SET status = :statusInactive WHERE id IN (:idsToDeactivate)", [
      'statusInactive' => SubscriberEntity::STATUS_INACTIVE,
      'idsToDeactivate' => $idsToDeactivate,
    ], ['idsToDeactivate' => Connection::PARAM_INT_ARRAY]);
    return count($idsToDeactivate);
  }

  private function activateSubscribers(Carbon $thresholdDate, int $batchSize): int {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $connection = $this->entityManager->getConnection();

    $idsToActivate = $connection->executeQuery("
      SELECT s.id
      FROM {$subscribersTable} s
      LEFT OUTER JOIN {$subscribersTable} s2 ON s.id = s2.id AND GREATEST(
        COALESCE(s2.last_engagement_at, '0'),
        COALESCE(s2.last_subscribed_at, '0'),
        COALESCE(s2.created_at, '0')
      ) > :thresholdDate
      WHERE s.last_subscribed_at < :thresholdDate
        AND s.status = :statusInactive
        AND s2.id IS NOT NULL
      GROUP BY s.id
      LIMIT :batchSize
    ", [
      'thresholdDate' => $thresholdDate,
      'statusInactive' => SubscriberEntity::STATUS_INACTIVE,
      'batchSize' => $batchSize,
    ], ['batchSize' => \PDO::PARAM_INT])->fetchAllAssociative();

    $idsToActivate = array_map(
      function($id) {
        return (int)$id['id'];
      },
      $idsToActivate
    );
    if (!count($idsToActivate)) {
      return 0;
    }
    $connection->executeQuery("UPDATE {$subscribersTable} SET status = :statusSubscribed WHERE id IN (:idsToActivate)", [
      'statusSubscribed' => SubscriberEntity::STATUS_SUBSCRIBED,
      'idsToActivate' => $idsToActivate,
    ], ['idsToActivate' => Connection::PARAM_INT_ARRAY]);
    return count($idsToActivate);
  }
}
