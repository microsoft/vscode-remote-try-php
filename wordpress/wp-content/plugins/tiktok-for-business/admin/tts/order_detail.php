<?php

namespace tiktok\admin\tts\order_detail;

use function tiktok\admin\tts\common\get_tts_seller_center_origin;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * The order detail class
 */
class OrderDetail {


	/**
	 * The current order
	 *
	 * @var $order
	 */
	private static $order = null;

	/**
	 * The constructor
	 */
	public static function init() {
		// check for woo install
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		self::disable_edit();
		self::notice();
		self::hide_tiktok_custom_fields();
	}

	/**
	 * Checks whether it is a tiktok order
	 */
	private static function is_tiktok_order_detail_page() {
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		global $post, $action;

		/**
		 * Some plugin like Jetpack will cause use is_tiktok_order_detail_page() before get_current_screen is defined
		 *
		 * So we directly return false in this case
		 */
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( is_null( $screen ) ) {
			return false;
		}

		// not order detail page
		if ( 'shop_order' !== $screen->id
			|| 'shop_order' !== $screen->post_type
			|| 'edit' !== $action
		) {
			return false;
		}

		if ( ! self::$order ) {
			self::$order = wc_get_order( $post->ID );
		}

		// whether tiktok order
		return self::$order->get_meta( 'tiktok_order' );
	}

	/**
	 * Disables edits on tiktok orders (they should be managed on seller center)
	 */
	private static function disable_edit() {
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		// make the postbox container interface unclickable
		add_action(
			'admin_enqueue_scripts',
			function () {
				if ( ! self::is_tiktok_order_detail_page() ) {
					return;
				}
				?>
				<style>
					.postbox-container {
						pointer-events: none;
					}

					#poststuff {
						cursor: not-allowed;
					}

					.postbox-container #woocommerce-shipment-tracking {
						pointer-events: initial;
						cursor: initial;
					}

					.postbox-container [id*='shipment-tracking'] {
						pointer-events: initial;
						cursor: initial;
					}

					.postbox-container [class*='shipment-tracking'] {
						pointer-events: initial;
						cursor: initial;
					}

				</style>
				<?php
			}
		);

		// prevents an order from being updated if it is a TikTok order
		add_action(
			'woocommerce_before_order_object_save',
			function ( $order ) {
				// If the order doesn't yet have an ID, it is new and saving is always allowed.
				if ( ! $order->get_id() ) {
					return;
				}

				// Bail early if this isn't a TikTok order.
				if ( ! $order->get_meta( 'tiktok_order' ) ) {
					return;
				}

				// Allow for tiktok orders created by API to go through
				if ( $order->get_created_via() === 'rest-api' ) {
					return;
				}

				throw new \Exception( esc_html__( 'TikTok orders cannot be modified via the normal interface. Modify on TikTok.' ) );
			}
		);
	}

	/**
	 * Delivers notice that tiktok orders are managed on seller center
	 */
	private static function notice() {
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		add_action(
			'admin_notices',
			function () {
				if ( ! self::is_tiktok_order_detail_page() ) {
					return;
				}
				?>
				<div class="notice notice-warning">
					<p>
				<?php echo esc_html__( 'Orders generated from TikTok Shop can only be managed through' ); ?>
							<a target="_blank" href="<?php echo esc_url( get_tts_seller_center_origin() . '/order/detail?order_no=' . self::$order->get_meta( 'tiktok_order_id' ) ); ?>"><?php echo esc_html__( 'TikTok Seller Center' ); ?></a>
					</p>
				</div>
				<?php
			}
		);
	}

	/**
	 * Because tiktok_order_update meta includes timestamp placeholder
	 * so we need to hide custom fields by tiktok
	 * located in wordpress/wp-admin/includes/meta-boxes.php post_custom_meta_box function
	 */
	private static function hide_tiktok_custom_fields() {
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		add_filter(
			'is_protected_meta',
			function ( $protected, $meta_key, $meta_type ) {
				if ( ! self::is_tiktok_order_detail_page() || 'post' !== $meta_type || 0 !== strpos( $meta_key, 'tiktok_' ) ) {
					return $protected;
				}

				return true;
			},
			10,
			3
		);
	}
}

OrderDetail::init();
