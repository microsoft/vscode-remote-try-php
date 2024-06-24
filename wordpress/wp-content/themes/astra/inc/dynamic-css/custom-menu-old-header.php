<?php
/**
 * Old Header Menu Last Item - Dynamic CSS
 *
 * @package astra
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_old_header_custom_menu_css' );

/**
 * Old Header Menu Last Item - Dynamic CSS.
 *
 * @param string $dynamic_css 
 * @since 3.5.0
 */
function astra_old_header_custom_menu_css( $dynamic_css ) {

	$menu_item = astra_get_option( 'header-main-rt-section' );
	if ( false === Astra_Builder_Helper::$is_header_footer_builder_active ) {
		$static_css = '';
		if ( 'widget' == $menu_item ) {

			$static_css .= '
            .ast-header-widget-area {
                line-height: 1.65;
            }
            .ast-header-widget-area .widget-title,
            .ast-header-widget-area .no-widget-text {
                margin-bottom: 0;
            }
            .ast-header-widget-area .widget {
                margin: .5em;
                display: inline-block;
                vertical-align: middle;
            }
            .ast-header-widget-area .widget p {
                margin-bottom: 0;
            }
            .ast-header-widget-area .widget ul {
                position: static;
                border: 0;
                width: auto;
            }
            .ast-header-widget-area .widget ul a {
                border: 0;
            }
            
            .ast-header-widget-area .widget.widget_search .search-field,
            .ast-header-widget-area .widget.widget_search .search-field:focus {
                padding: 10px 45px 10px 15px;
            }
            .ast-header-widget-area .widget:last-child {
                margin-bottom: 0.5em;
                margin-right: 0;
            }
            .submenu-with-border .ast-header-widget-area .widget ul {
                position: static;
                border: 0;
                width: auto;
            }
            .submenu-with-border .ast-header-widget-area .widget ul a {
                border: 0;
            }
            .ast-header-break-point .ast-header-widget-area .widget {
                margin: .5em 0;
                display: block;
            }';
		}
		if ( 'button' == $menu_item ) {
			$static_css .= '
            .ast-header-break-point .main-navigation ul .button-custom-menu-item .menu-link {
                padding: 0 20px;
                display: inline-block;
                width: 100%;
                border-bottom-width: 1px;
                border-style: solid;
                border-color: #eaeaea;
            }
            .button-custom-menu-item .ast-custom-button-link .ast-custom-button {
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
            }
            .button-custom-menu-item .ast-custom-button-link .ast-custom-button:hover {
                transition: all 0.1s ease-in-out;
            }';

		}  

		$search_box_type = astra_get_option( 'header-main-rt-section-search-box-type' );
		$show_icon       = 'full-screen' === $search_box_type || 'header-cover' === $search_box_type ? 'block' : 'none';
		$static_css     .= "
        .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .main-header-bar-navigation .ast-search-icon {
            display: $show_icon;
        }
        .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon .search-form {
            padding: 0;
            display: block;
            overflow: hidden;
        }
        .ast-header-break-point .ast-header-custom-item .widget:last-child {
            margin-bottom: 1em;
        }
        .ast-header-custom-item .widget {
            margin: 0.5em;
            display: inline-block;
            vertical-align: middle;
        }
        .ast-header-custom-item .widget p {
            margin-bottom: 0;
        }
        .ast-header-custom-item .widget li {
            width: auto;
        }
        .ast-header-custom-item-inside .button-custom-menu-item .menu-link {
            display: none;
        }
        
        .ast-header-custom-item-inside.ast-header-break-point .button-custom-menu-item .ast-custom-button-link {
            display: none;
        }
        .ast-header-custom-item-inside.ast-header-break-point .button-custom-menu-item .menu-link {
            display: block;
        }";
		if ( is_rtl() ) {
			$static_css .= '
            .ast-header-break-point.ast-header-custom-item-outside .main-header-bar .ast-search-icon {
                margin-left: 1em;
            }
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon .search-field,
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon.ast-inline-search .search-field {
                width: 100%;
                padding-left: 5.5em;
            }
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon .search-submit {
                display: block;
                position: absolute;
                height: 100%;
                top: 0;
                left: 0;
                padding: 0 1em;
                border-radius: 0;
            }
            .ast-header-break-point .ast-header-custom-item .ast-masthead-custom-menu-items {
                padding-right: 20px;
                padding-left: 20px;
                margin-bottom: 1em;
                margin-top: 1em;
            }
            .ast-header-custom-item-inside.ast-header-break-point .button-custom-menu-item {
                padding-right: 0;
                padding-left: 0;
                margin-top: 0;
                margin-bottom: 0;
            }';
		} else {
			$static_css .= '
            .ast-header-break-point.ast-header-custom-item-outside .main-header-bar .ast-search-icon {
                margin-right: 1em;
            }
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon .search-field,
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon.ast-inline-search .search-field {
                width: 100%;
                padding-right: 5.5em;
            }
            .ast-header-break-point.ast-header-custom-item-inside .main-header-bar .ast-search-menu-icon .search-submit {
                display: block;
                position: absolute;
                height: 100%;
                top: 0;
                right: 0;
                padding: 0 1em;
                border-radius: 0;
            }
            .ast-header-break-point .ast-header-custom-item .ast-masthead-custom-menu-items {
                padding-left: 20px;
                padding-right: 20px;
                margin-bottom: 1em;
                margin-top: 1em;
            }
            .ast-header-custom-item-inside.ast-header-break-point .button-custom-menu-item {
                padding-left: 0;
                padding-right: 0;
                margin-top: 0;
                margin-bottom: 0;
            }';
		}
		$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $static_css );
	}
	return $dynamic_css;
}
