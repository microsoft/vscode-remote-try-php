<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_LoggerSchema extends ActionScheduler_Abstract_Schema {
 const LOG_TABLE = 'actionscheduler_logs';
 protected $schema_version = 3;
 public function __construct() {
 $this->tables = [
 self::LOG_TABLE,
 ];
 }
 public function init() {
 add_action( 'action_scheduler_before_schema_update', array( $this, 'update_schema_3_0' ), 10, 2 );
 }
 protected function get_table_definition( $table ) {
 global $wpdb;
 $table_name = $wpdb->$table;
 $charset_collate = $wpdb->get_charset_collate();
 switch ( $table ) {
 case self::LOG_TABLE:
 $default_date = ActionScheduler_StoreSchema::DEFAULT_DATE;
 return "CREATE TABLE $table_name (
 log_id bigint(20) unsigned NOT NULL auto_increment,
 action_id bigint(20) unsigned NOT NULL,
 message text NOT NULL,
 log_date_gmt datetime NULL default '{$default_date}',
 log_date_local datetime NULL default '{$default_date}',
 PRIMARY KEY (log_id),
 KEY action_id (action_id),
 KEY log_date_gmt (log_date_gmt)
 ) $charset_collate";
 default:
 return '';
 }
 }
 public function update_schema_3_0( $table, $db_version ) {
 global $wpdb;
 if ( 'actionscheduler_logs' !== $table || version_compare( $db_version, '3', '>=' ) ) {
 return;
 }
 // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 $table_name = $wpdb->prefix . 'actionscheduler_logs';
 $table_list = $wpdb->get_col( "SHOW TABLES LIKE '{$table_name}'" );
 $default_date = ActionScheduler_StoreSchema::DEFAULT_DATE;
 if ( ! empty( $table_list ) ) {
 $query = "
 ALTER TABLE {$table_name}
 MODIFY COLUMN log_date_gmt datetime NULL default '{$default_date}',
 MODIFY COLUMN log_date_local datetime NULL default '{$default_date}'
 ";
 $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
 }
 // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 }
}
