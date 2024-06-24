<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Referrals;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class UrlDecorator {

  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    WPFunctions $wp,
    SettingsController $settings
  ) {
    $this->wp = $wp;
    $this->settings = $settings;
  }

  public function decorate($url) {
    $referralId = $this->settings->get(ReferralDetector::REFERRAL_SETTING_NAME, null);
    if ($referralId === null) {
      return $url;
    }
    return $this->wp->addQueryArg('ref', $referralId, $url);
  }
}
