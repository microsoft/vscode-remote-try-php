<?php
if (!defined('ABSPATH')) exit;
class CronExpression
{
 const MINUTE = 0;
 const HOUR = 1;
 const DAY = 2;
 const MONTH = 3;
 const WEEKDAY = 4;
 const YEAR = 5;
 private $cronParts;
 private $fieldFactory;
 private static $order = array(self::YEAR, self::MONTH, self::DAY, self::WEEKDAY, self::HOUR, self::MINUTE);
 public static function factory($expression, CronExpression_FieldFactory $fieldFactory = null)
 {
 $mappings = array(
 '@yearly' => '0 0 1 1 *',
 '@annually' => '0 0 1 1 *',
 '@monthly' => '0 0 1 * *',
 '@weekly' => '0 0 * * 0',
 '@daily' => '0 0 * * *',
 '@hourly' => '0 * * * *'
 );
 if (isset($mappings[$expression])) {
 $expression = $mappings[$expression];
 }
 return new self($expression, $fieldFactory ? $fieldFactory : new CronExpression_FieldFactory());
 }
 public function __construct($expression, CronExpression_FieldFactory $fieldFactory)
 {
 $this->fieldFactory = $fieldFactory;
 $this->setExpression($expression);
 }
 public function setExpression($value)
 {
 $this->cronParts = preg_split('/\s/', $value, -1, PREG_SPLIT_NO_EMPTY);
 if (count($this->cronParts) < 5) {
 throw new InvalidArgumentException(
 $value . ' is not a valid CRON expression'
 );
 }
 foreach ($this->cronParts as $position => $part) {
 $this->setPart($position, $part);
 }
 return $this;
 }
 public function setPart($position, $value)
 {
 if (!$this->fieldFactory->getField($position)->validate($value)) {
 throw new InvalidArgumentException(
 'Invalid CRON field value ' . $value . ' as position ' . $position
 );
 }
 $this->cronParts[$position] = $value;
 return $this;
 }
 public function getNextRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
 {
 return $this->getRunDate($currentTime, $nth, false, $allowCurrentDate);
 }
 public function getPreviousRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
 {
 return $this->getRunDate($currentTime, $nth, true, $allowCurrentDate);
 }
 public function getMultipleRunDates($total, $currentTime = 'now', $invert = false, $allowCurrentDate = false)
 {
 $matches = array();
 for ($i = 0; $i < max(0, $total); $i++) {
 $matches[] = $this->getRunDate($currentTime, $i, $invert, $allowCurrentDate);
 }
 return $matches;
 }
 public function getExpression($part = null)
 {
 if (null === $part) {
 return implode(' ', $this->cronParts);
 } elseif (array_key_exists($part, $this->cronParts)) {
 return $this->cronParts[$part];
 }
 return null;
 }
 public function __toString()
 {
 return $this->getExpression();
 }
 public function isDue($currentTime = 'now')
 {
 if ('now' === $currentTime) {
 $currentDate = date('Y-m-d H:i');
 $currentTime = strtotime($currentDate);
 } elseif ($currentTime instanceof DateTime) {
 $currentDate = $currentTime->format('Y-m-d H:i');
 $currentTime = strtotime($currentDate);
 } else {
 $currentTime = new DateTime($currentTime);
 $currentTime->setTime($currentTime->format('H'), $currentTime->format('i'), 0);
 $currentDate = $currentTime->format('Y-m-d H:i');
 $currentTime = (int)($currentTime->getTimestamp());
 }
 return $this->getNextRunDate($currentDate, 0, true)->getTimestamp() == $currentTime;
 }
 protected function getRunDate($currentTime = null, $nth = 0, $invert = false, $allowCurrentDate = false)
 {
 if ($currentTime instanceof DateTime) {
 $currentDate = $currentTime;
 } else {
 $currentDate = new DateTime($currentTime ? $currentTime : 'now');
 $currentDate->setTimezone(new DateTimeZone(date_default_timezone_get()));
 }
 $currentDate->setTime($currentDate->format('H'), $currentDate->format('i'), 0);
 $nextRun = clone $currentDate;
 $nth = (int) $nth;
 // Set a hard limit to bail on an impossible date
 for ($i = 0; $i < 1000; $i++) {
 foreach (self::$order as $position) {
 $part = $this->getExpression($position);
 if (null === $part) {
 continue;
 }
 $satisfied = false;
 // Get the field object used to validate this part
 $field = $this->fieldFactory->getField($position);
 // Check if this is singular or a list
 if (strpos($part, ',') === false) {
 $satisfied = $field->isSatisfiedBy($nextRun, $part);
 } else {
 foreach (array_map('trim', explode(',', $part)) as $listPart) {
 if ($field->isSatisfiedBy($nextRun, $listPart)) {
 $satisfied = true;
 break;
 }
 }
 }
 // If the field is not satisfied, then start over
 if (!$satisfied) {
 $field->increment($nextRun, $invert);
 continue 2;
 }
 }
 // Skip this match if needed
 if ((!$allowCurrentDate && $nextRun == $currentDate) || --$nth > -1) {
 $this->fieldFactory->getField(0)->increment($nextRun, $invert);
 continue;
 }
 return $nextRun;
 }
 // @codeCoverageIgnoreStart
 throw new RuntimeException('Impossible CRON expression');
 // @codeCoverageIgnoreEnd
 }
}
