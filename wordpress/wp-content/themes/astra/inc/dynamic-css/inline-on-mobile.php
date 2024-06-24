<?php
/**
 * Inline On Mobile - Dynamic CSS.
 *
 * @package astra
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_inline_on_mobile_css' );

/**
 * Inline On Mobile - Dynamic CSS.
 *
 * @param string $dynamic_css Dynamic CSS.
 * @since 3.5.0
 * @return string
 */
function astra_inline_on_mobile_css( $dynamic_css ) {

	$inline_on_mobile_enable = false;
	for ( $index = 1; $index <= Astra_Builder_Helper::$component_limit; $index++ ) {
		if ( false === astra_get_option( 'header-menu' . $index . '-menu-stack-on-mobile' ) ) {
			$inline_on_mobile_enable = true;
			break;
		}
	}

	if ( false === $inline_on_mobile_enable ) {
		return $dynamic_css;
	}

	$inline_on_mobile_css = '
    .ast-header-break-point .ast-mobile-header-wrap .ast-above-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item .menu-link,
    .ast-header-break-point .ast-mobile-header-wrap .ast-main-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item .menu-link,
    .ast-header-break-point .ast-mobile-header-wrap .ast-below-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item .menu-link {
      border: none;
    }
    
    .ast-header-break-point .ast-mobile-header-wrap .ast-above-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item-has-children > .ast-menu-toggle::before,
    .ast-header-break-point .ast-mobile-header-wrap .ast-main-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item-has-children > .ast-menu-toggle::before,
    .ast-header-break-point .ast-mobile-header-wrap .ast-below-header-wrap .main-header-bar-navigation .inline-on-mobile .menu-item-has-children > .ast-menu-toggle::before {
      font-size: .6rem;
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile {
        flex-wrap: unset;
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu .menu-link {
        padding: .1em 1em;
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item .ast-menu-toggle::before {
        transform: rotate(-90deg);
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item.ast-submenu-expanded .ast-menu-toggle::before {
        transform: rotate(-270deg);
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item > .sub-menu > .menu-item .menu-link:before {
        content: none;
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile {
        flex-wrap: unset;
    }
    
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu .menu-link {
        padding: .1em 1em;
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item .ast-menu-toggle::before {
        transform: rotate(-90deg);
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item.ast-submenu-expanded .ast-menu-toggle::before {
        transform: rotate(-270deg);
    }
    .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item > .sub-menu > .menu-item .menu-link:before {
        content: none;
    }
    .ast-header-break-point .inline-on-mobile .sub-menu {
        width: 150px;
    }';

	if ( is_rtl() ) {
		$inline_on_mobile_css .= '
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.menu-item-has-children {
            margin-left: 10px;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu {
            display: block;
            position: absolute;
            left: auto;
            right: 0;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu .menu-item .ast-menu-toggle {
            padding: 0;
            left: 1em;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item > .sub-menu {
            right: 100%;
            left: auto;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .ast-menu-toggle {
            left: -15px;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.menu-item-has-children {
            margin-left: 10px;
        }
        
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu {
            display: block;
            position: absolute;
            left: auto;
            right: 0;
        }
        
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item > .sub-menu {
            right: 100%;
            left: auto;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .ast-menu-toggle {
            left: -15px;
        }';
	} else {
		$inline_on_mobile_css .= '
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.menu-item-has-children {
            margin-right: 10px;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu {
            display: block;
            position: absolute;
            right: auto;
            left: 0;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu .menu-item .ast-menu-toggle {
            padding: 0;
            right: 1em;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item > .sub-menu {
            left: 100%;
            right: auto;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .ast-menu-toggle {
            right: -15px;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.menu-item-has-children {
            margin-right: 10px;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu {
            display: block;
            position: absolute;
            right: auto;
            left: 0;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .menu-item.ast-submenu-expanded > .sub-menu > .menu-item > .sub-menu {
            left: 100%;
            right: auto;
        }
        .ast-header-break-point .ast-mobile-header-wrap .ast-flex.inline-on-mobile .ast-menu-toggle {
            right: -15px;
        }';
	}

	return $dynamic_css .= Astra_Enqueue_Scripts::trim_css( $inline_on_mobile_css );
}
