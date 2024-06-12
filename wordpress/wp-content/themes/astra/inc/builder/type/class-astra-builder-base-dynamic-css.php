<?php
/**
 * Astra Builder Base Dynamic CSS.
 *
 * @package astra-builder
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Builder_Base_Dynamic_CSS' ) ) {

	/**
	 * Class Astra_Builder_Base_Dynamic_CSS.
	 */
	final class Astra_Builder_Base_Dynamic_CSS {

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
		public function __construct() {

			add_filter( 'astra_dynamic_theme_css', array( $this, 'footer_dynamic_css' ) );
			add_filter( 'astra_dynamic_theme_css', array( $this, 'mobile_header_logo_css' ) );
		}

		/**
		 * Prepare Advanced Margin / Padding Dynamic CSS.
		 *
		 * @param string $section_id section id.
		 * @param string $selector selector.
		 * @return array
		 */
		public static function prepare_advanced_typography_css( $section_id, $selector ) {

			$font_size = astra_get_option( 'font-size-' . $section_id );

			/**
			 * Typography CSS.
			 */
			$css_output_desktop = array(

				$selector => array(

					// Typography.
					'font-size' => astra_responsive_font( $font_size, 'desktop' ),
				),
			);

			$css_output_tablet = array(

				$selector => array(

					'font-size' => astra_responsive_font( $font_size, 'tablet' ),
				),
			);

			$css_output_mobile = array(

				$selector => array(

					'font-size' => astra_responsive_font( $font_size, 'mobile' ),
				),
			);

			/* Parse CSS from array() */
			$css_output  = astra_parse_css( $css_output_desktop );
			$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
			$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

			return $css_output;
		}

		/**
		 * Prepare Footer Dynamic CSS.
		 *
		 * @param string $dynamic_css Appended dynamic CSS.
		 * @param string $dynamic_css_filtered Filtered dynamic CSS.
		 * @return array
		 */
		public static function footer_dynamic_css( $dynamic_css, $dynamic_css_filtered = '' ) {

			/**
			 * Tablet CSS.
			 */
			$css_output_tablet = array(
				'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-firstrow .ast-builder-grid-row > *:first-child, .ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-lastrow .ast-builder-grid-row > *:last-child' => array(
					'grid-column' => '1 / -1',
				),
			);

			/**
			 * Mobile CSS.
			 */
			$css_output_mobile = array(
				'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-firstrow .ast-builder-grid-row > *:first-child, .ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-lastrow .ast-builder-grid-row > *:last-child' => array(
					'grid-column' => '1 / -1',
				),
			);

			/* Parse CSS from array() */
			$css_output  = astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
			$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

			if ( is_customize_preview() ) {

				/**
				 * Desktop CSS
				 */
				$css_output_desktop = array(
					'.ast-builder-grid-row-6-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 6, 1fr )',
					),
					'.ast-builder-grid-row-5-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 5, 1fr )',
					),
					'.ast-builder-grid-row-4-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 4, 1fr )',
					),
					'.ast-builder-grid-row-4-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr 1fr',
					),
					'.ast-builder-grid-row-4-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-3-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 3, 1fr )',
					),
					'.ast-builder-grid-row-3-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr',
					),
					'.ast-builder-grid-row-3-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-3-cheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr 1fr',
					),
					'.ast-builder-grid-row-3-cwide .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 3fr 1fr',
					),
					'.ast-builder-grid-row-2-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 2, 1fr )',
					),
					'.ast-builder-grid-row-2-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr',
					),
					'.ast-builder-grid-row-2-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr',
					),
					'.ast-builder-grid-row-2-full .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr',
					),
					'.ast-builder-grid-row-full .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr',
					),
				);

				/**
				 * Tablet CSS.
				 */
				$css_output_tablet = array(
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-6-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 6, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-5-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 5, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-4-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 4, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-4-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-4-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 3, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-cheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-cwide .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 3fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-firstrow .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-3-lastrow .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-2-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 2, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-2-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-2-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-tablet-full .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr',
					),
				);

				/**
				 * Mobile CSS
				 */
				$css_output_mobile = array(
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-6-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 6, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-5-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 5, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-4-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 4, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-4-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-4-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 3, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-cheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-cwide .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 3fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-firstrow .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-3-lastrow .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-2-equal .ast-builder-grid-row' => array(
						'grid-template-columns' => 'repeat( 2, 1fr )',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-2-lheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '2fr 1fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-2-rheavy .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr 2fr',
					),
					'.ast-builder-grid-row-container.ast-builder-grid-row-mobile-full .ast-builder-grid-row' => array(
						'grid-template-columns' => '1fr',
					),
				);

				/* Parse CSS from array() */
				$css_output .= astra_parse_css( $css_output_desktop );
				$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
				$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );
			}

			$dynamic_css .= $css_output;

			return $dynamic_css;
		}

		/**
		 * Different logo for mobile static CSS.
		 *
		 * @param string $dynamic_css Appended dynamic CSS.
		 * @since 3.5.0
		 * @return string
		 */
		public static function mobile_header_logo_css( $dynamic_css ) {

			$mobile_header_logo            = astra_get_option( 'mobile-header-logo' );
			$different_mobile_header_order = astra_get_option( 'different-mobile-logo' );

			if ( '' !== $mobile_header_logo && '1' == $different_mobile_header_order ) {
				$mobile_header_css = '
				.ast-header-break-point .ast-has-mobile-header-logo .custom-logo-link, .ast-header-break-point .wp-block-site-logo .custom-logo-link, .ast-desktop .wp-block-site-logo .custom-mobile-logo-link {
					display: none;
				}
				.ast-header-break-point .ast-has-mobile-header-logo .custom-mobile-logo-link {
					display: inline-block;
				}
				.ast-header-break-point.ast-mobile-inherit-site-logo .ast-has-mobile-header-logo .custom-logo-link,
				.ast-header-break-point.ast-mobile-inherit-site-logo .ast-has-mobile-header-logo .astra-logo-svg {
					display: block;
				}';

				$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $mobile_header_css );
			}
			return $dynamic_css;
		}

		/**
		 * Prepare Element visibility Dynamic CSS.
		 *
		 * @param string $section_id section id.
		 * @param string $selector selector.
		 * @param string $default_property Section default CSS property.
		 * @param string $mobile_tablet_default Mobile/Tabled display property.
		 * @return array
		 */
		public static function prepare_visibility_css( $section_id, $selector, $default_property = 'flex', $mobile_tablet_default = '' ) {

			$astra_options      = Astra_Theme_Options::get_astra_options();
			$css_output_desktop = array();
			$css_output_tablet  = array();
			$css_output_mobile  = array();

			// For Mobile/Tablet we need display grid property to display elements centered alignment.
			$mobile_tablet_default = ( $mobile_tablet_default ) ? $mobile_tablet_default : $default_property;

			$parent_visibility = astra_get_option(
				$section_id . '-visibility-responsive',
				array(
					'desktop' => ! isset( $astra_options[ $section_id . '-visibility-responsive' ] ) && isset( $astra_options[ $section_id . '-hide-desktop' ] ) ? ( $astra_options[ $section_id . '-hide-desktop' ] ? 0 : 1 ) : 1,
					'tablet'  => ! isset( $astra_options[ $section_id . '-visibility-responsive' ] ) && isset( $astra_options[ $section_id . '-hide-tablet' ] ) ? ( $astra_options[ $section_id . '-hide-tablet' ] ? 0 : 1 ) : 1,
					'mobile'  => ! isset( $astra_options[ $section_id . '-visibility-responsive' ] ) && isset( $astra_options[ $section_id . '-hide-mobile' ] ) ? ( $astra_options[ $section_id . '-hide-mobile' ] ? 0 : 1 ) : 1,
				)
			);

			$hide_desktop = ( $parent_visibility['desktop'] ) ? $default_property : 'none';
			$hide_tablet  = ( $parent_visibility['tablet'] ) ? $mobile_tablet_default : 'none';
			$hide_mobile  = ( $parent_visibility['mobile'] ) ? $mobile_tablet_default : 'none';

			$css_output_desktop = array(
				$selector => array(
					'display' => $hide_desktop,
				),
			);

			$css_output_tablet = array(
				'.ast-header-break-point ' . $selector => array(
					'display' => $hide_tablet,
				),
			);

			$css_output_mobile = array(
				'.ast-header-break-point ' . $selector => array(
					'display' => $hide_mobile,
				),
			);

			/* Parse CSS from array() */
			$css_output  = astra_parse_css( $css_output_desktop );
			$css_output .= astra_parse_css( $css_output_tablet, '', astra_get_tablet_breakpoint() );
			$css_output .= astra_parse_css( $css_output_mobile, '', astra_get_mobile_breakpoint() );

			return $css_output;
		}
	}

	/**
	 *  Prepare if class 'Astra_Builder_Base_Dynamic_CSS' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Builder_Base_Dynamic_CSS::get_instance();
}
