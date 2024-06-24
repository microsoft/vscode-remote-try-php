<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Sending;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\Scheduler;
use MailPoet\Cron\Workers\SendingQueue\SendingQueue;
use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\ScheduledTaskSubscriberEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;

/**
 * @extends Repository<ScheduledTaskEntity>
 */
class ScheduledTasksRepository extends Repository {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    EntityManager $entityManager,
    WPFunctions $wp
  ) {
    $this->wp = $wp;
    parent::__construct($entityManager);
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return ScheduledTaskEntity[]
   */
  public function findByNewsletterAndStatus(NewsletterEntity $newsletter, string $status): array {
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->andWhere('st.status = :status')
      ->andWhere('sq.newsletter = :newsletter')
      ->setParameter('status', $status)
      ->setParameter('newsletter', $newsletter)
      ->getQuery()
      ->getResult();
  }

  /**
   * @param NewsletterEntity $newsletter
   */
  public function findOneByNewsletter(NewsletterEntity $newsletter): ?ScheduledTaskEntity {
    $scheduledTask = $this->doctrineRepository->createQueryBuilder('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->andWhere('sq.newsletter = :newsletter')
      ->orderBy('sq.updatedAt', 'desc')
      ->setMaxResults(1)
      ->setParameter('newsletter', $newsletter)
      ->getQuery()
      ->getOneOrNullResult();
    // for phpstan because it detects mixed instead of entity
    return ($scheduledTask instanceof ScheduledTaskEntity) ? $scheduledTask : null;
  }

  public function findOneBySendingQueue(SendingQueueEntity $sendingQueue): ?ScheduledTaskEntity {
    $scheduledTask = $this->doctrineRepository->createQueryBuilder('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->andWhere('sq.id = :sendingQueue')
      ->setMaxResults(1)
      ->setParameter('sendingQueue', $sendingQueue)
      ->getQuery()
      ->getOneOrNullResult();
    // for phpstan because it detects mixed instead of entity
    return ($scheduledTask instanceof ScheduledTaskEntity) ? $scheduledTask : null;
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return ScheduledTaskEntity[]
   */
  public function findByScheduledAndRunningForNewsletter(NewsletterEntity $newsletter): array {
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->andWhere('st.status = :status OR st.status IS NULL')
      ->andWhere('sq.newsletter = :newsletter')
      ->setParameter('status', NewsletterEntity::STATUS_SCHEDULED)
      ->setParameter('newsletter', $newsletter)
      ->getQuery()
      ->getResult();
  }

  /**
   * @param NewsletterEntity $newsletter
   * @return ScheduledTaskEntity[]
   */
  public function findByNewsletterAndSubscriberId(NewsletterEntity $newsletter, int $subscriberId): array {
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->join(ScheduledTaskSubscriberEntity::class, 'sts', Join::WITH, 'st = sts.task')
      ->andWhere('sq.newsletter = :newsletter')
      ->andWhere('sts.subscriber = :subscriber')
      ->setParameter('newsletter', $newsletter)
      ->setParameter('subscriber', $subscriberId)
      ->getQuery()
      ->getResult();
  }

  public function findOneScheduledByNewsletterAndSubscriber(NewsletterEntity $newsletter, SubscriberEntity $subscriber): ?ScheduledTaskEntity {
    $scheduledTask = $this->doctrineRepository->createQueryBuilder('st')
      ->join(SendingQueueEntity::class, 'sq', Join::WITH, 'st = sq.task')
      ->join(ScheduledTaskSubscriberEntity::class, 'sts', Join::WITH, 'st = sts.task')
      ->andWhere('st.status = :status')
      ->andWhere('sq.newsletter = :newsletter')
      ->andWhere('sts.subscriber = :subscriber')
      ->setMaxResults(1)
      ->setParameter('status', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->setParameter('newsletter', $newsletter)
      ->setParameter('subscriber', $subscriber)
      ->getQuery()
      ->getOneOrNullResult();
    // for phpstan because it detects mixed instead of entity
    return ($scheduledTask instanceof ScheduledTaskEntity) ? $scheduledTask : null;
  }

  public function findScheduledOrRunningTask(?string $type): ?ScheduledTaskEntity {
    $queryBuilder = $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->where('((st.status = :scheduledStatus) OR (st.status is NULL))')
      ->andWhere('st.deletedAt IS NULL')
      ->setParameter('scheduledStatus', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->setMaxResults(1)
      ->orderBy('st.scheduledAt', 'DESC');
    if (!empty($type)) {
      $queryBuilder
        ->andWhere('st.type = :type')
        ->setParameter('type', $type);
    }
    return $queryBuilder->getQuery()->getOneOrNullResult();
  }

  public function findScheduledTask(?string $type): ?ScheduledTaskEntity {
    $queryBuilder = $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->where('st.status = :scheduledStatus')
      ->andWhere('st.deletedAt IS NULL')
      ->setParameter('scheduledStatus', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->setMaxResults(1)
      ->orderBy('st.scheduledAt', 'DESC');
    if (!empty($type)) {
      $queryBuilder
        ->andWhere('st.type = :type')
        ->setParameter('type', $type);
    }
    return $queryBuilder->getQuery()->getOneOrNullResult();
  }

  public function findPreviousTask(ScheduledTaskEntity $task): ?ScheduledTaskEntity {
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->where('st.type = :type')
      ->setParameter('type', $task->getType())
      ->andWhere('st.createdAt < :created')
      ->setParameter('created', $task->getCreatedAt())
      ->orderBy('st.scheduledAt', 'DESC')
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
  }

  public function findDueByType($type, $limit = null) {
    return $this->findByTypeAndStatus($type, ScheduledTaskEntity::STATUS_SCHEDULED, $limit);
  }

  public function findRunningByType($type, $limit = null) {
    return $this->findByTypeAndStatus($type, null, $limit);
  }

  public function findCompletedByType($type, $limit = null) {
    return $this->findByTypeAndStatus($type, ScheduledTaskEntity::STATUS_COMPLETED, $limit);
  }

  public function findFutureScheduledByType($type, $limit = null) {
    return $this->findByTypeAndStatus($type, ScheduledTaskEntity::STATUS_SCHEDULED, $limit, true);
  }

  public function getCountsPerStatus(string $type = 'sending') {
    $stats = [
      ScheduledTaskEntity::STATUS_COMPLETED => 0,
      ScheduledTaskEntity::STATUS_PAUSED => 0,
      ScheduledTaskEntity::STATUS_SCHEDULED => 0,
      ScheduledTaskEntity::VIRTUAL_STATUS_RUNNING => 0,
    ];

    $counts = $this->doctrineRepository->createQueryBuilder('st')
      ->select('COUNT(st.id) as value')
      ->addSelect('st.status')
      ->where('st.deletedAt IS NULL')
      ->andWhere('st.type = :type')
      ->setParameter('type', $type)
      ->addGroupBy('st.status')
      ->getQuery()
      ->getResult();

    foreach ($counts as $count) {
      if ($count['status'] === null) {
        $stats[ScheduledTaskEntity::VIRTUAL_STATUS_RUNNING] = (int)$count['value'];
        continue;
      }
      $stats[$count['status']] = (int)$count['value'];
    }
    return $stats;
  }

  /**
   * @param string|null $type
   * @param array $statuses
   * @param int $limit
   * @return array<ScheduledTaskEntity>
   */
  public function getLatestTasks(
    $type = null,
    $statuses = [
      ScheduledTaskEntity::STATUS_COMPLETED,
      ScheduledTaskEntity::STATUS_SCHEDULED,
      ScheduledTaskEntity::VIRTUAL_STATUS_RUNNING,
    ],
    $limit = Scheduler::TASK_BATCH_SIZE
  ) {
    $result = [];
    foreach ($statuses as $status) {
      $tasksQuery = $this->doctrineRepository->createQueryBuilder('st')
        ->select('st')
        ->where('st.deletedAt IS NULL')
        ->where('st.status = :status');

      if ($status === ScheduledTaskEntity::VIRTUAL_STATUS_RUNNING) {
        $tasksQuery = $tasksQuery->orWhere('st.status IS NULL');
      }

      if ($type) {
        $tasksQuery = $tasksQuery->andWhere('st.type = :type')
          ->setParameter('type', $type);
      }

      $tasks = $tasksQuery
        ->setParameter('status', $status)
        ->setMaxResults($limit)
        ->orderBy('st.id', 'desc')
        ->getQuery()
        ->getResult();
      $result = array_merge($result, $tasks);
    }

    return $result;
  }

  /**
   * @return ScheduledTaskEntity[]
   */
  public function findRunningSendingTasks(?int $limit = null): array {
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join('st.sendingQueue', 'sq')
      ->where('st.type = :type')
      ->andWhere('st.status IS NULL')
      ->andWhere('st.deletedAt IS NULL')
      ->orderBy('st.priority', 'ASC')
      ->addOrderBy('st.updatedAt', 'ASC')
      ->setMaxResults($limit)
      ->setParameter('type', SendingQueue::TASK_TYPE)
      ->getQuery()
      ->getResult();
  }

  /**
   * @param string $type
   * @param SubscriberEntity $subscriber
   * @return ScheduledTaskEntity[]
   * @throws \MailPoetVendor\Doctrine\ORM\NonUniqueResultException
   */
  public function findByTypeAndSubscriber(string $type, SubscriberEntity $subscriber): array {
    $query = $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join(ScheduledTaskSubscriberEntity::class, 'sts', Join::WITH, 'st = sts.task')
      ->where('st.type = :type')
      ->andWhere('sts.subscriber = :subscriber')
      ->andWhere('st.deletedAt IS NULL')
      ->andWhere('st.status = :status')
      ->setParameter('type', $type)
      ->setParameter('subscriber', $subscriber->getId())
      ->setParameter('status', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->getQuery();
    $tasks = $query->getResult();
    return $tasks;
  }

  public function touchAllByIds(array $ids): void {
    $now = CarbonImmutable::createFromTimestamp((int)$this->wp->currentTime('timestamp'));
    $this->entityManager->createQueryBuilder()
      ->update(ScheduledTaskEntity::class, 'st')
      ->set('st.updatedAt', ':updatedAt')
      ->setParameter('updatedAt', $now)
      ->where('st.id IN (:ids)')
      ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
      ->getQuery()
      ->execute();

    // update was done via DQL, make sure the entities are also refreshed in the entity manager
    $this->refreshAll(function (ScheduledTaskEntity $entity) use ($ids) {
      return in_array($entity->getId(), $ids, true);
    });
  }

  /**
   * @return ScheduledTaskEntity[]
   */
  public function findScheduledSendingTasks(?int $limit = null): array {
    $now = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    return $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->join('st.sendingQueue', 'sq')
      ->where('st.deletedAt IS NULL')
      ->andWhere('st.status = :status')
      ->andWhere('st.scheduledAt <= :now')
      ->andWhere('st.type = :type')
      ->orderBy('st.updatedAt', 'ASC')
      ->setMaxResults($limit)
      ->setParameter('status', ScheduledTaskEntity::STATUS_SCHEDULED)
      ->setParameter('now', $now)
      ->setParameter('type', SendingQueue::TASK_TYPE)
      ->getQuery()
      ->getResult();
  }

  public function invalidateTask(ScheduledTaskEntity $task): void {
    $task->setStatus(ScheduledTaskEntity::STATUS_INVALID);
    $this->persist($task);
    $this->flush();
  }

  /** @param int[] $ids */
  public function deleteByIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(ScheduledTaskEntity::class, 't')
      ->where('t.id IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (ScheduledTaskEntity $entity) use ($ids) {
      return in_array($entity->getId(), $ids, true);
    });
  }

  protected function findByTypeAndStatus($type, $status, $limit = null, $future = false) {
    $queryBuilder = $this->doctrineRepository->createQueryBuilder('st')
      ->select('st')
      ->where('st.type = :type')
      ->setParameter('type', $type)
      ->andWhere('st.deletedAt IS NULL');

    if (is_null($status)) {
      $queryBuilder->andWhere('st.status IS NULL');
    } else {
      $queryBuilder
        ->andWhere('st.status = :status')
        ->setParameter('status', $status);
    }

    if ($future) {
      $queryBuilder->andWhere('st.scheduledAt > :now');
    } else {
      $queryBuilder->andWhere('st.scheduledAt <= :now');
    }

    $now = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $queryBuilder->setParameter('now', $now);

    if ($limit) {
      $queryBuilder->setMaxResults($limit);
    }

    return $queryBuilder->getQuery()->getResult();
  }

  protected function getEntityClassName() {
    return ScheduledTaskEntity::class;
  }
}
