<?php
/**
 * Utility class used by the Feed generation to create the shipping column.
 *
 * @package Pinterest
 * @since   1.0.5
 */

namespace Automattic\WooCommerce\Pinterest;

defined( 'ABSPATH' ) || exit;

use \WC_Data_Store;

/**
 * Shipping class.
 * Supported features:
 * - Free shipping without additional settings.
 * - Free shipping with minimum order value. Minimum is tested over single item product. ( still, better than nothing )
 * - Flat rate shipping without additional settings.
 * - Flat rate with classes: no shipping class and regular classes.
 * - Shipping zone locations: single country, country + state, continent ( zip codes are not allowed).
 * - Locations mixing: continent + country + state.
 * - Taxes for shipping global.
 * - Taxes for shipping for specific country.
 * - Filtering of not supported countries.
 * - Simple products.
 * - Variable products.
 *
 * @since 1.0.5
 */
class Shipping {

	/**
	 * Local cache of shipping zones defined in WC settings.
	 *
	 * @var array|null $shipping_zones
	 */
	private static $shipping_zones = null;

	/**
	 * Prepare shipping information for $product.
	 *
	 * @since 1.0.5
	 *
	 * @param  WC_Product $product Product for which we want to generate the shipping column.
	 * @return array               Shipping information $product.
	 */
	public function prepare_shipping_info( $product ) {
		$info = array();

		/**
		 * Just to be sure that we are not obstructing the feed generation with shipping column calculation errors.
		 * Catch everything and log. This is a safety measure for unpredicted behavior.
		 */
		try {
			$shipping_zones = self::get_shipping_zones();

			foreach ( $shipping_zones as $zone ) {
				$shipping_info = $zone->get_locations_with_shipping();
				if ( is_null( $shipping_info ) ) {
					// No valid location in this shipping zone. Skip to the next zone.
					continue;
				}

				foreach ( $shipping_info['locations'] as $location ) {
					$best_shipping = self::get_best_shipping_with_cost( $location, $shipping_info['shipping_methods'], $product );
					if ( null === $best_shipping ) {
						// No valid shipping cost for $location. Skip to the next shipping destination.
						continue;
					}

					$entry = array(
						'country' => $location['country'],
						'state'   => $location['state'],
						'name'    => $best_shipping['name'],
						'cost'    => $best_shipping['cost'],
					);

					// Build shipping entry.
					$info[] = $entry;
				}
			}
		} catch ( \Throwable $th ) {
			Logger::log(
				sprintf(
					// translators: 1: error message.
					esc_html__( "There was an error in shipping information generation for the feed file:\n%s", 'pinterest-for-woocommerce' ),
					$th->getMessage()
				)
			);
		}

		return $info;
	}

	/**
	 * Get shipping zones defined in WooCommerce settings.
	 * Cache for efficiency - this information will not change between different products.
	 *
	 * @since 1.0.5
	 *
	 * @return array Shipping zones.
	 */
	private static function get_shipping_zones() {
		if ( null !== self::$shipping_zones ) {
			return self::$shipping_zones;
		}

		$data_store           = WC_Data_Store::load( 'shipping-zone' );
		$raw_zones            = $data_store->get_zones();
		self::$shipping_zones = array();

		foreach ( $raw_zones as $raw_zone ) {
			self::$shipping_zones[] = new PinterestShippingZone( $raw_zone );
		}

		return self::$shipping_zones;
	}

	/**
	 * Function used for the woocommerce_shipping_free_shipping_is_available filter.
	 * This is added to verify if min_amount feature has met the free shipping criteria.
	 * Normally this would be done by checking values in cart. Because we are not operating on cart we filter out
	 * this value ourselves.
	 *
	 * @since 1.0.5
	 *
	 * @param bool                      $is_available    Wether this shipping method should be available.
	 * @param array                     $package         Shipping package.
	 * @param WC_Shipping_Free_Shipping $shipping_method Shipping method.
	 * @return boolean
	 */
	public static function is_free_shipping_available( $is_available, $package, $shipping_method ) {
		if ( $is_available ) {
			return $is_available;
		}

		if ( ! in_array( $shipping_method->requires, array( 'min_amount' ), true ) ) {
			return $is_available;
		}

		$has_met_min_amount = $package['cart_subtotal'] >= $shipping_method->min_amount;

		return $has_met_min_amount;
	}

	/**
	 * Get the lowest possible shipping cost for given location and shipping methods for that location.
	 *
	 * @since 1.0.5
	 *
	 * @param array      $shipping_location Country and state values of the location.
	 * @param array      $shipping_methods  List of shippings methods we use to calculate the best rate.
	 * @param WC_Product $product           Product for which we want to generate the shipping column.
	 * @return array|null                   Name and cost for the best found rate or null in case nothing was found.
	 */
	private static function get_best_shipping_with_cost( $shipping_location, $shipping_methods, $product ) {

		// Since in a shipping zone all locations are treated the same we will perform the calculations for the first one.
		$package = self::put_product_into_a_shipping_package( $product, $shipping_location );
		$rates   = array();

		// Substitute default customer location and billing for.
		WC()->customer = new \WC_Customer( get_current_user_id() );
		WC()->customer->set_billing_location( $shipping_location['country'], $shipping_location['state'] );
		WC()->customer->set_shipping_location( $shipping_location['country'], $shipping_location['state'] );

		// By using the filter we can trick the get_rates_for_package to continue calculations even without having the Cart defined.
		add_filter( 'woocommerce_shipping_free_shipping_is_available', array( static::class, 'is_free_shipping_available' ), 10, 3 );
		foreach ( $shipping_methods as $shipping_method ) {
			$rates += $shipping_method->get_rates_for_package( $package );
		}
		remove_filter( 'woocommerce_shipping_free_shipping_is_available', array( static::class, 'is_free_shipping_available' ), 10 );

		// Check if shipping methods have returned any valid rates.
		if ( empty( $rates ) ) {
			return null;
		}

		$best_rate = self::calculate_best_rate( $rates );
		return $best_rate;
	}

	/**
	 * Pick the best rate from an array of rates.
	 *
	 * @since 1.0.5
	 *
	 * @param  array $rates List of shipping rates.
	 * @return array        Name and cost for the best found rate or null in case nothing was found.
	 */
	private static function calculate_best_rate( $rates ) {
		$best_cost = INF;
		$best_name = '';
		foreach ( $rates as $rate ) {
			$shipping_cost = (float) $rate->get_cost();
			$shipping_tax  = (float) $rate->get_shipping_tax();
			$total_cost    = $shipping_cost + $shipping_tax;
			if ( $total_cost < $best_cost ) {
				$best_cost = $total_cost;
				$best_name = $rate->get_label();
			}
		}

		if ( INF === $best_cost ) {
			return null;
		}

		return array(
			'cost' => wc_format_decimal( $best_cost, 2 ),
			'name' => $best_name,
		);
	}

	/**
	 * Helper function that packs products into a package structure required by the shipping methods.
	 *
	 * @since 1.0.5
	 *
	 * @param WC_Product $product  Product to package.
	 * @param array      $location Product destination location.
	 * @return array               Product packed into a package for use by shipping methods.
	 */
	public static function put_product_into_a_shipping_package( $product, $location ) {
		$cart_item = array(
			'key'          => 0,
			'product_id'   => $product->get_id(),
			'variation_id' => null,
			'variation'    => null,
			'quantity'     => 1,
			'data'         => $product,
			'data_hash'    => wc_get_cart_item_data_hash( $product ),
			'line_total'   => wc_remove_number_precision( (float) $product->get_price() ),
		);

		return array(
			'contents'        => array( $cart_item ),
			'contents_cost'   => (float) $product->get_price(),
			'applied_coupons' => array(),
			'user'            => array(
				'ID' => get_current_user_id(),
			),
			'destination'     => array(
				'country'   => $location['country'],
				'state'     => $location['state'],
				'postcode'  => '',  // May be used in the future.
				'city'      => '',
				'address'   => '',
				'address_1' => '',
				'address_2' => '',
			),
			'cart_subtotal'   => (float) $product->get_price(),
		);
	}

}
