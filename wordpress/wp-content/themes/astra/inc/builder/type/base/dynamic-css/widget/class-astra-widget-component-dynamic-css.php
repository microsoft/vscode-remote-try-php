<?php
/**
 * Astra Widget Component Dynamic CSS.
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
class Astra_Widget_Component_Dynamic_CSS {

	/**
	 * Dynamic CSS
	 *
	 * @param string $builder_type Builder Type.
	 * @return String Generated dynamic CSS for Heading Colors.
	 *
	 * @since 3.0.0
	 */
	public static function astra_widget_dynamic_css( $builder_type = 'header' ) {

		$generated_css = '';

		$no_of_widgets = 'header' === $builder_type ? Astra_Builder_Helper::$num_of_header_widgets : Astra_Builder_Helper::$num_of_footer_widgets;

		for ( $index = 1; $index <= $no_of_widgets; $index++ ) {

			if ( ! Astra_Builder_Helper::is_component_loaded( 'widget-' . $index, $builder_type ) ) {
				continue;
			}

			$_section = ( ! astra_has_widgets_block_editor() ) ? 'sidebar-widgets-' . $builder_type . '-widget-' . $index : 'astra-sidebar-widgets-' . $builder_type . '-widget-' . $index;

			$selector = '.' . $builder_type . '-widget-area[data-section="sidebar-widgets-' . $builder_type . '-widget-' . $index . '"]';

			$margin = astra_get_option( $_section . '-margin' );

			/**
			 * Copyright CSS.
			 */
			if ( Astra_Builder_Helper::apply_flex_based_css() ) {
				$builder_widget_selector = $selector . '.' . $builder_type . '-widget-area-inner';
			} else {
				$builder_widget_selector = $selector . ' .' . $builder_type . '-widget-area-inner';
			}


				$title_font_size   = astra_get_option( $builder_type . '-widget-' . $index . '-font-size' );
				$content_font_size = astra_get_option( $builder_type . '-widget-' . $index . '-content-font-size' );

				$title_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-title-color' ), 'desktop' );
				$title_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-title-color' ), 'tablet' );
				$title_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-title-color' ), 'mobile' );

				$text_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-color' ), 'desktop' );
				$text_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-color' ), 'tablet' );
				$text_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-color' ), 'mobile' );

				$link_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-color' ), 'desktop' );
				$link_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-color' ), 'tablet' );
				$link_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-color' ), 'mobile' );

				$link_h_color_desktop = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-h-color' ), 'desktop' );
				$link_h_color_tablet  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-h-color' ), 'tablet' );
				$link_h_color_mobile  = astra_get_prop( astra_get_option( $builder_type . '-widget-' . $index . '-link-h-color' ), 'mobile' );

				$css_output_desktop = array(
					$builder_widget_selector              => array(
						'color'     => $text_color_desktop,
						// Typography.
						'font-size' => astra_responsive_font( $content_font_size, 'desktop' ),
					),
					$builder_widget_selector . ' a'       => array(
						'color' => $link_color_desktop,
					),
					$builder_widget_selector . ' a:hover' => array(
						'color' => $link_h_color_desktop,
					),
					$selector . ' .widget-title, ' . $selector . ' h1, ' . $selector . ' .widget-area h1, ' . $selector . ' h2, ' . $selector . ' .widget-area h2, ' . $selector . ' h3, ' . $selector . ' .widget-area h3, ' . $selector . ' h4, ' . $selector . ' .widget-area h4, ' . $selector . ' h5, ' . $selector . ' .widget-area h5, ' . $selector . ' h6, ' . $selector . ' .widget-area h6' => array( 
						'color'     => $title_color_desktop,
						'font-size' => astra_responsive_font( $title_font_size, 'desktop' ),
					),
					$selector                             => array(
						// Margin CSS.
						'margin-top'    => astra_responsive_spacing( $margin, 'top', 'desktop' ),
						'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'desktop' ),
						'margin-left'   => astra_responsive_spacing( $margin, 'left', 'desktop' ),
						'margin-right'  => astra_responsive_spacing( $margin, 'right', 'desktop' ),
					),
				);

				$css_output_tablet = array(
					$builder_widget_selector              => array(
						'color'     => $text_color_tablet,
						// Typography.
						'font-size' => astra_responsive_font( $content_font_size, 'tablet' ),
					),
					$selector . ' .widget-title, ' . $selector . ' h1, ' . $selector . ' .widget-area h1, ' . $selector . ' h2, ' . $selector . ' .widget-area h2, ' . $selector . ' h3, ' . $selector . ' .widget-area h3, ' . $selector . ' h4, ' . $selector . ' .widget-area h4, ' . $selector . ' h5, ' . $selector . ' .widget-area h5, ' . $selector . ' h6, ' . $selector . ' .widget-area h6' => array( 
						'color'     => $title_color_tablet,
						'font-size' => astra_responsive_font( $title_font_size, 'tablet' ),
					),
					$builder_widget_selector . ' a'       => array(
						'color' => $link_color_tablet,
					),
					$builder_widget_selector . ' a:hover' => array(
						'color' => $link_h_color_tablet,
					),
					$selector                             => array(
						// Margin CSS.
						'margin-top'    => astra_responsive_spacing( $margin, 'top', 'tablet' ),
						'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'tablet' ),
						'margin-left'   => astra_responsive_spacing( $margin, 'left', 'tablet' ),
						'margin-right'  => astra_responsive_spacing( $margin, 'right', 'tablet' ),
					),
				);

				$css_output_mobile = array(
					$builder_widget_selector              => array(
						'color'     => $text_color_mobile,
						// Typography.
						'font-size' => astra_responsive_font( $content_font_size, 'mobile' ),
					),
					$selector . ' .widget-title, ' . $selector . ' h1, ' . $selector . ' .widget-area h1, ' . $selector . ' h2, ' . $selector . ' .widget-area h2, ' . $selector . ' h3, ' . $selector . ' .widget-area h3, ' . $selector . ' h4, ' . $selector . ' .widget-area h4, ' . $selector . ' h5, ' . $selector . ' .widget-area h5, ' . $selector . ' h6, ' . $selector . ' .widget-area h6' => array( 
						'color'     => $title_color_mobile,
						'font-size' => astra_responsive_font( $title_font_size, 'mobile' ),
					),
					$builder_widget_selector . ' a'       => array(
						'color' => $link_color_mobile,
					),
					$builder_widget_selector . ' a:hover' => array(
						'color' => $link_h_color_mobile,
					),
					$selector                             => array(
						// Margin CSS.
						'margin-top'    => astra_responsive_spacing( $margin, 'top', 'mobile' ),
						'margin-bottom' => astra_responsive_spacing( $margin, 'bottom', 'mobile' ),
						'margin-left'   => astra_responsive_spacing( $margin, 'left', 'mobile' ),
						'margin-right'  => astra_responsive_spacing( $margin, 'right', 'mobile' ),
					),
				);

				/* Parse CSS from array() */
				$css_output  = astra_parse_css( $css_output_desktop );
				$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
				$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

				$css_output .= Astra_Builder_Base_Dynamic_CSS::prepare_visibility_css( $_section, $selector, 'block' );

				$generated_css .= $css_output;

		}

		return $generated_css;
	}
}

/**
 * Kicking this off by creating object of this class.
 */

new Astra_Widget_Component_Dynamic_CSS();
