<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\License\Features;

if (!defined('ABSPATH')) exit;


use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class Subscribers {
  const SUBSCRIBERS_OLD_LIMIT = 2000;
  const SUBSCRIBERS_NEW_LIMIT = 1000;
  const NEW_LIMIT_DATE = '2019-11-00';
  const MSS_KEY_STATE = 'mta.mailpoet_api_key_state.state';
  const MSS_SUBSCRIBERS_LIMIT_SETTING_KEY = 'mta.mailpoet_api_key_state.data.site_active_subscriber_limit';
  const MSS_SUPPORT_SETTING_KEY = 'mta.mailpoet_api_key_state.data.support_tier';
  const PREMIUM_KEY_STATE = 'premium.premium_key_state.state';
  const PREMIUM_SUBSCRIBERS_LIMIT_SETTING_KEY = 'premium.premium_key_state.data.site_active_subscriber_limit';
  const MSS_EMAIL_VOLUME_LIMIT_SETTING_KEY = 'mta.mailpoet_api_key_state.data.email_volume_limit';
  const MSS_EMAILS_SENT_SETTING_KEY = 'mta.mailpoet_api_key_state.data.emails_sent';
  const PREMIUM_SUPPORT_SETTING_KEY = 'premium.premium_key_state.data.support_tier';
  const SUBSCRIBERS_COUNT_CACHE_KEY = 'mailpoet_subscribers_count';
  const SUBSCRIBERS_COUNT_CACHE_EXPIRATION_MINUTES = 60;
  const SUBSCRIBERS_COUNT_CACHE_MIN_VALUE = 2000;

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    SubscribersRepository $subscribersRepository,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->subscribersRepository = $subscribersRepository;
    $this->wp = $wp;
  }

  /**
   * Checks if the subscribers limit is reached
   * @return bool True if subscribers limit reached or restriction set, false otherwise
   */
  public function check(): bool {
    $limit = $this->getSubscribersLimit();
    $subscribersCount = $this->getSubscribersCount();
    if ($limit && $subscribersCount > $limit) {
      return true;
    }
    // We don't have data from MSS, or they might be outdated so we need to check accessibility restrictions
    $mssStateData = $this->settings->get(Bridge::API_KEY_STATE_SETTING_NAME);
    $restriction = $mssStateData['access_restriction'] ?? '';
    return $restriction === Bridge::KEY_ACCESS_SUBSCRIBERS_LIMIT;
  }

  public function checkEmailVolumeLimitIsReached(): bool {
    // We have data from MSS and we can determine based on the data
    $emailVolumeLimit = $this->getEmailVolumeLimit();
    $emailsSent = $this->getEmailsSent();
    if ($emailVolumeLimit && $emailsSent > $emailVolumeLimit) {
      return true;
    }
    // We don't have data from MSS, or they might be outdated so we need to check accessibility restrictions
    $mssStateData = $this->settings->get(Bridge::API_KEY_STATE_SETTING_NAME);
    $restriction = $mssStateData['access_restriction'] ?? '';
    return $restriction === Bridge::KEY_ACCESS_EMAIL_VOLUME_LIMIT;
  }

  public function getSubscribersCount(): int {
    $count = $this->wp->getTransient(self::SUBSCRIBERS_COUNT_CACHE_KEY);
    if (is_numeric($count)) {
      return (int)$count;
    }
    $count = $this->subscribersRepository->getTotalSubscribers();

    // cache only when number of subscribers exceeds minimum value
    if ($this->isSubscribersCountEnoughForCache($count)) {
      $this->wp->setTransient(self::SUBSCRIBERS_COUNT_CACHE_KEY, $count, self::SUBSCRIBERS_COUNT_CACHE_EXPIRATION_MINUTES * 60);
    }
    return $count;
  }

  public function isSubscribersCountEnoughForCache(int $count = null): bool {
    if (is_null($count) && func_num_args() === 0) {
      $count = $this->getSubscribersCount();
    }
    return $count > self::SUBSCRIBERS_COUNT_CACHE_MIN_VALUE;
  }

  /**
   * Returns true if key is valid or valid but underprivileged
   * Do not use the method to check if key is valid for sending emails or premium
   * This only means that the Bridge can authenticate the key.
   * @return bool
   */
  public function hasValidApiKey(): bool {
    $mssState = $this->settings->get(self::MSS_KEY_STATE);
    $premiumState = $this->settings->get(self::PREMIUM_KEY_STATE);
    return $this->hasValidMssKey()
      || $this->hasValidPremiumKey()
      || $mssState === Bridge::KEY_VALID_UNDERPRIVILEGED
      || $premiumState === Bridge::KEY_VALID_UNDERPRIVILEGED;
  }

  public function getSubscribersLimit() {
    if (!$this->hasValidApiKey()) {
      return $this->getFreeSubscribersLimit();
    }
    $mssState = $this->settings->get(self::MSS_KEY_STATE);
    if (($this->hasValidMssKey() || $mssState === Bridge::KEY_VALID_UNDERPRIVILEGED) && $this->hasMssSubscribersLimit()) {
      return $this->getMssSubscribersLimit();
    }

    $premiumState = $this->settings->get(self::PREMIUM_KEY_STATE);
    if (($this->hasValidPremiumKey() || $premiumState === Bridge::KEY_VALID_UNDERPRIVILEGED) && $this->hasPremiumSubscribersLimit()) {
      return $this->getPremiumSubscribersLimit();
    }

    return false;
  }

  public function getEmailVolumeLimit(): int {
    return (int)$this->settings->get(self::MSS_EMAIL_VOLUME_LIMIT_SETTING_KEY);
  }

  public function getEmailsSent(): int {
    return (int)$this->settings->get(self::MSS_EMAILS_SENT_SETTING_KEY);
  }

  public function hasValidMssKey() {
    $state = $this->settings->get(self::MSS_KEY_STATE);
    return $state === Bridge::KEY_VALID || $state === Bridge::KEY_EXPIRING;
  }

  private function hasMssSubscribersLimit() {
    return !empty($this->settings->get(self::MSS_SUBSCRIBERS_LIMIT_SETTING_KEY));
  }

  private function getMssSubscribersLimit() {
    return (int)$this->settings->get(self::MSS_SUBSCRIBERS_LIMIT_SETTING_KEY);
  }

  public function hasMssPremiumSupport() {
    return $this->hasValidMssKey() && $this->settings->get(self::MSS_SUPPORT_SETTING_KEY) === 'premium';
  }

  public function hasValidPremiumKey() {
    $state = $this->settings->get(self::PREMIUM_KEY_STATE);
    return $state === Bridge::KEY_VALID || $state === Bridge::KEY_EXPIRING;
  }

  private function hasPremiumSubscribersLimit() {
    return !empty($this->settings->get(self::PREMIUM_SUBSCRIBERS_LIMIT_SETTING_KEY));
  }

  private function getPremiumSubscribersLimit() {
    return (int)$this->settings->get(self::PREMIUM_SUBSCRIBERS_LIMIT_SETTING_KEY);
  }

  public function hasPremiumSupport() {
    return $this->hasValidPremiumKey() && $this->settings->get(self::PREMIUM_SUPPORT_SETTING_KEY) === 'premium';
  }

  private function getFreeSubscribersLimit() {
    $installationTime = strtotime((string)$this->settings->get('installed_at'));
    $oldUser = $installationTime < strtotime(self::NEW_LIMIT_DATE);
    return $oldUser ? self::SUBSCRIBERS_OLD_LIMIT : self::SUBSCRIBERS_NEW_LIMIT;
  }
}
