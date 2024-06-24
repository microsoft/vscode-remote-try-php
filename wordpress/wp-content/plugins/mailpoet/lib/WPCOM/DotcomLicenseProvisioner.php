<?php declare(strict_types = 1);

namespace MailPoet\WPCOM;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\ErrorResponse;
use MailPoet\API\JSON\v1\Services;
use MailPoet\API\JSON\v1\Settings;
use MailPoet\Logging\LoggerFactory;
use WP_Error;

/**
 * This class is responsible for receiving and activating the license purchased from WP.com Marketplace.
 */
class DotcomLicenseProvisioner {
  const EVENT_TYPE_PROVISION_LICENSE = 'provision_license';

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var Settings */
  private $settings;

  /** @var Services */
  private $services;

  /** @var DotcomHelperFunctions */
  private $dotcomHelperFunctions;

  public function __construct(
    LoggerFactory $loggerFactory,
    Settings $settings,
    Services $services,
    DotcomHelperFunctions $dotcomHelperFunctions
  ) {
    $this->loggerFactory = $loggerFactory;
    $this->settings = $settings;
    $this->services = $services;
    $this->dotcomHelperFunctions = $dotcomHelperFunctions;
  }

  /**
   * Activates MSS and adds API key for subscriptions purchased from WP.com Marketplace.
   *
   * @param bool $result
   * @param array $licensePayload
   * @param string $eventType
   * @return bool|WP_Error
   */
  public function provisionLicense(bool $result, array $licensePayload, string $eventType) {
    if (!$this->dotcomHelperFunctions->isAtomicPlatform() || $eventType !== self::EVENT_TYPE_PROVISION_LICENSE) {
      return $result;
    }

    $apiKey = $this->getKeyFromPayload($licensePayload);
    if (is_wp_error($apiKey)) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_PROVISIONING)->error(
        'key was not found in license payload'
      );
      return $apiKey;
    }

    return $this->activateMSS($apiKey);
  }

  /**
   * Returns API key from license payload.
   * @param array $licensePayload
   * @return string|WP_Error
   */
  private function getKeyFromPayload(array $licensePayload) {
    if (isset($licensePayload['apiKey']) && is_string($licensePayload['apiKey'])) {
      return $licensePayload['apiKey'];
    }

    return new WP_Error('invalid_license_payload', 'Invalid license payload: Missing API key.');
  }

  /**
   * Saves the API key and activates MSS.
   * @param string $apiKey
   * @return true|WP_Error
   */
  public function activateMSS(string $apiKey) {
    $response = $this->settings->setKeyAndSetupMss($apiKey);
    if ($response instanceof ErrorResponse) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_PROVISIONING)->error(
        'Setting sending method and key failed',
        ['$response' => $response]
      );
      return new WP_Error('Provisioning failed setting the data', $this->concatMessages($response));
    }

    // This is necessary if the key changed but the sending method was already set to MailPoet
    $response = $this->services->refreshMSSKeyStatus();
    if ($response instanceof ErrorResponse) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_PROVISIONING)->error(
        'Refreshing the key failed',
        ['$response' => $response]
      );
      return new WP_Error('Provisioning failed activating the data', $this->concatMessages($response));
    }

    $response = $this->services->refreshPremiumKeyStatus();
    if ($response instanceof ErrorResponse) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_PROVISIONING)->error(
        'Refreshing Premium key failed',
        ['$response' => $response]
      );
      return new WP_Error('Provisioning failed to verify api key access for MSS/premium', $this->concatMessages($response));
    }

    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_PROVISIONING)->info(
      'License was provisioned'
    );
    return true;
  }

  private function concatMessages(ErrorResponse $response): string {
    $data = $response->getData();
    $result = '';

    if (empty($data) || !isset($data['errors'])) {
      return $result;
    }

    foreach ($data['errors'] as $error) {
      $result .= $error['message'] . " ";
    }
    return $result;
  }
}
