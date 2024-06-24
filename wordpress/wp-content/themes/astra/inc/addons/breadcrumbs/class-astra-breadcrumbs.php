<?php
/**
 * Breadcrumbs for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_THEME_BREADCRUMBS_DIR', ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/' );
define( 'ASTRA_THEME_BREADCRUMBS_URI', ASTRA_THEME_URI . 'inc/addons/breadcrumbs/' );

if ( ! class_exists( 'Astra_Breadcrumbs' ) ) {

	/**
	 * Breadcrumbs Initial Setup
	 *
	 * @since 1.7.0
	 */
	class Astra_Breadcrumbs {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor function that initializes required actions and hooks
		 */
		public function __construct() {

			// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'class-astra-breadcrumbs-loader.php';
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'class-astra-breadcrumbs-markup.php';
			require_once ASTRA_THEME_BREADCRUMBS_DIR . 'class-astra-breadcrumb-trail.php';
			// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			// Third Party plugins in the breadcrumb options.
			add_filter( 'astra_breadcrumb_source_list', array( $this, 'astra_breadcrumb_source_list_items' ) );

			// Include front end files.
			if ( ! is_admin() ) {
				require_once ASTRA_THEME_BREADCRUMBS_DIR . 'dynamic-css/dynamic.css.php';// phpcs:ignore: WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}
		}

		/**
		 * Third Party Breadcrumb option
		 *
		 * @param Array $options breadcrumb options array.
		 *
		 * @return Array breadcrumb options array.
		 * @since 1.0.0
		 */
		public function astra_breadcrumb_source_list_items( $options ) {

			$breadcrumb_enable = is_callable( 'WPSEO_Options::get' ) ? WPSEO_Options::get( 'breadcrumbs-enable' ) : false;
			$wpseo_option      = get_option( 'wpseo_internallinks' ) ? get_option( 'wpseo_internallinks' ) : $breadcrumb_enable;
			if ( ! is_array( $wpseo_option ) ) {
				unset( $wpseo_option );
				$wpseo_option = array(
					'breadcrumbs-enable' => $breadcrumb_enable,
				);
			}

			if ( function_exists( 'yoast_breadcrumb' ) && true === $wpseo_option['breadcrumbs-enable'] ) {
				$options['yoast-seo-breadcrumbs'] = 'Yoast SEO Breadcrumbs';
			}

			if ( function_exists( 'bcn_display' ) ) {
				$options['breadcrumb-navxt'] = 'Breadcrumb NavXT';
			}

			if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
				$options['rank-math'] = 'Rank Math';
			}

			if ( function_exists( 'seopress_display_breadcrumbs' ) ) {
				$options['seopress'] = 'SEOPress';
			}

			return $options;
		}
	}

	/**
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Breadcrumbs::get_instance();

}
