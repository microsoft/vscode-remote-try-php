<?php
/**
 * Billing endpoint helper methods.
 *
 * @package Pinterest_For_WooCommerce/Classes
 */

namespace Automattic\WooCommerce\Pinterest;

use Automattic\WooCommerce\Pinterest\API\Base;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper class with functions for billing endpoint.
 */
class Billing {

	const CHECK_BILLING_SETUP_OFTEN         = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-check-billing-transient';
	const CHECK_BILLING_SETUP_ONCE_PER_HOUR = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-check-billing-once-per-hour-transient';
	/**
	 * Initialize Billing actions and Action Scheduler hooks.
	 *
	 * @since 1.2.5
	 */
	public static function schedule_event() {
		add_action( Heartbeat::DAILY, array( __CLASS__, 'handle_billing_setup_check' ) );
	}

	/**
	 * Check if the advertiser has set the billing data.
	 *
	 * @since 1.2.5
	 *
	 * @return mixed
	 */
	public static function handle_billing_setup_check() {

		Pinterest_For_Woocommerce()::add_billing_setup_info_to_account_data();

		return true;
	}

	/**
	 * Check if we are during the period of frequent billing checks.
	 * If the billing has been verified as correct we don't want the frequent check.
	 *
	 * @since 1.2.5
	 *
	 * @return bool
	 */
	public static function should_check_billing_setup_often() {
		/*
		 * Check if we have verified a correct billing setup.
		 */
		$account_data       = Pinterest_For_Woocommerce()::get_setting( 'account_data' );

		$has_billing_setup  = is_array( $account_data ) && ( $account_data['is_billing_setup'] ?? false );
		$should_check_often = false !== get_transient( self::CHECK_BILLING_SETUP_OFTEN );
		if ( $has_billing_setup && $should_check_often ) {
			/*
			 * We are just after initial setup or billing button click and billing setup is correct.
			 * Assume that the user has just crated the setup and we have caught it. We don't need to check for now.
			 */
			return false;
		}

		if ( $should_check_often ) {
			return true;
		}

		if ( false !== get_transient( self::CHECK_BILLING_SETUP_ONCE_PER_HOUR ) ) {
			// Last check was less then hour ago. Skip this one.
			return false;
		}

		return true;
	}

	/**
	 * Mark billing setup check as required often.
	 *
	 * @since 1.2.5
	 *
	 * @param int $duration For how lon frequent billing check should happen.
	 *
	 * @return void
	 */
	public static function check_billing_setup_often( $duration = HOUR_IN_SECONDS ) {
		set_transient( self::CHECK_BILLING_SETUP_OFTEN, true, $duration );
	}

	/**
	 * Clear billing check transient.
	 *
	 * @since 1.2.5
	 *
	 * @return void
	 */
	public static function do_not_check_billing_setup_often() {
		delete_transient( self::CHECK_BILLING_SETUP_OFTEN );
	}

	/**
	 * Mark setup as checked. This will delay next setup for an hour.
	 *
	 * @since 1.2.5
	 *
	 * @return void
	 */
	public static function mark_billing_setup_checked() {
		set_transient( self::CHECK_BILLING_SETUP_ONCE_PER_HOUR, true, HOUR_IN_SECONDS );
	}

	/**
	 * Helper function to check if billing has been set up.
	 *
	 * @since 1.2.5
	 * @return bool
	 */
	public static function has_billing_set_up(): bool {

		if ( ! Pinterest_For_Woocommerce()::get_data( 'is_advertiser_connected' ) ) {
			// Advertiser not connected, we can't establish if billing is set up.
			return false;
		}

		$advertiser_id = Pinterest_For_Woocommerce()::get_setting( 'tracking_advertiser' );

		if ( false === $advertiser_id ) {
			// No advertiser id stored. But we are connected. This is an abnormal state that should not happen.
			Logger::log( __( 'Advertiser connected but the connection id is missing.', 'pinterest-for-woocommerce' ) );
			return false;
		}

		try {
			$result = Base::get_advertiser_billing_profile( $advertiser_id );
			if ( 'success' !== $result['status'] ) {
				return false;
			}

			$billing_profile_data = (array) $result['data'];

			return (bool) $billing_profile_data['is_billing_setup'];

		} catch ( Throwable $th ) {

			Logger::log( $th->getMessage(), 'error' );
			return false;

		}
	}
}
