<?php
/**
 * Post Navigation - Dynamic CSS
 *
 * @package Astra
 * @since 4.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_navigation_css', 11 );

/**
 * Post Navigation - Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @return String Generated dynamic CSS for Post Navigation.
 *
 * @since 4.6.0
 */
function astra_navigation_css( $dynamic_css ) {
	$mobile_breakpoint = strval( astra_get_mobile_breakpoint() );
	$link_hover_color  = astra_get_option( 'link-h-color' );

	/** Backward compatibility support for left-right paddings @since 4.6.13 */
	$remove_mobile_device_paddings = apply_filters( 'astra_remove_single_posts_navigation_mobile_device_padding', ! empty( astra_get_option( 'remove_single_posts_navigation_mobile_device_padding' ) ) );
	$mobile_device_paddings        = ! $remove_mobile_device_paddings ? 'padding-left: 20px; padding-right: 20px;' : '';

	$navigation_css = '
		.single .post-navigation a p {
			margin-top: 0.5em;
			margin-bottom: 0;
			text-transform: initial;
			line-height: 1.65em;
			font-weight: normal;
		}
		.single .post-navigation a .ast-post-nav {
			font-weight: 600;
			display: block;
			text-transform: uppercase;
			font-size: 0.85em;
			letter-spacing: 0.05em;
		}
		.single .post-navigation a svg {
			top: .125em;
			width: 1em;
			height: 1em;
			position: relative;
			fill: currentColor;
		}
		.page-links .page-link:hover, .single .post-navigation a:hover {
			color: ' . esc_attr( $link_hover_color ) . ';
		}
		@media( min-width: 320px ) {
			.single .post-navigation .nav-previous a {
				text-align: left;
				padding-right: 20px;
			}
			.single .post-navigation .nav-next a {
				text-align: right;
				padding-left: 20px;
			}
			.comment-navigation .nav-previous:after, .post-navigation .nav-previous:after {
				position: absolute;
				content: "";
				top: 25%;
				right: 0;
				width: 1px;
				height: 50%;
				background: var(--ast-single-post-border, var(--ast-border-color));
			}
		}
		@media( max-width: ' . $mobile_breakpoint . 'px ) {
			.single .post-navigation .nav-links {
				-js-display: inline-flex;
				display: inline-flex;
				width: 100%;
				' . $mobile_device_paddings . '
			}
			.single .post-navigation a p {
				display: none;
			}
			.single .post-navigation .nav-previous {
				margin-bottom: 0;
			}
		}
		@media( min-width: 421px ) {
			.single .post-navigation a {
				max-width: 80%;
				width: 100%;
			}
			.post-navigation a {
				font-weight: 500;
				font-size: 16px;
			}
		}
	';

	// rtl css for post navigation.
	if ( is_rtl() ) {
		$navigation_css .= '
		@media( min-width: 320px ) {
			.single .post-navigation .nav-previous a {
					text-align: start;
				}
				.single .post-navigation .nav-next a {
					text-align: end;
				}
			}
			@media( max-width: ' . $mobile_breakpoint . 'px ) {
				.single .post-navigation .nav-links {
					padding-left: 0px;
					padding-right: 0px;
				}
			}
		';
	}

	$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $navigation_css );

	return $dynamic_css;
}
