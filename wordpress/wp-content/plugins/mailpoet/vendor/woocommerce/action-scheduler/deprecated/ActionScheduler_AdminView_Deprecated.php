<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_AdminView_Deprecated {
 public function action_scheduler_post_type_args( $args ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $args;
 }
 public function list_table_views( $views ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $views;
 }
 public function bulk_actions( $actions ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $actions;
 }
 public function list_table_columns( $columns ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $columns;
 }
 public static function list_table_sortable_columns( $columns ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $columns;
 }
 public static function list_table_column_content( $column_name, $post_id ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 }
 public static function row_actions( $actions, $post ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $actions;
 }
 public static function maybe_execute_action() {
 _deprecated_function( __METHOD__, '2.0.0' );
 }
 public static function admin_notices() {
 _deprecated_function( __METHOD__, '2.0.0' );
 }
 public function custom_orderby( $orderby, $query ){
 _deprecated_function( __METHOD__, '2.0.0' );
 }
 public function search_post_password( $search, $query ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 }
 public function post_updated_messages( $messages ) {
 _deprecated_function( __METHOD__, '2.0.0' );
 return $messages;
 }
}