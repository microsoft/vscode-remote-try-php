<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderStatusChangePayload;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderStatusChangeSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class OrderStatusChangedTrigger implements Trigger {

  /** @var WordPress */
  protected $wp;

  /** @var WooCommerce */
  protected $woocommerce;

  public function __construct(
    WordPress $wp,
    WooCommerce $woocommerceHelper
  ) {
    $this->wp = $wp;
    $this->woocommerce = $woocommerceHelper;
  }

  public function getKey(): string {
    return 'woocommerce:order-status-changed';
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Order status changed', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'from' => Builder::string()->required()->default('any'),
      'to' => Builder::string()->required()->default('wc-completed'),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      OrderSubject::KEY,
      OrderStatusChangeSubject::KEY,
      CustomerSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'woocommerce_order_status_changed',
      [
        $this,
        'handle',
      ],
      10,
      3
    );
  }

  public function handle(int $orderId, string $oldStatus, string $newStatus): void {
    $order = $this->woocommerce->wcGetOrder($orderId);
    if (!$order instanceof \WC_Order) {
      return;
    }

    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(OrderStatusChangeSubject::KEY, ['from' => $oldStatus, 'to' => $newStatus]),
      new Subject(OrderSubject::KEY, ['order_id' => $order->get_id()]),
      new Subject(CustomerSubject::KEY, ['customer_id' => $order->get_customer_id(), 'order_id' => $order->get_id()]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    /** @var OrderStatusChangePayload $orderPayload */
    $orderPayload = $args->getSinglePayloadByClass(OrderStatusChangePayload::class);
    $triggerArgs = $args->getStep()->getArgs();
    $configuredFrom = $triggerArgs['from'] ? str_replace('wc-', '', $triggerArgs['from']) : null;
    $configuredTo = $triggerArgs['to'] ? str_replace('wc-', '', $triggerArgs['to']) : null;
    if ($configuredFrom !== 'any' && $orderPayload->getFrom() !== $configuredFrom) {
      return false;
    }
    if ($configuredTo !== 'any' && $orderPayload->getTo() !== $configuredTo) {
      return false;
    }
    return true;
  }
}
