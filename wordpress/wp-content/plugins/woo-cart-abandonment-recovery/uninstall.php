<?php
/**
 * Woocommerce Cart Abandonment Recovery
 * Unscheduling the events.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

wp_clear_scheduled_hook( 'cartflows_ca_update_order_status_action' );

$delete_data = get_option( 'wcf_ca_delete_plugin_data' );

if ( 'on' === $delete_data ) {

	$options = array(
		'wcf_ca_status',
		'wcf_ca_gdpr_status',
		'wcf_ca_coupon_code_status',
		'wcf_ca_zapier_tracking_status',
		'wcf_ca_cut_off_time',
		'wcf_ca_from_name',
		'wcf_ca_from_email',
		'wcf_ca_reply_email',
		'wcf_ca_discount_type',
		'wcf_ca_coupon_amount',
		'wcf_ca_zapier_cart_abandoned_webhook',
		'wcf_ca_gdpr_message',
		'wcf_ca_coupon_expiry',
		'wcf_ca_coupon_expiry_unit',
		'wcf_ca_excludes_orders',
		'wcf_ca_delete_plugin_data',
		'wcf_ca_version',
		'wcf_ca_send_recovery_report_emails_to_admin',
		'wcf_ca_admin_email',
	);

	// Delete all options data.
	foreach ( $options as $index => $key ) {
		delete_option( $key );
	}
	//phpcs:disable WordPress.DB.DirectDatabaseQuery
	// Ignoring the direct query rule as we are dropping only our custom tables if exist.
	$wpdb->get_results( "DROP TABLE IF EXISTS {$wpdb->prefix}cartflows_ca_email_templates_meta" );

	$wpdb->get_results( "DROP TABLE IF EXISTS {$wpdb->prefix}cartflows_ca_email_history" );

	$wpdb->get_results( "DROP TABLE IF EXISTS {$wpdb->prefix}cartflows_ca_email_templates" );

	$wpdb->get_results( "DROP TABLE IF EXISTS {$wpdb->prefix}cartflows_ca_cart_abandonment" );
	//phpcs:disable WordPress.DB.DirectDatabaseQuery
}

