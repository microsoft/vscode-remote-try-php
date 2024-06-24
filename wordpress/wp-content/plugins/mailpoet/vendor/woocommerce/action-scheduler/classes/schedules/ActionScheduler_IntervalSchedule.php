<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_IntervalSchedule extends ActionScheduler_Abstract_RecurringSchedule implements ActionScheduler_Schedule {
 private $start_timestamp = NULL;
 private $interval_in_seconds = NULL;
 protected function calculate_next( DateTime $after ) {
 $after->modify( '+' . (int) $this->get_recurrence() . ' seconds' );
 return $after;
 }
 public function interval_in_seconds() {
 _deprecated_function( __METHOD__, '3.0.0', '(int)ActionScheduler_Abstract_RecurringSchedule::get_recurrence()' );
 return (int) $this->get_recurrence();
 }
 public function __sleep() {
 $sleep_params = parent::__sleep();
 $this->start_timestamp = $this->scheduled_timestamp;
 $this->interval_in_seconds = $this->recurrence;
 return array_merge( $sleep_params, array(
 'start_timestamp',
 'interval_in_seconds'
 ) );
 }
 public function __wakeup() {
 if ( is_null( $this->scheduled_timestamp ) && ! is_null( $this->start_timestamp ) ) {
 $this->scheduled_timestamp = $this->start_timestamp;
 unset( $this->start_timestamp );
 }
 if ( is_null( $this->recurrence ) && ! is_null( $this->interval_in_seconds ) ) {
 $this->recurrence = $this->interval_in_seconds;
 unset( $this->interval_in_seconds );
 }
 parent::__wakeup();
 }
}
