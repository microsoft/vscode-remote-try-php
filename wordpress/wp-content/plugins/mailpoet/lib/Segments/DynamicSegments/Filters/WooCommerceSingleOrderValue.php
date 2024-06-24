<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Util\Security;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommerceSingleOrderValue implements Filter {
  const ACTION_SINGLE_ORDER_VALUE = 'singleOrderValue';

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  public function __construct(
    WooFilterHelper $wooFilterHelper
  ) {
    $this->wooFilterHelper = $wooFilterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $type = $filterData->getParam('single_order_value_type');
    $amount = $filterData->getParam('single_order_value_amount');
    $isAllTime = $filterData->getParam('timeframe') === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME;
    $parameterSuffix = $filter->getId() ?? Security::generateRandomString();

    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);

    if (!$isAllTime) {
      $days = $filterData->getParam('days');
      if (!is_string($days)) {
        $days = '1'; // Default to last day
      }
      $date = Carbon::now()->subDays((int)$days);
      $dateParam = "date_$parameterSuffix";
      $queryBuilder
        ->andWhere("$orderStatsAlias.date_created >= :$dateParam")
        ->setParameter($dateParam, $date->toDateTimeString());
    }

    if ($type === '=') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales = :amount" . $parameterSuffix);
    } elseif ($type === '!=') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales != :amount" . $parameterSuffix);
    } elseif ($type === '>') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales > :amount" . $parameterSuffix);
    } elseif ($type === '>=') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales >= :amount" . $parameterSuffix);
    } elseif ($type === '<') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales < :amount" . $parameterSuffix);
    } elseif ($type === '<=') {
      $queryBuilder->andWhere("$orderStatsAlias.total_sales <= :amount" . $parameterSuffix);
    }

    $queryBuilder->setParameter('amount' . $parameterSuffix, $amount);

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
