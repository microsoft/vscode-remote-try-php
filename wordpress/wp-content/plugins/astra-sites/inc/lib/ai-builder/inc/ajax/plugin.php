<?php
/**
 * Plugin ajax actions.
 *
 * @package AiBuilder
 */

namespace AiBuilder\Inc\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AiBuilder\Inc\Ajax\AjaxBase;
use AiBuilder\Inc\Traits\Instance;
use AiBuilder\Inc\Classes\Importer\Ai_Builder_Error_Handler;
use STImporter\Importer\ST_Importer;
use AiBuilder\Inc\Traits\Helper;
use AiBuilder\Inc\Classes\Zipwp\Ai_Builder_ZipWP_Integration;
use STImporter\Importer\ST_Importer_Helper;
/**
 * Class Flows.
 */
class Plugin extends AjaxBase {

	use Instance;

	/**
	 * Register_ajax_events.
	 *
	 * @return void
	 */
	public function register_ajax_events() {

		$ajax_events = array(
			'required_plugins',
			'required_plugin_activate',
			'filesystem_permission',
			'set_start_flag',
			'download_image',
			'report_error',
			'activate_theme',
			'site_language',
		);

		$this->init_ajax_events( $ajax_events );
	}

	/**
	 * Required Plugins
	 *
	 * @since 2.0.0
	 *
	 * @param  array $required_plugins Required Plugins.
	 * @param  array $options            Site Options.
	 * @param  array $enabled_extensions Enabled Extensions.
	 * @return mixed
	 */
	public function required_plugins( $required_plugins = array(), $options = array(), $enabled_extensions = array() ) {
		Helper::required_plugins( $required_plugins, $options, $enabled_extensions );
	}

	/**
	 * Required Plugin Activate
	 *
	 * @since 2.0.0 Added parameters $init, $options & $enabled_extensions to add the WP CLI support.
	 * @since 1.0.0
	 * @param  string $init               Plugin init file.
	 * @param  array  $options            Site options.
	 * @param  array  $enabled_extensions Enabled extensions.
	 * @return void
	 */
	public function required_plugin_activate( $init = '', $options = array(), $enabled_extensions = array() ) {
		Helper::required_plugin_activate( $init, $options, $enabled_extensions );
	}

	/**
	 * Get the status of file system permission of "/wp-content/uploads" directory.
	 *
	 * @return void
	 */
	public function filesystem_permission() {
		Helper::filesystem_permission();
	}

	/**
	 * Set a flag that indicates the import process has started.
	 */
	public function set_start_flag() {
		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}
		$uuid          = isset( $_POST['uuid'] ) ? sanitize_text_field( $_POST['uuid'] ) : '';
		$template_type = isset( $_POST['template_type'] ) ? sanitize_text_field( $_POST['template_type'] ) : '';
		ST_Importer::set_import_process_start_flag( $template_type, $uuid );
		wp_send_json_success();
	}

	/**
	 * Download Images
	 *
	 * @since 4.1.0
	 * @return void
	 */
	public function download_image() {

		check_ajax_referer( 'astra-sites', '_ajax_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'data'   => 'You do not have permission to do this action.',
					'status' => false,

				)
			);
		}

		$index  = isset( $_POST['index'] ) ? sanitize_text_field( wp_unslash( $_POST['index'] ) ) : '';
		$images = Ai_Builder_ZipWP_Integration::get_business_details( 'images' );

		if ( empty( $images ) ) {
			wp_send_json_success(
				array(
					'data'   => 'No images selected to download!',
					'status' => true,
				)
			);
		}

		$image = $images[ $index ];

		if ( empty( $image ) ) {
			wp_send_json_success(
				array(
					'data'   => 'No image to download!',
					'status' => true,
				)
			);
		}

		$prepare_image = array(
			'id'          => $image['id'],
			'url'         => $image['url'],
			'description' => isset( $image['description'] ) ? $image['description'] : '',
		);

		$id = ST_Importer_Helper::download_image( $prepare_image );

		wp_send_json_success(
			array(
				'data'   => 'Image downloaded successfully!',
				'status' => true,
			)
		);

	}

	/**
	 * Report Error.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function report_error() {

		check_ajax_referer( 'astra-sites', '_ajax_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'data'   => 'You do not have permission to do this action.',
					'status' => false,

				)
			);
		}

		$api_url = add_query_arg( [], trailingslashit( ST_Importer_Helper::get_api_domain() ) . 'wp-json/starter-templates/v2/import-error/' );

		if ( ! astra_sites_is_valid_url( $api_url ) ) {
			wp_send_json_error(
				array(
					/* Translators: %s is URL. */
					'message' => sprintf( __( 'Invalid URL - %s', 'ai-builder', 'astra-sites' ), $api_url ),
					'code'    => 'Error',
				)
			);
		}

		$post_id           = ( isset( $_POST['id'] ) ) ? intval( $_POST['id'] ) : 0;
		$user_agent_string = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

		$ai_import_logger = get_option( 'ai_import_logger', array() );
		$ai_import_logger = is_array( $ai_import_logger ) ? $ai_import_logger : array();

		$ai_import_logger[] = array(
			'time' => current_time( 'mysql' ),
			'data' => array(
				'post_id'    => $post_id,
				'user_agent' => $user_agent_string,
			),
		);

		$last_index = count( $ai_import_logger ) - 1;

		if ( 0 === $post_id ) {
			$error = sprintf(
				/* translators: %d is the post ID */
				__( 'Invalid Post ID - %d', 'ai-builder', 'astra-sites' ),
				$post_id
			);

			$ai_import_logger[ $last_index ]['error'] = $error;
			update_option( 'ai_import_logger', $ai_import_logger );

			wp_send_json_error(
				array(
					'message' => $error,
					'code'    => 'Error',
				)
			);
		}

		$api_args = array(
			'timeout'  => 3,
			'blocking' => true,
			'body'     => array(
				'url'        => esc_url( site_url() ),
				'err'        => stripslashes( $_POST['error'] ),
				'id'         => $_POST['id'],
				'logfile'    => $this->get_log_file_path(),
				'version'    => AI_BUILDER_VER,
				'abspath'    => ABSPATH,
				'user_agent' => $user_agent_string,
				'server'     => array(
					'php_version'            => Helper::get_php_version(),
					'php_post_max_size'      => ini_get( 'post_max_size' ),
					'php_max_execution_time' => ini_get( 'max_execution_time' ),
					'max_input_time'         => ini_get( 'max_input_time' ),
					'php_memory_limit'       => ini_get( 'memory_limit' ),
					'php_max_input_vars'     => ini_get( 'max_input_vars' ), // phpcs:ignore:PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
				),
			),
		);

		do_action( 'st_before_sending_error_report', $api_args['body'] );

		$request = wp_safe_remote_post( $api_url, $api_args );

		do_action( 'st_after_sending_error_report', $api_args['body'], $request );

		if ( is_wp_error( $request ) ) {
			$ai_import_logger[ $last_index ]['error'] = $request;
			update_option( 'ai_import_logger', $ai_import_logger );
			wp_send_json_error( $request );
		}

		$code = (int) wp_remote_retrieve_response_code( $request );
		$data = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( 200 === $code ) {
			wp_send_json_success( $data );
		}

		wp_send_json_error( $data );
	}

	/**
	 * Get full path of the created log file.
	 *
	 * @return string File Path.
	 * @since 3.0.25
	 */
	public function get_log_file_path() {
		$log_file = get_option( 'astra_sites_recent_import_log_file', false );
		if ( ! empty( $log_file ) && isset( $log_file ) ) {
			return str_replace( ABSPATH, esc_url( site_url() ) . '/', $log_file );
		}

		return '';
	}

	/**
	 * Activate theme
	 *
	 * @since 1.3.2
	 * @return void
	 */
	public function activate_theme() {

		// Verify Nonce.
		check_ajax_referer( 'astra-sites', '_ajax_nonce' );

		if ( ! current_user_can( 'customize' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
		}

		Ai_Builder_Error_Handler::Instance()->start_error_handler();

		switch_theme( 'astra' );

		Ai_Builder_Error_Handler::Instance()->stop_error_handler();

		wp_send_json_success(
			array(
				'success' => true,
				'message' => __( 'Theme Activated', 'ai-builder', 'astra-sites' ),
			)
		);
	}
	/**
	 * Set site language.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function site_language() {

		if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
			}
		}

		if ( ! wp_doing_ajax() ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ai-builder', 'astra-sites' ) );
		}

		$language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : 'en_US';
		$result   = $this->set_language( $language );

		if ( ! $result ) {
			wp_send_json_error( __( 'Failed to set the site language.', 'ai-builder', 'astra-sites' ) );
		}

		wp_send_json_success();
	}

	/**
	 * Set the site language.
	 *
	 * @since 1.0.0
	 *
	 * @param string $language  The language code.
	 * @return bool
	 */
	public function set_language( $language = 'en_US' ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		$locale_code = 'en_US' === $language ? '' : $language;
		if ( '' !== $locale_code && wp_can_install_language_pack() ) {
			$language = wp_download_language_pack( $locale_code );
		}
		if ( ( '' === $locale_code ) || ( '' !== $locale_code && $language ) ) {
			update_option( 'WPLANG', $locale_code );
			load_default_textdomain( $locale_code );
			return switch_to_locale( $locale_code );
		}

		return false;
	}
}
