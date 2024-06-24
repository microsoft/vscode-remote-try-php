<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_InvalidActionException extends \InvalidArgumentException implements ActionScheduler_Exception {
 public static function from_schedule( $action_id, $schedule ) {
 $message = sprintf(
 __( 'Action [%1$s] has an invalid schedule: %2$s', 'action-scheduler' ),
 $action_id,
 var_export( $schedule, true )
 );
 return new static( $message );
 }
 public static function from_decoding_args( $action_id, $args = array() ) {
 $message = sprintf(
 __( 'Action [%1$s] has invalid arguments. It cannot be JSON decoded to an array. $args = %2$s', 'action-scheduler' ),
 $action_id,
 var_export( $args, true )
 );
 return new static( $message );
 }
}
