<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Abstract_RecurringSchedule extends ActionScheduler_Abstract_Schedule {
 private $first_date = NULL;
 protected $first_timestamp = NULL;
 protected $recurrence;
 public function __construct( DateTime $date, $recurrence, DateTime $first = null ) {
 parent::__construct( $date );
 $this->first_date = empty( $first ) ? $date : $first;
 $this->recurrence = $recurrence;
 }
 public function is_recurring() {
 return true;
 }
 public function get_first_date() {
 return clone $this->first_date;
 }
 public function get_recurrence() {
 return $this->recurrence;
 }
 public function __sleep() {
 $sleep_params = parent::__sleep();
 $this->first_timestamp = $this->first_date->getTimestamp();
 return array_merge( $sleep_params, array(
 'first_timestamp',
 'recurrence'
 ) );
 }
 public function __wakeup() {
 parent::__wakeup();
 if ( $this->first_timestamp > 0 ) {
 $this->first_date = as_get_datetime_object( $this->first_timestamp );
 } else {
 $this->first_date = $this->get_date();
 }
 }
}
