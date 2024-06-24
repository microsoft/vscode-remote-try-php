<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Referrals;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class ReferralDetector {
  const REFERRAL_CONSTANT_NAME = 'MAILPOET_REFERRAL_ID';
  const REFERRAL_SETTING_NAME = 'referral_id';

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

  public function detect() {
    $referralId = $this->settings->get(self::REFERRAL_SETTING_NAME, null);
    if ($referralId) {
      return $referralId;
    }
    $referralId = $this->wp->getOption(self::REFERRAL_CONSTANT_NAME, null);
    if ($referralId === null && defined(self::REFERRAL_CONSTANT_NAME)) {
      $referralId = constant(self::REFERRAL_CONSTANT_NAME);
    }
    if ($referralId !== null) {
      $this->settings->set(self::REFERRAL_SETTING_NAME, $referralId);
    }
    return $referralId;
  }
}
