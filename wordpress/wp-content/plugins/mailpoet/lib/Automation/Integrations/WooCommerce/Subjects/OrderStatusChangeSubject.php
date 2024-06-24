<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\OrderStatusChangePayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<OrderStatusChangePayload>
 */
class OrderStatusChangeSubject implements Subject {

  const KEY = 'woocommerce:order-status-changed';

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WooCommerce order status change', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'from' => Builder::string()->required(),
      'to' => Builder::string()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $from = $subjectData->getArgs()['from'];
    $to = $subjectData->getArgs()['to'];

    return new OrderStatusChangePayload($from, $to);
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getFields(): array {
    return [];
  }
}
