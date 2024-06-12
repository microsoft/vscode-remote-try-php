<?php
/**
 * Global color palette - Dynamic CSS
 *
 * @package astra-builder
 * @since 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_generate_global_palette_style' );

/**
 * Generate palette CSS variable styles on the front end.
 *
 * @since 3.7.0
 * @param string $dynamic_css dynamic css.
 * @return string
 */
function astra_generate_global_palette_style( $dynamic_css ) {

	$global_palette   = astra_get_option( 'global-color-palette' );
	$palette_style    = array();
	$variable_prefix  = Astra_Global_Palette::get_css_variable_prefix();
	$palette_css_vars = array();

	if ( isset( $global_palette['palette'] ) ) {
		foreach ( $global_palette['palette'] as $key => $color ) {
			$palette_key = str_replace( '--', '-', $variable_prefix ) . $key;

			$palette_style[ ':root .has' . $palette_key . '-color' ] = array(
				'color' => 'var(' . $variable_prefix . $key . ')',
			);

			$palette_style[ ':root .has' . $palette_key . '-background-color' ] = array(
				'background-color' => 'var(' . $variable_prefix . $key . ')',
			);

			$palette_style[ ':root .wp-block-button .has' . $palette_key . '-color' ] = array(
				'color' => 'var(' . $variable_prefix . $key . ')',
			);

			$palette_style[ ':root .wp-block-button .has' . $palette_key . '-background-color' ] = array(
				'background-color' => 'var(' . $variable_prefix . $key . ')',
			);

			$palette_css_vars[ $variable_prefix . $key ] = $color;
		}
	}

	$palette_style[':root'] = $palette_css_vars;
	$dynamic_css           .= astra_parse_css( $palette_style );

	return $dynamic_css;
}
