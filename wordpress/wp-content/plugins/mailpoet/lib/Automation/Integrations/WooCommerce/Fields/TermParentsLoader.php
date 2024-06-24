<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\WordPress;

class TermParentsLoader {
  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  /**
   * @param int[] $termIds
   * @return int[]
   */
  public function getParentIds(array $termIds): array {
    if (count($termIds) === 0) {
      return [];
    }
    $idsPlaceholder = implode(',', array_fill(0, count($termIds), '%s'));

    $wpdb = $this->wordPress->getWpdb();
    /** @var literal-string $query - PHPStan expects literal-string */
    $query = "
      SELECT DISTINCT tt.parent
      FROM {$wpdb->term_taxonomy} AS tt
      WHERE tt.parent != 0
      AND tt.term_id IN ($idsPlaceholder)
    ";
    $statement = (string)$wpdb->prepare($query, $termIds);

    $parentIds = array_map('intval', $wpdb->get_col($statement));
    if (count($parentIds) === 0) {
      return [];
    }
    return array_values(
      array_unique(
        array_merge($parentIds, $this->getParentIds($parentIds))
      )
    );
  }
}
