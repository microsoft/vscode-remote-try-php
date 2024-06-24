<?php
/**
 * WIdget control - Dynamic CSS
 *
 * @package Astra Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Heading Colors
 */
add_filter( 'astra_dynamic_theme_css', 'astra_fb_widget_dynamic_css' );

/**
 * Whether to fix the footer right-margin space not working case or not.
 *
 * As this affects the frontend, added this backward compatibility for existing users.
 *
 * @since 3.6.7
 * @return boolean false if it is an existing user, true if not.
 */
function astra_support_footer_widget_right_margin() {
	$astra_settings                                       = get_option( ASTRA_THEME_SETTINGS );
	$astra_settings['support-footer-widget-right-margin'] = isset( $astra_settings['support-footer-widget-right-margin'] ) ? false : true;
	return apply_filters( 'astra_apply_right_margin_footer_widget_css', $astra_settings['support-footer-widget-right-margin'] );
}

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return String Generated dynamic CSS for Heading Colors.
 *
 * @since 3.0.0
 */
function astra_fb_widget_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_widgets; $index++ ) {

		if ( ! Astra_Builder_Helper::is_component_loaded( 'widget-' . $index, 'footer' ) ) {
			continue;
		}

		$selector = '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' . $index . '"]';

		$alignment = astra_get_option( 'footer-widget-alignment-' . $index );

		$desktop_alignment = ( isset( $alignment['desktop'] ) ) ? $alignment['desktop'] : '';
		$tablet_alignment  = ( isset( $alignment['tablet'] ) ) ? $alignment['tablet'] : '';
		$mobile_alignment  = ( isset( $alignment['mobile'] ) ) ? $alignment['mobile'] : '';

		/**
		 * Widget CSS.
		 */
		if ( Astra_Builder_Helper::apply_flex_based_css() ) {
			$footer_widget_selector = $selector . '.footer-widget-area-inner';
		} else {
			$footer_widget_selector = $selector . ' .footer-widget-area-inner';
		}
		$css_output_desktop = array(
			$footer_widget_selector => array(
				'text-align' => $desktop_alignment,
			),
		);
		$css_output_tablet  = array(
			$footer_widget_selector => array(
				'text-align' => $tablet_alignment,
			),
		);
		$css_output_mobile  = array(
			$footer_widget_selector => array(
				'text-align' => $mobile_alignment,
			),
		);
		
		/* Parse CSS from array() */
		$css_output  = astra_parse_css( $css_output_desktop );
		$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
		$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );
		
		$dynamic_css .= $css_output;
		
	}

	if ( astra_support_footer_widget_right_margin() && ! is_customize_preview() ) {
		$footer_area_css_output = array(
			'.footer-widget-area.widget-area.site-footer-focus-item' => array(
				'width' => 'auto',
			),
		);
		$dynamic_css           .= astra_parse_css( $footer_area_css_output );
	}

	$dynamic_css .= Astra_Widget_Component_Dynamic_CSS::astra_widget_dynamic_css( 'footer' );

	return $dynamic_css;
}
