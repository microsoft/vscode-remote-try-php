<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_ActionFactory {
 public function get_stored_action( $status, $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {
 // The 6th parameter ($priority) is not formally declared in the method signature to maintain compatibility with
 // third-party subclasses created before this param was added.
 $priority = func_num_args() >= 6 ? (int) func_get_arg( 5 ) : 10;
 switch ( $status ) {
 case ActionScheduler_Store::STATUS_PENDING:
 $action_class = 'ActionScheduler_Action';
 break;
 case ActionScheduler_Store::STATUS_CANCELED:
 $action_class = 'ActionScheduler_CanceledAction';
 if ( ! is_null( $schedule ) && ! is_a( $schedule, 'ActionScheduler_CanceledSchedule' ) && ! is_a( $schedule, 'ActionScheduler_NullSchedule' ) ) {
 $schedule = new ActionScheduler_CanceledSchedule( $schedule->get_date() );
 }
 break;
 default:
 $action_class = 'ActionScheduler_FinishedAction';
 break;
 }
 $action_class = apply_filters( 'action_scheduler_stored_action_class', $action_class, $status, $hook, $args, $schedule, $group );
 $action = new $action_class( $hook, $args, $schedule, $group );
 $action->set_priority( $priority );
 return apply_filters( 'action_scheduler_stored_action_instance', $action, $hook, $args, $schedule, $group, $priority );
 }
 public function async( $hook, $args = array(), $group = '' ) {
 return $this->async_unique( $hook, $args, $group, false );
 }
 public function async_unique( $hook, $args = array(), $group = '', $unique = true ) {
 $schedule = new ActionScheduler_NullSchedule();
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $unique ? $this->store_unique_action( $action, $unique ) : $this->store( $action );
 }
 public function single( $hook, $args = array(), $when = null, $group = '' ) {
 return $this->single_unique( $hook, $args, $when, $group, false );
 }
 public function single_unique( $hook, $args = array(), $when = null, $group = '', $unique = true ) {
 $date = as_get_datetime_object( $when );
 $schedule = new ActionScheduler_SimpleSchedule( $date );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
 }
 public function recurring( $hook, $args = array(), $first = null, $interval = null, $group = '' ) {
 return $this->recurring_unique( $hook, $args, $first, $interval, $group, false );
 }
 public function recurring_unique( $hook, $args = array(), $first = null, $interval = null, $group = '', $unique = true ) {
 if ( empty( $interval ) ) {
 return $this->single_unique( $hook, $args, $first, $group, $unique );
 }
 $date = as_get_datetime_object( $first );
 $schedule = new ActionScheduler_IntervalSchedule( $date, $interval );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
 }
 public function cron( $hook, $args = array(), $base_timestamp = null, $schedule = null, $group = '' ) {
 return $this->cron_unique( $hook, $args, $base_timestamp, $schedule, $group, false );
 }
 public function cron_unique( $hook, $args = array(), $base_timestamp = null, $schedule = null, $group = '', $unique = true ) {
 if ( empty( $schedule ) ) {
 return $this->single_unique( $hook, $args, $base_timestamp, $group, $unique );
 }
 $date = as_get_datetime_object( $base_timestamp );
 $cron = CronExpression::factory( $schedule );
 $schedule = new ActionScheduler_CronSchedule( $date, $cron );
 $action = new ActionScheduler_Action( $hook, $args, $schedule, $group );
 return $unique ? $this->store_unique_action( $action ) : $this->store( $action );
 }
 public function repeat( $action ) {
 $schedule = $action->get_schedule();
 $next = $schedule->get_next( as_get_datetime_object() );
 if ( is_null( $next ) || ! $schedule->is_recurring() ) {
 throw new InvalidArgumentException( __( 'Invalid action - must be a recurring action.', 'action-scheduler' ) );
 }
 $schedule_class = get_class( $schedule );
 $new_schedule = new $schedule( $next, $schedule->get_recurrence(), $schedule->get_first_date() );
 $new_action = new ActionScheduler_Action( $action->get_hook(), $action->get_args(), $new_schedule, $action->get_group() );
 $new_action->set_priority( $action->get_priority() );
 return $this->store( $new_action );
 }
 public function create( array $options = array() ) {
 $defaults = array(
 'type' => 'single',
 'hook' => '',
 'arguments' => array(),
 'group' => '',
 'unique' => false,
 'when' => time(),
 'pattern' => null,
 'priority' => 10,
 );
 $options = array_merge( $defaults, $options );
 // Cron/recurring actions without a pattern are treated as single actions (this gives calling code the ability
 // to use functions like as_schedule_recurring_action() to schedule recurring as well as single actions).
 if ( ( 'cron' === $options['type'] || 'recurring' === $options['type'] ) && empty( $options['pattern'] ) ) {
 $options['type'] = 'single';
 }
 switch ( $options['type'] ) {
 case 'async':
 $schedule = new ActionScheduler_NullSchedule();
 break;
 case 'cron':
 $date = as_get_datetime_object( $options['when'] );
 $cron = CronExpression::factory( $options['pattern'] );
 $schedule = new ActionScheduler_CronSchedule( $date, $cron );
 break;
 case 'recurring':
 $date = as_get_datetime_object( $options['when'] );
 $schedule = new ActionScheduler_IntervalSchedule( $date, $options['pattern'] );
 break;
 case 'single':
 $date = as_get_datetime_object( $options['when'] );
 $schedule = new ActionScheduler_SimpleSchedule( $date );
 break;
 default:
 error_log( "Unknown action type '{$options['type']}' specified when trying to create an action for '{$options['hook']}'." );
 return 0;
 }
 $action = new ActionScheduler_Action( $options['hook'], $options['arguments'], $schedule, $options['group'] );
 $action->set_priority( $options['priority'] );
 $action_id = 0;
 try {
 $action_id = $options['unique'] ? $this->store_unique_action( $action ) : $this->store( $action );
 } catch ( Exception $e ) {
 error_log(
 sprintf(
 __( 'Caught exception while enqueuing action "%1$s": %2$s', 'action-scheduler' ),
 $options['hook'],
 $e->getMessage()
 )
 );
 }
 return $action_id;
 }
 protected function store( ActionScheduler_Action $action ) {
 $store = ActionScheduler_Store::instance();
 return $store->save_action( $action );
 }
 protected function store_unique_action( ActionScheduler_Action $action ) {
 $store = ActionScheduler_Store::instance();
 if ( method_exists( $store, 'save_unique_action' ) ) {
 return $store->save_unique_action( $action );
 } else {
 $existing_action_id = (int) $store->find_action(
 $action->get_hook(),
 array(
 'args' => $action->get_args(),
 'status' => ActionScheduler_Store::STATUS_PENDING,
 'group' => $action->get_group(),
 )
 );
 if ( $existing_action_id > 0 ) {
 return 0;
 }
 return $store->save_action( $action );
 }
 }
}
