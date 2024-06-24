<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\StatisticsClickEntity;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class NumberOfClicks implements Filter {
  const ACTION = 'numberOfClicks';

  /** @var EntityManager */
  private $entityManager;

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    EntityManager $entityManager,
    FilterHelper $filterHelper
  ) {
    $this->entityManager = $entityManager;
    $this->filterHelper = $filterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $clickCount = $filterData->getIntParam('clicks');
    $operator = $filterData->getStringParam('operator');
    $timeframe = $filterData->getStringParam('timeframe');
    $statsTable = $this->entityManager->getClassMetadata(StatisticsClickEntity::class)->getTableName();
    $subscribersTable = $this->filterHelper->getSubscribersTable();

    if ($timeframe === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME) {
      $queryBuilder->leftJoin($subscribersTable, $statsTable, 'clicks', "{$subscribersTable}.id = clicks.subscriber_id");
    } else {
      $days = $filterData->getIntParam('days');
      $dateParam = $this->filterHelper->getUniqueParameterName('days');
      $queryBuilder->leftJoin($subscribersTable, $statsTable, 'clicks', "{$subscribersTable}.id = clicks.subscriber_id AND clicks.created_at >= :$dateParam");
      $queryBuilder->setParameter($dateParam, CarbonImmutable::now()->subDays($days)->startOfDay());
    }

    $queryBuilder->groupBy("$subscribersTable.id");
    $clicksCountParam = $this->filterHelper->getUniqueParameterName('clicks');

    if ($operator === 'equals') {
      $queryBuilder->having("count(clicks.id) = :$clicksCountParam");
    } else if ($operator === 'not_equals') {
      $queryBuilder->having("count(clicks.id) != :$clicksCountParam");
    } else if ($operator === 'less') {
      $queryBuilder->having("count(clicks.id) < :$clicksCountParam");
    } else {
      $queryBuilder->having("count(clicks.id) > :$clicksCountParam");
    }
    $queryBuilder->setParameter($clicksCountParam, $clickCount);

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
