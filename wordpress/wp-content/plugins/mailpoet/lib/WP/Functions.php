<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WP;

if (!defined('ABSPATH')) exit;


use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;
use WP_Error;

class Functions {

  private static $instance;

  /**
   * @return Functions
   */
  public static function get() {
    if (self::$instance === null) {
      self::$instance = new Functions;
    }
    return self::$instance;
  }

  public static function set(Functions $instance) {
    self::$instance = $instance;
  }

  /**
   * @param string $tag
   * @param mixed ...$args
   * @return mixed
   */
  public function doAction($tag, ...$args) {
    return call_user_func_array('do_action', func_get_args());
  }

  /**
   * @param string $hookName
   * @return int
   */
  public function didAction($hookName) {
    return did_action($hookName);
  }

  /**
   * @param string $hookName
   * @return int
   */
  public function didFilter($hookName) {
    return did_filter($hookName);
  }

  public function trailingslashit(string $url) {
    return trailingslashit($url);
  }

  /**
   * @param string $tag
   * @param mixed ...$args
   * @return mixed
   */
  public function applyFilters($tag, ...$args) {
    return call_user_func_array('apply_filters', func_get_args());
  }

  /**
   * @param string $tag
   * @param callable $functionToAdd
   * @param int $priority
   * @param int $acceptedArgs
   * @return bool
   */
  public function addAction($tag, $functionToAdd, $priority = 10, $acceptedArgs = 1) {
    return call_user_func_array('add_action', func_get_args());
  }

  public function addCommentMeta($commentId, $metaKey, $metaValue, $unique = false) {
    return add_comment_meta($commentId, $metaKey, $metaValue, $unique);
  }

  public function addFilter($tag, callable $functionToAdd, $priority = 10, $acceptedArgs = 1) {
    return add_filter($tag, $functionToAdd, $priority, $acceptedArgs);
  }

  /**
   * @param bool|array{string, string} $crop
   */
  public function addImageSize($name, $width = 0, $height = 0, $crop = false) {
    return add_image_size($name, $width, $height, $crop);
  }

  /**
   * @param string $pageTitle
   * @param string $menuTitle
   * @param string $capability
   * @param string $menuSlug
   * @param callable|'' $callback
   * @param string $iconUrl
   * @param int $position
   * @return string
   */
  public function addMenuPage($pageTitle, $menuTitle, $capability, $menuSlug, $callback = '', $iconUrl = '', $position = null) {
    return add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position);
  }

  public function addQueryArg($key, $value = false, $url = false) {
    return add_query_arg($key, $value, $url); // nosemgrep: tools.wpscan-semgrep-rules.audit.php.wp.security.xss.query-arg
  }

  public function addScreenOption($option, $args = []) {
    add_screen_option($option, $args);
  }

  public function addShortcode($tag, callable $callback) {
    add_shortcode($tag, $callback);
  }

  /**
   * @param string $parentSlug
   * @param string $pageTitle
   * @param string $menuTitle
   * @param string $capability
   * @param string $menuSlug
   * @param callable|'' $callback
   * @param int $position
   * @return string|false
   */
  public function addSubmenuPage($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $callback = '', $position = null) {
    return add_submenu_page($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $callback, $position);
  }

  public function adminUrl($path = '', $scheme = 'admin') {
    return admin_url($path, $scheme);
  }

  public function currentFilter() {
    return current_filter();
  }

  public function currentTime($type, $gmt = false) {
    return current_time($type, $gmt);
  }

  public function currentUserCan($capability) {
    return current_user_can($capability);
  }

  public function dateI18n($dateformatstring, $timestampWithOffset = false, $gmt = false) {
    return date_i18n($dateformatstring, $timestampWithOffset, $gmt);
  }

  public function deleteCommentMeta($commentId, $metaKey, $metaValue = '') {
    return delete_comment_meta($commentId, $metaKey, $metaValue);
  }

  public function addOption($option, $value) {
    return add_option($option, $value);
  }

  public function deleteOption($option) {
    return delete_option($option);
  }

  public function doShortcode($content, $ignoreHtml = false) {
    return do_shortcode($content, $ignoreHtml);
  }

  public function escAttr($text) {
    return esc_attr($text);
  }

  public function escHtml($text) {
    return esc_html($text);
  }

  public function escSql($sql) {
    return esc_sql($sql);
  }

  public function escUrl($url): string {
    return esc_url($url);
  }

  public function getBloginfo($show = '', $filter = 'raw') {
    return get_bloginfo($show, $filter);
  }

  public function getCategories($args = '') {
    return get_categories($args);
  }

  public function getTags($args = '') {
    return get_tags($args);
  }

  public function getComment($comment = null, $output = OBJECT) {
    return get_comment($comment, $output);
  }

  public function getCommentMeta($commentId, $key = '', $single = false) {
    return get_comment_meta($commentId, $key, $single);
  }

  public function getCurrentScreen() {
    return get_current_screen();
  }

  public function getCurrentUserId() {
    return get_current_user_id();
  }

  public function getDateFromGmt($string, $format = 'Y-m-d H:i:s') {
    return get_date_from_gmt($string, $format);
  }

  public function getGmtFromDate($string, $format = 'Y-m-d H:i:s') {
    return get_gmt_from_date($string, $format);
  }

  public function getEditProfileUrl($userId = 0, $scheme = 'admin') {
    return get_edit_profile_url($userId, $scheme);
  }

  public function getEditableRoles() {
    return get_editable_roles();
  }

  public function getLocale() {
    return get_locale();
  }

  public function getObjectTaxonomies($object, $output = 'names') {
    return get_object_taxonomies($object, $output);
  }

  public function getOption($option, $default = false) {
    return get_option($option, $default);
  }

  public function getPages($args = []) {
    return get_pages($args);
  }

  public function getPermalink($post, $leavename = false) {
    return get_permalink($post, $leavename);
  }

  public function getPluginPageHook($pluginPage, $parentPage) {
    return get_plugin_page_hook($pluginPage, $parentPage);
  }

  public function getPluginUpdates() {
    return get_plugin_updates();
  }

  public function getPlugins($pluginFolder = '') {
    return get_plugins($pluginFolder);
  }

  public function getPost($post = null, $output = OBJECT, $filter = 'raw') {
    return get_post($post, $output, $filter);
  }

  public function wpUpdatePost($postarr = [], bool $wp_error = false, bool $fire_after_hooks = true) {
    return wp_update_post($postarr, $wp_error, $fire_after_hooks);
  }

  public function hasCategory($category = '', $post = null): bool {
    return has_category($category, $post);
  }

  public function hasTag($tag = '', $post = null): bool {
    return has_tag($tag, $post);
  }

  public function hasTerm($term = '', $taxonomy = '', $post = null): bool {
    return has_term($term, $taxonomy, $post);
  }

  public function getPostThumbnailId($post = null) {
    return get_post_thumbnail_id($post);
  }

  public function getPostTypes($args = [], $output = 'names', $operator = 'and') {
    return get_post_types($args, $output, $operator);
  }

  public function getPostType($post = null) {
    return get_post_type($post);
  }

  public function getPosts(array $args = null) {
    return get_posts($args);
  }

  public function getRole($role) {
    return get_role($role);
  }

  public function getSiteOption($option, $default = false, $deprecated = true) {
    return get_site_option($option, $default, $deprecated);
  }

  public function getSiteUrl($blogId = null, $path = '', $scheme = null) {
    return get_site_url($blogId, $path, $scheme);
  }

  public function getTemplatePart($slug, $name = null) {
    return get_template_part($slug, $name);
  }

  /**
   * @param string|array $args
   * @param string|array $deprecated
   */
  public function getTerms($args = [], $deprecated = '') {
    return get_terms($args, $deprecated);
  }

  /**
   * @param int|false $userId
   */
  public function getTheAuthorMeta($field = '', $userId = false) {
    return get_the_author_meta($field, $userId);
  }

  /**
   * @return false|int
   */
  public function getTheId() {
    return get_the_ID();
  }

  /**
   * @param  int|\WP_User $userId
   */
  public function getUserLocale($userId = 0) {
    return get_user_locale($userId);
  }

  public function getUserMeta($userId, $key = '', $single = false) {
    return get_user_meta($userId, $key, $single);
  }

  public function getUserdata($userId) {
    return get_userdata($userId);
  }

  public function getUserBy($field, $value) {
    return get_user_by($field, $value);
  }

  public function hasAction($tag, $functionToCheck = false) {
    return has_action($tag, $functionToCheck);
  }

  public function hasFilter($tag, $functionToCheck = false) {
    return has_filter($tag, $functionToCheck);
  }

  public function homeUrl($path = '', $scheme = null) {
    return home_url($path, $scheme);
  }

  public function includesUrl($path = '', $scheme = null) {
    return includes_url($path, $scheme);
  }

  public function isAdmin() {
    return is_admin();
  }

  public function isEmail($email) {
    return is_email($email);
  }

  public function isMultisite() {
    return is_multisite();
  }

  public function isPluginActive($plugin) {
    return is_plugin_active($plugin);
  }

  public function isRtl() {
    return is_rtl();
  }

  public function isSerialized($data, $strict = true) {
    return is_serialized($data, $strict);
  }

  public function isUserLoggedIn() {
    return is_user_logged_in();
  }

  /**
   * @param  string $domain
   * @param  string|false $pluginRelPath
   */
  public function loadPluginTextdomain($domain, $pluginRelPath = false) {
    return load_plugin_textdomain($domain, false, $pluginRelPath);
  }

  public function loadTextdomain($domain, $mofile) {
    return load_textdomain($domain, $mofile);
  }

  public function numberFormatI18n($number, $decimals = 0) {
    return number_format_i18n($number, $decimals);
  }

  public function pluginBasename($file) {
    return plugin_basename($file);
  }

  public function pluginsUrl($path = '', $plugin = '') {
    return plugins_url($path, $plugin);
  }

  public function registerActivationHook($file, $function) {
    return register_activation_hook($file, $function);
  }

  public function registerDeactivationHook($file, $function) {
    return register_deactivation_hook($file, $function);
  }

  public function registerPostType($postType, $args = []) {
    return register_post_type($postType, $args);
  }

  public function registerWidget($widget) {
    return register_widget($widget);
  }

    /**
   * @param string $tag
   * @param callable|string|array $functionToRemove
   * @param int $priority
   */
  public function removeAction($tag, $functionToRemove, $priority = 10) {
    return remove_action($tag, $functionToRemove, $priority);
  }

  public function removeAllActions($tag, $priority = false) {
    return remove_all_actions($tag, $priority);
  }

  public function removeAllFilters($tag, $priority = false) {
    return remove_all_filters($tag, $priority);
  }

  public function removeFilter($tag, callable $functionToRemove, $priority = 10) {
    return remove_filter($tag, $functionToRemove, $priority);
  }

  public function removeShortcode($tag) {
    return remove_shortcode($tag);
  }

  public function selfAdminUrl($path = '', $scheme = 'admin') {
    return self_admin_url($path, $scheme);
  }

  public function setTransient($transient, $value, $expiration = 0) {
    return set_transient($transient, $value, $expiration);
  }

  public function getTransient($transient) {
    return get_transient($transient);
  }

  public function setSiteTransient($transient, $value, $expiration = 0) {
    return set_site_transient($transient, $value, $expiration);
  }

  public function getSiteTransient($transient) {
    return get_site_transient($transient);
  }

  public function deleteTransient($transient) {
    return delete_transient($transient);
  }

  public function shortcodeParseAtts($text) {
    return shortcode_parse_atts($text);
  }

  public function singlePostTitle($prefix = '', $display = true) {
    return single_post_title($prefix, $display);
  }

  public function siteUrl($path = '', $scheme = null) {
    return site_url($path, $scheme);
  }

  public function statusHeader($code, $description = '') {
    status_header($code, $description);
  }

  public function stripslashesDeep($value) {
    return stripslashes_deep($value);
  }

  public function unloadTextdomain($domain) {
    return unload_textdomain($domain);
  }

  public function updateOption($option, $value, $autoload = null) {
    return update_option($option, $value, $autoload);
  }

  public function wpAddInlineScript($handle, $data, $position = 'after') {
    return wp_add_inline_script($handle, $data, $position);
  }

  public function wpCreateNonce($action = -1) {
    return wp_create_nonce($action);
  }

  public function wpDequeueScript($handle) {
    return wp_dequeue_script($handle);
  }

  public function wpDequeueStyle($handle) {
    return wp_dequeue_style($handle);
  }

  public function wpEncodeEmoji($content) {
    return wp_encode_emoji($content);
  }

  public function wpEnqueueMedia(array $args = []) {
    wp_enqueue_media($args);
  }

  public function wpEnqueueScript($handle, $src = '', array $deps = [], $ver = false, $inFooter = false) {
    return wp_enqueue_script($handle, $src, $deps, $ver, $inFooter);
  }

  public function wpEnqueueStyle($handle, $src = '', array $deps = [], $ver = false, $media = 'all') {
    return wp_enqueue_style($handle, $src, $deps, $ver, $media);
  }

  /**
   * @param string|\WP_Block_Type $name
   * @param array $args {
   * @return \WP_Block_Type|false
   */
  public function registerBlockType($name, $args = []) {
    return register_block_type($name, $args);
  }

  public function registerBlockTypeFromMetadata($name, $args = []) {
    return register_block_type_from_metadata($name, $args);
  }

  public function wpGetAttachmentImageSrc($attachmentId, $size = 'thumbnail', $icon = false) {
    return wp_get_attachment_image_src($attachmentId, $size, $icon);
  }

  public function wpGetCurrentUser() {
    return wp_get_current_user();
  }

  public function wpGetPostTerms($postId, $taxonomy = 'post_tag', array $args = []) {
    return wp_get_post_terms($postId, $taxonomy, $args);
  }

  public function wpGetReferer() {
    return wp_get_referer();
  }

  public function wpGetTheme($stylesheet = null, $themeRoot = null) {
    return wp_get_theme($stylesheet, $themeRoot);
  }

  public function wpInsertPost(array $postarr, $wpError = false) {
    return wp_insert_post($postarr, $wpError);
  }

  public function wpDeletePost(int $id, $force = false) {
    return wp_delete_post($id, $force);
  }

  public function wpJsonEncode($data, $options = 0, $depth = 512) {
    return wp_json_encode($data, $options, $depth);
  }

  public function wpLocalizeScript($handle, $objectName, array $l10n) {
    return wp_localize_script($handle, $objectName, $l10n);
  }

  public function wpLoginUrl($redirect = '', $forceReauth = false) {
    return wp_login_url($redirect, $forceReauth);
  }

  public function wpParseArgs($args, $defaults = '') {
    return wp_parse_args($args, $defaults);
  }

  public function wpParseUrl($url, $component = -1) {
    return wp_parse_url($url, $component);
  }

  public function wpSpecialcharsDecode($string, $quoteStyle = ENT_NOQUOTES) {
    return wp_specialchars_decode($string, $quoteStyle);
  }

  public function wpPrintScripts($handles = false) {
    return wp_print_scripts($handles);
  }

  public function wpRemoteGet($url, array $args = []) {
    return wp_remote_get($url, $args);
  }

  public function wpRemotePost($url, array $args = []) {
    return wp_remote_post($url, $args);
  }

  public function wpRemoteRetrieveBody($response) {
    return wp_remote_retrieve_body($response);
  }

  public function wpRemoteRetrieveResponseCode($response) {
    return wp_remote_retrieve_response_code($response);
  }

  public function wpRemoteRetrieveResponseMessage($response) {
    return wp_remote_retrieve_response_message($response);
  }

  public function wpSafeRedirect($location, $status = 302) {
    return wp_safe_redirect($location, $status);
  }

  public function wpStaticizeEmoji($text) {
    return wp_staticize_emoji($text);
  }

  public function wpTrimWords($text, $numWords = 55, $more = null) {
    return wp_trim_words($text, $numWords, $more);
  }

  public function wpUploadDir($time = null, $createDir = true, $refreshCache = false) {
    return wp_upload_dir($time, $createDir, $refreshCache);
  }

  public function wpVerifyNonce($nonce, $action = -1) {
    return wp_verify_nonce($nonce, $action);
  }

  public function wpautop($pee, $br = true) {
    return wpautop($pee, $br);
  }

  public function inTheLoop(): bool {
    return in_the_loop();
  }

  public function isMainQuery(): bool {
    return is_main_query();
  }

  public function isFrontPage(): bool {
    return is_front_page();
  }

  public function getPrivacyPolicyUrl(): string {
    return get_privacy_policy_url();
  }

  /**
   * @param 'hot_categories'|'hot_tags'|'plugin_information'|'query_plugins' $action
   * @param array|object $args
   * @return object|array|WP_Error
   */
  public function pluginsApi($action, $args = []) {
    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    return plugins_api($action, $args);
  }

  /**
   * @param int|string|array $post Optional. Post ID, title, slug, or array of such. Default empty.
   * @return bool Whether the query is for an existing single post.
   */
  public function isSingle($post = '') {
    return is_single($post);
  }

  /**
   * @param int|string|array $page Optional. Page ID, title, slug, or array of such. Default empty.
   * @return bool Whether the query is for an existing single page.
   */
  public function isPage($page = '') {
    return is_page($page);
  }

  /**
   * @param string|array $postTypes Optional. Post type or array of post types. Default empty.
   * @return bool Whether the query is for an existing single post of any of the given post types.
   */
  public function isSingular($postTypes = ''): bool {
    return is_singular($postTypes);
  }

  /**
   * Determines whether the query is for an existing archive page.
   *
   * Archive pages include category, tag, author, date, custom post type,
   * and custom taxonomy based archives.
   *
   * @return bool Whether the query is for an existing archive page.
   */
  public function isArchive(): bool {
    return is_archive();
  }

  public function isTag($tag = '') {
    return is_tag($tag);
  }

  public function isCategory($category = '') {
    return is_category($category);
  }

  public function isTax($taxonomy = '', $term = '') {
    return is_tax($taxonomy, $term);
  }

  /**
   * Determines whether the query is for an existing post type archive page.
   *
   * @param string|string[] $postTypes Optional. Post type or array of posts types
   *                                    to check against. Default empty.
   * @return bool Whether the query is for an existing post type archive page.
   */
  public function isPostTypeArchive($postTypes = ''): bool {
    return is_post_type_archive($postTypes);
  }

  /**
   * @param string $package
   * @param array $args
   * @return bool|WP_Error
   */
  public function installPlugin($package, $args = []) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
    // nosemgrep: tools.wpscan-semgrep-rules.audit.php.wp.security.arbitrary-plugin-install
    $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
    return $upgrader->install($package, $args);
  }

  /**
   * @param string $plugin
   * @param string $redirect
   * @param bool $networkWide
   * @param bool $silent
   * @return WP_Error|null
   */
  public function activatePlugin($plugin, $redirect = '', $networkWide = false, $silent = false) {
    return activate_plugin($plugin, $redirect, $networkWide, $silent);
  }

  public function wpGetAttachmentImageSrcset(int $attachmentId, $size = 'medium', $imageMeta = null) {
    return wp_get_attachment_image_srcset($attachmentId, $size, $imageMeta);
  }

  /**
   * @param string $host
   * @return array|bool
   */
  public function parseDbHost($host) {
    global $wpdb;
    return $wpdb->parse_db_host($host);
  }

  /**
   * @param int|\WP_Post $post
   * @param string $context
   * @return string|null
   */
  public function getEditPostLink($post, string $context = 'display') {
    return get_edit_post_link($post, $context);
  }

  /**
   * @param string $string
   * @param array[]|string $allowedHtml
   * @param array $allowedProtocols
   * @return string
   */
  public function wpKses(string $string, $allowedHtml, $allowedProtocols = []) {
    return wp_kses($string, $allowedHtml, $allowedProtocols);
  }

  public function deprecatedHook(string $hook_name, string $version, string $replacement, string $message) {
    _deprecated_hook(
      esc_html($hook_name),
      esc_html($version),
      esc_html($replacement),
      wp_kses_post($message)
    );
  }

  public function getTheExcerpt($post = null) {
    return get_the_excerpt($post);
  }

  public function hasExcerpt($post = null) {
    return has_excerpt($post);
  }

  public function wpMkdirP(string $dir) {
    return wp_mkdir_p($dir);
  }

  public function wpGetImageEditor(string $path, $args = []) {
    return wp_get_image_editor($path, $args);
  }

  public function wpRegisterScript(string $handle, $src, $deps = [], $ver = false, $in_footer = false): bool {
    return wp_register_script($handle, $src, $deps, $ver, $in_footer);
  }

  public function wpSetScriptTranslations(string $handle, string $domain = 'default', string $path = ''): bool {
    return wp_set_script_translations($handle, $domain, $path);
  }

  /**
   * @return \WP_Scripts
   */
  public function getWpScripts() {
    return wp_scripts();
  }

  /** @param string[]|null $protocols */
  public function escUrlRaw(string $url, array $protocols = null): string {
    return esc_url_raw($url, $protocols);
  }

  public function restUrl(string $path = '', string $scheme = 'rest'): string {
    return rest_url($path, $scheme);
  }

  public function registerRestRoute(string $namespace, string $route, array $args = [], bool $override = false): bool {
    return register_rest_route($namespace, $route, $args, $override);
  }

  public function registerRestField($object_type, string $attribute, array $args = []) {
    return register_rest_field($object_type, $attribute, $args);
  }

  /**
   * @param mixed $value
   * @return true|WP_Error
   */
  public function restValidateValueFromSchema($value, array $args, string $param = '') {
    return rest_validate_value_from_schema($value, $args, $param);
  }

  /**
   * @param mixed $value
   * @return mixed|WP_Error
   */
  public function restSanitizeValueFromSchema($value, array $args, string $param = '') {
    return rest_sanitize_value_from_schema($value, $args, $param);
  }

  /**
   * @param mixed $value
   * @param string $param
   * @param array $errors
   * @return WP_Error
   */
  public function restGetCombiningOperationError($value, string $param, array $errors): WP_Error {
    /* @phpstan-ignore-next-line Wrong annotation for first parameter in WP. */
    return rest_get_combining_operation_error($value, $param, $errors);
  }

  /**
   * @param mixed $value
   * @param array $args
   * @param string $param
   * @param bool $stopAfterFirstMatch
   * @return array|WP_Error
   */
  public function restFindOneMatchingSchema($value, array $args, string $param, bool $stopAfterFirstMatch = false) {
    return rest_find_one_matching_schema($value, $args, $param, $stopAfterFirstMatch);
  }

  /**
   * @param string $property
   * @param array $args
   * @return array|null
   */
  public function restFindMatchingPatternPropertySchema(string $property, array $args): ?array {
    return rest_find_matching_pattern_property_schema($property, $args);
  }

  public function wpGetInstalledTranslations(string $type): array {
    return wp_get_installed_translations($type);
  }

  public function getAvailableLanguages(?string $dir = null): array {
    return get_available_languages($dir);
  }

  public function isWpError($value): bool {
    return is_wp_error($value);
  }

  public function wpIsSiteUrlUsingHttps(): bool {
    return wp_is_site_url_using_https();
  }

  public function getPostMeta(int $postId, string $key = '', bool $single = false) {
    return get_post_meta($postId, $key, $single);
  }

  /**
   * @return bool|int
   */
  public function updatePostMeta(int $postId, string $metaKey, $metaValue, $prevValue = '') {
    return update_post_meta($postId, $metaKey, $metaValue, $prevValue);
  }

  public function getFileData(string $file, array $default_headers, string $context = 'plugin'): array {
    return get_file_data($file, $default_headers, $context);
  }

  public function getPluginData(string $plugin_file, bool $markup = true, bool $translate = true): array {
    return get_plugin_data($plugin_file, $markup, $translate);
  }

  public function wpIsBlockTheme(): bool {
    // wp_is_block_theme exists only in WP 5.9+
    if (function_exists('wp_is_block_theme')) {
      return wp_is_block_theme();
    }

    return false;
  }

  public function getShortcodeRegex($tagnames = null): string {
    return get_shortcode_regex($tagnames);
  }

  public function isHome() {
    return is_home();
  }

  public function getQueriedObjectId() {
    return get_queried_object_id();
  }

  /**
   * @param string $string
   * @param bool $removeBreaks
   */
  public function wpStripAllTags($string, $removeBreaks = false): string {
    return wp_strip_all_tags($string, $removeBreaks);
  }

  public function getTheContent($more_link_text = null, $strip_teaser = false, $post = null) {
    return get_the_content($more_link_text, $strip_teaser, $post);
  }

  public function getTaxonomy($taxonomy) {
    return get_taxonomy($taxonomy);
  }

  public function wpIsMaintenanceMode(): bool {
    return wp_is_maintenance_mode();
  }
}
