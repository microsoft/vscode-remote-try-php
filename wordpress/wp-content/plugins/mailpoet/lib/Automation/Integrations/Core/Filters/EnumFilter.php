<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class EnumFilter implements Filter {
  public const IS_ANY_OF = 'is-any-of';
  public const IS_NONE_OF = 'is-none-of';

  public function getFieldType(): string {
    return Field::TYPE_ENUM;
  }

  public function getConditions(): array {
    return [
      self::IS_ANY_OF => __('is any of', 'mailpoet'),
      self::IS_NONE_OF => __('is none of', 'mailpoet'),
    ];
  }

  public function getArgsSchema(string $condition): ObjectSchema {
    $paramsSchema = Builder::object([
      'in_the_last' => Builder::object([
        'number' => Builder::integer()->required()->minimum(1),
        'unit' => Builder::string()->required()->pattern('^(days)$')->default('days'),
      ]),
    ]);

    return Builder::object([
      'value' => Builder::oneOf([
        Builder::array(Builder::string())->minItems(1),
        Builder::array(Builder::integer())->minItems(1),
      ])->required(),
      'params' => $paramsSchema,
    ]);
  }

  public function getFieldParams(FilterData $data): array {
    $paramData = $data->getArgs()['params'] ?? [];
    $params = [];

    $inTheLastUnit = $paramData['in_the_last']['unit'] ?? null;
    $inTheLastNumber = $paramData['in_the_last']['number'] ?? null;
    if ($inTheLastUnit === 'days' && $inTheLastNumber !== null) {
      $params['in_the_last'] = $inTheLastNumber * DAY_IN_SECONDS;
    }

    return $params;
  }

  public function matches(FilterData $data, $value): bool {
    $filterValue = $data->getArgs()['value'] ?? null;
    if (!is_scalar($value) || !is_array($filterValue)) {
      return false;
    }

    $filterValue = array_unique($filterValue, SORT_REGULAR);
    switch ($data->getCondition()) {
      case self::IS_ANY_OF:
        return in_array($value, $filterValue, true);
      case self::IS_NONE_OF:
        return !in_array($value, $filterValue, true);
      default:
        return false;
    }
  }
}
