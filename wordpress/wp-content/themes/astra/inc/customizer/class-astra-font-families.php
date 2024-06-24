<?php
/**
 * Helper class for font settings.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.19
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Font info class for System and Google fonts.
 */
if ( ! class_exists( 'Astra_Font_Families' ) ) :

	/**
	 * Font info class for System and Google fonts.
	 */
	final class Astra_Font_Families {

		/**
		 * System Fonts
		 *
		 * @since 1.0.19
		 * @var array
		 */
		public static $system_fonts = array();

		/**
		 * Google Fonts
		 *
		 * @since 1.0.19
		 * @var array
		 */
		public static $google_fonts = array();

		/**
		 * Get System Fonts
		 *
		 * @since 1.0.19
		 *
		 * @return Array All the system fonts in Astra
		 */
		public static function get_system_fonts() {
			if ( empty( self::$system_fonts ) ) {
				self::$system_fonts = array(
					'Helvetica' => array(
						'fallback' => 'Verdana, Arial, sans-serif',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
					'Verdana'   => array(
						'fallback' => 'Helvetica, Arial, sans-serif',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
					'Arial'     => array(
						'fallback' => 'Helvetica, Verdana, sans-serif',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
					'Times'     => array(
						'fallback' => 'Georgia, serif',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
					'Georgia'   => array(
						'fallback' => 'Times, serif',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
					'Courier'   => array(
						'fallback' => 'monospace',
						'weights'  => array(
							'300',
							'400',
							'700',
						),
					),
				);
			}

			return apply_filters( 'astra_system_fonts', self::$system_fonts );
		}

		/**
		 * Custom Fonts
		 *
		 * @since 1.0.19
		 *
		 * @return Array All the custom fonts in Astra
		 */
		public static function get_custom_fonts() {
			$custom_fonts = array();

			return apply_filters( 'astra_custom_fonts', $custom_fonts );
		}

		/**
		 * Variant labels.
		 *
		 * @since 3.8.0
		 * @return array
		 */
		public static function font_variant_labels() {
			return array(
				'100'       => __( 'Thin 100', 'astra' ),
				'200'       => __( 'Extra Light 200', 'astra' ),
				'300'       => __( 'Light 300', 'astra' ),
				'400'       => __( 'Regular 400', 'astra' ),
				'500'       => __( 'Medium 500', 'astra' ),
				'600'       => __( 'Semi-Bold 600', 'astra' ),
				'700'       => __( 'Bold 700', 'astra' ),
				'800'       => __( 'Extra-Bold 800', 'astra' ),
				'900'       => __( 'Ultra-Bold 900', 'astra' ),
				'100italic' => __( 'Thin 100 Italic', 'astra' ),
				'200italic' => __( 'Extra Light 200 Italic', 'astra' ),
				'300italic' => __( 'Light 300 Italic', 'astra' ),
				'400italic' => __( 'Regular 400 Italic', 'astra' ),
				'italic'    => __( 'Regular 400 Italic', 'astra' ),
				'500italic' => __( 'Medium 500 Italic', 'astra' ),
				'600italic' => __( 'Semi-Bold 600 Italic', 'astra' ),
				'700italic' => __( 'Bold 700 Italic', 'astra' ),
				'800italic' => __( 'Extra-Bold 800 Italic', 'astra' ),
				'900italic' => __( 'Ultra-Bold 900 Italic', 'astra' ),
			);
		}

		/**
		 * Google Fonts used in astra.
		 * Array is generated from the google-fonts.json file.
		 *
		 * @since  1.0.19
		 *
		 * @return Array Array of Google Fonts.
		 */
		public static function get_google_fonts() {

			if ( empty( self::$google_fonts ) ) {

				/**
				 * Deprecating the Filter to change the Google Fonts JSON file path.
				 *
				 * @since 2.5.0
				 * @param string $json_file File where google fonts json format added.
				 * @return array
				 */
				$google_fonts_file = apply_filters( 'astra_google_fonts_php_file', ASTRA_THEME_DIR . 'inc/google-fonts.php' );

				if ( ! file_exists( $google_fonts_file ) ) {
					return array();
				}

				$google_fonts_arr = include $google_fonts_file;// phpcs:ignore: WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

				foreach ( $google_fonts_arr as $key => $font ) {
					$name = key( $font );
					foreach ( $font[ $name ] as $font_key => $single_font ) {

						if ( 'variants' === $font_key ) {

							foreach ( $single_font as $variant_key => $variant ) {

								if ( 'regular' == $variant ) {
									$font[ $name ][ $font_key ][ $variant_key ] = '400';
								}
							}
						}

						self::$google_fonts[ $name ] = array_values( $font[ $name ] );
					}
				}
			}

			return apply_filters( 'astra_google_fonts', self::$google_fonts );
		}

	}

endif;
