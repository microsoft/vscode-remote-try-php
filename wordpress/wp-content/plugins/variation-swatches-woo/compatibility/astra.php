<?php
/**
 * Astra Compatibility.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Compatibility;

use CFVSW\Inc\Helper;

/**
 * Astra Compatibility
 *
 * @since 1.0.0
 */
class Astra {

	/**
	 * Adds filter to update shop positions as per astra theme
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function get_shop_positions() {
		add_filter( 'cfvsw_swatches_shop_page_position', [ $this, 'positions_array' ] );
		add_filter( 'astra_get_option_shop-hover-style', [ $this, 'override_hover_effect_change' ] );
		add_filter( 'cfvsw_requires_global_settings', [ $this, 'is_required_astra_page' ] );
	}

	/**
	 * Returns astra theme positions
	 *
	 * @param array $default default position array.
	 * @return array
	 * @since 1.0.0
	 */
	public function positions_array( $default ) {
		return wp_parse_args(
			[
				'before_title' => [
					'action'   => 'astra_woo_shop_before_summary_wrap',
					'priority' => 10,
				],
				'after_title'  => [
					'action'   => 'astra_woo_shop_title_after',
					'priority' => 99,
				],
				'before_price' => [
					'action'   => 'astra_woo_shop_price_before',
					'priority' => 9,
				],
				'after_price'  => [
					'action'   => 'astra_woo_shop_price_after',
					'priority' => 1,
				],
			],
			$default
		);
	}

	/**
	 * Overrides astra feature swap images on hover
	 *
	 * @param string $style current style selected for hover.
	 * @return string
	 * @since 1.0.0
	 */
	public function override_hover_effect_change( $style ) {
		$helper               = new Helper();
		$get_shop_page_option = $helper->get_option( CFVSW_GLOBAL );
		return ! empty( $get_shop_page_option['enable_swatches_shop'] ) && 'swap' === $style ? '' : $style;
	}

	/**
	 * Checks if current page is required astra page for variation swatches.
	 *
	 * @param bool $status current status.
	 * @return boolean
	 * @since 1.0.1
	 */
	public function is_required_astra_page( $status ) {
		if ( ! function_exists( 'astra_get_option' ) ) {
			return $status;
		}

		if ( ! astra_get_option( 'enable-cart-upsells' ) ) {
			return $status;
		}
		return $status || is_cart();
	}
}
