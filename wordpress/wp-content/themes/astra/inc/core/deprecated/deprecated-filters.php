<?php
/**
 * Deprecated Filters of Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.23
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Deprecating astra_color_palletes filter.
add_filter( 'astra_color_palettes', 'astra_deprecated_color_palette', 10, 1 );

/**
 * Astra Color Palettes
 *
 * @since 1.0.23
 * @param array $color_palette  customizer color palettes.
 * @return array  $color_palette updated customizer color palettes.
 */
function astra_deprecated_color_palette( $color_palette ) {

	$color_palette = astra_apply_filters_deprecated( 'astra_color_palletes', array( $color_palette ), '1.0.22', 'astra_color_palettes', '' );

	return $color_palette;
}


// Deprecating astra_sigle_post_navigation_enabled filter.
add_filter( 'astra_single_post_navigation_enabled', 'astra_deprecated_sigle_post_navigation_enabled', 10, 1 );

/**
 * Astra Single Post Navigation
 *
 * @since 1.0.27
 * @param boolean $post_nav true | false.
 * @return boolean $post_nav true for enabled | false for disable.
 */
function astra_deprecated_sigle_post_navigation_enabled( $post_nav ) {

	$post_nav = astra_apply_filters_deprecated( 'astra_sigle_post_navigation_enabled', array( $post_nav ), '1.0.27', 'astra_single_post_navigation_enabled', '' );

	return $post_nav;
}

// Deprecating astra_primary_header_main_rt_section filter.
add_filter( 'astra_header_section_elements', 'astra_deprecated_primary_header_main_rt_section', 10, 2 );

/**
 * Astra Header elements.
 *
 * @since 1.2.2
 * @param array  $elements List of elements.
 * @param string $header Header section type.
 * @return array
 */
function astra_deprecated_primary_header_main_rt_section( $elements, $header ) {

	$elements = astra_apply_filters_deprecated( 'astra_primary_header_main_rt_section', array( $elements, $header ), '1.2.2', 'astra_header_section_elements', '' );

	return $elements;
}

if ( ! function_exists( 'astra_apply_filters_deprecated' ) ) {
	/**
	 * Astra Filter Deprecated
	 *
	 * @since 1.1.1
	 * @param string $tag         The name of the filter hook.
	 * @param array  $args        Array of additional function arguments to be passed to apply_filters().
	 * @param string $version     The version of WordPress that deprecated the hook.
	 * @param string $replacement Optional. The hook that should have been used. Default false.
	 * @param string $message     Optional. A message regarding the change. Default null.
	 */
	function astra_apply_filters_deprecated( $tag, $args, $version, $replacement = false, $message = null ) {
		if ( function_exists( 'apply_filters_deprecated' ) ) { /* WP >= 4.6 */
			return apply_filters_deprecated( $tag, $args, $version, $replacement, $message );
		} else {
			return apply_filters_ref_array( $tag, $args ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound
		}
	}
}

// Deprecating ast_footer_bar_display filter.
add_filter( 'astra_footer_bar_display', 'astra_deprecated_ast_footer_bar_display_filter', 10, 1 );

/**
 * Display footer builder markup.
 *
 * @since 3.7.4
 * @param boolean $display_footer true | false.
 * @return boolean true for enabled | false for disable.
 */
function astra_deprecated_ast_footer_bar_display_filter( $display_footer ) {
	return astra_apply_filters_deprecated( 'ast_footer_bar_display', array( $display_footer ), '3.7.4', 'astra_footer_bar_display', '' );
}

// Deprecating ast_main_header_display filter.
add_filter( 'astra_main_header_display', 'astra_deprecated_ast_main_header_display_filter', 10, 1 );

/**
 * Display header builder markup.
 *
 * @since 3.7.4
 * @param boolean $display_header true | false.
 * @return boolean true for enabled | false for disable.
 */
function astra_deprecated_ast_main_header_display_filter( $display_header ) {

	return astra_apply_filters_deprecated( 'ast_main_header_display', array( $display_header ), '3.7.4', 'astra_main_header_display', '' );
}

// Deprecating secondary_submenu_border_class filter.
add_filter( 'astra_secondary_submenu_border_class', 'astra_deprecated_secondary_submenu_border_class_filter', 10, 1 );

/**
 * Border class to secondary submenu
 *
 * @since 3.7.4
 * @param string $class_selector custom class assigned to secondary submenu.
 * @return string  $class_selector updated class selector.
 */
function astra_deprecated_secondary_submenu_border_class_filter( $class_selector ) {

	$class_selector = astra_apply_filters_deprecated( 'secondary_submenu_border_class', array( $class_selector ), '3.7.4', 'astra_secondary_submenu_border_class', '' );

	return $class_selector;
}

// Deprecating gtn_image_group_css_comp filter.
add_filter( 'astra_gutenberg_image_group_style_support', 'astra_deprecated_gtn_image_group_css_comp_filter', 10, 1 );

/**
 * Image, group compatibility support released in v2.4.4.
 *
 * @since 3.7.4
 * @param boolean $block_support true | false.
 * @return boolean true for enabled | false for disable.
 */
function astra_deprecated_gtn_image_group_css_comp_filter( $block_support ) {

	return astra_apply_filters_deprecated( 'gtn_image_group_css_comp', array( $block_support ), '3.7.4', 'astra_gutenberg_image_group_style_support', '' );
}

// Deprecating ast_footer_sml_layout filter.
add_filter( 'astra_footer_sml_layout', 'astra_deprecated_ast_footer_sml_layout_filter', 10, 1 );

/**
 * Footer bar meta setting option.
 *
 * @since 3.7.4
 * @param boolean $display_footer_bar true | false.
 * @return boolean true for enabled | false for disable.
 */
function astra_deprecated_ast_footer_sml_layout_filter( $display_footer_bar ) {

	return astra_apply_filters_deprecated( 'ast_footer_sml_layout', array( $display_footer_bar ), '3.7.4', 'astra_footer_sml_layout', '' );
}

// Deprecating primary_submenu_border_class filter.
add_filter( 'astra_primary_submenu_border_class', 'astra_deprecated_primary_submenu_border_class_filter', 10, 1 );

/**
 * Border class to primary submenu
 *
 * @since 3.7.4
 * @param string $class_selector custom class assigned to primary submenu.
 * @return string  $class_selector updated class selector.
 */
function astra_deprecated_primary_submenu_border_class_filter( $class_selector ) {

	$class_selector = astra_apply_filters_deprecated( 'primary_submenu_border_class', array( $class_selector ), '3.7.4', 'astra_primary_submenu_border_class', '' );

	return $class_selector;
}

// Deprecating astra_single_banner_post_meta filter.
add_filter( 'astra_single_post_meta', 'astra_deprecated_astra_single_banner_post_meta_filter', 10, 1 );

/**
 * Single meta markup filter.
 *
 * @since 4.0.2
 * @param string $meta_markup Markup of meta.
 * @return string  $meta_markup Markup of meta.
 */
function astra_deprecated_astra_single_banner_post_meta_filter( $meta_markup ) {
	$meta_markup = astra_apply_filters_deprecated( 'astra_single_banner_post_meta', array( $meta_markup ), '4.0.2', 'astra_single_post_meta', '' );
	return $meta_markup;
}

// Deprecating astra_get_option_dynamic-blog-layouts filter.
add_filter( 'astra_get_option_dynamic_blog_layouts', 'astra_deprecated_astra_get_option_dynamic_blog_layouts_filter', 10, 1 );

/**
 * Don't apply direct new layouts to legacy users.
 *
 * @since 4.1.0
 * @param string $dynamic_blog_layout false if it is an existing user , true if not.
 * @return boolean  $dynamic_blog_layout false if it is an existing user , true if not.
 */
function astra_deprecated_astra_get_option_dynamic_blog_layouts_filter( $dynamic_blog_layout ) {
	$dynamic_blog_layout = astra_apply_filters_deprecated( 'astra_get_option_dynamic-blog-layouts', array( $dynamic_blog_layout ), '4.1.0', 'astra_get_option_dynamic_blog_layouts', '' );
	return $dynamic_blog_layout;
}
