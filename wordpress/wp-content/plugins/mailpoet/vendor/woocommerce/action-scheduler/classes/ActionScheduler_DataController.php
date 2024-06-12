<?php
if (!defined('ABSPATH')) exit;
use Action_Scheduler\Migration\Controller;
class ActionScheduler_DataController {
 const DATASTORE_CLASS = 'ActionScheduler_DBStore';
 const LOGGER_CLASS = 'ActionScheduler_DBLogger';
 const STATUS_FLAG = 'action_scheduler_migration_status';
 const STATUS_COMPLETE = 'complete';
 const MIN_PHP_VERSION = '5.5';
 private static $instance;
 private static $sleep_time = 0;
 private static $free_ticks = 50;
 public static function dependencies_met() {
 $php_support = version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '>=' );
 return $php_support && apply_filters( 'action_scheduler_migration_dependencies_met', true );
 }
 public static function is_migration_complete() {
 return get_option( self::STATUS_FLAG ) === self::STATUS_COMPLETE;
 }
 public static function mark_migration_complete() {
 update_option( self::STATUS_FLAG, self::STATUS_COMPLETE );
 }
 public static function mark_migration_incomplete() {
 delete_option( self::STATUS_FLAG );
 }
 public static function set_store_class( $class ) {
 return self::DATASTORE_CLASS;
 }
 public static function set_logger_class( $class ) {
 return self::LOGGER_CLASS;
 }
 public static function set_sleep_time( $sleep_time ) {
 self::$sleep_time = (int) $sleep_time;
 }
 public static function set_free_ticks( $free_ticks ) {
 self::$free_ticks = (int) $free_ticks;
 }
 public static function maybe_free_memory( $ticks ) {
 if ( self::$free_ticks && 0 === $ticks % self::$free_ticks ) {
 self::free_memory();
 }
 }
 public static function free_memory() {
 if ( 0 < self::$sleep_time ) {
 \WP_CLI::warning( sprintf( _n( 'Stopped the insanity for %d second', 'Stopped the insanity for %d seconds', self::$sleep_time, 'action-scheduler' ), self::$sleep_time ) );
 sleep( self::$sleep_time );
 }
 \WP_CLI::warning( __( 'Attempting to reduce used memory...', 'action-scheduler' ) );
 global $wpdb, $wp_object_cache;
 $wpdb->queries = array();
 if ( ! is_a( $wp_object_cache, 'WP_Object_Cache' ) ) {
 return;
 }
 $wp_object_cache->group_ops = array();
 $wp_object_cache->stats = array();
 $wp_object_cache->memcache_debug = array();
 $wp_object_cache->cache = array();
 if ( is_callable( array( $wp_object_cache, '__remoteset' ) ) ) {
 call_user_func( array( $wp_object_cache, '__remoteset' ) ); // important
 }
 }
 public static function init() {
 if ( self::is_migration_complete() ) {
 add_filter( 'action_scheduler_store_class', array( 'ActionScheduler_DataController', 'set_store_class' ), 100 );
 add_filter( 'action_scheduler_logger_class', array( 'ActionScheduler_DataController', 'set_logger_class' ), 100 );
 add_action( 'deactivate_plugin', array( 'ActionScheduler_DataController', 'mark_migration_incomplete' ) );
 } elseif ( self::dependencies_met() ) {
 Controller::init();
 }
 add_action( 'action_scheduler/progress_tick', array( 'ActionScheduler_DataController', 'maybe_free_memory' ) );
 }
 public static function instance() {
 if ( ! isset( self::$instance ) ) {
 self::$instance = new static();
 }
 return self::$instance;
 }
}
