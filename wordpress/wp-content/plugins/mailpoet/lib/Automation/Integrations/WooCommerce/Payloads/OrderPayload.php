<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;

class OrderPayload implements Payload {

  /** @var \WC_Order */
  private $order;

  public function __construct(
    \WC_Order $order
  ) {
    $this->order = $order;
  }

  public function getOrder(): \WC_Order {
    return $this->order;
  }

  public function getEmail(): string {
    return $this->order->get_billing_email();
  }

  public function getId(): int {
    return $this->order->get_id();
  }
}
