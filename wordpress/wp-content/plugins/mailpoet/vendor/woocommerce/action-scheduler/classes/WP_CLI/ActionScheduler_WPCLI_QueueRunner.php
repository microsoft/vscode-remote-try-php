<?php
if (!defined('ABSPATH')) exit;
use Action_Scheduler\WP_CLI\ProgressBar;
class ActionScheduler_WPCLI_QueueRunner extends ActionScheduler_Abstract_QueueRunner {
 protected $actions;
 protected $claim;
 protected $progress_bar;
 public function __construct( ActionScheduler_Store $store = null, ActionScheduler_FatalErrorMonitor $monitor = null, ActionScheduler_QueueCleaner $cleaner = null ) {
 if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
 throw new Exception( sprintf( __( 'The %s class can only be run within WP CLI.', 'action-scheduler' ), __CLASS__ ) );
 }
 parent::__construct( $store, $monitor, $cleaner );
 }
 public function setup( $batch_size, $hooks = array(), $group = '', $force = false ) {
 $this->run_cleanup();
 $this->add_hooks();
 // Check to make sure there aren't too many concurrent processes running.
 if ( $this->has_maximum_concurrent_batches() ) {
 if ( $force ) {
 WP_CLI::warning( __( 'There are too many concurrent batches, but the run is forced to continue.', 'action-scheduler' ) );
 } else {
 WP_CLI::error( __( 'There are too many concurrent batches.', 'action-scheduler' ) );
 }
 }
 // Stake a claim and store it.
 $this->claim = $this->store->stake_claim( $batch_size, null, $hooks, $group );
 $this->monitor->attach( $this->claim );
 $this->actions = $this->claim->get_actions();
 return count( $this->actions );
 }
 protected function add_hooks() {
 add_action( 'action_scheduler_before_execute', array( $this, 'before_execute' ) );
 add_action( 'action_scheduler_after_execute', array( $this, 'after_execute' ), 10, 2 );
 add_action( 'action_scheduler_failed_execution', array( $this, 'action_failed' ), 10, 2 );
 }
 protected function setup_progress_bar() {
 $count = count( $this->actions );
 $this->progress_bar = new ProgressBar(
 sprintf( _n( 'Running %d action', 'Running %d actions', $count, 'action-scheduler' ), $count ),
 $count
 );
 }
 public function run( $context = 'WP CLI' ) {
 do_action( 'action_scheduler_before_process_queue' );
 $this->setup_progress_bar();
 foreach ( $this->actions as $action_id ) {
 // Error if we lost the claim.
 if ( ! in_array( $action_id, $this->store->find_actions_by_claim_id( $this->claim->get_id() ) ) ) {
 WP_CLI::warning( __( 'The claim has been lost. Aborting current batch.', 'action-scheduler' ) );
 break;
 }
 $this->process_action( $action_id, $context );
 $this->progress_bar->tick();
 }
 $completed = $this->progress_bar->current();
 $this->progress_bar->finish();
 $this->store->release_claim( $this->claim );
 do_action( 'action_scheduler_after_process_queue' );
 return $completed;
 }
 public function before_execute( $action_id ) {
 WP_CLI::log( sprintf( __( 'Started processing action %s', 'action-scheduler' ), $action_id ) );
 }
 public function after_execute( $action_id, $action = null ) {
 // backward compatibility
 if ( null === $action ) {
 $action = $this->store->fetch_action( $action_id );
 }
 WP_CLI::log( sprintf( __( 'Completed processing action %1$s with hook: %2$s', 'action-scheduler' ), $action_id, $action->get_hook() ) );
 }
 public function action_failed( $action_id, $exception ) {
 WP_CLI::error(
 sprintf( __( 'Error processing action %1$s: %2$s', 'action-scheduler' ), $action_id, $exception->getMessage() ),
 false
 );
 }
 protected function stop_the_insanity( $sleep_time = 0 ) {
 _deprecated_function( 'ActionScheduler_WPCLI_QueueRunner::stop_the_insanity', '3.0.0', 'ActionScheduler_DataController::free_memory' );
 ActionScheduler_DataController::free_memory();
 }
 protected function maybe_stop_the_insanity() {
 // The value returned by progress_bar->current() might be padded. Remove padding, and convert to int.
 $current_iteration = intval( trim( $this->progress_bar->current() ) );
 if ( 0 === $current_iteration % 50 ) {
 $this->stop_the_insanity();
 }
 }
}
