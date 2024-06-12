<?php
/**
 * Pinterest for WooCommerce Ads Credit Currency
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.3.9
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class handling ad credits based on currency.
 */
class AdsCreditCurrency {

	/**
	 * @var array $currency_credits_map Mapping of currency to spend requirement and credit given.
	 */
	public static $currency_credits_map = array(
		'USD' => array( 15, 125 ),
		'EUR' => array( 15, 131 ),
		'GBP' => array( 13, 117 ),
		'BRL' => array( 80, 673 ),
		'CAD' => array( 20, 172 ),
		'AUD' => array( 23, 195 ),
		'MXN' => array( 305, 2548 ),
		'ARS' => array( 2198, 18320 ),
		'CHF' => array( 14, 124 ),
		'CZK' => array( 385, 3216 ),
		'DKK' => array( 116, 970 ),
		'HUF' => array( 6366, 53050 ),
		'JPY' => array( 2172, 18102 ),
		'NOK' => array( 162, 1353 ),
		'NZD' => array( 26, 222 ),
		'PLN' => array( 74, 624 ),
		'RON' => array( 77, 646 ),
		'SEK' => array( 170, 1424 ),
	);

	/**
	 * Get spend requirement and credits based on currency.
	 *
	 * @since 1.3.9
	 *
	 * @return array $result Amount to be spent, credits given and currency symbol.
	 */
	public static function get_currency_credits() {

		$currency                              = get_woocommerce_currency();
		$credits_array                         = ( ! array_key_exists( $currency, self::$currency_credits_map ) || 'USD' === $currency ) ? self::$currency_credits_map['USD'] : self::$currency_credits_map[ $currency ];
		list( $spend_require, $credits_given ) = $credits_array;

		$result = array(
			'spendRequire' => html_entity_decode( wp_strip_all_tags( wc_price( $spend_require, array( 'decimals' => 0 ) ) ) ),
			'creditsGiven' => html_entity_decode( wp_strip_all_tags( wc_price( $credits_given, array( 'decimals' => 0 ) ) ) ),
		);

		return $result;
	}
}
