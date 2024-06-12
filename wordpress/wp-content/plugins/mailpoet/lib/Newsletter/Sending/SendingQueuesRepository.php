<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Sending;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Segments\DynamicSegments\FilterFactory;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

/**
 * @extends Repository<SendingQueueEntity>
 */
class SendingQueuesRepository extends Repository {
  /** @var ScheduledTaskSubscribersRepository */
  private $scheduledTaskSubscribersRepository;

  /** @var WPFunctions */
  private $wp;

  /** @var FilterFactory */
  private $filterFactory;

  /** @var LoggerFactory */
  private $loggerFactory;

  public function __construct(
    EntityManager $entityManager,
    WPFunctions $wp,
    ScheduledTaskSubscribersRepository $scheduledTaskSubscribersRepository,
    FilterFactory $filterFactory,
    LoggerFactory $loggerFactory
  ) {
    parent::__construct($entityManager);
    $this->scheduledTaskSubscribersRepository = $scheduledTaskSubscribersRepository;
    $this->wp = $wp;
    $this->filterFactory = $filterFactory;
    $this->loggerFactory = $loggerFactory;
  }

  protected function getEntityClassName() {
    return SendingQueueEntity::class;
  }

  /**
   * @param NewsletterEntity $newsletter
   * @param string|null $status
   * @return SendingQueueEntity|null
   * @throws \MailPoetVendor\Doctrine\ORM\NonUniqueResultException
   */
  public function findOneByNewsletterAndTaskStatus(NewsletterEntity $newsletter, $status): ?SendingQueueEntity {
    $queryBuilder = $this->entityManager->createQueryBuilder()
      ->select('s')
      ->from(SendingQueueEntity::class, 's')
      ->join('s.task', 't')
      ->andWhere('s.newsletter = :newsletter')
      ->setParameter('newsletter', $newsletter);

    if (is_null($status)) {
      $queryBuilder->andWhere('t.status IS NULL');
    } else {
      $queryBuilder->andWhere('t.status = :status')
        ->setParameter('status', $status);
    }

    return $queryBuilder->getQuery()->getOneOrNullResult();
  }

  public function countAllByNewsletterAndTaskStatus(NewsletterEntity $newsletter, string $status): int {
    return intval($this->entityManager->createQueryBuilder()
      ->select('count(s.task)')
      ->from(SendingQueueEntity::class, 's')
      ->join('s.task', 't')
      ->where('t.status = :status')
      ->andWhere('s.newsletter = :newsletter')
      ->setParameter('status', $status)
      ->setParameter('newsletter', $newsletter)
      ->getQuery()
      ->getSingleScalarResult());
  }

  public function getTaskIdsByNewsletterId(int $newsletterId): array {
    $results = $this->entityManager->createQueryBuilder()
      ->select('IDENTITY(s.task) as task_id')
      ->from(SendingQueueEntity::class, 's')
      ->andWhere('s.newsletter = :newsletter')
      ->setParameter('newsletter', $newsletterId)
      ->getQuery()
      ->getArrayResult();
    return array_map('intval', array_column($results, 'task_id'));
  }

  public function isSubscriberProcessed(SendingQueueEntity $queue, SubscriberEntity $subscriber): bool {
    $task = $queue->getTask();
    if (is_null($task)) return false;
    return $this->scheduledTaskSubscribersRepository->isSubscriberProcessed($task, $subscriber);
  }

  /**
   * @return SendingQueueEntity[]
   */
  public function findAllForSubscriberSentBetween(
    SubscriberEntity $subscriber,
    ?\DateTimeInterface $dateTo,
    ?\DateTimeInterface $dateFrom
  ): array {
    $qb = $this->entityManager->createQueryBuilder()
      ->select('s, n')
      ->from(SendingQueueEntity::class, 's')
      ->join('s.task', 't')
      ->join('t.subscribers', 'tsub')
      ->join('s.newsletter', 'n')
      ->where('t.status = :status')
      ->setParameter('status', ScheduledTaskEntity::STATUS_COMPLETED)
      ->andWhere('t.type = :sendingType')
      ->setParameter('sendingType', 'sending')
      ->andWhere('tsub.subscriber = :subscriber')
      ->setParameter('subscriber', $subscriber);
    if ($dateTo) {
      $qb->andWhere('t.updatedAt < :dateTo')
        ->setParameter('dateTo', $dateTo);
    }
    if ($dateFrom) {
      $qb->andWhere('t.updatedAt > :dateFrom')
        ->setParameter('dateFrom', $dateFrom);
    }
    return $qb->getQuery()->getResult();
  }

  public function getCampaignAnalyticsQuery() {
    $sevenDaysAgo = Carbon::now()->subDays(7);
    $thirtyDaysAgo = Carbon::now()->subDays(30);
    $threeMonthsAgo = Carbon::now()->subMonths(3);

    return $this->doctrineRepository->createQueryBuilder('q')
      ->select('
        n.type as newsletterType, 
        q.meta as sendingQueueMeta, 
        CASE 
            WHEN COUNT(s.id) > 0 THEN true
            ELSE false
        END as sentToSegment,
        CASE 
            WHEN t.processedAt >= :sevenDaysAgo THEN true
            ELSE false
        END as sentLast7Days,
        CASE 
            WHEN t.processedAt >= :thirtyDaysAgo THEN true
            ELSE false
        END as sentLast30Days,
        CASE 
            WHEN t.processedAt >= :threeMonthsAgo THEN true
            ELSE false
        END as sentLast3Months')
      ->join('q.task', 't')
      ->leftJoin('q.newsletter', 'n')
      ->leftJoin('n.newsletterSegments', 'ns')
      ->leftJoin('ns.segment', 's', 'WITH', 's.type = :dynamicType')
      ->andWhere('t.status = :taskStatus')
      ->andWhere('t.processedAt >= :since')
      ->setParameter('sevenDaysAgo', $sevenDaysAgo)
      ->setParameter('thirtyDaysAgo', $thirtyDaysAgo)
      ->setParameter('threeMonthsAgo', $threeMonthsAgo)
      ->setParameter('dynamicType', SegmentEntity::TYPE_DYNAMIC)
      ->setParameter('taskStatus', ScheduledTaskEntity::STATUS_COMPLETED)
      ->setParameter('since', $threeMonthsAgo)
      ->groupBy('q.id')
      ->getQuery();
  }

  public function pause(SendingQueueEntity $queue): void {
    if ($queue->getCountProcessed() !== $queue->getCountTotal()) {
      $task = $queue->getTask();
      if ($task instanceof ScheduledTaskEntity) {
        $task->setStatus(ScheduledTaskEntity::STATUS_PAUSED);
        $this->flush();
      }
    }
  }

  public function resume(SendingQueueEntity $queue): void {
    $task = $queue->getTask();
    if (!$task instanceof ScheduledTaskEntity) return;

    if ($queue->getCountProcessed() === $queue->getCountTotal()) {
      $processedAt = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
      $task->setProcessedAt($processedAt);
      $task->setStatus(ScheduledTaskEntity::STATUS_COMPLETED);
      // Update also status of newsletter if necessary
      $newsletter = $queue->getNewsletter();
      if ($newsletter instanceof NewsletterEntity && $newsletter->canBeSetSent()) {
        $newsletter->setStatus(NewsletterEntity::STATUS_SENT);
      }
      $this->flush();
    } else {
      $newsletter = $queue->getNewsletter();
      if (!$newsletter instanceof NewsletterEntity) return;
      if ($newsletter->getStatus() === NewsletterEntity::STATUS_CORRUPT) { // force a re-render
        $queue->setNewsletterRenderedBody(null);
        $this->persist($queue);
      }
      $newsletter->setStatus(NewsletterEntity::STATUS_SENDING);
      $task->setStatus(null);
      $this->flush();
    }
  }

  public function deleteByTask(ScheduledTaskEntity $scheduledTask): void {
    $this->entityManager->createQueryBuilder()
      ->delete(SendingQueueEntity::class, 'sq')
      ->where('sq.task = :task')
      ->setParameter('task', $scheduledTask)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (SendingQueueEntity $entity) use ($scheduledTask) {
      return $entity->getTask() === $scheduledTask;
    });
  }

  public function saveCampaignId(SendingQueueEntity $queue, string $campaignId): void {
    $meta = $queue->getMeta();
    if (!is_array($meta)) {
      $meta = [];
    }
    $meta['campaignId'] = $campaignId;
    $queue->setMeta($meta);
    $this->flush();
  }

  public function saveFilterSegmentMeta(SendingQueueEntity $queue, SegmentEntity $filterSegmentEntity): void {
    $meta = $queue->getMeta() ?? [];
    $meta['filterSegment'] = [
      'id' => $filterSegmentEntity->getId(),
      'name' => $filterSegmentEntity->getName(),
      'updatedAt' => $filterSegmentEntity->getUpdatedAt(),
      'filters' => array_map(function(DynamicSegmentFilterEntity $filterEntity) {
        $filter = $this->filterFactory->getFilterForFilterEntity($filterEntity);
        $data = $filterEntity->getFilterData();
        $filterData = [
          'filterType' => $data->getFilterType(),
          'action' => $data->getAction(),
          'data' => $filterEntity->getFilterData()->getData(),
          'lookupData' => [],
        ];
        try {
          $filterData['lookupData'] = $filter->getLookupData($data);
        } catch (\Throwable $e) {
          $this->loggerFactory->getLogger(LoggerFactory::TOPIC_SEGMENTS)->error("Failed to save lookup data for filter {$filterEntity->getId()}: {$e->getMessage()}");
        }
        return $filterData;
      }, $filterSegmentEntity->getDynamicFilters()->toArray()),
    ];
    $queue->setMeta($meta);
    $this->flush();
  }

  public function updateCounts(SendingQueueEntity $queue, ?int $count = null): void {
    if ($count) {
      // increment/decrement counts based on known subscriber count, don't exceed the bounds
      $queue->setCountProcessed(min($queue->getCountProcessed() + $count, $queue->getCountTotal()));
      $queue->setCountToProcess(max($queue->getCountToProcess() - $count, 0));
    } else {
      // query DB to update counts, slower but more accurate, to be used if count isn't known
      $task = $queue->getTask();
      $processed = $task ? $this->scheduledTaskSubscribersRepository->countProcessed($task) : 0;
      $unprocessed = $task ? $this->scheduledTaskSubscribersRepository->countUnprocessed($task) : 0;
      $queue->setCountProcessed($processed);
      $queue->setCountToProcess($unprocessed);
      $queue->setCountTotal($processed + $unprocessed);
    }
    $this->entityManager->flush();
  }

  /** @param int[] $ids */
  public function deleteByNewsletterIds(array $ids): void {
    $this->entityManager->createQueryBuilder()
      ->delete(SendingQueueEntity::class, 'q')
      ->where('q.newsletter IN (:ids)')
      ->setParameter('ids', $ids)
      ->getQuery()
      ->execute();

    // delete was done via DQL, make sure the entities are also detached from the entity manager
    $this->detachAll(function (SendingQueueEntity $entity) use ($ids) {
      $newsletter = $entity->getNewsletter();
      return $newsletter && in_array($newsletter->getId(), $ids, true);
    });
  }
}
