<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\DBAL\Query\QueryBuilder;

/**
 * The filters in this class are primarily intended for the premium plugin
 */
class SubscriberTag implements Filter {
  const TYPE = 'subscriberTag';

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function apply(QueryBuilder $queryBuilder, DynamicSegmentFilterEntity $filter): QueryBuilder {
    $this->wp->applyFilters('mailpoet_dynamic_segments_filter_subscriber_tag_apply', $queryBuilder, $filter);
    return $queryBuilder;
  }

  public function getLookupData(DynamicSegmentFilterData $filterData): array {
    $default = ['tags' => []];
    $filteredLookupData = $this->wp->applyFilters('mailpoet_dynamic_segments_filter_subscriber_tag_getLookupData', $default, $filterData);
    if (is_array($filteredLookupData)) {
      return $filteredLookupData;
    }
    return $default;
  }
}
