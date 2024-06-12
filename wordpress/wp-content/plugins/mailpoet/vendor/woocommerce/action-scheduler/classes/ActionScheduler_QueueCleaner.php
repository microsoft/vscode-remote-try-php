<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_QueueCleaner {
 protected $batch_size;
 private $store = null;
 private $month_in_seconds = 2678400;
 private $default_statuses_to_purge = [
 ActionScheduler_Store::STATUS_COMPLETE,
 ActionScheduler_Store::STATUS_CANCELED,
 ];
 public function __construct( ActionScheduler_Store $store = null, $batch_size = 20 ) {
 $this->store = $store ? $store : ActionScheduler_Store::instance();
 $this->batch_size = $batch_size;
 }
 public function delete_old_actions() {
 $lifespan = apply_filters( 'action_scheduler_retention_period', $this->month_in_seconds );
 try {
 $cutoff = as_get_datetime_object( $lifespan . ' seconds ago' );
 } catch ( Exception $e ) {
 _doing_it_wrong(
 __METHOD__,
 sprintf(
 esc_html__( 'It was not possible to determine a valid cut-off time: %s.', 'action-scheduler' ),
 esc_html( $e->getMessage() )
 ),
 '3.5.5'
 );
 return array();
 }
 $statuses_to_purge = (array) apply_filters( 'action_scheduler_default_cleaner_statuses', $this->default_statuses_to_purge );
 return $this->clean_actions( $statuses_to_purge, $cutoff, $this->get_batch_size() );
 }
 public function clean_actions( array $statuses_to_purge, DateTime $cutoff_date, $batch_size = null, $context = 'old' ) {
 $batch_size = $batch_size !== null ? $batch_size : $this->batch_size;
 $cutoff = $cutoff_date !== null ? $cutoff_date : as_get_datetime_object( $this->month_in_seconds . ' seconds ago' );
 $lifespan = time() - $cutoff->getTimestamp();
 if ( empty( $statuses_to_purge ) ) {
 $statuses_to_purge = $this->default_statuses_to_purge;
 }
 $deleted_actions = [];
 foreach ( $statuses_to_purge as $status ) {
 $actions_to_delete = $this->store->query_actions( array(
 'status' => $status,
 'modified' => $cutoff,
 'modified_compare' => '<=',
 'per_page' => $batch_size,
 'orderby' => 'none',
 ) );
 $deleted_actions = array_merge( $deleted_actions, $this->delete_actions( $actions_to_delete, $lifespan, $context ) );
 }
 return $deleted_actions;
 }
 private function delete_actions( array $actions_to_delete, $lifespan = null, $context = 'old' ) {
 $deleted_actions = [];
 if ( $lifespan === null ) {
 $lifespan = $this->month_in_seconds;
 }
 foreach ( $actions_to_delete as $action_id ) {
 try {
 $this->store->delete_action( $action_id );
 $deleted_actions[] = $action_id;
 } catch ( Exception $e ) {
 do_action( "action_scheduler_failed_{$context}_action_deletion", $action_id, $e, $lifespan, count( $actions_to_delete ) );
 }
 }
 return $deleted_actions;
 }
 public function reset_timeouts( $time_limit = 300 ) {
 $timeout = apply_filters( 'action_scheduler_timeout_period', $time_limit );
 if ( $timeout < 0 ) {
 return;
 }
 $cutoff = as_get_datetime_object($timeout.' seconds ago');
 $actions_to_reset = $this->store->query_actions( array(
 'status' => ActionScheduler_Store::STATUS_PENDING,
 'modified' => $cutoff,
 'modified_compare' => '<=',
 'claimed' => true,
 'per_page' => $this->get_batch_size(),
 'orderby' => 'none',
 ) );
 foreach ( $actions_to_reset as $action_id ) {
 $this->store->unclaim_action( $action_id );
 do_action( 'action_scheduler_reset_action', $action_id );
 }
 }
 public function mark_failures( $time_limit = 300 ) {
 $timeout = apply_filters( 'action_scheduler_failure_period', $time_limit );
 if ( $timeout < 0 ) {
 return;
 }
 $cutoff = as_get_datetime_object($timeout.' seconds ago');
 $actions_to_reset = $this->store->query_actions( array(
 'status' => ActionScheduler_Store::STATUS_RUNNING,
 'modified' => $cutoff,
 'modified_compare' => '<=',
 'per_page' => $this->get_batch_size(),
 'orderby' => 'none',
 ) );
 foreach ( $actions_to_reset as $action_id ) {
 $this->store->mark_failure( $action_id );
 do_action( 'action_scheduler_failed_action', $action_id, $timeout );
 }
 }
 public function clean( $time_limit = 300 ) {
 $this->delete_old_actions();
 $this->reset_timeouts( $time_limit );
 $this->mark_failures( $time_limit );
 }
 protected function get_batch_size() {
 return absint( apply_filters( 'action_scheduler_cleanup_batch_size', $this->batch_size ) );
 }
}
