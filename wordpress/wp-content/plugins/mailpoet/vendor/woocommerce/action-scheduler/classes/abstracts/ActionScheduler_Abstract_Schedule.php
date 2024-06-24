<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Abstract_Schedule extends ActionScheduler_Schedule_Deprecated {
 private $scheduled_date = NULL;
 protected $scheduled_timestamp = NULL;
 public function __construct( DateTime $date ) {
 $this->scheduled_date = $date;
 }
 abstract public function is_recurring();
 abstract protected function calculate_next( DateTime $after );
 public function get_next( DateTime $after ) {
 $after = clone $after;
 if ( $after > $this->scheduled_date ) {
 $after = $this->calculate_next( $after );
 return $after;
 }
 return clone $this->scheduled_date;
 }
 public function get_date() {
 return $this->scheduled_date;
 }
 public function __sleep() {
 $this->scheduled_timestamp = $this->scheduled_date->getTimestamp();
 return array(
 'scheduled_timestamp',
 );
 }
 public function __wakeup() {
 $this->scheduled_date = as_get_datetime_object( $this->scheduled_timestamp );
 unset( $this->scheduled_timestamp );
 }
}
