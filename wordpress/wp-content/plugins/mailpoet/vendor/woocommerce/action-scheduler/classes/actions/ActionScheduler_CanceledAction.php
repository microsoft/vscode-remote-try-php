<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_CanceledAction extends ActionScheduler_FinishedAction {
 public function __construct( $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {
 parent::__construct( $hook, $args, $schedule, $group );
 if ( is_null( $schedule ) ) {
 $this->set_schedule( new ActionScheduler_NullSchedule() );
 }
 }
}
