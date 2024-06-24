<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use WC_Customer;
use WC_Order;

class CustomerPayload implements Payload {
  private ?WC_Customer $customer;
  private ?WC_Order $order;

  public function __construct(
    WC_Customer $customer = null,
    WC_Order $order = null
  ) {
    $this->customer = $customer;
    $this->order = $order;
  }

  public function getBillingCompany(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_company() : null;
    }
    return (string)$this->customer->get_billing_company();
  }

  public function getBillingPhone(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_phone() : null;
    }
    return (string)$this->customer->get_billing_phone();
  }

  public function getBillingCity(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_city() : null;
    }
    return (string)$this->customer->get_billing_city();
  }

  public function getBillingPostcode(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_postcode() : null;
    }
    return (string)$this->customer->get_billing_postcode();
  }

  public function getBillingState(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_state() : null;
    }
    return (string)$this->customer->get_billing_state();
  }

  public function getBillingCountry(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_billing_country() : null;
    }
    return (string)$this->customer->get_billing_country();
  }

  public function getShippingCompany(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_company() : null;
    }
    return (string)$this->customer->get_shipping_company();
  }

  public function getShippingPhone(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_phone() : null;
    }
    return (string)$this->customer->get_shipping_phone();
  }

  public function getShippingCity(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_city() : null;
    }
    return (string)$this->customer->get_shipping_city();
  }

  public function getShippingPostcode(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_postcode() : null;
    }
    return (string)$this->customer->get_shipping_postcode();
  }

  public function getShippingState(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_state() : null;
    }
    return (string)$this->customer->get_shipping_state();
  }

  public function getShippingCountry(): ?string {
    if ($this->isGuest()) {
      return $this->order ? (string)$this->order->get_shipping_country() : null;
    }
    return (string)$this->customer->get_shipping_country();
  }

  public function getTotalSpent(): float {
    if ($this->isGuest()) {
      return $this->order && $this->order->is_paid() ? (float)$this->order->get_total() : 0.0;
    }
    return (float)$this->customer->get_total_spent();
  }

  public function getAverageSpent(): float {
    $totalSpent = $this->getTotalSpent();
    $orderCount = $this->getOrderCount();
    return $orderCount > 0 ? ($totalSpent / $orderCount) : 0.0;
  }

  public function getOrderCount(): int {
    if ($this->isGuest()) {
      return $this->order ? 1 : 0;
    }
    return (int)$this->customer->get_order_count();
  }

  public function getCustomer(): ?WC_Customer {
    return $this->customer;
  }

  public function getOrder(): ?WC_Order {
    return $this->order;
  }

  public function getId(): int {
    return $this->customer ? $this->customer->get_id() : 0;
  }

  /** @phpstan-assert-if-true null $this->customer */
  public function isGuest(): bool {
    return $this->customer === null;
  }
}
