<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Services;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;
use MailPoet\Services\Bridge\API;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class Bridge {
  const API_KEY_SETTING_NAME = 'mta.mailpoet_api_key';
  const API_KEY_STATE_SETTING_NAME = 'mta.mailpoet_api_key_state';
  const SUBSCRIPTION_TYPE_SETTING_NAME = 'mta.mailpoet_subscription_type';
  const MANUAL_SUBSCRIPTION_TYPE = 'MANUAL';
  const STRIPE_SUBSCRIPTION_TYPE = 'STRIPE';
  const WCCOM_SUBSCRIPTION_TYPE = 'WCCOM';
  const WPCOM_SUBSCRIPTION_TYPE = 'WPCOM';
  const WPCOM_BUNDLE_SUBSCRIPTION_TYPE = 'WPCOM_BUNDLE';
  const SUBSCRIPTION_TYPES = [
    self::MANUAL_SUBSCRIPTION_TYPE,
    self::STRIPE_SUBSCRIPTION_TYPE,
    self::WCCOM_SUBSCRIPTION_TYPE,
    self::WPCOM_SUBSCRIPTION_TYPE,
    self::WPCOM_BUNDLE_SUBSCRIPTION_TYPE,
  ];

  const AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING_NAME = 'authorized_emails_addresses_check';

  const PREMIUM_KEY_SETTING_NAME = 'premium.premium_key';
  const PREMIUM_KEY_STATE_SETTING_NAME = 'premium.premium_key_state';

  const KEY_ACCESS_INSUFFICIENT_PRIVILEGES = 'insufficient_privileges';
  const KEY_ACCESS_EMAIL_VOLUME_LIMIT = 'email_volume_limit_reached';
  const KEY_ACCESS_SUBSCRIBERS_LIMIT = 'subscribers_limit_reached';

  const PREMIUM_KEY_VALID = 'valid'; // for backwards compatibility until version 3.0.0
  const KEY_VALID = 'valid';
  const KEY_INVALID = 'invalid';
  const KEY_EXPIRING = 'expiring';
  const KEY_ALREADY_USED = 'already_used';
  const KEY_VALID_UNDERPRIVILEGED = 'valid_underprivileged';

  const KEY_CHECK_ERROR = 'check_error';

  const CHECK_ERROR_UNAVAILABLE = 503;
  const CHECK_ERROR_UNKNOWN = 'unknown';

  const BRIDGE_URL = 'https://bridge.mailpoet.com';

  /** @var API|null */
  public $api;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    SettingsController $settingsController = null
  ) {
    if ($settingsController === null) {
      $settingsController = SettingsController::getInstance();
    }
    $this->settings = $settingsController;
  }

  /**
   * @deprecated Use non static function isMailpoetSendingServiceEnabled instead
   * @return bool
   */
  public static function isMPSendingServiceEnabled() {
    try {
      $mailerConfig = SettingsController::getInstance()->get(Mailer::MAILER_CONFIG_SETTING_NAME);
      return !empty($mailerConfig['method'])
        && $mailerConfig['method'] === Mailer::METHOD_MAILPOET;
    } catch (\Exception $e) {
      return false;
    }
  }

  public function isMailpoetSendingServiceEnabled() {
    try {
      $mailerConfig = SettingsController::getInstance()->get(Mailer::MAILER_CONFIG_SETTING_NAME);
      return !empty($mailerConfig['method'])
        && $mailerConfig['method'] === Mailer::METHOD_MAILPOET;
    } catch (\Exception $e) {
      return false;
    }
  }

  public static function isMSSKeySpecified() {
    $settings = SettingsController::getInstance();
    $key = $settings->get(self::API_KEY_SETTING_NAME);
    return !empty($key);
  }

  public static function isPremiumKeySpecified() {
    $settings = SettingsController::getInstance();
    $key = $settings->get(self::PREMIUM_KEY_SETTING_NAME);
    return !empty($key);
  }

  public function pingBridge() {
    $params = [
      'blocking' => true,
      'timeout' => 10,
    ];
    $wp = new WPFunctions();
    $result = $wp->wpRemoteGet(self::BRIDGE_URL, $params);
    return $wp->wpRemoteRetrieveResponseCode($result);
  }

  public function validateBridgePingResponse($responseCode) {
    return $responseCode === 200;
  }

  /**
   * @return API
   */
  public function initApi($apiKey) {
    if ($this->api instanceof API) {
      $this->api->setKey($apiKey);
    } else {
      $this->api = new Bridge\API($apiKey);
    }
    return $this->api;
  }

  /**
   * @param string $key
   * @return API
   */
  public function getApi($key) {
    return $this->initApi($key);
  }

  public function getAuthorizedEmailAddresses($type = 'authorized'): array {
    $data = $this
      ->getApi($this->settings->get(self::API_KEY_SETTING_NAME))
      ->getAuthorizedEmailAddresses();
    if ($data && $type === 'all') {
      return $data;
    }
    return isset($data[$type]) ? $data[$type] : [];
  }

  /**
   * Create Authorized Email Address
   */
  public function createAuthorizedEmailAddress(string $emailAddress) {
    return $this
      ->getApi($this->settings->get(self::API_KEY_SETTING_NAME))
      ->createAuthorizedEmailAddress($emailAddress);
  }

  /**
   * Get a list of sender domains
   * returns an assoc array of [domainName => Array(DNS responses)]
   * pass in the domain arg to return only the DNS response for the domain
   * For format see @see https://github.com/mailpoet/services-bridge#sender-domains
   */
  public function getAuthorizedSenderDomains($domain = 'all'): array {
    $domain = strtolower($domain);

    $allSenderDomains = [];
    $data = $this->getRawSenderDomainData();
    if ($data === null) {
      return [];
    }

    foreach ($data as $subarray) {
      if (isset($subarray['domain'])) {
        $allSenderDomains[strtolower($subarray['domain'])] = $subarray['dns'] ?? [];
      }
    }

    if ($domain !== 'all') {
      // return an empty array if the provided domain can not be found
      return $allSenderDomains[$domain] ?? [];
    }

    return $allSenderDomains;
  }

  public function getRawSenderDomainData(): ?array {
    return $this
      ->getApi($this->settings->get(self::API_KEY_SETTING_NAME))
      ->getAuthorizedSenderDomains();
  }

  /**
   * Create a new Sender domain record
   * returns an Array of DNS response or array of error
   * @see https://github.com/mailpoet/services-bridge#verify-a-sender-domain for response format
   */
  public function createAuthorizedSenderDomain(string $domain): array {
    $data = $this
      ->getApi($this->settings->get(self::API_KEY_SETTING_NAME))
      ->createAuthorizedSenderDomain($domain);

    return $data['dns'] ?? $data;
  }

  /**
   * Verify Sender Domain records
   * returns an Array of DNS response or an array of error
   * @see https://github.com/mailpoet/services-bridge#verify-a-sender-domain
   */
  public function verifyAuthorizedSenderDomain(string $domain): array {
    return $this
      ->getApi($this->settings->get(self::API_KEY_SETTING_NAME))
      ->verifyAuthorizedSenderDomain($domain);
  }

  public function checkMSSKey($apiKey) {
    $result = $this
      ->getApi($apiKey)
      ->checkMSSKey();
    return $this->processKeyCheckResult($result);
  }

  private function storeSubscriptionType(?string $subscriptionType): void {
    if (in_array($subscriptionType, self::SUBSCRIPTION_TYPES, true)) {
      $this->settings->set(
        self::SUBSCRIPTION_TYPE_SETTING_NAME,
        $subscriptionType
      );
    }
  }

  public function storeMSSKeyAndState($key, $state) {
    return $this->storeKeyAndState(API::KEY_CHECK_TYPE_MSS, $key, $state);
  }

  public function checkPremiumKey($key) {
    $result = $this
      ->getApi($key)
      ->checkPremiumKey();
    return $this->processKeyCheckResult($result);
  }

  private function processKeyCheckResult(array $result) {
    $stateMap = [
      200 => self::KEY_VALID,
      401 => self::KEY_INVALID,
      402 => self::KEY_ALREADY_USED,
      403 => self::KEY_VALID_UNDERPRIVILEGED,
    ];

    if (!empty($result['code']) && isset($stateMap[$result['code']])) {
      if (
        $stateMap[$result['code']] == self::KEY_VALID
        && !empty($result['data']['expire_at'])
      ) {
        $keyState = self::KEY_EXPIRING;
      } else {
        $keyState = $stateMap[$result['code']];
      }
    } else {
      $keyState = self::KEY_CHECK_ERROR;
    }

    // Map of access error messages.
    // The message is set by shop when a subscription has limited access to the feature.
    // Insufficient privileges - is the default state if the plan doesn't include the feature.
    // If the bridge returns 403 and there is a message set by the shop it returns the message.
    $accessRestrictionsMap = [
      API::ERROR_MESSAGE_INSUFFICIENT_PRIVILEGES => self::KEY_ACCESS_INSUFFICIENT_PRIVILEGES,
      API::ERROR_MESSAGE_SUBSCRIBERS_LIMIT_REACHED => self::KEY_ACCESS_SUBSCRIBERS_LIMIT,
      API::ERROR_MESSAGE_EMAIL_VOLUME_LIMIT_REACHED => self::KEY_ACCESS_EMAIL_VOLUME_LIMIT,
    ];

    $accessRestriction = null;
    if (!empty($result['code']) && $result['code'] === 403 && !empty($result['error_message'])) {
      $accessRestriction = $accessRestrictionsMap[$result['error_message']] ?? null;
    }

    return $this->buildKeyState(
      $keyState,
      $result,
      $accessRestriction
    );
  }

  public function storePremiumKeyAndState($key, $state) {
    return $this->storeKeyAndState(API::KEY_CHECK_TYPE_PREMIUM, $key, $state);
  }

  private function storeKeyAndState(string $keyType, ?string $key, ?array $state) {
    if ($keyType === API::KEY_CHECK_TYPE_PREMIUM) {
      $keySettingName = self::PREMIUM_KEY_SETTING_NAME;
      $keyStateSettingName = self::PREMIUM_KEY_STATE_SETTING_NAME;
    } else {
      $keySettingName = self::API_KEY_SETTING_NAME;
      $keyStateSettingName = self::API_KEY_STATE_SETTING_NAME;
    }

    if (
      empty($state['state'])
      || $state['state'] === self::KEY_CHECK_ERROR
    ) {
      return false;
    }

    $previousKey = $this->settings->get($keySettingName);
    // If the key remain the same and the new state is not valid we want to preserve the data from the previous state.
    // The data contain information about state limits. We need those to display the correct information to users.
    if (empty($state['data']) && $previousKey === $key) {
      $previousState = $this->settings->get($keyStateSettingName);
      if (!empty($previousState['data'])) {
        $state['data'] = $previousState['data'];
      }
    }

    // store the key itself
    if ($previousKey !== $key) {
      $this->settings->set(
        $keySettingName,
        $key
      );
    }

    // store the key state
    $this->settings->set(
      $keyStateSettingName,
      $state
    );

    // store the subscription type
    if (!empty($state['data']) && !empty($state['data']['subscription_type'])) {
      $this->storeSubscriptionType($state['data']['subscription_type']);
    }
  }

  private function buildKeyState($keyState, $result, ?string $accessRestriction): array {
    return [
      'state' => $keyState,
      'access_restriction' => $accessRestriction,
      'data' => !empty($result['data']) ? $result['data'] : null,
      'code' => !empty($result['code']) ? $result['code'] : self::CHECK_ERROR_UNKNOWN,
    ];
  }

  public function updateSubscriberCount(string $key, int $count): bool {
    return $this->getApi($key)->updateSubscriberCount($count);
  }

  public function invalidateMssKey() {
    $key = $this->settings->get(self::API_KEY_SETTING_NAME);
    $this->storeMSSKeyAndState($key, $this->buildKeyState(
      self::KEY_INVALID,
      ['code' => API::RESPONSE_CODE_KEY_INVALID],
      null
    ));
  }
}
