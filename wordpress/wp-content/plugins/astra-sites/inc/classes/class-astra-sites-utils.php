<?php
/**
 * Astra Sites Utlis
 *
 * @since  1.0.0
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites_Utils' ) ) :

	/**
	 * Astra_Sites_Utils
	 */
	class Astra_Sites_Utils {

		/**
		 * Third party cache plugin clear cache.
		 *
		 * @since 4.0.0
		 * @return void
		 */
		public static function third_party_cache_plugins_clear_cache() {
			// Clear LiteSpeed cache.
			if ( class_exists( '\LiteSpeed\Purge' ) ) {
				\LiteSpeed\Purge::purge_all();
			}

			// Clear cloudways cache.
			self::clear_cloudways_cache();
		}

		/**
		 * This function helps to purge all cache in clodways envirnoment.
		 * In presence of Breeze plugin (https://wordpress.org/plugins/breeze/)
		 *
		 * @since 4.0.0
		 * @return void
		 */
		public static function clear_cloudways_cache() {
			if ( ! class_exists( 'Breeze_Configuration' ) || ! class_exists( 'Breeze_CloudFlare_Helper' ) || ! class_exists( 'Breeze_Admin' ) ) {
				return;
			}

			// clear varnish cache.
			$admin = new Breeze_Admin();
			$admin->breeze_clear_varnish();

			// clear static cache.
			Breeze_Configuration::breeze_clean_cache();
			Breeze_CloudFlare_Helper::reset_all_cache();
		}
		
	}

endif;
