<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class StringFilter implements Filter {
  public const CONDITION_CONTAINS = 'contains';
  public const CONDITION_DOES_NOT_CONTAIN = 'does-not-contain';
  public const CONDITION_IS = 'is';
  public const CONDITION_IS_NOT = 'is-not';
  public const CONDITION_STARTS_WITH = 'starts-with';
  public const CONDITION_ENDS_WITH = 'ends-with';
  public const CONDITION_IS_BLANK = 'is-blank';
  public const CONDITION_IS_NOT_BLANK = 'is-not-blank';
  public const CONDITION_MATCHES_REGEX = 'matches-regex';

  public function getFieldType(): string {
    return Field::TYPE_STRING;
  }

  public function getConditions(): array {
    return [
      self::CONDITION_IS => __('is', 'mailpoet'),
      self::CONDITION_IS_NOT => __('is not', 'mailpoet'),
      self::CONDITION_CONTAINS => __('contains', 'mailpoet'),
      self::CONDITION_DOES_NOT_CONTAIN => __('does not contain', 'mailpoet'),
      self::CONDITION_STARTS_WITH => __('starts with', 'mailpoet'),
      self::CONDITION_ENDS_WITH => __('ends with', 'mailpoet'),
      self::CONDITION_IS_BLANK => __('is blank', 'mailpoet'),
      self::CONDITION_IS_NOT_BLANK => __('is not blank', 'mailpoet'),
      self::CONDITION_MATCHES_REGEX => __('matches regex', 'mailpoet'),
    ];
  }

  public function getArgsSchema(string $condition): ObjectSchema {
    switch ($condition) {
      case self::CONDITION_IS_BLANK:
      case self::CONDITION_IS_NOT_BLANK:
        return Builder::object([]);
      default:
        return Builder::object(['value' => Builder::string()->required()]);
    }
  }

  public function getFieldParams(FilterData $data): array {
    return [];
  }

  public function matches(FilterData $data, $value): bool {
    $filterValue = $data->getArgs()['value'] ?? null;
    if (!is_string($value) || !is_string($filterValue)) {
      return false;
    }

    // match regex as it is
    $condition = $data->getCondition();
    if ($condition === self::CONDITION_MATCHES_REGEX) {
      return $this->matchesRegex($filterValue, $value);
    }

    // match all other conditions case insensitively
    $value = mb_strtolower($value);
    $filterValue = mb_strtolower($filterValue);

    switch ($data->getCondition()) {
      case self::CONDITION_IS:
        return $value === $filterValue;
      case self::CONDITION_IS_NOT:
        return $value !== $filterValue;
      case self::CONDITION_CONTAINS:
        return str_contains($value, $filterValue);
      case self::CONDITION_DOES_NOT_CONTAIN:
        return !str_contains($value, $filterValue);
      case self::CONDITION_STARTS_WITH:
        return str_starts_with($value, $filterValue);
      case self::CONDITION_ENDS_WITH:
        return str_ends_with($value, $filterValue);
      case self::CONDITION_IS_BLANK:
        return strlen($value) === 0;
      case self::CONDITION_IS_NOT_BLANK:
        return strlen($value) > 0;
      default:
        return false;
    }
  }

  protected function matchesRegex(string $regex, string $value): bool {
    // add '/' delimiters, if missing
    if (!@preg_match('#^/.*/[a-z]*$#ui', $regex)) {
      $regex = '/' . str_replace('/', '\\/', $regex) . '/u';
    }

    // add unicode flag, if not present
    if (!@preg_match('#/.*u.*$#ui', $regex)) {
      $regex .= 'u';
    }

    if (@preg_match($regex, '') === false) {
      throw new InvalidStateException("Invalid regular expression: '$regex'");
    }

    return @preg_match($regex, $value) === 1;
  }
}
