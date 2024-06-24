<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Logger {
 private static $logger = NULL;
 public static function instance() {
 if ( empty(self::$logger) ) {
 $class = apply_filters('action_scheduler_logger_class', 'ActionScheduler_wpCommentLogger');
 self::$logger = new $class();
 }
 return self::$logger;
 }
 abstract public function log( $action_id, $message, DateTime $date = NULL );
 abstract public function get_entry( $entry_id );
 abstract public function get_logs( $action_id );
 public function init() {
 $this->hook_stored_action();
 add_action( 'action_scheduler_canceled_action', array( $this, 'log_canceled_action' ), 10, 1 );
 add_action( 'action_scheduler_begin_execute', array( $this, 'log_started_action' ), 10, 2 );
 add_action( 'action_scheduler_after_execute', array( $this, 'log_completed_action' ), 10, 3 );
 add_action( 'action_scheduler_failed_execution', array( $this, 'log_failed_action' ), 10, 3 );
 add_action( 'action_scheduler_failed_action', array( $this, 'log_timed_out_action' ), 10, 2 );
 add_action( 'action_scheduler_unexpected_shutdown', array( $this, 'log_unexpected_shutdown' ), 10, 2 );
 add_action( 'action_scheduler_reset_action', array( $this, 'log_reset_action' ), 10, 1 );
 add_action( 'action_scheduler_execution_ignored', array( $this, 'log_ignored_action' ), 10, 2 );
 add_action( 'action_scheduler_failed_fetch_action', array( $this, 'log_failed_fetch_action' ), 10, 2 );
 add_action( 'action_scheduler_failed_to_schedule_next_instance', array( $this, 'log_failed_schedule_next_instance' ), 10, 2 );
 add_action( 'action_scheduler_bulk_cancel_actions', array( $this, 'bulk_log_cancel_actions' ), 10, 1 );
 }
 public function hook_stored_action() {
 add_action( 'action_scheduler_stored_action', array( $this, 'log_stored_action' ) );
 }
 public function unhook_stored_action() {
 remove_action( 'action_scheduler_stored_action', array( $this, 'log_stored_action' ) );
 }
 public function log_stored_action( $action_id ) {
 $this->log( $action_id, __( 'action created', 'action-scheduler' ) );
 }
 public function log_canceled_action( $action_id ) {
 $this->log( $action_id, __( 'action canceled', 'action-scheduler' ) );
 }
 public function log_started_action( $action_id, $context = '' ) {
 if ( ! empty( $context ) ) {
 $message = sprintf( __( 'action started via %s', 'action-scheduler' ), $context );
 } else {
 $message = __( 'action started', 'action-scheduler' );
 }
 $this->log( $action_id, $message );
 }
 public function log_completed_action( $action_id, $action = NULL, $context = '' ) {
 if ( ! empty( $context ) ) {
 $message = sprintf( __( 'action complete via %s', 'action-scheduler' ), $context );
 } else {
 $message = __( 'action complete', 'action-scheduler' );
 }
 $this->log( $action_id, $message );
 }
 public function log_failed_action( $action_id, Exception $exception, $context = '' ) {
 if ( ! empty( $context ) ) {
 $message = sprintf( __( 'action failed via %1$s: %2$s', 'action-scheduler' ), $context, $exception->getMessage() );
 } else {
 $message = sprintf( __( 'action failed: %s', 'action-scheduler' ), $exception->getMessage() );
 }
 $this->log( $action_id, $message );
 }
 public function log_timed_out_action( $action_id, $timeout ) {
 $this->log( $action_id, sprintf( __( 'action marked as failed after %s seconds. Unknown error occurred. Check server, PHP and database error logs to diagnose cause.', 'action-scheduler' ), $timeout ) );
 }
 public function log_unexpected_shutdown( $action_id, $error ) {
 if ( ! empty( $error ) ) {
 $this->log( $action_id, sprintf( __( 'unexpected shutdown: PHP Fatal error %1$s in %2$s on line %3$s', 'action-scheduler' ), $error['message'], $error['file'], $error['line'] ) );
 }
 }
 public function log_reset_action( $action_id ) {
 $this->log( $action_id, __( 'action reset', 'action-scheduler' ) );
 }
 public function log_ignored_action( $action_id, $context = '' ) {
 if ( ! empty( $context ) ) {
 $message = sprintf( __( 'action ignored via %s', 'action-scheduler' ), $context );
 } else {
 $message = __( 'action ignored', 'action-scheduler' );
 }
 $this->log( $action_id, $message );
 }
 public function log_failed_fetch_action( $action_id, Exception $exception = NULL ) {
 if ( ! is_null( $exception ) ) {
 $log_message = sprintf( __( 'There was a failure fetching this action: %s', 'action-scheduler' ), $exception->getMessage() );
 } else {
 $log_message = __( 'There was a failure fetching this action', 'action-scheduler' );
 }
 $this->log( $action_id, $log_message );
 }
 public function log_failed_schedule_next_instance( $action_id, Exception $exception ) {
 $this->log( $action_id, sprintf( __( 'There was a failure scheduling the next instance of this action: %s', 'action-scheduler' ), $exception->getMessage() ) );
 }
 public function bulk_log_cancel_actions( $action_ids ) {
 if ( empty( $action_ids ) ) {
 return;
 }
 foreach ( $action_ids as $action_id ) {
 $this->log_canceled_action( $action_id );
 }
 }
}
