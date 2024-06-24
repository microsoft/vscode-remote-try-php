<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class WooCommerceCustomerTextField implements Filter {
  const CITY = 'customerInCity';
  const POSTAL_CODE = 'customerInPostalCode';

  const ACTIONS = [self::CITY, self::POSTAL_CODE];

  /** @var FilterHelper */
  private $filterHelper;

  /** @var WooFilterHelper */
  private $wooFilterHelper;

  public function __construct(
    FilterHelper $filterHelper,
    WooFilterHelper $wooFilterHelper
  ) {
    $this->filterHelper = $filterHelper;
    $this->wooFilterHelper = $wooFilterHelper;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $action = $filterData->getParam('action');
    $value = $filterData->getParam('value');
    $operator = $filterData->getParam('operator');

    if (!is_string($action)) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }

    if (!is_string($value)) {
      throw new InvalidFilterException('Missing value', InvalidFilterException::MISSING_VALUE);
    }

    if (!is_string($operator)) {
      throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
    }

    $customerLookupAlias = $this->wooFilterHelper->applyCustomerLookupJoin($queryBuilder);
    $column = sprintf("%s.%s", $customerLookupAlias, $this->getColumnNameForAction($action));
    $parameter = $this->filterHelper->getUniqueParameterName('customerTextField');

    switch ($operator) {
      case DynamicSegmentFilterData::OPERATOR_IS:
        $queryBuilder->andWhere("$column = :$parameter");
        break;
      case DynamicSegmentFilterData::OPERATOR_IS_NOT:
        $queryBuilder->andWhere("$column != :$parameter");
        break;
      case DynamicSegmentFilterData::OPERATOR_CONTAINS:
        $queryBuilder->andWhere($queryBuilder->expr()->like($column, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_CONTAINS:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($column, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_STARTS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->like($column, ":$parameter"));
        $value = Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_STARTS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($column, ":$parameter"));
        $value = Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_ENDS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->like($column, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value);
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_ENDS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($column, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value);
        break;
      default:
        throw new InvalidFilterException('Invalid operator', InvalidFilterException::MISSING_OPERATOR);
    }

    $queryBuilder->setParameter($parameter, $value);

    return $queryBuilder;
  }

  private function getColumnNameForAction(string $field): string {
    switch ($field) {
      case self::CITY:
        return 'city';
      case self::POSTAL_CODE:
        return 'postcode';
    }

    throw new InvalidFilterException('Invalid action');
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
