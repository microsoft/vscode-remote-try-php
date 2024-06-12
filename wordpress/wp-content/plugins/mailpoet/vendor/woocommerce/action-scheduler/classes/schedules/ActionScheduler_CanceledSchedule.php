<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_CanceledSchedule extends ActionScheduler_SimpleSchedule {
 private $timestamp = NULL;
 public function calculate_next( DateTime $after ) {
 return null;
 }
 public function get_next( DateTime $after ) {
 return null;
 }
 public function is_recurring() {
 return false;
 }
 public function __wakeup() {
 if ( ! is_null( $this->timestamp ) ) {
 $this->scheduled_timestamp = $this->timestamp;
 unset( $this->timestamp );
 }
 parent::__wakeup();
 }
}
