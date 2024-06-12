<?php
/**
 * Ads supported countries.
 *
 * @package     Pinterest
 * @since 1.0.5
 */

use Automattic\WooCommerce\Pinterest\API\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Pinterest_For_Woocommerce_Ads_Supported_Countries' ) ) :

	/**
	 * Class handling the settings page and onboarding Wizard registration and rendering.
	 */
	class Pinterest_For_Woocommerce_Ads_Supported_Countries {

		/**
		 * Get the alpha-2 country codes where Pinterest advertises.
		 *
		 * @see https://help.pinterest.com/en/business/availability/ads-availability
		 *
		 * @since 1.3.7 Don't fetch the list of supported countries if the user is not connected. Use a fallback instead.
		 *
		 * @throws Exception If the user is not connected and the list of supported countries can't be fetched.
		 *
		 * @return string[]
		 */
		public static function get_countries() {
			try {
				/*
				 * If the user is not connected, we can't get the list of supported countries.
				 * We throw an exception and use a fallback.
				 */
				if ( ! Pinterest_For_Woocommerce()::is_connected() ) {
					throw new Exception( 'Pinterest user is not connected, using fallback list of supported countries.' );
				}

				$allowed_countries = Base::get_list_of_ads_supported_countries();
				$get_country_code  = function( $country_object ) {
					return $country_object->code;
				};

				// Extract codes.
				$allowed_countries_codes = array_map(
					$get_country_code,
					$allowed_countries['data'],
				);

				return $allowed_countries_codes;
			} catch ( Exception $th ) {
				// A fallback in case of error.
				return array(
					'AR', // Argentina.
					'AU', // Australia.
					'AT', // Austria.
					'BE', // Belgium.
					'BR', // Brazil.
					'CA', // Canada.
					'CL', // Chile.
					'CO', // Colombia.
					'CY', // Cyprus.
					'CZ', // Czech Republic.
					'DK', // Denmark.
					'FI', // Finland.
					'FR', // France.
					'DE', // Germany.
					'GR', // Greece.
					'HK', // Hong Kong.
					'HU', // Hungary.
					'IE', // Ireland.
					'IL', // Israel.
					'IT', // Italy.
					'JP', // Japan.
					'LU', // Luxembourg.
					'MT', // Malta.
					'MX', // Mexico.
					'NL', // Netherlands.
					'NZ', // New Zealand.
					'NO', // Norway.
					'PL', // Poland.
					'PT', // Portugal.
					'RO', // Romania.
					'SG', // Singapore.
					'SK', // Slovakia.
					'KR', // South Korea.
					'ES', // Spain.
					'SE', // Sweden.
					'CH', // Switzerland.
					'GB', // United Kingdom (UK).
					'US', // United States (US).
				);
			}
		}

		/**
		 * Check if user selected location is in the list of ads supported countries.
		 *
		 * @since 1.2.5
		 *
		 * @return bool Wether this is ads supported location.
		 */
		public static function is_ads_supported_country() {
			$store_country = Pinterest_For_Woocommerce()::get_base_country() ?? 'US';
			return in_array( $store_country, self::get_countries(), true );
		}
	}

endif;
