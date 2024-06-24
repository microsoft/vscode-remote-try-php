<?php
/**
 * Common Settings.
 *
 * @package uag
 */

namespace UagAdmin\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use UagAdmin\Ajax\Ajax_Base;
use UagAdmin\Inc\Admin_Helper;

use \ZipAI\Classes\Helper as Zip_Ai_Helper;
use \ZipAI\Classes\Module as Zip_Ai_Module;

/**
 * Class Common_Settings.
 */
class Common_Settings extends Ajax_Base {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 *
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @return object initialized object of class.
	 *
	 * @since 2.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register_ajax_events.
	 *
	 * @return void
	 */
	public function register_ajax_events() {

		$ajax_events = array(
			'enable_beta_updates',
			'enable_file_generation',
			'regenerate_assets',
			'enable_templates_button',
			'enable_on_page_css_button',
			'enable_block_condition',
			'enable_masonry_gallery',
			'enable_quick_action_sidebar',
			'enable_block_responsive',
			'enable_dynamic_content',
			'enable_animations_extension',
			'enable_gbs_extension',
			'blocks_activation_and_deactivation',
			'load_select_font_globally',
			'load_fse_font_globally',
			'fse_font_globally',
			'fse_font_globally_delete',
			'select_font_globally',
			'load_gfonts_locally',
			'preload_local_fonts',
			'collapse_panels',
			'copy_paste',
			'social',
			'dynamic_content_mode',
			'content_width',
			'container_global_padding',
			'container_global_elements_gap',
			'blocks_editor_spacing',
			'recaptcha_site_key_v2',
			'recaptcha_secret_key_v2',
			'recaptcha_site_key_v3',
			'recaptcha_secret_key_v3',
			'visibility_mode',
			'visibility_page',
			'fetch_pages',
			'load_font_awesome_5',
			'auto_block_recovery',
			'enable_legacy_blocks',
			'pro_activate',
			'insta_linked_accounts',
			'insta_all_users_media',
			'insta_refresh_all_tokens',
			'btn_inherit_from_theme',
			'zip_ai_module_status',
			'zip_ai_verify_authenticity',
			'enable_header_titlebar',
		);

		$this->init_ajax_events( $ajax_events );
	}

	/**
	 * Save global option of button to inherit from theme.
	 *
	 * @since 2.6.2
	 * @return void
	 */
	public function btn_inherit_from_theme() {

		$this->check_permission_nonce( 'uag_btn_inherit_from_theme' );
		if ( false !== get_option( 'uag_btn_inherit_from_theme_fallback' ) ) {
			\UAGB_Admin_Helper::delete_admin_settings_option( 'uag_btn_inherit_from_theme_fallback' );
		};
		
		$value = $this->check_post_value();
		$this->delete_all_assets(); // We need to regenerate assets when user changes this setting to regenerate the dynamic CSS according to it.
		$this->save_admin_settings( 'uag_btn_inherit_from_theme', sanitize_text_field( $value ) );
	}

	/**
	 * Checks if the user has the permission to perform the requested action and verifies the nonce.
	 *
	 * @param string $option The name of the option to check the nonce against.
	 * @param string $scope The capability required to perform the action. Default is 'manage_options'.
	 * @param string $security The security to check the nonce against. Default is 'security'.
	 * @return void
	 *
	 * @since 2.5.0
	 */
	private function check_permission_nonce( $option, $scope = 'manage_options', $security = 'security' ) {

		if ( ! current_user_can( $scope ) ) {
			wp_send_json_error( array( 'messsage' => $this->get_error_msg( 'permission' ) ) );
		}

		/**
		 * Nonce verification
		 */
		if ( ! check_ajax_referer( $option, $security, false ) ) {
			wp_send_json_error( array( 'messsage' => $this->get_error_msg( 'nonce' ) ) );
		}
	}

	/**
	 * Saves the success message after successfully updating admin settings option.
	 *
	 * @param string $option The name of the option to update.
	 * @param mixed  $value The value to be updated.
	 * @return void
	 *
	 * @since 2.5.0
	 */
	private function save_admin_settings( $option, $value ) {
		\UAGB_Admin_Helper::update_admin_settings_option( $option, $value );


		$response_data = array(
			'messsage' => __( 'Successfully saved data!', 'ultimate-addons-for-gutenberg' ),
		);
		wp_send_json_success( $response_data );
	}

	/**
	 * Checks if the specified key exists in the $_POST array and returns the corresponding value.
	 *
	 * @param string $key The key to check in the $_POST array. Default value is 'value'.
	 * @return mixed The value of the specified key in the $_POST array if it exists, otherwise sends a JSON error response.
	 *
	 *  @since 2.5.0
	 */
	private function check_post_value( $key = 'value' ) {
		// nonce verification done in function check_permission_nonce.
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error( array( 'messsage' => __( 'No post data found!', 'ultimate-addons-for-gutenberg' ) ) );
		}
		// security validation done as per data type in function save_admin_settings.
		return $_POST[ $key ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Required Spectra Pro Plugin Activate
	 *
	 * @return void
	 */
	public function pro_activate() {
		wp_clean_plugins_cache();
		$value = $this->check_post_value();
		$value = sanitize_text_field( wp_unslash( $value ) );
		$this->check_permission_nonce( 'uag_pro_activate', 'activate_plugins' );

		if ( empty( $value ) ) {
			$response_data = array( 'messsage' => $this->get_error_msg( 'default' ) );
			wp_send_json_error( $response_data );
		}

		$activate = activate_plugin( 'spectra-pro/spectra-pro.php' );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => $activate->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'success' => true,
				'message' => __( 'Plugin Successfully Activated', 'ultimate-addons-for-gutenberg' ),
			)
		);
	}

	/**
	 * Save settings - Saves google recaptcha v3 secret key.
	 *
	 * @return void
	 */
	public function recaptcha_secret_key_v3() {
		$this->check_permission_nonce( 'uag_recaptcha_secret_key_v3' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_recaptcha_secret_key_v3', sanitize_text_field( $value ) );
	}

	/**
	 * Save settings - Saves google recaptcha v2 secret key.
	 *
	 * @return void
	 */
	public function recaptcha_secret_key_v2() {
		$this->check_permission_nonce( 'uag_recaptcha_secret_key_v2' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_recaptcha_secret_key_v2', sanitize_text_field( $value ) );
	}

	/**
	 * Save settings - Saves google recaptcha v2 site key.
	 *
	 * @return void
	 */
	public function recaptcha_site_key_v2() {
		$this->check_permission_nonce( 'uag_recaptcha_site_key_v2' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_recaptcha_site_key_v2', sanitize_text_field( $value ) );
	}

	/**
	 * Save settings - Saves google recaptcha v3 site key.
	 *
	 * @return void
	 */
	public function recaptcha_site_key_v3() {
		$this->check_permission_nonce( 'uag_recaptcha_site_key_v3' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_recaptcha_site_key_v3', sanitize_text_field( $value ) );
	}

	/**
	 * Save settings - Saves fetch_pages.
	 *
	 * @return void
	 */
	public function fetch_pages() {
		$this->check_permission_nonce( 'uag_fetch_pages' );

		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => 5,
		);
		// nonce verification is done in above function check_permission_nonce.
		$keyword = ( isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $keyword ) ) {
			$args['s'] = $keyword;
		}

		$results = array();
		$pages   = get_posts( $args );
		if ( is_array( $pages ) ) {
			foreach ( $pages as $page ) {
				$results[] = array(
					'label' => $page->post_title,
					'value' => $page->ID,
				);
			}
		}

		wp_send_json_success( $results );
	}

	/**
	 * Save settings - Saves visibility_page.
	 *
	 * @return void
	 */
	public function visibility_page() {
		$this->check_permission_nonce( 'uag_visibility_page' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_visibility_page', intval( $value ) );
	}

	/**
	 * Save settings - Saves visibility_mode.
	 *
	 * @return void
	 */
	public function visibility_mode() {
		$this->check_permission_nonce( 'uag_visibility_mode' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_visibility_mode', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves content_width.
	 *
	 * @return void
	 */
	public function content_width() {
		$this->check_permission_nonce( 'uag_content_width' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_content_width', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves container global padding.
	 *
	 * @return void
	 */
	public function container_global_padding() {
		$this->check_permission_nonce( 'uag_container_global_padding' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_container_global_padding', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves container global elements gap.
	 *
	 * @return void
	 */
	public function container_global_elements_gap() {
		$this->check_permission_nonce( 'uag_container_global_elements_gap' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_container_global_elements_gap', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves blocks editor spacing.
	 *
	 * @return void
	 */
	public function blocks_editor_spacing() {
		$this->check_permission_nonce( 'uag_blocks_editor_spacing' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_blocks_editor_spacing', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Loads selected font globally.
	 *
	 * @return void
	 */
	public function load_select_font_globally() {
		$this->check_permission_nonce( 'uag_load_select_font_globally' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_load_select_font_globally', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Loads selected font globally.
	 *
	 * @since 2.5.1
	 * @return void
	 */
	public function load_fse_font_globally() {
		$this->check_permission_nonce( 'uag_load_fse_font_globally' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_load_fse_font_globally', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves selected font globally.
	 *
	 * @since 2.5.1
	 * @return void
	 */
	public function select_font_globally() {
		$this->check_permission_nonce( 'uag_select_font_globally' );
		$value = $this->check_post_value();
		$value = json_decode( stripslashes( $value ), true );
		$this->save_admin_settings( 'uag_select_font_globally', $this->sanitize_form_inputs( $value ) );
	}

	/**
	 * Save setting - Saves selected font globally.
	 *
	 * @since 2.5.1
	 * @return void
	 */
	public function fse_font_globally_delete() {
		$this->check_permission_nonce( 'uag_fse_font_globally_delete' );
		$value = $this->check_post_value();
		$value = json_decode( stripslashes( $value ), true );
		\UAGB_FSE_Fonts_Compatibility::delete_theme_font_family( $value );
	}

	/**
	 * Save setting - Saves selected font globally.
	 *
	 * @since 2.5.1
	 * @return void
	 */
	public function fse_font_globally() {
		$this->check_permission_nonce( 'uag_fse_font_globally' );
		$value = $this->check_post_value();
		$value = json_decode( stripslashes( $value ), true );

		$spectra_global_fse_fonts = \UAGB_Admin_Helper::get_admin_settings_option( 'spectra_global_fse_fonts', array() );

		if ( ! is_array( $spectra_global_fse_fonts ) ) {
			$spectra_global_fse_fonts = array();
		}

		$spectra_global_fse_fonts[] = $value;

		$this->save_admin_settings( 'spectra_global_fse_fonts', $this->sanitize_form_inputs( $spectra_global_fse_fonts ) );
	}

	/**
	 * Save setting - Enables masonry gallery.
	 *
	 * @return void
	 */
	public function enable_masonry_gallery() {
		$this->check_permission_nonce( 'uag_enable_masonry_gallery' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_masonry_gallery', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables quick action sidebar.
	 *
	 * @since 2.12.0
	 * @return void
	 */
	public function enable_quick_action_sidebar() {
		$this->check_permission_nonce( 'uag_enable_quick_action_sidebar' );
		$value = $this->check_post_value();
		$value = 'disabled' === $value ? 'disabled' : 'enabled';
		$this->save_admin_settings( 'uag_enable_quick_action_sidebar', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Loads gfonts locally.
	 *
	 * @return void
	 */
	public function load_gfonts_locally() {
		$this->check_permission_nonce( 'uag_load_gfonts_locally' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_load_gfonts_locally', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Collapses panels.
	 *
	 * @return void
	 */
	public function collapse_panels() {
		$this->check_permission_nonce( 'uag_collapse_panels' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_collapse_panels', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables copy paste.
	 *
	 * @return void
	 */
	public function copy_paste() {
		$this->check_permission_nonce( 'uag_copy_paste' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_copy_paste', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Saves social settings.
	 *
	 * @return void
	 *
	 * @since 2.1.0
	 */
	public function social() {
		$this->check_permission_nonce( 'uag_social' );

		$social = \UAGB_Admin_Helper::get_admin_settings_option(
			'uag_social',
			array(
				'socialRegister'    => false,
				'googleClientId'    => '',
				'facebookAppId'     => '',
				'facebookAppSecret' => '',
			)
		);
		// nonce verification is done in above function check_permission_nonce.
		if ( isset( $_POST['socialRegister'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$social['socialRegister'] = rest_sanitize_boolean( sanitize_text_field( $_POST['socialRegister'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
		if ( isset( $_POST['googleClientId'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$social['googleClientId'] = sanitize_text_field( $_POST['googleClientId'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
		if ( isset( $_POST['facebookAppId'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$social['facebookAppId'] = sanitize_text_field( $_POST['facebookAppId'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
		if ( isset( $_POST['facebookAppSecret'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$social['facebookAppSecret'] = sanitize_text_field( $_POST['facebookAppSecret'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		$this->save_admin_settings( 'uag_social', $social );
	}

	/**
	 * Save setting - Enables dynamic content mode.
	 *
	 * @return void
	 *
	 * @since 2.1.0
	 */
	public function dynamic_content_mode() {
		$this->check_permission_nonce( 'uag_dynamic_content_mode' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_dynamic_content_mode', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Preloads local fonts.
	 *
	 * @return void
	 */
	public function preload_local_fonts() {
		$this->check_permission_nonce( 'uag_preload_local_fonts' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_preload_local_fonts', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables block conditions.
	 *
	 * @return void
	 *
	 * @since 2.4.0
	 */
	public function enable_block_condition() {
		$this->check_permission_nonce( 'uag_enable_block_condition' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_block_condition', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables block responsiveness.
	 *
	 * @return void
	 */
	public function enable_block_responsive() {
		$this->check_permission_nonce( 'uag_enable_block_responsive' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_block_responsive', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables dynamic content.
	 *
	 * @return void
	 *
	 * @since 2.1.0
	 */
	public function enable_dynamic_content() {
		$this->check_permission_nonce( 'uag_enable_dynamic_content' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_dynamic_content', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables animation extension.
	 *
	 * @return void
	 *
	 * @since 2.6.0
	 */
	public function enable_animations_extension() {
		$this->check_permission_nonce( 'uag_enable_animations_extension' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_animations_extension', sanitize_text_field( $value ) );
	}

	/**
	 * Save settings - Enables templates button.
	 *
	 * @return void
	 */
	public function enable_templates_button() {
		$this->check_permission_nonce( 'uag_enable_templates_button' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_templates_button', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables the on-page CSS button .
	 *
	 * @return void
	 */
	public function enable_on_page_css_button() {
		$this->check_permission_nonce( 'uag_enable_on_page_css_button' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_on_page_css_button', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Activates and deactivates blocks .
	 *
	 * @return void
	 */
	public function blocks_activation_and_deactivation() {
		$this->check_permission_nonce( 'uag_blocks_activation_and_deactivation' );
		$value = $this->check_post_value();

		// will sanitize $value in later stage.
		$value = json_decode( stripslashes( $value ), true );

		if ( 'disabled' === \UAGB_Helper::$file_generation ) {
			\UAGB_Admin_Helper::create_specific_stylesheet(); // Get Specific Stylesheet.
		}

		$this->save_admin_settings( '_uagb_blocks', $this->sanitize_form_inputs( $value ) );
	}

	/**
	 * Save setting - Enables beta updates.
	 *
	 * @return void
	 */
	public function enable_beta_updates() {
		$this->check_permission_nonce( 'uag_enable_beta_updates' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uagb_beta', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables legacy blocks.
	 *
	 * @return void
	 */
	public function enable_legacy_blocks() {
		$this->check_permission_nonce( 'uag_enable_legacy_blocks' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_enable_legacy_blocks', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Enables file generation.
	 *
	 * @return void
	 */
	public function enable_file_generation() {
		$this->check_permission_nonce( 'uag_enable_file_generation' );
		$value = $this->check_post_value();
		$this->save_admin_settings( '_uagb_allow_file_generation', sanitize_text_field( $value ) );
	}

	/**
	 * Delete all Assets.
	 *
	 * @since 2.6.2
	 * @return void
	 */
	public function delete_all_assets() {

		$value = $this->check_post_value();

		$wp_upload_dir = \UAGB_Helper::get_uag_upload_dir_path();

		if ( file_exists( $wp_upload_dir . 'custom-style-blocks.css' ) ) {
			wp_delete_file( $wp_upload_dir . 'custom-style-blocks.css' );
		}

		if ( ! empty( $value ) ) {

			$file_generation = \UAGB_Helper::allow_file_generation();

			if ( 'enabled' === $file_generation ) {

				\UAGB_Helper::delete_uag_asset_dir();
			}

			\UAGB_Admin_Helper::create_specific_stylesheet();

			/* Update the asset version */
			\UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );

		}
	}
	/**
	 * Save setting - Regenerates assets.
	 *
	 * @return void
	 */
	public function regenerate_assets() {
		$this->check_permission_nonce( 'uag_regenerate_assets' );
		
		/* Update the asset version */
		\UAGB_Admin_Helper::create_specific_stylesheet();
		\UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );

		$response_data = array(
			'messsage' => __( 'Successfully saved data!', 'ultimate-addons-for-gutenberg' ),
		);
		wp_send_json_success( $response_data );
	}

	/**
	 * Save setting - Sanitizes form inputs.
	 *
	 * @param array $input_settings setting data.
	 * @return array    The sanitized form inputs.
	 */
	public function sanitize_form_inputs( $input_settings = array() ) {
		$new_settings = array();

		if ( ! empty( $input_settings ) ) {
			foreach ( $input_settings as $key => $value ) {

				$new_key = sanitize_text_field( $key );

				if ( is_array( $value ) ) {
					$new_settings[ $new_key ] = $this->sanitize_form_inputs( $value );
				} else {
					$new_settings[ $new_key ] = sanitize_text_field( $value );
				}
			}
		}

		return $new_settings;
	}

	/**
	 * Save setting - Loads font awesome 5.
	 *
	 * @return void
	 */
	public function load_font_awesome_5() {
		$this->check_permission_nonce( 'uag_load_font_awesome_5' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_load_font_awesome_5', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - Auto recovers the block.
	 *
	 * @return void
	 */
	public function auto_block_recovery() {
		$this->check_permission_nonce( 'uag_auto_block_recovery' );
		$value = $this->check_post_value();
		$this->save_admin_settings( 'uag_auto_block_recovery', sanitize_text_field( $value ) );
	}

	/**
	 * Save setting - All Linked Instagram Accounts.
	 *
	 * @return void
	 *
	 * @since 2.4.1
	 */
	public function insta_linked_accounts() {
		$this->check_permission_nonce( 'uag_insta_linked_accounts' );
		$value = $this->check_post_value();
		$value = json_decode( stripslashes( $value ), true );
		// The previous $value is not sanitized, as the array sanitization is handled in the class method used below.
		$this->save_admin_settings( 'uag_insta_linked_accounts', $this->sanitize_form_inputs( $value ) );
	}

	/**
	 * Save setting - All Instagram Users' Media.
	 *
	 * @return void
	 *
	 * @since 2.4.1
	 */
	public function insta_all_users_media() {
		$this->check_permission_nonce( 'uag_insta_all_users_media' );
		$value = $this->check_post_value();
		$value = json_decode( stripslashes( $value ), true );
		// The previous $value is not sanitized, as the array sanitization is handled in the class method used below.
		$this->save_admin_settings( 'uag_insta_all_users_media', $this->sanitize_form_inputs( $value ) );
	}

	/**
	 * Ajax Request - Refresh All Instagram Tokens.
	 *
	 * @return void
	 *
	 * @since 2.4.1
	 */
	public function insta_refresh_all_tokens() {
		$this->check_permission_nonce( 'uag_insta_refresh_all_tokens' );
		if ( ! empty( $_POST['value'] ) && class_exists( '\SpectraPro\BlocksConfig\InstagramFeed\Block' ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			\SpectraPro\BlocksConfig\InstagramFeed\Block::refresh_all_instagram_users();
			wp_send_json_success( array( 'messsage' => __( 'Successfully refreshed tokens!', 'ultimate-addons-for-gutenberg' ) ) );
		}
		wp_send_json_error( array( 'messsage' => __( 'Failed to refresh tokens', 'ultimate-addons-for-gutenberg' ) ) );
	}

	/**
	 * Save setting - Enables GBS extension.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	public function enable_gbs_extension() {
		$this->check_permission_nonce( 'uag_enable_gbs_extension' );
		$value = $this->check_post_value();

		$value = 'enabled' === $value ? 'enabled' : 'disabled';
		$this->save_gbs_default_in_upload_folder( $value );

		$this->save_admin_settings( 'uag_enable_gbs_extension', $value );
	}

	/**
	 * Generate or delete default block css files.
	 * These generated files will be used in frontend.
	 * when user will disable GBS extension.
	 *
	 * @param string $value value will be enabled or disabled.
	 * @since 2.9.0
	 * @return void
	 */
	public function save_gbs_default_in_upload_folder( $value ) {
		$spectra_global_block_styles = get_option( 'spectra_global_block_styles', array() );

		if ( empty( $spectra_global_block_styles ) || ! is_array( $spectra_global_block_styles ) ) {
			return;
		}

		$create_block_array = array();

		foreach ( $spectra_global_block_styles as $styles ) {
			if ( empty( $styles['blockName'] ) || ! is_string( $styles['blockName'] ) ) {
				continue;
			}

			$create_block_array[ $styles['blockName'] ] = true;
		}

		// Remove assets if css available.
		if ( 'enabled' === $value ) {
			// Store all post ids in array.
			$post_ids = array();

			foreach ( $spectra_global_block_styles as $styles_get ) {
				if ( empty( $styles_get['post_ids'] ) ) {
					continue;
				}

				foreach ( $styles_get['post_ids'] as $post_id ) {
					if ( ! $post_id || in_array( $post_id, $post_ids ) ) {
						continue;
					}

					delete_post_meta( $post_id, '_uag_page_assets' );
					delete_post_meta( $post_id, '_uag_css_file_name' );
					delete_post_meta( $post_id, '_uag_js_file_name' );

					$post_ids[] = $post_id;
				}
			}

			update_option( '__uagb_asset_version', time() );
		}

		foreach ( $create_block_array as $block_name => $index ) {
			// Check if uagb string exist in $block_name or not.
			if ( ! is_string( $block_name ) || 0 !== strpos( $block_name, 'uagb/' ) ) {
				continue;
			}

			$_block_slug = str_replace( 'uagb/', '', $block_name );

			// This is class name and file name.
			$class_name = 'uagb-gbs-default-' . $_block_slug;

			$wp_upload_dir = \UAGB_Helper::get_uag_upload_dir_path();

			$path_and_file_name = $wp_upload_dir . $class_name . '.css';

			// If $value is enabled then only remove css default files.
			if ( 'enabled' === $value ) {
				\UAGB_Helper::remove_file( $path_and_file_name );
				continue;
			}

			// For default GBS id we are assigning default GBS id attr globalBlockStyleId = $class_name.
			$dummy_attr = array( 'globalBlockStyleId' => $class_name );

			$_block_css = \UAGB_Block_Module::get_frontend_css( $_block_slug, $dummy_attr, '', true );

			$tab_styling_css = '';
			$mob_styling_css = '';
			$desktop         = $_block_css['desktop'];

			if ( ! empty( $_block_css['tablet'] ) ) {
				$tab_styling_css .= '@media only screen and (max-width: ' . UAGB_TABLET_BREAKPOINT . 'px) {';
				$tab_styling_css .= $_block_css['tablet'];
				$tab_styling_css .= '}';
			}

			if ( ! empty( $_block_css['mobile'] ) ) {
				$mob_styling_css .= '@media only screen and (max-width: ' . UAGB_MOBILE_BREAKPOINT . 'px) {';
				$mob_styling_css .= $_block_css['mobile'];
				$mob_styling_css .= '}';
			}
			$_block_css = $desktop . $tab_styling_css . $mob_styling_css;

			$wp_filesystem = uagb_filesystem();
			$wp_filesystem->put_contents( $path_and_file_name, $_block_css, FS_CHMOD_FILE );
		}
	}

	/**
	 * Save setting - Enables or Disables the given Zip AI Module.
	 *
	 * @since 2.10.2
	 * @return void
	 */
	public function zip_ai_module_status() {
		// Check permission.
		$this->check_permission_nonce( 'uag_zip_ai_module_status' );
		// Check the post value.
		$value = $this->check_post_value();
		// Check the post module.
		$module = $this->check_post_value( 'module' );

		// If module is not a string, then abandon ship.
		if ( ! is_string( $module ) ) {
			// Since the module was not a string, set it to a blank string and send an error message as the response.
			$module = '';
			wp_send_json_error( array( 'messsage' => __( 'Module not found!', 'ultimate-addons-for-gutenberg' ) ) );
		}

		// Sanitize the module.
		$module = sanitize_text_field( $module );

		// Replace the underscores in the module name with spaces, make the word AI capital, and capitalize the first letter of each word.
		$module_name = ucwords( str_replace( '_', ' ', str_replace( 'ai', 'AI', $module ) ) );

		// Check if the Zip AI Module is available.
		if ( class_exists( '\ZipAI\Classes\Module' ) ) {
			// If the value is 'disabled', disable the Zip AI Module - else enable it.
			if ( 'disabled' === $value ) {
				if ( Zip_Ai_Module::disable( $module ) ) {
					wp_send_json_success(
						array(
							'messsage' => sprintf(
							// Translators: %s is the module name.
								__( '%s disabled!', 'ultimate-addons-for-gutenberg' ),
								$module_name
							),
						)
					);
				} else {
					wp_send_json_error(
						array(
							'messsage' => sprintf(
							// Translators: %s is the module name.
								__( 'Unable to disable %s', 'ultimate-addons-for-gutenberg' ),
								$module_name
							),
						)
					);
				}
			} else {
				if ( Zip_Ai_Module::enable( $module ) ) {
					wp_send_json_success(
						array(
							'messsage' => sprintf(
							// Translators: %s is the module name.
								__( '%s enabled!', 'ultimate-addons-for-gutenberg' ),
								$module_name
							),
						)
					);
				} else {
					wp_send_json_error(
						array(
							'messsage' => sprintf(
							// Translators: %s is the module name.
								__( 'Unable to enable %s', 'ultimate-addons-for-gutenberg' ),
								$module_name
							),
						)
					);
				}
			}
		} else {
			wp_send_json_error( array( 'messsage' => __( 'Unable to save setting.', 'ultimate-addons-for-gutenberg' ) ) );
		}
	}

	/**
	 * Ajax Request - Verify if Zip AI is authorized.
	 *
	 * @since 2.10.2
	 * @return void
	 */
	public function zip_ai_verify_authenticity() {
		// Check permission.
		$this->check_permission_nonce( 'uag_zip_ai_verify_authenticity' );

		// If the Zip AI Helper Class exists, return a success based on the authorizatoin status, else return an error.
		if ( class_exists( '\ZipAI\Classes\Helper' ) ) {
			// Send a boolean based on whether the auth token has been added.
			wp_send_json_success( array( 'is_authorized' => Zip_Ai_Helper::is_authorized() ) );
		} else {
			wp_send_json_error( array( 'messsage' => __( 'Unable to verify authenticity.', 'ultimate-addons-for-gutenberg' ) ) );
		}
	}

	/**
	 * Save setting - Enables the header titlebar.
	 *
	 * @since 2.13.1
	 * @return void
	 */
	public function enable_header_titlebar() {
		$this->check_permission_nonce( 'uag_enable_header_titlebar' );
		$value = $this->check_post_value();
		$value = 'enabled' === $value ? 'enabled' : 'disabled';
		$this->save_admin_settings( 'uag_enable_header_titlebar', sanitize_text_field( $value ) );
	}
}
