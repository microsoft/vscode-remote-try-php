<?php
if (!defined('ABSPATH')) exit;
abstract class CronExpression_AbstractField implements CronExpression_FieldInterface
{
 public function isSatisfied($dateValue, $value)
 {
 if ($this->isIncrementsOfRanges($value)) {
 return $this->isInIncrementsOfRanges($dateValue, $value);
 } elseif ($this->isRange($value)) {
 return $this->isInRange($dateValue, $value);
 }
 return $value == '*' || $dateValue == $value;
 }
 public function isRange($value)
 {
 return strpos($value, '-') !== false;
 }
 public function isIncrementsOfRanges($value)
 {
 return strpos($value, '/') !== false;
 }
 public function isInRange($dateValue, $value)
 {
 $parts = array_map('trim', explode('-', $value, 2));
 return $dateValue >= $parts[0] && $dateValue <= $parts[1];
 }
 public function isInIncrementsOfRanges($dateValue, $value)
 {
 $parts = array_map('trim', explode('/', $value, 2));
 $stepSize = isset($parts[1]) ? $parts[1] : 0;
 if ($parts[0] == '*' || $parts[0] === '0') {
 return (int) $dateValue % $stepSize == 0;
 }
 $range = explode('-', $parts[0], 2);
 $offset = $range[0];
 $to = isset($range[1]) ? $range[1] : $dateValue;
 // Ensure that the date value is within the range
 if ($dateValue < $offset || $dateValue > $to) {
 return false;
 }
 for ($i = $offset; $i <= $to; $i+= $stepSize) {
 if ($i == $dateValue) {
 return true;
 }
 }
 return false;
 }
}
