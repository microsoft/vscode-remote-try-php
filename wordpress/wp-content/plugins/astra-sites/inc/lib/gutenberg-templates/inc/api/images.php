<?php
/**
 * Images API.
 *
 * @package {{package}}
 * @since 2.0.0
 */

namespace Gutenberg_Templates\Inc\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Api\Api_Base;
/**
 * Progress
 *
 * @since 2.0.0
 */
class Images extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/images/';

	/**
	 * Init Hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function register_routes() {

		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args' => array(
						'keywords' => array(
							'type'     => 'string',
							'required' => true,
						),
						'per_page' => array(
							'type'     => 'integer',
							'required' => false,
						),
						'page' => array(
							'type'     => 'integer',
							'required' => false,
						),
						'orientation' => array(
							'type'     => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required' => false,
						),
						'engine' => array(
							'type'     => 'string',
							'required' => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);

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
				__( 'Sorry, you are not allowed to do that.', 'ast-block-templates' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Save Prompts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data' => __( 'Nonce verification failed.', 'ast-block-templates' ),
					'status'  => false,

				)
			);
		}

		$api_endpoint = AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/image/v1/images';

		$post_data = array(
			'keywords' => isset( $request['keywords'] ) ? $request['keywords'] : '',
			'per_page' => isset( $request['per_page'] ) ? $request['per_page'] : 20,
			'page' => isset( $request['page'] ) ? $request['page'] : 1,
			'orientation' => isset( $request['orientation'] ) ? sanitize_text_field( $request['orientation'] ) : '',
			'engine' => isset( $request['engine'] ) ? sanitize_text_field( $request['engine'] ) : '',
		);

		$request_args = array(
			'body' => wp_json_encode( $post_data ),
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'timeout' => 100,
		);
		$response = wp_safe_remote_post( $api_endpoint, $request_args );



		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data' => 'Failed ' . $response->get_error_message(),
					'status'  => false,

				)
			);
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );
			if ( 200 === $response_code ) {
				$response_data = json_decode( $response_body, true );
				wp_send_json_success(
					array(
						'data' => $response_data,
						'status'  => true,
					)
				);

			} else {
				wp_send_json_error(
					array(
						'data' => 'Failed',
						'status'  => false,

					)
				);
			}
		}
	}
}
