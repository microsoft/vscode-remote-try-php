<?php
/**
 * Related Posts - Dynamic CSS
 *
 * @package astra
 * @since 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_related_posts_css', 11 );

/**
 * Related Posts - Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @return String Generated dynamic CSS for Related Posts.
 *
 * @since 3.4.0
 */
function astra_related_posts_css( $dynamic_css ) {

	if ( astra_target_rules_for_related_posts() ) {

		$link_color                    = astra_get_option( 'link-color' );
		$related_posts_title_alignment = astra_get_option( 'releted-posts-title-alignment' );

		// Added RTL language support for title alignment.
		if ( is_rtl() && 'center' !== $related_posts_title_alignment ) {
			$related_posts_title_alignment = ( 'left' === $related_posts_title_alignment ) ? 'right' : 'left';
		}

		// Related Posts Grid layout params.
		$related_posts_grid = astra_get_option( 'related-posts-grid-responsive' );
		$desktop_grid       = ( isset( $related_posts_grid['desktop'] ) ) ? $related_posts_grid['desktop'] : '2-equal';
		$tablet_grid        = ( isset( $related_posts_grid['tablet'] ) ) ? $related_posts_grid['tablet'] : '2-equal';
		$mobile_grid        = ( isset( $related_posts_grid['mobile'] ) ) ? $related_posts_grid['mobile'] : 'full';

		// Related Posts -> Post Title typography dynamic stylings.
		$related_post_title_font_size = astra_get_option( 'related-posts-title-font-size' );

		// Related Posts -> Post Meta typography dynamic stylings.
		$related_post_meta_font_size = astra_get_option( 'related-posts-meta-font-size' );

		// Related Posts -> Content typography dynamic stylings.
		$related_post_content_font_size = astra_get_option( 'related-posts-content-font-size' );

		// Related Posts -> Section Title typography dynamic stylings.
		$related_posts_section_title_font_size = astra_get_option( 'related-posts-section-title-font-size' );

		// Setting up container BG color by default to Related Posts's section BG color.
		$content_bg_obj     = astra_get_option( 'content-bg-obj-responsive' );
		$container_bg_color = '#ffffff';
		if ( isset( $content_bg_obj['desktop']['background-color'] ) && '' !== $content_bg_obj['desktop']['background-color'] ) {
			$container_bg_color = $content_bg_obj['desktop']['background-color'];
		}

		// Related Posts -> Color dynamic stylings.
		$related_posts_title_color = astra_get_option( 'related-posts-title-color' );
		/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$related_posts_bg_color              = astra_get_option( 'related-posts-background-color', $container_bg_color );
		$related_post_text_color             = astra_get_option( 'related-posts-text-color' );
		$related_posts_meta_color            = astra_get_option( 'related-posts-meta-color' );
		$related_posts_link_color            = astra_get_option( 'related-posts-link-color' );
		$related_posts_link_hover_color      = astra_get_option( 'related-posts-link-hover-color' );
		$related_posts_meta_link_hover_color = astra_get_option( 'related-posts-meta-link-hover-color' );
		$author_avatar_size                  = astra_get_option( 'related-posts-author-avatar-size' );

		// Aspect Ratio processing.
		$aspect_ratio_type     = astra_get_option( 'related-posts-image-ratio-type', '' );
		$predefined_scale      = astra_get_option( 'related-posts-image-ratio-pre-scale' );
		$custom_scale_width    = astra_get_option( 'related-posts-image-custom-scale-width', 16 );
		$custom_scale_height   = astra_get_option( 'related-posts-image-custom-scale-height', 9 );
		$aspect_ratio          = astra_get_dynamic_image_aspect_ratio( $aspect_ratio_type, $predefined_scale, $custom_scale_width, $custom_scale_height );
		$with_aspect_img_width = 'predefined' === $aspect_ratio_type || 'custom' === $aspect_ratio_type ? '100%' : '';
		$object_fit            = 'custom' === $aspect_ratio_type ? 'cover' : '';

		$css_desktop_output = array(
			'.ast-single-related-posts-container .ast-related-posts-wrapper' => array(
				'grid-template-columns' => Astra_Builder_Helper::$grid_size_mapping[ $desktop_grid ],
			),
			'.ast-related-posts-inner-section .ast-date-meta .posted-on, .ast-related-posts-inner-section .ast-date-meta .posted-on *' => array(
				'background' => esc_attr( $link_color ),
				'color'      => astra_get_foreground_color( $link_color ),
			),
			'.ast-related-posts-inner-section .ast-date-meta .posted-on .date-month, .ast-related-posts-inner-section .ast-date-meta .posted-on .date-year' => array(
				'color' => astra_get_foreground_color( $link_color ),
			),
			'.ast-single-related-posts-container'          => array(
				'background-color' => esc_attr( $related_posts_bg_color ),
			),
			/**
			 * Related Posts - Section Title
			 */
			'.ast-related-posts-title'                     => astra_get_font_array_css( astra_get_option( 'related-posts-section-title-font-family' ), astra_get_option( 'related-posts-section-title-font-weight' ), $related_posts_section_title_font_size, 'related-posts-section-title-font-extras', $related_posts_title_color ),
			'.ast-related-posts-title-section .ast-related-posts-title' => array(
				'text-align' => esc_attr( $related_posts_title_alignment ),
			),
			/**
			 * Related Posts - Post Title
			 */
			'.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' => astra_get_font_array_css( astra_get_option( 'related-posts-title-font-family' ), astra_get_option( 'related-posts-title-font-weight' ), $related_post_title_font_size, 'related-posts-title-font-extras', $related_post_text_color ),

			/**
			 * Related Posts - Meta
			 */
			'.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' => astra_get_font_array_css( astra_get_option( 'related-posts-meta-font-family' ), astra_get_option( 'related-posts-meta-font-weight' ), $related_post_meta_font_size, 'related-posts-meta-font-extras', $related_posts_meta_color ),

			'.ast-related-post-content .entry-meta a:hover, .ast-related-post-content .entry-meta span a span:hover' => array(
				'color' => esc_attr( $related_posts_meta_link_hover_color ),
			),
			/**
			 * Related Posts - CTA
			 */
			'.ast-related-post-cta a'                      => array(
				'color' => esc_attr( $related_posts_link_color ),
			),
			'.ast-related-post-cta a:hover'                => array(
				'color' => esc_attr( $related_posts_link_hover_color ),
			),
			/**
			 * Related Posts - Content
			 */
			'.ast-related-post-excerpt'                    => astra_get_font_array_css( astra_get_option( 'related-posts-content-font-family' ), astra_get_option( 'related-posts-content-font-weight' ), $related_post_content_font_size, 'related-posts-content-font-extras', $related_post_text_color ),

			'.ast-related-post-content .post-thumb-img-content img' => array(
				'aspect-ratio' => $aspect_ratio,
				'width'        => $with_aspect_img_width,
				'object-fit'   => $object_fit, // setting it to 'cover' for custom ratio option same as blogs archive page.
			),
			'.ast-related-post-content .ast-author-avatar' => array(
				'--ast-author-avatar-size' => astra_get_css_value( $author_avatar_size, 'px' ),
			),
		);

		if ( astra_has_global_color_format_support() ) {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$related_posts_bg_color = astra_get_option( 'related-posts-background-color', $content_bg_obj );

			if ( is_array( $related_posts_bg_color ) ) {
				$css_desktop_output['.ast-single-related-posts-container'] = astra_get_responsive_background_obj( $related_posts_bg_color, 'desktop' );
			} else {
				$css_desktop_output['.ast-single-related-posts-container'] = array(
					'background-color' => esc_attr( $related_posts_bg_color ),
				);
			}
		}

		$btn_text_color                   = astra_get_option( 'button-color', '#fff' );
		$btn_border_color                 = astra_get_option( 'theme-button-border-group-border-color' );
		$btn_border_h_color               = astra_get_option( 'theme-button-border-group-border-h-color' );
		$theme_color                      = astra_get_option( 'theme-color' );
		$link_hover_color                 = astra_get_option( 'link-h-color' );
		$btn_bg_color                     = astra_get_option( 'button-bg-color', $theme_color );
		$btn_text_hover_color             = astra_get_option( 'button-h-color', '#fff' );
		$btn_bg_hover_color               = astra_get_option( 'button-bg-h-color', $link_hover_color );
		$btn_preset_style                 = astra_get_option( 'button-preset-style' );
		$global_custom_button_border_size = astra_get_option( 'theme-button-border-group-border-size' );

		if ( 'button_04' === $btn_preset_style || 'button_05' === $btn_preset_style || 'button_06' === $btn_preset_style ) {

			if ( empty( $btn_border_color ) ) {
				$btn_border_color = $btn_bg_color;
			}

			if ( '' === astra_get_option( 'button-bg-color' ) && '' === astra_get_option( 'button-color' ) ) {
				$btn_text_color = $theme_color;
			} elseif ( '' === astra_get_option( 'button-color' ) ) {
					$btn_text_color = $btn_bg_color;
			}

			$btn_bg_color = 'transparent';
		}
		$css_desktop_output['.ast-related-cat-style--badge .cat-links > a, .ast-related-tag-style--badge .tags-links > a']             = array(
			'border-style'        => 'solid',
			'border-top-width'    => ( isset( $global_custom_button_border_size['top'] ) && '' !== $global_custom_button_border_size['top'] ) ? astra_get_css_value( $global_custom_button_border_size['top'], 'px' ) : '0',
			'border-right-width'  => ( isset( $global_custom_button_border_size['right'] ) && '' !== $global_custom_button_border_size['right'] ) ? astra_get_css_value( $global_custom_button_border_size['right'], 'px' ) : '0',
			'border-left-width'   => ( isset( $global_custom_button_border_size['left'] ) && '' !== $global_custom_button_border_size['left'] ) ? astra_get_css_value( $global_custom_button_border_size['left'], 'px' ) : '0',
			'border-bottom-width' => ( isset( $global_custom_button_border_size['bottom'] ) && '' !== $global_custom_button_border_size['bottom'] ) ? astra_get_css_value( $global_custom_button_border_size['bottom'], 'px' ) : '0',
			'padding'             => '4px 8px',
			'border-radius'       => '3px',
			'font-size'           => 'inherit',
			'color'               => esc_attr( $btn_text_color ),
			'border-color'        => empty( $btn_border_color ) ? esc_attr( $btn_bg_color ) : esc_attr( $btn_border_color ),
			'background-color'    => esc_attr( $btn_bg_color ),
		);
		$css_desktop_output['.ast-related-cat-style--badge .cat-links > a:hover, .ast-related-tag-style--badge .tags-links > a:hover'] = array(
			'color'            => esc_attr( $btn_text_hover_color ),
			'background-color' => esc_attr( $btn_bg_hover_color ),
			'border-color'     => empty( $btn_border_h_color ) ? esc_attr( $btn_bg_hover_color ) : esc_attr( $btn_border_h_color ),
		);
		$css_desktop_output['.ast-related-cat-style--underline .cat-links > a, .ast-related-tag-style--underline .tags-links > a']     = array(
			'text-decoration' => 'underline',
		);

		$dynamic_css .= astra_parse_css( $css_desktop_output );

		$css_max_tablet_output = array(
			'.ast-single-related-posts-container .ast-related-posts-wrapper .ast-related-post' => array(
				'width' => '100%',
			),
			'.ast-single-related-posts-container .ast-related-posts-wrapper' => array(
				'grid-template-columns' => Astra_Builder_Helper::$grid_size_mapping[ $tablet_grid ],
			),
			'.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' => array(
				'font-size' => astra_responsive_font( $related_post_title_font_size, 'tablet' ),
			),
			'.ast-related-post-content .entry-meta *' => array(
				'font-size' => astra_responsive_font( $related_post_meta_font_size, 'tablet' ),
			),
			'.ast-related-post-excerpt'               => array(
				'font-size' => astra_responsive_font( $related_post_content_font_size, 'tablet' ),
			),
			'.ast-related-posts-title'                => array(
				'font-size' => astra_responsive_font( $related_posts_section_title_font_size, 'tablet' ),
			),
		);

		if ( astra_has_global_color_format_support() ) {
			if ( is_array( $related_posts_bg_color ) ) {
				$css_max_tablet_output['.ast-single-related-posts-container'] = astra_get_responsive_background_obj( $related_posts_bg_color, 'desktop' );
			} else {
				$css_max_tablet_output['.ast-single-related-posts-container'] = array(
					'background-color' => esc_attr( $related_posts_bg_color ),
				);
			}
		}

		$dynamic_css .= astra_parse_css( $css_max_tablet_output, '', astra_get_tablet_breakpoint() );

		$css_max_mobile_output = array(
			'.ast-single-related-posts-container .ast-related-posts-wrapper' => array(
				'grid-template-columns' => Astra_Builder_Helper::$grid_size_mapping[ $mobile_grid ],
			),
			'.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' => array(
				'font-size' => astra_responsive_font( $related_post_title_font_size, 'mobile' ),
			),
			'.ast-related-post-content .entry-meta *' => array(
				'font-size' => astra_responsive_font( $related_post_meta_font_size, 'mobile' ),
			),
			'.ast-related-post-excerpt'               => array(
				'font-size' => astra_responsive_font( $related_post_content_font_size, 'mobile' ),
			),
			'.ast-related-posts-title'                => array(
				'font-size' => astra_responsive_font( $related_posts_section_title_font_size, 'mobile' ),
			),
		);

		if ( astra_has_global_color_format_support() ) {
			if ( is_array( $related_posts_bg_color ) ) {
				$css_max_mobile_output['.ast-single-related-posts-container'] = astra_get_responsive_background_obj( $related_posts_bg_color, 'desktop' );
			} else {
				$css_max_mobile_output['.ast-single-related-posts-container'] = array(
					'background-color' => esc_attr( $related_posts_bg_color ),
				);
			}
		}

		$dynamic_css .= astra_parse_css( $css_max_mobile_output, '', astra_get_mobile_breakpoint() );

		$dynamic_css .= Astra_Extended_Base_Dynamic_CSS::prepare_inner_section_advanced_css( 'ast-sub-section-related-posts', '.site .ast-single-related-posts-container' );

		return $dynamic_css;
	}

	return $dynamic_css;
}
