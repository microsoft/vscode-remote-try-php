<?php
/**
 * Post Structures - Special Pages Dynamic CSS
 *
 * @package Astra
 * @since 4.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Structures
 */
add_filter( 'astra_dynamic_theme_css', 'astra_special_archive_dynamic_css' );

/**
 * Special Pages Dynamic CSS.
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
 * @return string Generated dynamic CSS for Post Structures.
 *
 * @since 4.6.0
 */
function astra_special_archive_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {
	// Adding condition for search page only, once we have more special pages, we can modify this condition.
	if ( ! is_search() ) {
		return $dynamic_css;
	}

	foreach ( Astra_Posts_Structure_Loader::get_special_page_types() as $index => $special_type ) {

		$title_section   = 'section-' . $special_type . '-page-title';
		$layout_type     = astra_get_option( $title_section . '-layout', 'layout-1' );
		$layout_2_active = ( 'layout-2' === $layout_type ) ? true : false;

		if ( $layout_2_active ) {
			$selector = '.search .ast-archive-entry-banner';
		} else {
			$selector = '.search .ast-archive-description';
		}

		$horizontal_alignment = astra_get_option( $title_section . '-horizontal-alignment' );
		$desk_h_alignment     = ( isset( $horizontal_alignment['desktop'] ) ) ? $horizontal_alignment['desktop'] : '';
		$tab_h_alignment      = ( isset( $horizontal_alignment['tablet'] ) ) ? $horizontal_alignment['tablet'] : '';
		$mob_h_alignment      = ( isset( $horizontal_alignment['mobile'] ) ) ? $horizontal_alignment['mobile'] : '';

		if ( 'layout-1' === $layout_type ) {
			$desk_h_alignment = ( '' !== $desk_h_alignment ) ? $desk_h_alignment : 'left';
			$tab_h_alignment  = ( '' !== $tab_h_alignment ) ? $tab_h_alignment : 'left';
			$mob_h_alignment  = ( '' !== $mob_h_alignment ) ? $mob_h_alignment : 'left';
		}

		$elements_gap   = astra_get_option( $title_section . '-elements-gap', 10 );
		$banner_padding = astra_get_option( $title_section . '-banner-padding', Astra_Posts_Structure_Loader::get_customizer_default( 'responsive-padding' ) );
		$banner_margin  = astra_get_option( $title_section . '-banner-margin' );

		$banner_height      = astra_get_option( $title_section . '-banner-height' );
		$desk_banner_height = ( $layout_2_active && isset( $banner_height['desktop'] ) ) ? astra_get_css_value( $banner_height['desktop'], 'px' ) : '';
		$tab_banner_height  = ( $layout_2_active && isset( $banner_height['tablet'] ) ) ? astra_get_css_value( $banner_height['tablet'], 'px' ) : '';
		$mob_banner_height  = ( $layout_2_active && isset( $banner_height['mobile'] ) ) ? astra_get_css_value( $banner_height['mobile'], 'px' ) : '';

		$text_color       = astra_get_option( $title_section . '-banner-text-color' );
		$title_color      = astra_get_option( $title_section . '-banner-title-color' );
		$link_color       = astra_get_option( $title_section . '-banner-link-color' );
		$link_hover_color = astra_get_option( $title_section . '-banner-link-hover-color' );

		$vert_alignment  = ( $layout_2_active ) ? astra_get_option( $title_section . '-vertical-alignment', 'center' ) : 'center';
		$width_type      = astra_get_option( $title_section . '-banner-width-type', 'fullwidth' );
		$custom_width    = astra_get_option( $title_section . '-banner-custom-width', 1200 );
		$background_type = astra_get_option( $title_section . '-banner-image-type', 'none' );

		// Banner Text typography dynamic stylings.
		$banner_text_font_size = astra_get_option( $title_section . '-text-font-size' );

		// Banner Title typography dynamic stylings.
		$banner_title_font_size = astra_get_option( $title_section . '-title-font-size', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-size' ) );

		$css_output_min_tablet  = array();
		$narrow_container_width = astra_get_option( 'narrow-container-max-width', apply_filters( 'astra_narrow_container_width', 750 ) );

		// Few settings from banner section are also applicable to 'layout-1' so adding this condition & compatibility.
		if ( 'layout-1' === $layout_type ) {
			$site_content_width = astra_get_option( 'site-content-width', 1200 );

			/**
			 * Desktop CSS.
			 */
			$css_output_desktop = array(
				$selector                                 => array(
					'max-width'      => $site_content_width . 'px',
					'width'          => '100%',
					'text-align'     => $desk_h_alignment,
					'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'desktop' ),
					'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'desktop' ),
					'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'desktop' ),
					'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'desktop' ),
					'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'desktop' ),
					'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'desktop' ),
					'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'desktop' ),
					'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'desktop' ),
				),
				$selector . ' *'                          => astra_get_font_array_css( astra_get_option( $title_section . '-text-font-family' ), astra_get_option( $title_section . '-text-font-weight' ), $banner_text_font_size, $title_section . '-text-font-extras', $text_color ),
				$selector . ' h1, ' . $selector . ' h1 *' => astra_get_font_array_css( astra_get_option( $title_section . '-title-font-family' ), astra_get_option( $title_section . '-title-font-weight', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-weight' ) ), $banner_title_font_size, $title_section . '-title-font-extras', $title_color ),
				$selector . ' a, ' . $selector . ' a *'   => array(
					'color' => esc_attr( $link_color ),
				),
				$selector . ' a:hover, ' . $selector . ' a:hover *' => array(
					'color' => esc_attr( $link_hover_color ),
				),
				$selector . ' > *:not(:last-child)'       => array(
					'margin-bottom' => $elements_gap . 'px',
				),
			);

			/**
			 * Tablet CSS.
			 */
			$css_output_tablet = array(
				$selector         => array(
					'text-align'     => $tab_h_alignment,
					'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'tablet' ),
					'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'tablet' ),
					'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'tablet' ),
					'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'tablet' ),
					'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'tablet' ),
					'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'tablet' ),
					'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'tablet' ),
					'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'tablet' ),
				),
				$selector . ' h1' => array(
					'font-size' => astra_responsive_font( $banner_title_font_size, 'tablet' ),
				),
				$selector . ' *'  => array(
					'font-size' => astra_responsive_font( $banner_text_font_size, 'tablet' ),
				),
			);

			/**
			 * Mobile CSS.
			 */
			$css_output_mobile = array(
				$selector         => array(
					'text-align'     => $mob_h_alignment,
					'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'mobile' ),
					'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'mobile' ),
					'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'mobile' ),
					'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'mobile' ),
					'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'mobile' ),
					'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'mobile' ),
					'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'mobile' ),
					'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'mobile' ),
				),
				$selector . ' h1' => array(
					'font-size' => astra_responsive_font( $banner_title_font_size, 'mobile' ),
				),
				$selector . ' *'  => array(
					'font-size' => astra_responsive_font( $banner_text_font_size, 'mobile' ),
				),
			);

			if ( 'none' !== $background_type ) {
				$custom_background = astra_get_option( $title_section . '-banner-custom-bg' );
				$css_output_desktop['.search section.ast-archive-description'] = astra_get_responsive_background_obj( $custom_background, 'desktop' );
				$css_output_tablet['.search section.ast-archive-description']  = astra_get_responsive_background_obj( $custom_background, 'tablet' );
				$css_output_mobile['.search section.ast-archive-description']  = astra_get_responsive_background_obj( $custom_background, 'mobile' );
			}
		} else {
			/**
			 * Desktop CSS.
			 */
			$css_output_desktop = array(
				$selector                        => array(
					'text-align'      => $desk_h_alignment,
					'justify-content' => $vert_alignment,
					'min-height'      => $desk_banner_height,
					'margin-top'      => astra_responsive_spacing( $banner_margin, 'top', 'desktop' ),
					'margin-bottom'   => astra_responsive_spacing( $banner_margin, 'bottom', 'desktop' ),
					'margin-left'     => astra_responsive_spacing( $banner_margin, 'left', 'desktop' ),
					'margin-right'    => astra_responsive_spacing( $banner_margin, 'right', 'desktop' ),
					'padding-top'     => astra_responsive_spacing( $banner_padding, 'top', 'desktop' ),
					'padding-right'   => astra_responsive_spacing( $banner_padding, 'right', 'desktop' ),
					'padding-bottom'  => astra_responsive_spacing( $banner_padding, 'bottom', 'desktop' ),
					'padding-left'    => astra_responsive_spacing( $banner_padding, 'left', 'desktop' ),
				),
				$selector . ' .ast-container'    => array(
					'width' => '100%',
				),
				$selector . ' .ast-container *'  => astra_get_font_array_css( astra_get_option( $title_section . '-text-font-family' ), astra_get_option( $title_section . '-text-font-weight' ), $banner_text_font_size, $title_section . '-text-font-extras', $text_color ),
				$selector . ' .ast-container h1, ' . $selector . ' .ast-container h1 *' => astra_get_font_array_css( astra_get_option( $title_section . '-title-font-family' ), astra_get_option( $title_section . '-title-font-weight', Astra_Posts_Structure_Loader::get_customizer_default( 'title-font-weight' ) ), $banner_title_font_size, $title_section . '-title-font-extras', $title_color ),
				$selector . ' .ast-container h1' => array(
					'margin-bottom' => '0',
				),
				'.ast-page-builder-template ' . $selector . ' .ast-container' => array(
					'max-width' => '100%',
				),
				'.ast-narrow-container ' . $selector . ' .ast-container' => array(
					'max-width' => $narrow_container_width . 'px',
				),
				$selector . ' .ast-container a, ' . $selector . ' .ast-container a *' => array(
					'color' => esc_attr( $link_color ),
				),
				$selector . ' .ast-container a:hover, ' . $selector . ' .ast-container a:hover *' => array(
					'color' => esc_attr( $link_hover_color ),
				),
				$selector . ' .ast-container > *:not(:last-child)' => array(
					'margin-bottom' => $elements_gap . 'px',
				),
			);

			/**
			 * Min tablet width CSS.
			 */
			$css_output_min_tablet = array(
				'.ast-narrow-container ' . $selector . ' .ast-container' => array(
					'max-width'     => $narrow_container_width . 'px',
					'padding-left'  => '0',
					'padding-right' => '0',
				),
			);

			/**
			 * Tablet CSS.
			 */
			$css_output_tablet = array(
				$selector                        => array(
					'text-align'     => $tab_h_alignment,
					'min-height'     => $tab_banner_height,
					'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'tablet' ),
					'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'tablet' ),
					'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'tablet' ),
					'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'tablet' ),
					'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'tablet' ),
					'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'tablet' ),
					'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'tablet' ),
					'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'tablet' ),
				),
				$selector . ' .ast-container'    => array(
					'padding-left'  => '0',
					'padding-right' => '0',
				),
				$selector . ' .ast-container h1' => array(
					'font-size' => astra_responsive_font( $banner_title_font_size, 'tablet' ),
				),
				$selector . ' *'                 => array(
					'font-size' => astra_responsive_font( $banner_text_font_size, 'tablet' ),
				),
			);

			/**
			 * Mobile CSS.
			 */
			$css_output_mobile = array(
				$selector                        => array(
					'text-align'     => $mob_h_alignment,
					'min-height'     => $mob_banner_height,
					'padding-top'    => astra_responsive_spacing( $banner_padding, 'top', 'mobile' ),
					'padding-right'  => astra_responsive_spacing( $banner_padding, 'right', 'mobile' ),
					'padding-bottom' => astra_responsive_spacing( $banner_padding, 'bottom', 'mobile' ),
					'padding-left'   => astra_responsive_spacing( $banner_padding, 'left', 'mobile' ),
					'margin-top'     => astra_responsive_spacing( $banner_margin, 'top', 'mobile' ),
					'margin-right'   => astra_responsive_spacing( $banner_margin, 'right', 'mobile' ),
					'margin-bottom'  => astra_responsive_spacing( $banner_margin, 'bottom', 'mobile' ),
					'margin-left'    => astra_responsive_spacing( $banner_margin, 'left', 'mobile' ),
				),
				$selector . ' .ast-container h1' => array(
					'font-size' => astra_responsive_font( $banner_title_font_size, 'mobile' ),
				),
				$selector . ' *'                 => array(
					'font-size' => astra_responsive_font( $banner_text_font_size, 'mobile' ),
				),
			);

			if ( ( 'custom' === $width_type ) ) {
				$css_output_desktop[ $selector . '[data-banner-width-type="custom"]' ]['max-width'] = $custom_width . 'px';
			}

			if ( 'custom' === $background_type ) {
				$custom_background = astra_get_option( $title_section . '-banner-custom-bg' );
				$css_output_desktop[ $selector . '[data-banner-background-type="custom"]' ] = astra_get_responsive_background_obj( $custom_background, 'desktop' );
				$css_output_tablet[ $selector . '[data-banner-background-type="custom"]' ]  = astra_get_responsive_background_obj( $custom_background, 'tablet' );
				$css_output_mobile[ $selector . '[data-banner-background-type="custom"]' ]  = astra_get_responsive_background_obj( $custom_background, 'mobile' );
			}
		}

		/* Parse CSS from array() */
		$dynamic_css .= astra_parse_css( $css_output_desktop );
		$dynamic_css .= astra_parse_css( $css_output_min_tablet, astra_get_tablet_breakpoint( '', 1 ) );
		$dynamic_css .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
		$dynamic_css .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );
	}

	$dynamic_css .= '
		.ast-archive-entry-banner {
			-js-display: flex;
			display: flex;
			flex-direction: column;
			justify-content: center;
			text-align: center;
			position: relative;
			background: #eeeeee;
		}
		.ast-archive-entry-banner[data-banner-width-type="custom"] {
			margin: 0 auto;
			width: 100%;
		}
		.ast-archive-entry-banner[data-banner-layout="layout-1"] {
			background: inherit;
			padding: 20px 0;
			text-align: left;
		}
	';

	if ( is_customize_preview() ) {
		$dynamic_css .= '
			.site-header-focus-item .ast-container div.customize-partial-edit-shortcut,
			.site-header-focus-item .ast-container button.item-customizer-focus {
				font-size: inherit;
			}
		';
	}

	return $dynamic_css;
}
