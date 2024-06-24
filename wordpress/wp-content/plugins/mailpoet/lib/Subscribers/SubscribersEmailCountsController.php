<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class SubscribersEmailCountsController {
  /** @var EntityManager */
  private $entityManager;

  /** @var string */
  private $subscribersTable;

  /** @var string */
  private $scheduledTasksTable;

  public function __construct(
    EntityManager $entityManager
  ) {
    $this->entityManager = $entityManager;
    $this->subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $this->scheduledTasksTable = $this->entityManager->getClassMetadata(ScheduledTaskEntity::class)->getTableName();
  }

  public function updateSubscribersEmailCounts(?\DateTimeInterface $dateLastProcessed, int $batchSize, ?int $startId = null): array {
    $scheduledTaskSubscribersTable = $this->entityManager->getClassMetadata(ScheduledTaskSubscriberEntity::class)->getTableName();

    $connection = $this->entityManager->getConnection();

    $dayAgo = new Carbon();
    $dayAgoIso = $dayAgo->subDay()->toDateTimeString();

    $startId = (int)$startId;

    // Return if there are no new sending tasks
    if ($dateLastProcessed && !$this->newSendingTasksSince($dateLastProcessed)) {
      return [0, 0];
    }
    // Return if there are no subscribers to update
    [$countSubscribersToUpdate, $endId] = $this->countAndMaxOfSubscribersInRange($startId, $batchSize);
    if (!$countSubscribersToUpdate) {
      return [0, 0];
    }

    $queryParams = [
      'startId' => $startId,
      'endId' => $endId,
      'dayAgo' => $dayAgoIso,
    ];
    if ($dateLastProcessed) {
      $carbonDateLastProcessed = Carbon::createFromTimestamp($dateLastProcessed->getTimestamp());
      $dateFromIso = ($carbonDateLastProcessed->subDay())->toDateTimeString();
      $queryParams['dateFrom'] = $dateFromIso;
    }
    // If $dateLastProcessed provided, increment value, otherwise count all and reset value
    $initUpdateValue = $dateLastProcessed ? 's.email_count' : '';
    $dateLastProcessedSql = $dateLastProcessed ? ' AND st.processed_at >= :dateFrom' : '';

    $connection->executeQuery(
      "
      UPDATE {$this->subscribersTable} as s
      JOIN (
          SELECT s.id, COUNT(st.id) as email_count
          FROM {$this->subscribersTable} as s
          JOIN {$scheduledTaskSubscribersTable} as sts ON s.id = sts.subscriber_id
          JOIN {$this->scheduledTasksTable} as st ON st.id = sts.task_id
          WHERE s.id >= :startId
          AND s.id <= :endId
          AND st.type = 'sending'
          AND st.processed_at IS NOT NULL
          AND st.processed_at < :dayAgo
          {$dateLastProcessedSql}
          GROUP BY s.id
      ) counts ON counts.id = s.id
      SET s.email_count = {$initUpdateValue} + IFNULL(counts.email_count, 0)
    ",
      $queryParams
    );

    return [$countSubscribersToUpdate, $endId];
  }

  private function newSendingTasksSince(\DateTimeInterface $dateLastProcessed): bool {
    $carbonDateLastProcessed = Carbon::createFromTimestamp($dateLastProcessed->getTimestamp());
    $dateFromIso = ($carbonDateLastProcessed->subDay())->toDateTimeString();
    $queryParams['dateFrom'] = $dateFromIso;
    $dayAgo = new Carbon();
    $dayAgoIso = $dayAgo->subDay()->toDateTimeString();
    $queryParams['dayAgo'] = $dayAgoIso;

    $result = $this->entityManager->getConnection()->executeQuery(
      "
      SELECT count(id) FROM {$this->scheduledTasksTable}
      WHERE type = 'sending'
      AND processed_at IS NOT NULL
      AND processed_at < :dayAgo
      AND processed_at >= :dateFrom
      ",
      $queryParams
    )->fetchNumeric();

    /** @var int[] $result - it's required for PHPStan */
    return is_array($result) && isset($result[0]) && ((int)$result[0] > 0);
  }

  private function countAndMaxOfSubscribersInRange(int $startId, int $batchSize): array {
    $result = $this->entityManager->getConnection()->executeQuery(
      "
      SELECT COUNT(ids.id) as count, COALESCE(MAX(ids.id), 0) as max FROM (
        SELECT s.id FROM {$this->subscribersTable} as s
        WHERE s.id >= :startId
        ORDER BY s.id
        LIMIT :batchSize
        ) ids
    ",
      [
        'startId' => $startId,
        'batchSize' => $batchSize,
      ],
      [
        'startId' => \PDO::PARAM_INT,
        'batchSize' => \PDO::PARAM_INT,
      ]
    );

    /** @var array{0: array{count:int, max:int}} $subscribersInRange */
    $subscribersInRange = $result->fetchAllAssociative();

    return [intval($subscribersInRange[0]['count']), intval($subscribersInRange[0]['max'])];
  }
}
