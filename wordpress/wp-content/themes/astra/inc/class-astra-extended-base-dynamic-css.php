<?php
/**
 * Astra Extended Base Dynamic CSS.
 *
 * @package Astra
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Extended_Base_Dynamic_CSS' ) ) {

	/**
	 * Class Astra_Extended_Base_Dynamic_CSS.
	 */
	final class Astra_Extended_Base_Dynamic_CSS {

		/**
		 * Member Variable
		 *
		 * @var mixed instance
		 */
		private static $instance = null;

		/**
		 *  Initiator
		 */
		public static function get_instance() {

			/** @psalm-suppress RedundantConditionGivenDocblockType */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( is_null( self::$instance ) ) {
				/** @psalm-suppress RedundantConditionGivenDocblockType */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() { }

		/**
		 * Prepare Advanced Margin / Padding Dynamic CSS.
		 *
		 * @param string $section_id section id.
		 * @param string $selector selector.
		 * @return string
		 */
		public static function prepare_advanced_margin_padding_css( $section_id, $selector ) {

			if ( ! empty( $section_id ) && ! empty( $selector ) ) {

				$padding = astra_get_option( $section_id . '-padding' );
				$margin  = astra_get_option( $section_id . '-margin' );

				// Desktop CSS.
				$css_output_desktop = array(

					$selector => array(

						// Padding CSS.
						'padding-top'    => astra_responsive_spacing( $padding, 'top', 'desktop' ),
						'padding-bottom' => astra_responsive_spacing( $padding, 'bottom', 'desktop' ),
						'padding-left'   => astra_responsive_spacing( $padding, 'left', 'desktop' ),
						'padding-right'  => astra_responsive_spacing( $padding, 'right', 'desktop' ),

						// Margin CSS.
						'margin-top'     => astra_responsive_spacing( $margin, 'top', 'desktop' ),
						'margin-bottom'  => astra_responsive_spacing( $margin, 'bottom', 'desktop' ),
						'margin-left'    => astra_responsive_spacing( $margin, 'left', 'desktop' ),
						'margin-right'   => astra_responsive_spacing( $margin, 'right', 'desktop' ),
					),
				);

				// Tablet CSS.
				$css_output_tablet = array(

					$selector => array(

						// Padding CSS.
						'padding-top'    => astra_responsive_spacing( $padding, 'top', 'tablet' ),
						'padding-bottom' => astra_responsive_spacing( $padding, 'bottom', 'tablet' ),
						'padding-left'   => astra_responsive_spacing( $padding, 'left', 'tablet' ),
						'padding-right'  => astra_responsive_spacing( $padding, 'right', 'tablet' ),

						// Margin CSS.
						'margin-top'     => astra_responsive_spacing( $margin, 'top', 'tablet' ),
						'margin-bottom'  => astra_responsive_spacing( $margin, 'bottom', 'tablet' ),
						'margin-left'    => astra_responsive_spacing( $margin, 'left', 'tablet' ),
						'margin-right'   => astra_responsive_spacing( $margin, 'right', 'tablet' ),
					),
				);

				// Mobile CSS.
				$css_output_mobile = array(

					$selector => array(

						// Padding CSS.
						'padding-top'    => astra_responsive_spacing( $padding, 'top', 'mobile' ),
						'padding-bottom' => astra_responsive_spacing( $padding, 'bottom', 'mobile' ),
						'padding-left'   => astra_responsive_spacing( $padding, 'left', 'mobile' ),
						'padding-right'  => astra_responsive_spacing( $padding, 'right', 'mobile' ),

						// Margin CSS.
						'margin-top'     => astra_responsive_spacing( $margin, 'top', 'mobile' ),
						'margin-bottom'  => astra_responsive_spacing( $margin, 'bottom', 'mobile' ),
						'margin-left'    => astra_responsive_spacing( $margin, 'left', 'mobile' ),
						'margin-right'   => astra_responsive_spacing( $margin, 'right', 'mobile' ),
					),
				);

				$css_output  = astra_parse_css( $css_output_desktop );
				$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
				$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

				return $css_output;
			}

			return '';
		}

		/**
		 * Prepare Advanced Border Dynamic CSS.
		 *
		 * @param string $section_id section id.
		 * @param string $selector selector.
		 * @return string
		 */
		public static function prepare_inner_section_advanced_css( $section_id, $selector ) {

			if ( ! empty( $section_id ) && ! empty( $selector ) ) {
				$width              = astra_get_option(
					$section_id . '-border-width',
					array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					)
				);
				$color              = astra_get_option( $section_id . '-border-color', '' );
				$radius             = astra_get_option(
					$section_id . '-border-radius',
					array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					)
				);
				$css_output_desktop = array(
					$selector => array(
						'border-top-style'           => ! empty( $width['top'] ) ? 'solid' : '',
						'border-right-style'         => ! empty( $width['right'] ) ? 'solid' : '',
						'border-bottom-style'        => ! empty( $width['bottom'] ) ? 'solid' : '',
						'border-left-style'          => ! empty( $width['left'] ) ? 'solid' : '',
						'border-color'               => esc_attr( $color ),
						'border-top-width'           => ! empty( $width['top'] ) ? astra_get_css_value( $width['top'], 'px' ) : '',
						'border-bottom-width'        => ! empty( $width['bottom'] ) ? astra_get_css_value( $width['bottom'], 'px' ) : '',
						'border-left-width'          => ! empty( $width['left'] ) ? astra_get_css_value( $width['left'], 'px' ) : '',
						'border-right-width'         => ! empty( $width['right'] ) ? astra_get_css_value( $width['right'], 'px' ) : '',
						'border-top-left-radius'     => ! empty( $radius['top'] ) ? astra_get_css_value( $radius['top'], 'px' ) : '',
						'border-bottom-right-radius' => ! empty( $radius['bottom'] ) ? astra_get_css_value( $radius['bottom'], 'px' ) : '',
						'border-bottom-left-radius'  => ! empty( $radius['left'] ) ? astra_get_css_value( $radius['left'], 'px' ) : '',
						'border-top-right-radius'    => ! empty( $radius['right'] ) ? astra_get_css_value( $radius['right'], 'px' ) : '',
					),
				);

				$css_output = astra_parse_css( $css_output_desktop );

				$css_output .= self::prepare_advanced_margin_padding_css( $section_id, $selector );

				return $css_output;
			}

			return '';
		}
	}

	/**
	 *  Prepare if class 'Astra_Extended_Base_Dynamic_CSS' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Extended_Base_Dynamic_CSS::get_instance();
}
