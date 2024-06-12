<?php
/**
 * Button Styling Loader for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Initialization
 *
 * @since 3.0.0
 */
class Astra_Footer_Button_Component_Loader {

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ), 110 );
		add_action( 'astra_get_fonts', array( $this, 'add_fonts' ), 1 );
	}

	/**
	 * Add Font Family Callback
	 *
	 * @return void
	 */
	public function add_fonts() {
		/**
		 * Footer - Button
		 */
		$num_of_footer_button = Astra_Builder_Helper::$num_of_footer_button;
		for ( $index = 1; $index <= $num_of_footer_button; $index++ ) {
			if ( ! Astra_Builder_Helper::is_component_loaded( 'button-' . $index, 'footer' ) ) {
				continue;
			}

			$_prefix = 'button' . $index;

			$btn_font_family = astra_get_option( 'footer-' . $_prefix . '-font-family' );
			$btn_font_weight = astra_get_option( 'footer-' . $_prefix . '-font-weight' );
			Astra_Fonts::add_font( $btn_font_family, $btn_font_weight );
		}
	}

	/**
	 * Customizer Preview
	 *
	 * @since 3.0.0
	 */
	public function preview_scripts() {
		/**
		 * Load unminified if SCRIPT_DEBUG is true.
		 */
		/* Directory and Extension */
		$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';
		$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_script( 'astra-footer-button-customizer-preview-js', ASTRA_FOOTER_BUTTON_URI . '/assets/js/' . $dir_name . '/customizer-preview' . $file_prefix . '.js', array( 'customize-preview', 'astra-customizer-preview-js' ), ASTRA_THEME_VERSION, true );

		// Localize variables for Button JS.
		wp_localize_script(
			'astra-footer-button-customizer-preview-js',
			'AstraBuilderFooterButtonData',
			array(
				'component_limit'    => defined( 'ASTRA_EXT_VER' ) ? Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_footer_button,
				'tablet_break_point' => astra_get_tablet_breakpoint(),
				'mobile_break_point' => astra_get_mobile_breakpoint(),
			)
		);
	}
}

/**
*  Kicking this off by creating the object of the class.
*/
new Astra_Footer_Button_Component_Loader();
