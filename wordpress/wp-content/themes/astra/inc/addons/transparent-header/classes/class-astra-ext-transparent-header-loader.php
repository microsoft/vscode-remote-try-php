<?php
/**
 * Transparent Header - Customizer.
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Ext_Transparent_Header_Loader' ) ) {

	/**
	 * Customizer Initialization
	 *
	 * @since 1.0.0
	 */
	class Astra_Ext_Transparent_Header_Loader {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {

			add_filter( 'astra_theme_defaults', array( $this, 'theme_defaults' ) );
			add_action( 'customize_preview_init', array( $this, 'preview_scripts' ) );
			add_action( 'customize_register', array( $this, 'customize_register' ), 2 );

		}

		/**
		 * Set Options Default Values
		 *
		 * @param  array $defaults  Astra options default value array.
		 * @return array
		 */
		public function theme_defaults( $defaults ) {

			// Header - Transparent.
			$defaults['transparent-header-logo']           = '';
			$defaults['transparent-header-retina-logo']    = '';
			$defaults['different-transparent-logo']        = 0;
			$defaults['different-transparent-retina-logo'] = 0;
			$defaults['transparent-header-logo-width']     = array(
				'desktop' => 150,
				'tablet'  => 120,
				'mobile'  => 100,
			);
			$defaults['transparent-header-enable']         = 0;
			/**
			 * Old option for 404, search and archive pages.
			 *
			 * For default value on separate option this setting is in use.
			 */
			$defaults['transparent-header-disable-archive']            = 1;
			$defaults['transparent-header-disable-latest-posts-index'] = 1;
			$defaults['transparent-header-on-devices']                 = 'both';
			$defaults['transparent-header-main-sep']                   = '';
			$defaults['transparent-header-main-sep-color']             = '';

			/**
			* Transparent Header
			*/
			$defaults['transparent-header-bg-color']           = '';
			$defaults['transparent-header-color-site-title']   = '';
			$defaults['transparent-header-color-h-site-title'] = '';
			$defaults['transparent-menu-bg-color']             = '';
			$defaults['transparent-menu-color']                = '';
			$defaults['transparent-menu-h-color']              = '';
			$defaults['transparent-submenu-bg-color']          = '';
			$defaults['transparent-submenu-color']             = '';
			$defaults['transparent-submenu-h-color']           = '';
			$defaults['transparent-header-logo-color']         = '';

			/**
			* Transparent Header Responsive Colors
			*/
			$defaults['transparent-header-bg-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['hba-transparent-header-bg-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['hbb-transparent-header-bg-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-header-color-site-title-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-header-color-h-site-title-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-menu-bg-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-menu-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-menu-h-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-submenu-bg-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-submenu-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-submenu-h-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['transparent-content-section-text-color-responsive']   = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);
			$defaults['transparent-content-section-link-color-responsive']   = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);
			$defaults['transparent-content-section-link-h-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			return $defaults;
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {

			// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			/**
			 * Register Panel & Sections
			 */
			require_once ASTRA_THEME_TRANSPARENT_HEADER_DIR . 'classes/class-astra-transparent-header-panels-and-sections.php';

			/**
			 * Sections
			 */
			require_once ASTRA_THEME_TRANSPARENT_HEADER_DIR . 'classes/sections/class-astra-customizer-colors-transparent-header-configs.php';
			// Check Transparent Header is activated.
			require_once ASTRA_THEME_TRANSPARENT_HEADER_DIR . 'classes/sections/class-astra-customizer-transparent-header-configs.php';
			// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}

		/**
		 * Customizer Preview
		 */
		public function preview_scripts() {
			/**
			 * Load unminified if SCRIPT_DEBUG is true.
			 */
			/* Directory and Extension */
			$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';
			$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'astra-transparent-header-customizer-preview-js', ASTRA_THEME_TRANSPARENT_HEADER_URI . 'assets/js/' . $dir_name . '/customizer-preview' . $file_prefix . '.js', array( 'customize-preview', 'astra-customizer-preview-js' ), ASTRA_THEME_VERSION, true );

			// Localize variables for further JS.
			wp_localize_script(
				'astra-transparent-header-customizer-preview-js',
				'AstraBuilderTransparentData',
				array(
					'is_astra_hf_builder_active' => Astra_Builder_Helper::$is_header_footer_builder_active,
					'is_flex_based_css'          => Astra_Builder_Helper::apply_flex_based_css(),
					'transparent_header_devices' => astra_get_option( 'transparent-header-on-devices' ),
				)
			);
		}
	}
}

/**
*  Kicking this off by calling 'get_instance()' method
*/
Astra_Ext_Transparent_Header_Loader::get_instance();
