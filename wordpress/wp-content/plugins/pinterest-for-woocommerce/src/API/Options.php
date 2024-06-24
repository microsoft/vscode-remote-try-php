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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint handling Options.
 */
class Options extends VendorAPI {

	/**
	 * Initialize class
	 */
	public function __construct() {

		$this->base                        = 'settings';
		$this->supports_multiple_endpoints = true;
		$this->endpoint_callbacks_map      = array(
			'get_settings' => WP_REST_Server::READABLE,
			'set_settings' => WP_REST_Server::CREATABLE,
		);

		$this->register_routes();
	}


	/**
	 * Handle get settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		Pinterest_For_Woocommerce()::maybe_check_billing_setup();
		return array(
			PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME => Pinterest_For_Woocommerce()::get_settings( true ),
		);
	}


	/**
	 * Handle set settings.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array|WP_Error
	 */
	public function set_settings( WP_REST_Request $request ) {
		if ( ! $request->has_param( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME ) || ! is_array( $request->get_param( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME ) ) ) {
			return new WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_options_error', esc_html__( 'Missing option parameters.', 'pinterest-for-woocommerce' ), array( 'status' => 400 ) );
		}

		$new_settings = $request->get_param( PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME );

		if ( Pinterest_For_Woocommerce()::get_settings() !== $new_settings && ! Pinterest_For_Woocommerce()::save_settings( $new_settings ) ) {
			return new WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_options_error', esc_html__( 'There was an error saving the settings.', 'pinterest-for-woocommerce' ), array( 'status' => 500 ) );
		}

		return array(
			PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME => true,
		);
	}
}
