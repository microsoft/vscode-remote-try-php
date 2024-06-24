<?php
/**
 * Astra Social Component Dynamic CSS.
 *
 * @package     astra-builder
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       3.0.0
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Builder Dynamic CSS.
 *
 * @since 3.0.0
 */
class Astra_Social_Component_Dynamic_CSS {

	/**
	 * Dynamic CSS
	 *
	 * @param string $builder_type Builder Type.
	 * @return String Generated dynamic CSS for Heading Colors.
	 *
	 * @since 3.0.0
	 */
	public static function astra_social_dynamic_css( $builder_type = 'header' ) {

		$generated_css   = '';
		$social_css_flag = false;

		$number_of_social_icons = ( 'header' === $builder_type ) ? Astra_Builder_Helper::$num_of_header_social_icons : Astra_Builder_Helper::$num_of_footer_social_icons;

		for ( $index = 1; $index <= $number_of_social_icons; $index++ ) {

			if ( ! Astra_Builder_Helper::is_component_loaded( 'social-icons-' . $index, $builder_type ) ) {
				continue;
			}
			$social_css_flag = true;
			$selector        = '.ast-' . $builder_type . '-social-' . $index . '-wrap';
			$_section        = ( 'header' === $builder_type ) ? 'section-hb-social-icons-' . $index : 'section-fb-social-icons-' . $index;

			$icon_spacing       = astra_get_option( $builder_type . '-social-' . $index . '-space' );
			$icon_bg_spacing    = astra_get_option( $builder_type . '-social-' . $index . '-bg-space' );
			$icon_size          = astra_get_option( $builder_type . '-social-' . $index . '-size' );
			$icon_radius_fields = astra_get_option( $builder_type . '-social-' . $index . '-radius-fields' );

			$icon_spacing_desktop = ( isset( $icon_spacing['desktop'] ) && '' !== $icon_spacing['desktop'] ) ? (int) $icon_spacing['desktop'] / 2 : '';
			$icon_spacing_tablet  = ( isset( $icon_spacing['tablet'] ) && '' !== $icon_spacing['tablet'] ) ? (int) $icon_spacing['tablet'] / 2 : '';
			$icon_spacing_mobile  = ( isset( $icon_spacing['mobile'] ) && '' !== $icon_spacing['mobile'] ) ? (int) $icon_spacing['mobile'] / 2 : '';

			$icon_size_desktop = ( isset( $icon_size['desktop'] ) && '' !== $icon_size['desktop'] ) ? (int) $icon_size['desktop'] : '';
			$icon_size_tablet  = ( isset( $icon_size['tablet'] ) && '' !== $icon_size['tablet'] ) ? (int) $icon_size['tablet'] : '';
			$icon_size_mobile  = ( isset( $icon_size['mobile'] ) && '' !== $icon_size['mobile'] ) ? (int) $icon_size['mobile'] : '';

			$icon_bg_spacing = ( isset( $icon_bg_spacing ) && '' !== $icon_bg_spacing ) ? (int) $icon_bg_spacing : '';

			// Normal Responsive Colors.
			$color_type                       = astra_get_option( $builder_type . '-social-' . $index . '-color-type' );
			$toggle_brand_hover               = astra_get_option( $builder_type . '-social-' . $index . '-brand-hover-toggle' );
			$social_icons_brand_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-color' ), 'desktop' );
			$social_icons_brand_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-color' ), 'tablet' );
			$social_icons_brand_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-color' ), 'mobile' );

			$social_label_brand_color_desktop      = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-label-color' ), 'desktop' );
			$social_label_icons_brand_color_tablet = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-label-color' ), 'tablet' );
			$social_label_icons_brand_color_mobile = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-brand-label-color' ), 'mobile' );

			$social_icons_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-color' ), 'desktop' );
			$social_icons_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-color' ), 'tablet' );
			$social_icons_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-color' ), 'mobile' );

			// Hover Responsive Colors.
			$social_icons_h_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-h-color' ), 'desktop' );
			$social_icons_h_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-h-color' ), 'tablet' );
			$social_icons_h_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-h-color' ), 'mobile' );

			// Normal Responsive Bg Colors.
			$social_icons_bg_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-color' ), 'desktop' );
			$social_icons_bg_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-color' ), 'tablet' );
			$social_icons_bg_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-color' ), 'mobile' );

			// Hover Responsive Bg Colors.
			$social_icons_h_bg_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-h-color' ), 'desktop' );
			$social_icons_h_bg_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-h-color' ), 'tablet' );
			$social_icons_h_bg_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-bg-h-color' ), 'mobile' );

			// Normal Responsive Label Colors.
			$social_icons_label_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-color' ), 'desktop' );
			$social_icons_label_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-color' ), 'tablet' );
			$social_icons_label_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-color' ), 'mobile' );

			// Hover Responsive Label Colors.
			$social_icons_label_h_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-h-color' ), 'desktop' );
			$social_icons_label_h_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-h-color' ), 'tablet' );
			$social_icons_label_h_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-social-' . $index . '-label-h-color' ), 'mobile' );


			$margin = astra_get_option( $_section . '-margin' );

			/**
			 * Social Icon CSS.
			 */
			$css_output_desktop = array(            
				$selector . ' .ast-builder-social-element,' . $selector . ' .social-show-label-true .ast-builder-social-element' => array(
					// Icon Spacing.
					'margin-left'                => astra_get_css_value( $icon_spacing_desktop, 'px' ),
					'margin-right'               => astra_get_css_value( $icon_spacing_desktop, 'px' ),

					// Icon Background Space.
					'padding'                    => astra_get_css_value( $icon_bg_spacing, 'px' ),

					// Icon Radius.
					'border-top-left-radius'     => astra_responsive_spacing( $icon_radius_fields, 'top', 'desktop' ),
					'border-top-right-radius'    => astra_responsive_spacing( $icon_radius_fields, 'right', 'desktop' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $icon_radius_fields, 'bottom', 'desktop' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $icon_radius_fields, 'left', 'desktop' ),
				),
				$selector . ' .ast-builder-social-element svg' => array(

					// Icon Size.
					'width'  => astra_get_css_value( $icon_size_desktop, 'px' ),
					'height' => astra_get_css_value( $icon_size_desktop, 'px' ),
				),
				$selector . ' .ast-social-icon-image-wrap' => array(

					// Icon Background Space.
					'margin' => astra_get_css_value( $icon_bg_spacing, 'px' ),
				),
				$selector                                  => array(
					// Margin CSS.
					'margin-top'    => astra_responsive_spacing( $margin, 'top', 'desktop' ),
					'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'desktop' ),
					'margin-left'   => astra_responsive_spacing( $margin, 'left', 'desktop' ),
					'margin-right'  => astra_responsive_spacing( $margin, 'right', 'desktop' ),
				),
			);

			if ( 'custom' === $color_type || is_customize_preview() ) {
				
				$css_output_desktop[ $selector . ' .ast-social-color-type-custom svg' ]['fill'] = $social_icons_color_desktop;
				$css_output_desktop[ $selector . ' .ast-builder-social-element' ]['background'] = $social_icons_bg_color_desktop;

				$css_output_desktop[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover' ] = array(
					// Hover.
					'color'      => $social_icons_h_color_desktop,
					'background' => $social_icons_h_bg_color_desktop,
				);

				$css_output_desktop[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover svg' ] = array(
					'fill' => $social_icons_h_color_desktop,
				);

				if ( isset( $social_icons_label_color_desktop ) && ! empty( $social_icons_label_color_desktop ) ) {
					$css_output_desktop[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_label_color_desktop;
				} else {
					$css_output_desktop[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_color_desktop;
				}
				if ( isset( $social_icons_label_h_color_desktop ) && ! empty( $social_icons_label_h_color_desktop ) ) {
					$css_output_desktop[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_label_h_color_desktop;
				} else {
					$css_output_desktop[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_h_color_desktop;
				}
			}

		
			if ( 'official' === $color_type && false === $toggle_brand_hover ) {
				$css_output_desktop['.ast-social-color-type-official .ast-builder-social-element, .ast-social-color-type-official .social-item-label'] = array(
					'color'            => 'var(--color)',
					'background-color' => 'var(--background-color)',
				);

				$css_output_desktop['.header-social-inner-wrap.ast-social-color-type-official .ast-builder-social-element svg, .footer-social-inner-wrap.ast-social-color-type-official .ast-builder-social-element svg'] = array(
					'fill' => 'currentColor',
				);
			}

			if ( 'official' === $color_type && true === $toggle_brand_hover ) {
				$css_output_desktop['.ast-social-color-type-official .ast-builder-social-element:hover, .ast-social-color-type-official .ast-builder-social-element:hover .social-item-label'] = array(
					'color'            => 'var(--color)',
					'background-color' => 'var(--background-color)',
				);

				$css_output_desktop['.header-social-inner-wrap.ast-social-color-type-official .ast-builder-social-element:hover svg, .footer-social-inner-wrap.ast-social-color-type-official .ast-builder-social-element:hover svg'] = array(
					'fill' => 'currentColor',
				);

				$css_output_desktop[ $selector . ' .ast-social-color-type-official svg' ]['fill'] = $social_icons_brand_color_desktop;

				if ( isset( $social_label_brand_color_desktop ) && ! empty( $social_label_brand_color_desktop ) ) {
					$css_output_desktop[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_label_brand_color_desktop;
				} else {
					$css_output_desktop[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_icons_brand_color_desktop;
				}           
			}

			/**
			 * Social_icons CSS.
			 */
			$css_output_tablet = array(
				$selector . ' .ast-builder-social-element svg' => array(

					// Icon Size.
					'width'  => astra_get_css_value( $icon_size_tablet, 'px' ),
					'height' => astra_get_css_value( $icon_size_tablet, 'px' ),
				),
				$selector . ' .ast-builder-social-element' => array(
					// Icon Spacing.
					'margin-left'                => astra_get_css_value( $icon_spacing_tablet, 'px' ),
					'margin-right'               => astra_get_css_value( $icon_spacing_tablet, 'px' ),

					// Border Radius.
					'border-top-left-radius'     => astra_responsive_spacing( $icon_radius_fields, 'top', 'tablet' ),
					'border-top-right-radius'    => astra_responsive_spacing( $icon_radius_fields, 'right', 'tablet' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $icon_radius_fields, 'bottom', 'tablet' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $icon_radius_fields, 'left', 'tablet' ),
				),
				$selector                                  => array(
					// Margin CSS.
					'margin-top'    => astra_responsive_spacing( $margin, 'top', 'tablet' ),
					'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'tablet' ),
					'margin-left'   => astra_responsive_spacing( $margin, 'left', 'tablet' ),
					'margin-right'  => astra_responsive_spacing( $margin, 'right', 'tablet' ),
				),
			);

			if ( 'custom' === $color_type || is_customize_preview() ) {
				$css_output_tablet[ $selector . ' .ast-social-color-type-custom svg' ]['fill'] = $social_icons_color_tablet;

				$css_output_tablet[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element' ]['background'] = $social_icons_bg_color_tablet;

				$css_output_tablet[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover' ]     = array(
					// Hover.
					'color'      => $social_icons_h_color_tablet,
					'background' => $social_icons_h_bg_color_tablet,
				);
				$css_output_tablet[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover svg' ] = array(
					'fill' => $social_icons_h_color_tablet,
				);

				if ( isset( $social_icons_label_color_tablet ) && ! empty( $social_icons_label_color_tablet ) ) {
					$css_output_tablet[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_label_color_tablet;
				} else {
					$css_output_tablet[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_color_tablet;
				}
				if ( isset( $social_icons_label_h_color_tablet ) && ! empty( $social_icons_label_h_color_tablet ) ) {
					$css_output_tablet[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_label_h_color_tablet;
				} else {
					$css_output_tablet[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_h_color_tablet;
				}
			}

			if ( 'official' === $color_type && true === $toggle_brand_hover ) {
				$css_output_tablet[ $selector . ' .ast-social-color-type-official svg' ]['fill'] = $social_icons_brand_color_tablet;

				if ( isset( $social_label_icons_brand_color_tablet ) && ! empty( $social_label_icons_brand_color_tablet ) ) {
					$css_output_tablet[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_icons_label_color_tablet;
				} else {
					$css_output_tablet[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_icons_brand_color_tablet;
				}
			}

		

			/**
			 * Social_icons CSS.
			 */
			$css_output_mobile = array(
				$selector . ' .ast-builder-social-element svg' => array(

					// Icon Size.
					'width'  => astra_get_css_value( $icon_size_mobile, 'px' ),
					'height' => astra_get_css_value( $icon_size_mobile, 'px' ),
				),
				$selector . ' .ast-builder-social-element' => array(
					// Icon Spacing.
					'margin-left'                => astra_get_css_value( $icon_spacing_mobile, 'px' ),
					'margin-right'               => astra_get_css_value( $icon_spacing_mobile, 'px' ),

					// Border Radius.
					'border-top-left-radius'     => astra_responsive_spacing( $icon_radius_fields, 'top', 'mobile' ),
					'border-top-right-radius'    => astra_responsive_spacing( $icon_radius_fields, 'right', 'mobile' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $icon_radius_fields, 'bottom', 'mobile' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $icon_radius_fields, 'left', 'mobile' ),
				),
				$selector                                  => array(
					// Margin CSS.
					'margin-top'    => astra_responsive_spacing( $margin, 'top', 'mobile' ),
					'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'mobile' ),
					'margin-left'   => astra_responsive_spacing( $margin, 'left', 'mobile' ),
					'margin-right'  => astra_responsive_spacing( $margin, 'right', 'mobile' ),
				),
			);

			if ( 'custom' === $color_type || is_customize_preview() ) {
				$css_output_mobile[ $selector . ' .ast-social-color-type-custom svg' ]['fill'] = $social_icons_color_mobile;

				$css_output_mobile[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element' ]['background'] = $social_icons_bg_color_mobile;

				$css_output_mobile[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover' ]     = array(
					// Hover.
					'color'      => $social_icons_h_color_mobile,
					'background' => $social_icons_h_bg_color_mobile,
				);
				$css_output_mobile[ $selector . ' .ast-social-color-type-custom .ast-builder-social-element:hover svg' ] = array(
					'fill' => $social_icons_h_color_mobile,
				);

				if ( isset( $social_icons_label_color_mobile ) && ! empty( $social_icons_label_color_mobile ) ) {
					$css_output_mobile[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_label_color_mobile;
				} else {
					$css_output_mobile[ $selector . ' .ast-social-color-type-custom .social-item-label' ]['color'] = $social_icons_color_mobile;
				}
				if ( isset( $social_icons_label_h_color_mobile ) && ! empty( $social_icons_label_h_color_mobile ) ) {
					$css_output_mobile[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_label_h_color_mobile;
				} else {
					$css_output_mobile[ $selector . ' .ast-builder-social-element:hover .social-item-label' ]['color'] = $social_icons_h_color_mobile;
				}
			}

			if ( 'official' === $color_type && true === $toggle_brand_hover ) {
				$css_output_mobile[ $selector . ' .ast-social-color-type-official svg' ]['fill'] = $social_icons_brand_color_mobile;

				if ( isset( $social_label_icons_brand_color_mobile ) && ! empty( $social_label_icons_brand_color_mobile ) ) {
					$css_output_mobile[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_label_icons_brand_color_mobile;
				} else {
					$css_output_mobile[ $selector . ' .ast-social-color-type-official .social-item-label' ]['color'] = $social_icons_brand_color_mobile;
				}
			}

			if ( 'footer' === $builder_type ) {

				// Footer Social Alignment CSS.
				$alignment = astra_get_option( 'footer-social-' . $index . '-alignment' );

				$desktop_alignment = ( isset( $alignment['desktop'] ) ) ? $alignment['desktop'] : '';
				$tablet_alignment  = ( isset( $alignment['tablet'] ) ) ? $alignment['tablet'] : '';
				$mobile_alignment  = ( isset( $alignment['mobile'] ) ) ? $alignment['mobile'] : '';

				$css_output_desktop[ '[data-section="section-fb-social-icons-' . $index . '"] .footer-social-inner-wrap' ] = array(
					'text-align' => $desktop_alignment,
				);
				$css_output_tablet[ '[data-section="section-fb-social-icons-' . $index . '"] .footer-social-inner-wrap' ]  = array(
					'text-align' => $tablet_alignment,
				);
				$css_output_mobile[ '[data-section="section-fb-social-icons-' . $index . '"] .footer-social-inner-wrap' ]  = array(
					'text-align' => $mobile_alignment,
				);

			}

			/* Parse CSS from array() */
			$css_output  = astra_parse_css( $css_output_desktop );
			$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
			$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

			$css_output .= Astra_Builder_Base_Dynamic_CSS::prepare_advanced_typography_css( $_section, $selector );

			$visibility_selector = '.ast-builder-layout-element[data-section="' . $_section . '"]';
			$css_output         .= Astra_Builder_Base_Dynamic_CSS::prepare_visibility_css( $_section, $visibility_selector );

			$generated_css .= $css_output;
		}
		if ( $social_css_flag ) {
			return self::get_social_static_css() . $generated_css;
		}

		return $generated_css;
	}

	/**
	 * Load Social default css.
	 *
	 * @since 3.0.0
	 */
	public static function get_social_static_css() {
		$social_static_css = '
		.ast-social-stack-desktop .ast-builder-social-element,
		.ast-social-stack-tablet .ast-builder-social-element,
		.ast-social-stack-mobile .ast-builder-social-element {
		  margin-top: 6px;
		  margin-bottom: 6px;
		}

		.social-show-label-true .ast-builder-social-element {
			width: auto;
			padding: 0 0.4em;
		}
		[data-section^="section-fb-social-icons-"] .footer-social-inner-wrap {
			text-align: center;
		}
		.ast-footer-social-wrap {
			width: 100%;
		}';

		if ( is_rtl() ) {
			$social_static_css .= '.ast-footer-social-wrap .ast-builder-social-element:first-child {
				margin-right: 0;
			}
			.ast-footer-social-wrap .ast-builder-social-element:last-child {
				margin-left: 0;
			}
			.ast-header-social-wrap .ast-builder-social-element:first-child {
				margin-right: 0;
			}
			.ast-header-social-wrap .ast-builder-social-element:last-child {
				margin-left: 0;
			}
			.ast-builder-social-element {
				line-height: 1;
				color: #3a3a3a;
				background: transparent;
				vertical-align: middle;
				transition: all 0.01s;
				margin-right: 6px;
				margin-left: 6px;
				justify-content: center;
				align-items: center;
			}
			.ast-builder-social-element {
				line-height: 1;
				color: #3a3a3a;
				background: transparent;
				vertical-align: middle;
				transition: all 0.01s;
				margin-right: 6px;
				margin-left: 6px;
				justify-content: center;
				align-items: center;
			}
			.ast-builder-social-element .social-item-label {
				padding-right: 6px;
			}';
		} else {
			$social_static_css .= '.ast-footer-social-wrap .ast-builder-social-element:first-child {
				margin-left: 0;
			}
			.ast-footer-social-wrap .ast-builder-social-element:last-child {
				margin-right: 0;
			}
			.ast-header-social-wrap .ast-builder-social-element:first-child {
				margin-left: 0;
			}
			.ast-header-social-wrap .ast-builder-social-element:last-child {
				margin-right: 0;
			}
			.ast-builder-social-element {
				line-height: 1;
				color: #3a3a3a;
				background: transparent;
				vertical-align: middle;
				transition: all 0.01s;
				margin-left: 6px;
				margin-right: 6px;
				justify-content: center;
				align-items: center;
			}
			.ast-builder-social-element {
				line-height: 1;
				color: #3a3a3a;
				background: transparent;
				vertical-align: middle;
				transition: all 0.01s;
				margin-left: 6px;
				margin-right: 6px;
				justify-content: center;
				align-items: center;
			}
			.ast-builder-social-element .social-item-label {
				padding-left: 6px;
			}';
		}

		return Astra_Enqueue_Scripts::trim_css( $social_static_css );
	}
}

/**
 * Kicking this off by creating object of this class.
 */

new Astra_Social_Component_Dynamic_CSS();
