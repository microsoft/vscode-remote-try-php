<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Abstract_QueueRunner extends ActionScheduler_Abstract_QueueRunner_Deprecated {
 protected $cleaner;
 protected $monitor;
 protected $store;
 private $created_time;
 public function __construct( ActionScheduler_Store $store = null, ActionScheduler_FatalErrorMonitor $monitor = null, ActionScheduler_QueueCleaner $cleaner = null ) {
 $this->created_time = microtime( true );
 $this->store = $store ? $store : ActionScheduler_Store::instance();
 $this->monitor = $monitor ? $monitor : new ActionScheduler_FatalErrorMonitor( $this->store );
 $this->cleaner = $cleaner ? $cleaner : new ActionScheduler_QueueCleaner( $this->store );
 }
 public function process_action( $action_id, $context = '' ) {
 // Temporarily override the error handler while we process the current action.
 set_error_handler(
 function ( $type, $message ) {
 throw new Exception( $message );
 },
 E_USER_ERROR | E_RECOVERABLE_ERROR
 );
 try {
 try {
 $valid_action = false;
 do_action( 'action_scheduler_before_execute', $action_id, $context );
 if ( ActionScheduler_Store::STATUS_PENDING !== $this->store->get_status( $action_id ) ) {
 do_action( 'action_scheduler_execution_ignored', $action_id, $context );
 return;
 }
 $valid_action = true;
 do_action( 'action_scheduler_begin_execute', $action_id, $context );
 $action = $this->store->fetch_action( $action_id );
 $this->store->log_execution( $action_id );
 $action->execute();
 do_action( 'action_scheduler_after_execute', $action_id, $action, $context );
 $this->store->mark_complete( $action_id );
 } catch ( Throwable $e ) {
 // Throwable is defined when executing under PHP 7.0 and up. We convert it to an exception, for
 // compatibility with ActionScheduler_Logger.
 throw new Exception( $e->getMessage(), $e->getCode(), $e );
 }
 } catch ( Exception $e ) {
 // This catch block exists for compatibility with PHP 5.6.
 $this->handle_action_error( $action_id, $e, $context, $valid_action );
 } finally {
 restore_error_handler();
 }
 if ( isset( $action ) && is_a( $action, 'ActionScheduler_Action' ) && $action->get_schedule()->is_recurring() ) {
 $this->schedule_next_instance( $action, $action_id );
 }
 }
 private function handle_action_error( $action_id, $e, $context, $valid_action ) {
 if ( $valid_action ) {
 $this->store->mark_failure( $action_id );
 do_action( 'action_scheduler_failed_execution', $action_id, $e, $context );
 } else {
 do_action( 'action_scheduler_failed_validation', $action_id, $e, $context );
 }
 }
 protected function schedule_next_instance( ActionScheduler_Action $action, $action_id ) {
 // If a recurring action has been consistently failing, we may wish to stop rescheduling it.
 if (
 ActionScheduler_Store::STATUS_FAILED === $this->store->get_status( $action_id )
 && $this->recurring_action_is_consistently_failing( $action, $action_id )
 ) {
 ActionScheduler_Logger::instance()->log(
 $action_id,
 __( 'This action appears to be consistently failing. A new instance will not be scheduled.', 'action-scheduler' )
 );
 return;
 }
 try {
 ActionScheduler::factory()->repeat( $action );
 } catch ( Exception $e ) {
 do_action( 'action_scheduler_failed_to_schedule_next_instance', $action_id, $e, $action );
 }
 }
 private function recurring_action_is_consistently_failing( ActionScheduler_Action $action, $action_id ) {
 $consistent_failure_threshold = (int) apply_filters( 'action_scheduler_recurring_action_failure_threshold', 5 );
 // This query should find the earliest *failing* action (for the hook we are interested in) within our threshold.
 $query_args = array(
 'hook' => $action->get_hook(),
 'status' => ActionScheduler_Store::STATUS_FAILED,
 'date' => date_create( 'now', timezone_open( 'UTC' ) )->format( 'Y-m-d H:i:s' ),
 'date_compare' => '<',
 'per_page' => 1,
 'offset' => $consistent_failure_threshold - 1
 );
 $first_failing_action_id = $this->store->query_actions( $query_args );
 // If we didn't retrieve an action ID, then there haven't been enough failures for us to worry about.
 if ( empty( $first_failing_action_id ) ) {
 return false;
 }
 // Now let's fetch the first action (having the same hook) of *any status* within the same window.
 unset( $query_args['status'] );
 $first_action_id_with_the_same_hook = $this->store->query_actions( $query_args );
 return (bool) apply_filters(
 'action_scheduler_recurring_action_is_consistently_failing',
 $first_action_id_with_the_same_hook === $first_failing_action_id,
 $action
 );
 }
 protected function run_cleanup() {
 $this->cleaner->clean( 10 * $this->get_time_limit() );
 }
 public function get_allowed_concurrent_batches() {
 return apply_filters( 'action_scheduler_queue_runner_concurrent_batches', 1 );
 }
 public function has_maximum_concurrent_batches() {
 return $this->store->get_claim_count() >= $this->get_allowed_concurrent_batches();
 }
 protected function get_time_limit() {
 $time_limit = 30;
 // Apply deprecated filter from deprecated get_maximum_execution_time() method
 if ( has_filter( 'action_scheduler_maximum_execution_time' ) ) {
 _deprecated_function( 'action_scheduler_maximum_execution_time', '2.1.1', 'action_scheduler_queue_runner_time_limit' );
 $time_limit = apply_filters( 'action_scheduler_maximum_execution_time', $time_limit );
 }
 return absint( apply_filters( 'action_scheduler_queue_runner_time_limit', $time_limit ) );
 }
 protected function get_execution_time() {
 $execution_time = microtime( true ) - $this->created_time;
 // Get the CPU time if the hosting environment uses it rather than wall-clock time to calculate a process's execution time.
 if ( function_exists( 'getrusage' ) && apply_filters( 'action_scheduler_use_cpu_execution_time', defined( 'PANTHEON_ENVIRONMENT' ) ) ) {
 $resource_usages = getrusage();
 if ( isset( $resource_usages['ru_stime.tv_usec'], $resource_usages['ru_stime.tv_usec'] ) ) {
 $execution_time = $resource_usages['ru_stime.tv_sec'] + ( $resource_usages['ru_stime.tv_usec'] / 1000000 );
 }
 }
 return $execution_time;
 }
 protected function time_likely_to_be_exceeded( $processed_actions ) {
 $execution_time = $this->get_execution_time();
 $max_execution_time = $this->get_time_limit();
 // Safety against division by zero errors.
 if ( 0 === $processed_actions ) {
 return $execution_time >= $max_execution_time;
 }
 $time_per_action = $execution_time / $processed_actions;
 $estimated_time = $execution_time + ( $time_per_action * 3 );
 $likely_to_be_exceeded = $estimated_time > $max_execution_time;
 return apply_filters( 'action_scheduler_maximum_execution_time_likely_to_be_exceeded', $likely_to_be_exceeded, $this, $processed_actions, $execution_time, $max_execution_time );
 }
 protected function get_memory_limit() {
 if ( function_exists( 'ini_get' ) ) {
 $memory_limit = ini_get( 'memory_limit' );
 } else {
 $memory_limit = '128M'; // Sensible default, and minimum required by WooCommerce
 }
 if ( ! $memory_limit || -1 === $memory_limit || '-1' === $memory_limit ) {
 // Unlimited, set to 32GB.
 $memory_limit = '32G';
 }
 return ActionScheduler_Compatibility::convert_hr_to_bytes( $memory_limit );
 }
 protected function memory_exceeded() {
 $memory_limit = $this->get_memory_limit() * 0.90;
 $current_memory = memory_get_usage( true );
 $memory_exceeded = $current_memory >= $memory_limit;
 return apply_filters( 'action_scheduler_memory_exceeded', $memory_exceeded, $this );
 }
 protected function batch_limits_exceeded( $processed_actions ) {
 return $this->memory_exceeded() || $this->time_likely_to_be_exceeded( $processed_actions );
 }
 abstract public function run( $context = '' );
}
