<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_NullLogEntry extends ActionScheduler_LogEntry {
 public function __construct( $action_id = '', $message = '' ) {
 // nothing to see here
 }
}
 