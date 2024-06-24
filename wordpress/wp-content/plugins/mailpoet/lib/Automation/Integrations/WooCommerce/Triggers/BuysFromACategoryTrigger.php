<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Integrations\Core\Filters\EnumArrayFilter;
use MailPoet\Automation\Integrations\Core\Filters\EnumFilter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class BuysFromACategoryTrigger extends BuysAProductTrigger {
  const KEY = 'woocommerce:buys-from-a-category';

  public function getKey(): string {
    return self::KEY;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'category_ids' => Builder::array(
        Builder::integer()
      )->minItems(1)->required(),
      'to' => Builder::string()->required()->default('wc-completed'),
    ]);
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Customer buys from a category', 'mailpoet');
  }

  protected function getFilters(StepRunArgs $args): array {
    $triggerArgs = $args->getStep()->getArgs();
    $filters = [
      Filter::fromArray([
        'id' => '',
        'field_type' => Field::TYPE_ENUM_ARRAY,
        'field_key' => 'woocommerce:order:categories',
        'condition' => EnumArrayFilter::CONDITION_MATCHES_ANY_OF,
        'args' => [
          'value' => $triggerArgs['category_ids'] ?? [],
        ],
      ]),
    ];
    $status = str_replace('wc-', '', $triggerArgs['to'] ?? 'completed');
    if ($status === 'any') {
      return $filters;
    }

    $filters[] = Filter::fromArray([
      'id' => '',
      'field_type' => Field::TYPE_ENUM,
      'field_key' => 'woocommerce:order:status',
      'condition' => EnumFilter::IS_ANY_OF,
      'args' => [
        'value' => [$status],
      ],
    ]);
    return $filters;
  }
}
