<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class IntegerFilter extends NumberFilter {
  public function getFieldType(): string {
    return Field::TYPE_INTEGER;
  }

  public function getArgsSchema(string $condition): ObjectSchema {
    $paramsSchema = Builder::object([
      'in_the_last' => Builder::object([
        'number' => Builder::integer()->required()->minimum(1),
        'unit' => Builder::string()->required()->pattern('^(days)$')->default('days'),
      ]),
    ]);

    switch ($condition) {
      case self::CONDITION_BETWEEN:
      case self::CONDITION_NOT_BETWEEN:
        return Builder::object([
          'value' => Builder::array(Builder::integer())->minItems(2)->maxItems(2)->required(),
          'params' => $paramsSchema,
        ]);
      case self::CONDITION_IS_SET:
      case self::CONDITION_IS_NOT_SET:
        return Builder::object([
          'params' => $paramsSchema,
        ]);
      default:
        return Builder::object([
          'value' => Builder::integer()->required(),
          'params' => $paramsSchema,
        ]);
    }
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
    $matches = parent::matches($data, $value);
    if (!$matches) {
      return false;
    }

    if (isset($value) && !$this->isWholeNumber($value)) {
      return false;
    }

    $filterValue = $data->getArgs()['value'] ?? null;
    if (is_array($filterValue)) {
      foreach ($filterValue as $filterValueItem) {
        if (!$this->isWholeNumber($filterValueItem)) {
          return false;
        }
      }
      return true;
    }

    if (isset($filterValue) && !$this->isWholeNumber($filterValue)) {
      return false;
    }
    return true;
  }

  /** @param mixed $value */
  private function isWholeNumber($value): bool {
    return is_int($value) || (is_float($value) && $value === floor($value));
  }
}
