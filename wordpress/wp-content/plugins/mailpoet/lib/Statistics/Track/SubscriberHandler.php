<?php declare(strict_types = 1);

namespace MailPoet\Statistics\Track;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class SubscriberHandler {
  /** @var SubscriberCookie */
  private $subscriberCookie;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var TrackingConfig */
  private $trackingConfig;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SubscriberCookie $subscriberCookie,
    SubscribersRepository $subscribersRepository,
    TrackingConfig $trackingConfig,
    WPFunctions $wp
  ) {
    $this->subscriberCookie = $subscriberCookie;
    $this->subscribersRepository = $subscribersRepository;
    $this->trackingConfig = $trackingConfig;
    $this->wp = $wp;
  }

  public function identifyByLogin(string $login): void {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return;
    }

    $wpUser = $this->wp->getUserBy('login', $login);
    if ($wpUser) {
      $this->identifyByEmail($wpUser->user_email); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }
  }

  public function identifyByEmail(string $email): void {
    if (!$this->trackingConfig->isCookieTrackingEnabled()) {
      return;
    }

    $subscriber = $this->subscribersRepository->findOneBy(['email' => $email]);
    if ($subscriber) {
      $this->setCookieBySubscriber($subscriber);
    }
  }

  private function setCookieBySubscriber(SubscriberEntity $subscriber): void {
    $subscriberId = $subscriber->getId();
    if ($subscriberId) {
      $this->subscriberCookie->setSubscriberId($subscriberId);
    }
  }
}
