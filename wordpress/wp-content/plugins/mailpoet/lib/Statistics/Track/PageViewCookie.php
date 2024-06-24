<?php declare(strict_types = 1);

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\TrackingConfig;
use MailPoet\Util\Cookies;

class PageViewCookie {
  const COOKIE_NAME = 'mailpoet_page_view';
  const COOKIE_EXPIRY = 10 * 365 * 24 * 60 * 60; // 10 years (~ no expiry)

  /** @var Cookies */
  private $cookies;

  /** @var TrackingConfig */
  private $trackingConfig;

  public function __construct(
    Cookies $cookies,
    TrackingConfig $trackingConfig
  ) {
    $this->cookies = $cookies;
    $this->trackingConfig = $trackingConfig;
  }

  public function getPageViewTimestamp(): ?int {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return null;
    }

    return $this->getTimestampCookie(self::COOKIE_NAME);
  }

  public function setPageViewTimestamp(int $timestamp): void {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return;
    }

    $this->cookies->set(
      self::COOKIE_NAME,
      ['timestamp' => $timestamp],
      [
        'expires' => time() + self::COOKIE_EXPIRY,
        'path' => '/',
      ]
    );
  }

  private function getTimestampCookie(string $cookieName): ?int {
    $data = $this->cookies->get($cookieName);
    return is_array($data) && $data['timestamp']
      ? (int)$data['timestamp']
      : null;
  }
}
