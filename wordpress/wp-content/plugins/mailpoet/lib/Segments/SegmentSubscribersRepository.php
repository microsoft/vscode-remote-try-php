<?php declare(strict_types = 1);

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\InvalidStateException;
use MailPoet\NotFoundException;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Segments\DynamicSegments\FilterHandler;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;
use MailPoetVendor\Doctrine\ORM\QueryBuilder as ORMQueryBuilder;

class SegmentSubscribersRepository {
  /** @var EntityManager */
  private $entityManager;

  /** @var FilterHandler */
  private $filterHandler;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    EntityManager $entityManager,
    FilterHandler $filterHandler,
    SegmentsRepository $segmentsRepository
  ) {
    $this->entityManager = $entityManager;
    $this->filterHandler = $filterHandler;
    $this->segmentsRepository = $segmentsRepository;
  }

  public function findSubscribersIdsInSegment(int $segmentId, array $candidateIds = null): array {
    return $this->loadSubscriberIdsInSegment($segmentId, $candidateIds);
  }

  public function getSubscriberIdsInSegment(int $segmentId): array {
    return $this->loadSubscriberIdsInSegment($segmentId);
  }

  public function getSubscribersCount(int $segmentId, string $status = null): int {
    $segment = $this->getSegment($segmentId);
    $result = $this->getSubscribersStatisticsCount($segment);
    return (int)$result[$status ?: 'all'];
  }

  public function getSubscribersCountBySegmentIds(array $segmentIds, string $status = null, ?int $filterSegmentId = null): int {
    $segmentRepository = $this->entityManager->getRepository(SegmentEntity::class);
    $segments = $segmentRepository->findBy(['id' => $segmentIds]);
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $queryBuilder = $this->createCountQueryBuilder();

    $subQueries = [];
    foreach ($segments as $segment) {
      $segmentQb = $this->createCountQueryBuilder();
      $segmentQb->select("{$subscribersTable}.id AS inner_id");

      if ($segment->isStatic()) {
        $segmentQb = $this->filterSubscribersInStaticSegment($segmentQb, $segment, $status);
      } else {
        $segmentQb = $this->filterSubscribersInDynamicSegment($segmentQb, $segment, $status);
      }

      // inner parameters and types have to be merged to outer queryBuilder
      $queryBuilder->setParameters(array_merge(
        $segmentQb->getParameters(),
        $queryBuilder->getParameters()
      ), array_merge(
        $segmentQb->getParameterTypes(),
        $queryBuilder->getParameterTypes()
      ));
      $subQueries[] = $segmentQb->getSQL();
    }

    $queryBuilder->innerJoin(
      $subscribersTable,
      sprintf('(%s)', join(' UNION ', $subQueries)),
      'inner_subscribers',
      "inner_subscribers.inner_id = {$subscribersTable}.id"
    );

    try {
      if (is_int($filterSegmentId)) {
        $filterSegment = $this->segmentsRepository->verifyDynamicSegmentExists($filterSegmentId);
        $filterSegmentQb = $this->createCountQueryBuilder();
        $filterSegmentQb->select("{$subscribersTable}.id AS filter_segment_subscriber_id");
        $filterSegmentQb = $this->filterSubscribersInDynamicSegment($filterSegmentQb, $filterSegment, $status);
        $queryBuilder->setParameters(array_merge($filterSegmentQb->getParameters(), $queryBuilder->getParameters()), array_merge($filterSegmentQb->getParameterTypes(), $queryBuilder->getParameterTypes()));
        $queryBuilder->innerJoin(
          $subscribersTable,
          sprintf('(%s)', $filterSegmentQb->getSQL()),
          'filter_segment',
          "filter_segment.filter_segment_subscriber_id = {$subscribersTable}.id"
        );
      }
    } catch (InvalidStateException $exception) {
      return 0;
    }

    $statement = $this->executeQuery($queryBuilder);
    /** @var string $result */
    $result = $statement->fetchColumn();
    return (int)$result;
  }

  /**
   * @param DynamicSegmentFilterData[] $filters
   * @return int
   * @throws InvalidStateException
   */
  public function getDynamicSubscribersCount(array $filters): int {
    $segment = new SegmentEntity('temporary segment', SegmentEntity::TYPE_DYNAMIC, '');
    foreach ($filters as $filter) {
      $segment->addDynamicFilter(new DynamicSegmentFilterEntity($segment, $filter));
    }
    $queryBuilder = $this->createDynamicStatisticsQueryBuilder();
    $queryBuilder = $this->filterSubscribersInDynamicSegment($queryBuilder, $segment, null);
    $statement = $this->executeQuery($queryBuilder);
    /** @var array{all:string} $result */
    $result = $statement->fetch();
    return (int)$result['all'];
  }

  private function createCountQueryBuilder(): QueryBuilder {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    return $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("count(DISTINCT $subscribersTable.id)")
      ->from($subscribersTable);
  }

  private function createDynamicStatisticsQueryBuilder(): QueryBuilder {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    return $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->from($subscribersTable)
      ->addSelect("IFNULL(SUM(
            CASE WHEN $subscribersTable.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as `all`")
      ->addSelect("IFNULL(SUM(
            CASE WHEN $subscribersTable.deleted_at IS NOT NULL
              THEN 1 ELSE 0 END
        ), 0) as trash")
      ->addSelect("IFNULL(SUM(
            CASE WHEN $subscribersTable.status = :status_subscribed AND $subscribersTable.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as :status_subscribed")
      ->addSelect("IFNULL(SUM(
          CASE WHEN $subscribersTable.status = :status_unsubscribed AND $subscribersTable.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unsubscribed")
      ->addSelect("IFNULL(SUM(
          CASE WHEN $subscribersTable.status = :status_inactive AND $subscribersTable.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_inactive")
      ->addSelect("IFNULL(SUM(
          CASE WHEN $subscribersTable.status = :status_unconfirmed AND $subscribersTable.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unconfirmed")
      ->addSelect("IFNULL(SUM(
          CASE WHEN $subscribersTable.status = :status_bounced AND $subscribersTable.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_bounced")
      ->setParameter('status_subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('status_unsubscribed', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->setParameter('status_inactive', SubscriberEntity::STATUS_INACTIVE)
      ->setParameter('status_unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED)
      ->setParameter('status_bounced', SubscriberEntity::STATUS_BOUNCED);
  }

  private function createStaticStatisticsQueryBuilder(SegmentEntity $segment): QueryBuilder {
    $subscriberSegmentTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    return $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->from($subscriberSegmentTable, 'subscriber_segment')
      ->where('subscriber_segment.segment_id = :segment_id')
      ->setParameter('segment_id', $segment->getId())
      ->join('subscriber_segment', $subscribersTable, 'subscribers', 'subscribers.id = subscriber_segment.subscriber_id')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as `all`')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.deleted_at IS NOT NULL
              THEN 1 ELSE 0 END
        ), 0) as trash')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.status = :status_subscribed AND subscriber_segment.status = :status_subscribed AND subscribers.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as :status_subscribed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN (subscribers.status = :status_unsubscribed OR subscriber_segment.status = :status_unsubscribed) AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unsubscribed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_inactive AND subscriber_segment.status != :status_unsubscribed AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_inactive')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_unconfirmed  AND subscriber_segment.status != :status_unsubscribed AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unconfirmed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_bounced AND subscriber_segment.status != :status_unsubscribed AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_bounced')
      ->setParameter('status_subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('status_unsubscribed', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->setParameter('status_inactive', SubscriberEntity::STATUS_INACTIVE)
      ->setParameter('status_unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED)
      ->setParameter('status_bounced', SubscriberEntity::STATUS_BOUNCED);
  }

  private function createStaticGlobalStatusStatisticsQueryBuilder(SegmentEntity $segment): QueryBuilder {
    $subscriberSegmentTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    return $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->from($subscriberSegmentTable, 'subscriber_segment')
      ->where('subscriber_segment.segment_id = :segment_id')
      ->setParameter('segment_id', $segment->getId())
      ->join('subscriber_segment', $subscribersTable, 'subscribers', 'subscribers.id = subscriber_segment.subscriber_id')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as `all`')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.deleted_at IS NOT NULL
              THEN 1 ELSE 0 END
        ), 0) as trash')
      ->addSelect('IFNULL(SUM(
            CASE WHEN subscribers.status = :status_subscribed AND subscribers.deleted_at IS NULL
              THEN 1 ELSE 0 END
        ), 0) as :status_subscribed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_unsubscribed AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unsubscribed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_inactive AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_inactive')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_unconfirmed AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_unconfirmed')
      ->addSelect('IFNULL(SUM(
          CASE WHEN subscribers.status = :status_bounced AND subscribers.deleted_at IS NULL
            THEN 1 ELSE 0 END
        ), 0) as :status_bounced')
      ->setParameter('status_subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('status_unsubscribed', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->setParameter('status_inactive', SubscriberEntity::STATUS_INACTIVE)
      ->setParameter('status_unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED)
      ->setParameter('status_bounced', SubscriberEntity::STATUS_BOUNCED);
  }

  public function getSubscribersWithoutSegmentCount(): int {
    $queryBuilder = $this->entityManager->createQueryBuilder();
    $queryBuilder
      ->select('COUNT(DISTINCT s) AS subscribersCount')
      ->from(SubscriberEntity::class, 's');
    $this->addConstraintsForSubscribersWithoutSegment($queryBuilder);
    return (int)$queryBuilder->getQuery()->getSingleScalarResult();
  }

  public function getSubscribersWithoutSegmentStatisticsCount(): array {
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $queryBuilder = $this->entityManager
      ->getConnection()
      ->createQueryBuilder();
    $queryBuilder
      ->addSelect('IFNULL(SUM(
          CASE WHEN s.deleted_at IS NULL
            THEN 1 ELSE 0 END
      ), 0) as `all`')
      ->addSelect('IFNULL(SUM(
          CASE WHEN s.deleted_at IS NOT NULL
            THEN 1 ELSE 0 END
      ), 0) as trash')
      ->addSelect('IFNULL(SUM(
          CASE WHEN s.status = :status_subscribed AND s.deleted_at IS NULL
            THEN 1 ELSE 0 END
      ), 0) as :status_subscribed')
      ->addSelect('IFNULL(SUM(
        CASE WHEN s.status = :status_unsubscribed AND s.deleted_at IS NULL
          THEN 1 ELSE 0 END
      ), 0) as :status_unsubscribed')
      ->addSelect('IFNULL(SUM(
        CASE WHEN s.status = :status_inactive AND s.deleted_at IS NULL
          THEN 1 ELSE 0 END
      ), 0) as :status_inactive')
      ->addSelect('IFNULL(SUM(
        CASE WHEN s.status = :status_unconfirmed AND s.deleted_at IS NULL
          THEN 1 ELSE 0 END
      ), 0) as :status_unconfirmed')
      ->addSelect('IFNULL(SUM(
        CASE WHEN s.status = :status_bounced AND s.deleted_at IS NULL
          THEN 1 ELSE 0 END
      ), 0) as :status_bounced')
      ->from($subscribersTable, 's')
      ->setParameter('status_subscribed', SubscriberEntity::STATUS_SUBSCRIBED)
      ->setParameter('status_unsubscribed', SubscriberEntity::STATUS_UNSUBSCRIBED)
      ->setParameter('status_inactive', SubscriberEntity::STATUS_INACTIVE)
      ->setParameter('status_unconfirmed', SubscriberEntity::STATUS_UNCONFIRMED)
      ->setParameter('status_bounced', SubscriberEntity::STATUS_BOUNCED);

    $this->addConstraintsForSubscribersWithoutSegmentToDBAL($queryBuilder);
    $statement = $this->executeQuery($queryBuilder);
    $result = $statement->fetch();

    return $result;
  }

  public function addConstraintsForSubscribersWithoutSegment(ORMQueryBuilder $queryBuilder): void {
    $deletedSegmentsQueryBuilder = $this->entityManager->createQueryBuilder();
    $deletedSegmentsQueryBuilder->select('sg.id')
      ->from(SegmentEntity::class, 'sg')
      ->where($deletedSegmentsQueryBuilder->expr()->isNotNull('sg.deletedAt'));

    $queryBuilder
      ->leftJoin(
        's.subscriberSegments',
        'ssg',
        Join::WITH,
        (string)$queryBuilder->expr()->andX(
          $queryBuilder->expr()->eq('ssg.subscriber', 's.id'),
          $queryBuilder->expr()->eq('ssg.status', ':statusSubscribed'),
          $queryBuilder->expr()->notIn('ssg.segment', $deletedSegmentsQueryBuilder->getDQL())
        )
      )
      ->andWhere('ssg.id IS NULL')
      ->setParameter('statusSubscribed', SubscriberEntity::STATUS_SUBSCRIBED);
  }

  public function addConstraintsForSubscribersWithoutSegmentToDBAL(QueryBuilder $queryBuilder): void {
    $deletedSegmentsQueryBuilder = $this->entityManager->createQueryBuilder();
    $subscribersSegmentTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $deletedSegmentsQueryBuilder->select('sg.id')
      ->from(SegmentEntity::class, 'sg')
      ->where($deletedSegmentsQueryBuilder->expr()->isNotNull('sg.deletedAt'));

    $queryBuilder
      ->leftJoin(
        's',
        $subscribersSegmentTable,
        'ssg',
        (string)$queryBuilder->expr()->and(
          $queryBuilder->expr()->eq('ssg.subscriber_id', 's.id'),
          $queryBuilder->expr()->eq('ssg.status', ':statusSubscribed'),
          $queryBuilder->expr()->notIn('ssg.segment_id', $deletedSegmentsQueryBuilder->getQuery()->getSQL())
        )
      )
      ->andWhere('ssg.id IS NULL')
      ->setParameter('statusSubscribed', SubscriberEntity::STATUS_SUBSCRIBED);
  }

  private function loadSubscriberIdsInSegment(int $segmentId, array $candidateIds = null): array {
    $segment = $this->getSegment($segmentId);
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $queryBuilder = $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("DISTINCT $subscribersTable.id")
      ->from($subscribersTable);

    if ($segment->isStatic()) {
      $queryBuilder = $this->filterSubscribersInStaticSegment($queryBuilder, $segment, SubscriberEntity::STATUS_SUBSCRIBED);
    } else {
      $queryBuilder = $this->filterSubscribersInDynamicSegment($queryBuilder, $segment, SubscriberEntity::STATUS_SUBSCRIBED);
    }

    if ($candidateIds) {
      $queryBuilder->andWhere("$subscribersTable.id IN (:candidateIds)")
        ->setParameter('candidateIds', $candidateIds, Connection::PARAM_STR_ARRAY);
    }

    $statement = $this->executeQuery($queryBuilder);
    $result = $statement->fetchAll();
    return array_column($result, 'id');
  }

  private function filterSubscribersInStaticSegment(
    QueryBuilder $queryBuilder,
    SegmentEntity $segment,
    string $status = null
  ): QueryBuilder {
    $subscribersSegmentsTable = $this->entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $parameterName = "segment_{$segment->getId()}"; // When we use this method more times the parameter name has to be unique
    $queryBuilder = $queryBuilder->join(
      $subscribersTable,
      $subscribersSegmentsTable,
      'subsegment',
      "subsegment.subscriber_id = $subscribersTable.id AND subsegment.segment_id = :$parameterName"
    )->andWhere("$subscribersTable.deleted_at IS NULL")
      ->setParameter($parameterName, $segment->getId());
    if ($status) {
      $queryBuilder = $queryBuilder->andWhere("$subscribersTable.status = :status")
        ->andWhere("subsegment.status = :status")
        ->setParameter('status', $status);
    }
    return $queryBuilder;
  }

  private function filterSubscribersInDynamicSegment(
    QueryBuilder $queryBuilder,
    SegmentEntity $segment,
    string $status = null
  ): QueryBuilder {
    $filters = [];
    $dynamicFilters = $segment->getDynamicFilters();
    foreach ($dynamicFilters as $dynamicFilter) {
      $filters[] = $dynamicFilter->getFilterData();
    }

    // We don't allow dynamic segment without filers since it would return all subscribers
    // For BC compatibility fetching an empty result
    if (count($filters) === 0) {
      return $queryBuilder->andWhere('0 = 1');
    } elseif ($segment instanceof SegmentEntity) {
      try {
        $queryBuilder = $this->filterHandler->apply($queryBuilder, $segment);
      } catch (InvalidFilterException $e) {
        // If a segment has an invalid filter, we should simply consider it empty instead of throwing
        // an unhandled error. Unhandled errors here can break many admin pages.
        $queryBuilder->andWhere('0 = 1');
      }
    }
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $queryBuilder = $queryBuilder->andWhere("$subscribersTable.deleted_at IS NULL");
    if ($status) {
      $queryBuilder = $queryBuilder->andWhere("$subscribersTable.status = :status")
        ->setParameter('status', $status);
    }
    return $queryBuilder;
  }

  private function getSegment(int $id): SegmentEntity {
    $segment = $this->entityManager->find(SegmentEntity::class, $id);
    if (!$segment instanceof SegmentEntity) {
      throw new NotFoundException('Segment not found');
    }
    return $segment;
  }

  private function executeQuery(QueryBuilder $queryBuilder): Statement {
    $statement = $queryBuilder->execute();
    // Execute for select always returns statement but PHP Stan doesn't know that :(
    if (!$statement instanceof Statement) {
      throw new InvalidStateException('Invalid query.');
    }
    return $statement;
  }

  public function getSubscribersGlobalStatusStatisticsCount(SegmentEntity $segment): array {
    if ($segment->isStatic()) {
      $queryBuilder = $this->createStaticGlobalStatusStatisticsQueryBuilder($segment);
    } else {
      $queryBuilder = $this->createDynamicStatisticsQueryBuilder();
      $this->filterSubscribersInDynamicSegment($queryBuilder, $segment);
    }

    $statement = $this->executeQuery($queryBuilder);
    return $statement->fetch();
  }

  public function getSubscribersStatisticsCount(SegmentEntity $segment): array {
    if ($segment->isStatic()) {
      $queryBuilder = $this->createStaticStatisticsQueryBuilder($segment);
    } else {
      $queryBuilder = $this->createDynamicStatisticsQueryBuilder();
      $this->filterSubscribersInDynamicSegment($queryBuilder, $segment);
    }

    $statement = $this->executeQuery($queryBuilder);
    return $statement->fetch();
  }
}
