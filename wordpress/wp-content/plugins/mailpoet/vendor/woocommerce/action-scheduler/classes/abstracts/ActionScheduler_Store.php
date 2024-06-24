<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Store extends ActionScheduler_Store_Deprecated {
 const STATUS_COMPLETE = 'complete';
 const STATUS_PENDING = 'pending';
 const STATUS_RUNNING = 'in-progress';
 const STATUS_FAILED = 'failed';
 const STATUS_CANCELED = 'canceled';
 const DEFAULT_CLASS = 'ActionScheduler_wpPostStore';
 private static $store = NULL;
 protected static $max_args_length = 191;
 abstract public function save_action( ActionScheduler_Action $action, DateTime $scheduled_date = NULL );
 abstract public function fetch_action( $action_id );
 public function find_action( $hook, $params = array() ) {
 $params = wp_parse_args(
 $params,
 array(
 'args' => null,
 'status' => self::STATUS_PENDING,
 'group' => '',
 )
 );
 // These params are fixed for this method.
 $params['hook'] = $hook;
 $params['orderby'] = 'date';
 $params['per_page'] = 1;
 if ( ! empty( $params['status'] ) ) {
 if ( self::STATUS_PENDING === $params['status'] ) {
 $params['order'] = 'ASC'; // Find the next action that matches.
 } else {
 $params['order'] = 'DESC'; // Find the most recent action that matches.
 }
 }
 $results = $this->query_actions( $params );
 return empty( $results ) ? null : $results[0];
 }
 abstract public function query_actions( $query = array(), $query_type = 'select' );
 public function query_action( $query ) {
 $query['per_page'] = 1;
 $query['offset'] = 0;
 $results = $this->query_actions( $query );
 if ( empty( $results ) ) {
 return null;
 } else {
 return (int) $results[0];
 }
 }
 abstract public function action_counts();
 public function extra_action_counts() {
 $extra_actions = array();
 $pastdue_action_counts = ( int ) $this->query_actions( array(
 'status' => self::STATUS_PENDING,
 'date' => as_get_datetime_object(),
 ), 'count' );
 if ( $pastdue_action_counts ) {
 $extra_actions['past-due'] = $pastdue_action_counts;
 }
 return apply_filters( 'action_scheduler_extra_action_counts', $extra_actions );
 }
 abstract public function cancel_action( $action_id );
 abstract public function delete_action( $action_id );
 abstract public function get_date( $action_id );
 abstract public function stake_claim( $max_actions = 10, DateTime $before_date = null, $hooks = array(), $group = '' );
 abstract public function get_claim_count();
 abstract public function release_claim( ActionScheduler_ActionClaim $claim );
 abstract public function unclaim_action( $action_id );
 abstract public function mark_failure( $action_id );
 abstract public function log_execution( $action_id );
 abstract public function mark_complete( $action_id );
 abstract public function get_status( $action_id );
 abstract public function get_claim_id( $action_id );
 abstract public function find_actions_by_claim_id( $claim_id );
 protected function validate_sql_comparator( $comparison_operator ) {
 if ( in_array( $comparison_operator, array('!=', '>', '>=', '<', '<=', '=') ) ) {
 return $comparison_operator;
 }
 return '=';
 }
 protected function get_scheduled_date_string( ActionScheduler_Action $action, DateTime $scheduled_date = NULL ) {
 $next = null === $scheduled_date ? $action->get_schedule()->get_date() : $scheduled_date;
 if ( ! $next ) {
 $next = date_create();
 }
 $next->setTimezone( new DateTimeZone( 'UTC' ) );
 return $next->format( 'Y-m-d H:i:s' );
 }
 protected function get_scheduled_date_string_local( ActionScheduler_Action $action, DateTime $scheduled_date = NULL ) {
 $next = null === $scheduled_date ? $action->get_schedule()->get_date() : $scheduled_date;
 if ( ! $next ) {
 $next = date_create();
 }
 ActionScheduler_TimezoneHelper::set_local_timezone( $next );
 return $next->format( 'Y-m-d H:i:s' );
 }
 protected function validate_args( $args, $action_id ) {
 // Ensure we have an array of args.
 if ( ! is_array( $args ) ) {
 throw ActionScheduler_InvalidActionException::from_decoding_args( $action_id );
 }
 // Validate JSON decoding if possible.
 if ( function_exists( 'json_last_error' ) && JSON_ERROR_NONE !== json_last_error() ) {
 throw ActionScheduler_InvalidActionException::from_decoding_args( $action_id, $args );
 }
 }
 protected function validate_schedule( $schedule, $action_id ) {
 if ( empty( $schedule ) || ! is_a( $schedule, 'ActionScheduler_Schedule' ) ) {
 throw ActionScheduler_InvalidActionException::from_schedule( $action_id, $schedule );
 }
 }
 protected function validate_action( ActionScheduler_Action $action ) {
 if ( strlen( wp_json_encode( $action->get_args() ) ) > static::$max_args_length ) {
 // translators: %d is a number (maximum length of action arguments).
 throw new InvalidArgumentException( sprintf( __( 'ActionScheduler_Action::$args too long. To ensure the args column can be indexed, action args should not be more than %d characters when encoded as JSON.', 'action-scheduler' ), static::$max_args_length ) );
 }
 }
 public function cancel_actions_by_hook( $hook ) {
 $action_ids = true;
 while ( ! empty( $action_ids ) ) {
 $action_ids = $this->query_actions(
 array(
 'hook' => $hook,
 'status' => self::STATUS_PENDING,
 'per_page' => 1000,
 'orderby' => 'none',
 )
 );
 $this->bulk_cancel_actions( $action_ids );
 }
 }
 public function cancel_actions_by_group( $group ) {
 $action_ids = true;
 while ( ! empty( $action_ids ) ) {
 $action_ids = $this->query_actions(
 array(
 'group' => $group,
 'status' => self::STATUS_PENDING,
 'per_page' => 1000,
 'orderby' => 'none',
 )
 );
 $this->bulk_cancel_actions( $action_ids );
 }
 }
 private function bulk_cancel_actions( $action_ids ) {
 foreach ( $action_ids as $action_id ) {
 $this->cancel_action( $action_id );
 }
 do_action( 'action_scheduler_bulk_cancel_actions', $action_ids );
 }
 public function get_status_labels() {
 return array(
 self::STATUS_COMPLETE => __( 'Complete', 'action-scheduler' ),
 self::STATUS_PENDING => __( 'Pending', 'action-scheduler' ),
 self::STATUS_RUNNING => __( 'In-progress', 'action-scheduler' ),
 self::STATUS_FAILED => __( 'Failed', 'action-scheduler' ),
 self::STATUS_CANCELED => __( 'Canceled', 'action-scheduler' ),
 );
 }
 public function has_pending_actions_due() {
 $pending_actions = $this->query_actions( array(
 'date' => as_get_datetime_object(),
 'status' => ActionScheduler_Store::STATUS_PENDING,
 'orderby' => 'none',
 ) );
 return ! empty( $pending_actions );
 }
 public function init() {}
 public function mark_migrated( $action_id ) {}
 public static function instance() {
 if ( empty( self::$store ) ) {
 $class = apply_filters( 'action_scheduler_store_class', self::DEFAULT_CLASS );
 self::$store = new $class();
 }
 return self::$store;
 }
}
