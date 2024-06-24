<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_wcSystemStatus {
 protected $store;
 public function __construct( $store ) {
 $this->store = $store;
 }
 public function render() {
 $action_counts = $this->store->action_counts();
 $status_labels = $this->store->get_status_labels();
 $oldest_and_newest = $this->get_oldest_and_newest( array_keys( $status_labels ) );
 $this->get_template( $status_labels, $action_counts, $oldest_and_newest );
 }
 protected function get_oldest_and_newest( $status_keys ) {
 $oldest_and_newest = array();
 foreach ( $status_keys as $status ) {
 $oldest_and_newest[ $status ] = array(
 'oldest' => '&ndash;',
 'newest' => '&ndash;',
 );
 if ( 'in-progress' === $status ) {
 continue;
 }
 $oldest_and_newest[ $status ]['oldest'] = $this->get_action_status_date( $status, 'oldest' );
 $oldest_and_newest[ $status ]['newest'] = $this->get_action_status_date( $status, 'newest' );
 }
 return $oldest_and_newest;
 }
 protected function get_action_status_date( $status, $date_type = 'oldest' ) {
 $order = 'oldest' === $date_type ? 'ASC' : 'DESC';
 $action = $this->store->query_actions(
 array(
 'claimed' => false,
 'status' => $status,
 'per_page' => 1,
 'order' => $order,
 )
 );
 if ( ! empty( $action ) ) {
 $date_object = $this->store->get_date( $action[0] );
 $action_date = $date_object->format( 'Y-m-d H:i:s O' );
 } else {
 $action_date = '&ndash;';
 }
 return $action_date;
 }
 protected function get_template( $status_labels, $action_counts, $oldest_and_newest ) {
 $as_version = ActionScheduler_Versions::instance()->latest_version();
 $as_datastore = get_class( ActionScheduler_Store::instance() );
 ?>
 <table class="wc_status_table widefat" cellspacing="0">
 <thead>
 <tr>
 <th colspan="5" data-export-label="Action Scheduler"><h2><?php esc_html_e( 'Action Scheduler', 'action-scheduler' ); ?><?php echo wc_help_tip( esc_html__( 'This section shows details of Action Scheduler.', 'action-scheduler' ) ); ?></h2></th>
 </tr>
 <tr>
 <td colspan="2" data-export-label="Version"><?php esc_html_e( 'Version:', 'action-scheduler' ); ?></td>
 <td colspan="3"><?php echo esc_html( $as_version ); ?></td>
 </tr>
 <tr>
 <td colspan="2" data-export-label="Data store"><?php esc_html_e( 'Data store:', 'action-scheduler' ); ?></td>
 <td colspan="3"><?php echo esc_html( $as_datastore ); ?></td>
 </tr>
 <tr>
 <td><strong><?php esc_html_e( 'Action Status', 'action-scheduler' ); ?></strong></td>
 <td class="help">&nbsp;</td>
 <td><strong><?php esc_html_e( 'Count', 'action-scheduler' ); ?></strong></td>
 <td><strong><?php esc_html_e( 'Oldest Scheduled Date', 'action-scheduler' ); ?></strong></td>
 <td><strong><?php esc_html_e( 'Newest Scheduled Date', 'action-scheduler' ); ?></strong></td>
 </tr>
 </thead>
 <tbody>
 <?php
 foreach ( $action_counts as $status => $count ) {
 // WC uses the 3rd column for export, so we need to display more data in that (hidden when viewed as part of the table) and add an empty 2nd column.
 printf(
 '<tr><td>%1$s</td><td>&nbsp;</td><td>%2$s<span style="display: none;">, Oldest: %3$s, Newest: %4$s</span></td><td>%3$s</td><td>%4$s</td></tr>',
 esc_html( $status_labels[ $status ] ),
 esc_html( number_format_i18n( $count ) ),
 esc_html( $oldest_and_newest[ $status ]['oldest'] ),
 esc_html( $oldest_and_newest[ $status ]['newest'] )
 );
 }
 ?>
 </tbody>
 </table>
 <?php
 }
 public function __call( $name, $arguments ) {
 switch ( $name ) {
 case 'print':
 _deprecated_function( __CLASS__ . '::print()', '2.2.4', __CLASS__ . '::render()' );
 return call_user_func_array( array( $this, 'render' ), $arguments );
 }
 return null;
 }
}
