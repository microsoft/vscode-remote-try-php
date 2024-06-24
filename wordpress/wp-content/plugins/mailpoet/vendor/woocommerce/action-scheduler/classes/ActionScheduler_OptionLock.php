<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_OptionLock extends ActionScheduler_Lock {
 public function set( $lock_type ) {
 global $wpdb;
 $lock_key = $this->get_key( $lock_type );
 $existing_lock_value = $this->get_existing_lock( $lock_type );
 $new_lock_value = $this->new_lock_value( $lock_type );
 // The lock may not exist yet, or may have been deleted.
 if ( empty( $existing_lock_value ) ) {
 return (bool) $wpdb->insert(
 $wpdb->options,
 array(
 'option_name' => $lock_key,
 'option_value' => $new_lock_value,
 'autoload' => 'no',
 )
 );
 }
 if ( $this->get_expiration_from( $existing_lock_value ) >= time() ) {
 return false;
 }
 // Otherwise, try to obtain the lock.
 return (bool) $wpdb->update(
 $wpdb->options,
 array( 'option_value' => $new_lock_value ),
 array(
 'option_name' => $lock_key,
 'option_value' => $existing_lock_value,
 )
 );
 }
 public function get_expiration( $lock_type ) {
 return $this->get_expiration_from( $this->get_existing_lock( $lock_type ) );
 }
 private function get_expiration_from( $lock_value ) {
 $lock_string = explode( '|', $lock_value );
 // Old style lock?
 if ( count( $lock_string ) === 1 && is_numeric( $lock_string[0] ) ) {
 return (int) $lock_string[0];
 }
 // New style lock?
 if ( count( $lock_string ) === 2 && is_numeric( $lock_string[1] ) ) {
 return (int) $lock_string[1];
 }
 return false;
 }
 protected function get_key( $lock_type ) {
 return sprintf( 'action_scheduler_lock_%s', $lock_type );
 }
 private function get_existing_lock( $lock_type ) {
 global $wpdb;
 // Now grab the existing lock value, if there is one.
 return (string) $wpdb->get_var(
 $wpdb->prepare(
 "SELECT option_value FROM $wpdb->options WHERE option_name = %s",
 $this->get_key( $lock_type )
 )
 );
 }
 private function new_lock_value( $lock_type ) {
 return uniqid( '', true ) . '|' . ( time() + $this->get_duration( $lock_type ) );
 }
}
