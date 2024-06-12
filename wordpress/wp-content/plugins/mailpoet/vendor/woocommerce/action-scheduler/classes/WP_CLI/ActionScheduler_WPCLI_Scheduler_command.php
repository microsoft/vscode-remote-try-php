<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_WPCLI_Scheduler_command extends WP_CLI_Command {
 public function fix_schema( $args, $assoc_args ) {
 $schema_classes = array( ActionScheduler_LoggerSchema::class, ActionScheduler_StoreSchema::class );
 foreach ( $schema_classes as $classname ) {
 if ( is_subclass_of( $classname, ActionScheduler_Abstract_Schema::class ) ) {
 $obj = new $classname();
 $obj->init();
 $obj->register_tables( true );
 WP_CLI::success(
 sprintf(
 __( 'Registered schema for %s', 'action-scheduler' ),
 $classname
 )
 );
 }
 }
 }
 public function run( $args, $assoc_args ) {
 // Handle passed arguments.
 $batch = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batch-size', 100 ) );
 $batches = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batches', 0 ) );
 $clean = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'cleanup-batch-size', $batch ) );
 $hooks = explode( ',', WP_CLI\Utils\get_flag_value( $assoc_args, 'hooks', '' ) );
 $hooks = array_filter( array_map( 'trim', $hooks ) );
 $group = \WP_CLI\Utils\get_flag_value( $assoc_args, 'group', '' );
 $exclude_groups = \WP_CLI\Utils\get_flag_value( $assoc_args, 'exclude-groups', '' );
 $free_on = \WP_CLI\Utils\get_flag_value( $assoc_args, 'free-memory-on', 50 );
 $sleep = \WP_CLI\Utils\get_flag_value( $assoc_args, 'pause', 0 );
 $force = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );
 ActionScheduler_DataController::set_free_ticks( $free_on );
 ActionScheduler_DataController::set_sleep_time( $sleep );
 $batches_completed = 0;
 $actions_completed = 0;
 $unlimited = $batches === 0;
 if ( is_callable( [ ActionScheduler::store(), 'set_claim_filter' ] ) ) {
 $exclude_groups = $this->parse_comma_separated_string( $exclude_groups );
 if ( ! empty( $exclude_groups ) ) {
 ActionScheduler::store()->set_claim_filter('exclude-groups', $exclude_groups );
 }
 }
 try {
 // Custom queue cleaner instance.
 $cleaner = new ActionScheduler_QueueCleaner( null, $clean );
 // Get the queue runner instance
 $runner = new ActionScheduler_WPCLI_QueueRunner( null, null, $cleaner );
 // Determine how many tasks will be run in the first batch.
 $total = $runner->setup( $batch, $hooks, $group, $force );
 // Run actions for as long as possible.
 while ( $total > 0 ) {
 $this->print_total_actions( $total );
 $actions_completed += $runner->run();
 $batches_completed++;
 // Maybe set up tasks for the next batch.
 $total = ( $unlimited || $batches_completed < $batches ) ? $runner->setup( $batch, $hooks, $group, $force ) : 0;
 }
 } catch ( Exception $e ) {
 $this->print_error( $e );
 }
 $this->print_total_batches( $batches_completed );
 $this->print_success( $actions_completed );
 }
 private function parse_comma_separated_string( $string ): array {
 return array_filter( str_getcsv( $string ) );
 }
 protected function print_total_actions( $total ) {
 WP_CLI::log(
 sprintf(
 _n( 'Found %d scheduled task', 'Found %d scheduled tasks', $total, 'action-scheduler' ),
 $total
 )
 );
 }
 protected function print_total_batches( $batches_completed ) {
 WP_CLI::log(
 sprintf(
 _n( '%d batch executed.', '%d batches executed.', $batches_completed, 'action-scheduler' ),
 $batches_completed
 )
 );
 }
 protected function print_error( Exception $e ) {
 WP_CLI::error(
 sprintf(
 __( 'There was an error running the action scheduler: %s', 'action-scheduler' ),
 $e->getMessage()
 )
 );
 }
 protected function print_success( $actions_completed ) {
 WP_CLI::success(
 sprintf(
 _n( '%d scheduled task completed.', '%d scheduled tasks completed.', $actions_completed, 'action-scheduler' ),
 $actions_completed
 )
 );
 }
}
