<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\Integrations;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
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

  /**
   * @return \AutomateWoo\Customer|false
   */
  public function getAutomateWooCustomer(string $email) {
    // AutomateWoo\Customer_Factory::get_by_email() returns false if customer is not found
    // Second parameter is set to false to prevent creating new customer if not found
    return \AutomateWoo\Customer_Factory::get_by_email($email, false);
  }

  public function setup(): void {
    if (!$this->isAutomateWooActive() || !$this->areMethodsAvailable()) {
      return;
    }
    $this->wp->addAction(SubscriberEntity::HOOK_SUBSCRIBER_STATUS_CHANGED, [$this, 'maybeOptOutSubscriber'], 10, 1);
  }

  public function optOutSubscriber($subscriber): void {
    if (!$this->isAutomateWooActive() || !$this->areMethodsAvailable()) {
      return;
    }

    $automateWooCustomer = $this->getAutomateWooCustomer($subscriber->getEmail());
    if (!$automateWooCustomer) {
      return;
    }

    $automateWooCustomer->opt_out();
  }

  public function maybeOptOutSubscriber(int $subscriberId): void {
    $subscriber = $this->subscribersRepository->findOneById($subscriberId);
    if (!$subscriber || !$subscriber->getEmail() || $subscriber->getStatus() !== SubscriberEntity::STATUS_UNSUBSCRIBED) {
      return;
    }

    $this->optOutSubscriber($subscriber);
  }
}
