<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
class Runner {
 private $source_store;
 private $destination_store;
 private $source_logger;
 private $destination_logger;
 private $batch_fetcher;
 private $action_migrator;
 private $log_migrator;
 private $progress_bar;
 public function __construct( Config $config ) {
 $this->source_store = $config->get_source_store();
 $this->destination_store = $config->get_destination_store();
 $this->source_logger = $config->get_source_logger();
 $this->destination_logger = $config->get_destination_logger();
 $this->batch_fetcher = new BatchFetcher( $this->source_store );
 if ( $config->get_dry_run() ) {
 $this->log_migrator = new DryRun_LogMigrator( $this->source_logger, $this->destination_logger );
 $this->action_migrator = new DryRun_ActionMigrator( $this->source_store, $this->destination_store, $this->log_migrator );
 } else {
 $this->log_migrator = new LogMigrator( $this->source_logger, $this->destination_logger );
 $this->action_migrator = new ActionMigrator( $this->source_store, $this->destination_store, $this->log_migrator );
 }
 if ( defined( 'WP_CLI' ) && WP_CLI ) {
 $this->progress_bar = $config->get_progress_bar();
 }
 }
 public function run( $batch_size = 10 ) {
 $batch = $this->batch_fetcher->fetch( $batch_size );
 $batch_size = count( $batch );
 if ( ! $batch_size ) {
 return 0;
 }
 if ( $this->progress_bar ) {
 $this->progress_bar->set_message( sprintf( _n( 'Migrating %d action', 'Migrating %d actions', $batch_size, 'action-scheduler' ), $batch_size ) );
 $this->progress_bar->set_count( $batch_size );
 }
 $this->migrate_actions( $batch );
 return $batch_size;
 }
 public function migrate_actions( array $action_ids ) {
 do_action( 'action_scheduler/migration_batch_starting', $action_ids );
 \ActionScheduler::logger()->unhook_stored_action();
 $this->destination_logger->unhook_stored_action();
 foreach ( $action_ids as $source_action_id ) {
 $destination_action_id = $this->action_migrator->migrate( $source_action_id );
 if ( $destination_action_id ) {
 $this->destination_logger->log( $destination_action_id, sprintf(
 __( 'Migrated action with ID %1$d in %2$s to ID %3$d in %4$s', 'action-scheduler' ),
 $source_action_id,
 get_class( $this->source_store ),
 $destination_action_id,
 get_class( $this->destination_store )
 ) );
 }
 if ( $this->progress_bar ) {
 $this->progress_bar->tick();
 }
 }
 if ( $this->progress_bar ) {
 $this->progress_bar->finish();
 }
 \ActionScheduler::logger()->hook_stored_action();
 do_action( 'action_scheduler/migration_batch_complete', $action_ids );
 }
 public function init_destination() {
 $this->destination_store->init();
 $this->destination_logger->init();
 }
}
