<?php
/**
 * Astra Icons - Dynamic CSS.
 *
 * @package astra
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_icons_static_css' );

/**
 * Astra Icons - Dynamic CSS.
 *
 * @param string $dynamic_css Dynamic CSS.
 * @since 3.5.0
 */
function astra_icons_static_css( $dynamic_css ) {

	if ( false === Astra_Icons::is_svg_icons() ) {
		$astra_icons         = '
        .astra-icon-down_arrow::after {
            content: "\e900";
            font-family: Astra;
        }
        .astra-icon-close::after {
            content: "\e5cd";
            font-family: Astra;
        }
        .astra-icon-drag_handle::after {
            content: "\e25d";
            font-family: Astra;
        }
        .astra-icon-format_align_justify::after {
            content: "\e235";
            font-family: Astra;
        }
        .astra-icon-menu::after {
            content: "\e5d2";
            font-family: Astra;
        }
        .astra-icon-reorder::after {
            content: "\e8fe";
            font-family: Astra;
        }
        .astra-icon-search::after {
            content: "\e8b6";
            font-family: Astra;
        }
        .astra-icon-zoom_in::after {
            content: "\e56b";
            font-family: Astra;
        }
        .astra-icon-check-circle::after {
            content: "\e901";
            font-family: Astra;
        }
        .astra-icon-shopping-cart::after {
            content: "\f07a";
            font-family: Astra;
        }
        .astra-icon-shopping-bag::after {
            content: "\f290";
            font-family: Astra;
        }
        .astra-icon-shopping-basket::after {
            content: "\f291";
            font-family: Astra;
        }
        .astra-icon-circle-o::after {
            content: "\e903";
            font-family: Astra;
        }
        .astra-icon-certificate::after {
            content: "\e902";
            font-family: Astra;
        }';
		return $dynamic_css .= Astra_Enqueue_Scripts::trim_css( $astra_icons );
	}
	return $dynamic_css;
}
