<?php
/**
 * Handles wcpay promotion
 */

namespace Automattic\WooCommerce\Internal\Admin\WCPayPromotion;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\EvaluateSuggestion;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;
use Automattic\WooCommerce\Admin\RemoteSpecs\RemoteSpecsEngine;

/**
 * WC Pay Promotion engine.
 */
class Init extends RemoteSpecsEngine {
	const EXPLAT_VARIATION_PREFIX = 'woocommerce_wc_pay_promotion_payment_methods_table_';

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once __DIR__ . '/WCPaymentGatewayPreInstallWCPayPromotion.php';

		$is_payments_page = isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'checkout'; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! wp_is_json_request() && ! $is_payments_page ) {
			return;
		}

		add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'possibly_register_pre_install_wc_pay_promotion_gateway' ) );
		add_filter( 'option_woocommerce_gateway_order', array( __CLASS__, 'set_gateway_top_of_list' ) );
		add_filter( 'default_option_woocommerce_gateway_order', array( __CLASS__, 'set_gateway_top_of_list' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_payment_method_promotions' ) );
	}

	/**
	 * Possibly registers the pre install wc pay promoted gateway.
	 *
	 * @param array $gateways list of gateway classes.
	 * @return array list of gateway classes.
	 */
	public static function possibly_register_pre_install_wc_pay_promotion_gateway( $gateways ) {
		if ( self::can_show_promotion() && ! WCPaymentGatewayPreInstallWCPayPromotion::is_dismissed() ) {
			$gateways[] = 'Automattic\WooCommerce\Internal\Admin\WCPayPromotion\WCPaymentGatewayPreInstallWCPayPromotion';
		}
		return $gateways;
	}

	/**
	 * Checks if promoted gateway can be registered.
	 *
	 * @return boolean if promoted gateway should be registered.
	 */
	public static function can_show_promotion() {
		// Check if WC Pay is enabled.
		if ( class_exists( '\WC_Payments' ) ) {
			return false;
		}
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) === 'no' ) {
			return false;
		}
		if ( ! apply_filters( 'woocommerce_allow_marketplace_suggestions', true ) ) {
			return false;
		}

		$wc_pay_spec = self::get_wc_pay_promotion_spec();
		if ( ! $wc_pay_spec ) {
			return false;
		}
		return true;
	}

	/**
	 * By default, new payment gateways are put at the bottom of the list on the admin "Payments" settings screen.
	 * For visibility, we want WooCommerce Payments to be at the top of the list.
	 *
	 * @param array $ordering Existing ordering of the payment gateways.
	 *
	 * @return array Modified ordering.
	 */
	public static function set_gateway_top_of_list( $ordering ) {
		$ordering = (array) $ordering;
		$id       = WCPaymentGatewayPreInstallWCPayPromotion::GATEWAY_ID;
		// Only tweak the ordering if the list hasn't been reordered with WooCommerce Payments in it already.
		if ( ! isset( $ordering[ $id ] ) || ! is_numeric( $ordering[ $id ] ) ) {
			$is_empty        = empty( $ordering ) || ( count( $ordering ) === 1 && $ordering[0] === false );
			$ordering[ $id ] = $is_empty ? 0 : ( min( $ordering ) - 1 );
		}
		return $ordering;
	}

	/**
	 * Get WC Pay promotion spec.
	 */
	public static function get_wc_pay_promotion_spec() {
		$promotions            = self::get_promotions();
		$wc_pay_promotion_spec = array_values(
			array_filter(
				$promotions,
				function( $promotion ) {
					return isset( $promotion->plugins ) && in_array( 'woocommerce-payments', $promotion->plugins, true );
				}
			)
		);

		return current( $wc_pay_promotion_spec );
	}

	/**
	 * Go through the specs and run them.
	 */
	public static function get_promotions() {
		$locale = get_user_locale();

		$specs   = self::get_specs();
		$results = EvaluateSuggestion::evaluate_specs( $specs );

		if ( count( $results['errors'] ) > 0 ) {
			// Unlike payment gateway suggestions, we don't have a non-empty default set of promotions to fall back to.
			// So just set the specs transient with expired time to 3 hours.
			WCPayPromotionDataSourcePoller::get_instance()->set_specs_transient( array( $locale => $specs ), 3 * HOUR_IN_SECONDS );
			self::log_errors( $results['errors'] );
		}

		return $results['suggestions'];
	}

	/**
	 * Get merchant WooPay eligibility.
	 */
	public static function is_woopay_eligible() {
		$wcpay_promotion = self::get_wc_pay_promotion_spec();

		return $wcpay_promotion && 'woocommerce_payments:woopay' === $wcpay_promotion->id;
	}

	/**
	 * Delete the specs transient.
	 */
	public static function delete_specs_transient() {
		WCPayPromotionDataSourcePoller::get_instance()->delete_specs_transient();
	}

	/**
	 * Get specs or fetch remotely if they don't exist.
	 */
	public static function get_specs() {
		if ( get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) === 'no' ) {
			return array();
		}
		return WCPayPromotionDataSourcePoller::get_instance()->get_specs_from_data_sources();
	}

	/**
	 * Loads the payment method promotions scripts and styles.
	 */
	public static function load_payment_method_promotions() {
		WCAdminAssets::register_style( 'payment-method-promotions', 'style', array( 'wp-components' ) );
		WCAdminAssets::register_script( 'wp-admin-scripts', 'payment-method-promotions', true );
	}
}

