<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_ListTable extends ActionScheduler_Abstract_ListTable {
 protected $package = 'action-scheduler';
 protected $columns = array();
 protected $row_actions = array();
 protected $store;
 protected $logger;
 protected $runner;
 protected $bulk_actions = array();
 protected static $did_notification = false;
 private static $time_periods;
 public function __construct( ActionScheduler_Store $store, ActionScheduler_Logger $logger, ActionScheduler_QueueRunner $runner ) {
 $this->store = $store;
 $this->logger = $logger;
 $this->runner = $runner;
 $this->table_header = __( 'Scheduled Actions', 'action-scheduler' );
 $this->bulk_actions = array(
 'delete' => __( 'Delete', 'action-scheduler' ),
 );
 $this->columns = array(
 'hook' => __( 'Hook', 'action-scheduler' ),
 'status' => __( 'Status', 'action-scheduler' ),
 'args' => __( 'Arguments', 'action-scheduler' ),
 'group' => __( 'Group', 'action-scheduler' ),
 'recurrence' => __( 'Recurrence', 'action-scheduler' ),
 'schedule' => __( 'Scheduled Date', 'action-scheduler' ),
 'log_entries' => __( 'Log', 'action-scheduler' ),
 );
 $this->sort_by = array(
 'schedule',
 'hook',
 'group',
 );
 $this->search_by = array(
 'hook',
 'args',
 'claim_id',
 );
 $request_status = $this->get_request_status();
 if ( empty( $request_status ) ) {
 $this->sort_by[] = 'status';
 } elseif ( in_array( $request_status, array( 'in-progress', 'failed' ) ) ) {
 $this->columns += array( 'claim_id' => __( 'Claim ID', 'action-scheduler' ) );
 $this->sort_by[] = 'claim_id';
 }
 $this->row_actions = array(
 'hook' => array(
 'run' => array(
 'name' => __( 'Run', 'action-scheduler' ),
 'desc' => __( 'Process the action now as if it were run as part of a queue', 'action-scheduler' ),
 ),
 'cancel' => array(
 'name' => __( 'Cancel', 'action-scheduler' ),
 'desc' => __( 'Cancel the action now to avoid it being run in future', 'action-scheduler' ),
 'class' => 'cancel trash',
 ),
 ),
 );
 self::$time_periods = array(
 array(
 'seconds' => YEAR_IN_SECONDS,
 'names' => _n_noop( '%s year', '%s years', 'action-scheduler' ),
 ),
 array(
 'seconds' => MONTH_IN_SECONDS,
 'names' => _n_noop( '%s month', '%s months', 'action-scheduler' ),
 ),
 array(
 'seconds' => WEEK_IN_SECONDS,
 'names' => _n_noop( '%s week', '%s weeks', 'action-scheduler' ),
 ),
 array(
 'seconds' => DAY_IN_SECONDS,
 'names' => _n_noop( '%s day', '%s days', 'action-scheduler' ),
 ),
 array(
 'seconds' => HOUR_IN_SECONDS,
 'names' => _n_noop( '%s hour', '%s hours', 'action-scheduler' ),
 ),
 array(
 'seconds' => MINUTE_IN_SECONDS,
 'names' => _n_noop( '%s minute', '%s minutes', 'action-scheduler' ),
 ),
 array(
 'seconds' => 1,
 'names' => _n_noop( '%s second', '%s seconds', 'action-scheduler' ),
 ),
 );
 parent::__construct(
 array(
 'singular' => 'action-scheduler',
 'plural' => 'action-scheduler',
 'ajax' => false,
 )
 );
 add_screen_option(
 'per_page',
 array(
 'default' => $this->items_per_page,
 )
 );
 add_filter( 'set_screen_option_' . $this->get_per_page_option_name(), array( $this, 'set_items_per_page_option' ), 10, 3 );
 set_screen_options();
 }
 public function set_items_per_page_option( $status, $option, $value ) {
 return $value;
 }
 private static function human_interval( $interval, $periods_to_include = 2 ) {
 if ( $interval <= 0 ) {
 return __( 'Now!', 'action-scheduler' );
 }
 $output = '';
 for ( $time_period_index = 0, $periods_included = 0, $seconds_remaining = $interval; $time_period_index < count( self::$time_periods ) && $seconds_remaining > 0 && $periods_included < $periods_to_include; $time_period_index++ ) {
 $periods_in_interval = floor( $seconds_remaining / self::$time_periods[ $time_period_index ]['seconds'] );
 if ( $periods_in_interval > 0 ) {
 if ( ! empty( $output ) ) {
 $output .= ' ';
 }
 $output .= sprintf( translate_nooped_plural( self::$time_periods[ $time_period_index ]['names'], $periods_in_interval, 'action-scheduler' ), $periods_in_interval );
 $seconds_remaining -= $periods_in_interval * self::$time_periods[ $time_period_index ]['seconds'];
 $periods_included++;
 }
 }
 return $output;
 }
 protected function get_recurrence( $action ) {
 $schedule = $action->get_schedule();
 if ( $schedule->is_recurring() && method_exists( $schedule, 'get_recurrence' ) ) {
 $recurrence = $schedule->get_recurrence();
 if ( is_numeric( $recurrence ) ) {
 return sprintf( __( 'Every %s', 'action-scheduler' ), self::human_interval( $recurrence ) );
 } else {
 return $recurrence;
 }
 }
 return __( 'Non-repeating', 'action-scheduler' );
 }
 public function column_args( array $row ) {
 if ( empty( $row['args'] ) ) {
 return apply_filters( 'action_scheduler_list_table_column_args', '', $row );
 }
 $row_html = '<ul>';
 foreach ( $row['args'] as $key => $value ) {
 $row_html .= sprintf( '<li><code>%s => %s</code></li>', esc_html( var_export( $key, true ) ), esc_html( var_export( $value, true ) ) );
 }
 $row_html .= '</ul>';
 return apply_filters( 'action_scheduler_list_table_column_args', $row_html, $row );
 }
 public function column_log_entries( array $row ) {
 $log_entries_html = '<ol>';
 $timezone = new DateTimezone( 'UTC' );
 foreach ( $row['log_entries'] as $log_entry ) {
 $log_entries_html .= $this->get_log_entry_html( $log_entry, $timezone );
 }
 $log_entries_html .= '</ol>';
 return $log_entries_html;
 }
 protected function get_log_entry_html( ActionScheduler_LogEntry $log_entry, DateTimezone $timezone ) {
 $date = $log_entry->get_date();
 $date->setTimezone( $timezone );
 return sprintf( '<li><strong>%s</strong><br/>%s</li>', esc_html( $date->format( 'Y-m-d H:i:s O' ) ), esc_html( $log_entry->get_message() ) );
 }
 protected function maybe_render_actions( $row, $column_name ) {
 if ( 'pending' === strtolower( $row[ 'status_name' ] ) ) {
 return parent::maybe_render_actions( $row, $column_name );
 }
 return '';
 }
 public function display_admin_notices() {
 global $wpdb;
 if ( ( is_a( $this->store, 'ActionScheduler_HybridStore' ) || is_a( $this->store, 'ActionScheduler_DBStore' ) ) && apply_filters( 'action_scheduler_enable_recreate_data_store', true ) ) {
 $table_list = array(
 'actionscheduler_actions',
 'actionscheduler_logs',
 'actionscheduler_groups',
 'actionscheduler_claims',
 );
 $found_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 foreach ( $table_list as $table_name ) {
 if ( ! in_array( $wpdb->prefix . $table_name, $found_tables ) ) {
 $this->admin_notices[] = array(
 'class' => 'error',
 'message' => __( 'It appears one or more database tables were missing. Attempting to re-create the missing table(s).' , 'action-scheduler' ),
 );
 $this->recreate_tables();
 parent::display_admin_notices();
 return;
 }
 }
 }
 if ( $this->runner->has_maximum_concurrent_batches() ) {
 $claim_count = $this->store->get_claim_count();
 $this->admin_notices[] = array(
 'class' => 'updated',
 'message' => sprintf(
 _n(
 'Maximum simultaneous queues already in progress (%s queue). No additional queues will begin processing until the current queues are complete.',
 'Maximum simultaneous queues already in progress (%s queues). No additional queues will begin processing until the current queues are complete.',
 $claim_count,
 'action-scheduler'
 ),
 $claim_count
 ),
 );
 } elseif ( $this->store->has_pending_actions_due() ) {
 $async_request_lock_expiration = ActionScheduler::lock()->get_expiration( 'async-request-runner' );
 // No lock set or lock expired
 if ( false === $async_request_lock_expiration || $async_request_lock_expiration < time() ) {
 $in_progress_url = add_query_arg( 'status', 'in-progress', remove_query_arg( 'status' ) );
 $async_request_message = sprintf( __( 'A new queue has begun processing. <a href="%s">View actions in-progress &raquo;</a>', 'action-scheduler' ), esc_url( $in_progress_url ) );
 } else {
 $async_request_message = sprintf( __( 'The next queue will begin processing in approximately %d seconds.', 'action-scheduler' ), $async_request_lock_expiration - time() );
 }
 $this->admin_notices[] = array(
 'class' => 'notice notice-info',
 'message' => $async_request_message,
 );
 }
 $notification = get_transient( 'action_scheduler_admin_notice' );
 if ( is_array( $notification ) ) {
 delete_transient( 'action_scheduler_admin_notice' );
 $action = $this->store->fetch_action( $notification['action_id'] );
 $action_hook_html = '<strong><code>' . $action->get_hook() . '</code></strong>';
 if ( 1 == $notification['success'] ) {
 $class = 'updated';
 switch ( $notification['row_action_type'] ) {
 case 'run' :
 $action_message_html = sprintf( __( 'Successfully executed action: %s', 'action-scheduler' ), $action_hook_html );
 break;
 case 'cancel' :
 $action_message_html = sprintf( __( 'Successfully canceled action: %s', 'action-scheduler' ), $action_hook_html );
 break;
 default :
 $action_message_html = sprintf( __( 'Successfully processed change for action: %s', 'action-scheduler' ), $action_hook_html );
 break;
 }
 } else {
 $class = 'error';
 $action_message_html = sprintf( __( 'Could not process change for action: "%1$s" (ID: %2$d). Error: %3$s', 'action-scheduler' ), $action_hook_html, esc_html( $notification['action_id'] ), esc_html( $notification['error_message'] ) );
 }
 $action_message_html = apply_filters( 'action_scheduler_admin_notice_html', $action_message_html, $action, $notification );
 $this->admin_notices[] = array(
 'class' => $class,
 'message' => $action_message_html,
 );
 }
 parent::display_admin_notices();
 }
 public function column_schedule( $row ) {
 return $this->get_schedule_display_string( $row['schedule'] );
 }
 protected function get_schedule_display_string( ActionScheduler_Schedule $schedule ) {
 $schedule_display_string = '';
 if ( is_a( $schedule, 'ActionScheduler_NullSchedule' ) ) {
 return __( 'async', 'action-scheduler' );
 }
 if ( ! method_exists( $schedule, 'get_date' ) || ! $schedule->get_date() ) {
 return '0000-00-00 00:00:00';
 }
 $next_timestamp = $schedule->get_date()->getTimestamp();
 $schedule_display_string .= $schedule->get_date()->format( 'Y-m-d H:i:s O' );
 $schedule_display_string .= '<br/>';
 if ( gmdate( 'U' ) > $next_timestamp ) {
 $schedule_display_string .= sprintf( __( ' (%s ago)', 'action-scheduler' ), self::human_interval( gmdate( 'U' ) - $next_timestamp ) );
 } else {
 $schedule_display_string .= sprintf( __( ' (%s)', 'action-scheduler' ), self::human_interval( $next_timestamp - gmdate( 'U' ) ) );
 }
 return $schedule_display_string;
 }
 protected function bulk_delete( array $ids, $ids_sql ) {
 foreach ( $ids as $id ) {
 try {
 $this->store->delete_action( $id );
 } catch ( Exception $e ) {
 // A possible reason for an exception would include a scenario where the same action is deleted by a
 // concurrent request.
 error_log(
 sprintf(
 __( 'Action Scheduler was unable to delete action %1$d. Reason: %2$s', 'action-scheduler' ),
 $id,
 $e->getMessage()
 )
 );
 }
 }
 }
 protected function row_action_cancel( $action_id ) {
 $this->process_row_action( $action_id, 'cancel' );
 }
 protected function row_action_run( $action_id ) {
 $this->process_row_action( $action_id, 'run' );
 }
 protected function recreate_tables() {
 if ( is_a( $this->store, 'ActionScheduler_HybridStore' ) ) {
 $store = $this->store;
 } else {
 $store = new ActionScheduler_HybridStore();
 }
 add_action( 'action_scheduler/created_table', array( $store, 'set_autoincrement' ), 10, 2 );
 $store_schema = new ActionScheduler_StoreSchema();
 $logger_schema = new ActionScheduler_LoggerSchema();
 $store_schema->register_tables( true );
 $logger_schema->register_tables( true );
 remove_action( 'action_scheduler/created_table', array( $store, 'set_autoincrement' ), 10 );
 }
 protected function process_row_action( $action_id, $row_action_type ) {
 try {
 switch ( $row_action_type ) {
 case 'run' :
 $this->runner->process_action( $action_id, 'Admin List Table' );
 break;
 case 'cancel' :
 $this->store->cancel_action( $action_id );
 break;
 }
 $success = 1;
 $error_message = '';
 } catch ( Exception $e ) {
 $success = 0;
 $error_message = $e->getMessage();
 }
 set_transient( 'action_scheduler_admin_notice', compact( 'action_id', 'success', 'error_message', 'row_action_type' ), 30 );
 }
 public function prepare_items() {
 $this->prepare_column_headers();
 $per_page = $this->get_items_per_page( $this->get_per_page_option_name(), $this->items_per_page );
 $query = array(
 'per_page' => $per_page,
 'offset' => $this->get_items_offset(),
 'status' => $this->get_request_status(),
 'orderby' => $this->get_request_orderby(),
 'order' => $this->get_request_order(),
 'search' => $this->get_request_search_query(),
 );
 if ( 'past-due' === $this->get_request_status() ) {
 $query['status'] = ActionScheduler_Store::STATUS_PENDING;
 $query['date'] = as_get_datetime_object();
 }
 $this->items = array();
 $total_items = $this->store->query_actions( $query, 'count' );
 $status_labels = $this->store->get_status_labels();
 foreach ( $this->store->query_actions( $query ) as $action_id ) {
 try {
 $action = $this->store->fetch_action( $action_id );
 } catch ( Exception $e ) {
 continue;
 }
 if ( is_a( $action, 'ActionScheduler_NullAction' ) ) {
 continue;
 }
 $this->items[ $action_id ] = array(
 'ID' => $action_id,
 'hook' => $action->get_hook(),
 'status_name' => $this->store->get_status( $action_id ),
 'status' => $status_labels[ $this->store->get_status( $action_id ) ],
 'args' => $action->get_args(),
 'group' => $action->get_group(),
 'log_entries' => $this->logger->get_logs( $action_id ),
 'claim_id' => $this->store->get_claim_id( $action_id ),
 'recurrence' => $this->get_recurrence( $action ),
 'schedule' => $action->get_schedule(),
 );
 }
 $this->set_pagination_args( array(
 'total_items' => $total_items,
 'per_page' => $per_page,
 'total_pages' => ceil( $total_items / $per_page ),
 ) );
 }
 protected function display_filter_by_status() {
 $this->status_counts = $this->store->action_counts() + $this->store->extra_action_counts();
 parent::display_filter_by_status();
 }
 protected function get_search_box_button_text() {
 return __( 'Search hook, args and claim ID', 'action-scheduler' );
 }
 protected function get_per_page_option_name() {
 return str_replace( '-', '_', $this->screen->id ) . '_per_page';
 }
}
