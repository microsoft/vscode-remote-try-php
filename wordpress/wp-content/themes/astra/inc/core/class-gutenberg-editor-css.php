<?php
/**
 * Gutenberg Editor CSS
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        http://wpastra.com/
 * @since       Astra 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Gutenberg_Editor_CSS' ) ) :

	/**
	 * Admin Helper
	 */
	// @codingStandardsIgnoreStart WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	class Gutenberg_Editor_CSS {
		// @codingStandardsIgnoreEnd WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		/**
		 * Get dynamic CSS  required for the block editor to make editing experience similar to how it looks on frontend.
		 *
		 * @return String CSS to be loaded in the editor interface.
		 */
		public static function get_css() {
			global $pagenow;
			global $post;
			$post_id     = astra_get_post_id();
			$is_site_rtl = is_rtl();
			$ltr_left    = $is_site_rtl ? 'right' : 'left';
			$ltr_right   = $is_site_rtl ? 'left' : 'right';

			$site_content_width      = astra_get_option( 'site-content-width', 1200 ) + 56;
			$headings_font_family    = astra_get_option( 'headings-font-family' );
			$headings_font_weight    = astra_get_option( 'headings-font-weight' );
			$headings_text_transform = astra_get_font_extras( astra_get_option( 'headings-font-extras' ), 'text-transform' );
			$headings_line_height    = astra_get_font_extras( astra_get_option( 'headings-font-extras' ), 'line-height', 'line-height-unit' );
			$body_font_family        = astra_body_font_family();
			$para_margin_bottom      = astra_get_option( 'para-margin-bottom' );
			$theme_color             = astra_get_option( 'theme-color' );
			$link_color              = astra_get_option( 'link-color', $theme_color );
			$heading_base_color      = astra_get_option( 'heading-base-color' );
			$highlight_theme_color   = astra_get_foreground_color( $theme_color );
			$ast_narrow_width        = astra_get_option( 'narrow-container-max-width', apply_filters( 'astra_narrow_container_width', 750 ) ) . 'px';

			$body_font_weight    = astra_get_option( 'body-font-weight' );
			$body_font_size      = astra_get_option( 'font-size-body' );
			$body_line_height    = astra_get_option( 'body-line-height' );
			$body_text_transform = astra_get_option( 'body-text-transform' );
			$box_bg_obj          = astra_get_option( 'site-layout-outside-bg-obj-responsive' );
			$text_color          = astra_get_option( 'text-color' );

			$heading_h1_font_size = astra_get_option( 'font-size-h1' );
			$heading_h2_font_size = astra_get_option( 'font-size-h2' );
			$heading_h3_font_size = astra_get_option( 'font-size-h3' );
			$heading_h4_font_size = astra_get_option( 'font-size-h4' );
			$heading_h5_font_size = astra_get_option( 'font-size-h5' );
			$heading_h6_font_size = astra_get_option( 'font-size-h6' );

			/**
			 * WooCommerce Grid Products compatibility.
			 */
			$link_h_color             = astra_get_option( 'link-h-color' );
			$btn_color                = astra_get_option( 'button-color' );
			$btn_bg_color             = astra_get_option( 'button-bg-color', '', $theme_color );
			$btn_h_color              = astra_get_option( 'button-h-color' );
			$btn_bg_h_color           = astra_get_option( 'button-bg-h-color', '', $link_h_color );
			$btn_border_radius_fields = astra_get_option( 'button-radius-fields' );
			$theme_btn_padding        = astra_get_option( 'theme-button-padding' );

			/**
			 * Button theme compatibility.
			 */
			$global_custom_button_border_size = astra_get_option( 'theme-button-border-group-border-size' );
			$btn_border_color                 = astra_get_option( 'theme-button-border-group-border-color' );
			$btn_border_h_color               = astra_get_option( 'theme-button-border-group-border-h-color' );

			/**
			 * Theme Button Typography
			 */
			$theme_btn_font_family     = astra_get_option( 'font-family-button' );
			$theme_btn_font_size       = astra_get_option( 'font-size-button' );
			$theme_btn_font_weight     = astra_get_option( 'font-weight-button' );
			$theme_btn_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-button' ), 'text-transform' );
			$theme_btn_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-button' ), 'line-height', 'line-height-unit' );
			$theme_btn_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-button' ), 'letter-spacing', 'letter-spacing-unit' );
			$theme_btn_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-button' ), 'text-decoration' );

			$h1_font_family     = astra_get_option( 'font-family-h1' );
			$h1_font_weight     = astra_get_option( 'font-weight-h1' );
			$h1_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h1' ), 'line-height', 'line-height-unit' );
			$h1_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h1' ), 'text-transform' );
			$h1_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h1' ), 'letter-spacing', 'letter-spacing-unit' );
			$h1_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h1' ), 'text-decoration' );

			$h2_font_family     = astra_get_option( 'font-family-h2' );
			$h2_font_weight     = astra_get_option( 'font-weight-h2' );
			$h2_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h2' ), 'line-height', 'line-height-unit' );
			$h2_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h2' ), 'text-transform' );
			$h2_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h2' ), 'letter-spacing', 'letter-spacing-unit' );
			$h2_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h2' ), 'text-decoration' );

			$h3_font_family     = astra_get_option( 'font-family-h3' );
			$h3_font_weight     = astra_get_option( 'font-weight-h3' );
			$h3_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h3' ), 'line-height', 'line-height-unit' );
			$h3_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h3' ), 'text-transform' );
			$h3_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h3' ), 'letter-spacing', 'letter-spacing-unit' );
			$h3_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h3' ), 'text-decoration' );

			$h4_font_family     = astra_get_option( 'font-family-h4' );
			$h4_font_weight     = astra_get_option( 'font-weight-h4' );
			$h4_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h4' ), 'line-height', 'line-height-unit' );
			$h4_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h4' ), 'text-transform' );
			$h4_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h4' ), 'letter-spacing', 'letter-spacing-unit' );
			$h4_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h4' ), 'text-decoration' );

			$h5_font_family     = astra_get_option( 'font-family-h5' );
			$h5_font_weight     = astra_get_option( 'font-weight-h5' );
			$h5_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h5' ), 'line-height', 'line-height-unit' );
			$h5_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h5' ), 'text-transform' );
			$h5_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h5' ), 'letter-spacing', 'letter-spacing-unit' );
			$h5_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h5' ), 'text-decoration' );

			$h6_font_family     = astra_get_option( 'font-family-h6' );
			$h6_font_weight     = astra_get_option( 'font-weight-h6' );
			$h6_line_height     = astra_get_font_extras( astra_get_option( 'font-extras-h6' ), 'line-height', 'line-height-unit' );
			$h6_text_transform  = astra_get_font_extras( astra_get_option( 'font-extras-h6' ), 'text-transform' );
			$h6_letter_spacing  = astra_get_font_extras( astra_get_option( 'font-extras-h6' ), 'letter-spacing', 'letter-spacing-unit' );
			$h6_text_decoration = astra_get_font_extras( astra_get_option( 'font-extras-h6' ), 'text-decoration' );

			// Checking if font weight is different in GB editor.
			if ( 'inherit' === $h1_font_weight ) {
				$h1_font_weight = 'normal';
			}
			if ( 'inherit' === $h2_font_weight ) {
				$h2_font_weight = 'normal';
			}
			if ( 'inherit' === $h3_font_weight ) {
				$h3_font_weight = 'normal';
			}
			if ( 'inherit' === $h4_font_weight ) {
				$h4_font_weight = 'normal';
			}
			if ( 'inherit' === $h5_font_weight ) {
				$h5_font_weight = 'normal';
			}
			if ( 'inherit' === $h6_font_weight ) {
				$h6_font_weight = 'normal';
			}

			$single_post_title       = astra_get_option( 'ast-dynamic-single-post-structure', array( 'ast-dynamic-single-post-title', 'ast-dynamic-single-post-meta' ) );
			$title_enabled_from_meta = get_post_meta( $post_id, 'site-post-title', true );

			$is_widget_title_support_font_weight = Astra_Dynamic_CSS::support_font_css_to_widget_and_in_editor();
			$font_weight_prop                    = ( $is_widget_title_support_font_weight ) ? 'inherit' : 'normal';

			// Fallback for H1 - headings typography.
			if ( 'inherit' == $h1_font_family ) {
				$h1_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h1_font_weight ) {
				$h1_font_weight = $headings_font_weight;
			}
			if ( '' == $h1_text_transform ) {
				$h1_text_transform = $headings_text_transform;
			}
			if ( '' == $h1_line_height ) {
				$h1_line_height = $headings_line_height;
			}

			// Fallback for H2 - headings typography.
			if ( 'inherit' == $h2_font_family ) {
				$h2_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h2_font_weight ) {
				$h2_font_weight = $headings_font_weight;
			}
			if ( '' == $h2_text_transform ) {
				$h2_text_transform = $headings_text_transform;
			}
			if ( '' == $h2_line_height ) {
				$h2_line_height = $headings_line_height;
			}

			// Fallback for H3 - headings typography.
			if ( 'inherit' == $h3_font_family ) {
				$h3_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h3_font_weight ) {
				$h3_font_weight = $headings_font_weight;
			}
			if ( '' == $h3_text_transform ) {
				$h3_text_transform = $headings_text_transform;
			}
			if ( '' == $h3_line_height ) {
				$h3_line_height = $headings_line_height;
			}

			// Fallback for H4 - headings typography.
			if ( 'inherit' == $h4_font_family ) {
				$h4_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h4_font_weight ) {
				$h4_font_weight = $headings_font_weight;
			}
			if ( '' == $h4_text_transform ) {
				$h4_text_transform = $headings_text_transform;
			}
			if ( '' == $h4_line_height ) {
				$h4_line_height = $headings_line_height;
			}

			// Fallback for H5 - headings typography.
			if ( 'inherit' == $h5_font_family ) {
				$h5_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h5_font_weight ) {
				$h5_font_weight = $headings_font_weight;
			}
			if ( '' == $h5_text_transform ) {
				$h5_text_transform = $headings_text_transform;
			}
			if ( '' == $h5_line_height ) {
				$h5_line_height = $headings_line_height;
			}

			// Fallback for H6 - headings typography.
			if ( 'inherit' == $h6_font_family ) {
				$h6_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $h6_font_weight ) {
				$h6_font_weight = $headings_font_weight;
			}
			if ( '' == $h6_text_transform ) {
				$h6_text_transform = $headings_text_transform;
			}
			if ( '' == $h6_line_height ) {
				$h6_line_height = $headings_line_height;
			}

			if ( empty( $btn_color ) ) {
				$btn_color = astra_get_foreground_color( $theme_color );
			}

			if ( empty( $btn_h_color ) ) {
				$btn_h_color = astra_get_foreground_color( $link_h_color );
			}

			if ( is_array( $body_font_size ) ) {
				$body_font_size_desktop = ( isset( $body_font_size['desktop'] ) && '' != $body_font_size['desktop'] ) ? $body_font_size['desktop'] : 15;
			} else {
				$body_font_size_desktop = ( '' != $body_font_size ) ? $body_font_size : 15;
			}

			// Site title (Page Title) on Block Editor.
			$post_type                 = strval( get_post_type() );
			$site_title_font_family    = astra_get_option( 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-family' );
			$site_title_font_weight    = astra_get_option( 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-weight' );
			$site_title_line_height    = astra_get_option( 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-line-height' );
			$site_title_font_size      = astra_get_option( 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-size' );
			$site_title_text_transform = astra_get_option( 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-text-transform', $headings_text_transform );

			// Fallback for Site title (Page Title).
			if ( 'inherit' == $site_title_font_family ) {
				$site_title_font_family = $headings_font_family;
			}
			if ( $font_weight_prop === $site_title_font_weight ) {
				$site_title_font_weight = $headings_font_weight;
			}
			if ( '' == $site_title_text_transform ) {
				$site_title_text_transform = '' === $headings_text_transform ? astra_get_option( 'text-transform-h1' ) : $headings_text_transform;
			}
			if ( '' == $site_title_line_height ) {
				$site_title_line_height = $headings_line_height;
			}
			if ( 'inherit' == $site_title_font_weight || '' == $site_title_font_weight ) {
				$site_title_font_weight = 'normal';
			}

			// check the selection color incase of empty/no theme color.
			$selection_text_color = ( 'transparent' === $highlight_theme_color ) ? '' : $highlight_theme_color;

			// Gutenberg editor improvement.
			/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$improve_gb_ui = astra_get_option( 'improve-gb-editor-ui', true );
			/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$ast_content_width = apply_filters( 'astra_block_content_width', '910px' );

			$content_width_size             = ( true === $improve_gb_ui ) ? $ast_content_width : '1200px';
			$css                            = ':root{ --wp--custom--ast-content-width-size: ' . $content_width_size . ' }';
			$css                           .= '.ast-narrow-container { --wp--custom--ast-content-width-size: ' . $ast_narrow_width . ' }';
			$astra_apply_content_background = astra_apply_content_background_fullwidth_layouts();

			$desktop_css = array(
				'html'                                    => array(
					'font-size' => astra_get_font_css_value( (int) $body_font_size_desktop * 6.25, '%' ),
				),
				':root'                                   => Astra_Global_Palette::generate_global_palette_style(),
				'.block-editor-writing-flow a'            => array(
					'color' => esc_attr( $link_color ),
				),

				// Global selection CSS.
				'.block-editor-block-list__layout .block-editor-block-list__block ::selection,.block-editor-block-list__layout .block-editor-block-list__block.is-multi-selected .editor-block-list__block-edit:before' => array(
					'background-color' => esc_attr( $theme_color ),
				),
				'.block-editor-block-list__layout .block-editor-block-list__block ::selection,.block-editor-block-list__layout .block-editor-block-list__block.is-multi-selected .editor-block-list__block-edit' => array(
					'color' => esc_attr( $selection_text_color ),
				),
				'.ast-separate-container .edit-post-visual-editor, .ast-page-builder-template .edit-post-visual-editor, .ast-plain-container .edit-post-visual-editor, .ast-separate-container #wpwrap #editor .edit-post-visual-editor' => astra_get_responsive_background_obj( $box_bg_obj, 'desktop' ),
				'.editor-post-title__block .editor-post-title__input,  .edit-post-visual-editor .block-editor-block-list__block h1, .edit-post-visual-editor .block-editor-block-list__block h2, .edit-post-visual-editor .block-editor-block-list__block h3, .edit-post-visual-editor .block-editor-block-list__block h4, .edit-post-visual-editor .block-editor-block-list__block h5, .edit-post-visual-editor .block-editor-block-list__block h6, .edit-post-visual-editor h1, .edit-post-visual-editor h2, .edit-post-visual-editor h3, .edit-post-visual-editor h4, .edit-post-visual-editor h5, .edit-post-visual-editor h6' => array(
					'font-family'    => astra_get_css_value( $headings_font_family, 'font' ),
					'font-weight'    => astra_get_css_value( $headings_font_weight, 'font' ),
					'text-transform' => esc_attr( $headings_text_transform ),
				),
				'.edit-post-visual-editor h1, .edit-post-visual-editor h2, .edit-post-visual-editor h3, .edit-post-visual-editor h4, .edit-post-visual-editor h5, .edit-post-visual-editor h6' => array(
					'line-height' => esc_attr( $headings_line_height ),
				),
				'.edit-post-visual-editor.editor-styles-wrapper p,.block-editor-block-list__block p, .block-editor-block-list__layout, .editor-post-title' => array(
					'font-size' => astra_responsive_font( $body_font_size, 'desktop' ),
				),
				'.edit-post-visual-editor.editor-styles-wrapper p,.block-editor-block-list__block p, .wp-block-latest-posts a,.editor-default-block-appender textarea.editor-default-block-appender__content, .block-editor-block-list__block, .block-editor-block-list__block h1, .block-editor-block-list__block h2, .block-editor-block-list__block h3, .block-editor-block-list__block h4, .block-editor-block-list__block h5, .block-editor-block-list__block h6, .edit-post-visual-editor .editor-styles-wrapper' => array(
					'font-family'    => astra_get_font_family( $body_font_family ),
					'font-weight'    => esc_attr( $body_font_weight ),
					'font-size'      => astra_responsive_font( $body_font_size, 'desktop' ),
					'line-height'    => esc_attr( $body_line_height ),
					'text-transform' => esc_attr( $body_text_transform ),
					'margin-bottom'  => astra_get_css_value( $para_margin_bottom, 'em' ),
				),
				'.editor-post-title__block .editor-post-title__input' => array(
					'font-family' => ( 'inherit' === $headings_font_family ) ? astra_get_font_family( $body_font_family ) : astra_get_font_family( $headings_font_family ),
					'font-weight' => 'normal',
				),
				'.block-editor-block-list__block'         => array(
					'color' => esc_attr( $text_color ),
				),

				/**
				 * Content base heading color.
				 */
				'.editor-post-title__block .editor-post-title__input, .wc-block-grid__product-title, .editor-styles-wrapper .block-editor-block-list__block h1, .editor-styles-wrapper .block-editor-block-list__block h2, .editor-styles-wrapper .block-editor-block-list__block h3, .editor-styles-wrapper .block-editor-block-list__block h4, .editor-styles-wrapper .block-editor-block-list__block h5, .editor-styles-wrapper .block-editor-block-list__block h6, .editor-styles-wrapper .wp-block-heading, .editor-styles-wrapper .wp-block-uagb-advanced-heading h1, .editor-styles-wrapper .wp-block-uagb-advanced-heading h2, .editor-styles-wrapper .wp-block-uagb-advanced-heading h3, .editor-styles-wrapper .wp-block-uagb-advanced-heading h4, .editor-styles-wrapper .wp-block-uagb-advanced-heading h5, .editor-styles-wrapper .wp-block-uagb-advanced-heading h6,.editor-styles-wrapper h1.block-editor-block-list__block, .editor-styles-wrapper h2.block-editor-block-list__block, .editor-styles-wrapper h3.block-editor-block-list__block, .editor-styles-wrapper h4.block-editor-block-list__block, .editor-styles-wrapper h5.block-editor-block-list__block, .editor-styles-wrapper h6.block-editor-block-list__block' => array(
					'color' => esc_attr( $heading_base_color ),
				),
				// Blockquote Text Color.
				'blockquote'                              => array(
					'color' => astra_adjust_brightness( $text_color, 75, 'darken' ),
				),
				'blockquote .editor-rich-text__tinymce a' => array(
					'color' => astra_hex_to_rgba( $link_color, 1 ),
				),
				'blockquote'                              => array(
					'border-color' => astra_hex_to_rgba( $link_color, 0.05 ),
				),
				'.block-editor-block-list__block .wp-block-quote:not(.is-large):not(.is-style-large), .edit-post-visual-editor .wp-block-pullquote blockquote' => array(
					'border-color' => astra_hex_to_rgba( $link_color, 0.15 ),
				),

				// Heading H1 - H6 font size.
				'.editor-styles-wrapper .block-editor-block-list__block h1, .wp-block-heading h1, .wp-block-freeform.block-library-rich-text__tinymce h1, .editor-styles-wrapper .wp-block-heading h1, .wp-block-heading h1.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h1, .editor-styles-wrapper h1.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h1_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h1_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h1_font_weight, 'font' ),
					'line-height'     => esc_attr( $h1_line_height ),
					'text-transform'  => esc_attr( $h1_text_transform ),
					'text-decoration' => esc_attr( $h1_text_decoration ),
					'letter-spacing'  => esc_attr( $h1_letter_spacing ),

				),
				'.editor-styles-wrapper .block-editor-block-list__block h2, .wp-block-heading h2, .wp-block-freeform.block-library-rich-text__tinymce h2, .editor-styles-wrapper .wp-block-heading h2, .wp-block-heading h2.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h2, .editor-styles-wrapper h2.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h2_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h2_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h2_font_weight, 'font' ),
					'line-height'     => esc_attr( $h2_line_height ),
					'text-transform'  => esc_attr( $h2_text_transform ),
					'text-decoration' => esc_attr( $h2_text_decoration ),
					'letter-spacing'  => esc_attr( $h2_letter_spacing ),
				),
				'.editor-styles-wrapper .block-editor-block-list__block h3, .wp-block-heading h3, .wp-block-freeform.block-library-rich-text__tinymce h3, .editor-styles-wrapper .wp-block-heading h3, .wp-block-heading h3.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h3, .editor-styles-wrapper h3.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h3_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h3_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h3_font_weight, 'font' ),
					'line-height'     => esc_attr( $h3_line_height ),
					'text-transform'  => esc_attr( $h3_text_transform ),
					'text-decoration' => esc_attr( $h3_text_decoration ),
					'letter-spacing'  => esc_attr( $h3_letter_spacing ),
				),
				'.editor-styles-wrapper .block-editor-block-list__block h4, .wp-block-heading h4, .wp-block-freeform.block-library-rich-text__tinymce h4, .editor-styles-wrapper .wp-block-heading h4, .wp-block-heading h4.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h4, .editor-styles-wrapper h4.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h4_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h4_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h4_font_weight, 'font' ),
					'line-height'     => esc_attr( $h4_line_height ),
					'text-transform'  => esc_attr( $h4_text_transform ),
					'text-decoration' => esc_attr( $h4_text_decoration ),
					'letter-spacing'  => esc_attr( $h4_letter_spacing ),
				),
				'.editor-styles-wrapper .block-editor-block-list__block h5, .wp-block-heading h5, .wp-block-freeform.block-library-rich-text__tinymce h5, .editor-styles-wrapper .wp-block-heading h5, .wp-block-heading h5.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h5, .editor-styles-wrapper h5.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h5_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h5_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h5_font_weight, 'font' ),
					'line-height'     => esc_attr( $h5_line_height ),
					'text-transform'  => esc_attr( $h5_text_transform ),
					'text-decoration' => esc_attr( $h5_text_decoration ),
					'letter-spacing'  => esc_attr( $h5_letter_spacing ),
				),
				'.editor-styles-wrapper .block-editor-block-list__block h6, .wp-block-heading h6, .wp-block-freeform.block-library-rich-text__tinymce h6, .editor-styles-wrapper .wp-block-heading h6, .wp-block-heading h6.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h6, .editor-styles-wrapper h6.block-editor-block-list__block' => array(
					'font-size'       => astra_responsive_font( $heading_h6_font_size, 'desktop' ),
					'font-family'     => astra_get_css_value( $h6_font_family, 'font' ),
					'font-weight'     => astra_get_css_value( $h6_font_weight, 'font' ),
					'line-height'     => esc_attr( $h6_line_height ),
					'text-transform'  => esc_attr( $h6_text_transform ),
					'text-decoration' => esc_attr( $h6_text_decoration ),
					'letter-spacing'  => esc_attr( $h6_letter_spacing ),
				),

				/* Seperator block default width */
				'.wp-block-separator:not(.is-style-wide):not(.is-style-dots)' => array(
					'width' => '100px !important',
				),

				/**
				 * WooCommerce Grid Products compatibility.
				 */
				'.wc-block-grid__product-title'           => array(
					'color' => esc_attr( $text_color ),
				),
				'.wc-block-grid__product .wc-block-grid__product-onsale' => array(
					'background-color' => $theme_color,
					'color'            => astra_get_foreground_color( $theme_color ),
				),
				'.editor-styles-wrapper .wc-block-grid__products .wc-block-grid__product .wp-block-button__link, .wc-block-grid__product-onsale' => array(
					'color'            => $btn_color,
					'border-color'     => $btn_bg_color,
					'background-color' => $btn_bg_color,
				),
				'.wc-block-grid__products .wc-block-grid__product .wp-block-button__link:hover' => array(
					'color'            => $btn_h_color,
					'border-color'     => $btn_bg_h_color,
					'background-color' => $btn_bg_h_color,
				),
				'.wc-block-grid__products .wc-block-grid__product .wp-block-button__link' => array(
					'border-top-left-radius'     => astra_responsive_spacing( $btn_border_radius_fields, 'top', 'desktop' ),
					'border-top-right-radius'    => astra_responsive_spacing( $btn_border_radius_fields, 'right', 'desktop' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $btn_border_radius_fields, 'bottom', 'desktop' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $btn_border_radius_fields, 'left', 'desktop' ),
					'padding-top'                => astra_responsive_spacing( $theme_btn_padding, 'top', 'desktop' ),
					'padding-right'              => astra_responsive_spacing( $theme_btn_padding, 'right', 'desktop' ),
					'padding-bottom'             => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'desktop' ),
					'padding-left'               => astra_responsive_spacing( $theme_btn_padding, 'left', 'desktop' ),
				),

				// Margin bottom same as applied on frontend.
				'.editor-styles-wrapper .is-root-container.block-editor-block-list__layout > .wp-block-heading' => array(
					'margin-bottom' => '20px',
				),

				/**
				 * Site title (Page Title) on Block Editor.
				 */
				'body .edit-post-visual-editor__post-title-wrapper > h1:first-of-type' => array(
					'font-size'      => astra_responsive_font( $site_title_font_size, 'desktop' ),
					'font-weight'    => astra_get_css_value( $site_title_font_weight, 'font' ),
					'font-family'    => astra_get_css_value( $site_title_font_family, 'font', $body_font_family ),
					'line-height'    => esc_attr( $site_title_line_height ),
					'text-transform' => esc_attr( $site_title_text_transform ),
				),
			);

			if ( false === $improve_gb_ui ) {
				$desktop_css['.editor-post-title__block,.editor-default-block-appender,.block-editor-block-list__block'] = array(
					'max-width' => astra_get_css_value( $site_content_width, 'px' ),
				);
				$desktop_css['.block-editor-block-list__block[data-align=wide]'] = array(
					'max-width' => astra_get_css_value( $site_content_width + 200, 'px' ),
				);
			}

			$background_style_data = astra_get_responsive_background_obj( $box_bg_obj, 'desktop' );
			if ( empty( $background_style_data ) ) {
				$background_style_data = array(
					'background-color' => '#ffffff',
				);
			}

			if ( astra_wp_version_compare( '5.7', '>=' ) ) {

				if ( true === $improve_gb_ui ) {
					$desktop_css['.editor-styles-wrapper > .block-editor-block-list__layout']       = array(
						'width'   => '100%',
						'margin'  => '0 auto',
						'padding' => '0',
					);
					$desktop_css['.ast-separate-container .edit-post-visual-editor']                = array(
						'padding' => '20px',
					);
					$desktop_css['.edit-post-visual-editor__post-title-wrapper .editor-post-title'] = array(
						'margin' => '0',
					);
				} else {
					$desktop_css['.edit-post-visual-editor']                        = array(
						'padding'     => '20px',
						'padding-top' => 'calc(2em + 20px)',
					);
					$desktop_css['.edit-post-visual-editor .editor-styles-wrapper'] = array(
						'max-width' => astra_get_css_value( $site_content_width - 56, 'px' ),
						'width'     => '100%',
						'margin'    => '0 auto',
						'padding'   => '0',
					);
					$desktop_css['.ast-page-builder-template .edit-post-visual-editor .editor-styles-wrapper'] = array(
						'max-width' => '100%',
					);
					$desktop_css['.ast-separate-container .edit-post-visual-editor .block-editor-block-list__layout .wp-block[data-align="full"] figure.wp-block-image, .ast-separate-container .edit-post-visual-editor .wp-block[data-align="full"] .wp-block-cover'] = array(
						'margin-left'  => 'calc(-4.8em - 10px)',
						'margin-right' => 'calc(-4.8em - 10px)',
					);
					$desktop_css['.ast-separate-container .editor-post-title'] = array(
						'margin-top' => '0',
					);
				}

				$desktop_css['.ast-page-builder-template .edit-post-visual-editor'] = array(
					'padding' => '0',
				);
				$desktop_css['.editor-styles-wrapper .block-editor-writing-flow']   = array(
					'height'  => '100%',
					'padding' => '10px',
				);
				$desktop_css['.ast-page-builder-template .editor-styles-wrapper .block-editor-writing-flow, .ast-plain-container .editor-styles-wrapper .block-editor-writing-flow, #editor .edit-post-visual-editor'] = $background_style_data;
			}

			if ( astra_wp_version_compare( '5.8', '>=' ) ) {
				$desktop_css['.edit-post-visual-editor__content-area > div']            = array(
					'background' => 'inherit !important',
				);
				$desktop_css['.wp-block[data-align=left]>*']                            = array(
					'float' => 'left',
				);
				$desktop_css['.wp-block[data-align=right]>*']                           = array(
					'float' => 'right',
				);
				$desktop_css['.wp-block[data-align=left], .wp-block[data-align=right]'] = array(
					'float' => 'none !important',
				);
				if ( false === $astra_apply_content_background ) {
					$desktop_css['.ast-page-builder-template .editor-styles-wrapper, .ast-plain-container .editor-styles-wrapper, .ast-narrow-container .editor-styles-wrapper'] = $background_style_data;
				}
			}

			if ( ( ( ! in_array( 'ast-dynamic-single-post-title', $single_post_title ) ) && ( 'post' === get_post_type() ) ) || ( 'disabled' === $title_enabled_from_meta ) ) {
				$destop_title_css = array(
					'.editor-post-title__block' => array(
						'opacity' => '0.2',
					),
				);
				$css             .= astra_parse_css( $destop_title_css );
			}

			$content_links_underline = astra_get_option( 'underline-content-links' );

			if ( $content_links_underline ) {
				$desktop_css['.edit-post-visual-editor a'] = array(
					'text-decoration' => 'underline',
				);

				$reset_underline_from_anchors = Astra_Dynamic_CSS::unset_builder_elements_underline();

				$excluding_anchor_selectors = $reset_underline_from_anchors ? '.edit-post-visual-editor a.uagb-tabs-list, .edit-post-visual-editor .uagb-ifb-cta a, .edit-post-visual-editor a.uagb-marketing-btn__link, .edit-post-visual-editor .uagb-post-grid a, .edit-post-visual-editor .uagb-toc__wrap a, .edit-post-visual-editor .uagb-taxomony-box a, .edit-post-visual-editor .uagb_review_block a' : '';

				$desktop_css[ $excluding_anchor_selectors ] = array(
					'text-decoration' => 'none',
				);
			}

			$css .= astra_parse_css( $desktop_css );

			/**
			 * Global button CSS - Tablet.
			 */
			$css_prod_button_tablet = array(
				'.wc-block-grid__products .wc-block-grid__product .wp-block-button__link' => array(
					'padding-top'    => astra_responsive_spacing( $theme_btn_padding, 'top', 'tablet' ),
					'padding-right'  => astra_responsive_spacing( $theme_btn_padding, 'right', 'tablet' ),
					'padding-bottom' => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'tablet' ),
					'padding-left'   => astra_responsive_spacing( $theme_btn_padding, 'left', 'tablet' ),
				),
			);

			if ( astra_wp_version_compare( '5.7', '>=' ) ) {
				$css_prod_button_tablet['.ast-page-builder-template .editor-styles-wrapper .block-editor-writing-flow, .ast-plain-container .editor-styles-wrapper .block-editor-writing-flow'] = astra_get_responsive_background_obj( $box_bg_obj, 'tablet' );
			}

			$css .= astra_parse_css( $css_prod_button_tablet, '', astra_get_tablet_breakpoint() );

			/**
			 * Global button CSS - Mobile.
			 */
			$css_prod_button_mobile = array(
				'.wc-block-grid__products .wc-block-grid__product .wp-block-button__link' => array(
					'padding-top'    => astra_responsive_spacing( $theme_btn_padding, 'top', 'mobile' ),
					'padding-right'  => astra_responsive_spacing( $theme_btn_padding, 'right', 'mobile' ),
					'padding-bottom' => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'mobile' ),
					'padding-left'   => astra_responsive_spacing( $theme_btn_padding, 'left', 'mobile' ),
				),
			);

			if ( astra_wp_version_compare( '5.7', '>=' ) ) {
				$css_prod_button_mobile['.ast-page-builder-template .editor-styles-wrapper .block-editor-writing-flow, .ast-plain-container .editor-styles-wrapper .block-editor-writing-flow'] = astra_get_responsive_background_obj( $box_bg_obj, 'mobile' );
			}

			$css .= astra_parse_css( $css_prod_button_mobile, '', astra_get_mobile_breakpoint() );

			$theme_btn_top_border    = ( isset( $global_custom_button_border_size['top'] ) && '' !== $global_custom_button_border_size['top'] ) ? astra_get_css_value( $global_custom_button_border_size['top'], 'px' ) : '1px';
			$theme_btn_right_border  = ( isset( $global_custom_button_border_size['right'] ) && '' !== $global_custom_button_border_size['right'] ) ? astra_get_css_value( $global_custom_button_border_size['right'], 'px' ) : '1px';
			$theme_btn_left_border   = ( isset( $global_custom_button_border_size['left'] ) && '' !== $global_custom_button_border_size['left'] ) ? astra_get_css_value( $global_custom_button_border_size['left'], 'px' ) : '1px';
			$theme_btn_bottom_border = ( isset( $global_custom_button_border_size['bottom'] ) && '' !== $global_custom_button_border_size['bottom'] ) ? astra_get_css_value( $global_custom_button_border_size['bottom'], 'px' ) : '1px';

			if ( Astra_Dynamic_CSS::page_builder_button_style_css() ) {

				$is_support_wp_5_8            = Astra_Dynamic_CSS::is_block_editor_support_enabled();
				$search_button_selector       = $is_support_wp_5_8 ? ', .block-editor-writing-flow .wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' : '';
				$search_button_hover_selector = $is_support_wp_5_8 ? ', .block-editor-writing-flow .wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button:hover, .block-editor-writing-flow .wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button:focus' : '';

				$file_block_button_selector       = ( true === $improve_gb_ui ) ? ', .block-editor-writing-flow .wp-block-file .wp-block-file__button' : '';
				$file_block_button_hover_selector = ( true === $improve_gb_ui ) ? ', .block-editor-writing-flow .wp-block-file .wp-block-file__button:hover, .block-editor-writing-flow .wp-block-file .wp-block-file__button:focus' : '';

				$button_desktop_css = array(
					/**
					 * Gutenberg button compatibility for default styling.
					 */
					'.editor-styles-wrapper .wp-block-button .wp-block-button__link' . $search_button_selector . $file_block_button_selector => array(
						'border-style'               => 'solid',
						'border-top-width'           => $theme_btn_top_border,
						'border-right-width'         => $theme_btn_right_border,
						'border-left-width'          => $theme_btn_left_border,
						'border-bottom-width'        => $theme_btn_bottom_border,
						'color'                      => esc_attr( $btn_color ),
						'border-color'               => empty( $btn_border_color ) ? esc_attr( $btn_bg_color ) : esc_attr( $btn_border_color ),
						'background-color'           => esc_attr( $btn_bg_color ),
						'font-family'                => astra_get_font_family( $theme_btn_font_family ),
						'font-weight'                => esc_attr( $theme_btn_font_weight ),
						'line-height'                => esc_attr( $theme_btn_line_height ),
						'text-transform'             => esc_attr( $theme_btn_text_transform ),
						'letter-spacing'             => astra_get_css_value( $theme_btn_letter_spacing, 'px' ),
						'text-decoration'            => esc_attr( $theme_btn_text_decoration ),
						'font-size'                  => astra_responsive_font( $theme_btn_font_size, 'desktop' ),
						'border-top-left-radius'     => astra_responsive_spacing( $btn_border_radius_fields, 'top', 'desktop' ),
						'border-top-right-radius'    => astra_responsive_spacing( $btn_border_radius_fields, 'right', 'desktop' ),
						'border-bottom-right-radius' => astra_responsive_spacing( $btn_border_radius_fields, 'bottom', 'desktop' ),
						'border-bottom-left-radius'  => astra_responsive_spacing( $btn_border_radius_fields, 'left', 'desktop' ),
						'padding-top'                => astra_responsive_spacing( $theme_btn_padding, 'top', 'desktop' ),
						'padding-right'              => astra_responsive_spacing( $theme_btn_padding, 'right', 'desktop' ),
						'padding-bottom'             => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'desktop' ),
						'padding-left'               => astra_responsive_spacing( $theme_btn_padding, 'left', 'desktop' ),
					),
					'.wp-block-button .wp-block-button__link:hover, .wp-block-button .wp-block-button__link:focus' . $search_button_hover_selector . $file_block_button_hover_selector => array(
						'color'            => esc_attr( $btn_h_color ),
						'background-color' => esc_attr( $btn_bg_h_color ),
						'border-color'     => empty( $btn_border_h_color ) ? esc_attr( $btn_bg_h_color ) : esc_attr( $btn_border_h_color ),
					),
				);

				if ( true === $improve_gb_ui ) {
					$button_desktop_css['.block-editor-writing-flow .wp-block-file__content-wrapper'] = array(
						'display'         => 'flex',
						'align-items'     => 'center',
						'flex-wrap'       => 'wrap',
						'justify-content' => 'space-between',
					);
				}

				if ( $is_support_wp_5_8 ) {
					$button_desktop_css['.wp-block-search .wp-block-search__input, .wp-block-search.wp-block-search__button-inside .wp-block-search__inside-wrapper'] = array(
						'border-color' => '#eaeaea',
						'background'   => '#fafafa',
					);
					$button_desktop_css['.block-editor-writing-flow .wp-block-search .wp-block-search__inside-wrapper .wp-block-search__input']                       = array(
						'padding' => '15px',
					);
					$button_desktop_css['.wp-block-search__button svg'] = array(
						'fill' => 'currentColor',
					);
				}

				$css .= astra_parse_css( $button_desktop_css );

				/**
				 * Global button CSS - Tablet.
				 */
				$css_global_button_tablet = array(
					'.wp-block-button .wp-block-button__link' => array(
						'padding-top'    => astra_responsive_spacing( $theme_btn_padding, 'top', 'tablet' ),
						'padding-right'  => astra_responsive_spacing( $theme_btn_padding, 'right', 'tablet' ),
						'padding-bottom' => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'tablet' ),
						'padding-left'   => astra_responsive_spacing( $theme_btn_padding, 'left', 'tablet' ),
					),
				);

				$css .= astra_parse_css( $css_global_button_tablet, '', astra_get_tablet_breakpoint() );

				/**
				 * Global button CSS - Mobile.
				 */
				$css_global_button_mobile = array(
					'.wp-block-button .wp-block-button__link' => array(
						'padding-top'    => astra_responsive_spacing( $theme_btn_padding, 'top', 'mobile' ),
						'padding-right'  => astra_responsive_spacing( $theme_btn_padding, 'right', 'mobile' ),
						'padding-bottom' => astra_responsive_spacing( $theme_btn_padding, 'bottom', 'mobile' ),
						'padding-left'   => astra_responsive_spacing( $theme_btn_padding, 'left', 'mobile' ),
					),
				);

				$css .= astra_parse_css( $css_global_button_mobile, '', astra_get_mobile_breakpoint() );
			}

			if ( Astra_Dynamic_CSS::gutenberg_core_patterns_compat() ) {

				$link_hover_color     = astra_get_option( 'link-h-color' );
				$btn_text_hover_color = astra_get_option( 'button-h-color' );
				if ( empty( $btn_text_hover_color ) ) {
					$btn_text_hover_color = astra_get_foreground_color( $link_hover_color );
				}

				/**
				 * When supporting GB button outline patterns in v3.3.0 we have given 2px as default border for GB outline button, where we restrict button border for flat type buttons.
				 * But now while reverting this change there is no need of default border because whatever customizer border will set it should behave accordingly. Although it is empty ('') WP applying 2px as default border for outline buttons.
				 *
				 * @since 3.6.3
				 */
				$default_border_size = '2px';
				if ( ! astra_button_default_padding_updated() ) {
					$default_border_size = '';
				}

				// Outline Gutenberg button compatibility CSS.
				$theme_btn_top_border    = ( isset( $global_custom_button_border_size['top'] ) && ( '' !== $global_custom_button_border_size['top'] && '0' !== $global_custom_button_border_size['top'] ) ) ? astra_get_css_value( $global_custom_button_border_size['top'], 'px' ) : $default_border_size;
				$theme_btn_right_border  = ( isset( $global_custom_button_border_size['right'] ) && ( '' !== $global_custom_button_border_size['right'] && '0' !== $global_custom_button_border_size['right'] ) ) ? astra_get_css_value( $global_custom_button_border_size['right'], 'px' ) : $default_border_size;
				$theme_btn_left_border   = ( isset( $global_custom_button_border_size['left'] ) && ( '' !== $global_custom_button_border_size['left'] && '0' !== $global_custom_button_border_size['left'] ) ) ? astra_get_css_value( $global_custom_button_border_size['left'], 'px' ) : $default_border_size;
				$theme_btn_bottom_border = ( isset( $global_custom_button_border_size['bottom'] ) && ( '' !== $global_custom_button_border_size['bottom'] && '0' !== $global_custom_button_border_size['bottom'] ) ) ? astra_get_css_value( $global_custom_button_border_size['bottom'], 'px' ) : $default_border_size;

				// Added CSS compatibility support for Gutenberg pattern.
				$button_patterns_compat_css = array(
					'.wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color), .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color)' => array(
						'color' => empty( $btn_border_color ) ? esc_attr( $btn_bg_color ) : esc_attr( $btn_border_color ),
					),
					'.wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-button.is-style-outline .wp-block-button__link:focus' => array(
						'color' => esc_attr( $btn_text_hover_color ) . ' !important',
					),
					'.wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-button .wp-block-button__link:focus' => array(
						'border-color' => empty( $btn_border_h_color ) ? esc_attr( $btn_bg_h_color ) : esc_attr( $btn_border_h_color ),
					),
				);

				if ( ! astra_button_default_padding_updated() ) {
					$button_patterns_compat_css['.wp-block-button .wp-block-button__link']                  = array(
						'border'  => 'none',
						'padding' => '15px 30px',
					);
					$button_patterns_compat_css['.wp-block-button.is-style-outline .wp-block-button__link'] = array(
						'border-style'        => 'solid',
						'border-top-width'    => esc_attr( $theme_btn_top_border ),
						'border-right-width'  => esc_attr( $theme_btn_right_border ),
						'border-bottom-width' => esc_attr( $theme_btn_bottom_border ),
						'border-left-width'   => esc_attr( $theme_btn_left_border ),
						'border-color'        => empty( $btn_border_color ) ? esc_attr( $btn_bg_color ) : esc_attr( $btn_border_color ),
						'padding-top'         => 'calc(15px - ' . (int) $theme_btn_top_border . 'px)',
						'padding-right'       => 'calc(30px - ' . (int) $theme_btn_right_border . 'px)',
						'padding-bottom'      => 'calc(15px - ' . (int) $theme_btn_bottom_border . 'px)',
						'padding-left'        => 'calc(30px - ' . (int) $theme_btn_left_border . 'px)',
					);
				}

				$css .= astra_parse_css( $button_patterns_compat_css );

				if ( ! astra_button_default_padding_updated() ) {
					// Tablet CSS.
					$button_patterns_tablet_compat_css = array(
						'.wp-block-button .wp-block-button__link' => array(
							'border'  => 'none',
							'padding' => '15px 30px',
						),
						'.wp-block-button.is-style-outline .wp-block-button__link' => array(
							'padding-top'    => 'calc(15px - ' . (int) $theme_btn_top_border . 'px)',
							'padding-right'  => 'calc(30px - ' . (int) $theme_btn_right_border . 'px)',
							'padding-bottom' => 'calc(15px - ' . (int) $theme_btn_bottom_border . 'px)',
							'padding-left'   => 'calc(30px - ' . (int) $theme_btn_left_border . 'px)',
						),
					);

					$css .= astra_parse_css( $button_patterns_tablet_compat_css, '', astra_get_tablet_breakpoint() );

					// Mobile CSS.
					$button_patterns_mobile_compat_css = array(
						'.wp-block-button .wp-block-button__link' => array(
							'border'  => 'none',
							'padding' => '15px 30px',
						),
						'.wp-block-button.is-style-outline .wp-block-button__link' => array(
							'padding-top'    => 'calc(15px - ' . (int) $theme_btn_top_border . 'px)',
							'padding-right'  => 'calc(30px - ' . (int) $theme_btn_right_border . 'px)',
							'padding-bottom' => 'calc(15px - ' . (int) $theme_btn_bottom_border . 'px)',
							'padding-left'   => 'calc(30px - ' . (int) $theme_btn_left_border . 'px)',
						),
					);

					$css .= astra_parse_css( $button_patterns_mobile_compat_css, '', astra_get_mobile_breakpoint() );
				}

				if ( $is_site_rtl ) {
					$gb_patterns_min_mobile_css = array(
						'.editor-styles-wrapper .alignleft' => array(
							'margin-left' => '20px',
						),
						'.editor-styles-wrapper .alignright' => array(
							'margin-right' => '20px',
						),
					);
				} else {
					$gb_patterns_min_mobile_css = array(
						'.editor-styles-wrapper .alignleft'  => array(
							'margin-right' => '20px',
						),
						'.editor-styles-wrapper .alignright' => array(
							'margin-left' => '20px',
						),
					);
				}

				if ( ! astra_button_default_padding_updated() ) {
					$gb_patterns_min_mobile_css['.editor-styles-wrapper p.has-background'] = array(
						'padding' => '20px',
					);
				}

				/* Parse CSS from array() -> min-width: (mobile-breakpoint) px CSS  */
				$css .= astra_parse_css( $gb_patterns_min_mobile_css );
			}

			if ( Astra_Dynamic_CSS::gutenberg_core_blocks_css_comp() ) {

				$desktop_screen_gb_css = array(
					'.wp-block-columns'                  => array(
						'margin-bottom' => 'unset',
					),
					'figure.size-full'                   => array(
						'margin' => '2rem 0',
					),
					'.wp-block-gallery'                  => array(
						'margin-bottom' => '1.6em',
					),
					'.wp-block-group'                    => array(
						'padding-top'    => '4em',
						'padding-bottom' => '4em',
					),
					'.wp-block-group__inner-container:last-child, .wp-block-table table' => array(
						'margin-bottom' => '0',
					),
					'.blocks-gallery-grid'               => array(
						'width' => '100%',
					),
					'.wp-block-navigation-link__content' => array(
						'padding' => '5px 0',
					),
					'.wp-block-group .wp-block-group .has-text-align-center, .wp-block-group .wp-block-column .has-text-align-center' => array(
						'max-width' => '100%',
					),
					'.has-text-align-center'             => array(
						'margin' => '0 auto',
					),
				);

				/* Parse CSS from array() -> Desktop CSS */
				$css .= astra_parse_css( $desktop_screen_gb_css );

				if ( false === $improve_gb_ui ) {
					$middle_screen_min_gb_css = array(
						'.wp-block-cover__inner-container, .alignwide .wp-block-group__inner-container, .alignfull .wp-block-group__inner-container' => array(
							'max-width' => '1200px',
							'margin'    => '0 auto',
						),
						'.wp-block-group.alignnone, .wp-block-group.aligncenter, .wp-block-group.alignleft, .wp-block-group.alignright, .wp-block-group.alignwide, .wp-block-columns.alignwide' => array(
							'margin' => '2rem 0 1rem 0',
						),
					);

					/* Parse CSS from array() -> min-width: (1200)px CSS */
					$css .= astra_parse_css( $middle_screen_min_gb_css, '1200' );
				}

				$middle_screen_max_gb_css = array(
					'.wp-block-group'                     => array(
						'padding' => '3em',
					),
					'.wp-block-group .wp-block-group'     => array(
						'padding' => '1.5em',
					),
					'.wp-block-columns, .wp-block-column' => array(
						'margin' => '1rem 0',
					),
				);

				/* Parse CSS from array() -> max-width: (1200)px CSS */
				$css .= astra_parse_css( $middle_screen_max_gb_css, '', '1200' );

				$tablet_screen_min_gb_css = array(
					'.wp-block-columns .wp-block-group' => array(
						'padding' => '2em',
					),
				);

				/* Parse CSS from array() -> min-width: (tablet-breakpoint)px CSS */
				$css .= astra_parse_css( $tablet_screen_min_gb_css, astra_get_tablet_breakpoint() );

				$mobile_screen_max_gb_css = array(
					'.wp-block-media-text .wp-block-media-text__content' => array(
						'padding' => '3em 2em',
					),
					'.wp-block-cover-image .wp-block-cover__inner-container, .wp-block-cover .wp-block-cover__inner-container' => array(
						'width' => 'unset',
					),
					'.wp-block-cover, .wp-block-cover-image' => array(
						'padding' => '2em 0',
					),
					'.wp-block-group, .wp-block-cover' => array(
						'padding' => '2em',
					),
					'.wp-block-media-text__media img, .wp-block-media-text__media video' => array(
						'width'     => 'unset',
						'max-width' => '100%',
					),
					'.wp-block-media-text.has-background .wp-block-media-text__content' => array(
						'padding' => '1em',
					),
				);

				/* Parse CSS from array() -> max-width: (mobile-breakpoint)px CSS */
				$css .= astra_parse_css( $mobile_screen_max_gb_css, '', astra_get_mobile_breakpoint() );
			}

			if ( Astra_Dynamic_CSS::gutenberg_core_patterns_compat() ) {

				// Added CSS compatibility support for Gutenberg Editor's Media & Text block pattern.
				if ( $is_site_rtl ) {
					$gb_editor_block_pattern_css = array(
						'.wp-block-media-text .wp-block-media-text__content .wp-block-group__inner-container' => array(
							'padding' => '0 8% 0 0',
						),
						'.ast-separate-container .block-editor-block-list__layout .wp-block[data-align="full"] .wp-block[data-align="center"] > .wp-block-image' => array(
							'margin-right' => 'auto',
							'margin-left'  => 'auto',
						),
					);
				} else {
					$gb_editor_block_pattern_css = array(
						'.wp-block-media-text .wp-block-media-text__content .wp-block-group__inner-container' => array(
							'padding' => '0 0 0 8%',
						),
						'.ast-separate-container .block-editor-block-list__layout .wp-block[data-align="full"] .wp-block[data-align="center"] > .wp-block-image' => array(
							'margin-right' => 'auto',
							'margin-left'  => 'auto',
						),
					);
				}

				if ( false === $improve_gb_ui ) {
					$gb_editor_block_pattern_css['.block-editor-block-list__layout * .block-editor-block-list__block'] = array(
						'padding-left'  => '20px',
						'padding-right' => '20px',
					);
				}

				$css .= astra_parse_css( $gb_editor_block_pattern_css );
			}

			/**
			 * Updating core block layouts in editor as well.
			 *
			 * @since 3.7.4
			 */
			if ( true === $improve_gb_ui ) {

				$gb_editor_core_blocks_ui_css = array(
					'.editor-styles-wrapper' => array(
						'padding' => '0',
					),
					'.block-editor-block-list__layout > .wp-block-cover, .block-editor-block-list__layout > .wp-block-group, .block-editor-block-list__layout > .wp-block-columns, .block-editor-block-list__layout > .wp-block-media-text' => array(
						'max-width' => '910px',
						'margin'    => '0 auto',
					),
					'.ast-page-builder-template .block-editor-block-list__layout.is-root-container > .wp-block:not(.wp-block-cover):not(.wp-block-group):not(.wp-block-columns):not(.wp-block-media-text):not([data-align=wide]):not([data-align=full]):not([data-align=left]):not([data-align=right]), .editor-styles-wrapper .wp-block.editor-post-title__block' => array(
						'max-width' => '100%',
					),
					'.edit-post-visual-editor .editor-post-title__input, p.wp-block-paragraph, .wp-block[data-align="wide"]' => array(
						'max-width' => astra_get_css_value( $site_content_width - 56, 'px' ),
					),
					'.block-editor-block-list__layout > .wp-block-group, .block-editor-block-list__layout > .wp-block-cover, .wp-block[data-align=center] .wp-block-cover, .wp-block[data-align=center] .wp-block-group, .edit-post-visual-editor .editor-post-title__input' => array(
						'margin' => '0 auto',
					),
					'.block-editor-block-list__layout.is-root-container > .wp-block' => array(
						'margin' => '28px auto',
					),
					'.editor-styles-wrapper .block-editor-block-list__layout .wp-block [class*="__inner-container"] > .wp-block:not([data-align=wide]):not([data-align=full]):not([data-align=left]):not([data-align=right]):not(p.wp-block-paragraph)' => array(
						'max-width' => '50rem',
						'width'     => '100%',
					),
					'.block-editor-block-list__layout .wp-block-group, .block-editor-block-list__layout .wp-block-cover, .block-editor-block-list__layout .wp-block-columns.has-background' => array(
						'padding' => '4em',
					),
					'.block-editor-block-list__layout.is-root-container > .wp-block[data-align="wide"] > .wp-block-group, .block-editor-block-list__layout.is-root-container > .wp-block[data-align="full"] > .wp-block-group, .block-editor-block-list__layout.is-root-container > .wp-block[data-align="wide"] > .wp-block-cover, .block-editor-block-list__layout.is-root-container > .wp-block[data-align="full"] > .wp-block-cover, .block-editor-block-list__layout.is-root-container > .wp-block[data-align="wide"] > .wp-block-columns, .block-editor-block-list__layout.is-root-container > .wp-block[data-align="full"] > .wp-block-columns' => array(
						'padding' => '6em 4em',
					),
					'.editor-styles-wrapper .block-editor-block-list__layout.is-root-container > .wp-block[data-align=full]' => array(
						'margin-left'  => '0',
						'margin-right' => '0',
					),
					'.ast-separate-container .editor-styles-wrapper .block-editor-block-list__layout.is-root-container > .wp-block[data-align=full]' => array(
						'margin-left'  => '-20px',
						'margin-right' => '-20px',
					),
					'.editor-styles-wrapper .block-editor-default-block-appender__content' => array(
						'margin-bottom' => '0',
					),
					'.wp-block .wp-block:not(.wp-block-paragraph)' => array(
						'margin-top'    => '0',
						'margin-bottom' => '0',
					),
					'.block-editor-block-list__layout > .wp-block[data-align="wide"] .wp-block-group:not(.has-background), .block-editor-block-list__layout > .wp-block[data-align="full"] .wp-block-group:not(.has-background), .wp-block-group:not(.has-background)' => array(
						'padding' => '2em',
					),
					'.wp-block[data-align="left"] figure figcaption, .wp-block[data-align="right"] figure figcaption, .wp-block[data-align="center"] figure figcaption' => array(
						'padding-left'  => '20px',
						'padding-right' => '20px',
					),
					'.wp-block[data-align="right"] figure figcaption' => array(
						'text-align' => 'right',
					),
					'.wp-block-cover .wp-block-cover__inner-container .wp-block-heading' => array(
						'color' => '#ffffff',
					),
				);

				$css .= astra_parse_css( $gb_editor_core_blocks_ui_css );
			} else {

				$block_editor_padding_css = '.edit-post-visual-editor .block-editor-block-list__layout .block-editor-block-list__layout {
					padding: 0;
				}
				.editor-post-title__block {
					max-width: 1256px;
				}';

				$css .= Astra_Enqueue_Scripts::trim_css( $block_editor_padding_css );
			}

			$tablet_css = array(
				// Heading H1 - H6 font size.
				'.edit-post-visual-editor h1, .wp-block-heading h1, .wp-block-freeform.block-library-rich-text__tinymce h1, .edit-post-visual-editor .wp-block-heading h1, .wp-block-heading h1.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h1' => array(
					'font-size' => astra_responsive_font( $heading_h1_font_size, 'tablet', 30 ),
				),
				'.edit-post-visual-editor h2, .wp-block-heading h2, .wp-block-freeform.block-library-rich-text__tinymce h2, .edit-post-visual-editor .wp-block-heading h2, .wp-block-heading h2.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h2' => array(
					'font-size' => astra_responsive_font( $heading_h2_font_size, 'tablet', 25 ),
				),
				'.edit-post-visual-editor h3, .wp-block-heading h3, .wp-block-freeform.block-library-rich-text__tinymce h3, .edit-post-visual-editor .wp-block-heading h3, .wp-block-heading h3.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h3' => array(
					'font-size' => astra_responsive_font( $heading_h3_font_size, 'tablet', 20 ),
				),
				'.edit-post-visual-editor h4, .wp-block-heading h4, .wp-block-freeform.block-library-rich-text__tinymce h4, .edit-post-visual-editor .wp-block-heading h4, .wp-block-heading h4.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h4' => array(
					'font-size' => astra_responsive_font( $heading_h4_font_size, 'tablet' ),
				),
				'.edit-post-visual-editor h5, .wp-block-heading h5, .wp-block-freeform.block-library-rich-text__tinymce h5, .edit-post-visual-editor .wp-block-heading h5, .wp-block-heading h5.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h5' => array(
					'font-size' => astra_responsive_font( $heading_h5_font_size, 'tablet' ),
				),
				'.edit-post-visual-editor h6, .wp-block-heading h6, .wp-block-freeform.block-library-rich-text__tinymce h6, .edit-post-visual-editor .wp-block-heading h6, .wp-block-heading h6.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h6' => array(
					'font-size' => astra_responsive_font( $heading_h6_font_size, 'tablet' ),
				),
				'.ast-separate-container .edit-post-visual-editor, .ast-page-builder-template .edit-post-visual-editor, .ast-plain-container .edit-post-visual-editor, .ast-separate-container #wpwrap #editor .edit-post-visual-editor' => astra_get_responsive_background_obj( $box_bg_obj, 'tablet' ),
			);

			$css .= astra_parse_css( $tablet_css, '', astra_get_tablet_breakpoint() );

			$mobile_css = array(
				// Heading H1 - H6 font size.
				'.edit-post-visual-editor h1, .wp-block-heading h1, .wp-block-freeform.block-library-rich-text__tinymce h1, .edit-post-visual-editor .wp-block-heading h1, .wp-block-heading h1.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h1' => array(
					'font-size' => astra_responsive_font( $heading_h1_font_size, 'mobile', 30 ),
				),
				'.edit-post-visual-editor h2, .wp-block-heading h2, .wp-block-freeform.block-library-rich-text__tinymce h2, .edit-post-visual-editor .wp-block-heading h2, .wp-block-heading h2.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h2' => array(
					'font-size' => astra_responsive_font( $heading_h2_font_size, 'mobile', 25 ),
				),
				'.edit-post-visual-editor h3, .wp-block-heading h3, .wp-block-freeform.block-library-rich-text__tinymce h3, .edit-post-visual-editor .wp-block-heading h3, .wp-block-heading h3.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h3' => array(
					'font-size' => astra_responsive_font( $heading_h3_font_size, 'mobile', 20 ),
				),
				'.edit-post-visual-editor h4, .wp-block-heading h4, .wp-block-freeform.block-library-rich-text__tinymce h4, .edit-post-visual-editor .wp-block-heading h4, .wp-block-heading h4.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h4' => array(
					'font-size' => astra_responsive_font( $heading_h4_font_size, 'mobile' ),
				),
				'.edit-post-visual-editor h5, .wp-block-heading h5, .wp-block-freeform.block-library-rich-text__tinymce h5, .edit-post-visual-editor .wp-block-heading h5, .wp-block-heading h5.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h5' => array(
					'font-size' => astra_responsive_font( $heading_h5_font_size, 'mobile' ),
				),
				'.edit-post-visual-editor h6, .wp-block-heading h6, .wp-block-freeform.block-library-rich-text__tinymce h6, .edit-post-visual-editor .wp-block-heading h6, .wp-block-heading h6.editor-rich-text__tinymce, .editor-styles-wrapper .wp-block-uagb-advanced-heading h6' => array(
					'font-size' => astra_responsive_font( $heading_h6_font_size, 'mobile' ),
				),
				'.ast-separate-container .edit-post-visual-editor, .ast-page-builder-template .edit-post-visual-editor, .ast-plain-container .edit-post-visual-editor, .ast-separate-container #wpwrap #editor .edit-post-visual-editor' => astra_get_responsive_background_obj( $box_bg_obj, 'mobile' ),
			);

			$css .= astra_parse_css( $mobile_css, '', astra_get_mobile_breakpoint() );

			if ( is_callable( 'Astra_Woocommerce::astra_global_btn_woo_comp' ) && Astra_Woocommerce::astra_global_btn_woo_comp() ) {

				$woo_global_button_css = array(
					'.editor-styles-wrapper .wc-block-grid__products .wc-block-grid__product .wp-block-button__link' => array(
						'border-top-width'    => ( isset( $global_custom_button_border_size['top'] ) && '' !== $global_custom_button_border_size['top'] ) ? astra_get_css_value( $global_custom_button_border_size['top'], 'px' ) : '0',
						'border-right-width'  => ( isset( $global_custom_button_border_size['right'] ) && '' !== $global_custom_button_border_size['right'] ) ? astra_get_css_value( $global_custom_button_border_size['right'], 'px' ) : '0',
						'border-left-width'   => ( isset( $global_custom_button_border_size['left'] ) && '' !== $global_custom_button_border_size['left'] ) ? astra_get_css_value( $global_custom_button_border_size['left'], 'px' ) : '0',
						'border-bottom-width' => ( isset( $global_custom_button_border_size['bottom'] ) && '' !== $global_custom_button_border_size['bottom'] ) ? astra_get_css_value( $global_custom_button_border_size['bottom'], 'px' ) : '0',
						'border-color'        => $btn_border_color ? $btn_border_color : $btn_bg_color,
					),
					'.wc-block-grid__products .wc-block-grid__product .wp-block-button__link:hover' => array(
						'border-color' => $btn_bg_h_color,
					),
				);
				$css                  .= astra_parse_css( $woo_global_button_css );
			}

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				$page_builder_css = array(
					'.ast-page-builder-template .editor-post-title__block, .ast-page-builder-template .editor-default-block-appender' => array(
						'width'     => '100%',
						'max-width' => '100%',
					),
					'.ast-page-builder-template .wp-block[data-align="right"] > *' => array(
						'max-width' => 'unset',
						'width'     => 'unset',
					),
					'.ast-page-builder-template .block-editor-block-list__layout' => array(
						'padding-left'  => 0,
						'padding-right' => 0,
					),
					'.ast-page-builder-template .editor-block-list__block-edit'   => array(
						'padding-left'  => '20px',
						'padding-right' => '20px',
					),
					'.ast-page-builder-template .editor-block-list__block-edit .editor-block-list__block-edit' => array(
						'padding-left'  => '0',
						'padding-right' => '0',
					),
				);

			} else {

				$page_builder_css = array(
					'.ast-page-builder-template .editor-post-title__block, .ast-page-builder-template .editor-default-block-appender, .ast-page-builder-template .block-editor-block-list__block' => array(
						'width'     => '100%',
						'max-width' => '100%',
					),
					'.ast-page-builder-template .block-editor-block-list__layout' => array(
						'padding-left'  => 0,
						'padding-right' => 0,
					),
					'.ast-page-builder-template .editor-block-list__block-edit'   => array(
						'padding-left'  => '20px',
						'padding-right' => '20px',
					),
					'.ast-page-builder-template .editor-block-list__block-edit .editor-block-list__block-edit' => array(
						'padding-left'  => '0',
						'padding-right' => '0',
					),
				);
			}

			$css .= astra_parse_css( $page_builder_css );

			$aligned_full_content_css = array(
				'.ast-page-builder-template .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"] > .block-editor-block-list__block-edit, .ast-plain-container .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"] > .block-editor-block-list__block-edit' => array(
					'margin-left'  => '0',
					'margin-right' => '0',
				),
				'.ast-page-builder-template .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"], .ast-plain-container .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"]' => array(
					'margin-left'  => '0',
					'margin-right' => '0',
				),
			);

			$css .= astra_parse_css( $aligned_full_content_css );

			$boxed_container = array(
				'.ast-separate-container .block-editor-writing-flow'       => array(
					'max-width'        => astra_get_css_value( $site_content_width - 56, 'px' ),
					'margin'           => '0 auto',
					'background-color' => '#fff',
				),
				'.ast-separate-container .gutenberg__editor, .ast-two-container .gutenberg__editor' => array(
					'background-color' => '#f5f5f5',
				),

				'.ast-separate-container .block-editor-block-list__layout, .ast-two-container .editor-block-list__layout' => array(
					'padding-top' => '0',
				),

				'.ast-two-container .editor-post-title, .ast-separate-container .block-editor-block-list__layout, .ast-two-container .editor-post-title' => array(
					'padding-top'    => 'calc( 5.34em - 19px)',
					'padding-bottom' => '5.34em',
					'padding-left'   => 'calc( 6.67em - 28px )',
					'padding-right'  => 'calc( 6.67em - 28px )',
				),
				'.ast-separate-container .block-editor-block-list__layout' => array(
					'padding-top'    => '0',
					'padding-bottom' => '5.34em',
					'padding-left'   => 'calc( 6.67em - 28px )',
					'padding-right'  => 'calc( 6.67em - 28px )',
				),
				'.ast-separate-container .editor-post-title' => array(
					'padding-top'    => '62px',
					'padding-bottom' => '5.34em',
					'padding-left'   => '72px',
					'padding-right'  => '72px',
				),
				'.ast-separate-container .editor-post-title, .ast-two-container .editor-post-title' => array(
					'padding-bottom' => '0',
				),
				'.ast-separate-container .editor-block-list__block, .ast-two-container .editor-block-list__block' => array(
					'max-width' => 'calc(' . astra_get_css_value( $site_content_width, 'px' ) . ' - 6.67em)',
				),
				'.ast-separate-container .editor-block-list__block[data-align=wide], .ast-two-container .editor-block-list__block[data-align=wide]' => array(
					'margin-left'  => '-20px',
					'margin-right' => '-20px',
				),
				'.ast-separate-container .editor-block-list__block[data-align=full], .ast-two-container .editor-block-list__block[data-align=full]' => array(
					'margin-left'  => '-6.67em',
					'margin-right' => '-6.67em',
				),
				'.ast-separate-container .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"], .ast-separate-container .block-editor-block-list__layout .editor-block-list__block[data-align="full"] > .block-editor-block-list__block-edit, .ast-two-container .block-editor-block-list__layout .editor-block-list__block[data-align="full"], .ast-two-container .block-editor-block-list__layout .editor-block-list__block[data-align="full"] > .block-editor-block-list__block-edit' => array(
					'margin-left'  => '0',
					'margin-right' => '0',
				),
			);

			if ( true === $improve_gb_ui ) {
				$boxed_editor_content_area = '.ast-separate-container .block-editor-block-list__layout.is-root-container, .ast-max-width-layout.ast-plain-container .edit-post-visual-editor .block-editor-block-list__layout.is-root-container, .ast-separate-container .editor-post-title, .ast-two-container .editor-post-title';
				$boxed_container           = array();
			}

			$boxed_container_tablet = array();
			$boxed_container_mobile = array();

			if ( astra_has_gcp_typo_preset_compatibility() ) {

				$boxed_editor_content_area = '.ast-separate-container .block-editor-writing-flow, .ast-max-width-layout.ast-plain-container .edit-post-visual-editor .block-editor-writing-flow';
				$content_bg_obj            = astra_get_option( 'content-bg-obj-responsive' );
				$boxed_container_mobile    = array();
				$boxed_container_tablet    = array();

				$selector_for_content_background = ( true === $astra_apply_content_background ) ? '.edit-post-visual-editor .editor-styles-wrapper' : $boxed_editor_content_area;

				$boxed_container[ $selector_for_content_background ] = astra_get_responsive_background_obj( $content_bg_obj, 'desktop' );

				$boxed_container_tablet[ $selector_for_content_background ] = astra_get_responsive_background_obj( $content_bg_obj, 'tablet' );

				$boxed_container_mobile[ $selector_for_content_background ] = astra_get_responsive_background_obj( $content_bg_obj, 'mobile' );

				/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$css .= astra_parse_css( $boxed_container_tablet, '', astra_get_tablet_breakpoint() );
				/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$css .= astra_parse_css( $boxed_container_mobile, '', astra_get_mobile_breakpoint() );
			}

			$css .= astra_parse_css( $boxed_container );

			// Manage the extra padding applied in the block inster preview of blocks.
			$block_inserter_css = array(
				'.ast-separate-container .block-editor-inserter__preview .block-editor-block-list__layout' => array(
					'padding-top'    => '0px',
					'padding-bottom' => '0px',
					'padding-left'   => '0px',
					'padding-right'  => '0px',
				),
			);

			$css .= astra_parse_css( $block_inserter_css );

			// WP 5.5 compatibility fix the extra padding applied for the block patterns in the editor view.
			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {

				$block_pattern_css = array(
					'.ast-separate-container .block-editor-inserter__panel-content .block-editor-block-list__layout' => array(
						'padding-top'    => '0px',
						'padding-bottom' => '0px',
						'padding-left'   => '0px',
						'padding-right'  => '0px',

					),
					'.block-editor-inserter__panel-content .block-editor-block-list__layout' => array(
						'margin-left'  => '60px',
						'margin-right' => '60px',
					),
					'.block-editor-inserter__panel-content .block-editor-block-list__layout .block-editor-block-list__layout' => array(
						'margin-left'  => '0px',
						'margin-right' => '0px',
					),
					'.ast-page-builder-template .block-editor-inserter__panel-content .block-editor-block-list__layout' => array(
						'margin-left'  => '0px',
						'margin-right' => '0px',
					),
				);

				$css .= astra_parse_css( $block_pattern_css );
			} else {
				$full_width_streched_css = array(
					'.ast-page-builder-template .block-editor-block-list__layout' => array(
						'margin-left'  => '60px',
						'margin-right' => '60px',
					),
					'.ast-page-builder-template .block-editor-block-list__layout .block-editor-block-list__layout' => array(
						'margin-left'  => '0px',
						'margin-right' => '0px',
					),
				);

				$css .= astra_parse_css( $full_width_streched_css );
			}

			$ast_gtn_mobile_css = array(
				'.ast-separate-container .editor-post-title' => array(
					'padding-top'   => '19px',
					'padding-left'  => '28px',
					'padding-right' => '28px',
				),
				'.ast-separate-container .block-editor-block-list__layout' => array(
					'padding-bottom' => '2.34em',
					'padding-left'   => 'calc( 3.67em - 28px )',
					'padding-right'  => 'calc( 3.67em - 28px )',
				),
				'.ast-page-builder-template .block-editor-block-list__layout' => array(
					'margin-left'  => '30px',
					'margin-right' => '30px',
				),
				'.ast-plain-container .block-editor-block-list__layout' => array(
					'padding-left'  => '30px',
					'padding-right' => '30px',
				),
			);

			/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$css .= astra_parse_css( $ast_gtn_mobile_css, '', astra_get_mobile_breakpoint() );

			if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {
				$gtn_full_wide_image_css = array(
					'.wp-block[data-align="left"], .wp-block[data-align="right"]' => array(
						'max-width' => '100%',
						'width'     => '100%',
					),
				);

				if ( ! astra_wp_version_compare( '5.8', '>=' ) ) {
					$gtn_full_wide_image_css['.ast-separate-container .block-editor-block-list__layout .wp-block[data-align="full"] figure.wp-block-image'] = array(
						'margin-left'  => '-4.8em',
						'margin-right' => '-4.81em',
						'max-width'    => 'unset',
						'width'        => 'unset',
					);
				}

				if ( false === $improve_gb_ui ) {
					$gtn_full_wide_image_css['.ast-plain-container .wp-block[data-align="left"], .ast-plain-container .wp-block[data-align="right"], .ast-plain-container .wp-block[data-align="center"], .ast-plain-container .wp-block[data-align="full"]'] = array(
						'max-width' => astra_get_css_value( $site_content_width, 'px' ),
					);
					$gtn_full_wide_image_css['.wp-block[data-align="center"]']                                       = array(
						'max-width' => '100%',
						'width'     => '100%',
					);
					$gtn_full_wide_image_css['.ast-separate-container .wp-block[data-align="full"] .wp-block-cover'] = array(
						'margin-left'  => '-4.8em',
						'margin-right' => '-4.81em',
						'max-width'    => 'unset',
						'width'        => 'unset',
					);
					$gtn_full_wide_image_css['.ast-plain-container .wp-block[data-align="wide"]']                    = array(
						'max-width' => astra_get_css_value( $site_content_width - 56, 'px' ),
					);
					$gtn_full_wide_image_css['.ast-separate-container .editor-styles-wrapper .block-editor-block-list__layout.is-root-container > .wp-block[data-align="full"], .ast-plain-container .editor-styles-wrapper .block-editor-block-list__layout.is-root-container > .wp-block[data-align="full"]'] = array(
						'margin-left'  => 'auto',
						'margin-right' => 'auto',
					);
				}
			} else {
				$gtn_full_wide_image_css = array(
					'.ast-separate-container .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"] figure.wp-block-image' => array(
						'margin-left'  => '-4.8em',
						'margin-right' => '-4.81em',
						'max-width'    => 'unset',
						'width'        => 'unset',
					),
					'.ast-separate-container .block-editor-block-list__block[data-align="full"] .wp-block-cover' => array(
						'margin-left'  => '-4.8em',
						'margin-right' => '-4.81em',
						'max-width'    => 'unset',
						'width'        => 'unset',
					),
				);
			}

			$css .= astra_parse_css( $gtn_full_wide_image_css );

			if ( true === $improve_gb_ui ) {
				$compatibility_css = '
				.wp-block-cover__inner-container .wp-block {
					color: #ffffff;
				}
				.edit-post-visual-editor blockquote {
					padding: 0 1.2em 1.2em;
				}
				.editor-styles-wrapper .wp-block-pullquote blockquote::before {
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
				.editor-styles-wrapper .wp-block-pullquote.is-style-solid-color blockquote {
					max-width: 100%;
					text-align: inherit;
				}
				ul.wp-block-categories__list, ul.wp-block-archives-list {
					list-style-type: none;
				}';

				if ( $is_site_rtl ) {
					$compatibility_css .= '
					.edit-post-visual-editor ul, .edit-post-visual-editor ol {
						margin-right: 20px;
					}';
				} else {
					$compatibility_css .= '
					.edit-post-visual-editor ul, .edit-post-visual-editor ol {
						margin-left: 20px;
					}';
				}
			} else {
				$compatibility_css = '
					.edit-post-visual-editor blockquote {
						padding: 1.2em;
					}
				';
			}

			$css .= Astra_Enqueue_Scripts::trim_css( $compatibility_css );

			if ( false === $improve_gb_ui && in_array( $pagenow, array( 'post-new.php' ) ) && ! isset( $post ) ) {

				$boxed_container = array(
					'.block-editor-writing-flow'       => array(
						'max-width'        => astra_get_css_value( $site_content_width - 56, 'px' ),
						'margin'           => '0 auto',
						'background-color' => '#fff',
					),
					'.gutenberg__editor'               => array(
						'background-color' => '#f5f5f5',
					),
					'.block-editor-block-list__layout, .editor-post-title' => array(
						'padding-top'    => 'calc( 5.34em - 19px)',
						'padding-bottom' => '5.34em',
						'padding-left'   => 'calc( 6.67em - 28px )',
						'padding-right'  => 'calc( 6.67em - 28px )',
					),
					'.block-editor-block-list__layout' => array(
						'padding-top' => '0',
					),
					'.editor-post-title'               => array(
						'padding-bottom' => '0',
					),
					'.block-editor-block-list__block'  => array(
						'max-width' => 'calc(' . astra_get_css_value( $site_content_width, 'px' ) . ' - 6.67em)',
					),
					'.block-editor-block-list__block[data-align=wide]' => array(
						'margin-left'  => '-20px',
						'margin-right' => '-20px',
					),
					'.block-editor-block-list__block[data-align=full]' => array(
						'margin-left'  => '-6.67em',
						'margin-right' => '-6.67em',
					),
					'.block-editor-block-list__layout .block-editor-block-list__block[data-align="full"], .block-editor-block-list__layout .block-editor-block-list__block[data-align="full"] > .editor-block-list__block-edit' => array(
						'margin-left'  => '0',
						'margin-right' => '0',
					),
				);

				$css .= astra_parse_css( $boxed_container );

			}

			/* Narrow Width Container CSS */
			$narrow_container = array(
				// Visibility icon alignment.
				'.ast-narrow-container .edit-post-visual-editor__post-title-wrapper, .ast-stacked-title-visibility .edit-post-visual-editor__post-title-wrapper' => array(
					'max-width' => 'var(--wp--custom--ast-content-width-size)',
					'padding'   => 0,
				),
			);

			$css .= astra_parse_css( $narrow_container );

			return $css;
		}
	}

endif;
