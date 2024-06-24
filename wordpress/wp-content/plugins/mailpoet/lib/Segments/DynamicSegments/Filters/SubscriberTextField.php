<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class SubscriberTextField implements Filter {
  const FIRST_NAME = 'subscriberFirstName';
  const LAST_NAME = 'subscriberLastName';
  const EMAIL = 'subscriberEmail';

  const TYPES = [self::FIRST_NAME, self::LAST_NAME, self::EMAIL];

  /** @var FilterHelper */
  private $filterHelper;

  public function __construct(
    FilterHelper $filterHelper
  ) {
    $this->filterHelper = $filterHelper;
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

    $columnName = $this->getColumnNameForAction($action);
    $parameter = $this->filterHelper->getUniqueParameterName('subscriberText');

    switch ($operator) {
      case DynamicSegmentFilterData::OPERATOR_IS:
        $queryBuilder->andWhere("$columnName = :$parameter");
        break;
      case DynamicSegmentFilterData::OPERATOR_IS_NOT:
        $queryBuilder->andWhere("$columnName != :$parameter");
        break;
      case DynamicSegmentFilterData::OPERATOR_CONTAINS:
        $queryBuilder->andWhere($queryBuilder->expr()->like($columnName, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_CONTAINS:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($columnName, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_STARTS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->like($columnName, ":$parameter"));
        $value = Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_STARTS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($columnName, ":$parameter"));
        $value = Helpers::escapeSearch($value) . '%';
        break;
      case DynamicSegmentFilterData::OPERATOR_ENDS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->like($columnName, ":$parameter"));
        $value = '%' . Helpers::escapeSearch($value);
        break;
      case DynamicSegmentFilterData::OPERATOR_NOT_ENDS_WITH:
        $queryBuilder->andWhere($queryBuilder->expr()->notLike($columnName, ":$parameter"));
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
      case self::FIRST_NAME:
        return 'first_name';
      case self::LAST_NAME:
        return 'last_name';
      case self::EMAIL:
        return 'email';
    }

    throw new InvalidFilterException('Invalid action');
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
