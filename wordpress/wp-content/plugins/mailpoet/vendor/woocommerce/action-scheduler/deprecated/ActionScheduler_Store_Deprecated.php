<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Store_Deprecated {
 public function mark_failed_fetch_action( $action_id ) {
 _deprecated_function( __METHOD__, '3.0.0', 'ActionScheduler_Store::mark_failure()' );
 self::$store->mark_failure( $action_id );
 }
 protected static function hook() {
 _deprecated_function( __METHOD__, '3.0.0' );
 }
 protected static function unhook() {
 _deprecated_function( __METHOD__, '3.0.0' );
 }
 protected function get_local_timezone() {
 _deprecated_function( __FUNCTION__, '2.1.0', 'ActionScheduler_TimezoneHelper::set_local_timezone()' );
 return ActionScheduler_TimezoneHelper::get_local_timezone();
 }
}
