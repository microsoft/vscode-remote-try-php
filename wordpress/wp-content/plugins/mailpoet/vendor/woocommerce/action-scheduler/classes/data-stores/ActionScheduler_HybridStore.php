<?php
if (!defined('ABSPATH')) exit;
use ActionScheduler_Store as Store;
use Action_Scheduler\Migration\Runner;
use Action_Scheduler\Migration\Config;
use Action_Scheduler\Migration\Controller;
class ActionScheduler_HybridStore extends Store {
 const DEMARKATION_OPTION = 'action_scheduler_hybrid_store_demarkation';
 private $primary_store;
 private $secondary_store;
 private $migration_runner;
 private $demarkation_id = 0;
 public function __construct( Config $config = null ) {
 $this->demarkation_id = (int) get_option( self::DEMARKATION_OPTION, 0 );
 if ( empty( $config ) ) {
 $config = Controller::instance()->get_migration_config_object();
 }
 $this->primary_store = $config->get_destination_store();
 $this->secondary_store = $config->get_source_store();
 $this->migration_runner = new Runner( $config );
 }
 public function init() {
 add_action( 'action_scheduler/created_table', [ $this, 'set_autoincrement' ], 10, 2 );
 $this->primary_store->init();
 $this->secondary_store->init();
 remove_action( 'action_scheduler/created_table', [ $this, 'set_autoincrement' ], 10 );
 }
 public function set_autoincrement( $table_name, $table_suffix ) {
 if ( ActionScheduler_StoreSchema::ACTIONS_TABLE === $table_suffix ) {
 if ( empty( $this->demarkation_id ) ) {
 $this->demarkation_id = $this->set_demarkation_id();
 }
 global $wpdb;
 $default_date = new DateTime( 'tomorrow' );
 $null_action = new ActionScheduler_NullAction();
 $date_gmt = $this->get_scheduled_date_string( $null_action, $default_date );
 $date_local = $this->get_scheduled_date_string_local( $null_action, $default_date );
 $row_count = $wpdb->insert(
 $wpdb->{ActionScheduler_StoreSchema::ACTIONS_TABLE},
 [
 'action_id' => $this->demarkation_id,
 'hook' => '',
 'status' => '',
 'scheduled_date_gmt' => $date_gmt,
 'scheduled_date_local' => $date_local,
 'last_attempt_gmt' => $date_gmt,
 'last_attempt_local' => $date_local,
 ]
 );
 if ( $row_count > 0 ) {
 $wpdb->delete(
 $wpdb->{ActionScheduler_StoreSchema::ACTIONS_TABLE},
 [ 'action_id' => $this->demarkation_id ]
 );
 }
 }
 }
 private function set_demarkation_id( $id = null ) {
 if ( empty( $id ) ) {
 global $wpdb;
 $id = (int) $wpdb->get_var( "SELECT MAX(ID) FROM $wpdb->posts" );
 $id ++;
 }
 update_option( self::DEMARKATION_OPTION, $id );
 return $id;
 }
 public function find_action( $hook, $params = [] ) {
 $found_unmigrated_action = $this->secondary_store->find_action( $hook, $params );
 if ( ! empty( $found_unmigrated_action ) ) {
 $this->migrate( [ $found_unmigrated_action ] );
 }
 return $this->primary_store->find_action( $hook, $params );
 }
 public function query_actions( $query = [], $query_type = 'select' ) {
 $found_unmigrated_actions = $this->secondary_store->query_actions( $query, 'select' );
 if ( ! empty( $found_unmigrated_actions ) ) {
 $this->migrate( $found_unmigrated_actions );
 }
 return $this->primary_store->query_actions( $query, $query_type );
 }
 public function action_counts() {
 $unmigrated_actions_count = $this->secondary_store->action_counts();
 $migrated_actions_count = $this->primary_store->action_counts();
 $actions_count_by_status = array();
 foreach ( $this->get_status_labels() as $status_key => $status_label ) {
 $count = 0;
 if ( isset( $unmigrated_actions_count[ $status_key ] ) ) {
 $count += $unmigrated_actions_count[ $status_key ];
 }
 if ( isset( $migrated_actions_count[ $status_key ] ) ) {
 $count += $migrated_actions_count[ $status_key ];
 }
 $actions_count_by_status[ $status_key ] = $count;
 }
 $actions_count_by_status = array_filter( $actions_count_by_status );
 return $actions_count_by_status;
 }
 public function stake_claim( $max_actions = 10, DateTime $before_date = null, $hooks = array(), $group = '' ) {
 $claim = $this->secondary_store->stake_claim( $max_actions, $before_date, $hooks, $group );
 $claimed_actions = $claim->get_actions();
 if ( ! empty( $claimed_actions ) ) {
 $this->migrate( $claimed_actions );
 }
 $this->secondary_store->release_claim( $claim );
 return $this->primary_store->stake_claim( $max_actions, $before_date, $hooks, $group );
 }
 private function migrate( $action_ids ) {
 $this->migration_runner->migrate_actions( $action_ids );
 }
 public function save_action( ActionScheduler_Action $action, DateTime $date = null ) {
 return $this->primary_store->save_action( $action, $date );
 }
 public function fetch_action( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id, true );
 if ( $store ) {
 return $store->fetch_action( $action_id );
 } else {
 return new ActionScheduler_NullAction();
 }
 }
 public function cancel_action( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 $store->cancel_action( $action_id );
 }
 }
 public function delete_action( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 $store->delete_action( $action_id );
 }
 }
 public function get_date( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 return $store->get_date( $action_id );
 } else {
 return null;
 }
 }
 public function mark_failure( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 $store->mark_failure( $action_id );
 }
 }
 public function log_execution( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 $store->log_execution( $action_id );
 }
 }
 public function mark_complete( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 $store->mark_complete( $action_id );
 }
 }
 public function get_status( $action_id ) {
 $store = $this->get_store_from_action_id( $action_id );
 if ( $store ) {
 return $store->get_status( $action_id );
 }
 return null;
 }
 protected function get_store_from_action_id( $action_id, $primary_first = false ) {
 if ( $primary_first ) {
 $stores = [
 $this->primary_store,
 $this->secondary_store,
 ];
 } elseif ( $action_id < $this->demarkation_id ) {
 $stores = [
 $this->secondary_store,
 $this->primary_store,
 ];
 } else {
 $stores = [
 $this->primary_store,
 ];
 }
 foreach ( $stores as $store ) {
 $action = $store->fetch_action( $action_id );
 if ( ! is_a( $action, 'ActionScheduler_NullAction' ) ) {
 return $store;
 }
 }
 return null;
 }
 public function get_claim_count() {
 return $this->primary_store->get_claim_count();
 }
 public function get_claim_id( $action_id ) {
 return $this->primary_store->get_claim_id( $action_id );
 }
 public function release_claim( ActionScheduler_ActionClaim $claim ) {
 $this->primary_store->release_claim( $claim );
 }
 public function unclaim_action( $action_id ) {
 $this->primary_store->unclaim_action( $action_id );
 }
 public function find_actions_by_claim_id( $claim_id ) {
 return $this->primary_store->find_actions_by_claim_id( $claim_id );
 }
}
