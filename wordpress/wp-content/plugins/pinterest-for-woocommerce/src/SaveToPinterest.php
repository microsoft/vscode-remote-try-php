<?php
/**
 * Pinterest for WooCommerce Pins
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest;

use \Pinterest_For_Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class adding Save Pin support.
 */
class SaveToPinterest {

	/**
	 * Initiate class.
	 */
	public static function maybe_init() {

		if ( ! Pinterest_For_Woocommerce::is_setup_complete() || ! self::show_pin_button() ) {
			return;
		}

		add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'render_product_pin' ) );
		add_action( 'woocommerce_before_shop_loop_item', array( __CLASS__, 'render_product_pin' ), 1 );
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( __CLASS__, 'add_to_wc_blocks' ), 10, 3 );

		// Enqueue our JS files.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}


	/**
	 * Show Product Pin HTML.
	 *
	 * @since 1.0.0
	 */
	public static function render_product_pin() {

		global $product;

		if ( empty( $product ) ) {
			return;
		}

		echo wp_kses_post( self::render_pin( $product->get_id() ) );
	}


	/**
	 * Show Pin HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @param int $post_thumbnail_id Optional. Post Thumbnail ID.
	 *
	 * @return string
	 */
	public static function render_pin( $post_id, $post_thumbnail_id = '' ) {

		$attributes = array(
			'description' => esc_html( get_the_title() ),
			'url'         => esc_url( get_the_permalink() ),
		);

		$post_thumbnail_id = empty( $post_thumbnail_id ) ? get_post_thumbnail_id( $post_id ) : $post_thumbnail_id;
		$attachment        = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
		if ( ! empty( $attachment ) ) {
			$attributes['media'] = esc_url( $attachment[0] );
		}

		/**
		 * Return HTML that will be replaced by the Pinterest button.
		 *
		 * Documentation: https://developers.pinterest.com/docs/widgets/save/
		 * Here we set the 'pin-do' data attribute to 'buttonPin' so that the
		 * Image used is the one explicitly set in the media attribute.
		 */
		return sprintf(
			'<div class="pinterest-for-woocommerce-image-wrapper"><a data-pin-do="buttonPin" href="%s"></a></div>',
			esc_url(
				add_query_arg(
					$attributes,
					'https://www.pinterest.com/pin/create/button/'
				)
			)
		);
	}


	/**
	 * Return if we must show the Save Pin button.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function show_pin_button() {
		return (bool) Pinterest_For_Woocommerce()::get_setting( 'save_to_pinterest' );
	}


	/**
	 * Add the Pinit button's markup to WC blocks
	 *
	 * @since 1.0.0
	 *
	 * @param string     $html The html to be filtered.
	 * @param Object     $data Data passed to the filter.
	 * @param WC_Product $product The product object.
	 *
	 * @return bool
	 */
	public static function add_to_wc_blocks( $html, $data, $product ) {

		if ( empty( $product ) ) {
			return;
		}

		return str_replace( '</li>', self::render_pin( $product->get_id() ) . '</li>', $html );
	}


	/**
	 * Enqueue JS files necessary to properly track actions such as search.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {

		$ext = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-save-button',
			Pinterest_For_Woocommerce()->plugin_url() . '/assets/js/pinterest-for-woocommerce-save-button' . $ext . '.js',
			array(),
			PINTEREST_FOR_WOOCOMMERCE_VERSION,
			true
		);
	}

}
