<?php
/**
 * Handle Pinterest Businesses
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use \WP_REST_Server;
use \WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint handing Pinterest linked business accounts.
 */
class Businesses extends VendorAPI {

	/**
	 * Initialize class
	 */
	public function __construct() {
		$this->base              = 'businesses';
		$this->endpoint_callback = 'get_businesses';
		$this->methods           = WP_REST_Server::READABLE;

		$this->register_routes();
	}


	/**
	 * Get the Linked Business Accounts assigned to the authorized Pinterest account.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array|WP_Error
	 *
	 * @throws \Exception PHP Exception.
	 */
	public function get_businesses( WP_REST_Request $request ) {

		try {

			$businesses = Pinterest_For_Woocommerce()::get_linked_businesses( true );

			return $businesses;

		} catch ( \Throwable $th ) {

			/* Translators: The error description as returned from the API */
			$error_message = sprintf( esc_html__( 'Could not fetch linked business accounts for Pinterest account ID. [%s]', 'pinterest-for-woocommerce' ), $th->getMessage() );

			return new \WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_businesses_error', $error_message, array( 'status' => $th->getCode() ) );
		}
	}
}
