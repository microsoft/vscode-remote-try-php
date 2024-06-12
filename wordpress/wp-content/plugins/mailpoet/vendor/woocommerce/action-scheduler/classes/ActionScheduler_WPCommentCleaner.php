<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_WPCommentCleaner {
 protected static $cleanup_hook = 'action_scheduler/cleanup_wp_comment_logs';
 protected static $wp_comment_logger = null;
 protected static $has_logs_option_key = 'as_has_wp_comment_logs';
 public static function init() {
 if ( empty( self::$wp_comment_logger ) ) {
 self::$wp_comment_logger = new ActionScheduler_wpCommentLogger();
 }
 add_action( self::$cleanup_hook, array( __CLASS__, 'delete_all_action_comments' ) );
 // While there are orphaned logs left in the comments table, we need to attach the callbacks which filter comment counts.
 add_action( 'pre_get_comments', array( self::$wp_comment_logger, 'filter_comment_queries' ), 10, 1 );
 add_action( 'wp_count_comments', array( self::$wp_comment_logger, 'filter_comment_count' ), 20, 2 ); // run after WC_Comments::wp_count_comments() to make sure we exclude order notes and action logs
 add_action( 'comment_feed_where', array( self::$wp_comment_logger, 'filter_comment_feed' ), 10, 2 );
 // Action Scheduler may be displayed as a Tools screen or WooCommerce > Status administration screen
 add_action( 'load-tools_page_action-scheduler', array( __CLASS__, 'register_admin_notice' ) );
 add_action( 'load-woocommerce_page_wc-status', array( __CLASS__, 'register_admin_notice' ) );
 }
 public static function has_logs() {
 return 'yes' === get_option( self::$has_logs_option_key );
 }
 public static function maybe_schedule_cleanup() {
 if ( (bool) get_comments( array( 'type' => ActionScheduler_wpCommentLogger::TYPE, 'number' => 1, 'fields' => 'ids' ) ) ) {
 update_option( self::$has_logs_option_key, 'yes' );
 if ( ! as_next_scheduled_action( self::$cleanup_hook ) ) {
 as_schedule_single_action( gmdate( 'U' ) + ( 6 * MONTH_IN_SECONDS ), self::$cleanup_hook );
 }
 }
 }
 public static function delete_all_action_comments() {
 global $wpdb;
 $wpdb->delete( $wpdb->comments, array( 'comment_type' => ActionScheduler_wpCommentLogger::TYPE, 'comment_agent' => ActionScheduler_wpCommentLogger::AGENT ) );
 delete_option( self::$has_logs_option_key );
 }
 public static function register_admin_notice() {
 add_action( 'admin_notices', array( __CLASS__, 'print_admin_notice' ) );
 }
 public static function print_admin_notice() {
 $next_cleanup_message = '';
 $next_scheduled_cleanup_hook = as_next_scheduled_action( self::$cleanup_hook );
 if ( $next_scheduled_cleanup_hook ) {
 $next_cleanup_message = sprintf( __( 'This data will be deleted in %s.', 'action-scheduler' ), human_time_diff( gmdate( 'U' ), $next_scheduled_cleanup_hook ) );
 }
 $notice = sprintf(
 __( 'Action Scheduler has migrated data to custom tables; however, orphaned log entries exist in the WordPress Comments table. %1$s <a href="%2$s">Learn more &raquo;</a>', 'action-scheduler' ),
 $next_cleanup_message,
 'https://github.com/woocommerce/action-scheduler/issues/368'
 );
 echo '<div class="notice notice-warning"><p>' . wp_kses_post( $notice ) . '</p></div>';
 }
}
