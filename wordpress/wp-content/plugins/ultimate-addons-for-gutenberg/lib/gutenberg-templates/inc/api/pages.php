<?php
/**
 * Pages generate content confirmation API.
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
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Pages generate content confirmation.
 *
 * @since 0.0.1
 */
class Pages extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/pages-onboarding/';

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
	 * Save the pages onboarding flag status.
	 *
	 * @return WP_REST_Response
	 */
	public function set(): WP_REST_Response {

		$saved = update_option( 'ast-show-pages-onboarding', 'no' );
		$response = new WP_REST_Response(
			array(
				'success' => $saved,
			)
		);
		$response->set_status( 200 );
		return $response;
	}
}
