<?php
/**
 * Revoke Access API.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace Gutenberg_Templates\Inc\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Api\Api_Base;
use Gutenberg_Templates\Inc\Traits\Helper;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Pages generate content confirmation.
 *
 * @since 0.0.1
 */
class RevokeAccess extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/revoke-access/';

	/**
	 * Init Hooks.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function register_routes() {

		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
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
			return new WP_Error(
				'gt_rest_cannot_access',
				__( 'Sorry, you are not allowed to do that.', 'ast-block-templates' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Revoke access.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response
	 */
	public function set( $request ): WP_REST_Response {

		$nonce = $request->get_header( 'X-WP-Nonce' );
		$nonce = isset( $nonce ) ? sanitize_text_field( $nonce ) : '';
		// Verify the nonce.
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data' => __( 'Nonce verification failed.', 'ast-block-templates' ),
					'status'  => false,

				)
			);
		}
		
		delete_option( 'ast-block-templates-show-onboarding' );
		
		Helper::delete_admin_settings_option( 'zip_ai_settings' );
		$response = new WP_REST_Response(
			array(
				'success' => true,
			)
		);
		$response->set_status( 200 );
		return $response;
	}
}
