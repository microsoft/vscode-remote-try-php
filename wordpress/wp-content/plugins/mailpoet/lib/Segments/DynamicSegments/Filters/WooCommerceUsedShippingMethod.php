<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\WooCommerce\Helper;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommerceUsedShippingMethod implements Filter {
  const ACTION = 'usedShippingMethod';

  const VALID_OPERATORS = [
    DynamicSegmentFilterData::OPERATOR_NONE,
    DynamicSegmentFilterData::OPERATOR_ANY,
    DynamicSegmentFilterData::OPERATOR_ALL,
  ];

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  /** @var Helper */
  private $wooHelper;

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper,
    Helper $wooHelper
  ) {
    $this->wooFilterHelper = $wooFilterHelper;
    $this->wooHelper = $wooHelper;
    $this->filterHelper = $filterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $operator = $filterData->getParam('operator');
    $shippingMethodInstanceIds = $filterData->getParam('shipping_methods');
    $isAllTime = $filterData->getParam('timeframe') === DynamicSegmentFilterData::TIMEFRAME_ALL_TIME;

    $days = $filterData->getParam('days');

    if (!is_string($operator) || !in_array($operator, self::VALID_OPERATORS, true)) {
      throw new InvalidFilterException('Invalid operator', InvalidFilterException::MISSING_OPERATOR);
    }

    if (!is_array($shippingMethodInstanceIds) || empty($shippingMethodInstanceIds)) {
      throw new InvalidFilterException('Missing shipping methods', InvalidFilterException::MISSING_VALUE);
    }

    $data = $filterData->getData();
    $this->filterHelper->validateDaysPeriodData((array)$data);

    $includedStatuses = array_keys($this->wooHelper->getOrderStatuses());
    $failedKey = array_search('wc-failed', $includedStatuses, true);
    if ($failedKey !== false) {
      unset($includedStatuses[$failedKey]);
    }
    $date = is_int($days) ? Carbon::now()->subDays($days) : Carbon::now();

    switch ($operator) {
      case DynamicSegmentFilterData::OPERATOR_ANY:
        $this->applyForAnyOperator($queryBuilder, $includedStatuses, $shippingMethodInstanceIds, $date, $isAllTime);
        break;
      case DynamicSegmentFilterData::OPERATOR_ALL:
        $this->applyForAllOperator($queryBuilder, $includedStatuses, $shippingMethodInstanceIds, $date, $isAllTime);
        break;
      case DynamicSegmentFilterData::OPERATOR_NONE:
        $this->applyForNoneOperator($queryBuilder, $includedStatuses, $shippingMethodInstanceIds, $date, $isAllTime);
        break;
    }

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $lookupData = ['shippingMethods' => []];
    if (!$this->wooHelper->isWooCommerceActive()) {
      return $lookupData;
    }
    $allMethods = $this->wooHelper->getShippingMethodInstancesData();
    $configuredShippingMethodInstanceIds = $filterData->getArrayParam('shipping_methods');

    foreach ($configuredShippingMethodInstanceIds as $instanceId) {
      if (isset($allMethods[$instanceId])) {
        $data = $allMethods[$instanceId];
        $lookupData['shippingMethods'][$instanceId] = $data['name'];
      }
    }

    return $lookupData;
  }

  private function applyForAnyOperator(QueryBuilder $queryBuilder, array $includedStatuses, array $shippingMethodInstanceIds, Carbon $date, bool $isAllTime): void {
    $instanceIdsParam = $this->filterHelper->getUniqueParameterName('instanceIds');

    $orderItemsTable = $this->filterHelper->getPrefixedTable('woocommerce_order_items');
    $orderItemsTableAlias = 'orderItems';
    $orderItemMetaTable = $this->filterHelper->getPrefixedTable('woocommerce_order_itemmeta');
    $orderItemMetaTableAlias = 'orderItemMeta';
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder, $includedStatuses);
    $queryBuilder
      ->innerJoin($orderStatsAlias, $orderItemsTable, $orderItemsTableAlias, "$orderStatsAlias.order_id = $orderItemsTableAlias.order_id")
      ->innerJoin($orderItemsTableAlias, $orderItemMetaTable, $orderItemMetaTableAlias, "$orderItemsTableAlias.order_item_id = $orderItemMetaTableAlias.order_item_id")
      ->andWhere("$orderItemsTableAlias.order_item_type = 'shipping'")
      ->andWhere("$orderItemMetaTableAlias.meta_key = 'instance_id'")
      ->andWhere("$orderItemMetaTableAlias.meta_value IN (:$instanceIdsParam)")
      ->setParameter($instanceIdsParam, $shippingMethodInstanceIds, Connection::PARAM_STR_ARRAY);
    if (!$isAllTime) {
      $dateParam = $this->filterHelper->getUniqueParameterName('date');
      $queryBuilder
        ->andWhere("$orderStatsAlias.date_created >= :$dateParam")
        ->setParameter($dateParam, $date->toDateTimeString());
    }
  }

  private function applyForAllOperator(QueryBuilder $queryBuilder, array $includedStatuses, array $shippingMethodInstanceIds, Carbon $date, bool $isAllTime): void {
    $orderItemTypeParam = $this->filterHelper->getUniqueParameterName('orderItemType');
    $instanceIdsParam = $this->filterHelper->getUniqueParameterName('instanceIds');

    $orderItemsTable = $this->filterHelper->getPrefixedTable('woocommerce_order_items');
    $orderItemsTableAlias = 'orderItems';
    $orderItemMetaTable = $this->filterHelper->getPrefixedTable('woocommerce_order_itemmeta');
    $orderItemMetaTableAlias = 'orderItemMeta';
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder, $includedStatuses);

    $queryBuilder
      ->innerJoin($orderStatsAlias, $orderItemsTable, $orderItemsTableAlias, "$orderStatsAlias.order_id = $orderItemsTableAlias.order_id")
      ->innerJoin($orderItemsTableAlias, $orderItemMetaTable, $orderItemMetaTableAlias, "$orderItemsTableAlias.order_item_id = $orderItemMetaTableAlias.order_item_id")
      ->andWhere("$orderItemsTableAlias.order_item_type = :$orderItemTypeParam")
      ->andWhere("$orderItemMetaTableAlias.meta_key = 'instance_id'")
      ->andWhere("$orderItemMetaTableAlias.meta_value IN (:$instanceIdsParam)")
      ->setParameter($orderItemTypeParam, 'shipping')
      ->setParameter($instanceIdsParam, $shippingMethodInstanceIds, Connection::PARAM_STR_ARRAY)
      ->groupBy('inner_subscriber_id')
      ->having("COUNT(DISTINCT($orderItemMetaTableAlias.meta_value)) = " . count($shippingMethodInstanceIds));

    if (!$isAllTime) {
      $dateParam = $this->filterHelper->getUniqueParameterName('date');
      $queryBuilder
        ->andWhere("$orderStatsAlias.date_created >= :$dateParam")
        ->setParameter($dateParam, $date->toDateTimeString());
    }
  }

  private function applyForNoneOperator(QueryBuilder $queryBuilder, array $includedStatuses, array $shippingMethodInstanceIds, Carbon $date, bool $isAllTime): void {
    $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
    $this->applyForAnyOperator($subQuery, $includedStatuses, $shippingMethodInstanceIds, $date, $isAllTime);
    $subscribersTable = $this->filterHelper->getSubscribersTable();
    $queryBuilder->andWhere($queryBuilder->expr()->notIn("$subscribersTable.id", $this->filterHelper->getInterpolatedSQL($subQuery)));
  }
}
