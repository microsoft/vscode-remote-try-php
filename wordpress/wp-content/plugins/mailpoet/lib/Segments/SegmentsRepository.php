<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use DateTime;
use MailPoet\ConflictException;
use MailPoet\Doctrine\Repository;
use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\NewsletterSegmentEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\InvalidStateException;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\NotFoundException;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\ORMException;

/**
 * @extends Repository<SegmentEntity>
 */
class SegmentsRepository extends Repository {

  /** @var NewsletterSegmentRepository */
  private $newsletterSegmentRepository;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var WPFunctions */
  private $wp;

  /** @var LoggerFactory */
  private $loggerFactory;

  public function __construct(
    EntityManager $entityManager,
    NewsletterSegmentRepository $newsletterSegmentRepository,
    FormsRepository $formsRepository,
    WPFunctions $wp,
    LoggerFactory $loggerFactory
  ) {
    parent::__construct($entityManager);
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
    $this->formsRepository = $formsRepository;
    $this->wp = $wp;
    $this->loggerFactory = $loggerFactory;
  }

  protected function getEntityClassName() {
    return SegmentEntity::class;
  }

  /**
   * @param string[] $types
   * @return SegmentEntity[]
   */
  public function findByTypeNotIn(array $types): array {
    return $this->doctrineRepository->createQueryBuilder('s')
      ->select('s')
      ->where('s.type NOT IN (:types)')
      ->setParameter('types', $types)
      ->getQuery()
      ->getResult();
  }

  public function getWPUsersSegment(): SegmentEntity {
    $segment = $this->findOneBy(['type' => SegmentEntity::TYPE_WP_USERS]);

    if (!$segment) {
      // create the wp users segment
      $segment = new SegmentEntity(
        __('WordPress Users', 'mailpoet'),
        SegmentEntity::TYPE_WP_USERS,
        __('This list contains all of your WordPress users.', 'mailpoet')
      );

      $this->entityManager->persist($segment);
      $this->entityManager->flush();
    }

    return $segment;
  }

  public function getWooCommerceSegment(): SegmentEntity {
    $segment = $this->findOneBy(['type' => SegmentEntity::TYPE_WC_USERS]);
    if (!$segment) {
      // create the WooCommerce customers segment
      $segment = new SegmentEntity(
        __('WooCommerce Customers', 'mailpoet'),
        SegmentEntity::TYPE_WC_USERS,
        __('This list contains all of your WooCommerce customers.', 'mailpoet')
      );
      $this->entityManager->persist($segment);
      $this->entityManager->flush();
    }
    return $segment;
  }

  public function getCountsPerType(): array {
    $results = $this->doctrineRepository->createQueryBuilder('s')
      ->select('s.type, COUNT(s) as cnt')
      ->where('s.deletedAt IS NULL')
      ->groupBy('s.type')
      ->getQuery()
      ->getResult();

    $countMap = [];
    foreach ($results as $result) {
      $countMap[$result['type']] = (int)$result['cnt'];
    }
    return $countMap;
  }

  public function isNameUnique(string $name, ?int $id): bool {
    $qb = $this->doctrineRepository->createQueryBuilder('s')
      ->select('s')
      ->where('s.name = :name')
      ->setParameter('name', $name);

    if ($id !== null) {
      $qb->andWhere('s.id != :id')
        ->setParameter('id', $id);
    }

    $results = $qb->getQuery()
      ->getResult();

    return count($results) === 0;
  }

  /**
   * @throws ConflictException
   */
  public function verifyNameIsUnique(string $name, ?int $id): void {
    if (!$this->isNameUnique($name, $id)) {
      throw new ConflictException("Could not create new segment with name [{$name}] because a segment with that name already exists.");
    }
  }

  /**
   * @param int $id
   *
   * @return SegmentEntity
   * @throws InvalidStateException
   */
  public function verifyDynamicSegmentExists(int $id): SegmentEntity {
    try {
      $dynamicSegment = $this->findOneById($id);
      if (!$dynamicSegment instanceof SegmentEntity) {
        throw InvalidStateException::create()->withMessage(sprintf("Could not find segment with ID '%s'.", $id));
      }
      if ($dynamicSegment->getType() !== SegmentEntity::TYPE_DYNAMIC) {
        throw InvalidStateException::create()->withMessage(sprintf("Segment with ID '%s' is not a dynamic segment. Its type is %s.", $id, $dynamicSegment->getType()));
      }
    } catch (InvalidStateException $exception) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_SEGMENTS)->error(sprintf("Could not verify existence of dynamic segment: %s", $exception->getMessage()));
      throw $exception;
    }
    return $dynamicSegment;
  }

  /**
   * @param DynamicSegmentFilterData[] $filtersData
   * @throws ConflictException
   * @throws NotFoundException
   * @throws ORMException
   */
  public function createOrUpdate(
    string $name,
    string $description = '',
    string $type = SegmentEntity::TYPE_DEFAULT,
    array $filtersData = [],
    ?int $id = null,
    bool $displayInManageSubscriptionPage = true
  ): SegmentEntity {
    $displayInManageSubPage = $type === SegmentEntity::TYPE_DEFAULT ? $displayInManageSubscriptionPage : false;

    if ($id) {
      $segment = $this->findOneById($id);
      if (!$segment instanceof SegmentEntity) {
        throw new NotFoundException("Segment with ID [{$id}] was not found.");
      }
      if ($name !== $segment->getName()) {
        $this->verifyNameIsUnique($name, $id);
        $segment->setName($name);
      }
      $segment->setDescription($description);
      $segment->setDisplayInManageSubscriptionPage($displayInManageSubPage);
    } else {
      $this->verifyNameIsUnique($name, $id);
      $segment = new SegmentEntity($name, $type, $description);
      $segment->setDisplayInManageSubscriptionPage($displayInManageSubPage);
      $this->persist($segment);
    }

    // We want to remove redundant filters before update
    while ($segment->getDynamicFilters()->count() > count($filtersData)) {
      $filterEntity = $segment->getDynamicFilters()->last();
      if ($filterEntity) {
        $segment->getDynamicFilters()->removeElement($filterEntity);
        $this->entityManager->remove($filterEntity);
      }
    }

    $createOrUpdateFilter = function ($filterData, $key) use ($segment) {
      if ($filterData instanceof DynamicSegmentFilterData) {
        $filterEntity = $segment->getDynamicFilters()->get($key);
        if (!$filterEntity instanceof DynamicSegmentFilterEntity) {
          $filterEntity = new DynamicSegmentFilterEntity($segment, $filterData);
          $segment->getDynamicFilters()->add($filterEntity);
          $this->entityManager->persist($filterEntity);
        } else {
          $filterEntity->setFilterData($filterData);
        }
      }
    };

    $wpActionName = 'mailpoet_dynamic_segments_filters_save';
    if ($this->wp->hasAction($wpActionName)) {
      $this->wp->doAction($wpActionName, $createOrUpdateFilter, $filtersData);
    } else {
      $filterData = reset($filtersData);
      $key = key($filtersData);
      $createOrUpdateFilter($filterData, $key);
    }

    $this->flush();
    return $segment;
  }

  public function bulkDelete(array $ids, string $type = SegmentEntity::TYPE_DEFAULT): int {
    if (empty($ids)) {
      return 0;
    }

    $count = 0;
    $this->entityManager->transactional(function (EntityManager $entityManager) use ($ids, $type, &$count) {
      $subscriberSegmentTable = $entityManager->getClassMetadata(SubscriberSegmentEntity::class)->getTableName();
      $segmentTable = $entityManager->getClassMetadata(SegmentEntity::class)->getTableName();
      $segmentFiltersTable = $entityManager->getClassMetadata(DynamicSegmentFilterEntity::class)->getTableName();

      $entityManager->getConnection()->executeStatement("
         DELETE ss FROM $subscriberSegmentTable ss
         JOIN $segmentTable s ON ss.`segment_id` = s.`id`
         WHERE ss.`segment_id` IN (:ids)
         AND s.`type` = :type
      ", [
        'ids' => $ids,
        'type' => $type,
      ], ['ids' => Connection::PARAM_INT_ARRAY]);

      $entityManager->getConnection()->executeStatement("
         DELETE df FROM $segmentFiltersTable df
         WHERE df.`segment_id` IN (:ids)
      ", [
        'ids' => $ids,
      ], ['ids' => Connection::PARAM_INT_ARRAY]);

      $queryBuilder = $entityManager->createQueryBuilder();
      $count = $queryBuilder->delete(SegmentEntity::class, 's')
        ->where('s.id IN (:ids)')
        ->andWhere('s.type = :type')
        ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
        ->setParameter('type', $type, \PDO::PARAM_STR)
        ->getQuery()->execute();

      $queryBuilder = $entityManager->createQueryBuilder();
      $queryBuilder->delete(NewsletterSegmentEntity::class, 'ns')
        ->where('ns.segment IN (:ids)')
        ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
        ->getQuery()->execute();
    });
    return $count;
  }

  public function bulkTrash(array $ids, string $type = SegmentEntity::TYPE_DEFAULT): int {
    $activelyUsedInNewsletters = $this->newsletterSegmentRepository->getSubjectsOfActivelyUsedEmailsForSegments($ids);
    $activelyUsedInForms = $this->formsRepository->getNamesOfFormsForSegments();
    $activelyUsed = array_unique(array_merge(array_keys($activelyUsedInNewsletters), array_keys($activelyUsedInForms)));
    $ids = array_diff($ids, $activelyUsed);
    return $this->updateDeletedAt($ids, new Carbon(), $type);
  }

  public function doTrash(array $ids, string $type = SegmentEntity::TYPE_DEFAULT): int {
    return $this->updateDeletedAt($ids, new Carbon(), $type);
  }

  public function bulkRestore(array $ids, string $type = SegmentEntity::TYPE_DEFAULT): int {
    return $this->updateDeletedAt($ids, null, $type);
  }

  private function updateDeletedAt(array $ids, ?DateTime $deletedAt, string $type): int {
    if (empty($ids)) {
      return 0;
    }

    $rows = $this->entityManager->createQueryBuilder()->update(SegmentEntity::class, 's')
    ->set('s.deletedAt', ':deletedAt')
    ->where('s.id IN (:ids)')
    ->andWhere('s.type IN (:type)')
    ->setParameter('deletedAt', $deletedAt)
    ->setParameter('ids', $ids)
    ->setParameter('type', $type)
    ->getQuery()->execute();

    return $rows;
  }

  public function findByUpdatedScoreNotInLastDay(int $limit): array {
    $dateTime = (new Carbon())->subDay();
    return $this->entityManager->createQueryBuilder()
      ->select('s')
      ->from(SegmentEntity::class, 's')
      ->where('s.averageEngagementScoreUpdatedAt IS NULL')
      ->orWhere('s.averageEngagementScoreUpdatedAt < :dateTime')
      ->setParameter('dateTime', $dateTime)
      ->getQuery()
      ->setMaxResults($limit)
      ->getResult();
  }

  /**
   * Returns count of segments that have more than one dynamic filter
   */
  public function getSegmentCountWithMultipleFilters(): int {
    $segmentFiltersTable = $this->entityManager->getClassMetadata(DynamicSegmentFilterEntity::class)->getTableName();
    $qbInner = $this->entityManager->getConnection()->createQueryBuilder()
      ->select('COUNT(DISTINCT sf.id) AS segmentCount')
      ->from($segmentFiltersTable, 'sf')
      ->groupBy('sf.segment_id')
      ->having('COUNT(sf.id) > 1');
    /** @var null|int $result */
    $result = $this->entityManager->getConnection()->createQueryBuilder()
      ->select('count(*)')
      ->from(sprintf('(%s) as subCounts', $qbInner->getSQL()))
      ->execute()
      ->fetchOne();
    return (int)$result;
  }
}
