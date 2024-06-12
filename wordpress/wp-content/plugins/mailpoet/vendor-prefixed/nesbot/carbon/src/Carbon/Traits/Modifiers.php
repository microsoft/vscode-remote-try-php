<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\CarbonInterface;
use ReturnTypeWillChange;
trait Modifiers
{
 protected static $midDayAt = 12;
 public static function getMidDayAt()
 {
 return static::$midDayAt;
 }
 public static function setMidDayAt($hour)
 {
 static::$midDayAt = $hour;
 }
 public function midDay()
 {
 return $this->setTime(static::$midDayAt, 0, 0, 0);
 }
 public function next($modifier = null)
 {
 if ($modifier === null) {
 $modifier = $this->dayOfWeek;
 }
 return $this->change('next ' . (\is_string($modifier) ? $modifier : static::$days[$modifier]));
 }
 private function nextOrPreviousDay($weekday = \true, $forward = \true)
 {
 $date = $this;
 $step = $forward ? 1 : -1;
 do {
 $date = $date->addDays($step);
 } while ($weekday ? $date->isWeekend() : $date->isWeekday());
 return $date;
 }
 public function nextWeekday()
 {
 return $this->nextOrPreviousDay();
 }
 public function previousWeekday()
 {
 return $this->nextOrPreviousDay(\true, \false);
 }
 public function nextWeekendDay()
 {
 return $this->nextOrPreviousDay(\false);
 }
 public function previousWeekendDay()
 {
 return $this->nextOrPreviousDay(\false, \false);
 }
 public function previous($modifier = null)
 {
 if ($modifier === null) {
 $modifier = $this->dayOfWeek;
 }
 return $this->change('last ' . (\is_string($modifier) ? $modifier : static::$days[$modifier]));
 }
 public function firstOfMonth($dayOfWeek = null)
 {
 $date = $this->startOfDay();
 if ($dayOfWeek === null) {
 return $date->day(1);
 }
 return $date->modify('first ' . static::$days[$dayOfWeek] . ' of ' . $date->rawFormat('F') . ' ' . $date->year);
 }
 public function lastOfMonth($dayOfWeek = null)
 {
 $date = $this->startOfDay();
 if ($dayOfWeek === null) {
 return $date->day($date->daysInMonth);
 }
 return $date->modify('last ' . static::$days[$dayOfWeek] . ' of ' . $date->rawFormat('F') . ' ' . $date->year);
 }
 public function nthOfMonth($nth, $dayOfWeek)
 {
 $date = $this->avoidMutation()->firstOfMonth();
 $check = $date->rawFormat('Y-m');
 $date = $date->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);
 return $date->rawFormat('Y-m') === $check ? $this->modify((string) $date) : \false;
 }
 public function firstOfQuarter($dayOfWeek = null)
 {
 return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER - 2, 1)->firstOfMonth($dayOfWeek);
 }
 public function lastOfQuarter($dayOfWeek = null)
 {
 return $this->setDate($this->year, $this->quarter * static::MONTHS_PER_QUARTER, 1)->lastOfMonth($dayOfWeek);
 }
 public function nthOfQuarter($nth, $dayOfWeek)
 {
 $date = $this->avoidMutation()->day(1)->month($this->quarter * static::MONTHS_PER_QUARTER);
 $lastMonth = $date->month;
 $year = $date->year;
 $date = $date->firstOfQuarter()->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);
 return $lastMonth < $date->month || $year !== $date->year ? \false : $this->modify((string) $date);
 }
 public function firstOfYear($dayOfWeek = null)
 {
 return $this->month(1)->firstOfMonth($dayOfWeek);
 }
 public function lastOfYear($dayOfWeek = null)
 {
 return $this->month(static::MONTHS_PER_YEAR)->lastOfMonth($dayOfWeek);
 }
 public function nthOfYear($nth, $dayOfWeek)
 {
 $date = $this->avoidMutation()->firstOfYear()->modify('+' . $nth . ' ' . static::$days[$dayOfWeek]);
 return $this->year === $date->year ? $this->modify((string) $date) : \false;
 }
 public function average($date = null)
 {
 return $this->addRealMicroseconds((int) ($this->diffInRealMicroseconds($this->resolveCarbon($date), \false) / 2));
 }
 public function closest($date1, $date2)
 {
 return $this->diffInRealMicroseconds($date1) < $this->diffInRealMicroseconds($date2) ? $date1 : $date2;
 }
 public function farthest($date1, $date2)
 {
 return $this->diffInRealMicroseconds($date1) > $this->diffInRealMicroseconds($date2) ? $date1 : $date2;
 }
 public function min($date = null)
 {
 $date = $this->resolveCarbon($date);
 return $this->lt($date) ? $this : $date;
 }
 public function minimum($date = null)
 {
 return $this->min($date);
 }
 public function max($date = null)
 {
 $date = $this->resolveCarbon($date);
 return $this->gt($date) ? $this : $date;
 }
 public function maximum($date = null)
 {
 return $this->max($date);
 }
 #[\ReturnTypeWillChange]
 public function modify($modify)
 {
 return parent::modify((string) $modify);
 }
 public function change($modifier)
 {
 return $this->modify(\preg_replace_callback('/^(next|previous|last)\\s+(\\d{1,2}(h|am|pm|:\\d{1,2}(:\\d{1,2})?))$/i', function ($match) {
 $match[2] = \str_replace('h', ':00', $match[2]);
 $test = $this->avoidMutation()->modify($match[2]);
 $method = $match[1] === 'next' ? 'lt' : 'gt';
 $match[1] = $test->{$method}($this) ? $match[1] . ' day' : 'today';
 return $match[1] . ' ' . $match[2];
 }, \strtr(\trim($modifier), [' at ' => ' ', 'just now' => 'now', 'after tomorrow' => 'tomorrow +1 day', 'before yesterday' => 'yesterday -1 day'])));
 }
}
