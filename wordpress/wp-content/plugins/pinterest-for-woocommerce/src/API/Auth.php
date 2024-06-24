<?php
/**
 * API Auth
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest\Logger as Logger;
use Throwable;
use \WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the endpoint to which we are returned to, after being authorized by Pinterest.
 */
class Auth extends VendorAPI {

	/**
	 * Initiate class.
	 */
	public function __construct() {

		$this->base              = \PINTEREST_FOR_WOOCOMMERCE_API_AUTH_ENDPOINT;
		$this->endpoint_callback = 'oauth_callback';
		$this->methods           = 'GET';

		$this->register_routes();
	}


	/**
	 * Authenticate request
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return boolean
	 */
	public function permissions_check( WP_REST_Request $request ) {

		$control = get_transient( \PINTEREST_FOR_WOOCOMMERCE_AUTH );

		if ( ! $control || ! $request->has_param( 'control' ) || $control !== $request->get_param( 'control' ) ) {
			add_filter( 'rest_pre_serve_request', array( $this, 'redirect_to_settings_page' ), 10, 3 );
			return false;
		}

		delete_transient( \PINTEREST_FOR_WOOCOMMERCE_AUTH );

		return true;
	}



	/**
	 * When we got a permissions check failure, Hijack the rest_pre_serve_request filter
	 * to sent the user to the settings page instead of showing a white page with the printed REST response
	 *
	 * @param bool             $served  Whether the request has already been served. Default false.
	 * @param WP_HTTP_Response $result  Result to send to the client. Usually a `WP_REST_Response`.
	 * @param WP_REST_Request  $request Request used to generate the response.
	 * @return bool
	 */
	public function redirect_to_settings_page( $served, $result, $request ) {

		if ( 401 === $result->get_status() ) {
			$error_message = esc_html__( 'Something went wrong with your attempt to authorize this App. Please try again.', 'pinterest-for-woocommerce' );
			wp_safe_redirect( add_query_arg( 'error', rawurlencode( $error_message ), $this->get_redirect_url( $request->get_param( 'view' ), true ) ) );
			exit;
		}

		return $served;
	}

	/**
	 * REST Route callback function for POST requests.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request.
	 */
	public function oauth_callback( WP_REST_Request $request ) {

		$error_args = '';
		$error      = $request->has_param( 'error' ) ? sanitize_text_field( $request->get_param( 'error' ) ) : '';
		$token      = $request->get_param( 'pinterestv3_access_token' );
		$control    = $request->get_param( 'control' );

		if ( empty( $token ) || empty( $control ) ) {
			$error = esc_html__( 'Empty response, please try again later.', 'pinterest-for-woocommerce' );
		}

		// Save token information.
		if ( empty( $error ) ) {

			Pinterest_For_Woocommerce()::save_token(
				array(
					'access_token' => sanitize_text_field( $token ),
				)
			);

			try {
				/**
				 * Actions to perform after getting the authorization token.
				 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
				 */
				do_action( 'pinterest_for_woocommerce_token_saved' );
			} catch ( Throwable $th ) {
				$error = esc_html__( 'There was an error getting the account data. Please try again later.', 'pinterest-for-woocommerce' );
			}
		}

		if ( ! empty( $error ) ) {
			$error_args = '&error=' . $error;
			// Force the logs to debug the connection procedure.
			Logger::log( wp_json_encode( $error ), 'error', null, true );
		}

		wp_safe_redirect( $this->get_redirect_url( $request->get_param( 'view' ), ! empty( $error ) ) . $error_args );
		exit;
	}

	/**
	 * Returns the redirect URI based on the current request's parameters and plugin settings.
	 *
	 * @param string  $view      The context of the view.
	 * @param boolean $has_error Whether there was an error with the auth process.
	 *
	 * @return string
	 */
	private function get_redirect_url( $view = null, $has_error = false ) {

		$query_args = array(
			'page' => 'wc-admin',
			'path' => '/pinterest/onboarding',
			'step' => $has_error || ! Pinterest_For_Woocommerce()::is_business_connected() ? 'setup-account' : 'claim-website',
		);

		if ( ! empty( $view ) ) {
			$query_args['view'] = sanitize_key( $view );
		}

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// nosemgrep: audit.php.wp.security.xss.query-arg
		return add_query_arg(
			$query_args,
			admin_url( 'admin.php' )
		);
	}
}
