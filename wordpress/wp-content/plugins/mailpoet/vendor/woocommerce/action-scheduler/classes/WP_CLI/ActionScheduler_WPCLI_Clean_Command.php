<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_WPCLI_Clean_Command extends WP_CLI_Command {
 public function clean( $args, $assoc_args ) {
 // Handle passed arguments.
 $batch = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batch-size', 20 ) );
 $batches = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'batches', 0 ) );
 $status = explode( ',', WP_CLI\Utils\get_flag_value( $assoc_args, 'status', '' ) );
 $status = array_filter( array_map( 'trim', $status ) );
 $before = \WP_CLI\Utils\get_flag_value( $assoc_args, 'before', '' );
 $sleep = \WP_CLI\Utils\get_flag_value( $assoc_args, 'pause', 0 );
 $batches_completed = 0;
 $actions_deleted = 0;
 $unlimited = $batches === 0;
 try {
 $lifespan = as_get_datetime_object( $before );
 } catch ( Exception $e ) {
 $lifespan = null;
 }
 try {
 // Custom queue cleaner instance.
 $cleaner = new ActionScheduler_QueueCleaner( null, $batch );
 // Clean actions for as long as possible.
 while ( $unlimited || $batches_completed < $batches ) {
 if ( $sleep && $batches_completed > 0 ) {
 sleep( $sleep );
 }
 $deleted = count( $cleaner->clean_actions( $status, $lifespan, null,'CLI' ) );
 if ( $deleted <= 0 ) {
 break;
 }
 $actions_deleted += $deleted;
 $batches_completed++;
 $this->print_success( $deleted );
 }
 } catch ( Exception $e ) {
 $this->print_error( $e );
 }
 $this->print_total_batches( $batches_completed );
 if ( $batches_completed > 1 ) {
 $this->print_success( $actions_deleted );
 }
 }
 protected function print_total_batches( int $batches_processed ) {
 WP_CLI::log(
 sprintf(
 _n( '%d batch processed.', '%d batches processed.', $batches_processed, 'action-scheduler' ),
 $batches_processed
 )
 );
 }
 protected function print_error( Exception $e ) {
 WP_CLI::error(
 sprintf(
 __( 'There was an error deleting an action: %s', 'action-scheduler' ),
 $e->getMessage()
 )
 );
 }
 protected function print_success( int $actions_deleted ) {
 WP_CLI::success(
 sprintf(
 _n( '%d action deleted.', '%d actions deleted.', $actions_deleted, 'action-scheduler' ),
 $actions_deleted
 )
 );
 }
}
