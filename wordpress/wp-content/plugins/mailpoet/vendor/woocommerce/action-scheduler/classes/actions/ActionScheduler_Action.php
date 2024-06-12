<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_Action {
 protected $hook = '';
 protected $args = array();
 protected $schedule = NULL;
 protected $group = '';
 protected $priority = 10;
 public function __construct( $hook, array $args = array(), ActionScheduler_Schedule $schedule = NULL, $group = '' ) {
 $schedule = empty( $schedule ) ? new ActionScheduler_NullSchedule() : $schedule;
 $this->set_hook($hook);
 $this->set_schedule($schedule);
 $this->set_args($args);
 $this->set_group($group);
 }
 public function execute() {
 $hook = $this->get_hook();
 if ( ! has_action( $hook ) ) {
 throw new Exception(
 sprintf(
 __( 'Scheduled action for %1$s will not be executed as no callbacks are registered.', 'action-scheduler' ),
 $hook
 )
 );
 }
 do_action_ref_array( $hook, array_values( $this->get_args() ) );
 }
 protected function set_hook( $hook ) {
 $this->hook = $hook;
 }
 public function get_hook() {
 return $this->hook;
 }
 protected function set_schedule( ActionScheduler_Schedule $schedule ) {
 $this->schedule = $schedule;
 }
 public function get_schedule() {
 return $this->schedule;
 }
 protected function set_args( array $args ) {
 $this->args = $args;
 }
 public function get_args() {
 return $this->args;
 }
 protected function set_group( $group ) {
 $this->group = $group;
 }
 public function get_group() {
 return $this->group;
 }
 public function is_finished() {
 return FALSE;
 }
 public function set_priority( $priority ) {
 if ( $priority < 0 ) {
 $priority = 0;
 } elseif ( $priority > 255 ) {
 $priority = 255;
 }
 $this->priority = (int) $priority;
 }
 public function get_priority() {
 return $this->priority;
 }
}
