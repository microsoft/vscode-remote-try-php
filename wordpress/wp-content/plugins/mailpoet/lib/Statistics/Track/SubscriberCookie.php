<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Settings\TrackingConfig;
use MailPoet\Util\Cookies;

class SubscriberCookie {
  const COOKIE_NAME = 'mailpoet_subscriber';
  const COOKIE_NAME_LEGACY = 'mailpoet_abandoned_cart_tracking';
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

  public function getSubscriberId(): ?int {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return null;
    }

    $subscriberId = $this->getSubscriberIdFromCookie(self::COOKIE_NAME);
    if ($subscriberId) {
      return $subscriberId;
    }

    $subscriberId = $this->getSubscriberIdFromCookie(self::COOKIE_NAME_LEGACY);
    if ($subscriberId) {
      $this->setSubscriberId($subscriberId);
      $this->cookies->delete(self::COOKIE_NAME_LEGACY);
      return $subscriberId;
    }
    return null;
  }

  public function setSubscriberId(int $subscriberId): void {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return;
    }

    $this->cookies->set(
      self::COOKIE_NAME,
      ['subscriber_id' => $subscriberId],
      [
        'expires' => time() + self::COOKIE_EXPIRY,
        'path' => '/',
      ]
    );
  }

  private function getSubscriberIdFromCookie(string $cookieName): ?int {
    $data = $this->cookies->get($cookieName);
    return is_array($data) && $data['subscriber_id']
      ? (int)$data['subscriber_id']
      : null;
  }
}
