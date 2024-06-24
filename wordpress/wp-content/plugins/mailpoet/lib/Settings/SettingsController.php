<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Settings;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronTrigger;
use MailPoet\DI\ContainerWrapper;

class SettingsController {

  const DEFAULT_SENDING_METHOD_GROUP = 'website';
  const DEFAULT_SENDING_METHOD = 'PHPMail';
  const DEFAULT_SENDING_FREQUENCY_EMAILS = 25;
  const DEFAULT_SENDING_FREQUENCY_INTERVAL = 5; // in minutes
  const DEFAULT_DEACTIVATE_SUBSCRIBER_AFTER_INACTIVE_DAYS = 365;

  private $loaded = false;

  private $settings = [];

  private $defaults = null;

  /** @var SettingsRepository */
  private $settingsRepository;

  private static $instance;

  public function __construct(
    SettingsRepository $settingsRepository
  ) {
    $this->settingsRepository = $settingsRepository;
  }

  public function get($key, $default = null) {
    $this->ensureLoaded();
    $keyParts = explode('.', $key);
    $setting = $this->settings;
    if ($default === null) {
      $default = $this->getDefaultValue($keyParts);
    }
    foreach ($keyParts as $keyPart) {
      if (is_array($setting) && array_key_exists($keyPart, $setting)) {
        $setting = $setting[$keyPart];
      } else {
        return $default;
      }
    }
    if (is_array($setting) && is_array($default)) {
      return array_replace_recursive($default, $setting);
    }
    return $setting;
  }

  public function getAllDefaults() {
    if ($this->defaults === null) {
      $this->defaults = [
        'mta_group' => self::DEFAULT_SENDING_METHOD_GROUP,
        'mta' => [
          'method' => self::DEFAULT_SENDING_METHOD,
          'frequency' => [
            'emails' => self::DEFAULT_SENDING_FREQUENCY_EMAILS,
            'interval' => self::DEFAULT_SENDING_FREQUENCY_INTERVAL,
          ],
        ],
        CronTrigger::SETTING_NAME => [
          'method' => CronTrigger::DEFAULT_METHOD,
        ],
        'signup_confirmation' => [
          'enabled' => true,
          'use_mailpoet_editor' => true,
          'subject' => __('Confirm your subscription to [site:title]', 'mailpoet'),
          'body' => __("Hello [subscriber:firstname | default:there],\n\nYou've received this message because you subscribed to [site:title]. Please confirm your subscription to receive emails from us:\n\n[activation_link]Click here to confirm your subscription.[/activation_link] \n\nIf you received this email by mistake, simply delete it. You won't receive any more emails from us unless you confirm your subscription using the link above.\n\nThank you,\n\n<a target=\"_blank\" href=\"[site:homepage_url]\">[site:title]</a>", 'mailpoet'),
        ],
        'tracking' => [
          'level' => TrackingConfig::LEVEL_FULL,
        ],
        'analytics' => [
          'enabled' => false,
        ],
        'display_nps_poll' => true,
        'deactivate_subscriber_after_inactive_days' => self::DEFAULT_DEACTIVATE_SUBSCRIBER_AFTER_INACTIVE_DAYS,
      ];
    }
    return $this->defaults;
  }

  /**
   * Fetches the value from DB and update in cache
   * This is required for sync settings between parallel processes e.g. cron
   */
  public function fetch($key, $default = null) {
    $keys = explode('.', $key);
    $mainKey = $keys[0];
    $this->settings[$mainKey] = $this->fetchValue($mainKey);
    return $this->get($key, $default);
  }

  public function getAll() {
    $this->ensureLoaded();
    return array_replace_recursive($this->getAllDefaults(), $this->settings);
  }

  public function set($key, $value) {
    $this->ensureLoaded();
    $keyParts = explode('.', $key);
    $mainKey = $keyParts[0];
    $lastKey = array_pop($keyParts);
    $setting =& $this->settings;
    foreach ($keyParts as $keyPart) {
      $setting =& $setting[$keyPart];
      if (!is_array($setting)) {
        $setting = [];
      }
    }
    $setting[$lastKey] = $value;
    $this->settingsRepository->createOrUpdateByName($mainKey, $this->settings[$mainKey]);
  }

  public function delete($key) {
    $setting = $this->settingsRepository->findOneByName($key);
    if ($setting) {
      $this->settingsRepository->remove($setting);
      $this->settingsRepository->flush();
    }
    unset($this->settings[$key]);
  }

  /**
   * Returns true if a value is stored in the database for the given key
   *
   * @param string $key
   *
   * @return bool
   */
  public function hasSavedValue(string $key): bool {
    return $this->get($key, 'unset') !== 'unset';
  }

  private function ensureLoaded() {
    if ($this->loaded) {
      return;
    }

    $this->settings = [];
    foreach ($this->settingsRepository->findAll() as $setting) {
      $this->settings[$setting->getName()] = $setting->getValue();
    }
    $this->loaded = true;
  }

  private function getDefaultValue($keys) {
    $default = $this->getAllDefaults();
    foreach ($keys as $key) {
      if (array_key_exists($key, $default)) {
        $default = $default[$key];
      } else {
        return null;
      }
    }
    return $default;
  }

  private function fetchValue($key) {
    $setting = $this->settingsRepository->findOneByName($key);
    return $setting ? $setting->getValue() : null;
  }

  public function resetCache() {
    $this->settings = [];
    $this->loaded = false;
  }

  public static function setInstance($instance) {
    self::$instance = $instance;
  }

  /** @return SettingsController */
  public static function getInstance() {
    if (isset(self::$instance)) return self::$instance;
    return ContainerWrapper::getInstance()->get(SettingsController::class);
  }
}
