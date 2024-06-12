<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Services\Bridge;
use MailPoet\Services\Release\API;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class Updater {
  private $plugin;
  private $slug;
  private $version;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    $pluginName,
    $slug,
    $version
  ) {
    $this->plugin = WPFunctions::get()->pluginBasename($pluginName);
    $this->slug = $slug;
    $this->version = $version;
    $this->settings = SettingsController::getInstance();
  }

  public function init() {
    WPFunctions::get()->addFilter('pre_set_site_transient_update_plugins', [$this, 'checkForUpdate']);
  }

  public function checkForUpdate($updateTransient) {
    if (!$updateTransient instanceof \stdClass) {
      $updateTransient = new \stdClass;
    }

    $latestVersion = $this->getLatestVersion();

    if (isset($latestVersion->new_version)) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      if (version_compare((string)$this->version, $latestVersion->new_version, '<')) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $updateTransient->response[$this->plugin] = $latestVersion;
      } else {
        $updateTransient->no_update[$this->plugin] = $latestVersion; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      }
      $updateTransient->last_checked = time(); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $updateTransient->checked[$this->plugin] = $this->version;
    }

    return $updateTransient;
  }

  public function getLatestVersion() {
    $key = $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    $api = new API($key);
    $data = $api->getPluginInformation($this->slug . '/latest');
    return $data;
  }
}
