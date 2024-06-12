<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
use ActionScheduler_DataController;
use ActionScheduler_LoggerSchema;
use ActionScheduler_StoreSchema;
use Action_Scheduler\WP_CLI\ProgressBar;
class Controller {
 private static $instance;
 private $migration_scheduler;
 private $store_classname;
 private $logger_classname;
 private $migrate_custom_store;
 protected function __construct( Scheduler $migration_scheduler ) {
 $this->migration_scheduler = $migration_scheduler;
 $this->store_classname = '';
 }
 public function get_store_class( $class ) {
 if ( \ActionScheduler_DataController::is_migration_complete() ) {
 return \ActionScheduler_DataController::DATASTORE_CLASS;
 } elseif ( \ActionScheduler_Store::DEFAULT_CLASS !== $class ) {
 $this->store_classname = $class;
 return $class;
 } else {
 return 'ActionScheduler_HybridStore';
 }
 }
 public function get_logger_class( $class ) {
 \ActionScheduler_Store::instance();
 if ( $this->has_custom_datastore() ) {
 $this->logger_classname = $class;
 return $class;
 } else {
 return \ActionScheduler_DataController::LOGGER_CLASS;
 }
 }
 public function has_custom_datastore() {
 return (bool) $this->store_classname;
 }
 public function schedule_migration() {
 $logging_tables = new ActionScheduler_LoggerSchema();
 $store_tables = new ActionScheduler_StoreSchema();
 if (
 ActionScheduler_DataController::is_migration_complete()
 || $this->migration_scheduler->is_migration_scheduled()
 || ! $store_tables->tables_exist()
 || ! $logging_tables->tables_exist()
 ) {
 return;
 }
 $this->migration_scheduler->schedule_migration();
 }
 public function get_migration_config_object() {
 static $config = null;
 if ( ! $config ) {
 $source_store = $this->store_classname ? new $this->store_classname() : new \ActionScheduler_wpPostStore();
 $source_logger = $this->logger_classname ? new $this->logger_classname() : new \ActionScheduler_wpCommentLogger();
 $config = new Config();
 $config->set_source_store( $source_store );
 $config->set_source_logger( $source_logger );
 $config->set_destination_store( new \ActionScheduler_DBStoreMigrator() );
 $config->set_destination_logger( new \ActionScheduler_DBLogger() );
 if ( defined( 'WP_CLI' ) && WP_CLI ) {
 $config->set_progress_bar( new ProgressBar( '', 0 ) );
 }
 }
 return apply_filters( 'action_scheduler/migration_config', $config );
 }
 public function hook_admin_notices() {
 if ( ! $this->allow_migration() || \ActionScheduler_DataController::is_migration_complete() ) {
 return;
 }
 add_action( 'admin_notices', array( $this, 'display_migration_notice' ), 10, 0 );
 }
 public function display_migration_notice() {
 printf( '<div class="notice notice-warning"><p>%s</p></div>', esc_html__( 'Action Scheduler migration in progress. The list of scheduled actions may be incomplete.', 'action-scheduler' ) );
 }
 private function hook() {
 add_filter( 'action_scheduler_store_class', array( $this, 'get_store_class' ), 100, 1 );
 add_filter( 'action_scheduler_logger_class', array( $this, 'get_logger_class' ), 100, 1 );
 add_action( 'init', array( $this, 'maybe_hook_migration' ) );
 add_action( 'wp_loaded', array( $this, 'schedule_migration' ) );
 // Action Scheduler may be displayed as a Tools screen or WooCommerce > Status administration screen
 add_action( 'load-tools_page_action-scheduler', array( $this, 'hook_admin_notices' ), 10, 0 );
 add_action( 'load-woocommerce_page_wc-status', array( $this, 'hook_admin_notices' ), 10, 0 );
 }
 public function maybe_hook_migration() {
 if ( ! $this->allow_migration() || \ActionScheduler_DataController::is_migration_complete() ) {
 return;
 }
 $this->migration_scheduler->hook();
 }
 public function allow_migration() {
 if ( ! \ActionScheduler_DataController::dependencies_met() ) {
 return false;
 }
 if ( null === $this->migrate_custom_store ) {
 $this->migrate_custom_store = apply_filters( 'action_scheduler_migrate_data_store', false );
 }
 return ( ! $this->has_custom_datastore() ) || $this->migrate_custom_store;
 }
 public static function init() {
 if ( \ActionScheduler_DataController::dependencies_met() ) {
 self::instance()->hook();
 }
 }
 public static function instance() {
 if ( ! isset( self::$instance ) ) {
 self::$instance = new static( new Scheduler() );
 }
 return self::$instance;
 }
}
