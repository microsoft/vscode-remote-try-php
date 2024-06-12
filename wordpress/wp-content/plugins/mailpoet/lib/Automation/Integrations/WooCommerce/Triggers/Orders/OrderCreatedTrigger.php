<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class OrderCreatedTrigger implements Trigger {

  /** @var WordPress */
  private $wp;

  /** @var int[] */
  private $processedOrders = [];

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  public function getKey(): string {
    return 'woocommerce:order-created';
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Order created', 'mailpoet');
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'woocommerce_new_order',
      [
        $this,
        'handleCreate',
      ],
      10,
      2
    );
  }

  /**
   * @param int $orderId
   * @param \WC_Order $order
   * @return void
   */
  public function handleCreate($orderId, $order) {

    if (in_array($orderId, $this->processedOrders)) {
      return;
    }
    /**
     * Creating an order via wc_create_order() does not yet set crucial information like the customer's email address.
     * It just creates the order object and saves it to the database. We need therefore to wait for the order to have at least the billing address stored.
     **/
    if (!$order->get_billing_email()) {
      add_action(
        'woocommerce_after_order_object_save',
        function($order) use ($orderId) {
          if ((int)$orderId !== (int)$order->get_id()) {
            return;
          }
          $this->handleCreate($order->get_id(), $order);
        }
      );
      return;
    }
    $this->processedOrders[] = $orderId;
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(OrderSubject::KEY, ['order_id' => $order->get_id()]),
      new Subject(CustomerSubject::KEY, ['customer_id' => $order->get_customer_id(), 'order_id' => $order->get_id()]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    /**
     * If we come to this point we always want to trigger the automation.
     * The evaluation whether this is a "new" order is done in the handleCreate() method.
     */
    return true;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }

  public function getSubjectKeys(): array {
    return [
      OrderSubject::KEY,
      CustomerSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }
}
