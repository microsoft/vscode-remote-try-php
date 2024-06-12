<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\TagEntity;
use MailPoet\Listing\ListingDefinition;
use MailPoet\Listing\ListingRepository;
use MailPoet\Segments\DynamicSegments\FilterHandler;
use MailPoet\Segments\SegmentSubscribersRepository;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\DBAL\Driver\Statement;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;
use MailPoetVendor\Doctrine\ORM\Query\Expr\Join;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class SubscriberListingRepository extends ListingRepository {
  public const FILTER_WITHOUT_LIST = 'without-list';

  const DEFAULT_SORT_BY = 'createdAt';

  private static $supportedStatuses = [
    SubscriberEntity::STATUS_SUBSCRIBED,
    SubscriberEntity::STATUS_UNSUBSCRIBED,
    SubscriberEntity::STATUS_INACTIVE,
    SubscriberEntity::STATUS_BOUNCED,
    SubscriberEntity::STATUS_UNCONFIRMED,
  ];

  /** @var FilterHandler */
  private $dynamicSegmentsFilter;

  /** @var EntityManager */
  private $entityManager;

  /** @var SegmentSubscribersRepository */
  private $segmentSubscribersRepository;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  /** @var null | ListingDefinition */
  private $definition = null;

  public function __construct(
    EntityManager $entityManager,
    FilterHandler $dynamicSegmentsFilter,
    SegmentSubscribersRepository $segmentSubscribersRepository,
    SubscribersCountsController $subscribersCountsController
  ) {
    parent::__construct($entityManager);
    $this->dynamicSegmentsFilter = $dynamicSegmentsFilter;
    $this->entityManager = $entityManager;
    $this->segmentSubscribersRepository = $segmentSubscribersRepository;
    $this->subscribersCountsController = $subscribersCountsController;
  }

  public function getData(ListingDefinition $definition): array {
    $this->definition = $definition;
    $dynamicSegment = $this->getDynamicSegmentFromFilters($definition);
    if ($dynamicSegment === null) {
      return parent::getData($definition);
    }
    return $this->getDataForDynamicSegment($definition, $dynamicSegment);
  }

  public function getCount(ListingDefinition $definition): int {
    $this->definition = $definition;
    $dynamicSegment = $this->getDynamicSegmentFromFilters($definition);
    if ($dynamicSegment === null) {
      return parent::getCount($definition);
    }
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $subscribersIdsQuery = $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("count(DISTINCT $subscribersTable.id)")
      ->from($subscribersTable);
    $subscribersIdsQuery = $this->applyConstraintsForDynamicSegment($subscribersIdsQuery, $definition, $dynamicSegment);
    return (int)$subscribersIdsQuery->execute()->fetchColumn();
  }

  public function getActionableIds(ListingDefinition $definition): array {
    $this->definition = $definition;
    $ids = $definition->getSelection();
    if (!empty($ids)) {
      return $ids;
    }
    $dynamicSegment = $this->getDynamicSegmentFromFilters($definition);
    if ($dynamicSegment === null) {
      return parent::getActionableIds($definition);
    }
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $subscribersIdsQuery = $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("DISTINCT $subscribersTable.id")
      ->from($subscribersTable);
    $subscribersIdsQuery = $this->applyConstraintsForDynamicSegment($subscribersIdsQuery, $definition, $dynamicSegment);
    $idsStatement = $subscribersIdsQuery->execute();
    $result = $idsStatement->fetchAll();
    return array_column($result, 'id');
  }

  protected function applySelectClause(QueryBuilder $queryBuilder) {
    $queryBuilder->select("PARTIAL s.{id,email,firstName,lastName,status,createdAt,updatedAt,countConfirmations,wpUserId,isWoocommerceUser,engagementScore,lastSubscribedAt}");
  }

  protected function applyFromClause(QueryBuilder $queryBuilder) {
    $queryBuilder->from(SubscriberEntity::class, 's');
  }

  protected function applyGroup(QueryBuilder $queryBuilder, string $group) {
    // include/exclude deleted
    if ($group === 'trash') {
      $queryBuilder->andWhere('s.deletedAt IS NOT NULL');
    } else {
      $queryBuilder->andWhere('s.deletedAt IS NULL');
    }

    if (!in_array($group, self::$supportedStatuses)) {
      return;
    }

    if (!in_array($group, [SubscriberEntity::STATUS_SUBSCRIBED, SubscriberEntity::STATUS_UNSUBSCRIBED])) {
      $queryBuilder
        ->andWhere('s.status = :status')
        ->setParameter('status', $group);
      return;
    }

    $segment = $this->definition && array_key_exists('segment', $this->definition->getFilters()) ? $this->entityManager->find(SegmentEntity::class, (int)$this->definition->getFilters()['segment']) : null;
    if (!$segment instanceof SegmentEntity || !$segment->isStatic()) {
      $queryBuilder
        ->andWhere('s.status = :status')
        ->setParameter('status', $group);
      return;
    }

    $operator = $group === SubscriberEntity::STATUS_SUBSCRIBED ? 'AND' : 'OR';
    $queryBuilder
      ->andWhere('(s.status = :status ' . $operator . ' ss.status = :status)')
      ->setParameter('status', $group);
  }

  protected function applySearch(QueryBuilder $queryBuilder, string $search) {
    $search = Helpers::escapeSearch($search);
    $queryBuilder
      ->andWhere('s.email LIKE :search or s.firstName LIKE :search or s.lastName LIKE :search')
      ->setParameter('search', "%$search%");
  }

  protected function applyFilters(QueryBuilder $queryBuilder, array $filters) {
    if (isset($filters['segment'])) {
      if ($filters['segment'] === self::FILTER_WITHOUT_LIST) {
        $this->segmentSubscribersRepository->addConstraintsForSubscribersWithoutSegment($queryBuilder);
      } else {
        $segment = $this->entityManager->find(SegmentEntity::class, (int)$filters['segment']);
        if ($segment instanceof SegmentEntity && $segment->isStatic()) {
          $queryBuilder->join('s.subscriberSegments', 'ss', Join::WITH, 'ss.segment = :ssSegment')
            ->setParameter('ssSegment', $segment->getId());
        }
      }
    }

    // filtering by minimal updated at
    if (isset($filters['minUpdatedAt']) && $filters['minUpdatedAt'] instanceof \DateTimeInterface) {
      $queryBuilder->andWhere('s.updatedAt >= :updatedAt')
        ->setParameter('updatedAt', $filters['minUpdatedAt']);
    }

    if (isset($filters['tag'])) {
      $tag = $this->entityManager->find(TagEntity::class, (int)$filters['tag']);
      if ($tag) {
        $queryBuilder->join('s.subscriberTags', 'st', Join::WITH, 'st.tag = :stTag')
          ->setParameter('stTag', $tag);
      }
    }
  }

  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters) {
    // nothing to do here
  }

  protected function applySorting(QueryBuilder $queryBuilder, string $sortBy, string $sortOrder) {
    if (!$sortBy) {
      $sortBy = self::DEFAULT_SORT_BY;
    }
    $queryBuilder->addOrderBy("s.$sortBy", $sortOrder);
  }

  public function getGroups(ListingDefinition $definition): array {
    $queryBuilder = clone $this->queryBuilder;
    $this->applyFromClause($queryBuilder);

    $groupCounts = [
      SubscriberEntity::STATUS_SUBSCRIBED => 0,
      SubscriberEntity::STATUS_UNCONFIRMED => 0,
      SubscriberEntity::STATUS_UNSUBSCRIBED => 0,
      SubscriberEntity::STATUS_INACTIVE => 0,
      SubscriberEntity::STATUS_BOUNCED => 0,
      'trash' => 0,
    ];
    foreach (array_keys($groupCounts) as $group) {
      $groupDefinition = $group === $definition->getGroup() ? $definition : new ListingDefinition(
        $group,
        $definition->getFilters(),
        $definition->getSearch(),
        $definition->getParameters(),
        $definition->getSortBy(),
        $definition->getSortOrder(),
        $definition->getOffset(),
        $definition->getLimit(),
        $definition->getSelection()
      );
      $groupCounts[$group] = $this->getCount($groupDefinition);
    }

    $trashedCount = $groupCounts['trash'];
    unset($groupCounts['trash']);
    $totalCount = (int)array_sum($groupCounts);

    return [
      [
        'name' => 'all',
        'label' => __('All', 'mailpoet'),
        'count' => $totalCount,
      ],
      [
        'name' => SubscriberEntity::STATUS_SUBSCRIBED,
        'label' => __('Subscribed', 'mailpoet'),
        'count' => $groupCounts[SubscriberEntity::STATUS_SUBSCRIBED],
      ],
      [
        'name' => SubscriberEntity::STATUS_UNCONFIRMED,
        'label' => __('Unconfirmed', 'mailpoet'),
        'count' => $groupCounts[SubscriberEntity::STATUS_UNCONFIRMED],
      ],
      [
        'name' => SubscriberEntity::STATUS_UNSUBSCRIBED,
        'label' => __('Unsubscribed', 'mailpoet'),
        'count' => $groupCounts[SubscriberEntity::STATUS_UNSUBSCRIBED],
      ],
      [
        'name' => SubscriberEntity::STATUS_INACTIVE,
        'label' => __('Inactive', 'mailpoet'),
        'count' => $groupCounts[SubscriberEntity::STATUS_INACTIVE],
      ],
      [
        'name' => SubscriberEntity::STATUS_BOUNCED,
        'label' => __('Bounced', 'mailpoet'),
        'count' => $groupCounts[SubscriberEntity::STATUS_BOUNCED],
      ],
      [
        'name' => 'trash',
        'label' => __('Trash', 'mailpoet'),
        'count' => $trashedCount,
      ],
    ];
  }

  public function getFilters(ListingDefinition $definition): array {
    return [
      'segment' => $this->getSegmentFilter($definition),
      'tag' => $this->getTagsFilter($definition),
    ];
  }

  /**
   * @return array<array{label: string, value: string|int}>
   */
  private function getSegmentFilter(ListingDefinition $definition): array {
    $group = $definition->getGroup();

    $subscribersWithoutSegmentStats = $this->subscribersCountsController->getSubscribersWithoutSegmentStatisticsCount();
    $key = $group ?: 'all';
    $subscribersWithoutSegmentCount = $subscribersWithoutSegmentStats[$key];

    $subscribersWithoutSegmentLabel = sprintf(
      // translators: %s is the number of subscribers without a list.
      __('Subscribers without a list (%s)', 'mailpoet'),
      number_format((float)$subscribersWithoutSegmentCount)
    );

    $queryBuilder = clone $this->queryBuilder;
    $queryBuilder
      ->select('s')
      ->from(SegmentEntity::class, 's');
    if ($group !== 'trash') {
      $queryBuilder->andWhere('s.deletedAt IS NULL');
    }

    // format segment list
    $allSubscribersList = [
      'label' => __('All Lists', 'mailpoet'),
      'value' => '',
    ];

    $withoutSegmentList = [
      'label' => $subscribersWithoutSegmentLabel,
      'value' => self::FILTER_WITHOUT_LIST,
    ];

    $segmentList = [];
    foreach ($queryBuilder->getQuery()->getResult() as $segment) {
      $key = $group ?: 'all';
      $count = $this->subscribersCountsController->getSegmentStatisticsCount($segment);
      $subscribersCount = (float)$count[$key];
      // filter segments without subscribers
      if (!$subscribersCount) {
        continue;
      }
      $segmentList[] = [
        'label' => sprintf('%s (%s)', $segment->getName(), number_format($subscribersCount)),
        'value' => $segment->getId(),
      ];
    }

    usort($segmentList, function($a, $b) {
      return strcasecmp($a['label'], $b['label']);
    });

    array_unshift($segmentList, $allSubscribersList, $withoutSegmentList);
    return $segmentList;
  }

  /**
   * @return array<int, array{label: string, value: string|int}>
   */
  private function getTagsFilter(ListingDefinition $definition): array {
    $group = $definition->getGroup();

    $allTagsList = [
      'label' => __('All Tags', 'mailpoet'),
      'value' => '',
    ];

    $status = in_array($group, ['all', 'trash']) ? null : $group;
    $isDeleted = $group === 'trash';
    $tagsStatistics = $this->subscribersCountsController->getTagsStatisticsCount($status, $isDeleted);

    $tagsList = [];
    foreach ($tagsStatistics as $tagStatistics) {
      $tagsList[] = [
        'label' => sprintf('%s (%s)', $tagStatistics['name'], number_format((float)$tagStatistics['subscribersCount'])),
        'value' => $tagStatistics['id'],
      ];
    }

    array_unshift($tagsList, $allTagsList);
    return $tagsList;
  }

  private function getDataForDynamicSegment(ListingDefinition $definition, SegmentEntity $segment) {
    $queryBuilder = clone $this->queryBuilder;
    $sortBy = Helpers::underscoreToCamelCase($definition->getSortBy()) ?: self::DEFAULT_SORT_BY;
    $this->applySelectClause($queryBuilder);
    $this->applyFromClause($queryBuilder);

    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $subscribersIdsQuery = $this->entityManager
      ->getConnection()
      ->createQueryBuilder()
      ->select("DISTINCT $subscribersTable.id")
      ->from($subscribersTable);
    $subscribersIdsQuery = $this->applyConstraintsForDynamicSegment($subscribersIdsQuery, $definition, $segment);
    $subscribersIdsQuery->orderBy("$subscribersTable." . Helpers::camelCaseToUnderscore($sortBy), $definition->getSortOrder());
    $subscribersIdsQuery->setFirstResult($definition->getOffset());
    $subscribersIdsQuery->setMaxResults($definition->getLimit());

    $idsStatement = $subscribersIdsQuery->execute();
    // This shouldn't happen because execute on select SQL always returns Statement, but PHPStan doesn't know that
    if (!$idsStatement instanceof Statement) {
      $queryBuilder->andWhere('0 = 1');
      return;
    }
    $result = $idsStatement->fetchAll();
    $ids = array_column($result, 'id');
    if (count($ids)) {
      $queryBuilder->andWhere('s.id IN (:subscriberIds)')
        ->setParameter('subscriberIds', $ids);
    } else {
      $queryBuilder->andWhere('0 = 1'); // Don't return any subscribers if no ids found
    }
    $this->applySorting($queryBuilder, $sortBy, $definition->getSortOrder());
    return $queryBuilder->getQuery()->getResult();
  }

  private function applyConstraintsForDynamicSegment(
    DBALQueryBuilder $subscribersQuery,
    ListingDefinition $definition,
    SegmentEntity $segment
  ) {
    // Apply dynamic segments filters
    $subscribersQuery = $this->dynamicSegmentsFilter->apply($subscribersQuery, $segment);
    // Apply group, search to fetch only necessary ids
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    if ($definition->getSearch()) {
      $search = Helpers::escapeSearch((string)$definition->getSearch());
      $subscribersQuery
        ->andWhere("$subscribersTable.email LIKE :search or $subscribersTable.first_name LIKE :search or $subscribersTable.last_name LIKE :search")
        ->setParameter('search', "%$search%");
    }
    if ($definition->getGroup()) {
      if ($definition->getGroup() === 'trash') {
        $subscribersQuery->andWhere("$subscribersTable.deleted_at IS NOT NULL");
      } else {
        $subscribersQuery->andWhere("$subscribersTable.deleted_at IS NULL");
      }
      if (in_array($definition->getGroup(), self::$supportedStatuses)) {
        $subscribersQuery
          ->andWhere("$subscribersTable.status = :status")
          ->setParameter('status', $definition->getGroup());
      }
    }
    return $subscribersQuery;
  }

  private function getDynamicSegmentFromFilters(ListingDefinition $definition): ?SegmentEntity {
    $filters = $definition->getFilters();
    if (!$filters || !isset($filters['segment'])) {
      return null;
    }
    if ($filters['segment'] === self::FILTER_WITHOUT_LIST) {
      return null;
    }
    $segment = $this->entityManager->find(SegmentEntity::class, (int)$filters['segment']);
    if (!$segment instanceof SegmentEntity) {
      return null;
    }
    return $segment->isStatic() ? null : $segment;
  }
}
