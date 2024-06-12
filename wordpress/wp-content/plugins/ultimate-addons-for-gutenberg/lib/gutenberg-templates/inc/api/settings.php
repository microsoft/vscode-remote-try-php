<?php
/**
 * Settings API.
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
use Gutenberg_Templates\Inc\Importer\Plugin;
/**
 * Settings
 *
 * @since 2.0.0
 */
class Settings extends Api_Base {

	use Instance;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/settings/';

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
						'key' => array(
							'type'     => 'string',
							'required' => true,
						),
						'value' => array(
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
	 * Get Settings.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @return mixed
	 */
	public function get( $request ) {

		$ai_settings = get_option( 'ast_block_templates_ai_settings', array() );
		$response = new \WP_REST_Response(
			array(
				'success' => true,
				'data' => $ai_settings,
			)
		);
		$response->set_status( 200 );
		return $response;
	}

	/**
	 * Save Settings.
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

		$setting_key = $request->get_param( 'key' );
		$setting_value = $request->get_param( 'value' );

		if ( ! empty( $setting_key ) ) {
			$settings = get_option( 'ast_block_templates_ai_settings', array() );
			$settings[ $setting_key ] = $setting_value;

			if ( 'disable_ai' === $setting_key ) {
	
				$ai_settings = get_option( 'zip_ai_modules', array() );
				$ai_copilot_value = $setting_value ? 'disabled' : 'enabled';
				$ai_settings['ai_design_copilot']['status'] = $ai_copilot_value;
				update_option( 'zip_ai_modules', $ai_settings );

				$settings['disable_ai'] = $setting_value;
			}

			if ( 'adaptive_mode' === $setting_key ) {
				$ai_settings = get_option( 'zip_ai_modules', array() );
				$ai_copilot_value = $setting_value ? 'enabled' : 'disabled';
				$ai_settings['ai_design_copilot']['status'] = $ai_copilot_value;
				update_option( 'zip_ai_modules', $ai_settings );

				$settings['disable_ai'] = ! $setting_value;
			}

			$status = update_option( 'ast_block_templates_ai_settings', $settings );

			$blocks = Plugin::instance()->get_all_blocks();

			$response = new \WP_REST_Response(
				array(
					'success' => $status,
					'blocks'  => $blocks,
				)
			);

			$response->set_status( 200 );
			return $response;
		}

		
		return new \WP_Error(
			'failed',
			__( 'Sorry, settings are not saved.', 'ast-block-templates' ),
			array( 'status' => 'fail' )
		);
	}
}
