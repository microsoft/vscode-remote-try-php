<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\SegmentDependencyValidator;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class FilterHandler {
  /** @var EntityManager */
  private $entityManager;

  /** @var SegmentDependencyValidator */
  private $segmentDependencyValidator;

  /** @var FilterFactory */
  private $filterFactory;

  public function __construct(
    EntityManager $entityManager,
    SegmentDependencyValidator $segmentDependencyValidator,
    FilterFactory $filterFactory
  ) {

    $this->entityManager = $entityManager;
    $this->segmentDependencyValidator = $segmentDependencyValidator;
    $this->filterFactory = $filterFactory;
  }

  public function apply(QueryBuilder $queryBuilder, SegmentEntity $segment): QueryBuilder {
    $filters = $segment->getDynamicFilters();
    $filterSelects = [];
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $pluginsForAllFiltersMissing = $this->segmentDependencyValidator->getMissingPluginsByAllFilters($filters);
    foreach ($filters as $filter) {
      $subscribersIdsQuery = $this->entityManager
        ->getConnection()
        ->createQueryBuilder()
        ->select("DISTINCT $subscribersTable.id as inner_subscriber_id")
        ->from($subscribersTable);
      // When a required plugin is missing we want to return empty result
      if ($pluginsForAllFiltersMissing || $this->segmentDependencyValidator->getMissingPluginsByFilter($filter)) {
        $subscribersIdsQuery->andWhere('1 = 0');
      } else {
        $this->filterFactory->getFilterForFilterEntity($filter)->apply($subscribersIdsQuery, $filter);
      }
      $filterSelects[] = $subscribersIdsQuery->getSQL();
      $queryBuilder->setParameters(
        array_merge(
          $subscribersIdsQuery->getParameters(),
          $queryBuilder->getParameters()
        ),
        array_merge(
          $subscribersIdsQuery->getParameterTypes(),
          $queryBuilder->getParameterTypes()
        )
      );
    }
    $this->joinSubqueries($queryBuilder, $segment, $filterSelects);
    return $queryBuilder;
  }

  private function joinSubqueries(QueryBuilder $queryBuilder, SegmentEntity $segment, array $subQueries): QueryBuilder {
    $filter = $segment->getDynamicFilters()->first();
    if (!$filter) return $queryBuilder;
    $subscribersTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    if ($segment->getFiltersConnectOperator() === DynamicSegmentFilterData::CONNECT_TYPE_OR) {
      // the final query: SELECT * FROM subscribers INNER JOIN (filter_select1 UNION filter_select2) filtered_subscribers ON filtered_subscribers.inner_subscriber_id = id
      $queryBuilder->innerJoin(
        $subscribersTable,
        sprintf('(%s)', join(' UNION ', $subQueries)),
        'filtered_subscribers',
        "filtered_subscribers.inner_subscriber_id = $subscribersTable.id"
      );
      return $queryBuilder;
    }

    foreach ($subQueries as $key => $subQuery) {
      // we need a unique name for each subquery so that we can join them together in the sql query - just make sure the identifier starts with a letter, not a number
      $subqueryName = 'a' . $key;
      $queryBuilder->innerJoin(
        $subscribersTable,
        "($subQuery)",
        $subqueryName,
        "$subqueryName.inner_subscriber_id = $subscribersTable.id"
      );
    }
    return $queryBuilder;
  }
}
