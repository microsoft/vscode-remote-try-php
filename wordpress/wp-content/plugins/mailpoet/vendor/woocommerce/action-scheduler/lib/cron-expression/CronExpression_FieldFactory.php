<?php
if (!defined('ABSPATH')) exit;
class CronExpression_FieldFactory
{
 private $fields = array();
 public function getField($position)
 {
 if (!isset($this->fields[$position])) {
 switch ($position) {
 case 0:
 $this->fields[$position] = new CronExpression_MinutesField();
 break;
 case 1:
 $this->fields[$position] = new CronExpression_HoursField();
 break;
 case 2:
 $this->fields[$position] = new CronExpression_DayOfMonthField();
 break;
 case 3:
 $this->fields[$position] = new CronExpression_MonthField();
 break;
 case 4:
 $this->fields[$position] = new CronExpression_DayOfWeekField();
 break;
 case 5:
 $this->fields[$position] = new CronExpression_YearField();
 break;
 default:
 throw new InvalidArgumentException(
 $position . ' is not a valid position'
 );
 }
 }
 return $this->fields[$position];
 }
}
