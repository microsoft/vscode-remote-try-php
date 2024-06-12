<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\WordPress;

class ContextFactory {

  /** @var WordPress  */
  private $wp;

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  /** @return mixed[] */
  public function getContextData(): array {
    return [
      'comment_statuses' => $this->getCommentStatuses(),
      'post_types' => $this->getPostTypes(),
      'taxonomies' => $this->getTaxonomies(),
    ];
  }

  /**
   * @return string[][]
   */
  private function getCommentStatuses(): array {
    $statiMap = $this->wp->getCommentStatuses();
    $stati = [];
    foreach ($statiMap as $id => $name) {
      $stati[] = [
        'id' => $id,
        'name' => $name,
      ];
    }
    return $stati;
  }

  /**
   * @return array<int, array<string, array<string, bool>|bool|string>>
   */
  private function getPostTypes(): array {
    /** @var \WP_Post_Type[] $postTypes */
    $postTypes = $this->wp->getPostTypes([], 'object');
    return array_values(array_map(function(\WP_Post_Type $type): array {

      $supports = ['comments' => false];
      foreach (array_keys($supports) as $key) {
        $supports[$key] = $this->wp->postTypeSupports($type->name, $key);
      }

      return [
        'name' => $type->name,
        'label' => $type->label,
        'supports' => $supports,
        //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'show_in_rest' => $type->show_in_rest,
        //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'rest_base' => $type->rest_base,
        //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'rest_namespace' => $type->rest_namespace,
        'public' => $type->public,
      ];
    },
    $postTypes));
  }

  /**
   * @return array<int, array<string, string[]|bool|string>>
   */
  private function getTaxonomies(): array {
    /** @var \WP_Taxonomy[] $taxonomies */
    $taxonomies = array_filter(
      $this->wp->getTaxonomies([], 'object'),
      function($object): bool {
        return $object instanceof \WP_Taxonomy;
      }
    );
    return array_values(array_map(
      function(\WP_Taxonomy $taxonomy): array {
        return [
          'name' => $taxonomy->name,
          'label' => $taxonomy->label,
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          'show_in_rest' => $taxonomy->show_in_rest,
          'public' => $taxonomy->public,
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          'rest_base' => $taxonomy->rest_base,
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          'rest_namespace' => $taxonomy->rest_namespace,
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          'object_type' => (array)$taxonomy->object_type,
        ];
      },
      $taxonomies
    ));
  }
}
