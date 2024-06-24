<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Util\Security;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

class SubscriberScore implements Filter {
  const TYPE = 'subscriberScore';

  const HIGHER_THAN = 'higherThan';
  const LOWER_THAN = 'lowerThan';
  const EQUALS = 'equals';
  const NOT_EQUALS = 'not_equals';
  const UNKNOWN = 'unknown';
  const NOT_UNKNOWN = 'not_unknown';

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $filterData = $filter->getFilterData();
    $value = $filterData->getParam('value');
    $operator = $filterData->getParam('operator');
    $parameterSuffix = $filter->getId() ?: Security::generateRandomString();
    $parameter = 'score' . $parameterSuffix;

    if ($operator === self::HIGHER_THAN) {
      $queryBuilder->andWhere("engagement_score > :$parameter");
    } elseif ($operator === self::LOWER_THAN) {
      $queryBuilder->andWhere("engagement_score < :$parameter");
    } elseif ($operator === self::EQUALS) {
      $queryBuilder->andWhere("engagement_score = :$parameter");
    } elseif ($operator === self::NOT_EQUALS) {
      $queryBuilder->andWhere("engagement_score != :$parameter");
    } elseif ($operator === self::UNKNOWN) {
      $queryBuilder->andWhere("engagement_score IS NULL");
    } elseif ($operator === self::NOT_UNKNOWN) {
      $queryBuilder->andWhere("engagement_score IS NOT NULL");
    } else {
      throw new InvalidFilterException('Incorrect value for operator', InvalidFilterException::MISSING_VALUE);
    }
    $queryBuilder->setParameter($parameter, (int)$value);

    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    return [];
  }
}
