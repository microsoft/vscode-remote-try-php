<?php
/**
 * Initialize Setup.
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
use Gutenberg_Templates\Inc\Importer\Plugin;
use Gutenberg_Templates\Inc\Importer\Sync_Library;
/**
 * Progress
 *
 * @since 0.0.1
 */
class Initialize_Setup extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/setup/';

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
					'callback'            => array( $this, 'setup_templates' ),
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
	 * @return \WP_REST_Response
	 */
	public function setup_templates( $request ) {

		$is_fresh_site = get_option( 'ast_block_templates_fresh_site', 'yes' );

		if ( 'yes' === $is_fresh_site ) {
			Sync_Library::instance()->set_default_assets();
			update_option( 'ast_block_templates_fresh_site', 'no' );
		}

		Sync_Library::instance()->process_sync();

		$response = new \WP_REST_Response(
			array(
				'success' => true,
				'syncing' => $is_fresh_site,
			)
		);
		$response->set_status( 200 );
		return $response;
	}
}
