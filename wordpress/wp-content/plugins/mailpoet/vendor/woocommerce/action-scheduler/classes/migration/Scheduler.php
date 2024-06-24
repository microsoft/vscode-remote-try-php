<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
class Scheduler {
 const HOOK = 'action_scheduler/migration_hook';
 const GROUP = 'action-scheduler-migration';
 public function hook() {
 add_action( self::HOOK, array( $this, 'run_migration' ), 10, 0 );
 }
 public function unhook() {
 remove_action( self::HOOK, array( $this, 'run_migration' ), 10 );
 }
 public function run_migration() {
 $migration_runner = $this->get_migration_runner();
 $count = $migration_runner->run( $this->get_batch_size() );
 if ( $count === 0 ) {
 $this->mark_complete();
 } else {
 $this->schedule_migration( time() + $this->get_schedule_interval() );
 }
 }
 public function mark_complete() {
 $this->unschedule_migration();
 \ActionScheduler_DataController::mark_migration_complete();
 do_action( 'action_scheduler/migration_complete' );
 }
 public function is_migration_scheduled() {
 $next = as_next_scheduled_action( self::HOOK );
 return ! empty( $next );
 }
 public function schedule_migration( $when = 0 ) {
 $next = as_next_scheduled_action( self::HOOK );
 if ( ! empty( $next ) ) {
 return $next;
 }
 if ( empty( $when ) ) {
 $when = time() + MINUTE_IN_SECONDS;
 }
 return as_schedule_single_action( $when, self::HOOK, array(), self::GROUP );
 }
 public function unschedule_migration() {
 as_unschedule_action( self::HOOK, null, self::GROUP );
 }
 private function get_schedule_interval() {
 return (int) apply_filters( 'action_scheduler/migration_interval', 0 );
 }
 private function get_batch_size() {
 return (int) apply_filters( 'action_scheduler/migration_batch_size', 250 );
 }
 private function get_migration_runner() {
 $config = Controller::instance()->get_migration_config_object();
 return new Runner( $config );
 }
}
