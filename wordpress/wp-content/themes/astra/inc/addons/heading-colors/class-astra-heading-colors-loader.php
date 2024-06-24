<?php
/**
 * Heading Colors Loader for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Initialization
 *
 * @since 2.2.0
 */
class Astra_Heading_Colors_Loader {

	/**
	 * Constructor
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		add_filter( 'astra_theme_defaults', array( $this, 'theme_defaults' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ), 2 );
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ), 110 );
		// Load Google fonts.
		add_action( 'astra_get_fonts', array( $this, 'add_fonts' ), 1 );
	}

	/**
	 * Enqueue google fonts.
	 *
	 * @since 2.2.0
	 */
	public function add_fonts() {

		$font_family_h1 = astra_get_option( 'font-family-h1' );
		$font_weight_h1 = astra_get_option( 'font-weight-h1' );
		Astra_Fonts::add_font( $font_family_h1, $font_weight_h1 );

		$font_family_h2 = astra_get_option( 'font-family-h2' );
		$font_weight_h2 = astra_get_option( 'font-weight-h2' );
		Astra_Fonts::add_font( $font_family_h2, $font_weight_h2 );

		$font_family_h3 = astra_get_option( 'font-family-h3' );
		$font_weight_h3 = astra_get_option( 'font-weight-h3' );
		Astra_Fonts::add_font( $font_family_h3, $font_weight_h3 );

		if ( astra_has_gcp_typo_preset_compatibility() ) {

			$font_family_h4 = astra_get_option( 'font-family-h4' );
			$font_weight_h4 = astra_get_option( 'font-weight-h4' );
			Astra_Fonts::add_font( $font_family_h4, $font_weight_h4 );

			$font_family_h5 = astra_get_option( 'font-family-h5' );
			$font_weight_h5 = astra_get_option( 'font-weight-h5' );
			Astra_Fonts::add_font( $font_family_h5, $font_weight_h5 );

			$font_family_h6 = astra_get_option( 'font-family-h6' );
			$font_weight_h6 = astra_get_option( 'font-weight-h6' );
			Astra_Fonts::add_font( $font_family_h6, $font_weight_h6 );

		}

		$theme_btn_font_family = astra_get_option( 'font-family-button' );
		$theme_btn_font_weight = astra_get_option( 'font-weight-button' );
		Astra_Fonts::add_font( $theme_btn_font_family, $theme_btn_font_weight );

		$theme_secondary_btn_font_family = astra_get_option( 'secondary-font-family-button' );
		$theme_secondary_btn_font_weight = astra_get_option( 'secondary-font-weight-button' );
		Astra_Fonts::add_font( $theme_secondary_btn_font_family, $theme_secondary_btn_font_weight );

		$header_btn_font_family = astra_get_option( 'primary-header-button-font-family' );
		$header_btn_font_weight = astra_get_option( 'primary-header-button-font-weight' );
		Astra_Fonts::add_font( $header_btn_font_family, $header_btn_font_weight );
	}

	/**
	 * Set Options Default Values
	 *
	 * @param  array $defaults  Astra options default value array.
	 * @return array
	 *
	 * @since 2.2.0
	 */
	public function theme_defaults( $defaults ) {

		$astra_options = Astra_Theme_Options::get_astra_options();
		/**
		 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
		 *
		 * @since 4.0.0
		 */
		$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

		/**
		* Heading Tags <h1> to <h6>
		*/
		$defaults['h1-color'] = '';
		$defaults['h2-color'] = '';
		$defaults['h3-color'] = '';
		$defaults['h4-color'] = '';
		$defaults['h5-color'] = '';
		$defaults['h6-color'] = '';

		// Header <H1>.
		$defaults['font-family-h1'] = 'inherit';
		$defaults['font-weight-h1'] = 'inherit';
		$defaults['font-extras-h1'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h1'] ) && isset( $astra_options['line-height-h1'] ) ? $astra_options['line-height-h1'] : '1.4',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h1'] ) && isset( $astra_options['text-transform-h1'] ) ? $astra_options['text-transform-h1'] : '',
			'text-decoration'     => '',
		);

		// Header <H2>.
		$defaults['font-family-h2'] = 'inherit';
		$defaults['font-weight-h2'] = 'inherit';
		$defaults['font-extras-h2'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h2'] ) && isset( $astra_options['line-height-h2'] ) ? $astra_options['line-height-h2'] : '1.3',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h2'] ) && isset( $astra_options['text-transform-h2'] ) ? $astra_options['text-transform-h2'] : '',
			'text-decoration'     => '',
		);

		// Header <H3>.
		$defaults['font-family-h3'] = 'inherit';
		$defaults['font-weight-h3'] = 'inherit';
		$defaults['font-extras-h3'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h3'] ) && isset( $astra_options['line-height-h3'] ) ? $astra_options['line-height-h3'] : '1.3',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h3'] ) && isset( $astra_options['text-transform-h3'] ) ? $astra_options['text-transform-h3'] : '',
			'text-decoration'     => '',
		);

		// Header <H4>.
		$defaults['font-family-h4'] = 'inherit';
		$defaults['font-weight-h4'] = 'inherit';
		$defaults['font-extras-h4'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h4'] ) && isset( $astra_options['line-height-h4'] ) ? $astra_options['line-height-h4'] : '1.2',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h4'] ) && isset( $astra_options['text-transform-h4'] ) ? $astra_options['text-transform-h4'] : '',
			'text-decoration'     => '',
		);

		// Header <H5>.
		$defaults['font-family-h5'] = 'inherit';
		$defaults['font-weight-h5'] = 'inherit';
		$defaults['font-extras-h5'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h5'] ) && isset( $astra_options['line-height-h5'] ) ? $astra_options['line-height-h5'] : '1.2',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h5'] ) && isset( $astra_options['text-transform-h5'] ) ? $astra_options['text-transform-h5'] : '',
			'text-decoration'     => '',
		);

		// Header <H6>.
		$defaults['font-family-h6'] = 'inherit';
		$defaults['font-weight-h6'] = 'inherit';
		$defaults['font-extras-h6'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-h6'] ) && isset( $astra_options['line-height-h6'] ) ? $astra_options['line-height-h6'] : '1.25',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-h6'] ) && isset( $astra_options['text-transform-h6'] ) ? $astra_options['text-transform-h6'] : '',
			'text-decoration'     => '',
		);

		/**
		 * Theme button Font Defaults
		 */
		$defaults['font-weight-button']           = $apply_new_default_color_typo_values ? '500' : 'inherit';
		$defaults['secondary-font-weight-button'] = $apply_new_default_color_typo_values ? '500' : 'inherit';
		$defaults['font-family-button']           = 'inherit';
		$defaults['secondary-font-family-button'] = 'inherit';
		$defaults['font-size-button']             = array(
			'desktop'      => $apply_new_default_color_typo_values ? '16' : '',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);
		$defaults['secondary-font-size-button']   = array(
			'desktop'      => $apply_new_default_color_typo_values ? '16' : '',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);

		$defaults['font-extras-button'] = array(
			'line-height'         => ! isset( $astra_options['font-extras-button'] ) && isset( $astra_options['theme-btn-line-height'] ) ? $astra_options['theme-btn-line-height'] : 1,
			'line-height-unit'    => 'em',
			'letter-spacing'      => ! isset( $astra_options['font-extras-button'] ) && isset( $astra_options['theme-btn-letter-spacing'] ) ? $astra_options['theme-btn-letter-spacing'] : '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['font-extras-button'] ) && isset( $astra_options['text-transform-button'] ) ? $astra_options['text-transform-button'] : '',
			'text-decoration'     => '',
		);

		$defaults['secondary-font-extras-button'] = array(
			'line-height'         => ! isset( $astra_options['secondary-font-extras-button'] ) && isset( $astra_options['secondary-theme-btn-line-height'] ) ? $astra_options['secondary-theme-btn-line-height'] : 1,
			'line-height-unit'    => 'em',
			'letter-spacing'      => ! isset( $astra_options['secondary-font-extras-button'] ) && isset( $astra_options['secondary-theme-btn-letter-spacing'] ) ? $astra_options['secondary-theme-btn-letter-spacing'] : '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['secondary-font-extras-button'] ) && isset( $astra_options['secondary-text-transform-button'] ) ? $astra_options['secondary-text-transform-button'] : '',
			'text-decoration'     => '',
		);

		return $defaults;
	}

	/**
	 * Load color configs for the Heading Colors.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @since 2.2.0
	 */
	public function customize_register( $wp_customize ) {

		/**
		 * Register Panel & Sections
		 */
		require_once ASTRA_THEME_HEADING_COLORS_DIR . 'customizer/class-astra-heading-colors-configs.php';// phpcs:ignore: WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Customizer Preview
	 *
	 * @since 2.2.0
	 */
	public function preview_scripts() {
		/**
		 * Load unminified if SCRIPT_DEBUG is true.
		 */
		/* Directory and Extension */
		$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';
		$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'astra-heading-colors-customizer-preview-js', ASTRA_THEME_HEADING_COLORS_URI . 'assets/js/' . $dir_name . '/customizer-preview' . $file_prefix . '.js', array( 'customize-preview', 'astra-customizer-preview-js' ), ASTRA_THEME_VERSION, true );

		wp_localize_script(
			'astra-heading-colors-customizer-preview-js',
			'astraHeadingColorOptions',
			array(
				'maybeApplyHeadingColorForTitle' => astra_has_global_color_format_support(),
			)
		);

	}
}

/**
*  Kicking this off by creating the object of the class.
*/
new Astra_Heading_Colors_Loader();
