<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoet\Entities\StatisticsOpenEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscribersLastEngagement extends SimpleWorker {
  const AUTOMATIC_SCHEDULING = false;
  const SUPPORT_MULTIPLE_INSTANCES = false;
  const BATCH_SIZE = 2000;
  const TASK_TYPE = 'subscribers_last_engagement';

  /** @var EntityManager */
  private $entityManager;

  public function __construct(
    EntityManager $entityManager
  ) {
    parent::__construct();
    $this->entityManager = $entityManager;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer): bool {
    $meta = $task->getMeta();
    $minId = $meta['nextId'] ?? 1;
    $highestId = $this->getHighestSubscriberId();
    while ($minId <= $highestId) {
      $maxId = $minId + self::BATCH_SIZE;
      $this->processBatch($minId, $maxId);
      $task->setMeta(['nextId' => $maxId]);
      $this->scheduledTasksRepository->persist($task);
      $this->scheduledTasksRepository->flush();
      $this->cronHelper->enforceExecutionLimit($timer); // Throws exception and interrupts process if over execution limit
      $minId = $maxId;
    }
    return true;
  }

  private function processBatch(int $minSubscriberId, int $maxSubscriberId): void {
    $statisticsClicksTable = $this->entityManager->getClassMetadata(StatisticsClickEntity::class)->getTableName();
    $statisticsOpensTable = $this->entityManager->getClassMetadata(StatisticsOpenEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $query = "
      UPDATE $subscribersTable as mps
        LEFT JOIN (SELECT max(created_at) as created_at, subscriber_id FROM $statisticsOpensTable as mpsoinner GROUP BY mpsoinner.subscriber_id) as mpso ON mpso.subscriber_id = mps.id
        LEFT JOIN (SELECT max(created_at) as created_at, subscriber_id FROM $statisticsClicksTable as mpscinner GROUP BY mpscinner.subscriber_id) as mpsc ON mpsc.subscriber_id = mps.id
      SET mps.last_engagement_at = NULLIF(GREATEST(COALESCE(mpso.created_at, '0'), COALESCE(mpsc.created_at, '0')), '0')
      WHERE mps.last_engagement_at IS NULL AND mps.id >= $minSubscriberId AND  mps.id < $maxSubscriberId;
    ";

    $this->entityManager->getConnection()->executeStatement($query);
  }

  private function getHighestSubscriberId(): int {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $result = $this->entityManager->getConnection()->executeQuery("SELECT MAX(id) FROM $subscribersTable LIMIT 1;")->fetchNumeric();
    return is_array($result) && isset($result[0]) ? (int)$result[0] : 0;
  }
}
