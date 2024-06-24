<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Exceptions\UnknownUnitException;
trait Boundaries
{
 public function startOfDay()
 {
 return $this->setTime(0, 0, 0, 0);
 }
 public function endOfDay()
 {
 return $this->setTime(static::HOURS_PER_DAY - 1, static::MINUTES_PER_HOUR - 1, static::SECONDS_PER_MINUTE - 1, static::MICROSECONDS_PER_SECOND - 1);
 }
 public function startOfMonth()
 {
 return $this->setDate($this->year, $this->month, 1)->startOfDay();
 }
 public function endOfMonth()
 {
 return $this->setDate($this->year, $this->month, $this->daysInMonth)->endOfDay();
 }
 public function startOfQuarter()
 {
 $month = ($this->quarter - 1) * static::MONTHS_PER_QUARTER + 1;
 return $this->setDate($this->year, $month, 1)->startOfDay();
 }
 public function endOfQuarter()
 {
 return $this->startOfQuarter()->addMonths(static::MONTHS_PER_QUARTER - 1)->endOfMonth();
 }
 public function startOfYear()
 {
 return $this->setDate($this->year, 1, 1)->startOfDay();
 }
 public function endOfYear()
 {
 return $this->setDate($this->year, 12, 31)->endOfDay();
 }
 public function startOfDecade()
 {
 $year = $this->year - $this->year % static::YEARS_PER_DECADE;
 return $this->setDate($year, 1, 1)->startOfDay();
 }
 public function endOfDecade()
 {
 $year = $this->year - $this->year % static::YEARS_PER_DECADE + static::YEARS_PER_DECADE - 1;
 return $this->setDate($year, 12, 31)->endOfDay();
 }
 public function startOfCentury()
 {
 $year = $this->year - ($this->year - 1) % static::YEARS_PER_CENTURY;
 return $this->setDate($year, 1, 1)->startOfDay();
 }
 public function endOfCentury()
 {
 $year = $this->year - 1 - ($this->year - 1) % static::YEARS_PER_CENTURY + static::YEARS_PER_CENTURY;
 return $this->setDate($year, 12, 31)->endOfDay();
 }
 public function startOfMillennium()
 {
 $year = $this->year - ($this->year - 1) % static::YEARS_PER_MILLENNIUM;
 return $this->setDate($year, 1, 1)->startOfDay();
 }
 public function endOfMillennium()
 {
 $year = $this->year - 1 - ($this->year - 1) % static::YEARS_PER_MILLENNIUM + static::YEARS_PER_MILLENNIUM;
 return $this->setDate($year, 12, 31)->endOfDay();
 }
 public function startOfWeek($weekStartsAt = null)
 {
 return $this->subDays((7 + $this->dayOfWeek - ($weekStartsAt ?? $this->firstWeekDay)) % 7)->startOfDay();
 }
 public function endOfWeek($weekEndsAt = null)
 {
 return $this->addDays((7 - $this->dayOfWeek + ($weekEndsAt ?? $this->lastWeekDay)) % 7)->endOfDay();
 }
 public function startOfHour()
 {
 return $this->setTime($this->hour, 0, 0, 0);
 }
 public function endOfHour()
 {
 return $this->setTime($this->hour, static::MINUTES_PER_HOUR - 1, static::SECONDS_PER_MINUTE - 1, static::MICROSECONDS_PER_SECOND - 1);
 }
 public function startOfMinute()
 {
 return $this->setTime($this->hour, $this->minute, 0, 0);
 }
 public function endOfMinute()
 {
 return $this->setTime($this->hour, $this->minute, static::SECONDS_PER_MINUTE - 1, static::MICROSECONDS_PER_SECOND - 1);
 }
 public function startOfSecond()
 {
 return $this->setTime($this->hour, $this->minute, $this->second, 0);
 }
 public function endOfSecond()
 {
 return $this->setTime($this->hour, $this->minute, $this->second, static::MICROSECONDS_PER_SECOND - 1);
 }
 public function startOf($unit, ...$params)
 {
 $ucfUnit = \ucfirst(static::singularUnit($unit));
 $method = "startOf{$ucfUnit}";
 if (!\method_exists($this, $method)) {
 throw new UnknownUnitException($unit);
 }
 return $this->{$method}(...$params);
 }
 public function endOf($unit, ...$params)
 {
 $ucfUnit = \ucfirst(static::singularUnit($unit));
 $method = "endOf{$ucfUnit}";
 if (!\method_exists($this, $method)) {
 throw new UnknownUnitException($unit);
 }
 return $this->{$method}(...$params);
 }
}
