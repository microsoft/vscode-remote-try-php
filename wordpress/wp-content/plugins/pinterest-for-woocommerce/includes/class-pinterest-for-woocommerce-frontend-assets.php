<?php
/**
 * Handle frontend scripts
 *
 * @class       Pinterest_For_Woocommerce_Frontend_Scripts
 * @version     1.0.0
 * @package     Pinterest_For_Woocommerce/Classes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Pinterest\SaveToPinterest;

/**
 * Pinterest_For_Woocommerce_Frontend_Scripts Class.
 */
class Pinterest_For_Woocommerce_Frontend_Assets {

	/**
	 * Hook in methods.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'maybe_defer_scripts' ), 10, 3 );
	}


	/**
	 * Enqueues frontend related scripts & styles
	 *
	 * @return void
	 */
	public function load_assets() {

		if ( ! function_exists( 'is_woocommerce' ) ) {
			return;
		}

		if ( ! ( SaveToPinterest::show_pin_button() && ( is_front_page() || is_woocommerce() || is_product() || self::has_product_blocks() ) ) ) {
			return;
		}

		$assets_path_url = str_replace( array( 'http:', 'https:' ), '', Pinterest_For_Woocommerce()->plugin_url() ) . '/assets/';
		$ext             = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'pinterest-for-woocommerce-pinit', 'https://assets.pinterest.com/js/pinit.js', array(), PINTEREST_FOR_WOOCOMMERCE_VERSION, true );
		wp_enqueue_style( 'pinterest-for-woocommerce-pins', $assets_path_url . 'css/frontend/pinterest-for-woocommerce-pins' . $ext . '.css', array(), PINTEREST_FOR_WOOCOMMERCE_VERSION );

	}


	/**
	 * Checks if we are displaying a page/post that has product blocks in it,
	 * by calling has_block() for each of the registered WC product blocks with
	 * product in the name.
	 *
	 * @return boolean
	 */
	private static function has_product_blocks() {

		$all_blocks = get_dynamic_block_names();

		$product_blocks = preg_grep( '/woocommerce\//', $all_blocks );

		foreach ( $product_blocks as $block ) {

			if ( has_block( $block ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Filters the HTML script tag to defer specifics scripts
	 * added by the plugin in order to optimise initial page load.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 */
	public function maybe_defer_scripts( $tag, $handle, $src ) {

		$defer = array(
			'pinterest-for-woocommerce-pinit',
		);

		if ( in_array( $handle, $defer, true ) ) {
			return '<script src="' . $src . '" defer="defer"></script>' . "\n"; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript --- Not enqueuing here.
		}

		return $tag;
	}
}

new Pinterest_For_Woocommerce_Frontend_Assets();
