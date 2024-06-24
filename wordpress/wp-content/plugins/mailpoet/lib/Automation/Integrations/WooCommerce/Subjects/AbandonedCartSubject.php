<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\AbandonedCartPayload;
use MailPoet\Automation\Integrations\WooCommerce\WooCommerce;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<AbandonedCartPayload>
 */
class AbandonedCartSubject implements Subject {
  const KEY = 'woocommerce:abandoned_cart';

  /** @var WooCommerce */
  private $woocommerceHelper;

  public function __construct(
    WooCommerce $woocommerceHelper
  ) {
    $this->woocommerceHelper = $woocommerceHelper;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce abandoned cart', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'user_id' => Builder::integer()->required(),
      'last_activity_at' => Builder::string()->required()->default(30),
      'product_ids' => Builder::array(Builder::integer())->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    if (!$this->woocommerceHelper->isWooCommerceActive()) {
      throw InvalidStateException::create()->withMessage('WooCommerce is not active');
    }
    $lastActivityAt = \DateTimeImmutable::createFromFormat(\DateTime::W3C, $subjectData->getArgs()['last_activity_at']);
    if (!$lastActivityAt) {
      throw InvalidStateException::create()->withMessage('Invalid abandoned cart time');
    }

    $customer = new \WC_Customer($subjectData->getArgs()['user_id']);

    return new AbandonedCartPayload($customer, $lastActivityAt, $subjectData->getArgs()['product_ids']);
  }

  public function getFields(): array {
    return [
      new Field(
        'woocommerce:cart:cart-total',
        Field::TYPE_NUMBER,
        __('Cart total', 'mailpoet'),
        function (AbandonedCartPayload $payload) {
          return $payload->getTotal();
        }
      ),
    ];
  }
}
