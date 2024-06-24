<?php
/**
 * WIDGET component.
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

define( 'ASTRA_BUILDER_FOOTER_WIDGET_DIR', ASTRA_THEME_DIR . 'inc/builder/type/footer/widget' );
define( 'ASTRA_BUILDER_FOOTER_WIDGET_URI', ASTRA_THEME_URI . 'inc/builder/type/footer/widget' );

/**
 * Heading Initial Setup
 *
 * @since 3.0.0
 */
class Astra_Footer_Widget_Component {

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_BUILDER_FOOTER_WIDGET_DIR . '/class-astra-footer-widget-component-loader.php';

		// Include front end files.
		if ( ! is_admin() || Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
			require_once ASTRA_BUILDER_FOOTER_WIDGET_DIR . '/dynamic-css/dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}
}

/**
 *  Kicking this off by creating an object.
 */
new Astra_Footer_Widget_Component();
