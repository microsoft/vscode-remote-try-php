<?php
/**
 * Above Header - Dynamic CSS
 *
 * @package astra-builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Above Header Row.
 */
add_filter( 'astra_dynamic_theme_css', 'astra_above_header_row_setting', 11 );

/**
 * Above Header Row - Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return String Generated dynamic CSS for Heading Colors.
 *
 * @since 3.0.0
 */
function astra_above_header_row_setting( $dynamic_css, $dynamic_css_filtered = '' ) {

	if ( ! is_customize_preview() && ( ! Astra_Builder_Helper::is_row_empty( 'above', 'header', 'desktop' ) && ! Astra_Builder_Helper::is_row_empty( 'above', 'header', 'mobile' ) ) ) {
		return $dynamic_css;
	}

	$parse_css = '';

	// Common CSS options.
	$hba_header_height  = astra_get_option( 'hba-header-height' );
	$hba_header_divider = astra_get_option( 'hba-header-separator' );
	$hba_border_color   = astra_get_option( 'hba-header-bottom-border-color' );

	// Background CSS options.
	$hba_header_bg_obj  = astra_get_option( 'hba-header-bg-obj-responsive' );
	$desktop_background = isset( $hba_header_bg_obj['desktop']['background-color'] ) ? $hba_header_bg_obj['desktop']['background-color'] : '';
	$tablet_background  = isset( $hba_header_bg_obj['tablet']['background-color'] ) ? $hba_header_bg_obj['tablet']['background-color'] : '';
	$mobile_background  = isset( $hba_header_bg_obj['mobile']['background-color'] ) ? $hba_header_bg_obj['mobile']['background-color'] : '';

	// Header Height.
	$hba_header_height_desktop = ( isset( $hba_header_height['desktop'] ) && ! empty( $hba_header_height['desktop'] ) ) ? $hba_header_height['desktop'] : '';
	$hba_header_height_tablet  = ( isset( $hba_header_height['tablet'] ) && ! empty( $hba_header_height['tablet'] ) ) ? $hba_header_height['tablet'] : '';
	$hba_header_height_mobile  = ( isset( $hba_header_height['mobile'] ) && ! empty( $hba_header_height['mobile'] ) ) ? $hba_header_height['mobile'] : '';

	/**
	 * Above Header General options
	 */
	$common_css_output = array(
		'.ast-above-header .main-header-bar-navigation' => array(
			'height' => '100%',
		),
		'.ast-header-break-point .ast-mobile-header-wrap .ast-above-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item .menu-link' => array(
			'border' => 'none',
		),
		'.ast-header-break-point .ast-mobile-header-wrap .ast-above-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item-has-children > .ast-menu-toggle::before' => array(
			'font-size' => '.6rem',
		),
		'.ast-header-break-point .ast-mobile-header-wrap .ast-above-header-wrap .main-header-bar-navigation .ast-submenu-expanded > .ast-menu-toggle::before' => array(
			'transform' => 'rotateX(180deg)',
		),
		'.ast-mobile-header-wrap .ast-above-header-bar , .ast-above-header-bar .site-above-header-wrap' => array(
			'min-height' => astra_get_css_value( $hba_header_height_desktop, 'px' ),
		),
		'.ast-desktop .ast-above-header-bar .main-header-menu > .menu-item' => array(
			'line-height' => astra_get_css_value( $hba_header_height_desktop, 'px' ),
		),
		'.ast-desktop .ast-above-header-bar .ast-header-woo-cart, .ast-desktop .ast-above-header-bar .ast-header-edd-cart' => array(
			'line-height' => astra_get_css_value( $hba_header_height_desktop, 'px' ),
		),
	);

	// Apply border only when it has positive value.
	if ( '' !== $hba_header_divider && 'inherit' !== $hba_header_divider ) {
		$common_css_output['.ast-above-header-bar'] = array(
			'border-bottom-width' => astra_get_css_value( $hba_header_divider, 'px' ),
			'border-bottom-color' => esc_attr( $hba_border_color ),
			'border-bottom-style' => 'solid',
		);
	} else {
		$common_css_output['.ast-above-header-bar'] = array(
			'border-bottom-style' => 'none',
		);
	}

	$parse_css .= astra_parse_css( $common_css_output );

	// Above Header Background Responsive - Desktop.
	$desktop_bg = array(
		'.ast-above-header.ast-above-header-bar'        => astra_get_responsive_background_obj( $hba_header_bg_obj, 'desktop' ),
		'.ast-header-break-point .ast-above-header-bar' => array(
			'background-color' => esc_attr( $desktop_background ),
		),
	);
	$parse_css .= astra_parse_css( $desktop_bg );

	// Above Header Background Responsive - Tablet.
	$tablet_bg  = array(
		'.ast-above-header.ast-above-header-bar'        => astra_get_responsive_background_obj( $hba_header_bg_obj, 'tablet' ),
		'.ast-header-break-point .ast-above-header-bar' => array(
			'background-color' => esc_attr( $tablet_background ),
		),
		'.ast-mobile-header-wrap .ast-above-header-bar , .ast-above-header-bar .site-above-header-wrap' => array(
			'min-height' => astra_get_css_value( $hba_header_height_tablet, 'px' ),
		),
		'#masthead .ast-mobile-header-wrap .ast-above-header-bar' => array(
			'padding-left'  => '20px',
			'padding-right' => '20px',
		),
	);
	$parse_css .= astra_parse_css( $tablet_bg, '', astra_get_tablet_breakpoint() );

	// Above Header Background Responsive - Mobile.
	$mobile_bg  = array(
		'.ast-above-header.ast-above-header-bar'        => astra_get_responsive_background_obj( $hba_header_bg_obj, 'mobile' ),
		'.ast-header-break-point .ast-above-header-bar' => array(
			'background-color' => esc_attr( $mobile_background ),
		),
		'.ast-mobile-header-wrap .ast-above-header-bar , .ast-above-header-bar .site-above-header-wrap' => array(
			'min-height' => astra_get_css_value( $hba_header_height_mobile, 'px' ),
		),
	);
	$parse_css .= astra_parse_css( $mobile_bg, '', astra_get_mobile_breakpoint() );

	// Trim white space for faster page loading.
	$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $parse_css );

	$_section = 'section-above-header-builder';

	$selector = '.site-above-header-wrap[data-section="ast_header_above"]';

	$parent_selector = '.ast-above-header.ast-above-header-bar, .ast-header-break-point #masthead.site-header .ast-above-header-bar';

	$dynamic_css .= Astra_Extended_Base_Dynamic_CSS::prepare_advanced_margin_padding_css( $_section, $parent_selector );

	$dynamic_css .= Astra_Builder_Base_Dynamic_CSS::prepare_visibility_css( $_section, '.ast-above-header-bar', 'block', $mobile_tablet_default_display = 'grid' );

	return $dynamic_css;
}
