<?php
/**
 * Transparent Header - Dynamic CSS
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Transparent Above Header
 */
add_filter( 'astra_dynamic_theme_css', 'astra_ext_transparent_above_header_sections_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return String Generated dynamic CSS for above header transparent header.
 */
function astra_ext_transparent_above_header_sections_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	$above_header_layout = astra_get_option( 'above-header-layout', 'disabled' );

	if ( 'disabled' === $above_header_layout ) {
		return $dynamic_css;
	}

	if ( false == Astra_Ext_Transparent_Header_Markup::is_transparent_header() ) {
		return $dynamic_css;
	}

	/**
	 * Set colors
	 */

	$transparent_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'desktop' );
	$transparent_bg_color_tablet  = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'tablet' );
	$transparent_bg_color_mobile  = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'mobile' );

	$transparent_color_site_title_desktop = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'desktop' );
	$transparent_color_site_title_tablet  = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'tablet' );
	$transparent_color_site_title_mobile  = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'mobile' );

	$transparent_color_h_site_title_desktop = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'desktop' );
	$transparent_color_h_site_title_tablet  = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'tablet' );
	$transparent_color_h_site_title_mobile  = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'mobile' );

	$transparent_menu_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'desktop' );
	$transparent_menu_color_desktop    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'desktop' );
	$transparent_menu_h_color_desktop  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'desktop' );

	$transparent_menu_bg_color_tablet = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'tablet' );
	$transparent_menu_color_tablet    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'tablet' );
	$transparent_menu_h_color_tablet  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'tablet' );

	$transparent_menu_bg_color_mobile = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'mobile' );
	$transparent_menu_color_mobile    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'mobile' );
	$transparent_menu_h_color_mobile  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'mobile' );

	$transparent_sub_menu_color_desktop    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'desktop' );
	$transparent_sub_menu_h_color_desktop  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'desktop' );
	$transparent_sub_menu_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'desktop' );

	$transparent_sub_menu_color_tablet    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'tablet' );
	$transparent_sub_menu_h_color_tablet  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'tablet' );
	$transparent_sub_menu_bg_color_tablet = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'tablet' );

	$transparent_sub_menu_color_mobile    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'mobile' );
	$transparent_sub_menu_h_color_mobile  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'mobile' );
	$transparent_sub_menu_bg_color_mobile = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'mobile' );

	$transparent_content_section_text_color_desktop   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'desktop' );
	$transparent_content_section_link_color_desktop   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'desktop' );
	$transparent_content_section_link_h_color_desktop = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'desktop' );

	$transparent_content_section_text_color_tablet   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'tablet' );
	$transparent_content_section_link_color_tablet   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'tablet' );
	$transparent_content_section_link_h_color_tablet = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'tablet' );

	$transparent_content_section_text_color_mobile   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'mobile' );
	$transparent_content_section_link_color_mobile   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'mobile' );
	$transparent_content_section_link_h_color_mobile = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'mobile' );

	/**
	 * Generate Dynamic CSS
	 */

	$css = '';
	/**
	 * Transparent Header Colors
	 */
	$transparent_header_desktop = array(
		'.ast-theme-transparent-header .ast-above-header-menu, .ast-theme-transparent-header.ast-header-break-point .ast-above-header-section-separated .ast-above-header-navigation ul.ast-above-header-menu' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-item > .menu-link,.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-ancestor > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item:hover > .menu-link'     => array(
			'color' => esc_attr( $transparent_menu_h_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation > ul.ast-above-header-menu > .menu-item-has-children:not(.current-menu-item) > .ast-menu-toggle'                => array(
			'color' => esc_attr( $transparent_menu_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu' => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .menu-item,.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu, .ast-theme-transparent-header .ast-above-header-navigation .ast-above-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_desktop ),
		),

		// Content Section text color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select, .ast-theme-transparent-header .ast-above-header-section .widget, .ast-theme-transparent-header .ast-above-header-section .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_desktop ),
		),
		// Content Section link color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a, .ast-theme-transparent-header .ast-above-header-section .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_desktop ),
		),
		// Content Section link hover color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a:hover, .ast-theme-transparent-header .ast-above-header-section .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_desktop ),
		),

	);

	$transparent_header_tablet = array(
		'.ast-theme-transparent-header .ast-above-header-menu, .ast-theme-transparent-header.ast-header-break-point .ast-above-header-section-separated .ast-above-header-navigation ul.ast-above-header-menu, .ast-theme-transparent-header.ast-header-break-point .ast-below-header-section-separated .ast-below-header-actual-nav' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-item > .menu-link,.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-ancestor > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item:hover > .menu-link'     => array(
			'color' => esc_attr( $transparent_menu_h_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation > ul.ast-above-header-menu > .menu-item-has-children:not(.current-menu-item) > .ast-menu-toggle'                => array(
			'color' => esc_attr( $transparent_menu_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu' => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .menu-item,.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu, .ast-theme-transparent-header .ast-above-header-navigation .ast-above-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_tablet ),
		),

		// Content Section text color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select, .ast-theme-transparent-header .ast-above-header-section .widget, .ast-theme-transparent-header .ast-above-header-section .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_tablet ),
		),
		// Content Section link color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a, .ast-theme-transparent-header .ast-above-header-section .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_tablet ),
		),
		// Content Section link hover color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a:hover, .ast-theme-transparent-header .ast-above-header-section .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_tablet ),
		),
	);

	$transparent_header_mobile = array(
		'.ast-theme-transparent-header .ast-above-header-menu, .ast-theme-transparent-header.ast-header-break-point .ast-above-header-section-separated .ast-above-header-navigation ul.ast-above-header-menu, .ast-theme-transparent-header.ast-header-break-point .ast-below-header-section-separated .ast-below-header-actual-nav' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-item > .menu-link,.ast-theme-transparent-header .ast-above-header-navigation .menu-item.current-menu-ancestor > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header-navigation .menu-item:hover > .menu-link'     => array(
			'color' => esc_attr( $transparent_menu_h_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation a, .ast-header-break-point.ast-theme-transparent-header .ast-above-header-navigation > ul.ast-above-header-menu > .menu-item-has-children:not(.current-menu-item) > .ast-menu-toggle'                => array(
			'color' => esc_attr( $transparent_menu_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu' => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .menu-item,.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-above-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-above-header-menu .sub-menu, .ast-theme-transparent-header .ast-above-header-navigation .ast-above-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_mobile ),
		),

		// Content Section text color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select, .ast-theme-transparent-header .ast-above-header-section .widget, .ast-theme-transparent-header .ast-above-header-section .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_mobile ),
		),
		// Content Section link color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a, .ast-theme-transparent-header .ast-above-header-section .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_mobile ),
		),
		// Content Section link hover color.
		'.ast-theme-transparent-header .ast-above-header-section .user-select a:hover, .ast-theme-transparent-header .ast-above-header-section .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_mobile ),
		),
	);

	/* Parse CSS from array() */
	$css .= astra_parse_css( $transparent_header_desktop );
	$css .= astra_parse_css( $transparent_header_tablet, '', astra_get_tablet_breakpoint() );
	$css .= astra_parse_css( $transparent_header_mobile, '', astra_get_mobile_breakpoint() );

	return $dynamic_css . $css;

}



/**
 * Transparent Below Header
 */
add_filter( 'astra_dynamic_theme_css', 'astra_ext_transparent_below_header_sections_dynamic_css' );

/**
 * Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return String Generated dynamic CSS.
 */
function astra_ext_transparent_below_header_sections_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

	// set page width depending on site layout.
	$below_header_layout = astra_get_option( 'below-header-layout', 'disabled' );

	if ( 'disabled' === $below_header_layout ) {
		return $dynamic_css;
	}

	if ( false == Astra_Ext_Transparent_Header_Markup::is_transparent_header() ) {
		return $dynamic_css;
	}

	/**
	 * Set colors
	 */

	$transparent_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'desktop' );
	$transparent_bg_color_tablet  = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'tablet' );
	$transparent_bg_color_mobile  = astra_get_prop( astra_get_option( 'transparent-header-bg-color-responsive' ), 'mobile' );

	$transparent_color_site_title_desktop = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'desktop' );
	$transparent_color_site_title_tablet  = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'tablet' );
	$transparent_color_site_title_mobile  = astra_get_prop( astra_get_option( 'transparent-header-color-site-title-responsive' ), 'mobile' );

	$transparent_color_h_site_title_desktop = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'desktop' );
	$transparent_color_h_site_title_tablet  = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'tablet' );
	$transparent_color_h_site_title_mobile  = astra_get_prop( astra_get_option( 'transparent-header-color-h-site-title-responsive' ), 'mobile' );

	$transparent_menu_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'desktop' );
	$transparent_menu_color_desktop    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'desktop' );
	$transparent_menu_h_color_desktop  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'desktop' );

	$transparent_menu_bg_color_tablet = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'tablet' );
	$transparent_menu_color_tablet    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'tablet' );
	$transparent_menu_h_color_tablet  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'tablet' );

	$transparent_menu_bg_color_mobile = astra_get_prop( astra_get_option( 'transparent-menu-bg-color-responsive' ), 'mobile' );
	$transparent_menu_color_mobile    = astra_get_prop( astra_get_option( 'transparent-menu-color-responsive' ), 'mobile' );
	$transparent_menu_h_color_mobile  = astra_get_prop( astra_get_option( 'transparent-menu-h-color-responsive' ), 'mobile' );

	$transparent_sub_menu_color_desktop    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'desktop' );
	$transparent_sub_menu_h_color_desktop  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'desktop' );
	$transparent_sub_menu_bg_color_desktop = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'desktop' );

	$transparent_sub_menu_color_tablet    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'tablet' );
	$transparent_sub_menu_h_color_tablet  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'tablet' );
	$transparent_sub_menu_bg_color_tablet = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'tablet' );

	$transparent_sub_menu_color_mobile    = astra_get_prop( astra_get_option( 'transparent-submenu-color-responsive' ), 'mobile' );
	$transparent_sub_menu_h_color_mobile  = astra_get_prop( astra_get_option( 'transparent-submenu-h-color-responsive' ), 'mobile' );
	$transparent_sub_menu_bg_color_mobile = astra_get_prop( astra_get_option( 'transparent-submenu-bg-color-responsive' ), 'mobile' );

	$transparent_content_section_text_color_desktop   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'desktop' );
	$transparent_content_section_link_color_desktop   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'desktop' );
	$transparent_content_section_link_h_color_desktop = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'desktop' );

	$transparent_content_section_text_color_tablet   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'tablet' );
	$transparent_content_section_link_color_tablet   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'tablet' );
	$transparent_content_section_link_h_color_tablet = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'tablet' );

	$transparent_content_section_text_color_mobile   = astra_get_prop( astra_get_option( 'transparent-content-section-text-color-responsive' ), 'mobile' );
	$transparent_content_section_link_color_mobile   = astra_get_prop( astra_get_option( 'transparent-content-section-link-color-responsive' ), 'mobile' );
	$transparent_content_section_link_h_color_mobile = astra_get_prop( astra_get_option( 'transparent-content-section-link-h-color-responsive' ), 'mobile' );

	/**
	 * Generate Dynamic CSS
	 */

	$css = '';
	/**
	 * Transparent Header Colors
	 */
	$transparent_header_desktop = array(
		'.ast-theme-transparent-header.ast-no-toggle-below-menu-enable.ast-header-break-point .ast-below-header-navigation-wrap, .ast-theme-transparent-header .ast-below-header-actual-nav, .ast-theme-transparent-header.ast-header-break-point .ast-below-header-actual-nav' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-below-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_desktop ),
		),
		/**
		 * Below Header Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu, .ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu' => array(
			'color' => esc_attr( $transparent_menu_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_menu_h_color_desktop ),
		),

		/**
		 * Below Header Dropdown Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.focus > .menu-item' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu'               => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_desktop ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu, .ast-theme-transparent-header .ast-below-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_desktop ),
		),

		/**
		 * Content Colors & Typography
		 */
		'.ast-theme-transparent-header .below-header-user-select, .ast-theme-transparent-header .below-header-user-select .widget,.ast-theme-transparent-header .below-header-user-select .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_desktop ),
		),

		'.ast-theme-transparent-header .below-header-user-select a, .ast-theme-transparent-header .below-header-user-select .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_desktop ),
		),

		'.ast-theme-transparent-header .below-header-user-select a:hover, .ast-theme-transparent-header .below-header-user-select .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_desktop ),
		),
	);

	$transparent_header_tablet = array(

		'.ast-theme-transparent-header.ast-no-toggle-below-menu-enable.ast-header-break-point .ast-below-header-navigation-wrap, .ast-theme-transparent-header .ast-below-header-actual-nav, .ast-theme-transparent-header.ast-header-break-point .ast-below-header-actual-nav' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-below-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_tablet ),
		),
		/**
		 * Below Header Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu, .ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu' => array(
			'color' => esc_attr( $transparent_menu_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_menu_h_color_tablet ),
		),

		/**
		 * Below Header Dropdown Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.focus > .menu-item' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu'               => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_tablet ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu, .ast-theme-transparent-header .ast-below-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_tablet ),
		),

		/**
		 * Content Colors & Typography
		 */
		'.ast-theme-transparent-header .below-header-user-select, .ast-theme-transparent-header .below-header-user-select .widget,.ast-theme-transparent-header .below-header-user-select .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_tablet ),
		),

		'.ast-theme-transparent-header .below-header-user-select a, .ast-theme-transparent-header .below-header-user-select .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_tablet ),
		),

		'.ast-theme-transparent-header .below-header-user-select a:hover, .ast-theme-transparent-header .below-header-user-select .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_tablet ),
		),
	);

	$transparent_header_mobile = array(

		'.ast-theme-transparent-header.ast-no-toggle-below-menu-enable.ast-header-break-point .ast-below-header-navigation-wrap, .ast-theme-transparent-header .ast-below-header-actual-nav, .ast-theme-transparent-header.ast-header-break-point .ast-below-header-actual-nav' => array(
			'background-color' => esc_attr( $transparent_menu_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-below-header .ast-search-menu-icon form' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),
		'.ast-theme-transparent-header .ast-below-header .slide-search .search-field:focus' => array(
			'background-color' => esc_attr( $transparent_bg_color_mobile ),
		),
		/**
		 * Below Header Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu, .ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu a, .ast-header-break-point.ast-theme-transparent-header .ast-below-header-menu' => array(
			'color' => esc_attr( $transparent_menu_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_menu_h_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-ancestor > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .menu-item.current-menu-item > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .ast-menu-toggle, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .ast-menu-toggle' => array(
			'color' => esc_attr( $transparent_menu_h_color_mobile ),
		),

		/**
		 * Below Header Dropdown Navigation
		 */

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:hover > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item:focus > .menu-item, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.focus > .menu-item' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-ancestor.focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:hover > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item:focus > .menu-link, .ast-theme-transparent-header .ast-below-header-menu .sub-menu .menu-item.current-menu-item.focus > .menu-link' => array(
			'color' => esc_attr( $transparent_sub_menu_h_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu'               => array(
			'background-color' => esc_attr( $transparent_sub_menu_bg_color_mobile ),
		),

		'.ast-theme-transparent-header .ast-below-header-menu .sub-menu, .ast-theme-transparent-header .ast-below-header-menu .sub-menu a' => array(
			'color' => esc_attr( $transparent_sub_menu_color_mobile ),
		),

		/**
		 * Content Colors & Typography
		 */
		'.ast-theme-transparent-header .below-header-user-select, .ast-theme-transparent-header .below-header-user-select .widget,.ast-theme-transparent-header .below-header-user-select .widget-title' => array(
			'color' => esc_attr( $transparent_content_section_text_color_mobile ),
		),

		'.ast-theme-transparent-header .below-header-user-select a, .ast-theme-transparent-header .below-header-user-select .widget a' => array(
			'color' => esc_attr( $transparent_content_section_link_color_mobile ),
		),

		'.ast-theme-transparent-header .below-header-user-select a:hover, .ast-theme-transparent-header .below-header-user-select .widget a:hover' => array(
			'color' => esc_attr( $transparent_content_section_link_h_color_mobile ),
		),
	);

	/* Parse CSS from array() */
	$css .= astra_parse_css( $transparent_header_desktop );
	$css .= astra_parse_css( $transparent_header_tablet, '', astra_get_tablet_breakpoint() );
	$css .= astra_parse_css( $transparent_header_mobile, '', astra_get_mobile_breakpoint() );

	return $dynamic_css . $css;
}
