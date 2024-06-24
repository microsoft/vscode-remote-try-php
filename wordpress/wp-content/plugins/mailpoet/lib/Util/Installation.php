<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Installation {
  const NEW_INSTALLATION_DAYS_LIMIT = 30;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function isNewInstallation() {
    $installedAt = $this->settings->get('installed_at');
    if (is_null($installedAt)) {
      return true;
    }
    $installedAt = Carbon::createFromTimestamp(strtotime($installedAt));
    $currentTime = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    return $currentTime->diffInDays($installedAt) <= self::NEW_INSTALLATION_DAYS_LIMIT;
  }
}
