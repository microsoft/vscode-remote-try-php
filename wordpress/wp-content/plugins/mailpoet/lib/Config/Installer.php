<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Services\Bridge;
use MailPoet\Services\Release\API;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\License\License;
use MailPoet\WP\Functions as WPFunctions;

class Installer {
  const PREMIUM_PLUGIN_SLUG = 'mailpoet-premium';
  const PREMIUM_PLUGIN_PATH = 'mailpoet-premium/mailpoet-premium.php';

  private $slug;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    $slug
  ) {
    $this->slug = $slug;
    $this->settings = SettingsController::getInstance();
  }

  public function init() {
    WPFunctions::get()->addFilter('plugins_api', [$this, 'getPluginInformation'], 10, 3);
  }

  public function generatePluginDownloadUrl(): string {
    $premiumKey = $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    return "https://release.mailpoet.com/downloads/mailpoet-premium/$premiumKey/latest/mailpoet-premium.zip";
  }

  public function generatePluginActivationUrl(string $plugin): string {
    return WPFunctions::get()->adminUrl('plugins.php?' . implode('&', [
      'action=activate',
      'plugin=' . urlencode($plugin),
      '_wpnonce=' . WPFunctions::get()->wpCreateNonce('activate-plugin_' . $plugin),
    ]));
  }

  public function getPluginInformation($data, $action = '', $args = null) {
    if (
      $action === 'plugin_information'
      && isset($args->slug)
      && $args->slug === $this->slug
    ) {
      $data = $this->retrievePluginInformation();
    }

    return $data;
  }

  public static function getPremiumStatus() {
    $slug = self::PREMIUM_PLUGIN_SLUG;

    $premiumPluginActive = License::getLicense();
    $premiumPluginInstalled = $premiumPluginActive || self::isPluginInstalled($slug);
    $premiumPluginInitialized = defined('MAILPOET_PREMIUM_INITIALIZED') && MAILPOET_PREMIUM_INITIALIZED;
    $installer = new Installer(self::PREMIUM_PLUGIN_SLUG);
    $pluginInformation = $installer->retrievePluginInformation();

    return [
      'premium_plugin_active' => $premiumPluginActive,
      'premium_plugin_installed' => $premiumPluginInstalled,
      'premium_plugin_initialized' => $premiumPluginInitialized,
      'premium_plugin_info' => $pluginInformation,
    ];
  }

  public static function isPluginInstalled($slug) {
    $installedPlugin = self::getInstalledPlugin($slug);
    return !empty($installedPlugin);
  }

  private static function getInstalledPlugin($slug) {
    $installedPlugin = [];
    if (is_dir(WP_PLUGIN_DIR . '/' . $slug)) {
      $installedPlugin = WPFunctions::get()->getPlugins('/' . $slug);
    }
    return $installedPlugin;
  }

  public static function getPluginFile($slug) {
    $pluginFile = false;
    $installedPlugin = self::getInstalledPlugin($slug);
    if (!empty($installedPlugin)) {
      $pluginFile = $slug . '/' . key($installedPlugin);
    }
    return $pluginFile;
  }

  public function retrievePluginInformation() {
    $key = $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    $api = new API($key);
    return $api->getPluginInformation($this->slug);
  }
}
