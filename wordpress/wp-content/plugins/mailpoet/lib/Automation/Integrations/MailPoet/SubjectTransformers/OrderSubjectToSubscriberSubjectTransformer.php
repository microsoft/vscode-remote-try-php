<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments;
use MailPoet\Subscribers\SubscribersRepository;

class OrderSubjectToSubscriberSubjectTransformer implements SubjectTransformer {

  /** @var SubscribersRepository  */
  private $subscribersRepository;

  /** @var Segments\WooCommerce  */
  private $woocommerce;

  /** @var WooCommerce  */
  private $woocommerceHelper;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    Segments\WooCommerce $woocommerce,
    WooCommerce $woocommerceHelper
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->woocommerce = $woocommerce;
    $this->woocommerceHelper = $woocommerceHelper;
  }

  public function transform(Subject $data): Subject {
    if ($this->accepts() !== $data->getKey()) {
      throw new \InvalidArgumentException('Invalid subject type');
    }

      $subscriber = $this->findOrCreateSubscriber($data);
    if (!$subscriber instanceof SubscriberEntity) {
      throw new \InvalidArgumentException('Subscriber not found');
    }

    return new Subject(SubscriberSubject::KEY, ['subscriber_id' => $subscriber->getId()]);
  }

  public function accepts(): string {
    return OrderSubject::KEY;
  }

  public function returns(): string {
    return SubscriberSubject::KEY;
  }

  private function findOrCreateSubscriber(Subject $order): ?SubscriberEntity {
    $subscriber = $this->findSubscriber($order);
    if ($subscriber) {
      return $subscriber;
    }

    $orderId = $order->getArgs()['order_id'] ?? null;
    if (!$orderId) {
      return null;
    }
    $this->woocommerce->synchronizeGuestCustomer($orderId);

    return $this->findSubscriber($order);
  }

  private function findSubscriber(Subject $order): ?SubscriberEntity {
    $orderId = $order->getArgs()['order_id'] ?? null;
    if (!$orderId) {
      return null;
    }
    $wcOrder = $this->woocommerceHelper->wcGetOrder($orderId);
    if (!$wcOrder instanceof \WC_Order) {
      return null;
    }
    $billingEmail = $wcOrder->get_billing_email();
    return $billingEmail ?
      $this->subscribersRepository->findOneBy(['email' => $billingEmail]) :
      $this->subscribersRepository->findOneBy(['wpUserId' => $wcOrder->get_user_id()]);
  }
}
