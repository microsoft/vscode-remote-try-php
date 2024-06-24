<?php
/**
 * Pinterest for WooCommerce Tracking
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Pinterest\API\AdvertiserConnect;
use WC_Product;
use Automattic\WooCommerce\Utilities\NumberUtil;
use \Premmerce\WooCommercePinterest\PinterestPlugin;

/**
 * Class adding Save Pin support.
 */
class Tracking {


	/**
	 * The var used to hold the JS that is to be printed.
	 *
	 * @var string
	 */
	private static $script = '';

	/**
	 * The var used to hold the events specific JS that is to be printed.
	 *
	 * @var array
	 */
	private static $events = array();

	private const TAG_ID_SLUG       = '%%TAG_ID%%';
	private const HASHED_EMAIL_SLUG = '%%HASHED_EMAIL%%';

	/**
	 * The base tracking snippet.
	 * Documentation: https://help.pinterest.com/en/business/article/install-the-pinterest-tag
	 *
	 * @var string
	 */
	private static $base_tag = "<!-- Pinterest Pixel Base Code -->\n<script type=\"text/javascript\">\n  !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version=\"3.0\";var t=document.createElement(\"script\");t.async=!0,t.src=e;var r=document.getElementsByTagName(\"script\")[0];r.parentNode.insertBefore(t,r)}}(\"https://s.pinimg.com/ct/core.js\");\n\n  pintrk('load', '" . self::TAG_ID_SLUG . "', { np: \"woocommerce\" } );\n  pintrk('page');\n</script>\n<!-- End Pinterest Pixel Base Code -->\n";

	/**
	 * The base tracking snippet with Enchanced match support.
	 * Documentation: https://help.pinterest.com/en/business/article/enhanced-match
	 *
	 * @var string
	 */
	private static $base_tag_em = "<!-- Pinterest Pixel Base Code -->\n<script type=\"text/javascript\">\n  !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version=\"3.0\";var t=document.createElement(\"script\");t.async=!0,t.src=e;var r=document.getElementsByTagName(\"script\")[0];r.parentNode.insertBefore(t,r)}}(\"https://s.pinimg.com/ct/core.js\");\n\n pintrk('load', '" . self::TAG_ID_SLUG . "', { em: '" . self::HASHED_EMAIL_SLUG . "', np: \"woocommerce\" });\n  pintrk('page');\n</script>\n<!-- End Pinterest Pixel Base Code -->\n";


	/**
	 * The noscript base tracking snippet.
	 * Documentation: https://help.pinterest.com/en/business/article/install-the-pinterest-tag
	 *
	 * @var string
	 */
	private static $noscript_base_tag = '<!-- Pinterest Pixel Base Code --><noscript><img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?tid=' . self::TAG_ID_SLUG . '&noscript=1" /></noscript><!-- End Pinterest Pixel Base Code -->';

	/**
	 * The user/customer specific key used to store async events that are to be printed the next
	 * time we print out events.
	 *
	 * @var string
	 */
	private static $deferred_conversion_events_transient_key = null;

	/**
	 * Initiate class.
	 */
	public static function maybe_init() {

		if ( ! self::tracking_enabled() || wp_doing_cron() || is_admin() ) {
			return;
		}

		if ( is_object( WC()->session ) ) {
			self::$deferred_conversion_events_transient_key = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_async_events_' . md5( WC()->session->get_customer_id() );
		}

		// Enqueue our JS files.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Base tag.
		self::base_tag();

		// WC events.
		if ( function_exists( 'WC' ) ) {

			if ( ! wp_doing_ajax() ) {
				add_action( 'wp_head', array( __CLASS__, 'late_events_handling' ), 20 );
			}

			// AddToCart - ajax.
			if ( 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'ajax_tracking_snippet' ), 20 );
				add_filter(
					'woocommerce_loop_add_to_cart_args',
					array( __CLASS__, 'filter_add_to_cart_attributes' ),
					10,
					2
				);
			}

			// AddToCart - non-ajax.
			add_action( 'woocommerce_add_to_cart', array( __CLASS__, 'hook_add_to_cart_event' ), 20, 4 );

			// Checkout.
			add_action( 'woocommerce_before_thankyou', array( __CLASS__, 'hook_checkout_event' ), 10, 1 );

		}

		self::load_async_events();

		add_action( 'shutdown', array( __CLASS__, 'save_async_events' ) );

		// Print to head.
		add_action( 'wp_head', array( __CLASS__, 'print_script' ) );

		// Print noscript to body.
		add_action( 'wp_body_open', array( __CLASS__, 'print_noscript' ), 0 );

		add_action( 'admin_init', array( __CLASS__, 'verify_advertiser_connection' ) );
	}


	/**
	 * Loads any stored events to be printed.
	 *
	 * @return void
	 */
	private static function load_async_events() {

		if ( self::$deferred_conversion_events_transient_key ) {
			$async_events = get_transient( self::$deferred_conversion_events_transient_key );

			if ( $async_events ) {
				self::$events = array_merge( self::$events, $async_events );
				delete_transient( self::$deferred_conversion_events_transient_key );
			}
		}
	}


	/**
	 * Store any events that weren't printed on shutdown.
	 *
	 * @return void
	 */
	public static function save_async_events() {

		if ( ! empty( self::$events ) && self::$deferred_conversion_events_transient_key ) {
			set_transient( self::$deferred_conversion_events_transient_key, self::$events, 10 * MINUTE_IN_SECONDS );
		}
	}


	/**
	 * Enqueue JS files necessary to properly track actions such as search.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {

		$ext = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script(
			PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-tracking-scripts',
			Pinterest_For_Woocommerce()->plugin_url() . '/assets/js/pinterest-for-woocommerce-tracking' . $ext . '.js',
			array(),
			PINTEREST_FOR_WOOCOMMERCE_VERSION,
			true
		);
	}


	/**
	 * Initialize events that need access to conditional tags.
	 *
	 * @return void
	 */
	public static function late_events_handling() {

		// Product page visit.
		self::page_visit_event();

		// Product category page visit.
		self::category_visit_event();
	}


	/**
	 * Retunrs the hashed e-mails from the logged in user or Session data,
	 * to be used when Enchanced match is enabled.
	 * See https://help.pinterest.com/en/business/article/enhanced-match
	 *
	 * @return string|false
	 */
	public static function get_hashed_customer_email() {

		$user_email = false;

		if ( is_user_logged_in() ) {

			$user       = wp_get_current_user();
			$user_email = $user->user_email;
		}

		if ( empty( $user_email ) ) {
			$session_customer = function_exists( 'WC' ) && isset( WC()->session ) ? WC()->session->get( 'customer' ) : false;
			$user_email       = $session_customer ? $session_customer['email'] : false;
		}

		return $user_email ? md5( $user_email ) : false;
	}


	/**
	 * Use woocommerce_add_to_cart to enqueue our AddToCart event.
	 *
	 * @param string  $cart_item_key The cart item's key.
	 * @param integer $product_id    The product ID.
	 * @param integer $quantity      The quantity.
	 * @param integer $variation_id  The Variation ID.
	 *
	 * @return void
	 */
	public static function hook_add_to_cart_event( $cart_item_key, $product_id, $quantity, $variation_id ) {
		$redirect_to_cart = 'yes' === get_option( 'woocommerce_cart_redirect_after_add' );

		if ( wp_doing_ajax() && ! $redirect_to_cart ) {
			return;
		}

		$object_id = empty( $variation_id ) ? $product_id : $variation_id;
		$product   = wc_get_product( $object_id );

		$product_price = self::get_product_display_price( $product );

		self::add_event(
			'AddToCart',
			array(
				'product_id'     => $product->get_id(),
				'product_name'   => $product->get_name(),
				'value'          => ( $product_price * $quantity ),
				'order_quantity' => $quantity,
				'currency'       => get_woocommerce_currency(),
			)
		);
	}


	/**
	 * Use woocommerce_before_thankyou to enqueue our Checkout event.
	 *
	 * @param integer $order_id The Order's ID.
	 *
	 * @return void
	 */
	public static function hook_checkout_event( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$order_items    = array();
		$total_quantity = 0;

		foreach ( $order->get_items() as $order_item ) {

			if ( ! method_exists( $order_item, 'get_product' ) ) {
				continue;
			}

			$product = $order_item->get_product();

			$product_price = self::get_product_display_price( $product );

			$terms      = wc_get_object_terms( $product->get_id(), 'product_cat' );
			$categories = ! empty( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();

			$order_items[] = array(
				'product_id'       => $product->get_id(),
				'product_name'     => $order_item->get_name(),
				'product_price'    => $product_price,
				'product_quantity' => $order_item->get_quantity(),
				'product_category' => $categories,
			);

			$total_quantity += $order_item->get_quantity();
		}

		self::add_event(
			'checkout',
			array(
				'order_id'       => $order_id,
				'value'          => $order->get_total(),
				'order_quantity' => $total_quantity,
				'currency'       => $order->get_currency(),
				'line_items'     => $order_items,
			)
		);

	}


	/**
	 * Attaches a piece of JS to wc-add-to-cart script, which binds to the
	 * added_to_cart event, in order to trigger our AddToCart event
	 * when the item is added via AJAX.
	 *
	 * @return void
	 */
	public static function ajax_tracking_snippet() {

		if ( is_product() ) {
			$tracking = self::get_add_to_cart_snippet_product();
		} else {
			$tracking = self::get_add_to_cart_snippet_archive();
		}

		wp_add_inline_script( 'wc-add-to-cart', $tracking );

	}


	/**
	 * Get the add to cart tracking snippet for archives.
	 *
	 * @return string
	 */
	protected static function get_add_to_cart_snippet_archive() {
		$wc_currency = esc_js( get_woocommerce_currency() );
		$tracking    = <<< JS
jQuery( function( $ ) {
	$( document.body ).on( 'added_to_cart', function( e, fragments, cart_hash, thisbutton ) {
		var quantity = thisbutton.data( 'quantity' );
		pintrk( 'track', 'AddToCart', {
			'product_id': thisbutton.data( 'product_id' ),
			'product_name': thisbutton.data( 'product_name' ),
			'value': thisbutton.data( 'price' ) * quantity,
			'order_quantity': quantity,
			'currency': '{$wc_currency}'
		} );
	} );
} );
JS;

		return $tracking;
	}


	/**
	 * Get the add to cart tracking snippet for single product page.
	 *
	 * @return string
	 */
	protected static function get_add_to_cart_snippet_product() {
		$product = wc_get_product( get_the_ID() );

		if ( ! $product ) {
			return '';
		}

		$product_id    = $product->get_id();
		$product_name  = esc_js( $product->get_name() );
		$product_price = floatval( self::get_product_display_price( $product ) );

		$wc_currency = esc_js( get_woocommerce_currency() );
		$tracking    = <<< JS
jQuery( function( $ ) {
	$( document.body ).on( 'added_to_cart', function( e, fragments, cart_hash, thisbutton ) {
		var quantity = document.querySelector( 'input.qty[name="quantity"]' ).value;
		pintrk( 'track', 'AddToCart', {
			'product_id': '{$product_id}',
			'product_name': '{$product_name}',
			'value': {$product_price} * quantity,
			'order_quantity': quantity,
			'currency': '{$wc_currency}'
		} );
	} );
} );
JS;

		return $tracking;
	}


	/**
	 * Checks and returns if tracking is enabled and we got an active tag.
	 *
	 * @return boolean
	 */
	private static function tracking_enabled() {

		/**
		 * Allow third party plugins to disable the tracking pixel.
		 *
		 * This filter is not guaranteed to be here in the future. It may be removed at any time. Use at your own risk.
		 *
		 * @since 1.2.7
		 *
		 * @param bool $is_disable Tracking is enabled if false, and disabled if true.
		 */
		if ( apply_filters( 'woocommerce_pinterest_disable_tracking', false ) ) {
			return false;
		}

		if ( ! Pinterest_For_Woocommerce()::get_setting( 'track_conversions' ) || ! self::get_active_tag() ) {
			return false;
		}

		return true;
	}


	/**
	 * Enqueues the base tag for printing.
	 *
	 * @return void
	 */
	private static function base_tag() {

		$active_tag = self::get_active_tag();
		$email      = '';

		if ( ! $active_tag ) {
			return;
		}

		if ( Pinterest_For_Woocommerce()::get_setting( 'enhanced_match_support' ) ) {
			$email = self::get_hashed_customer_email();
		}

		$snippet = empty( $email ) ? self::$base_tag : self::$base_tag_em;
		$snippet = str_replace( array( self::TAG_ID_SLUG, self::HASHED_EMAIL_SLUG ), array( sanitize_key( $active_tag ), $email ), $snippet );

		self::$script .= $snippet;
	}


	/**
	 * Enqueues the page visit event code for printing.
	 *
	 * @return void
	 */
	private static function page_visit_event() {

		$data = array();

		if ( is_product() ) {

			$product = wc_get_product();

			$data = array(
				'product_id'    => $product->get_id(),
				'product_name'  => $product->get_name(),
				'product_price' => wc_get_price_to_display( $product ),
				'currency'      => get_woocommerce_currency(),
			);
		}

		self::add_event( 'pagevisit', $data );
	}


	/**
	 * Enqueues the Category visit event code for printing.
	 *
	 * @return void
	 */
	private static function category_visit_event() {

		if ( ! is_product_category() ) {
			return;
		}

		$queried_object = get_queried_object();

		$data = array(
			'product_category' => $queried_object->term_id,
			'category_name'    => $queried_object->name,
		);

		self::add_event( 'ViewCategory', $data );
	}


	/**
	 * Enqueues or prints the given event, depending on if
	 * we have already run wp_head or not.
	 *
	 * @param string $event The event's type.
	 * @param array  $data  The data to be passed to the JS function.
	 *
	 * @return void
	 */
	private static function add_event( $event, $data = array() ) {

		$action = did_action( 'wp_head' ) ? 'print_event' : 'enqueue_event';
		call_user_func_array( array( __CLASS__, $action ), array( $event, $data ) );

	}


	/**
	 * Enqueues the given event.
	 *
	 * @param string $event The event's type.
	 * @param array  $data  The data to be passed to the JS function.
	 *
	 * @return void
	 */
	private static function enqueue_event( $event, $data = array() ) {
		self::$events[] = self::prepare_event_code( $event, $data );
	}


	/**
	 * Prints the given event.
	 *
	 * @param string $event The event's type.
	 * @param array  $data  The data to be passed to the JS function.
	 *
	 * @return void
	 */
	private static function print_event( $event, $data = array() ) {
		echo '<script>' . self::prepare_event_code( $event, $data ) . '</script>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Gets the event's JS code to be enqueued or printed.
	 *
	 * @param string $event The event's type.
	 * @param array  $data  The data to be passed to the JS function.
	 *
	 * @return string
	 */
	private static function prepare_event_code( $event, $data = array() ) {
		$data_string = empty( $data ) ? null : wp_json_encode( $data );

		return sprintf(
			'pintrk( \'track\', \'%s\' %s);',
			$event,
			empty( $data_string ) ? '' : ', ' . $data_string
		);
	}


	/**
	 * Get the actual JS & markup for the base tag as configured in the settings.
	 *
	 * @return object|boolean
	 */
	public static function get_active_tag() {
		return Pinterest_For_Woocommerce()::get_setting( 'tracking_tag' );
	}


	/**
	 * Prints the enqueued base code and events snippets.
	 * Meant to be used in wp_head.
	 *
	 * @return void
	 */
	public static function print_script() {

		if ( ! empty( self::$script ) ) {

			echo self::$script; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Printing hardcoded JS tracking code.

			if ( ! empty( self::$events ) ) {
				echo '<script>' . implode( PHP_EOL, self::$events ) . '</script>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Printing hardcoded JS tracking code.
				self::$events = array();
			}
		}
	}


	/**
	 * Prints the noscript code.
	 *
	 * @return void
	 */
	public static function print_noscript() {

		$active_tag = self::get_active_tag();

		if ( ! $active_tag ) {
			return;
		}

		$noscript = str_replace( self::TAG_ID_SLUG, sanitize_key( $active_tag ), self::$noscript_base_tag );

		echo $noscript; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --- Printing hardcoded JS tracking code.
	}


	/**
	 * Verify if the advertiser is properly connected to the platform.
	 */
	public function verify_advertiser_connection() {
		// Verify if advertiser and tag need to be connected due to a plugin upgrade.
		try {
			self::maybe_connect_advertiser_tag();

			/* Translators: The error description */
			Logger::log( esc_html__( 'Advertiser connected successfully', 'pinterest-for-woocommerce' ) );

		} catch ( \Exception $e ) {

			/* Translators: The error description */
			Logger::log( sprintf( esc_html__( 'Could not connect the advertiser. Try to connect from the connection tab. [%s]', 'pinterest-for-woocommerce' ), $e->getMessage() ), 'error' );
		}
	}

	/**
	 * Call connect advertiser method if needed after plugin upgrade.
	 *
	 * @throws \Exception PHP Exception.
	 */
	private static function maybe_connect_advertiser_tag() {

		$is_connected         = Pinterest_For_Woocommerce()::get_data( 'is_advertiser_connected' );
		$connected_advertiser = Pinterest_For_Woocommerce()::get_setting( 'tracking_advertiser' );
		$connected_tag        = Pinterest_For_Woocommerce()::get_setting( 'tracking_tag' );

		// Check if advertiser is already connected.
		if ( ! $is_connected && $connected_advertiser && $connected_tag ) {
			AdvertiserConnect::connect_advertiser_and_tag( $connected_advertiser, $connected_tag );
		}
	}

	/**
	 * Filter the "Add to cart" button attributes to include more data.
	 *
	 * @see woocommerce_template_loop_add_to_cart()
	 *
	 * @since 1.0.11
	 *
	 * @param array      $args The arguments used for the Add to cart button.
	 * @param WC_Product $product The product object.
	 *
	 * @return array The filtered arguments for the Add to cart button.
	 */
	public static function filter_add_to_cart_attributes( array $args, WC_Product $product ) {
		$attributes = array(
			'data-product_name' => $product->get_name(),
			'data-price'        => self::get_product_display_price( $product ),
		);

		$args['attributes'] = array_merge( $args['attributes'], $attributes );

		return $args;
	}


	/**
	 * Get the formatted warning message for the potential conflicting tags.
	 *
	 * @since 1.2.3
	 *
	 * @return string The warning message.
	 */
	public static function get_third_party_tags_warning_message() {

		$third_party_tags = self::get_third_party_installed_tags();

		if ( empty( $third_party_tags ) ) {
			return '';
		}

		return sprintf(
			/* Translators: 1: Conflicting plugins, 2: Plugins Admin page opening tag, 3: Pinterest settings opening tag, 4: Closing anchor tag */
			esc_html__( 'The following installed plugin(s) can potentially cause problems with tracking: %1$s. %2$sRemove conflicting plugins%4$s or %3$smanage tracking settings%4$s.', 'pinterest-for-woocommerce' ),
			implode( ', ', $third_party_tags ),
			sprintf( '<a href="%s" target="_blank">', esc_url( admin_url( 'plugins.php' ) ) ),
			sprintf( '<a href="%s" target="_blank">', esc_url( wc_admin_url( '&path=/pinterest/settings' ) ) ),
			'</a>',
		);

	}


	/**
	 * Detect if there are other tags installed on the site.
	 *
	 * @since 1.2.3
	 *
	 * @return array The list of installed tags.
	 */
	public static function get_third_party_installed_tags() {

		$third_party_tags = array();

		if ( defined( 'GTM4WP_VERSION' ) ) {
			$third_party_tags['gtm'] = 'Google Tag Manager';
		}

		if ( defined( 'PYS_PINTEREST_VERSION' ) ) {
			$third_party_tags['pys'] = 'Pixel Your Site - Pinterest Addon';
		}

		if ( class_exists( PinterestPlugin::class ) ) {
			$third_party_tags['softblues'] = 'Pinterest for WooCommerce by Softblues';
		}

		return $third_party_tags;
	}

	/**
	 * Get product's price including/excluding tax.
	 *
	 * @param WC_Product $product The product object.
	 *
	 * @return string
	 */
	protected static function get_product_display_price( $product ) {
		return self::price_includes_tax() ? wc_get_price_including_tax( $product ) : NumberUtil::round( wc_get_price_excluding_tax( $product ), wc_get_price_decimals() );
	}

	/**
	 * Get if prices should include tax.
	 *
	 * @since 1.2.18
	 * @return bool
	 */
	protected static function price_includes_tax() {
		if ( isset( WC()->cart ) && method_exists( WC()->cart, 'display_prices_including_tax' ) ) {
			return WC()->cart->display_prices_including_tax();
		}

		if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
			return false;
		}

		return 'incl' === get_option( 'woocommerce_tax_display_cart' );
	}

}
