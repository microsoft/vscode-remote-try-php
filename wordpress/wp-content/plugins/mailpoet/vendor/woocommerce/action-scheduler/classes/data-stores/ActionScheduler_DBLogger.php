<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_DBLogger extends ActionScheduler_Logger {
 public function log( $action_id, $message, DateTime $date = null ) {
 if ( empty( $date ) ) {
 $date = as_get_datetime_object();
 } else {
 $date = clone $date;
 }
 $date_gmt = $date->format( 'Y-m-d H:i:s' );
 ActionScheduler_TimezoneHelper::set_local_timezone( $date );
 $date_local = $date->format( 'Y-m-d H:i:s' );
 //phpcs:ignore Generic.Commenting.DocComment.MissingShort
 global $wpdb;
 $wpdb->insert(
 $wpdb->actionscheduler_logs,
 array(
 'action_id' => $action_id,
 'message' => $message,
 'log_date_gmt' => $date_gmt,
 'log_date_local' => $date_local,
 ),
 array( '%d', '%s', '%s', '%s' )
 );
 return $wpdb->insert_id;
 }
 public function get_entry( $entry_id ) {
 //phpcs:ignore Generic.Commenting.DocComment.MissingShort
 global $wpdb;
 $entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->actionscheduler_logs} WHERE log_id=%d", $entry_id ) );
 return $this->create_entry_from_db_record( $entry );
 }
 private function create_entry_from_db_record( $record ) {
 if ( empty( $record ) ) {
 return new ActionScheduler_NullLogEntry();
 }
 if ( is_null( $record->log_date_gmt ) ) {
 $date = as_get_datetime_object( ActionScheduler_StoreSchema::DEFAULT_DATE );
 } else {
 $date = as_get_datetime_object( $record->log_date_gmt );
 }
 return new ActionScheduler_LogEntry( $record->action_id, $record->message, $date );
 }
 public function get_logs( $action_id ) {
 //phpcs:ignore Generic.Commenting.DocComment.MissingShort
 global $wpdb;
 $records = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->actionscheduler_logs} WHERE action_id=%d", $action_id ) );
 return array_map( array( $this, 'create_entry_from_db_record' ), $records );
 }
 public function init() {
 $table_maker = new ActionScheduler_LoggerSchema();
 $table_maker->init();
 $table_maker->register_tables();
 parent::init();
 add_action( 'action_scheduler_deleted_action', array( $this, 'clear_deleted_action_logs' ), 10, 1 );
 }
 public function clear_deleted_action_logs( $action_id ) {
 //phpcs:ignore Generic.Commenting.DocComment.MissingShort
 global $wpdb;
 $wpdb->delete( $wpdb->actionscheduler_logs, array( 'action_id' => $action_id ), array( '%d' ) );
 }
 public function bulk_log_cancel_actions( $action_ids ) {
 if ( empty( $action_ids ) ) {
 return;
 }
 //phpcs:ignore Generic.Commenting.DocComment.MissingShort
 global $wpdb;
 $date = as_get_datetime_object();
 $date_gmt = $date->format( 'Y-m-d H:i:s' );
 ActionScheduler_TimezoneHelper::set_local_timezone( $date );
 $date_local = $date->format( 'Y-m-d H:i:s' );
 $message = __( 'action canceled', 'action-scheduler' );
 $format = '(%d, ' . $wpdb->prepare( '%s, %s, %s', $message, $date_gmt, $date_local ) . ')';
 $sql_query = "INSERT {$wpdb->actionscheduler_logs} (action_id, message, log_date_gmt, log_date_local) VALUES ";
 $value_rows = array();
 foreach ( $action_ids as $action_id ) {
 $value_rows[] = $wpdb->prepare( $format, $action_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
 }
 $sql_query .= implode( ',', $value_rows );
 $wpdb->query( $sql_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
 }
}
