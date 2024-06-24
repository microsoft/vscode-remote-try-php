<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Analytics;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\SettingsController;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Analytics {

  const SETTINGS_LAST_SENT_KEY = 'analytics_last_sent';
  const SEND_AFTER_DAYS = 7;
  const ANALYTICS_FILTER = 'mailpoet_analytics';

  /** @var Reporter */
  private $reporter;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    Reporter $reporter,
    SettingsController $settingsController
  ) {
    $this->reporter = $reporter;
    $this->settings = $settingsController;
    $this->wp = new WPFunctions;
  }

  /** @return array|null */
  public function generateAnalytics() {
    if ($this->shouldSend()) {
      $data = $this->getAnalyticsData();
      $this->recordDataSent();
      return $data;
    }
    return null;
  }

  public function getAnalyticsData() {
    return $this->wp->applyFilters(self::ANALYTICS_FILTER, $this->reporter->getData());
  }

  /** @return bool */
  public function isEnabled() {
    $analyticsSettings = $this->settings->get('analytics', []);
    return !empty($analyticsSettings['enabled']) === true;
  }

  public function setPublicId($newPublicId) {
    $currentPublicId = $this->settings->get('public_id');
    if ($currentPublicId !== $newPublicId) {
      $this->settings->set('public_id', $newPublicId);
      $this->settings->set('new_public_id', 'true');
      // Force user data to be resent
      $this->settings->delete(Analytics::SETTINGS_LAST_SENT_KEY);
    }
  }

  /** @return string */
  public function getPublicId() {
    $publicId = $this->settings->get('public_id', '');
    if (empty($publicId)) {
      // The previous implementation used md5, so this is just to ensure consistency
      $randomId = md5(Security::generateRandomString(32));
      $this->settings->set('public_id', $randomId);
      $this->settings->set('new_public_id', 'true');
      return $randomId;
    }
    return $publicId;
  }

  /**
   * Returns true if a the public_id was added and update new_public_id to false
   * @return bool
   */
  public function isPublicIdNew() {
    $newPublicId = $this->settings->get('new_public_id');
    if ($newPublicId === 'true') {
      $this->settings->set('new_public_id', 'false');
      return true;
    }
    return false;
  }

  public function shouldSend() {
    if (!$this->isEnabled()) {
      return false;
    }
    $nextSend = $this->getNextSendDate();
    return $nextSend->isPast();
  }

  public function getNextSendDate(): Carbon {
    $lastSent = $this->settings->get(Analytics::SETTINGS_LAST_SENT_KEY);
    if (!$lastSent) {
      return Carbon::now()->subMinute();
    }

    return Carbon::createFromTimestamp(strtotime($lastSent))->addDays(self::SEND_AFTER_DAYS);
  }

  public function recordDataSent() {
    $this->settings->set(Analytics::SETTINGS_LAST_SENT_KEY, Carbon::now());
  }
}
