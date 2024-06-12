<?php
if (!defined('ABSPATH')) exit;
class CronExpression_DayOfMonthField extends CronExpression_AbstractField
{
 private static function getNearestWeekday($currentYear, $currentMonth, $targetDay)
 {
 $tday = str_pad($targetDay, 2, '0', STR_PAD_LEFT);
 $target = new DateTime("$currentYear-$currentMonth-$tday");
 $currentWeekday = (int) $target->format('N');
 if ($currentWeekday < 6) {
 return $target;
 }
 $lastDayOfMonth = $target->format('t');
 foreach (array(-1, 1, -2, 2) as $i) {
 $adjusted = $targetDay + $i;
 if ($adjusted > 0 && $adjusted <= $lastDayOfMonth) {
 $target->setDate($currentYear, $currentMonth, $adjusted);
 if ($target->format('N') < 6 && $target->format('m') == $currentMonth) {
 return $target;
 }
 }
 }
 }
 public function isSatisfiedBy(DateTime $date, $value)
 {
 // ? states that the field value is to be skipped
 if ($value == '?') {
 return true;
 }
 $fieldValue = $date->format('d');
 // Check to see if this is the last day of the month
 if ($value == 'L') {
 return $fieldValue == $date->format('t');
 }
 // Check to see if this is the nearest weekday to a particular value
 if (strpos($value, 'W')) {
 // Parse the target day
 $targetDay = substr($value, 0, strpos($value, 'W'));
 // Find out if the current day is the nearest day of the week
 return $date->format('j') == self::getNearestWeekday(
 $date->format('Y'),
 $date->format('m'),
 $targetDay
 )->format('j');
 }
 return $this->isSatisfied($date->format('d'), $value);
 }
 public function increment(DateTime $date, $invert = false)
 {
 if ($invert) {
 $date->modify('previous day');
 $date->setTime(23, 59);
 } else {
 $date->modify('next day');
 $date->setTime(0, 0);
 }
 return $this;
 }
 public function validate($value)
 {
 return (bool) preg_match('/[\*,\/\-\?LW0-9A-Za-z]+/', $value);
 }
}
