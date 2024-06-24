<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

interface Filter {
  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder;

  /**
   * At sending time, we store the current state of every filter so we can tell in the future how it was configured. This
   * method should be used to return any data that might change after sending time. For example, if a filter stores IDs
   * of related entities, we should try to look up descriptive names for those entities in case they get deleted or
   * renamed later.
   *
   * @param DynamicSegmentFilterData $filterData
   *
   * @return array
   */
  public function getLookupData(DynamicSegmentFilterData $filterData): array;
}
