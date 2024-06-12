<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_QueueRunner extends ActionScheduler_Abstract_QueueRunner {
 const WP_CRON_HOOK = 'action_scheduler_run_queue';
 const WP_CRON_SCHEDULE = 'every_minute';
 protected $async_request;
 private static $runner = null;
 private $processed_actions_count = 0;
 public static function instance() {
 if ( empty(self::$runner) ) {
 $class = apply_filters('action_scheduler_queue_runner_class', 'ActionScheduler_QueueRunner');
 self::$runner = new $class();
 }
 return self::$runner;
 }
 public function __construct( ActionScheduler_Store $store = null, ActionScheduler_FatalErrorMonitor $monitor = null, ActionScheduler_QueueCleaner $cleaner = null, ActionScheduler_AsyncRequest_QueueRunner $async_request = null ) {
 parent::__construct( $store, $monitor, $cleaner );
 if ( is_null( $async_request ) ) {
 $async_request = new ActionScheduler_AsyncRequest_QueueRunner( $this->store );
 }
 $this->async_request = $async_request;
 }
 public function init() {
 add_filter( 'cron_schedules', array( self::instance(), 'add_wp_cron_schedule' ) );
 // Check for and remove any WP Cron hook scheduled by Action Scheduler < 3.0.0, which didn't include the $context param
 $next_timestamp = wp_next_scheduled( self::WP_CRON_HOOK );
 if ( $next_timestamp ) {
 wp_unschedule_event( $next_timestamp, self::WP_CRON_HOOK );
 }
 $cron_context = array( 'WP Cron' );
 if ( ! wp_next_scheduled( self::WP_CRON_HOOK, $cron_context ) ) {
 $schedule = apply_filters( 'action_scheduler_run_schedule', self::WP_CRON_SCHEDULE );
 wp_schedule_event( time(), $schedule, self::WP_CRON_HOOK, $cron_context );
 }
 add_action( self::WP_CRON_HOOK, array( self::instance(), 'run' ) );
 $this->hook_dispatch_async_request();
 }
 public function hook_dispatch_async_request() {
 add_action( 'shutdown', array( $this, 'maybe_dispatch_async_request' ) );
 }
 public function unhook_dispatch_async_request() {
 remove_action( 'shutdown', array( $this, 'maybe_dispatch_async_request' ) );
 }
 public function maybe_dispatch_async_request() {
 // Only start an async queue at most once every 60 seconds.
 if (
 is_admin()
 && ! ActionScheduler::lock()->is_locked( 'async-request-runner' )
 && ActionScheduler::lock()->set( 'async-request-runner' )
 ) {
 $this->async_request->maybe_dispatch();
 }
 }
 public function run( $context = 'WP Cron' ) {
 ActionScheduler_Compatibility::raise_memory_limit();
 ActionScheduler_Compatibility::raise_time_limit( $this->get_time_limit() );
 do_action( 'action_scheduler_before_process_queue' );
 $this->run_cleanup();
 $this->processed_actions_count = 0;
 if ( false === $this->has_maximum_concurrent_batches() ) {
 $batch_size = apply_filters( 'action_scheduler_queue_runner_batch_size', 25 );
 do {
 $processed_actions_in_batch = $this->do_batch( $batch_size, $context );
 $this->processed_actions_count += $processed_actions_in_batch;
 } while ( $processed_actions_in_batch > 0 && ! $this->batch_limits_exceeded( $this->processed_actions_count ) ); // keep going until we run out of actions, time, or memory
 }
 do_action( 'action_scheduler_after_process_queue' );
 return $this->processed_actions_count;
 }
 protected function do_batch( $size = 100, $context = '' ) {
 $claim = $this->store->stake_claim($size);
 $this->monitor->attach($claim);
 $processed_actions = 0;
 foreach ( $claim->get_actions() as $action_id ) {
 // bail if we lost the claim
 if ( ! in_array( $action_id, $this->store->find_actions_by_claim_id( $claim->get_id() ) ) ) {
 break;
 }
 $this->process_action( $action_id, $context );
 $processed_actions++;
 if ( $this->batch_limits_exceeded( $processed_actions + $this->processed_actions_count ) ) {
 break;
 }
 }
 $this->store->release_claim($claim);
 $this->monitor->detach();
 $this->clear_caches();
 return $processed_actions;
 }
 protected function clear_caches() {
 $flushing_runtime_cache_explicitly_supported = function_exists( 'wp_cache_supports' ) && wp_cache_supports( 'flush_runtime' );
 $flushing_runtime_cache_implicitly_supported = ! function_exists( 'wp_cache_supports' ) && function_exists( 'wp_cache_flush_runtime' );
 if ( $flushing_runtime_cache_explicitly_supported || $flushing_runtime_cache_implicitly_supported ) {
 wp_cache_flush_runtime();
 } elseif (
 ! wp_using_ext_object_cache()
 || apply_filters( 'action_scheduler_queue_runner_flush_cache', false )
 ) {
 wp_cache_flush();
 }
 }
 public function add_wp_cron_schedule( $schedules ) {
 $schedules['every_minute'] = array(
 'interval' => 60, // in seconds
 'display' => __( 'Every minute', 'action-scheduler' ),
 );
 return $schedules;
 }
}
