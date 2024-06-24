<?php
/**
 * Handle ZipWP API calls.
 *
 * @package {{package}}
 * @since {{since}}
 */

namespace Gutenberg_Templates\Inc\Classes;

use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Traits\Instance;

/**
 * AST Block Templates ZipWP API
 *
 * @since {{since}}
 */
class Ast_Block_Templates_Zipwp_Api {

	use Instance;

	/**
	 * Constructor
	 *
	 * @since 2.1.13
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Get api domain
	 *
	 * @since 2.1.13
	 * @return string
	 */
	public function get_api_domain() {
		return ( defined( 'ZIPWP_API' ) ? ZIPWP_API : 'https://api.zipwp.com/api/' );
	}

	/**
	 * Get API headers
	 *
	 * @since 2.1.13
	 * @return array<string, string>
	 */
	public function get_api_headers() {
		return array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . Helper::decrypt( Helper::get_setting( 'zip_token' ) ),
		);
	}

	/**
	 * Get api namespace
	 *
	 * @since 2.1.13
	 * @return string
	 */
	public function get_api_namespace() {
		return 'zipwp/v1';
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  object $request WP_REST_Request Full details about the request.
	 * @return object|boolean
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			return new \WP_Error(
				'gt_rest_cannot_access',
				__( 'Sorry, you are not allowed to do that.', 'astra-sites' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Register route
	 *
	 * @since 2.1.13
	 * @return void
	 */
	public function register_route() {
		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			'/search-category/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'search_business_category' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args' => array(
						'keyword' => array(
							'type'     => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required' => true,
						),
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/site-languages/',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_site_languages' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Search business category.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function search_business_category( $request ) {

		$nonce = (string) $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data' => __( 'Nonce verification failed.', 'astra-sites' ),
					'status'  => false,

				)
			);
		}

		$keyword = $request['keyword'];
		$api_endpoint = $this->get_api_domain() . 'sites/business/search?q=' . $keyword;

		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response = wp_safe_remote_get( $api_endpoint, $request_args );
		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data' => 'Failed ' . $response->get_error_message(),
					'status'  => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			wp_send_json_success(
				array(
					'data' => $response_data['results'],
					'status'  => true,
				)
			);

		} else {
			wp_send_json_error(
				array(
					'data' => 'Failed - ' . $response_body,
					'status'  => false,

				)
			);
		}
	}

	/**
	 * Get ZipWP Languages list.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get_site_languages( $request ) {
		$nonce = (string) $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data' => __( 'Nonce verification failed.', 'astra-sites' ),
					'status'  => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . 'sites/languages/';
		$request_args = array(
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data' => 'Failed ' . $response->get_error_message(),
					'status'  => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );
			if ( $response_data ) {
				wp_send_json_success(
					array(
						'data' => $response_data['data'],
						'status'  => true,
					)
				);
			}
			wp_send_json_error(
				array(
					'data' => $response_data,
					'status'  => false,

				)
			);
		}
		wp_send_json_error(
			array(
				'data' => 'Failed ' . $response_body,
				'status'  => false,

			)
		);
	}
}
