<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Lock {
 private static $locker = NULL;
 protected static $lock_duration = MINUTE_IN_SECONDS;
 public function is_locked( $lock_type ) {
 return ( $this->get_expiration( $lock_type ) >= time() );
 }
 abstract public function set( $lock_type );
 abstract public function get_expiration( $lock_type );
 protected function get_duration( $lock_type ) {
 return apply_filters( 'action_scheduler_lock_duration', self::$lock_duration, $lock_type );
 }
 public static function instance() {
 if ( empty( self::$locker ) ) {
 $class = apply_filters( 'action_scheduler_lock_class', 'ActionScheduler_OptionLock' );
 self::$locker = new $class();
 }
 return self::$locker;
 }
}
