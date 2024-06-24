<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\WordPress;
use WP_Error;
use WP_Term;

class TermOptionsBuilder {
  /** @var WordPress */
  private $wordPress;

  /** @var array<string, array<array{id: int, name: string}>> */
  private $cache = [];

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  /** @return array<array{id: int, name: string}> */
  public function getTermOptions(string $taxonomy): array {
    if (!isset($this->cache[$taxonomy])) {
      $this->cache[$taxonomy] = $this->fetchTermOptions($taxonomy);
    }
    return $this->cache[$taxonomy];
  }

  public function resetCache(): void {
    $this->cache = [];
  }

  /** @return array<array{id: int, name: string}> */
  private function fetchTermOptions(string $taxonomy): array {
    /** @var WP_Term[]|WP_Error $terms */
    $terms = $this->wordPress->getTerms(['taxonomy' => $taxonomy, 'hide_empty' => false, 'orderby' => 'name']);
    if ($terms instanceof WP_Error) {
      return [];
    }

    $termsMap = [];
    foreach ($terms as $term) {
      $termsMap[$term->parent][] = $term;
    }
    return $this->buildTermsList($termsMap);
  }

  /**
   * @param array<int, array<WP_Term>> $termsMap
   * @return array<array{id: int, name: string}>
   */
  private function buildTermsList(array $termsMap, int $parentId = 0): array {
    $list = [];
    foreach ($termsMap[$parentId] ?? [] as $term) {
      $id = $term->term_id; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $list[] = ['id' => $id, 'name' => $term->name];
      if (isset($termsMap[$id])) {
        foreach ($this->buildTermsList($termsMap, $id) as $child) {
          $list[] = ['id' => $child['id'], 'name' => "$term->name | {$child['name']}"];
        }
      }
    }
    return $list;
  }
}
