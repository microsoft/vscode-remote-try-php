<?php declare(strict_types = 1);

namespace MailPoet\Settings;

if (!defined('ABSPATH')) exit;


class TrackingConfig {
  const LEVEL_FULL = 'full';
  const LEVEL_PARTIAL = 'partial';
  const LEVEL_BASIC = 'basic';

  /** @var SettingsController */
  private $settings;

  public function __construct(
    SettingsController $settings
  ) {
    $this->settings = $settings;
  }

  public function isEmailTrackingEnabled(string $level = null): bool {
    $level = $level ?? $this->settings->get('tracking.level', self::LEVEL_FULL);
    return in_array($level, [self::LEVEL_PARTIAL, self::LEVEL_FULL], true);
  }

  public function isCookieTrackingEnabled(string $level = null): bool {
    $level = $level ?? $this->settings->get('tracking.level', self::LEVEL_FULL);
    return $level === self::LEVEL_FULL;
  }

  public function getConfig(): array {
    return [
      'level' => $this->settings->get('tracking.level', self::LEVEL_FULL),
      'emailTrackingEnabled' => $this->isEmailTrackingEnabled(),
      'cookieTrackingEnabled' => $this->isCookieTrackingEnabled(),
    ];
  }
}
