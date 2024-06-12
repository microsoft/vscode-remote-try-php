<?php
/**
 * Astra WordPress-5.8 compatibility - Dynamic CSS.
 *
 * @package astra
 * @since 3.6.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( astra_block_based_legacy_setup() ) {
	add_filter( 'astra_dynamic_theme_css', 'astra_block_editor_compatibility_css' );
} else {
	add_filter( 'astra_dynamic_theme_css', 'astra_load_modern_block_editor_ui' );
}

/**
 * This is new compatibillity CSS added at time 'improve-gb-editor-ui'. So requiring this for new setup as well that's why making it common.
 *
 * @since 3.6.5
 */
function astra_get_block_editor_required_css() {
	return '
		.wp-block-file {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
			justify-content: space-between;
		}
		.wp-block-pullquote {
			border: none;
		}
		.wp-block-pullquote blockquote::before {
			content: "\201D";
			font-family: "Helvetica",sans-serif;
			display: flex;
			transform: rotate( 180deg );
			font-size: 6rem;
			font-style: normal;
			line-height: 1;
			font-weight: bold;
			align-items: center;
			justify-content: center;
		}
		.has-text-align-right > blockquote::before {
			justify-content: flex-start;
		}
		.has-text-align-left > blockquote::before {
			justify-content: flex-end;
		}
		figure.wp-block-pullquote.is-style-solid-color blockquote {
			max-width: 100%;
			text-align: inherit;
		}';
}

/**
 * Astra WordPress compatibility - Dynamic CSS.
 *
 * @param string $dynamic_css Dynamic CSS.
 * @since 3.6.5
 */
function astra_block_editor_compatibility_css( $dynamic_css ) {

	if ( Astra_Dynamic_CSS::is_block_editor_support_enabled() ) {

		$compatibility_css = '
		.wp-block-search {
			margin-bottom: 20px;
		}
		.wp-block-site-tagline {
			margin-top: 20px;
		}
		form.wp-block-search .wp-block-search__input, .wp-block-search.wp-block-search__button-inside .wp-block-search__inside-wrapper {
			border-color: #eaeaea;
			background: #fafafa;
		}
		.wp-block-search.wp-block-search__button-inside .wp-block-search__inside-wrapper .wp-block-search__input:focus, .wp-block-loginout input:focus {
			outline: thin dotted;
		}
		.wp-block-loginout input:focus {
			border-color: transparent;
		}
	 	form.wp-block-search .wp-block-search__inside-wrapper .wp-block-search__input {
			padding: 12px;
		}
		form.wp-block-search .wp-block-search__button svg {
			fill: currentColor;
			width: 20px;
			height: 20px;
		}
		.wp-block-loginout p label {
			display: block;
		}
		.wp-block-loginout p:not(.login-remember):not(.login-submit) input {
			width: 100%;
		}
		.wp-block-loginout .login-remember input {
			width: 1.1rem;
			height: 1.1rem;
			margin: 0 5px 4px 0;
			vertical-align: middle;
		}';

		$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $compatibility_css );
	}

	/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( astra_get_option( 'improve-gb-editor-ui', true ) ) {
		/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$is_site_rtl = is_rtl();

		$editor_improvement_css = astra_get_block_editor_required_css();

		if ( $is_site_rtl ) {
			$editor_improvement_css .= '
			blockquote {
				padding: 0 1.2em 1.2em;
			}
			.wp-block-button__link {
				border: 2px solid currentColor;
			}
			body .wp-block-file .wp-block-file__button {
				text-decoration: none;
			}
			ul.wp-block-categories-list.wp-block-categories, ul.wp-block-archives-list.wp-block-archives {
				list-style-type: none;
			}
			ul, ol {
				margin-right: 20px;
			}
			figure.alignright figcaption {
				text-align: left;
			}';
		} else {
			$editor_improvement_css .= '
			blockquote {
				padding: 0 1.2em 1.2em;
			}
			.wp-block-button__link {
				border: 2px solid currentColor;
			}
			body .wp-block-file .wp-block-file__button {
				text-decoration: none;
			}
			ul.wp-block-categories-list.wp-block-categories, ul.wp-block-archives-list.wp-block-archives {
				list-style-type: none;
			}
			ul, ol {
				margin-left: 20px;
			}
			figure.alignright figcaption {
				text-align: right;
			}';
		}
	} else {
		$editor_improvement_css = '
			blockquote {
				padding: 1.2em;
			}
		';
	}

	$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $editor_improvement_css );

	return $dynamic_css;
}

/**
 * Astra block editor 2.0 Spectra compatibility - Dynamic CSS.
 *
 * @param string $dynamic_css Dynamic CSS.
 * @return string $dynamic_css Dynamic CSS.
 *
 * @since 3.8.0
 */
function astra_load_modern_block_editor_ui( $dynamic_css ) {
	$dynamic_css             .= astra_get_block_editor_required_css();
	$ltr_left                 = is_rtl() ? 'right' : 'left';
	$ltr_right                = is_rtl() ? 'left' : 'right';
	$astra_block_editor_v2_ui = astra_get_option( 'wp-blocks-v2-ui', true );
	$ast_container_width      = astra_get_option( 'site-content-width', 1200 ) . 'px';
	$blocks_spacings          = Astra_WP_Editor_CSS::astra_get_block_spacings();
	$list_blocks_space        = astra_get_option( 'list-blocks-spacing', true );

	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$tablet_breakpoint = (string) astra_get_tablet_breakpoint();
	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$mobile_breakpoint = (string) astra_get_mobile_breakpoint();
	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$desktop_top_spacing    = isset( $blocks_spacings['desktop']['top'] ) ? $blocks_spacings['desktop']['top'] : '';
	$desktop_right_spacing  = isset( $blocks_spacings['desktop']['right'] ) ? $blocks_spacings['desktop']['right'] : '';
	$desktop_bottom_spacing = isset( $blocks_spacings['desktop']['bottom'] ) ? $blocks_spacings['desktop']['bottom'] : '';
	$desktop_left_spacing   = isset( $blocks_spacings['desktop']['left'] ) ? $blocks_spacings['desktop']['left'] : '';
	$tablet_top_spacing     = isset( $blocks_spacings['tablet']['top'] ) ? $blocks_spacings['tablet']['top'] : '';
	$tablet_right_spacing   = isset( $blocks_spacings['tablet']['right'] ) ? $blocks_spacings['tablet']['right'] : '';
	$tablet_bottom_spacing  = isset( $blocks_spacings['tablet']['bottom'] ) ? $blocks_spacings['tablet']['bottom'] : '';
	$tablet_left_spacing    = isset( $blocks_spacings['tablet']['left'] ) ? $blocks_spacings['tablet']['left'] : '';
	$mobile_top_spacing     = isset( $blocks_spacings['mobile']['top'] ) ? $blocks_spacings['mobile']['top'] : '';
	$mobile_right_spacing   = isset( $blocks_spacings['mobile']['right'] ) ? $blocks_spacings['mobile']['right'] : '';
	$mobile_bottom_spacing  = isset( $blocks_spacings['mobile']['bottom'] ) ? $blocks_spacings['mobile']['bottom'] : '';
	$mobile_left_spacing    = isset( $blocks_spacings['mobile']['left'] ) ? $blocks_spacings['mobile']['left'] : '';

	$ast_content_width = apply_filters( 'astra_block_content_width', $astra_block_editor_v2_ui ? $ast_container_width : '910px' );
	$ast_wide_width    = apply_filters( 'astra_block_wide_width', $astra_block_editor_v2_ui ? 'calc(' . esc_attr( $ast_container_width ) . ' + var(--wp--custom--ast-default-block-left-padding) + var(--wp--custom--ast-default-block-right-padding))' : $ast_container_width );
	$ast_narrow_width  = astra_get_option( 'narrow-container-max-width', apply_filters( 'astra_narrow_container_width', 750 ) ) . 'px';

	// Spectra Compatibility - page title alignment with page container layouts.
	$spectra_gutenberg_compat_css = Astra_Dynamic_CSS::spectra_gutenberg_compat_css();
	$v4_block_editor_compat       = Astra_Dynamic_CSS::v4_block_editor_compat();

	$dynamic_css .= '
		html body {
			--wp--custom--ast-default-block-top-padding: ' . $desktop_top_spacing . ';
			--wp--custom--ast-default-block-right-padding: ' . $desktop_right_spacing . ';
			--wp--custom--ast-default-block-bottom-padding: ' . $desktop_bottom_spacing . ';
			--wp--custom--ast-default-block-left-padding: ' . $desktop_left_spacing . ';
			--wp--custom--ast-container-width: ' . $ast_container_width . ';
			--wp--custom--ast-content-width-size: ' . $ast_content_width . ';
			--wp--custom--ast-wide-width-size: ' . $ast_wide_width . ';
		}

		.ast-narrow-container {
			--wp--custom--ast-content-width-size: ' . $ast_narrow_width . ';
			--wp--custom--ast-wide-width-size: ' . $ast_narrow_width . ';
		}

		@media(max-width: ' . $tablet_breakpoint . 'px) {
			html body {
				--wp--custom--ast-default-block-top-padding: ' . $tablet_top_spacing . ';
				--wp--custom--ast-default-block-right-padding: ' . $tablet_right_spacing . ';
				--wp--custom--ast-default-block-bottom-padding: ' . $tablet_bottom_spacing . ';
				--wp--custom--ast-default-block-left-padding: ' . $tablet_left_spacing . ';
			}
		}
		@media(max-width: ' . $mobile_breakpoint . 'px) {
			html body {
				--wp--custom--ast-default-block-top-padding: ' . $mobile_top_spacing . ';
				--wp--custom--ast-default-block-right-padding: ' . $mobile_right_spacing . ';
				--wp--custom--ast-default-block-bottom-padding: ' . $mobile_bottom_spacing . ';
				--wp--custom--ast-default-block-left-padding: ' . $mobile_left_spacing . ';
			}
		}
	';

	$astra_wide_particular_selector = $astra_block_editor_v2_ui ? '.entry-content[ast-blocks-layout] > .alignwide' : '.entry-content[ast-blocks-layout] > .alignwide, .entry-content[ast-blocks-layout] .wp-block-cover__inner-container, .entry-content[ast-blocks-layout] > p';
	$astra_full_stretched_selector  = $astra_block_editor_v2_ui ? '.ast-plain-container.ast-no-sidebar .entry-content > .alignfull, .ast-page-builder-template .ast-no-sidebar .entry-content > .alignfull' : '.ast-plain-container.ast-no-sidebar .entry-content .alignfull, .ast-page-builder-template .ast-no-sidebar .entry-content .alignfull';

	$dynamic_css .= '
	.entry-content > .wp-block-group, .entry-content > .wp-block-cover, .entry-content > .wp-block-columns {
		padding-top: var(--wp--custom--ast-default-block-top-padding);
		padding-right: var(--wp--custom--ast-default-block-right-padding);
		padding-bottom: var(--wp--custom--ast-default-block-bottom-padding);
		padding-left: var(--wp--custom--ast-default-block-left-padding);
	}
	' . $astra_full_stretched_selector . ' {
		margin-left: calc( -50vw + 50%);
		margin-right: calc( -50vw + 50%);
		max-width: 100vw;
		width: 100vw;
	}
	.ast-plain-container.ast-no-sidebar .entry-content .alignfull .alignfull, .ast-page-builder-template.ast-no-sidebar .entry-content .alignfull .alignfull, .ast-plain-container.ast-no-sidebar .entry-content .alignfull .alignwide, .ast-page-builder-template.ast-no-sidebar .entry-content .alignfull .alignwide, .ast-plain-container.ast-no-sidebar .entry-content .alignwide .alignfull, .ast-page-builder-template.ast-no-sidebar .entry-content .alignwide .alignfull,
	.ast-plain-container.ast-no-sidebar .entry-content .alignwide .alignwide, .ast-page-builder-template.ast-no-sidebar .entry-content .alignwide .alignwide, .ast-plain-container.ast-no-sidebar .entry-content .wp-block-column .alignfull, .ast-page-builder-template.ast-no-sidebar .entry-content .wp-block-column .alignfull,
	.ast-plain-container.ast-no-sidebar .entry-content .wp-block-column .alignwide, .ast-page-builder-template.ast-no-sidebar .entry-content .wp-block-column .alignwide {
		margin-left: auto;
		margin-right: auto;
		width: 100%;
	}
	[ast-blocks-layout] .wp-block-separator:not(.is-style-dots) {
		height: 0;
	}
	[ast-blocks-layout] .wp-block-separator {
		margin: 20px auto;
	}
	[ast-blocks-layout] .wp-block-separator:not(.is-style-wide):not(.is-style-dots) {
		max-width: 100px;
	}
	[ast-blocks-layout] .wp-block-separator.has-background {
		padding: 0;
	}
	.entry-content[ast-blocks-layout] > * {
		max-width: var(--wp--custom--ast-content-width-size);
		margin-left: auto;
		margin-right: auto;
	}
	' . $astra_wide_particular_selector . ' {
		max-width: var(--wp--custom--ast-wide-width-size);
	}
	.entry-content[ast-blocks-layout] .alignfull {
		max-width: none;
	}
	.entry-content .wp-block-columns {
		margin-bottom: 0;
	}
	blockquote {
		margin: 1.5em;
		border-color: rgba(0,0,0,0.05);
	}
	.wp-block-quote:not(.has-text-align-right):not(.has-text-align-center) {
		border-' . esc_attr( $ltr_left ) . ': 5px solid rgba(0, 0, 0, 0.05);
	}
	.has-text-align-right > blockquote, blockquote.has-text-align-right {
		border-' . esc_attr( $ltr_right ) . ': 5px solid rgba(0, 0, 0, 0.05);
	}
	.has-text-align-left > blockquote, blockquote.has-text-align-left {
		border-' . esc_attr( $ltr_left ) . ': 5px solid rgba(0, 0, 0, 0.05);
	}
	.wp-block-site-tagline, .wp-block-latest-posts .read-more {
		margin-top: 15px;
	}
	.wp-block-loginout p label {
		display: block;
	}
	.wp-block-loginout p:not(.login-remember):not(.login-submit) input {
		width: 100%;
	}
	.wp-block-loginout input:focus {
		border-color: transparent;
	}
	.wp-block-loginout input:focus {
		outline: thin dotted;
	}
	.entry-content .wp-block-media-text .wp-block-media-text__content {
		padding: 0 0 0 8%;
	}
	.entry-content .wp-block-media-text.has-media-on-the-right .wp-block-media-text__content {
		padding: 0 8% 0 0;
	}
	.entry-content .wp-block-media-text.has-background .wp-block-media-text__content {
		padding: 8%;
	}
	.entry-content .wp-block-cover:not([class*="background-color"]) .wp-block-cover__inner-container, .entry-content .wp-block-cover:not([class*="background-color"]) .wp-block-cover-image-text, .entry-content .wp-block-cover:not([class*="background-color"]) .wp-block-cover-text, .entry-content .wp-block-cover-image:not([class*="background-color"]) .wp-block-cover__inner-container, .entry-content .wp-block-cover-image:not([class*="background-color"]) .wp-block-cover-image-text, .entry-content .wp-block-cover-image:not([class*="background-color"]) .wp-block-cover-text {
		color: var(--ast-global-color-5);
	}
	.wp-block-loginout .login-remember input {
		width: 1.1rem;
		height: 1.1rem;
		margin: 0 5px 4px 0;
		vertical-align: middle;
	}
	.wp-block-latest-posts > li > *:first-child, .wp-block-latest-posts:not(.is-grid) > li:first-child {
		margin-top: 0;
	}
	';

	if ( $astra_block_editor_v2_ui ) {
		$single_post_continer_spacing = astra_get_option( 'single-post-inside-spacing' );

		$container_lg_spacing = ( true === astra_check_is_structural_setup() ) ? '3' : '6.67';

		$astra_continer_left_spacing  = defined( 'ASTRA_EXT_VER' ) && astra_responsive_spacing( $single_post_continer_spacing, 'left', 'desktop' ) ? astra_responsive_spacing( $single_post_continer_spacing, 'left', 'desktop', $container_lg_spacing ) : 'var(--ast-container-default-xlg-padding)';
		$astra_continer_right_spacing = defined( 'ASTRA_EXT_VER' ) && astra_responsive_spacing( $single_post_continer_spacing, 'right', 'desktop' ) ? astra_responsive_spacing( $single_post_continer_spacing, 'right', 'desktop', $container_lg_spacing ) : 'var(--ast-container-default-xlg-padding)';

		$astra_1200_continer_left_spacing  = defined( 'ASTRA_EXT_VER' ) && astra_responsive_spacing( $single_post_continer_spacing, 'left', 'desktop' ) ? astra_responsive_spacing( $single_post_continer_spacing, 'left', 'desktop', '2.4' ) : 'var(--ast-container-default-xs-padding)';
		$astra_1200_continer_right_spacing = defined( 'ASTRA_EXT_VER' ) && astra_responsive_spacing( $single_post_continer_spacing, 'right', 'desktop' ) ? astra_responsive_spacing( $single_post_continer_spacing, 'right', 'desktop', '2.4' ) : 'var(--ast-container-default-xs-padding)';

		$alignwide_1200_left_negative_margin  = $astra_continer_left_spacing ? 'calc(-1 * min(' . $astra_continer_left_spacing . ', 20px))' : '-20px';
		$alignwide_1200_right_negative_margin = $astra_continer_right_spacing ? 'calc(-1 * min(' . $astra_continer_right_spacing . ', 20px))' : '-20px';

		$dynamic_css .= '
			.wp-block-search__inside-wrapper .wp-block-search__input {
				padding: 0 10px;
				color: var(--ast-global-color-3);
				background: var(--ast-global-color-5);
				border-color: var(--ast-border-color);
			}
			.wp-block-latest-posts .read-more {
				margin-bottom: 1.5em;
			}
			.wp-block-search__no-button .wp-block-search__inside-wrapper .wp-block-search__input {
				padding-top: 5px;
				padding-bottom: 5px;
			}
			.wp-block-latest-posts .wp-block-latest-posts__post-date, .wp-block-latest-posts .wp-block-latest-posts__post-author {
				font-size: 1rem;
			}
			.wp-block-latest-posts > li > *, .wp-block-latest-posts:not(.is-grid) > li {
				margin-top: 12px;
				margin-bottom: 12px;
			}
			.ast-page-builder-template .entry-content[ast-blocks-layout] > *, .ast-page-builder-template .entry-content[ast-blocks-layout] > .alignfull > * {
				max-width: none;
			}
			.ast-page-builder-template .entry-content[ast-blocks-layout] > .alignwide > * {
				max-width: var(--wp--custom--ast-wide-width-size);
			}
			.ast-page-builder-template .entry-content[ast-blocks-layout] > .inherit-container-width > *, .ast-page-builder-template .entry-content[ast-blocks-layout] > * > *, .entry-content[ast-blocks-layout] > .wp-block-cover .wp-block-cover__inner-container {
				max-width: var(--wp--custom--ast-content-width-size);
				margin-left: auto;
				margin-right: auto;
			}
			.entry-content[ast-blocks-layout] .wp-block-cover:not(.alignleft):not(.alignright) {
				width: auto;
			}
			@media(max-width: 1200px) {
				.ast-separate-container .entry-content > .alignfull, .ast-separate-container .entry-content[ast-blocks-layout] > .alignwide, .ast-plain-container .entry-content[ast-blocks-layout] > .alignwide, .ast-plain-container .entry-content .alignfull {
					margin-left: ' . $alignwide_1200_left_negative_margin . ' ;
					margin-right: ' . $alignwide_1200_right_negative_margin . ';
				}
			}
			@media(min-width: 1201px) {
				.ast-separate-container .entry-content > .alignfull {
					margin-left: calc(-1 * ' . $astra_continer_left_spacing . ' );
					margin-right: calc(-1 * ' . $astra_continer_right_spacing . ' );
				}
				.ast-separate-container .entry-content[ast-blocks-layout] > .alignwide, .ast-plain-container .entry-content[ast-blocks-layout] > .alignwide {
					margin-left: calc(-1 * var(--wp--custom--ast-default-block-left-padding) );
					margin-right: calc(-1 * var(--wp--custom--ast-default-block-right-padding) );
				}
			}
			@media(min-width: ' . $tablet_breakpoint . 'px) {
				.ast-separate-container .entry-content .wp-block-group.alignwide:not(.inherit-container-width) > :where(:not(.alignleft):not(.alignright)), .ast-plain-container .entry-content .wp-block-group.alignwide:not(.inherit-container-width) > :where(:not(.alignleft):not(.alignright)) {
					max-width: calc( var(--wp--custom--ast-content-width-size) + 80px );
				}
				.ast-plain-container.ast-right-sidebar .entry-content[ast-blocks-layout] .alignfull, .ast-plain-container.ast-left-sidebar .entry-content[ast-blocks-layout] .alignfull {
					margin-left: -60px;
					margin-right: -60px;
				}
			}
			@media(min-width: ' . $mobile_breakpoint . 'px) {
				.entry-content > .alignleft {
					margin-' . esc_attr( $ltr_right ) . ': 20px;
				}
				.entry-content > .alignright {
					margin-' . esc_attr( $ltr_left ) . ': 20px;
				}
			}
		';

	} else {
		$dynamic_css .= '
			.wp-block-latest-posts > li > a {
				font-size: 28px;
			}
			.wp-block-latest-posts > li > *, .wp-block-latest-posts:not(.is-grid) > li {
				margin-top: 15px;
				margin-bottom: 15px;
			}
			.wp-block-latest-posts .wp-block-latest-posts__post-date, .wp-block-latest-posts .wp-block-latest-posts__post-author {
				font-size: 15px;
			}
		';
	}

	$mobile_css = array(
		'.wp-block-columns .wp-block-column:not(:last-child)' => array(
			'margin-bottom' => '20px',
		),
		'.wp-block-latest-posts' => array(
			'margin' => '0',
		),
	);
	/* Parse CSS from array -> max-width(mobile-breakpoint) */
	/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$dynamic_css .= astra_parse_css( $mobile_css, '', $mobile_breakpoint );
	/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$dynamic_css .= '
		@media( max-width: 600px ) {
			.entry-content .wp-block-media-text .wp-block-media-text__content, .entry-content .wp-block-media-text.has-media-on-the-right .wp-block-media-text__content {
				padding: 8% 0 0;
			}
			.entry-content .wp-block-media-text.has-background .wp-block-media-text__content {
				padding: 8%;
			}
		}
	';

	// Spectra Compatibility - Container block alignment with page title for container layouts.
	if ( $spectra_gutenberg_compat_css ) {
		$dynamic_css .= '
			.ast-page-builder-template .entry-header {
				padding-' . esc_attr( $ltr_left ) . ': 0;
			}
		';
	}

	// Spectra compatibility for narrow width container.
	$dynamic_css .= '
		.ast-narrow-container .site-content .wp-block-uagb-image--align-full .wp-block-uagb-image__figure {
			max-width: 100%;
			margin-left: auto;
			margin-right: auto;
		}
	';

	$direction     = is_rtl() ? 'right' : 'left';
	$lists_padding = Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? 'padding-' . $direction . ': 20px;' : '';
	// Block editor experience improvements introduced with v4.0.0.
	if ( $v4_block_editor_compat ) {
		$dynamic_css .= '
			.entry-content ul, .entry-content ol {
				padding: revert;
				margin: revert;
				' . $lists_padding . '
			}
		';
	}

	return $dynamic_css;
}
