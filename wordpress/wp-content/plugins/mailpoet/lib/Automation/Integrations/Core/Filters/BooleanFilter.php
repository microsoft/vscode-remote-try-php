<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class BooleanFilter implements Filter {
  public const CONDITION_IS = 'is';

  public function getFieldType(): string {
    return Field::TYPE_BOOLEAN;
  }

  public function getConditions(): array {
    return [
      self::CONDITION_IS => __('is', 'mailpoet'),
    ];
  }

  public function getArgsSchema(string $condition): ObjectSchema {
    return Builder::object([
      'value' => Builder::boolean()->required(),
    ]);
  }

  public function getFieldParams(FilterData $data): array {
    return [];
  }

  public function matches(FilterData $data, $value): bool {
    $filterValue = $data->getArgs()['value'] ?? null;
    if (!is_bool($value) || !is_bool($filterValue)) {
      return false;
    }
    return $data->getCondition() === self::CONDITION_IS && $value === $filterValue;
  }
}
