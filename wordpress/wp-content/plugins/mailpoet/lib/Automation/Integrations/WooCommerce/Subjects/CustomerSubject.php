<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\WooCommerce\Fields\CustomerFieldsFactory;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\CustomerPayload;
use MailPoet\NotFoundException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use WC_Customer;
use WC_Order;

/**
 * @implements Subject<CustomerPayload>
 */
class CustomerSubject implements Subject {
  const KEY = 'woocommerce:customer';

  /** @var CustomerFieldsFactory */
  private $customerFieldsFactory;

  public function __construct(
    CustomerFieldsFactory $customerFieldsFactory
  ) {
    $this->customerFieldsFactory = $customerFieldsFactory;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce customer', 'mailpoet');
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'customer_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $args = $subjectData->getArgs();
    $customerId = isset($args['customer_id']) ? (int)$args['customer_id'] : null;
    $orderId = isset($args['order_id']) ? (int)$args['order_id'] : null;

    $order = $orderId === null ? null : wc_get_order($orderId);
    $order = $order instanceof WC_Order ? $order : null;

    if (!$customerId) {
      return new CustomerPayload(null, $order);
    }

    $customer = new WC_Customer($customerId);
    if (!$customer->get_id()) {
      // translators: %d is the ID of the customer.
      throw NotFoundException::create()->withMessage(sprintf(__("Customer with ID '%d' not found.", 'mailpoet'), $customerId));
    }

    return new CustomerPayload($customer, $order);
  }

  /** @return Field[] */
  public function getFields(): array {
    return $this->customerFieldsFactory->getFields();
  }
}
