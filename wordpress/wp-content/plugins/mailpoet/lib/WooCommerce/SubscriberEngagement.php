<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\SubscribersRepository;
use WC_Order;

class SubscriberEngagement {

  /** @var Helper */
  private $woocommerceHelper;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    Helper $woocommerceHelper,
    SubscribersRepository $subscribersRepository
  ) {
    $this->woocommerceHelper = $woocommerceHelper;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function updateSubscriberEngagement($orderId): void {
    $order = $this->woocommerceHelper->wcGetOrder($orderId);
    if (!$order instanceof WC_Order) {
      return;
    }

    $subscriber = $this->subscribersRepository->findOneBy(['email' => $order->get_billing_email()]);
    if (!$subscriber instanceof SubscriberEntity) {
      return;
    }

    $this->subscribersRepository->maybeUpdateLastEngagement($subscriber);
  }

  public function updateSubscriberLastPurchase($orderId): void {
    $order = $this->woocommerceHelper->wcGetOrder($orderId);
    if (!$order instanceof WC_Order) {
      return;
    }

    $subscriber = $this->subscribersRepository->findOneBy(['email' => $order->get_billing_email()]);
    if (!$subscriber instanceof SubscriberEntity) {
      return;
    }

    $this->subscribersRepository->maybeUpdateLastPurchaseAt($subscriber);
  }
}
