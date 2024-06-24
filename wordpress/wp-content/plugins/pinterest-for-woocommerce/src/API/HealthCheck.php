<?php
/**
 * Return Pinterest Feed health status.
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest as Pinterest;
use \WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint used to check the Health status of the connected Merchant object.
 */
class HealthCheck extends VendorAPI {

	/**
	 * Initialize class
	 */
	public function __construct() {
		$this->base              = 'health';
		$this->endpoint_callback = 'health_check';
		$this->methods           = WP_REST_Server::READABLE;

		$this->register_routes();
	}


	/**
	 * Get the merchant object from the API and return the status, and if exists, the disapproval rationale.
	 *
	 * @return array
	 *
	 * @throws \Exception PHP Exception.
	 */
	public function health_check() {

		try {

			$response = array();

			if ( ! Pinterest_For_Woocommerce()::get_data( 'merchant_id' ) ) {
				return array( 'status' => 'pending_initial_configuration' );
			}

			$merchant_connected_diff_platform = Pinterest_For_Woocommerce()::get_data( 'merchant_connected_diff_platform' );
			if ( $merchant_connected_diff_platform ) {
				return array( 'status' => 'merchant_connected_diff_platform' );
			}

			$locale_error = Pinterest_For_Woocommerce()::get_data( 'merchant_locale_not_valid' );
			if ( $locale_error ) {
				return array( 'status' => 'merchant_locale_not_valid' );
			}

			$merchant = Pinterest\Merchants::get_merchant();

			if ( 'success' !== $merchant['status'] || empty( $merchant['data']->product_pin_approval_status ) ) {
				throw new \Exception( __( 'Could not get approval status from Pinterest.', 'pinterest-for-woocommerce' ), 200 );
			}

			$response['status'] = $merchant['data']->product_pin_approval_status;

			if ( isset( $merchant['data']->product_pin_approval_status_reasons ) ) {
				$response['reasons'] = $merchant['data']->product_pin_approval_status_reasons;
			}

			return $response;

		} catch ( \Throwable $th ) {

			/* Translators: The error description as returned from the API */
			$error_message = sprintf( __( 'Could not fetch account status. [%s]', 'pinterest-for-woocommerce' ), $th->getMessage() );

			return array(
				'status'  => 'error',
				'message' => $error_message,
				'code'    => $th->getCode(),
			);
		}
	}
}
