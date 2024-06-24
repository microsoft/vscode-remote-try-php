<?php
/**
 * API Options
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest\Logger as Logger;

use \WP_REST_Server;
use \WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint handing Domain verification.
 */
class DomainVerification extends VendorAPI {

	/**
	 * The number of remaining attempts to retry domain verification on error.
	 *
	 * @var integer
	 */
	private static $verification_attempts_remaining = 3;

	/**
	 * Initialize class
	 */
	public function __construct() {

		$this->base              = 'domain_verification';
		$this->endpoint_callback = 'handle_verification';
		$this->methods           = WP_REST_Server::EDITABLE;

		$this->register_routes();
	}


	/**
	 * Handle domain verification by triggering the realtime verification process
	 * using the Pinterst API.
	 *
	 * @return mixed
	 *
	 * @throws \Exception PHP Exception.
	 */
	public function handle_verification() {
		return self::trigger_domain_verification();
	}


	/**
	 * Triggers the realtime verification process using the Pinterst API.
	 *
	 * @return mixed
	 *
	 * @throws \Exception PHP Exception.
	 */
	public static function trigger_domain_verification() {
		static $verification_data;

		try {

			if ( is_null( $verification_data ) ) {
				// Get verification code from pinterest.
				$verification_data = Base::domain_verification_data();
			}

			if ( 'success' === $verification_data['status'] && ! empty( $verification_data['data']->verification_code ) ) {

				Pinterest_For_Woocommerce()::save_data( 'verification_data', (array) $verification_data['data'] );

				$result = Base::trigger_verification();

				if ( 'success' === $result['status'] ) {
					$account_data = Pinterest_For_Woocommerce()::update_account_data();
					return array_merge( (array) $result['data'], array( 'account_data' => $account_data ) );
				}

				throw new \Exception( 'Meta tag verification failed', 409 );

			}

			throw new \Exception( 'Domain verification failed', 406 );

		} catch ( \Throwable $th ) {

			$error_code = $th->getCode() >= 400 ? $th->getCode() : 400;

			if ( 403 === $error_code && self::$verification_attempts_remaining > 0 ) {
				self::$verification_attempts_remaining--;
				Logger::log( sprintf( 'Retrying domain verification in 5 seconds. Attempts left: %d', self::$verification_attempts_remaining ), 'debug' );
				sleep( 5 );
				return call_user_func( __METHOD__ );
			}

			return new \WP_Error(
				\PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_verification_error',
				$th->getMessage(),
				array(
					'status'         => $error_code,
					'pinterest_code' => method_exists( $th, 'get_pinterest_code' ) ? $th->get_pinterest_code() : 0,
				)
			);

		}
	}
}
