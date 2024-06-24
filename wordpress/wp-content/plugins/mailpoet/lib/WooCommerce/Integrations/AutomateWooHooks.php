<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\Integrations;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class AutomateWooHooks {
  const AUTOMATE_WOO_PLUGIN_SLUG = 'automatewoo/automatewoo.php';

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    WPFunctions $wp
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->wp = $wp;
  }

  public function isAutomateWooActive(): bool {
    return $this->wp->isPluginActive(self::AUTOMATE_WOO_PLUGIN_SLUG);
  }

  public function areMethodsAvailable(): bool {
    return class_exists('AutomateWoo\Customer_Factory') && method_exists('AutomateWoo\Customer_Factory', 'get_by_email') &&
      class_exists('AutomateWoo\Customer') && method_exists('AutomateWoo\Customer', 'opt_out');
  }

  public function isAutomateWooReady(): bool {
    return $this->isAutomateWooActive() && $this->areMethodsAvailable();
  }

  /**
   * @return \AutomateWoo\Customer|false
   */
  public function getAutomateWooCustomer(string $email) {
    // AutomateWoo\Customer_Factory::get_by_email() returns false if customer is not found
    // Second parameter is set to false to prevent creating new customer if not found
    return \AutomateWoo\Customer_Factory::get_by_email($email, false);
  }

  public function setup(): void {
    if (!$this->isAutomateWooReady()) {
      return;
    }
    $this->wp->addAction(SubscriberEntity::HOOK_SUBSCRIBER_STATUS_CHANGED, [$this, 'syncSubscriber'], 10, 1);
    $this->wp->addAction('mailpoet_segment_subscribed', [$this, 'maybeOptInSubscriber'], 10, 1);
  }

  public function optOutSubscriber($subscriber): void {
    if (!$this->isAutomateWooReady() || !$subscriber) {
      return;
    }

    $automateWooCustomer = $this->getAutomateWooCustomer($subscriber->getEmail());
    if (!$automateWooCustomer) {
      return;
    }

    $automateWooCustomer->opt_out();
  }

  public function optInSubscriber($subscriber): void {
    if (!$this->isAutomateWooReady() || !$subscriber) {
      return;
    }

    $automateWooCustomer = $this->getAutomateWooCustomer($subscriber->getEmail());
    if (!$automateWooCustomer) {
      return;
    }

    $automateWooCustomer->opt_in();
  }

  public function syncSubscriber(int $subscriberId): void {
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber || !$subscriber->getEmail()) {
      return;
    }

    if ($this->isWooCommerceSubscribed($subscriber)) {
      $this->optInSubscriber($subscriber);
    } else {
      $this->optOutSubscriber($subscriber);
    }
  }

  /**
   * Opt-In the subscriber in AW only if the subscriber belongs to WooCommerce list.
   */
  public function maybeOptInSubscriber(SubscriberSegmentEntity $subscriberSegment) {
    if ($subscriberSegment->getSegment() && $subscriberSegment->getSegment()->getType() === SegmentEntity::TYPE_WC_USERS) {
      $this->optInSubscriber($subscriberSegment->getSubscriber());
    }
  }

  private function isWooCommerceSubscribed(SubscriberEntity $subscriber) {
    return $subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED
      && $this->subscribersRepository->getWooCommerceSegmentSubscriber($subscriber->getEmail());
  }
}
