<?php
namespace Cron;
if (!defined('ABSPATH')) exit;
use InvalidArgumentException;
class FieldFactory
{
 private $fields = array();
 public function getField($position)
 {
 if (!isset($this->fields[$position])) {
 switch ($position) {
 case 0:
 $this->fields[$position] = new MinutesField();
 break;
 case 1:
 $this->fields[$position] = new HoursField();
 break;
 case 2:
 $this->fields[$position] = new DayOfMonthField();
 break;
 case 3:
 $this->fields[$position] = new MonthField();
 break;
 case 4:
 $this->fields[$position] = new DayOfWeekField();
 break;
 case 5:
 $this->fields[$position] = new YearField();
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
