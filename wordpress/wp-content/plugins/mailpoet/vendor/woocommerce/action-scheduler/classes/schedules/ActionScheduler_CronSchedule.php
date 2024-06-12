<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_CronSchedule extends ActionScheduler_Abstract_RecurringSchedule implements ActionScheduler_Schedule {
 private $start_timestamp = NULL;
 private $cron = NULL;
 public function __construct( DateTime $start, $recurrence, DateTime $first = null ) {
 if ( ! is_a( $recurrence, 'CronExpression' ) ) {
 $recurrence = CronExpression::factory( $recurrence );
 }
 // For backward compatibility, we need to make sure the date is set to the first matching cron date, not whatever date is passed in. Importantly, by passing true as the 3rd param, if $start matches the cron expression, then it will be used. This was previously handled in the now deprecated next() method.
 $date = $recurrence->getNextRunDate( $start, 0, true );
 // parent::__construct() will set this to $date by default, but that may be different to $start now.
 $first = empty( $first ) ? $start : $first;
 parent::__construct( $date, $recurrence, $first );
 }
 protected function calculate_next( DateTime $after ) {
 return $this->recurrence->getNextRunDate( $after, 0, false );
 }
 public function get_recurrence() {
 return strval( $this->recurrence );
 }
 public function __sleep() {
 $sleep_params = parent::__sleep();
 $this->start_timestamp = $this->scheduled_timestamp;
 $this->cron = $this->recurrence;
 return array_merge( $sleep_params, array(
 'start_timestamp',
 'cron'
 ) );
 }
 public function __wakeup() {
 if ( is_null( $this->scheduled_timestamp ) && ! is_null( $this->start_timestamp ) ) {
 $this->scheduled_timestamp = $this->start_timestamp;
 unset( $this->start_timestamp );
 }
 if ( is_null( $this->recurrence ) && ! is_null( $this->cron ) ) {
 $this->recurrence = $this->cron;
 unset( $this->cron );
 }
 parent::__wakeup();
 }
}
