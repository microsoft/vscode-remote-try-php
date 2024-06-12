<?php declare(strict_types = 1);

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;

class SubscriberActivityTracker {

  const TRACK_INTERVAL = 60; // 1 minute

  /** @var PageViewCookie */
  private $pageViewCookie;

  /** @var SubscriberCookie */
  private $subscriberCookie;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var  WPFunctions */
  private $wp;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var callable[] */
  private $callbacks = [];

  /** @var WooCommerceHelper */
  private $wooCommerceHelper;

  public function __construct(
    PageViewCookie $pageViewCookie,
    SubscriberCookie $subscriberCookie,
    SubscribersRepository $subscribersRepository,
    WPFunctions $wp,
    WooCommerceHelper $wooCommerceHelper,
    TrackingConfig $trackingConfig
  ) {
    $this->pageViewCookie = $pageViewCookie;
    $this->subscriberCookie = $subscriberCookie;
    $this->subscribersRepository = $subscribersRepository;
    $this->wp = $wp;
    $this->wooCommerceHelper = $wooCommerceHelper;
    $this->trackingConfig = $trackingConfig;
  }

  public function trackActivity(): bool {
    // Don't track in admin interface
    if ($this->wp->isAdmin()) {
      return false;
    }

    $subscriber = null;
    $latestTimestamp = $this->getLatestTimestampFromCookie();

    // If cookie tracking is not allowed try use last activity from subscriber data
    if ($latestTimestamp === null) {
      $subscriber = $this->getSubscriber();
      if (!$subscriber) {
        return false; // Can't determine timestamp
      }
      $latestTimestamp = $this->getLatestTimestampFromSubscriber($subscriber);
    }

    if ($latestTimestamp + self::TRACK_INTERVAL > $this->wp->currentTime('timestamp')) {
      return false;
    }

    if ($subscriber === null) {
      $subscriber = $this->getSubscriber();
    }

    if (!$subscriber) {
      return false;
    }

    $this->processTracking($subscriber);
    return true;
  }

  public function registerCallback(string $slug, callable $callback): void {
    $this->callbacks[$slug] = $callback;
  }

  public function unregisterCallback(string $slug): void {
    unset($this->callbacks[$slug]);
  }

  private function processTracking(SubscriberEntity $subscriber): void {
    $this->subscribersRepository->maybeUpdateLastPageViewAt($subscriber);
    $this->pageViewCookie->setPageViewTimestamp($this->wp->currentTime('timestamp'));
    foreach ($this->callbacks as $callback) {
      $callback($subscriber);
    }
  }

  private function getLatestTimestampFromCookie(): ?int {
    if ($this->trackingConfig->isCookieTrackingEnabled()) {
      return $this->pageViewCookie->getPageViewTimestamp() ?? 0;
    }
    return null;
  }

  private function getLatestTimestampFromSubscriber(SubscriberEntity $subscriber): int {
    return $subscriber->getLastEngagementAt() ? $subscriber->getLastEngagementAt()->getTimestamp() : 0;
  }

  private function getSubscriber(): ?SubscriberEntity {
    $wpUser = $this->wp->wpGetCurrentUser();
    if ($wpUser->exists()) {
      return $this->subscribersRepository->findOneBy(['wpUserId' => $wpUser->ID]);
    }

    $subscriberId = $this->subscriberCookie->getSubscriberId();
    if ($subscriberId) {
      return $this->subscribersRepository->findOneById($subscriberId);
    }

    if (!$this->wooCommerceHelper->isWooCommerceActive()) {
      return null;
    }
    $wooCommerce = $this->wooCommerceHelper->WC();
    if (!$wooCommerce || !$wooCommerce->session) {
      return null;
    }
    $customer = $wooCommerce->session->get('customer');
    if (!is_array($customer) || empty($customer['email'])) {
      return null;
    }
    return $this->subscribersRepository->findOneBy(['email' => $customer['email']]);
  }
}
