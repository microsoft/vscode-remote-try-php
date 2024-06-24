<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_LogEntry {
 protected $action_id = '';
 protected $message = '';
 protected $date;
 public function __construct( $action_id, $message, $date = null ) {
 if ( null !== $date && ! is_a( $date, 'DateTime' ) ) {
 _doing_it_wrong( __METHOD__, 'The third parameter must be a valid DateTime instance, or null.', '2.0.0' );
 $date = null;
 }
 $this->action_id = $action_id;
 $this->message = $message;
 $this->date = $date ? $date : new Datetime;
 }
 public function get_date() {
 return $this->date;
 }
 public function get_action_id() {
 return $this->action_id;
 }
 public function get_message() {
 return $this->message;
 }
}
