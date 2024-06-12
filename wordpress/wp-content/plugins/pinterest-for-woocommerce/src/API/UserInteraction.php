<?php
/**
 * API Options
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use \WP_Error;
use \WP_REST_Server;
use \WP_REST_Request;

use Automattic\WooCommerce\Pinterest\Billing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint handling Options.
 */
class UserInteraction extends VendorAPI {

	const USER_INTERACTION     = 'user_interaction';
	const ADS_MODAL_DISMISSED  = 'ads_modal_dismissed';
	const ADS_NOTICE_DISMISSED = 'ads_notice_dismissed';
	const BILLING_FLOW_ENTERED = 'billing_setup_flow_entered';

	/**
	 * Initialize class
	 */
	public function __construct() {
		$this->base                        = self::USER_INTERACTION;
		$this->supports_multiple_endpoints = true;
		$this->endpoint_callbacks_map      = array(
			'get_user_interaction' => WP_REST_Server::READABLE,
			'set_user_interaction' => WP_REST_Server::CREATABLE,
		);

		$this->register_routes();
	}


	/**
	 * Handle get settings.
	 *
	 * @return array
	 */
	public function get_user_interaction() {
		return array(
			self::ADS_MODAL_DISMISSED  => (bool) get_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_MODAL_DISMISSED ),
			self::ADS_NOTICE_DISMISSED => (bool) get_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_NOTICE_DISMISSED ),
		);
	}


	/**
	 * Handle set settings.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array|WP_Error
	 */
	public function set_user_interaction( WP_REST_Request $request ) {

		if ( $request->has_param( self::ADS_MODAL_DISMISSED ) ) {
			update_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_MODAL_DISMISSED, true, false );
			// Confirm dismissal.
			return array(
				self::ADS_MODAL_DISMISSED => true,
			);
		}

		if ( $request->has_param( self::ADS_NOTICE_DISMISSED ) ) {
			update_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_NOTICE_DISMISSED, true, false );
			// Confirm notice dismissal.
			return array(
				self::ADS_NOTICE_DISMISSED => true,
			);
		}

		if ( $request->has_param( self::BILLING_FLOW_ENTERED ) ) {
			update_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::BILLING_FLOW_ENTERED, true, false );
			Billing::check_billing_setup_often();
			return array(
				self::BILLING_FLOW_ENTERED => true,
			);
		}

		return new WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_' . self::USER_INTERACTION, esc_html__( 'Unrecognized interaction parameter', 'pinterest-for-woocommerce' ), array( 'status' => 400 ) );
	}

	/**
	 * Flush options.
	 */
	public static function flush_options() {
		delete_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_MODAL_DISMISSED );
		delete_option( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_' . self::ADS_NOTICE_DISMISSED );
		Billing::do_not_check_billing_setup_often();
	}
}
