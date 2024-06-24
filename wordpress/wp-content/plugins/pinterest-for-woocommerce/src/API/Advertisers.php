<?php
/**
 * Handle Pinterest Advertisers
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
 * Endpoint handing Pinterest advertisers.
 */
class Advertisers extends VendorAPI {

	/**
	 * Initialize class
	 */
	public function __construct() {

		$this->base              = 'tagowners';
		$this->endpoint_callback = 'get_advertisers';
		$this->methods           = WP_REST_Server::READABLE;

		$this->register_routes();
	}


	/**
	 * Get the advertisers assigned to the authorized Pinterest account.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array|WP_Error
	 *
	 * @throws \Exception PHP Exception.
	 */
	public function get_advertisers( WP_REST_Request $request ) {

		try {

			$advertisers = Base::get_advertisers();

			$terms_agreed = $request->has_param( 'terms_agreed' ) ? (int) $request->get_param( 'terms_agreed' ) : false;

			if ( 'success' !== $advertisers['status'] && 1000 === $advertisers['code'] ) {
				// User needs to take manual action in Pinterest dashboard.
				throw new \Exception( esc_html__( 'No advertiser exists.', 'pinterest-for-woocommerce' ), 1000 );
			}

			if ( 'success' !== $advertisers['status'] ) {
				throw new \Exception( esc_html__( 'Response error', 'pinterest-for-woocommerce' ), 400 );
			}

			if ( empty( $advertisers['data'] ) && ! empty( $terms_agreed ) ) {
				$advertiser          = Base::create_advertiser( $terms_agreed );
				$advertisers['data'] = 'success' === $advertiser['status'] ? array( $advertiser['data'] ) : array();
			}

			return array( 'advertisers' => $advertisers['data'] );

		} catch ( \Throwable $th ) {

			/* Translators: The error description as returned from the API */
			$error_message = sprintf( esc_html__( 'Could not fetch advertisers for Pinterest account ID. [%s]', 'pinterest-for-woocommerce' ), $th->getMessage() );

			return new \WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_advertisers_error', $error_message, array( 'status' => $th->getCode() ) );
		}
	}
}
