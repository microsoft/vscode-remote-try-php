<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\StatisticsNewsletterEntity;
use MailPoetVendor\Carbon\CarbonImmutable;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class EmailsReceived implements Filter {
  const ACTION = 'numberReceived';

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
    $emailCount = $filterData->getIntParam('emails');
    $operator = $filterData->getStringParam('operator');
    $timeframe = $filterData->getStringParam('timeframe');
    $statsTable = $this->entityManager->getClassMetadata(StatisticsNewsletterEntity::class)->getTableName();
    $subscribersTable = $this->filterHelper->getSubscribersTable();

    if ($timeframe === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME) {
      $queryBuilder->leftJoin($subscribersTable, $statsTable, 'emails', "{$subscribersTable}.id = emails.subscriber_id");
    } else {
      $days = $filterData->getIntParam('days');
      $dateParam = $this->filterHelper->getUniqueParameterName('days');
      $queryBuilder->leftJoin($subscribersTable, $statsTable, 'emails', "{$subscribersTable}.id = emails.subscriber_id AND emails.sent_at >= :$dateParam");
      $queryBuilder->setParameter($dateParam, CarbonImmutable::now()->subDays($days)->startOfDay());
    }

    $queryBuilder->groupBy("$subscribersTable.id");
    $emailCountParam = $this->filterHelper->getUniqueParameterName('emails');

    if ($operator === 'equals') {
      $queryBuilder->having("count(emails.id) = :$emailCountParam");
    } else if ($operator === 'not_equals') {
      $queryBuilder->having("count(emails.id) != :$emailCountParam");
    } else if ($operator === 'less') {
      $queryBuilder->having("count(emails.id) < :$emailCountParam");
    } else {
      $queryBuilder->having("count(emails.id) > :$emailCountParam");
    }
    $queryBuilder->setParameter($emailCountParam, $emailCount);

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
