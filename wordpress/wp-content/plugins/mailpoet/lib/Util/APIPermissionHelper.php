<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


if (!class_exists('\WP_REST_Posts_Controller')) {
  require_once ABSPATH . '/wp-includes/rest-api/endpoints/class-wp-rest-controller.php';
  require_once ABSPATH . '/wp-includes/rest-api/endpoints/class-wp-rest-posts-controller.php';
}

class APIPermissionHelper extends \WP_REST_Posts_Controller {
  public function __construct() {
    // constructor is needed to override parent constructor
  }

  public function checkReadPermission(\WP_Post $post): bool {
    return parent::check_read_permission($post);
  }

  /**
   * Checks if a given post type can be viewed or managed.
   * Refrain from checking `show_in_rest` contrary to what parent::check_is_post_type_allowed does
   *
   * @param \WP_Post_Type|string $post_type Post type name or object.
   * @return bool Whether the post type is allowed in REST.
   * @see parent::check_is_post_type_allowed
   */
  // phpcs:disable PSR1.Methods.CamelCapsMethodName
  protected function check_is_post_type_allowed($post_type) {
    if (!is_object($post_type)) {
      $post_type = get_post_type_object($post_type);
    }

    return !empty($post_type) && $post_type->public;
  }
}
