<?php
/**
 * Astra Button Component Dynamic CSS.
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
class Astra_Button_Component_Dynamic_CSS {

	/**
	 * Dynamic CSS
	 *
	 * @param string $builder_type Builder Type.
	 * @return String Generated dynamic CSS for Heading Colors.
	 *
	 * @since 3.0.0
	 */
	public static function astra_button_dynamic_css( $builder_type = 'header' ) {

		$generated_css = '';

		$number_of_button = ( 'header' === $builder_type ) ? Astra_Builder_Helper::$num_of_header_button : Astra_Builder_Helper::$num_of_footer_button;
		$hb_button_flag   = false;

		for ( $index = 1; $index <= $number_of_button; $index++ ) {

			if ( ! Astra_Builder_Helper::is_component_loaded( 'button-' . $index, $builder_type ) ) {
				continue;
			}
			$hb_button_flag = ( 'header' === $builder_type ) ? true : false;

			$_section = ( 'header' === $builder_type ) ? 'section-hb-button-' . $index : 'section-fb-button-' . $index;
			$context  = ( 'header' === $builder_type ) ? 'hb' : 'fb';

			$_prefix = 'button' . $index;

			$selector                    = '.ast-' . $builder_type . '-button-' . $index;
			$button_font_size            = astra_get_option( $builder_type . '-' . $_prefix . '-font-size' );
			$button_border_width         = astra_get_option( $builder_type . '-' . $_prefix . '-border-size' );
			$button_border_radius_fields = astra_get_option( $builder_type . '-' . $_prefix . '-border-radius-fields' );

			// Normal Responsive Colors.
			$button_bg_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-color' ), 'desktop' );
			$button_bg_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-color' ), 'tablet' );
			$button_bg_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-color' ), 'mobile' );
			$button_color_desktop    = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-color' ), 'desktop' );
			$button_color_tablet     = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-color' ), 'tablet' );
			$button_color_mobile     = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-color' ), 'mobile' );
			// Hover Responsive Colors.
			$button_bg_h_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-h-color' ), 'desktop' );
			$button_bg_h_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-h-color' ), 'tablet' );
			$button_bg_h_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-back-h-color' ), 'mobile' );
			$button_h_color_desktop    = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-h-color' ), 'desktop' );
			$button_h_color_tablet     = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-h-color' ), 'tablet' );
			$button_h_color_mobile     = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-text-h-color' ), 'mobile' );

			// Normal Responsive Colors.
			$button_border_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-color' ), 'desktop' );
			$button_border_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-color' ), 'tablet' );
			$button_border_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-color' ), 'mobile' );

			// Hover Responsive Colors.
			$button_border_h_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-h-color' ), 'desktop' );
			$button_border_h_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-h-color' ), 'tablet' );
			$button_border_h_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-' . $_prefix . '-border-h-color' ), 'mobile' );

			/**
			 * Button CSS.
			 */
			$css_output_desktop = array(

				/**
				 * Button font size.
				 */
				$selector . '[data-section*="section-' . $context . '-button-"] .ast-builder-button-wrap .ast-custom-button' => astra_get_font_array_css( astra_get_option( $builder_type . '-' . $_prefix . '-font-family', 'inherit' ), astra_get_option( $builder_type . '-' . $_prefix . '-font-weight', 'inherit' ), $button_font_size, $builder_type . '-' . $_prefix . '-font-extras' ),

				/**
				 * Button Colors.
				 */
				$selector . ' .ast-custom-button'       => array(
					// Colors.
					'color'                      => $button_color_desktop,
					'background'                 => $button_bg_color_desktop,

					// Border.
					'border-color'               => $button_border_color_desktop,
					'border-top-width'           => astra_get_css_value( $button_border_width['top'], 'px' ),
					'border-bottom-width'        => astra_get_css_value( $button_border_width['bottom'], 'px' ),
					'border-left-width'          => astra_get_css_value( $button_border_width['left'], 'px' ),
					'border-right-width'         => astra_get_css_value( $button_border_width['right'], 'px' ),
					'border-top-left-radius'     => astra_responsive_spacing( $button_border_radius_fields, 'top', 'desktop' ),
					'border-top-right-radius'    => astra_responsive_spacing( $button_border_radius_fields, 'right', 'desktop' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $button_border_radius_fields, 'bottom', 'desktop' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $button_border_radius_fields, 'left', 'desktop' ),
				),

				// Hover & Focus Options.
				$selector . ' .ast-custom-button:hover' => array(
					'color'        => $button_h_color_desktop,
					'background'   => $button_bg_h_color_desktop,
					'border-color' => $button_border_h_color_desktop,
				),
			);

			/**
			 * Button CSS.
			 */
			$css_output_tablet = array(

				/**
				 * Button font size.
				 */
				$selector . '[data-section*="section-' . $context . '-button-"] .ast-builder-button-wrap .ast-custom-button' => array(
					// Typography.
					'font-size' => astra_responsive_font( $button_font_size, 'tablet' ),
				),

				/**
				 * Button Colors.
				 */
				$selector . ' .ast-custom-button'       => array(
					// Typography.
					'font-size'                  => astra_responsive_font( $button_font_size, 'tablet' ),

					// Colors.
					'color'                      => $button_color_tablet,
					'background'                 => $button_bg_color_tablet,
					'border-color'               => $button_border_color_tablet,
					'border-top-left-radius'     => astra_responsive_spacing( $button_border_radius_fields, 'top', 'tablet' ),
					'border-top-right-radius'    => astra_responsive_spacing( $button_border_radius_fields, 'right', 'tablet' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $button_border_radius_fields, 'bottom', 'tablet' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $button_border_radius_fields, 'left', 'tablet' ),
				),
				// Hover & Focus Options.
				$selector . ' .ast-custom-button:hover' => array(
					'color'        => $button_h_color_tablet,
					'background'   => $button_bg_h_color_tablet,
					'border-color' => $button_border_h_color_tablet,
				),
			);

			/**
			 * Button CSS.
			 */
			$css_output_mobile = array(

				/**
				 * Button font size.
				 */
				$selector . '[data-section*="section-' . $context . '-button-"] .ast-builder-button-wrap .ast-custom-button' => array(
					// Typography.
					'font-size' => astra_responsive_font( $button_font_size, 'mobile' ),
				),

				/**
				 * Button Colors.
				 */
				$selector . ' .ast-custom-button'       => array(
					// Typography.
					'font-size'                  => astra_responsive_font( $button_font_size, 'mobile' ),

					// Colors.
					'color'                      => $button_color_mobile,
					'background'                 => $button_bg_color_mobile,
					'border-color'               => $button_border_color_mobile,
					'border-top-left-radius'     => astra_responsive_spacing( $button_border_radius_fields, 'top', 'mobile' ),
					'border-top-right-radius'    => astra_responsive_spacing( $button_border_radius_fields, 'right', 'mobile' ),
					'border-bottom-right-radius' => astra_responsive_spacing( $button_border_radius_fields, 'bottom', 'mobile' ),
					'border-bottom-left-radius'  => astra_responsive_spacing( $button_border_radius_fields, 'left', 'mobile' ),
				),
				// Hover & Focus Options.
				$selector . ' .ast-custom-button:hover' => array(
					'color'        => $button_h_color_mobile,
					'background'   => $button_bg_h_color_mobile,
					'border-color' => $button_border_h_color_mobile,
				),
			);

			/* Parse CSS from array() */
			$css_output  = astra_parse_css( $css_output_desktop );
			$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
			$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

			$generated_css .= $css_output;

			$generated_css .= Astra_Extended_Base_Dynamic_CSS::prepare_advanced_margin_padding_css( $_section, $selector . '[data-section*="section-' . $context . '-button-"] .ast-builder-button-wrap .ast-custom-button' );

			$visibility_selector = '.ast-' . $builder_type . '-button-' . $index . '[data-section="' . $_section . '"]';
			$generated_css      .= Astra_Builder_Base_Dynamic_CSS::prepare_visibility_css( $_section, $visibility_selector );
		}

		if ( true === $hb_button_flag ) {
			$static_hb_css = array(
				'[data-section*="section-hb-button-"] .menu-link' => array(
					'display' => 'none',
				),
			);
			return astra_parse_css( $static_hb_css ) . $generated_css;
		}

		return $generated_css;
	}
}

/**
 * Kicking this off by creating object of this class.
 */

new Astra_Button_Component_Dynamic_CSS();
