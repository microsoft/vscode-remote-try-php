<?php
/**
 * WIDGET Styling Loader for Astra theme.
 *
 * @package     Astra Builder
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
class Astra_Footer_Widget_Component_Loader {

	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'preview_scripts' ), 110 );
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
		wp_enqueue_script( 'astra-footer-widget-customizer-preview-js', ASTRA_BUILDER_FOOTER_WIDGET_URI . '/assets/js/' . $dir_name . '/customizer-preview' . $file_prefix . '.js', array( 'customize-preview', 'astra-customizer-preview-js' ), ASTRA_THEME_VERSION, true );

		// Localize variables for WIDGET JS.
		wp_localize_script(
			'astra-footer-widget-customizer-preview-js',
			'AstraBuilderWidgetData',
			array(
				'footer_widget_count' => defined( 'ASTRA_EXT_VER' ) ? Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_footer_widgets,
				'tablet_break_point'  => astra_get_tablet_breakpoint(),
				'mobile_break_point'  => astra_get_mobile_breakpoint(),
				'is_flex_based_css'   => Astra_Builder_Helper::apply_flex_based_css(),
				'has_block_editor'    => astra_has_widgets_block_editor(),
			)
		);
	}
}

/**
*  Kicking this off by creating the object of the class.
*/
new Astra_Footer_Widget_Component_Loader();
