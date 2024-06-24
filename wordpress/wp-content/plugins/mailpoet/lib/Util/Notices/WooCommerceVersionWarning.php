<?php declare(strict_types = 1);

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class WooCommerceVersionWarning {
  const OPTION_NAME = 'mailpoet-dismissed-woo-version-outdated-notice';
  const DISMISS_NOTICE_TIMEOUT_SECONDS = 2592000; // 30 days

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
      return;
    }
    $woocommerceVersion = $this->wp->getPluginData(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'];
    $requiredWooCommerceVersion = $this->getRequiredWooCommerceVersion();
    if ($shouldDisplay && $this->isOutdatedWooCommerceVersion($woocommerceVersion, $requiredWooCommerceVersion)) {
      $this->display($requiredWooCommerceVersion);
    }
  }

  public function isOutdatedWooCommerceVersion($woocommerceVersion, $requiredWooCommerceVersion): bool {
    return version_compare($woocommerceVersion, $requiredWooCommerceVersion, '<') && !$this->wp->getTransient($this->getTransientKey());
  }

  private function display($requiredWooCommerceVersion) {
    // translators: %s is the PHP version
    $errorString = __('MailPoet plugin requires WooCommerce version %s or newer. Please update your WooCommerce plugin version, or read our [link]instructions[/link] for additional options on how to resolve this issue.', 'mailpoet');
    $errorString = sprintf($errorString, $requiredWooCommerceVersion);
    $error = Helpers::replaceLinkTags($errorString, 'https://kb.mailpoet.com/article/152-minimum-requirements-for-mailpoet-3#woocommerce-version', [
      'target' => '_blank',
    ]);

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    Notice::displayWarning($error, $extraClasses, self::OPTION_NAME);
  }

  public function disable() {
    $this->wp->setTransient($this->getTransientKey(), true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }

  private function getTransientKey() {
    $woocommerceVersion = $this->wp->getPluginData(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')['Version'];
    return self::OPTION_NAME . '_' . $this->getRequiredWooCommerceVersion() . '_' . $woocommerceVersion;
  }

  private function getRequiredWooCommerceVersion(): string {
    $pluginData = $this->wp->getFileData(
      Env::$file,
      [
        'RequiredWCVersion' => 'WC requires at least',
      ]
    );
    return $pluginData['RequiredWCVersion'] ?? '100.0.0';
  }
}
