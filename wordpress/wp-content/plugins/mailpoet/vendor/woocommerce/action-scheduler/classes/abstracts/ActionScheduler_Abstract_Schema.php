<?php
if (!defined('ABSPATH')) exit;
abstract class ActionScheduler_Abstract_Schema {
 protected $schema_version = 1;
 protected $db_version;
 protected $tables = array();
 public function init() {}
 public function register_tables( $force_update = false ) {
 global $wpdb;
 // make WP aware of our tables
 foreach ( $this->tables as $table ) {
 $wpdb->tables[] = $table;
 $name = $this->get_full_table_name( $table );
 $wpdb->$table = $name;
 }
 // create the tables
 if ( $this->schema_update_required() || $force_update ) {
 foreach ( $this->tables as $table ) {
 do_action( 'action_scheduler_before_schema_update', $table, $this->db_version );
 $this->update_table( $table );
 }
 $this->mark_schema_update_complete();
 }
 }
 abstract protected function get_table_definition( $table );
 private function schema_update_required() {
 $option_name = 'schema-' . static::class;
 $this->db_version = get_option( $option_name, 0 );
 // Check for schema option stored by the Action Scheduler Custom Tables plugin in case site has migrated from that plugin with an older schema
 if ( 0 === $this->db_version ) {
 $plugin_option_name = 'schema-';
 switch ( static::class ) {
 case 'ActionScheduler_StoreSchema':
 $plugin_option_name .= 'Action_Scheduler\Custom_Tables\DB_Store_Table_Maker';
 break;
 case 'ActionScheduler_LoggerSchema':
 $plugin_option_name .= 'Action_Scheduler\Custom_Tables\DB_Logger_Table_Maker';
 break;
 }
 $this->db_version = get_option( $plugin_option_name, 0 );
 delete_option( $plugin_option_name );
 }
 return version_compare( $this->db_version, $this->schema_version, '<' );
 }
 private function mark_schema_update_complete() {
 $option_name = 'schema-' . static::class;
 // work around race conditions and ensure that our option updates
 $value_to_save = (string) $this->schema_version . '.0.' . time();
 update_option( $option_name, $value_to_save );
 }
 private function update_table( $table ) {
 require_once ABSPATH . 'wp-admin/includes/upgrade.php';
 $definition = $this->get_table_definition( $table );
 if ( $definition ) {
 $updated = dbDelta( $definition );
 foreach ( $updated as $updated_table => $update_description ) {
 if ( strpos( $update_description, 'Created table' ) === 0 ) {
 do_action( 'action_scheduler/created_table', $updated_table, $table );
 }
 }
 }
 }
 protected function get_full_table_name( $table ) {
 return $GLOBALS['wpdb']->prefix . $table;
 }
 public function tables_exist() {
 global $wpdb;
 $tables_exist = true;
 foreach ( $this->tables as $table_name ) {
 $table_name = $wpdb->prefix . $table_name;
 $pattern = str_replace( '_', '\\_', $table_name );
 $existing_table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pattern ) );
 if ( $existing_table !== $table_name ) {
 $tables_exist = false;
 break;
 }
 }
 return $tables_exist;
 }
}
