<?php
/**
 * Logo SVG Icons - Dynamic CSS
 *
 * @package Astra
 * @since 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_logo_svg_icons_dynamic_css', 20 );

/**
 * Main styles for the logo svg icons.
 *
 * @param string $dynamic_css
 * @return string
 * @since 4.7.0
 */
function astra_logo_svg_icons_dynamic_css( $dynamic_css ) {

	if ( ! astra_logo_svg_icon() ) {
		return $dynamic_css;
	}

	$header_logo_width         = astra_get_option( 'ast-header-responsive-logo-width' );
	$logo_svg_icon_color       = astra_get_option( 'logo-svg-icon-color' );
	$logo_svg_site_title_gap   = astra_get_option( 'logo-svg-site-title-gap' );
	$logo_svg_icon_hover_color = astra_get_option( 'logo-svg-icon-hover-color' );

	$enabled_logo_title_inline = astra_get_option( 'logo-title-inline' );

	/**
	 * Start: Desktop related styles
	 */
	$desktop_css_output = array(
		'header .ast-logo-svg-icon'           => array(
			'display' => 'inline-flex', // Fix for the vertical alignment issue with the SVG logo.
		),
		'header .ast-logo-svg-icon svg'       => array(
			'width' => astra_get_css_value( $header_logo_width['desktop'], 'px', '30' ),
			'fill'  => esc_attr( $logo_svg_icon_color ),
		),
		'header .ast-logo-svg-icon:hover svg' => array(
			'fill' => esc_attr( $logo_svg_icon_hover_color ),
		),
	);

	if ( isset( $logo_svg_site_title_gap['desktop'] ) ) {
		if ( $enabled_logo_title_inline ) {
			$desktop_css_output['.ast-logo-title-inline .ast-site-identity'] = array(
				'gap' => astra_get_css_value( $logo_svg_site_title_gap['desktop'], 'px' ),
			);
		} else {
			$desktop_css_output['.ast-site-identity .ast-logo-svg-icon'] = array(
				'margin-bottom' => astra_get_css_value( $logo_svg_site_title_gap['desktop'], 'px' ),
			);
		}
	}

	/**
	 * End: Desktop related styles
	 */

	/**
	 * Start: Tablet related styles
	 */
	$tablet_css_output = array(
		'header .ast-logo-svg-icon svg' => array(
			'width' => astra_get_css_value( $header_logo_width['tablet'], 'px', '30' ),
		),
	);

	if ( isset( $logo_svg_site_title_gap['tablet'] ) ) {
		if ( $enabled_logo_title_inline ) {
			$tablet_css_output['.ast-logo-title-inline .ast-site-identity'] = array(
				'gap' => astra_get_css_value( $logo_svg_site_title_gap['tablet'], 'px' ),
			);
		} else {
			$tablet_css_output['.ast-site-identity .ast-logo-svg-icon'] = array(
				'margin-bottom' => astra_get_css_value( $logo_svg_site_title_gap['tablet'], 'px' ),
			);
		}
	}
	/**
	 * End: Tablet related styles
	 */

	/**
	 * Start: Mobile related styles
	 */
	$mobile_css_output = array(
		'header .ast-logo-svg-icon svg' => array(
			'width' => astra_get_css_value( $header_logo_width['mobile'], 'px', '30' ),
		),
	);

	if ( isset( $logo_svg_site_title_gap['mobile'] ) ) {
		if ( $enabled_logo_title_inline ) {
			$mobile_css_output['.ast-logo-title-inline .ast-site-identity'] = array(
				'gap' => astra_get_css_value( $logo_svg_site_title_gap['mobile'], 'px' ),
			);
		} else {
			$mobile_css_output['.ast-site-identity .ast-logo-svg-icon'] = array(
				'margin-bottom' => astra_get_css_value( $logo_svg_site_title_gap['mobile'], 'px' ),
			);
		}
	}
	/**
	 * End: Mobile related styles
	 */

	$dynamic_css .= astra_parse_css( $desktop_css_output );
	$dynamic_css .= astra_parse_css( $tablet_css_output, '', astra_get_tablet_breakpoint() );
	$dynamic_css .= astra_parse_css( $mobile_css_output, '', astra_get_mobile_breakpoint() );

	return $dynamic_css;

}
