<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderStatusChangePayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class OrderCancelledTrigger extends OrderStatusChangedTrigger {
  public function getKey(): string {
    return 'woocommerce:order-cancelled';
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Order cancelled', 'mailpoet');
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    /** @var OrderStatusChangePayload $orderPayload */
    $orderPayload = $args->getSinglePayloadByClass(OrderStatusChangePayload::class);
    return $orderPayload->getTo() === 'cancelled';
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }
}
