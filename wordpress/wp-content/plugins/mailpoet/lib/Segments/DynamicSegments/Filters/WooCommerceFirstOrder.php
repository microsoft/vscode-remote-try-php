<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommerceFirstOrder implements Filter {
  const ACTION = 'firstOrder';

  /** @var DateFilterHelper */
  private $dateFilterHelper;

  /** @var FilterHelper */
  private $filterHelper;

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  public function __construct(
    DateFilterHelper $dateFilterHelper,
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper
  ) {
    $this->dateFilterHelper = $dateFilterHelper;
    $this->filterHelper = $filterHelper;
    $this->wooFilterHelper = $wooFilterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $operator = $this->dateFilterHelper->getOperatorFromFilter($filter);
    $dateValue = $this->dateFilterHelper->getDateValueFromFilter($filter);
    $date = $this->dateFilterHelper->getDateStringForOperator($operator, $dateValue);
    $subscribersTable = $this->filterHelper->getSubscribersTable();

    if (in_array($operator, [DateFilterHelper::NOT_ON, DateFilterHelper::NOT_IN_THE_LAST])) {
      $subQuery = $this->filterHelper->getNewSubscribersQueryBuilder();
      $this->applyConditionsToQueryBuilder($operator, $date, $subQuery);
      $queryBuilder->andWhere($queryBuilder->expr()->notIn("{$subscribersTable}.id", $this->filterHelper->getInterpolatedSQL($subQuery)));
    } else {
      $this->applyConditionsToQueryBuilder($operator, $date, $queryBuilder);
    }

    return $queryBuilder;
  }

  private function applyConditionsToQueryBuilder(string $operator, string $date, QueryBuilder $queryBuilder): QueryBuilder {
    $orderStatsAlias = $this->wooFilterHelper->applyOrderStatusFilter($queryBuilder);
    $dateParam = $this->filterHelper->getUniqueParameterName('date');
    $subscribersTable = $this->filterHelper->getSubscribersTable();

    $queryBuilder->groupBy("$subscribersTable.id");

    switch ($operator) {
      case DateFilterHelper::BEFORE:
        $queryBuilder->andHaving("MIN($orderStatsAlias.date_created) < :$dateParam");
        break;
      case DateFilterHelper::AFTER:
        $queryBuilder->andHaving("MIN($orderStatsAlias.date_created) > :$dateParam");
        break;
      case DateFilterHelper::IN_THE_LAST:
      case DateFilterHelper::NOT_IN_THE_LAST:
      case DateFilterHelper::ON_OR_AFTER:
        $queryBuilder->andHaving("MIN($orderStatsAlias.date_created) >= :$dateParam");
        break;
      case DateFilterHelper::ON:
      case DateFilterHelper::NOT_ON:
        $queryBuilder->andHaving("MIN($orderStatsAlias.date_created) = :$dateParam");
        break;
      case DateFilterHelper::ON_OR_BEFORE:
        $queryBuilder->andHaving("MIN($orderStatsAlias.date_created) <= :$dateParam");
        break;
      default:
        throw new InvalidFilterException('Incorrect value for operator', InvalidFilterException::MISSING_VALUE);
    }
    $queryBuilder->setParameter($dateParam, $date);

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
