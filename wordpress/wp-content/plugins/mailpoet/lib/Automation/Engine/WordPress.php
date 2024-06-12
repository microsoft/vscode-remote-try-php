<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine;

if (!defined('ABSPATH')) exit;


use DateTimeZone;
use WP_Comment;
use WP_Error;
use WP_Locale;
use WP_Post;
use WP_Term;
use WP_User;
use wpdb;

class WordPress {
  public function getWpdb(): wpdb {
    global $wpdb;
    return $wpdb;
  }

  public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool {
    return add_action($hookName, $callback, $priority, $acceptedArgs);
  }

  public function removeAction(string $hookName, callable $callback, int $priority = 10): bool {
    return remove_action($hookName, $callback, $priority);
  }

  /** @param mixed ...$arg */
  public function doAction(string $hookName, ...$arg): void {
    do_action($hookName, ...$arg);
  }

  /**
   * @param mixed $value
   * @param mixed ...$args
   * @return mixed
   */
  public function applyFilters(string $hookName, $value, ...$args) {
    return apply_filters($hookName, $value, ...$args);
  }

  public function wpTimezone(): DateTimeZone {
    return wp_timezone();
  }

  public function wpGetCurrentUser(): WP_User {
    return wp_get_current_user();
  }

  /** @param mixed ...$args */
  public function currentUserCan(string $capability, ...$args): bool {
    return current_user_can($capability, ...$args);
  }

  public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool {
    return register_rest_route($namespace, $route, $args, $override);
  }

  public function getWpLocale(): WP_Locale {
    global $wp_locale;
    return $wp_locale;
  }

  /**
   * @param 'ARRAY_A'|'ARRAY_N'|'OBJECT' $object
   * @return array|WP_Post|null
   */
  public function getPost(int $id, string $object = OBJECT) {
    return get_post($id, $object);
  }

  /** @return WP_Post[]|int[] */
  public function getPosts(array $args = null): array {
    return get_posts($args);
  }

  /**
   * @param string|array $args
   * @return WP_Comment[]|int[]|int
   */
  public function getComments($args = '') {
    return get_comments($args);
  }

  /**
   * @param string $email
   * @return false|string
   */
  public function isEmail(string $email) {
    return is_email($email);
  }

  /**
   * @param 'ARRAY_A'|'ARRAY_N'|'OBJECT' $output
   * @return WP_Comment|array|null
   */
  public function getComment(int $id, string $output = OBJECT) {
    return get_comment($id, $output);
  }

  /**
   * @param array|string $args
   * @param array|string $deprecated
   * @return WP_Term[]|int[]|string[]|string|WP_Error
   */
  public function getTerms($args = [], $deprecated = '') {
    return get_terms($args, $deprecated);
  }

  /**
   * @param string|int $idOrEmail
   * @param array $args
   * @return false|string
   */
  public function getAvatarUrl($idOrEmail, $args = null) {
    return get_avatar_url($idOrEmail, $args);
  }

  /**
   * @param string $optionName
   * @param mixed $default
   * @return false|mixed|void
   */
  public function getOption(string $optionName, $default = false) {
    return get_option($optionName, $default);
  }

  /**
   * @return string[]
   */
  public function getCommentStatuses(): array {
    return get_comment_statuses();
  }

  public function getPostStatuses(): array {
    return get_post_statuses();
  }

  /**
   * @return array<int,int|string|WP_Term>|string|WP_Error
   */
  public function wpGetPostTerms(int $postId, string $taxonomy, array $args = []) {
    return wp_get_post_terms($postId, $taxonomy, $args);
  }

  /**
   * @param int|\WP_Comment $comment
   * @return false|string
   */
  public function wpGetCommentStatus($comment) {
    return wp_get_comment_status($comment);
  }

  /**
   * @return string[]|\WP_Post_Type[]
   */
  public function getPostTypes(array $args = [], string $output = 'names', string $operator = 'and'): array {
    return get_post_types($args, $output, $operator);
  }

  public function postTypeSupports(string $type, string $feature): bool {
    return post_type_supports($type, $feature);
  }

  /**
   * @param 'and'|'or' $operator
   * @return string[]|\WP_Taxonomy[]
   */
  public function getTaxonomies(array $args = [], string $output = 'names', string $operator = 'and'): array {
    return get_taxonomies($args, $output, $operator);
  }

  /**
   * @return mixed
   */
  public function getCommentMeta(int $commentId, string $key = '', bool $isSingle = false) {
    return get_comment_meta($commentId, $key, $isSingle);
  }

  /**
   * @param int|WP_Term|object $term
   * @param string $taxonomy
   * @param 'ARRAY_A'|'ARRAY_N'|'OBJECT' $output
   * @param string $filter
   * @return WP_Term|array|WP_Error|null
   */
  public function getTerm($term, string $taxonomy = '', string $output = OBJECT, string $filter = 'raw') {
    return get_term($term, $taxonomy, $output, $filter);
  }

  /** @return \WP_Taxonomy|false */
  public function getTaxonomy(string $name) {
    return get_taxonomy($name);
  }

  /** @return int|string */
  public function currentTime(string $type, bool $gmt = false) {
    return current_time($type, $gmt);
  }

  /**
   * @param string $field
   * @param string|int $value
   * @return false|WP_User
   */
  public function getUserBy(string $field, $value) {
    return get_user_by($field, $value);
  }
}
