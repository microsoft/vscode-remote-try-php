<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\WooCommerce\Fields\OrderFieldsFactory;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderPayload;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\NotFoundException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<OrderPayload>
 */
class OrderSubject implements Subject {

  const KEY = 'woocommerce:order';

  /** @var WooCommerce */
  private $woocommerce;

  /** @var OrderFieldsFactory */
  private $orderFieldsFactory;

  public function __construct(
    OrderFieldsFactory $orderFieldsFactory,
    WooCommerce $woocommerce
  ) {
    $this->woocommerce = $woocommerce;
    $this->orderFieldsFactory = $orderFieldsFactory;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce order', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'order_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['order_id'];
    $order = $this->woocommerce->wcGetOrder($id);
    if (!$order instanceof \WC_Order) {
      // translators: %d is the order ID.
      throw NotFoundException::create()->withMessage(sprintf(__("Order with ID '%d' not found.", 'mailpoet'), $id));
    }
    return new OrderPayload($order);
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getFields(): array {
    return $this->orderFieldsFactory->getFields();
  }
}
