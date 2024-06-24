<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderStatusChangePayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class OrderCompletedTrigger extends OrderStatusChangedTrigger {
  public function getKey(): string {
    return 'woocommerce:order-completed';
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Order completed', 'mailpoet');
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $orderPayload = $args->getSinglePayloadByClass(OrderStatusChangePayload::class);
    return $orderPayload->getTo() === 'completed';
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }
}
