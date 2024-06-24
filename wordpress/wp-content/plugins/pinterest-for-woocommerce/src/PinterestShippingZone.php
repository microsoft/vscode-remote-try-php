<?php
/**
 * Represents a single Pinterest shipping zone
 *
 * @since   1.0.5
 * @package Automattic\WooCommerce\Pinterest
 */

namespace Automattic\WooCommerce\Pinterest;

defined( 'ABSPATH' ) || exit;

use \WC_Shipping_Zone;

/**
 * PinterestShippingZone class.
 *
 * By extending WC_Shipping_Zone we are able to add functionality necessary for Pinterest shipping column generation.
 * This allows us to operate on familiar interface which will be useful when we will continue to expand the functionality.
 *
 * @since 1.0.5
 */
class PinterestShippingZone extends WC_Shipping_Zone {

	/**
	 * Caching for internal structure of locations.
	 *
	 * @var $zone_countries_with_states
	 */
	private $zone_countries_with_states = null;

	/**
	 * Caching for supported shipping methods.
	 *
	 * @var $zone_countries_with_states
	 */
	private $supported_shipping_methods = null;

	/**
	 * Types of allowed shipping methods.
	 */
	const ALLOWED_SHIPPING_METHODS = array( 'free_shipping', 'flat_rate' );

	/**
	 * Type of settings in the required field that we support.
	 */
	const ALLOWED_FREE_SHIPPING_REQUIRED_SETTINGS = array( '', 'min_amount' );

	/**
	 * From the list of countries filter out those which are not supported right now.
	 *
	 * @since   1.0.5
	 *
	 * @param  array $locations List of countries to filter.
	 * @return array            List of filtered countries.
	 */
	private function filter_out_not_allowed_countries( $locations ) {
		$allowed_countries = \Pinterest_For_Woocommerce_Ads_Supported_Countries::get_countries();
		return array_filter(
			$locations,
			function ( $location ) use ( $allowed_countries ) {
				return in_array( $location['country'], $allowed_countries, true );
			}
		);
	}

	/**
	 * From the zone settings generate a list of countries with states.
	 * States are optional and added only if specified.
	 * Countries are filtered to only include supported countries.
	 *
	 * @since 1.0.5
	 *
	 * @return array Array of locations supported by this zone.
	 */
	public function get_countries_with_states() {
		if ( null !== $this->zone_countries_with_states ) {
			return $this->zone_countries_with_states;
		}

		$all_continents = WC()->countries->get_continents();
		$zone_locations = $this->get_zone_locations();
		$continents     = array_filter( $zone_locations, array( $this, 'location_is_continent' ) );
		$countries      = array_filter( $zone_locations, array( $this, 'location_is_country' ) );
		$states         = array_filter( $zone_locations, array( $this, 'location_is_state' ) );
		$postcodes      = array_filter( $zone_locations, array( $this, 'location_is_postcode' ) );
		$locations      = array();

		if ( ! empty( $postcodes ) ) {
			/**
			 * We don't process zones with postcodes because Pinterest does not support postcode locations.
			 * We need to act as if this zone is empty and it is not able to provide any shipping locations.
			 */
			$this->zone_countries_with_states = array();
			return $this->zone_countries_with_states;
		}

		foreach ( $continents as $location ) {
			$locations += array_map(
				array( $this, 'map_to_location' ),
				$all_continents[ $location->code ]['countries'],
			);
		}

		foreach ( $countries as $location ) {
			$locations[] = $this->map_to_location( $location->code );
		}

		foreach ( $states as $location ) {
			$location_codes = explode( ':', $location->code );
			$locations[]    = $this->map_to_location(
				$location_codes[0],
				$location_codes[1]
			);
		}

		$locations = $this->filter_out_not_allowed_countries( $locations );
		$locations = $this->remove_duplicate_locations( $locations );

		// Cache the locations.
		$this->zone_countries_with_states = $locations;
		return $this->zone_countries_with_states;
	}

	/**
	 * Remove duplicated locations. Encapsulated into separate function
	 * in case we will need to do more complicated filtering as this is
	 * a multidimensional filtering. It will also allow unit testing.
	 *
	 * Correctness of this approach is verified indirectly by shipping UT.
	 *
	 * @since 1.0.5
	 *
	 * @param array $locations Array of locations.
	 * @return array Array of locations with duplications removed.
	 */
	public function remove_duplicate_locations( $locations ) {
		return array_unique( $locations, SORT_REGULAR );
	}

	/**
	 * Turn country and state into location array.
	 *
	 * @since 1.0.5
	 *
	 * @param string $country Location country.
	 * @param string $state   Location state.
	 * @return array          Location array.
	 */
	private function map_to_location( $country, $state = '' ) {
		return array(
			'country' => $country,
			'state'   => $state,
		);
	}

	/**
	 * Combines zone locations with allowed shipping methods.
	 *
	 * @since 1.0.5
	 *
	 * @return array Array with locations and allowed shipping methods.
	 */
	public function get_locations_with_shipping() {
		$countries_with_states = $this->get_countries_with_states();
		$shipping_methods      = $this->get_supported_shipping_methods();

		if ( empty( $countries_with_states ) || empty( $shipping_methods ) ) {
			return null;
		}

		return array(
			'locations'        => $countries_with_states,
			'shipping_methods' => $shipping_methods,
		);
	}


	/**
	 * Get shipping methods supported by the implementation.
	 * Methods are filtered by allowed types and features.
	 *
	 * @since 1.0.5
	 *
	 * @return array Supported shipping methods.
	 */
	private function get_supported_shipping_methods() {
		if ( null !== $this->supported_shipping_methods ) {
			return $this->supported_shipping_methods;
		}

		$active_shipping_methods          = $this->get_shipping_methods( true );
		$this->supported_shipping_methods = array_filter(
			$active_shipping_methods,
			array( $this, 'is_shipping_method_supported' )
		);

		return $this->supported_shipping_methods;
	}

	/**
	 * Verify if shipping method is supported.
	 *
	 * @since 1.0.5
	 *
	 * @param WC_Shipping_Method $shipping_method Shipping rate to verify.
	 * @return boolean
	 */
	private function is_shipping_method_supported( $shipping_method ) {
		if ( ! in_array( $shipping_method->id, self::ALLOWED_SHIPPING_METHODS, true ) ) {
			return false;
		}

		if ( 'free_shipping' === $shipping_method->id && ! in_array( $shipping_method->requires, self::ALLOWED_FREE_SHIPPING_REQUIRED_SETTINGS, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_continent( $location ) {
		return 'continent' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_country( $location ) {
		return 'country' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_state( $location ) {
		return 'state' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_postcode( $location ) {
		return 'postcode' === $location->type;
	}

}
