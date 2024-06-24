<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Sending;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\InvalidStateException;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

/**
 * @extends Repository<ScheduledTaskSubscriberEntity>
 */
class ScheduledTaskSubscribersRepository extends Repository {
  protected function getEntityClassName() {
    return ScheduledTaskSubscriberEntity::class;
  }

  public function isSubscriberProcessed(ScheduledTaskEntity $task, SubscriberEntity $subscriber): bool {
    $scheduledTaskSubscriber = $this
      ->doctrineRepository
      ->createQueryBuilder('sts')
      ->andWhere('sts.processed = 1')
      ->andWhere('sts.task = :task')
      ->andWhere('sts.subscriber = :subscriber')
      ->setParameter('subscriber', $subscriber)
      ->setParameter('task', $task)
      ->getQuery()
      ->getOneOrNullResult();
    return !empty($scheduledTaskSubscriber);
  }

  public function createOrUpdate(array $data): ?ScheduledTaskSubscriberEntity {
    if (!isset($data['task_id'], $data['subscriber_id'])) {
      return null;
    }

    $taskSubscriber = $this->findOneBy(['task' => $data['task_id'], 'subscriber' => $data['subscriber_id']]);
    if (!$taskSubscriber) {
      $task = $this->entityManager->getReference(ScheduledTaskEntity::class, (int)$data['task_id']);
      $subscriber = $this->entityManager->getReference(SubscriberEntity::class, (int)$data['subscriber_id']);
      if (!$task || !$subscriber) throw new InvalidStateException();

      $taskSubscriber = new ScheduledTaskSubscriberEntity($task, $subscriber);
      $this->persist($taskSubscriber);
    }

    $processed = $data['processed'] ?? ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED;
    $failed = $data['failed'] ?? ScheduledTaskSubscriberEntity::FAIL_STATUS_OK;

    $taskSubscriber->setProcessed($processed);
    $taskSubscriber->setFailed($failed);
    $this->flush();
    return $taskSubscriber;
  }

  public function countSubscriberIdsBatchForTask(int $taskId, int $lastProcessedSubscriberId): int {
    $queryBuilder = $this->getBaseSubscribersIdsBatchForTaskQuery($taskId, $lastProcessedSubscriberId);
    $countSubscribers = $queryBuilder
      ->select('count(sts.subscriber)')
      ->getQuery()
      ->getSingleScalarResult();

    return intval($countSubscribers);
  }

  public function getSubscriberIdsBatchForTask(int $taskId, int $lastProcessedSubscriberId, int $limit): array {
    $queryBuilder = $this->getBaseSubscribersIdsBatchForTaskQuery($taskId, $lastProcessedSubscriberId);
    $subscribersIds = $queryBuilder
      ->select('IDENTITY(sts.subscriber) AS subscriber_id')
      ->orderBy('sts.subscriber', 'asc')
      ->setMaxResults($limit)
      ->getQuery()
      ->getSingleColumnResult();

    return $subscribersIds;
  }

  /**
   * @param int[] $subscriberIds
   */
  public function updateProcessedSubscribers(ScheduledTaskEntity $task, array $subscriberIds): void {
    if ($subscriberIds) {
      $this->entityManager->createQueryBuilder()
        ->update(ScheduledTaskSubscriberEntity::class, 'sts')
        ->set('sts.processed', ScheduledTaskSubscriberEntity::STATUS_PROCESSED)
        ->where('sts.subscriber IN (:subscriberIds)')
        ->andWhere('sts.task = :task')
        ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY)
        ->setParameter('task', $task)
        ->getQuery()
        ->execute();

      // update was done via DQL, make sure the entities are also refreshed in the entity manager
      $this->refreshAll(function (ScheduledTaskSubscriberEntity $entity) use ($task, $subscriberIds) {
        return $entity->getTask() === $task && in_array($entity->getSubscriberId(), $subscriberIds, true);
      });
    }

    $this->checkCompleted($task);
  }

  public function createSubscribersForBounceWorker(ScheduledTaskEntity $scheduledTaskEntity): void {
    $scheduledTaskSubscribersTable = $this->entityManager->getClassMetadata(ScheduledTaskSubscriberEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $stmt = $this->entityManager->getConnection()->prepare("
      INSERT IGNORE INTO " . $scheduledTaskSubscribersTable . "
      (task_id, subscriber_id, processed)
      SELECT :taskId AS task_id, s.`id` AS subscriber_id, :unprocessed AS processed
      FROM " . $subscribersTable . " s
      WHERE s.`deleted_at` IS NULL
      AND s.`status` IN (:subscribed, :unconfirmed)
    ");
    $stmt->bindValue('taskId', $scheduledTaskEntity->getId());
    $stmt->bindValue('unprocessed', ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED);
    $stmt->bindValue('subscribed', SubscriberEntity::STATUS_SUBSCRIBED);
    $stmt->bindValue('unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED);
    $stmt->executeQuery();
  }

  /** @param int[] $ids */
  public function deleteByTaskIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(ScheduledTaskSubscriberEntity::class, 'sts')
      ->where('sts.task IN (:taskIds)')
      ->setParameter('taskIds', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (ScheduledTaskSubscriberEntity $entity) use ($ids) {
      $task = $entity->getTask();
      return $task && in_array($task->getId(), $ids, true);
    });
  }

  public function deleteByScheduledTask(ScheduledTaskEntity $scheduledTask): void {
    $this->entityManager->createQueryBuilder()
      ->delete(ScheduledTaskSubscriberEntity::class, 'sts')
      ->where('sts.task = :task')
      ->setParameter('task', $scheduledTask)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (ScheduledTaskSubscriberEntity $entity) use ($scheduledTask) {
      return $entity->getTask() === $scheduledTask;
    });
  }

  public function deleteByScheduledTaskAndSubscriberIds(ScheduledTaskEntity $scheduledTask, array $subscriberIds): void {
    $this->entityManager->createQueryBuilder()
      ->delete(ScheduledTaskSubscriberEntity::class, 'sts')
      ->where('sts.task = :task')
      ->andWhere('sts.subscriber IN (:subscriberIds)')
      ->setParameter('task', $scheduledTask)
      ->setParameter('subscriberIds', $subscriberIds, Connection::PARAM_INT_ARRAY)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (ScheduledTaskSubscriberEntity $entity) use ($scheduledTask, $subscriberIds) {
      return $entity->getTask() === $scheduledTask && in_array($entity->getSubscriberId(), $subscriberIds, true);
    });

    $this->checkCompleted($scheduledTask);
  }

  public function setSubscribers(ScheduledTaskEntity $task, array $subscriberIds): void {
    $this->deleteByScheduledTask($task);

    foreach ($subscriberIds as $subscriberId) {
      $this->createOrUpdate([
        'task_id' => $task->getId(),
        'subscriber_id' => $subscriberId,
      ]);
    }
  }

  public function saveError(ScheduledTaskEntity $scheduledTask, int $subscriberId, string $errorMessage): void {
    $scheduledTaskSubscriber = $this->findOneBy(['task' => $scheduledTask, 'subscriber' => $subscriberId]);

    if ($scheduledTaskSubscriber instanceof ScheduledTaskSubscriberEntity) {
      $scheduledTaskSubscriber->setFailed(ScheduledTaskSubscriberEntity::FAIL_STATUS_FAILED);
      $scheduledTaskSubscriber->setProcessed(ScheduledTaskSubscriberEntity::STATUS_PROCESSED);
      $scheduledTaskSubscriber->setError($errorMessage);
      $this->persist($scheduledTaskSubscriber);
      $this->flush();

      $this->checkCompleted($scheduledTask);
    }
  }

  public function countProcessed(ScheduledTaskEntity $scheduledTaskEntity): int {
    return $this->countBy(['task' => $scheduledTaskEntity, 'processed' => ScheduledTaskSubscriberEntity::STATUS_PROCESSED]);
  }

  public function countUnprocessed(ScheduledTaskEntity $scheduledTaskEntity): int {
    return $this->countBy(['task' => $scheduledTaskEntity, 'processed' => ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED]);
  }

  private function checkCompleted(ScheduledTaskEntity $task): void {
    $count = $this->countUnprocessed($task);
    if ($count === 0) {
      $task->setStatus(ScheduledTaskEntity::STATUS_COMPLETED);
      $task->setProcessedAt(Carbon::createFromTimestamp(WPFunctions::get()->currentTime('timestamp')));
      $this->entityManager->flush();
    }
  }

  private function getBaseSubscribersIdsBatchForTaskQuery(int $taskId, int $lastProcessedSubscriberId): QueryBuilder {
    return $this->entityManager
      ->createQueryBuilder()
      ->from(ScheduledTaskSubscriberEntity::class, 'sts')
      ->andWhere('sts.task = :taskId')
      ->andWhere('sts.subscriber > :lastProcessedSubscriberId')
      ->andWhere('sts.processed = :status')
      ->setParameter('taskId', $taskId)
      ->setParameter('lastProcessedSubscriberId', $lastProcessedSubscriberId)
      ->setParameter('status', ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED);
  }
}
