<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_SimpleSchedule extends ActionScheduler_Abstract_Schedule {
 private $timestamp = NULL;
 public function calculate_next( DateTime $after ) {
 return null;
 }
 public function is_recurring() {
 return false;
 }
 public function __sleep() {
 $sleep_params = parent::__sleep();
 $this->timestamp = $this->scheduled_timestamp;
 return array_merge( $sleep_params, array(
 'timestamp',
 ) );
 }
 public function __wakeup() {
 if ( is_null( $this->scheduled_timestamp ) && ! is_null( $this->timestamp ) ) {
 $this->scheduled_timestamp = $this->timestamp;
 unset( $this->timestamp );
 }
 parent::__wakeup();
 }
}
