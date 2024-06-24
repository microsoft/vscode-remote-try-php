<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\Core\Filters;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter as FilterData;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Integration\Filter;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class DateTimeFilter implements Filter {
  public const CONDITION_BEFORE = 'before';
  public const CONDITION_AFTER = 'after';
  public const CONDITION_ON = 'on';
  public const CONDITION_NOT_ON = 'not-on';
  public const CONDITION_IN_THE_LAST = 'in-the-last';
  public const CONDITION_NOT_IN_THE_LAST = 'not-in-the-last';
  public const CONDITION_IS_SET = 'is-set';
  public const CONDITION_IS_NOT_SET = 'is-not-set';
  public const CONDITION_ON_THE_DAYS_OF_THE_WEEK = 'on-the-days-of-the-week';

  public const FORMAT_DATETIME = 'Y-m-d\TH:i:s';
  public const FORMAT_DATE = 'Y-m-d';

  public const REGEX_DATETIME = '^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$';
  public const REGEX_DATE = '^\d{4}-\d{2}-\d{2}$';

  /** @var DateTimeZone */
  private $localTimezone;

  public function __construct(
    DateTimeZone $localTimezone
  ) {
    $this->localTimezone = $localTimezone;
  }

  public function getFieldType(): string {
    return Field::TYPE_DATETIME;
  }

  public function getConditions(): array {
    return [
      self::CONDITION_BEFORE => __('before', 'mailpoet'),
      self::CONDITION_AFTER => __('after', 'mailpoet'),
      self::CONDITION_ON => __('on', 'mailpoet'),
      self::CONDITION_NOT_ON => __('not on', 'mailpoet'),
      self::CONDITION_IN_THE_LAST => __('in the last', 'mailpoet'),
      self::CONDITION_NOT_IN_THE_LAST => __('not in the last', 'mailpoet'),
      self::CONDITION_IS_SET => __('is set', 'mailpoet'),
      self::CONDITION_IS_NOT_SET => __('is not set', 'mailpoet'),
      self::CONDITION_ON_THE_DAYS_OF_THE_WEEK => __('on the day(s) of the week', 'mailpoet'),
    ];
  }

  public function getArgsSchema(string $condition): ObjectSchema {
    switch ($condition) {
      case self::CONDITION_BEFORE:
      case self::CONDITION_AFTER:
        return Builder::object([
          'value' => Builder::string()->pattern(self::REGEX_DATETIME)->required(),
        ]);
      case self::CONDITION_ON:
      case self::CONDITION_NOT_ON:
        return Builder::object([
          'value' => Builder::string()->pattern(self::REGEX_DATE)->required(),
        ]);
      case self::CONDITION_IN_THE_LAST:
      case self::CONDITION_NOT_IN_THE_LAST:
        return Builder::object([
          'value' => Builder::object([
            'number' => Builder::integer()->minimum(1)->required(),
            'unit' => Builder::string()->pattern('^days|weeks|months$')->required(),
          ])->required(),
        ]);
      case self::CONDITION_IS_SET:
      case self::CONDITION_IS_NOT_SET:
        return Builder::object([]);
      case self::CONDITION_ON_THE_DAYS_OF_THE_WEEK:
        return Builder::object([
          'value' => Builder::array(Builder::integer()->minimum(0)->maximum(6))->minItems(1)->required(),
        ]);
      default:
        throw new InvalidStateException();
    }
  }

  public function getFieldParams(FilterData $data): array {
    return [];
  }

  public function matches(FilterData $data, $value): bool {
    $filterValue = $data->getArgs()['value'] ?? null;
    $condition = $data->getCondition();

    // is set/is not set
    if (in_array($condition, [self::CONDITION_IS_SET, self::CONDITION_IS_NOT_SET], true)) {
      return $this->matchesSet($condition, $value);
    }

    // in the last/not in the last
    if (in_array($condition, [self::CONDITION_IN_THE_LAST, self::CONDITION_NOT_IN_THE_LAST], true)) {
      return $this->matchesInTheLast($condition, $filterValue, $value);
    }

    // on the day(s) of the week
    if ($condition === self::CONDITION_ON_THE_DAYS_OF_THE_WEEK) {
      return $this->matchesOnTheDaysOfTheWeek($filterValue, $value);
    }

    // other conditions
    if (!is_string($filterValue) || !$value instanceof DateTimeInterface) {
      return false;
    }

    $datetime = $this->convertToLocalTimezone($value);
    switch ($condition) {
      case 'before':
        $ref = DateTimeImmutable::createFromFormat(self::FORMAT_DATETIME, $filterValue, $this->localTimezone);
        return $ref && $datetime < $ref;
      case 'after':
        $ref = DateTimeImmutable::createFromFormat(self::FORMAT_DATETIME, $filterValue, $this->localTimezone);
        return $ref && $datetime > $ref;
      case 'on':
        return $datetime->format(self::FORMAT_DATE) === $filterValue;
      case 'not-on':
        return $datetime->format(self::FORMAT_DATE) !== $filterValue;
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

  /**
   * @param mixed $filterValue
   * @param mixed $value
   */
  private function matchesInTheLast(string $condition, $filterValue, $value): bool {
    if (!is_array($filterValue) || !isset($filterValue['number']) || !isset($filterValue['unit']) || !$value instanceof DateTimeInterface) {
      return false;
    }

    $number = $filterValue['number'];
    $unit = $filterValue['unit'];
    if (!is_integer($number) || !in_array($unit, ['days', 'weeks', 'months'], true)) {
      return false;
    }

    $now = new DateTimeImmutable('now', $this->localTimezone);
    $ref = $now->modify("-$number $unit");
    $matches = $ref <= $value && $value <= $now;
    return $condition === self::CONDITION_IN_THE_LAST ? $matches : !$matches;
  }

  /**
   * @param mixed $filterValue
   * @param mixed $value
   */
  private function matchesOnTheDaysOfTheWeek($filterValue, $value): bool {
    if (!is_array($filterValue) || !$value instanceof DateTimeInterface) {
      return false;
    }
    foreach ($filterValue as $day) {
      if (!is_integer($day) || $day < 0 || $day > 6) {
        return false;
      }
    }

    $date = $this->convertToLocalTimezone($value);
    $day = (int)$date->format('w');
    return in_array($day, $filterValue, true);
  }

  private function convertToLocalTimezone(DateTimeInterface $datetime): DateTimeImmutable {
    $value = DateTimeImmutable::createFromFormat('U', (string)$datetime->getTimestamp(), $this->localTimezone);
    if (!$value) {
      throw new InvalidStateException('Failed to convert datetime to WP timezone');
    }
    return $value;
  }
}
