<?php
/**
 * Progress API.
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
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Api\Api_Base;
/**
 * Progress
 *
 * @since 0.0.1
 */
class Category extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/categories/';

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
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get' ),
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
		
		// To do: Check api token or JWT token for permission.
		return true;
	}

	/**
	 * Save Prompts.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response
	 */
	public function get( $request ) {

		$categories = Helper::instance()->get_block_template_category();
		$response = new \WP_REST_Response(
			array(
				'success' => true,
				'categories' => $categories,
			)
		);
		$response->set_status( 200 );
		return $response;
	}
}
