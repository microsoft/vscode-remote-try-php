<?php
/**
 * Single Post UI Improvement - Dynamic CSS
 *
 * @package astra-builder
 * @since 4.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( apply_filters( 'astra_improvise_single_post_design', Astra_Dynamic_CSS::astra_4_6_0_compatibility() && is_single() ) ) {
	add_filter( 'astra_dynamic_theme_css', 'astra_single_post_css', 11 );
}


/**
 * Single Post UI Improvement - Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @return String Generated dynamic CSS for Pagination.
 *
 * @since 4.6.0
 */
function astra_single_post_css( $dynamic_css ) {
	$is_boxed       = astra_is_content_style_boxed();
	$content_layout = astra_get_content_layout();

	$post_with_unboxed_layout = ( 'plain-container' === $content_layout || 'narrow-container' === $content_layout ) && ! $is_boxed ? true : false;

	$static_css = '
		:root {
			--ast-single-post-border: #e1e8ed;
		}
		.entry-content h1, .entry-content h2, .entry-content h3, .entry-content h4, .entry-content h5, .entry-content h6 {
			margin-top: 1.5em;
			margin-bottom: calc(0.3em + 10px);
		}
		code, kbd, samp {
			background: var(--ast-code-block-background);
			padding: 3px 6px;
		}
		.ast-row.comment-textarea fieldset.comment-form-comment {
			border: none;
			padding: unset;
			margin-bottom: 1.5em;
		}
		.entry-content > * {
			margin-bottom: 1.5em;
		}
		.entry-content .wp-block-image,
		.entry-content .wp-block-embed {
			margin-top: 2em;
			margin-bottom: 3em;
		}
	';

	if ( $post_with_unboxed_layout ) {
		$static_css .= '
			:root {
				--ast-single-post-nav-padding: 4em 0 0;
			}
			.ast-single-post .ast-post-format-content {
				max-width: 100%;
			}
			.post-navigation + .comments-area {
				border-top: none;
				padding-top: 5em;
			}
		';
	} else {
		$nav_padding = astra_check_current_post_comment_enabled() || 0 < get_comments_number() ? '3em 0 1em' : '3em 0 0';
		$static_css .= '
			:root {
				--ast-single-post-nav-padding: ' . $nav_padding . ';
			}
		';
	}

	$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $static_css );

	return $dynamic_css;
}
