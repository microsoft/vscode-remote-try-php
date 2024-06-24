<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cart abandonment tracking class.
 */
class Cartflows_Ca_Cron {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor function that initializes required actions and hooks.
	 */
	public function __construct() {
		// We are adding cron run time to check order status.
		add_filter( 'cron_schedules', array( $this, 'cartflows_ca_update_order_status_action' ) );//phpcs:ignore WordPress.WP.CronInterval.ChangeDetected

		// Schedule an action if it's not already scheduled.
		if ( ! wp_next_scheduled( 'cartflows_ca_update_order_status_action' ) ) {
			wp_schedule_event( time(), 'every_fifteen_minutes', 'cartflows_ca_update_order_status_action' );
		}
	}

		/**
		 * Create custom schedule.
		 *
		 * @param array $schedules schedules.
		 * @return mixed
		 */
	public function cartflows_ca_update_order_status_action( $schedules ) {

		/**
		 * Add filter to change the cron interval time to uodate order status.
		 */
		$cron_time = apply_filters( 'woo_ca_update_order_cron_interval', get_option( 'wcf_ca_cron_run_time', 20 ) );

		$schedules['every_fifteen_minutes'] = array(
			'interval' => intval( $cron_time ) * MINUTE_IN_SECONDS,
			'display'  => __( 'Every Fifteen Minutes', 'woo-cart-abandonment-recovery' ),
		);

		return $schedules;
	}
}

Cartflows_Ca_Cron::get_instance();
