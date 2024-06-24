<?php
/**
 * Handle a Disconnection request.
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use \WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the endpoint which will handle the disconnection.
 */
class AuthDisconnect extends VendorAPI {

	/**
	 * Initiate class.
	 */
	public function __construct() {

		$this->base              = 'auth_disconnect';
		$this->endpoint_callback = 'handle_disconnect';
		$this->methods           = 'POST';

		$this->register_routes();
	}


	/**
	 * REST Route callback function for POST requests.
	 *
	 * @return array|WP_Error
	 *
	 * @since 1.0.0
	 */
	public function handle_disconnect() {
		return array(
			'disconnected' => Pinterest_For_Woocommerce()::disconnect(),
		);
	}
}
