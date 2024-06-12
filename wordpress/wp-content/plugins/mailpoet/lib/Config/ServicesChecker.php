<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\Util\License\License;
use MailPoet\WP\DateTime;
use MailPoet\WP\Notice as WPNotice;

class ServicesChecker {

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  public function __construct() {
    $this->settings = SettingsController::getInstance();
    $this->subscribersFeature = ContainerWrapper::getInstance()->get(SubscribersFeature::class);
  }

  public function isPremiumPluginActive() {
    return License::getLicense() ? true : false;
  }

  public function isMailPoetAPIKeyValid($displayErrorNotice = true, $forceCheck = false) {
    if (!$forceCheck && !Bridge::isMPSendingServiceEnabled()) {
      return null;
    }

    $mssKeySpecified = Bridge::isMSSKeySpecified();
    $mssKey = $this->settings->get(Bridge::API_KEY_STATE_SETTING_NAME);

    if (
      !$mssKeySpecified
      || empty($mssKey['state'])
      || $mssKey['state'] == Bridge::KEY_INVALID
    ) {
      if ($displayErrorNotice) {
        $error = '<h3>' . __('All sending is currently paused!', 'mailpoet') . '</h3>';
        $error .= '<p>' . __('Your key to send with MailPoet is invalid.', 'mailpoet') . '</p>';
        $error .= '<p><a '
          . ' href="https://account.mailpoet.com?s=' . ($this->subscribersFeature->getSubscribersCount() + 1) . '"'
          . ' class="button button-primary" '
          . ' target="_blank"'
          . '>' . __('Purchase a key', 'mailpoet') . '</a></p>';

        WPNotice::displayError($error, '', '', false, false);
      }
      return false;
    } elseif (
      $mssKey['state'] == Bridge::KEY_EXPIRING
      && !empty($mssKey['data']['expire_at'])
    ) {
      if ($displayErrorNotice) {
        $dateTime = new DateTime();
        $date = $dateTime->formatDate(strtotime($mssKey['data']['expire_at']));
        $error = Helpers::replaceLinkTags(
          // translators: %s is a date.
          __("Your newsletters are awesome! Don't forget to [link]upgrade your MailPoet email plan[/link] by %s to keep sending them to your subscribers.", 'mailpoet'),
          'https://account.mailpoet.com?s=' . $this->subscribersFeature->getSubscribersCount(),
          ['target' => '_blank']
        );
        $error = sprintf($error, $date);
        WPNotice::displayWarning($error);
      }
      return true;
    } elseif ($mssKey['state'] == Bridge::KEY_VALID) {
      return true;
    }

    return false;
  }

  public function isPremiumKeyValid($displayErrorNotice = true) {
    $premiumKeySpecified = Bridge::isPremiumKeySpecified();
    $premiumPluginActive = License::getLicense();
    $premiumKey = $this->settings->get(Bridge::PREMIUM_KEY_STATE_SETTING_NAME);

    if (!$premiumPluginActive) {
      $displayErrorNotice = false;
    }

    if (
      !$premiumKeySpecified
      || empty($premiumKey['state'])
      || $premiumKey['state'] === Bridge::KEY_INVALID
      || $premiumKey['state'] === Bridge::KEY_ALREADY_USED
    ) {
      if ($displayErrorNotice) {
        $errorString = __('[link1]Register[/link1] your copy of the MailPoet Premium plugin to receive access to automatic upgrades and support. Need a license key? [link2]Purchase one now.[/link2]', 'mailpoet');
        $error = Helpers::replaceLinkTags(
          $errorString,
          'admin.php?page=mailpoet-settings#premium',
          [],
          'link1'
        );
        $error = Helpers::replaceLinkTags(
          $error,
          'admin.php?page=mailpoet-upgrade',
          [],
          'link2'
        );
        WPNotice::displayWarning($error);
      }
      return false;
    } elseif (
      $premiumKey['state'] === Bridge::KEY_EXPIRING
      && !empty($premiumKey['data']['expire_at'])
    ) {
      if ($displayErrorNotice) {
        $dateTime = new DateTime();
        $date = $dateTime->formatDate(strtotime($premiumKey['data']['expire_at']));
        $error = Helpers::replaceLinkTags(
          // translators: %s is a date.
          __("Your License Key for MailPoet is expiring! Don't forget to [link]renew your license[/link] by %s to keep enjoying automatic updates and Premium support.", 'mailpoet'),
          'https://account.mailpoet.com',
          ['target' => '_blank']
        );
        $error = sprintf($error, $date);
        WPNotice::displayWarning($error);
      }
      return true;
    } elseif ($premiumKey['state'] === Bridge::KEY_VALID) {
      return true;
    }

    return false;
  }

  public function isBundledSubscription(): bool {
    $subscriptionType = $this->settings->get(Bridge::SUBSCRIPTION_TYPE_SETTING_NAME);
    return $subscriptionType === Bridge::WPCOM_BUNDLE_SUBSCRIPTION_TYPE;
  }

  public function isMailPoetAPIKeyPendingApproval(): bool {
    $mssActive = Bridge::isMPSendingServiceEnabled();
    $mssKeyValid = $this->isMailPoetAPIKeyValid();
    $isApproved = $this->settings->get('mta.mailpoet_api_key_state.data.is_approved');
    $mssKeyPendingApproval = $isApproved === false || $isApproved === 'false'; // API unfortunately saves this as a string
    return $mssActive && $mssKeyValid && $mssKeyPendingApproval;
  }

  public function isUserActivelyPaying(): bool {
    $isPremiumKeyValid = $this->isPremiumKeyValid(false);

    $mssActive = Bridge::isMPSendingServiceEnabled();
    $isMssKeyValid = $this->isMailPoetAPIKeyValid(false);

    if (!$mssActive || ($isPremiumKeyValid && !$isMssKeyValid)) {
      return $this->subscribersFeature->hasPremiumSupport();
    } else {
      return $this->subscribersFeature->hasMssPremiumSupport();
    }
  }

  /**
   * Return a key when it can be used for account administration purposes (stats report, auth. addresses or domains administration)
   * Key can be used when it is valid for MSS or Premium, but also when it is valid but has no privileges for MSS or Premium (API returns 403).
   */
  public function getValidAccountKey(): ?string {
    if ($this->isMailPoetAPIKeyValid(false, true)) {
      return $this->settings->get(Bridge::API_KEY_SETTING_NAME);
    }
    $mssKeyState = $this->settings->get(Bridge::API_KEY_STATE_SETTING_NAME);
    if (($mssKeyState['state'] ?? null) === Bridge::KEY_VALID_UNDERPRIVILEGED) {
      return $this->settings->get(Bridge::API_KEY_SETTING_NAME);
    }

    if ($this->isPremiumKeyValid(false)) {
      return $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    }
    $premiumKeyState = $this->settings->get(Bridge::PREMIUM_KEY_STATE_SETTING_NAME);
    if (($premiumKeyState['state'] ?? null) === Bridge::KEY_VALID_UNDERPRIVILEGED) {
      return $this->settings->get(Bridge::PREMIUM_KEY_SETTING_NAME);
    }

    return null;
  }

  public function generatePartialApiKey(): string {
    $key = (string)($this->getValidAccountKey());
    if ($key) {
      $halfKeyLength = (int)(strlen($key) / 2);

      return substr($key, 0, $halfKeyLength);
    }
    return '';
  }
}
