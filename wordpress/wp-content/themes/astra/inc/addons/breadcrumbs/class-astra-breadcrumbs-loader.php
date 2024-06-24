<?php
/**
 * Breadcrumbs Loader for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Breadcrumbs_Loader' ) ) {

	/**
	 * Customizer Initialization
	 *
	 * @since 1.7.0
	 */
	class Astra_Breadcrumbs_Loader {

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
			add_action( 'customize_preview_init', array( $this, 'preview_scripts' ), 110 );
			add_action( 'customize_register', array( $this, 'customize_register' ), 2 );
			// Load Google fonts.
			add_action( 'astra_get_fonts', array( $this, 'add_fonts' ), 1 );
		}

		/**
		 * Enqueue google fonts.
		 *
		 * @return void
		 */
		public function add_fonts() {
			$breadcrumb_font_family = astra_get_option( 'breadcrumb-font-family' );
			$breadcrumb_font_weight = astra_get_option( 'breadcrumb-font-weight' );
			Astra_Fonts::add_font( $breadcrumb_font_family, $breadcrumb_font_weight );
		}

		/**
		 * Set Options Default Values
		 *
		 * @param  array $defaults  Astra options default value array.
		 * @return array
		 */
		public function theme_defaults( $defaults ) {

			$astra_options = Astra_Theme_Options::get_astra_options();

			/**
			 * Breadcrumb Typography
			 */
			$defaults['breadcrumb-font-family'] = 'inherit';
			$defaults['breadcrumb-font-weight'] = 'inherit';

			/**
			 * Breadcrumb Responsive Colors
			 */
			$defaults['breadcrumb-text-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['breadcrumb-active-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['breadcrumb-hover-color-responsive'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['breadcrumb-separator-color'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['breadcrumb-bg-color'] = array(
				'desktop' => '',
				'tablet'  => '',
				'mobile'  => '',
			);

			$defaults['breadcrumb-spacing'] = array(
				'desktop'      => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'tablet'       => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'mobile'       => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			);

			/**
			 * Breadcrumb Font Defaults
			 */
			$defaults['breadcrumb-font-family'] = 'inherit';
			$defaults['breadcrumb-font-weight'] = 'inherit';
			$defaults['breadcrumb-font-size']   = array(
				'desktop'      => '',
				'tablet'       => '',
				'mobile'       => '',
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			);
			$defaults['breadcrumb-font-extras'] = array(
				'line-height'         => ! isset( $astra_options['breadcrumb-font-extras'] ) && isset( $astra_options['breadcrumb-line-height'] ) ? $astra_options['breadcrumb-line-height'] : '',
				'line-height-unit'    => 'em',
				'letter-spacing'      => '',
				'letter-spacing-unit' => 'px',
				'text-transform'      => ! isset( $astra_options['breadcrumb-font-extras'] ) && isset( $astra_options['breadcrumb-text-transform'] ) ? $astra_options['breadcrumb-text-transform'] : '',
				'text-decoration'     => '',
			);

			/**
			 * Breadcrumb Separator defaults
			 */

			$defaults['breadcrumb-separator-selector'] = '\003E';
			$defaults['breadcrumb-separator']          = '\00bb';

			return $defaults;
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {

			/**
			 * Register Panel & Sections
			 */
			// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'customizer/class-astra-breadcrumbs-configs.php';
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'customizer/class-astra-breadcrumbs-color-configs.php';
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'customizer/class-astra-breadcrumbs-typo-configs.php';
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
			wp_enqueue_script( 'astra-breadcrumbs-customizer-preview-js', ASTRA_THEME_BREADCRUMBS_URI . 'assets/js/' . $dir_name . '/customizer-preview' . $file_prefix . '.js', array( 'customize-preview', 'astra-customizer-preview-js' ), ASTRA_THEME_VERSION, true );
		}
	}
}

/**
*  Kicking this off by calling 'get_instance()' method
*/
Astra_Breadcrumbs_Loader::get_instance();
