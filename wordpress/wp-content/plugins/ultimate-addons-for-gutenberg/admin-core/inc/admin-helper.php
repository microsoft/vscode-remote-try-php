<?php
/**
 * Uag Admin Helper.
 *
 * @package Uag
 */

namespace UagAdmin\Inc;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \ZipAI\Classes\Module as Zip_Ai_Module;

/**
 * Class Admin_Helper.
 */
class Admin_Helper {

	/**
	 * Common.
	 *
	 * @var object instance
	 */
	public static $common = null;

	/**
	 * Options.
	 *
	 * @var object instance
	 */
	public static $options = null;

	/**
	 * Get Common settings.
	 *
	 * @return array.
	 */
	public static function get_common_settings() {

		$uag_versions   = self::get_rollback_versions_options();
		$changelog_data = self::get_changelog_feed_data();

		$theme_data          = \WP_Theme_JSON_Resolver::get_theme_data();
		$theme_settings      = $theme_data->get_settings();
		$theme_font_families = isset( $theme_settings['typography']['fontFamilies']['theme'] ) && is_array( $theme_settings['typography']['fontFamilies']['theme'] ) ? $theme_settings['typography']['fontFamilies']['theme'] : array();

		// Prepare to get the Zip AI Co-pilot modules.
		$zip_ai_modules = array();

		// If the Zip AI Helper is available, get the required modules and their states.
		if ( class_exists( '\ZipAI\Classes\Module' ) ) {
			$zip_ai_modules = Zip_Ai_Module::get_all_modules();
		}

		$inherit_from_theme = false !== get_option( 'uag_btn_inherit_from_theme_fallback' ) ? 'disabled' : \UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' );

		$options = array(
			'rollback_to_previous_version'       => isset( $uag_versions[0]['value'] ) ? $uag_versions[0]['value'] : '',
			'enable_beta_updates'                => \UAGB_Admin_Helper::get_admin_settings_option( 'uagb_beta', 'no' ),
			'enable_file_generation'             => \UAGB_Admin_Helper::get_admin_settings_option( '_uagb_allow_file_generation', 'enabled' ),
			'blocks_activation_and_deactivation' => self::get_blocks(),
			'enable_templates_button'            => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_templates_button', 'yes' ),
			'enable_on_page_css_button'          => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' ),
			'enable_block_condition'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_block_condition', 'disabled' ),
			'enable_masonry_gallery'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_masonry_gallery', 'enabled' ),
			'enable_quick_action_sidebar'        => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_quick_action_sidebar', 'enabled' ),
			'enable_block_responsive'            => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_block_responsive', 'enabled' ),
			'enable_dynamic_content'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_dynamic_content', 'enabled' ),
			'enable_animations_extension'        => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_animations_extension', 'enabled' ),
			'enable_header_titlebar'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_header_titlebar', 'enabled' ),
			'enable_gbs_extension'               => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' ),
			'select_font_globally'               => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_select_font_globally', array() ),
			'load_select_font_globally'          => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_select_font_globally', 'disabled' ),
			'load_fse_font_globally'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_fse_font_globally', 'disabled' ),
			'load_gfonts_locally'                => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_gfonts_locally', 'disabled' ),
			'collapse_panels'                    => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_collapse_panels', 'enabled' ),
			'copy_paste'                         => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_copy_paste', 'enabled' ),
			'preload_local_fonts'                => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_preload_local_fonts', 'disabled' ),
			'btn_inherit_from_theme'             => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' ),
			'btn_inherit_from_theme_fallback'    => $inherit_from_theme,
			'social'                             => \UAGB_Admin_Helper::get_admin_settings_option(
				'uag_social',
				array(
					'socialRegister'    => false,
					'googleClientId'    => '',
					'facebookAppId'     => '',
					'facebookAppSecret' => '',
				)
			),
			'dynamic_content_mode'               => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_dynamic_content_mode', 'popup' ),
			'preload_local_fonts'                => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_preload_local_fonts', 'disabled' ),
			'visibility_mode'                    => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_mode', 'disabled' ),
			'visibility_page'                    => self::get_visibility_page(),
			'uag_previous_versions'              => $uag_versions,
			'changelog_data'                     => $changelog_data,
			'uagb_old_user_less_than_2'          => get_option( 'uagb-old-user-less-than-2' ),
			'recaptcha_site_key_v2'              => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v2', '' ),
			'recaptcha_secret_key_v2'            => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v2', '' ),
			'recaptcha_site_key_v3'              => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v3', '' ),
			'recaptcha_secret_key_v3'            => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v3', '' ),
			'insta_linked_accounts'              => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_insta_linked_accounts', array() ),
			'spectra_global_fse_fonts'           => \UAGB_Admin_Helper::get_admin_settings_option( 'spectra_global_fse_fonts', array() ),
			'theme_fonts'                        => $theme_font_families,
			'zip_ai_modules'                     => $zip_ai_modules,
		);

		return $options;
	}

	/**
	 * Get Visibility Page
	 *
	 * @since 2.8.0
	 * @return boolean|array
	 */
	public static function get_visibility_page() {
		$page_id = \UAGB_Admin_Helper::get_admin_settings_option( 'uag_visibility_page', '' );

		if ( $page_id ) {
			return array(
				'value' => $page_id,
				'label' => \get_the_title( $page_id ),
			);
		}
		return false;
	}

	/**
	 * Get Changelogs from API.
	 *
	 * @since 2.0.0
	 * @return array $changelog_data Changelog Data.
	 */
	public static function get_changelog_feed_data() {
		$posts          = json_decode( wp_remote_retrieve_body( wp_remote_get( 'https://wpspectra.com/wp-json/wp/v2/changelog?per_page=3' ) ) );
		$changelog_data = array();

		if ( isset( $posts ) && is_array( $posts ) ) {
			foreach ( $posts as $post ) {

				$changelog_data[] = array(
					'title'       => $post->title->rendered,
					'date'        => gmdate( 'l F j, Y', strtotime( $post->date ) ),
					'description' => $post->content->rendered,
					'link'        => $post->link,
				);
			}
		}

		return $changelog_data;
	}
	/**
	 * Get blocks.
	 */
	public static function get_blocks() {
		// Get all blocks.
		$list_blocks    = \UAGB_Helper::$block_list;
		$default_blocks = array();

		// Set all extension to enabled.
		foreach ( $list_blocks as $slug => $value ) {
			$_slug                    = str_replace( 'uagb/', '', $slug );
			$default_blocks[ $_slug ] = $_slug;
		}

		// Escape attrs.
		$default_blocks = array_map( 'esc_attr', $default_blocks );
		$saved_blocks   = \UAGB_Admin_Helper::get_admin_settings_option( '_uagb_blocks', array() );

		return wp_parse_args( $saved_blocks, $default_blocks );
	}

	/**
	 * Get options.
	 */
	public static function get_options() {

		$general_settings          = self::get_common_settings();
		$shareable_common_settings = \UAGB_Admin_Helper::get_admin_settings_shareable_data();
		$options                   = array_merge( $general_settings, $shareable_common_settings );
		$options                   = apply_filters( 'uag_global_data_options', $options );

		return $options;
	}

	/**
	 * Get Rollback versions.
	 *
	 * @since 1.23.0
	 * @return array
	 * @access public
	 */
	public static function get_rollback_versions_options() {

		$rollback_versions = \UAGB_Admin_Helper::get_instance()->get_rollback_versions();

		$rollback_versions_options = array();

		foreach ( $rollback_versions as $version ) {

			$version = array(
				'label' => $version,
				'value' => $version,

			);

			$rollback_versions_options[] = $version;
		}

		return $rollback_versions_options;
	}
	/**
	 * Sort Rollback versions.
	 *
	 * @param string $prev Previous Version.
	 * @param string $next Next Version.
	 *
	 * @since 1.23.0
	 * @return array
	 * @access public
	 */
	public static function sort_rollback_versions( $prev, $next ) {

		if ( version_compare( $prev, $next, '==' ) ) {
			return 0;
		}

		if ( version_compare( $prev, $next, '>' ) ) {
			return -1;
		}

		return 1;
	}
}
