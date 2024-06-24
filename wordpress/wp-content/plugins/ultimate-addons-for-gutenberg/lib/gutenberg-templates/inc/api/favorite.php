<?php
/**
 * Favorite API.
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
 * Favorite
 *
 * @since 2.0.0
 */
class Favorite extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/favorite/';

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
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$namespace,
			$this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'save' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args' => array(
						'type' => array(
							'type'     => 'string',
							'required' => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'block_id' => array(
							'type'     => 'integer',
							'required' => true,
						),
						'status' => array(
							'type'     => 'boolean',
							'required' => true,
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
	 * Get Favorite.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get( $request ) {

		$favorites = get_option( 'ast_block_templates_favorites', array() );
		$response = new \WP_REST_Response(
			array(
				'success' => true,
				'data' => $favorites,
			)
		);
		$response->set_status( 200 );
		return $response;
	}

	/**
	 * Save Favorite.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function save( $request ) {

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

		$favorites = get_option( 'ast_block_templates_favorites', array() );
		$block_type = $request->get_param( 'type' );
		$id = $request->get_param( 'block_id' );
		$status = $request->get_param( 'status' );

		// Empty favorite then add favorite in respective array tye and early return.
		if ( empty( $favorites ) && $status ) {
			$favorites[ $block_type ][] = $id;
			$update_status = update_option( 'ast_block_templates_favorites', $favorites );
			return rest_ensure_response( array( 'success' => $update_status ) );
		}

		// Empty patterns OR blocks array then add favorite and return early.
		if ( empty( $favorites[ $block_type ] ) && $status ) {
			$favorites[ $block_type ][] = $id;
			$update_status = update_option( 'ast_block_templates_favorites', $favorites );
			return rest_ensure_response( array( 'success' => $update_status ) );
		}

		if ( $status ) {
			// Insert the block-id/page-id if it doesn't already exist.
			if ( ! in_array( $id, $favorites[ $block_type ] ) ) {
				$favorites[ $block_type ][] = $id;
			}
		} else {
			// Remove the block-id/page-id if it exists.
			if ( isset( $favorites[ $block_type ] ) && is_array( $favorites[ $block_type ] ) ) {
				$key = array_search( $id, $favorites[ $block_type ] );
				if ( false !== $key ) {
					unset( $favorites[ $block_type ][ $key ] );
					$favorites[ $block_type ] = array_values( $favorites[ $block_type ] );
				}
			}
		}

		$update_status = update_option( 'ast_block_templates_favorites', $favorites );

		return rest_ensure_response( array( 'success' => $update_status ) );
	}
}
