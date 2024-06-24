<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class NumberFilter implements Filter {
  public const CONDITION_EQUALS = 'equals';
  public const CONDITION_NOT_EQUAL = 'not-equal';
  public const CONDITION_GREATER_THAN = 'greater-than';
  public const CONDITION_LESS_THAN = 'less-than';
  public const CONDITION_BETWEEN = 'between';
  public const CONDITION_NOT_BETWEEN = 'not-between';
  public const CONDITION_IS_MULTIPLE_OF = 'is-multiple-of';
  public const CONDITION_IS_NOT_MULTIPLE_OF = 'is-not-multiple-of';
  public const CONDITION_IS_SET = 'is-set';
  public const CONDITION_IS_NOT_SET = 'is-not-set';

  public function getFieldType(): string {
    return Field::TYPE_NUMBER;
  }

  public function getConditions(): array {
    return [
      self::CONDITION_EQUALS => __('equals', 'mailpoet'),
      self::CONDITION_NOT_EQUAL => __('not equal', 'mailpoet'),
      self::CONDITION_GREATER_THAN => __('greater than', 'mailpoet'),
      self::CONDITION_LESS_THAN => __('less than', 'mailpoet'),
      self::CONDITION_BETWEEN => __('between', 'mailpoet'),
      self::CONDITION_NOT_BETWEEN => __('not between', 'mailpoet'),
      self::CONDITION_IS_MULTIPLE_OF => __('is multiple of', 'mailpoet'),
      self::CONDITION_IS_NOT_MULTIPLE_OF => __('is not multiple of', 'mailpoet'),
      self::CONDITION_IS_SET => __('is set', 'mailpoet'),
      self::CONDITION_IS_NOT_SET => __('is not set', 'mailpoet'),
    ];
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
          'value' => Builder::array(Builder::number())->minItems(2)->maxItems(2)->required(),
          'params' => $paramsSchema,
        ]);
      case self::CONDITION_IS_SET:
      case self::CONDITION_IS_NOT_SET:
        return Builder::object([
          'params' => $paramsSchema,
        ]);
      default:
        return Builder::object([
          'value' => Builder::number()->required(),
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

  /**
   * @param float $value
   */
  public function matches(FilterData $data, $value): bool {
    $filterValue = $data->getArgs()['value'] ?? null;
    $condition = $data->getCondition();

    // is between/not between
    if (in_array($condition, [self::CONDITION_BETWEEN, self::CONDITION_NOT_BETWEEN], true)) {
      return $this->matchesBetween($condition, $value, $filterValue);
    }

    // is set/is not set
    if (in_array($condition, [self::CONDITION_IS_SET, self::CONDITION_IS_NOT_SET], true)) {
      return $this->matchesSet($condition, $value);
    }

    if (!$this->isNumber($value) || !$this->isNumber($filterValue)) {
      return false;
    }

    $value = floatval($value);
    $filterValue = floatval($filterValue);

    switch ($condition) {
      case self::CONDITION_EQUALS:
        return $value === $filterValue;
      case self::CONDITION_NOT_EQUAL:
        return $value !== $filterValue;
      case self::CONDITION_GREATER_THAN:
        return $value > $filterValue;
      case self::CONDITION_LESS_THAN:
        return $value < $filterValue;
      case self::CONDITION_IS_MULTIPLE_OF:
        return fmod($value, $filterValue) === 0.0;
      case self::CONDITION_IS_NOT_MULTIPLE_OF:
        return fmod($value, $filterValue) !== 0.0;
      default:
        return false;
    }
  }

  /**
   * @param float|null $value
   * @param mixed $filterValue
   */
  private function matchesBetween(string $condition, $value, $filterValue): bool {
    if (!is_array($filterValue) || count($filterValue) !== 2) {
      return false;
    }

    if (!$this->isNumber($filterValue[0]) || !$this->isNumber($filterValue[1]) || $filterValue[0] >= $filterValue[1]) {
      return false;
    }

    if (!$this->isNumber($value)) {
      return false;
    }

    /** @var float $value */
    $value = floatval($value);
    $from = floatval($filterValue[0]);
    $to = floatval($filterValue[1]);

    switch ($condition) {
      case self::CONDITION_BETWEEN:
        return $value > $from && $value < $to;
      case self::CONDITION_NOT_BETWEEN:
        return $value <= $from || $value >= $to;
      default:
        return false;
    }
  }

  /** @param mixed $value */
  private function matchesSet(string $condition, $value): bool {
    switch ($condition) {
      case self::CONDITION_IS_SET:
        return $value !== null;
      case self::CONDITION_IS_NOT_SET:
        return $value === null;
      default:
        return false;
    }
  }

  /** @param mixed $value */
  private function isNumber($value): bool {
    return is_integer($value) || is_float($value);
  }
}
