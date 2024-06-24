<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class EnumArrayFilter implements Filter {
  public const CONDITION_MATCHES_ANY_OF = 'matches-any-of';
  public const CONDITION_MATCHES_ALL_OF = 'matches-all-of';
  public const CONDITION_MATCHES_NONE_OF = 'matches-none-of';

  public function getFieldType(): string {
    return Field::TYPE_ENUM_ARRAY;
  }

  public function getConditions(): array {
    return [
      self::CONDITION_MATCHES_ANY_OF => __('matches any of', 'mailpoet'),
      self::CONDITION_MATCHES_ALL_OF => __('matches all of', 'mailpoet'),
      self::CONDITION_MATCHES_NONE_OF => __('matches none of', 'mailpoet'),
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
    if (!is_array($value) || !is_array($filterValue)) {
      return false;
    }

    $filterValue = array_unique($filterValue, SORT_REGULAR);
    $value = array_unique($value, SORT_REGULAR);

    $filterCount = count($filterValue);
    $matchedCount = count(array_intersect($value, $filterValue));
    switch ($data->getCondition()) {
      case self::CONDITION_MATCHES_ANY_OF:
        return $filterCount > 0 && $matchedCount > 0;
      case self::CONDITION_MATCHES_ALL_OF:
        return $filterCount > 0 && $matchedCount === count($filterValue);
      case self::CONDITION_MATCHES_NONE_OF:
        return $matchedCount === 0;
      default:
        return false;
    }
  }
}
