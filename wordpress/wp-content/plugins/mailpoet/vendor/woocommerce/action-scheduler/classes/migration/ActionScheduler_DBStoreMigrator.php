<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_DBStoreMigrator extends ActionScheduler_DBStore {
 public function save_action( ActionScheduler_Action $action, \DateTime $scheduled_date = null, \DateTime $last_attempt_date = null ){
 try {
 global $wpdb;
 $action_id = parent::save_action( $action, $scheduled_date );
 if ( null !== $last_attempt_date ) {
 $data = [
 'last_attempt_gmt' => $this->get_scheduled_date_string( $action, $last_attempt_date ),
 'last_attempt_local' => $this->get_scheduled_date_string_local( $action, $last_attempt_date ),
 ];
 $wpdb->update( $wpdb->actionscheduler_actions, $data, array( 'action_id' => $action_id ), array( '%s', '%s' ), array( '%d' ) );
 }
 return $action_id;
 } catch ( \Exception $e ) {
 // translators: %s is an error message.
 throw new \RuntimeException( sprintf( __( 'Error saving action: %s', 'action-scheduler' ), $e->getMessage() ), 0 );
 }
 }
}
