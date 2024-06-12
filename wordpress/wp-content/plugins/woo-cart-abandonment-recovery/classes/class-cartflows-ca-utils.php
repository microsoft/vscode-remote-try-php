<?php
/**
 * Utils.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cartflows_Ca_Utils.
 */
class Cartflows_Ca_Utils {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Common zapier data
	 *
	 * @var zapier
	 */
	private static $zapier = null;

	/**
	 * Common zapier data
	 *
	 * @var zapier
	 */
	private static $cart_abandonment_settings = null;


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
	 * Check if cart abandonment tracking is enabled.
	 *
	 * @return bool
	 */
	public function is_cart_abandonment_tracking_enabled() {

		$wcf_ca_status = get_option( 'wcf_ca_status' );

		// Check if abandonment cart tracking is disabled or zapier webhook is empty.
		if ( isset( $wcf_ca_status ) && 'on' === $wcf_ca_status ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if cart abandonment tracking is enabled.
	 *
	 * @return bool
	 */
	public function is_zapier_trigger_enabled() {

		$wcf_ca_zapier_tracking_status = get_option( 'wcf_ca_zapier_tracking_status' );

		// Check if zapier tracking is disabled or zapier webhook is empty.
		if ( isset( $wcf_ca_zapier_tracking_status ) && 'on' === $wcf_ca_zapier_tracking_status ) {
			return true;
		}

		return false;
	}

	/**
	 * Get cart abandonment tracking cutoff time.
	 *
	 * @param  boolean $in_seconds get cutoff time in seconds if true.
	 * @return bool
	 */
	public function get_cart_abandonment_tracking_cut_off_time( $in_seconds = false ) {

		$cart_abandoned_time = apply_filters( 'cartflows_ca_cart_abandonment_cut_off_time', WCF_DEFAULT_CUT_OFF_TIME );
		return $in_seconds ? $cart_abandoned_time * MINUTE_IN_SECONDS : $cart_abandoned_time;

	}

	/**
	 * Check if GDPR is enabled.
	 *
	 * @return bool
	 */
	public function is_gdpr_enabled() {

		$wcf_ca_gdpr_status = get_option( 'wcf_ca_gdpr_status' );

		// Check if abandonment cart tracking is disabled or zapier webhook is empty.
		if ( isset( $wcf_ca_gdpr_status ) && 'on' === $wcf_ca_gdpr_status ) {
			return true;
		}

		return false;
	}


}
