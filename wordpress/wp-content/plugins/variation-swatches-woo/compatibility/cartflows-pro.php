<?php
/**
 * Cartflows Compatibility.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Compatibility;

/**
 * Cartflows Compatibility
 *
 * @since 1.0.0
 */
class Cartflows_Pro {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'cfvsw_requires_global_settings', [ $this, 'is_required_cartflows_page' ] );
	}

	/**
	 * Checks if current page is Cartflows offer page.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_cartflows_pro_active() {
		return function_exists( '_is_cartflows_pro' ) ? _is_cartflows_pro() : false;
	}

	/**
	 * Checks if current page is Cartflows offer page.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_cartflows_offer_page() {
		if ( $this->is_cartflows_pro_active() ) {
			return function_exists( '_is_wcf_base_offer_type' ) ? _is_wcf_base_offer_type() : false;
		}
		return false;
	}

	/**
	 * Checks if current page is Cartflows checkout page.
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_cartflows_checkout_page() {
		if ( $this->is_cartflows_pro_active() ) {
			return function_exists( '_is_wcf_checkout_type' ) ? _is_wcf_checkout_type() : false;
		}
		return false;

	}

	/**
	 * Checks if current page is Cartflows checkout page.
	 *
	 * @param bool $status current status.
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_required_cartflows_page( $status ) {
		return $status || $this->is_cartflows_checkout_page() || $this->is_cartflows_offer_page();
	}
}
