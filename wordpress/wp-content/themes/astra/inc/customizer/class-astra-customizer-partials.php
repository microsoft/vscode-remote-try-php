<?php
/**
 * Customizer Partial.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer Partials
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Astra_Customizer_Partials' ) ) {

	/**
	 * Customizer Partials initial setup
	 */
	class Astra_Customizer_Partials {

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __construct() { }

		/**
		 * Render Partial Site Tagline
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_partial_site_tagline() {

			$site_tagline_setting = astra_get_option( 'display-site-tagline-responsive' );
			$site_tagline         = ( $site_tagline_setting['desktop'] || $site_tagline_setting['tablet'] || $site_tagline_setting['mobile'] ) ? true : false;

			if ( true === $site_tagline ) {
				return get_bloginfo( 'description', 'display' );
			}
		}

		/**
		 * Render Partial Site Tagline
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_partial_site_title() {

			$site_title_setting = astra_get_option( 'display-site-title-responsive' );
			$site_title         = ( $site_title_setting['desktop'] || $site_title_setting['tablet'] || $site_title_setting['mobile'] ) ? true : false;

			if ( true === $site_title ) {
				return get_bloginfo( 'name', 'display' );
			}
		}

		/**
		 * Render Partial Header Right Section HTML
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_header_main_rt_section_html() {

			$right_section_html = astra_get_option( 'header-main-rt-section-html' );

			return do_shortcode( wp_kses_post( $right_section_html ) );
		}

		/**
		 * Render Partial Text Custom Menu Item
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_header_main_rt_section_button_text() {
			$custom_button_text = astra_get_option( 'header-main-rt-section-button-text' );

			$html = '<div class="ast-button"> ' . $custom_button_text . ' </div>';
			return do_shortcode( wp_kses_post( $html ) );
		}

		/**
		 * Render Partial Text Header Site Title & Tagline
		 *
		 * @since 2.2.0
		 *
		 * @return mixed
		 */
		public static function render_header_site_title_tagline() {
			$site_title           = astra_get_option( 'display-site-title-responsive' );
			$display_site_title   = ( $site_title['desktop'] || $site_title['tablet'] || $site_title['mobile'] ) ? true : false;
			$site_tagline         = astra_get_option( 'display-site-tagline-responsive' );
			$display_site_tagline = ( $site_tagline['desktop'] || $site_tagline['tablet'] || $site_tagline['mobile'] ) ? true : false;


			$html = astra_get_site_title_tagline( $display_site_title, $display_site_tagline );

			return do_shortcode( wp_kses_post( $html ) );
		}

		/**
		 * Render Partial Footer Section 1 Credit
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_footer_sml_section_1_credit() {

			$output = astra_get_small_footer_custom_text( 'footer-sml-section-1-credit' );
			return do_shortcode( wp_kses_post( $output ) );
		}

		/**
		 * Render Partial Footer Section 2 Credit
		 *
		 * @since 1.0.0
		 *
		 * @return mixed
		 */
		public static function render_footer_sml_section_2_credit() {

			$output = astra_get_small_footer_custom_text( 'footer-sml-section-2-credit' );
			return do_shortcode( wp_kses_post( $output ) );
		}

		/**
		 * Render Partial text for the mobile toggle menu
		 *
		 * @since 2.6.0
		 *
		 * @return mixed
		 */
		public static function mobile_toggle_menu() {

			$output = astra_masthead_toggle_buttons_primary();
			return do_shortcode( wp_kses_post( $output ) );
		}
	}
}
