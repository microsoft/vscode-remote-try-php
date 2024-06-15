<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

if (!defined('ABSPATH')) exit;


/*
 * Plugin Name: MailPoet
 * Version: 4.51.2
 * Plugin URI: https://www.mailpoet.com
 * Description: Create and send newsletters, post notifications and welcome emails from your WordPress.
 * Author: MailPoet
 * Author URI: https://www.mailpoet.com
 * Requires at least: 6.4
 * Text Domain: mailpoet
 * Domain Path: /lang
 *
 * WC requires at least: 8.8.3
 * WC tested up to: 8.9.1
 *
 * @package WordPress
 * @author MailPoet
 * @since 3.0.0-beta.1
 */

$mailpoetPlugin = [
  'version' => '4.51.2',
  'filename' => __FILE__,
  'path' => dirname(__FILE__),
  'autoloader' => dirname(__FILE__) . '/vendor/autoload.php',
  'initializer' => dirname(__FILE__) . '/mailpoet_initializer.php',
];

const MAILPOET_MINIMUM_REQUIRED_WP_VERSION = '6.4';
const MAILPOET_MINIMUM_REQUIRED_WOOCOMMERCE_VERSION = '7.9';// Older versions lead to fatal errors

function mailpoet_deactivate_plugin() {
  deactivate_plugins(plugin_basename(__FILE__));
  if (!empty($_GET['activate'])) {
    unset($_GET['activate']);
  }
}

// Check for minimum supported WP version
if (version_compare(get_bloginfo('version'), MAILPOET_MINIMUM_REQUIRED_WP_VERSION, '<')) {
  add_action('admin_notices', 'mailpoet_wp_version_notice');
  // deactivate the plugin
  add_action('admin_init', 'mailpoet_deactivate_plugin');
  return;
}

// Check for minimum supported PHP version
if (version_compare(phpversion(), '7.4.0', '<')) {
  add_action('admin_notices', 'mailpoet_php_version_notice');
  // deactivate the plugin
  add_action('admin_init', 'mailpoet_deactivate_plugin');
  return;
}

// Check for minimum supported WooCommerce version
if (!function_exists('is_plugin_active')) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (is_plugin_active('woocommerce/woocommerce.php')) {
  $woocommerceVersion = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'];
  if (version_compare($woocommerceVersion, MAILPOET_MINIMUM_REQUIRED_WOOCOMMERCE_VERSION, '<')) {
    add_action('admin_notices', 'mailpoet_woocommerce_version_notice');
    // deactivate the plugin
    add_action('admin_init', 'mailpoet_deactivate_plugin');
    return;
  }
}

// Display WP version error notice
function mailpoet_wp_version_notice() {
  $notice = str_replace(
    '[link]',
    '<a href="https://kb.mailpoet.com/article/152-minimum-requirements-for-mailpoet-3#wp_version" target="_blank">',
    sprintf(
      // translators: %s is the number of minimum WordPress version that MailPoet requires
      __('MailPoet plugin requires WordPress version %s or newer. Please read our [link]instructions[/link] on how to resolve this issue.', 'mailpoet'),
      MAILPOET_MINIMUM_REQUIRED_WP_VERSION
    )
  );
  $notice = str_replace('[/link]', '</a>', $notice);
  printf(
    '<div class="error"><p>%1$s</p></div>',
    wp_kses(
      $notice,
      [
        'a' => [
          'href' => true,
          'target' => true,
        ],
      ]
    )
  );
}

// Display WooCommerce version error notice
function mailpoet_woocommerce_version_notice() {
  $notice = str_replace(
    '[link]',
    '<a href="https://kb.mailpoet.com/article/152-minimum-requirements-for-mailpoet-3#woocommerce-version" target="_blank">',
    sprintf(
      // translators: %s is the number of minimum WooCommerce version that MailPoet requires
      __('MailPoet plugin requires WooCommerce version %s or newer. Please update your WooCommerce plugin version, or read our [link]instructions[/link] for additional options on how to resolve this issue.', 'mailpoet'),
      MAILPOET_MINIMUM_REQUIRED_WOOCOMMERCE_VERSION
    )
  );
  $notice = str_replace('[/link]', '</a>', $notice);
  printf(
    '<div class="error"><p>%1$s</p></div>',
    wp_kses(
      $notice,
      [
        'a' => [
          'href' => true,
          'target' => true,
        ],
      ]
    )
  );
}

// Display PHP version error notice
function mailpoet_php_version_notice() {
  $noticeP1 = __('MailPoet requires PHP version 7.4 or newer (8.1 recommended). You are running version [version].', 'mailpoet');
  $noticeP1 = str_replace('[version]', phpversion(), $noticeP1);

  $noticeP2 = __('Please read our [link]instructions[/link] on how to upgrade your site.', 'mailpoet');
  $noticeP2 = str_replace(
    '[link]',
    '<a href="https://kb.mailpoet.com/article/251-upgrading-the-websites-php-version" target="_blank">',
    $noticeP2
  );
  $noticeP2 = str_replace('[/link]', '</a>', $noticeP2);

  $noticeP3 = __('If you can’t upgrade the PHP version, [link]install this version[/link] of MailPoet. Remember to not update MailPoet ever again!', 'mailpoet');
  $noticeP3 = str_replace(
    '[link]',
    '<a href="https://downloads.wordpress.org/plugin/mailpoet.4.38.0.zip" target="_blank">',
    $noticeP3
  );
  $noticeP3 = str_replace('[/link]', '</a>', $noticeP3);

  $allowedTags = [
    'a' => [
      'href' => true,
      'target' => true,
    ],
  ];
  printf(
    '<div class="error"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>',
    esc_html($noticeP1),
    wp_kses(
      $noticeP2,
      $allowedTags
    ),
    wp_kses(
      $noticeP3,
      $allowedTags
    )
  );
}

if (isset($_SERVER['SERVER_SOFTWARE']) && strpos(strtolower(sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE']))), 'microsoft-iis') !== false) {
  add_action('admin_notices', 'mailpoet_microsoft_iis_notice');
  // deactivate the plugin
  add_action('admin_init', 'mailpoet_deactivate_plugin');
  return;
}

// Display IIS server error notice
function mailpoet_microsoft_iis_notice() {
  $notice = __("MailPoet plugin cannot run under Microsoft's Internet Information Services (IIS) web server. We recommend that you use a web server powered by Apache or NGINX.", 'mailpoet');
  printf('<div class="error"><p>%1$s</p></div>', esc_html($notice));
}

// Check for presence of core dependencies
if (!file_exists($mailpoetPlugin['autoloader']) || !file_exists($mailpoetPlugin['initializer'])) {
  add_action('admin_notices', 'mailpoet_core_dependency_notice');
  // deactivate the plugin
  add_action('admin_init', 'mailpoet_deactivate_plugin');
  return;
}

// Display missing core dependencies error notice
function mailpoet_core_dependency_notice() {
  $notice = __('MailPoet cannot start because it is missing core files. Please reinstall the plugin.', 'mailpoet');
  printf('<div class="error"><p>%1$s</p></div>', esc_html($notice));
}

// Initialize plugin
require_once($mailpoetPlugin['initializer']);
