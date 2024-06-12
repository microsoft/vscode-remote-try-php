<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments\Filters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoetVendor\Carbon\CarbonImmutable;

class DateFilterHelper {
  const BEFORE = 'before';
  const AFTER = 'after';
  const ON = 'on';
  const ON_OR_BEFORE = 'onOrBefore';
  const ON_OR_AFTER = 'onOrAfter';
  const NOT_ON = 'notOn';
  const IN_THE_LAST = 'inTheLast';
  const NOT_IN_THE_LAST = 'notInTheLast';

  public function getValidOperators(): array {
    return array_merge(
      $this->getAbsoluteDateOperators(),
      $this->getRelativeDateOperators()
    );
  }

  public function getAbsoluteDateOperators(): array {
    return [
      self::BEFORE,
      self::AFTER,
      self::ON,
      self::ON_OR_BEFORE,
      self::ON_OR_AFTER,
      self::NOT_ON,
    ];
  }

  public function getRelativeDateOperators(): array {
    return [
      self::IN_THE_LAST,
      self::NOT_IN_THE_LAST,
    ];
  }

  public function getDateStringForOperator(string $operator, string $value): string {
    if (in_array($operator, self::getAbsoluteDateOperators())) {
      $carbon = CarbonImmutable::createFromFormat('Y-m-d', $value);
      if (!$carbon instanceof CarbonImmutable) {
        throw new InvalidFilterException('Invalid date value', InvalidFilterException::INVALID_DATE_VALUE);
      }
    } else if (in_array($operator, self::getRelativeDateOperators())) {
      $carbon = CarbonImmutable::now()->subDays(intval($value) - 1);
    } else {
      throw new InvalidFilterException('Incorrect value for operator', InvalidFilterException::MISSING_VALUE);
    }

    return $carbon->toDateString();
  }

  public function getDateValueFromFilter(DynamicSegmentFilterEntity $filter): string {
    $filterData = $filter->getFilterData();
    $dateValue = $filterData->getParam('value');
    if (!is_string($dateValue)) {
      throw new InvalidFilterException('Incorrect value for date', InvalidFilterException::INVALID_DATE_VALUE);
    }
    return $dateValue;
  }

  public function getOperatorFromFilter(DynamicSegmentFilterEntity $filter): string {
    $filterData = $filter->getFilterData();
    $operator = $filterData->getParam('operator');
    if (!is_string($operator) || !in_array($operator, $this->getValidOperators())) {
      throw new InvalidFilterException('Incorrect value for operator', InvalidFilterException::MISSING_VALUE);
    }
    return $operator;
  }
}
