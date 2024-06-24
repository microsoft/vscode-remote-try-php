<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package TikTok
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/../utils/utilities.php';

class Tt4b_Pixel_Class {
	// TTCLID Cookie name
	const TTCLID_COOKIE    = 'tiktok_ttclid';
	const TTP_COOKIE = '_ttp';
	private static $events = [];


	/**
	 * Fires the view content event
	 *
	 * @return void
	 */
	public static function inject_view_content_event() {
		// do not fire without woocommerce
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		$event  = 'ViewContent';
		$logger = new Logger();
		$logger->log( __METHOD__, "hit $event" );
		$mapi = new Tt4b_Mapi_Class( $logger );
		global $post;
		if ( ! isset( $post->ID ) ) {
			return;
		}
		$fields = self::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		$product    = wc_get_product( $post->ID );
		$content_id = (string) $product->get_sku();
		if ( '' === $content_id ) {
			$content_id = (string) $product->get_id();
		}
		$content_type = 'product';
		if ( $product->is_type( 'variable' ) ) {
			$content_type = 'product_group';
		}
		$event_id = self::get_event_id( $content_id );
		$content = self::get_properties_from_product( $product, 1, 0, Method::VIEWCONTENT );

		$properties = [
			'contents'             => [
				$content,
			],
			'content_type'         => $content_type,
			'currency'             => get_woocommerce_currency(),
			'value'                => (float) $product->get_price(),
			'event_trigger_source' => 'WooCommerce',
		];

		$user         = self::get_user();
		$hashed_email = $user['email'];
		$hashed_phone = $user['phone'];

		$url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$referrer = wp_get_referer();
		$page = [
			'url' => $url,
		];
		if ( $referrer ) {
			$page['referrer'] = $referrer;
		}

		$data = [
			[
				'event'      => $event,
				'event_id'   => $event_id,
				'event_time' => time(),
				'user'       => $user,
				'properties' => $properties,
				'page'       => $page,
			],
		];

		$params = [
			'partner_name'    => 'WooCommerce',
			'event_source'    => 'web',
			'event_source_id' => $fields['pixel_code'],
			'data'            => $data,
		];

		// events API track
		$mapi->mapi_post( 'event/track/', $fields['access_token'], $params, 'v1.3' );

		// js pixel track
		self::enqueue_event( $event, $fields['pixel_code'], $properties, $hashed_email, $hashed_phone, $event_id, $user['first_name'], $user['last_name'], $user['city'], $user['state'], $user['country'], $user['zip_code'] );

	}

	/**
	 * Fires the add to cart event
	 *
	 * @param string $cart_item_key The cart item id
	 * @param string $product_id The product id
	 * @param string $quantity The quantity of products
	 * @param string $variation_id The variant id
	 *
	 * @return void
	 */
	public static function inject_add_to_cart_event( $cart_item_key, $product_id, $quantity, $variation_id ) {
		// do not fire without woocommerce
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		$event  = 'AddToCart';
		$logger = new Logger();
		$logger->log( __METHOD__, "hit $event" );
		$mapi    = new Tt4b_Mapi_Class( $logger );
		$product = wc_get_product( $product_id );

		$fields = self::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		$content_id = (string) $product->get_sku();
		if ( '' === $content_id ) {
			$content_id = (string) $product->get_id();
		}
		$content_type = 'product';
		$content = self::get_properties_from_product( $product, 1, $variation_id, Method::ADDTOCART );

		$event_id = self::get_event_id( $content_id );
		$properties = [
			'contents'     => [
				$content,
			],
			'content_type'         => $content_type,
			'currency'             => get_woocommerce_currency(),
			'value'                => ( $content['price'] * (float) $quantity ),
			'event_trigger_source' => 'WooCommerce',
		];

		$user         = self::get_user();
		$hashed_email = $user['email'];
		$hashed_phone = $user['phone'];

		$url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$referrer = wp_get_referer();
		$page = [
			'url' => $url,
		];
		if ( $referrer ) {
			$page['referrer'] = $referrer;
		}

		$data   = [
			[
				'event'      => $event,
				'event_id'   => $event_id,
				'event_time' => time(),
				'user'       => $user,
				'properties' => $properties,
				'page'       => $page,
			],
		];
		$params = [
			'partner_name'    => 'WooCommerce',
			'event_source'    => 'web',
			'event_source_id' => $fields['pixel_code'],
			'data'            => $data,
		];
		// events API track
		$mapi->mapi_post( 'event/track/', $fields['access_token'], $params, 'v1.3' );

		// js pixel track
		self::enqueue_event( $event, $fields['pixel_code'], $properties, $hashed_email, $hashed_phone, $event_id, $user['first_name'], $user['last_name'], $user['city'], $user['state'], $user['country'], $user['zip_code'] );

	}

	/**
	 * Fires the start checkout event
	 *
	 * @return void
	 */
	public static function inject_initiate_checkout_event() {
		// do not fire without woocommerce
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		if ( null === WC()->cart || WC()->cart->get_cart_contents_count() === 0 ) {
			return;
		}

		$event  = 'InitiateCheckout';
		$logger = new Logger();
		$logger->log( __METHOD__, "hit $event" );
		$mapi = new Tt4b_Mapi_Class( $logger );
		// if registration required, and can't register in checkout and user not logged in, don't fire event.
		if ( ! WC()->checkout()->is_registration_enabled()
			 && WC()->checkout()->is_registration_required()
			 && ! is_user_logged_in()
		) {
			return;
		}
		$fields = self::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		$event_contents = [];
		$value              = 0;
		$event_id           = self::get_event_id( '' );
		$content_type       = 'product';
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product      = $cart_item['data'];
			$quantity     = (int) $cart_item['quantity'];
			$variation_id = isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0;
			$content      = self::get_properties_from_product( $product, $quantity, $variation_id, Method::STARTCHECKOUT );
			$value      += $content['price'] * $content['quantity'];
			array_push( $event_contents, $content );
		}

		$user         = self::get_user();
		$hashed_email = $user['email'];
		$hashed_phone = $user['phone'];

		$url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$referrer = wp_get_referer();
		$page = [
			'url' => $url,
		];
		if ( $referrer ) {
			$page['referrer'] = $referrer;
		}

		$properties = [
			'contents'             => $event_contents,
			'content_type'         => $content_type,
			'currency'             => get_woocommerce_currency(),
			'value'                => $value,
			'event_trigger_source' => 'WooCommerce',
		];

		$data   = [
			[
				'event'      => $event,
				'event_id'   => $event_id,
				'event_time' => time(),
				'user'       => $user,
				'properties' => $properties,
				'page'       => $page,
			],
		];
		$params = [
			'partner_name'    => 'WooCommerce',
			'event_source'    => 'web',
			'event_source_id' => $fields['pixel_code'],
			'data'            => $data,
		];

		// events API track
		$mapi->mapi_post( 'event/track/', $fields['access_token'], $params, 'v1.3' );

		// js pixel track
		self::enqueue_event( $event, $fields['pixel_code'], $properties, $hashed_email, $hashed_phone, $event_id, $user['first_name'], $user['last_name'], $user['city'], $user['state'], $user['country'], $user['zip_code'] );

	}

	/**
	 * Fires the purchase event
	 *
	 * @param string $order_id the order id
	 *
	 * @return void
	 */
	public static function inject_purchase_event( $order_id ) {
		// do not fire without woocommerce
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		$event  = 'Purchase';
		$logger = new Logger();
		$logger->log( __METHOD__, "hit $event" );
		$mapi   = new Tt4b_Mapi_Class( $logger );
		$fields = self::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// format of js and s2s payloads differ
		$event_contents = [];
		$value              = 0;
		$event_id           = self::get_event_id( '' );
		$content_type       = 'product';
		foreach ( $order->get_items() as $item ) {
			$product    = $item->get_product();
			$quantity   = $item->get_quantity();
			$parent_product_id = $product->get_parent_id();
			$content = self::get_properties_from_product( $product, $quantity, $parent_product_id, Method::PURCHASE );
			$value      += $content['price'] * $content['quantity'];
			array_push( $event_contents, $content );
		}

		$user         = self::get_user();
		$hashed_email = $user['email'];
		$hashed_phone = $user['phone'];

		$url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		$page = [
			'url' => $url,
		];

		$properties = [
			'contents'             => $event_contents,
			'content_type'         => $content_type,
			'currency'             => get_woocommerce_currency(),
			'value'                => $value,
			'event_trigger_source' => 'WooCommerce',
		];

		$data   = [
			[
				'event'      => $event,
				'event_id'   => $event_id,
				'event_time' => time(),
				'user'       => $user,
				'properties' => $properties,
				'page'       => $page,
			],
		];
		$params = [
			'partner_name'    => 'WooCommerce',
			'event_source'    => 'web',
			'event_source_id' => $fields['pixel_code'],
			'data'            => $data,
		];

		// events API track
		$mapi->mapi_post( 'event/track/', $fields['access_token'], $params, 'v1.3' );

		// js pixel track
		self::enqueue_event( $event, $fields['pixel_code'], $properties, $hashed_email, $hashed_phone, $event_id, $user['first_name'], $user['last_name'], $user['city'], $user['state'], $user['country'], $user['zip_code'] );
	}

	/**
	 *  Gets product property meta data.
	 *
	 * @param object $product      the product.
	 * @param int    $quantity     the quantity.
	 * @param int    $variation_id the variation_id.
	 * @param string $method       the method.
	 */
	public static function get_properties_from_product( $product, $quantity, $variation_id, $method ) {
		$content_id = (string) $product->get_sku();
		if ( '' === $content_id ) {
			$content_id = (string) $product->get_id();
		}

		if ( Method::PURCHASE === $method && $variation_id > 0 ) {
			$parent_product = wc_get_product( $variation_id );
			// check if parent_id matches variation id, update content_id according to method used in catalog sync.
			$parent_id = $parent_product->get_sku();
			if ( '' === $parent_id ) {
				$parent_id = $parent_product->get_id();
			}
			$content_id = variation_content_id_helper( $method, $parent_id, $content_id, $product->get_id() );
		}

		$price = $product->get_price();
		if ( Method::STARTCHECKOUT === $method ) {
			$price = self::get_product_subtotal_as_float( $product );
		}
		$sale_price = $product->get_sale_price();
		if ( '0' === $sale_price || '' === $sale_price ) {
			$sale_price = $price;
		}
		$availability = 'IN_STOCK';
		$stock_status = $product->is_in_stock();
		if ( false === $stock_status ) {
			$availability = 'OUT_OF_STOCK';
		}

		// variation_id will be > 0 if product variation is added, variation_id is post ID.
		if ( Method::PURCHASE !== $method && Method::VIEWCONTENT !== $method && $variation_id > 0 ) {
			$variation = wc_get_product( $variation_id );
			// if variation sku is same as parent product id, update content_id to match synced SKU_ID synced during catalog sync.
			$content_id = variation_content_id_helper( $method, $content_id, $variation->get_sku(), $variation_id );

			// use variation price.
			$price = $variation->get_price();
			$sale_price = $variation->get_sale_price();

			if ( Method::STARTCHECKOUT === $method ) {
				WC()->cart->get_subtotal();
				$price = self::get_product_subtotal_as_float( $variation );
			}

			if ( '0' === $sale_price || '' === $sale_price ) {
				$sale_price = $price;
			}
		}

		$content  = [
			'price'          => (float) $price,
			'quantity'       => $quantity,
			'content_id'     => $content_id,
			'content_name'   => $product->get_name(),
			'description'    => $product->get_short_description(),
			'availability'   => $availability,
			'sale_price'     => (float) $sale_price,
			'on_sale'        => $product->is_on_sale(),
		];

		$review_count = $product->get_review_count();
		if ( $review_count > 0 ) {
			$content['review_count'] = $review_count;
			$content['average_rating'] = (float) $product->get_average_rating();
		}

		$weight = $product->get_weight();
		if ( '' !== $weight ) {
			$content['weight'] = (float) $weight;
			$content['weight_unit'] = 'KG';
		}
		return $content;
	}

	/**
	 *  Gets the user param needed for view content, add to cart, start checkout, complete payment.
	 */
	public static function get_user() {
		$pixel_obj    = new Tt4b_Pixel_Class();
		$current_user = wp_get_current_user();

		$user_agent = '';
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		}
		$advanced_matching = get_option( 'tt4b_advanced_matching' );

		$email        = $current_user->user_email;
		$external_id  = (string) $current_user->ID;

		$phone_number = get_user_meta( $current_user->ID, 'billing_phone', true );
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			$ip = WC_Geolocation::get_ip_address();
		} else {
			$ip = self::get_user_ip_address();
		}

		$first_name = $current_user->user_firstname;
		$last_name = $current_user->user_lastname;
		$user_id = $current_user->ID;
		$zip_code = get_user_meta( $user_id, 'billing_postcode', true );
		$user = [
			'ip'          => $ip,
			'user_agent'  => $user_agent,
			'locale'      => strtok( get_locale(), '_' ),
			'external_id' => $external_id,
			'email' => '',
			'phone' => '',
			'first_name' => '',
			'last_name' => '',
			'zip_code' => '',
			'city' => '',
			'state' => '',
			'country' => '',
		];

		if ( isset( $_COOKIE[ self::TTCLID_COOKIE ] ) ) {
			$user['ttclid'] = sanitize_text_field( $_COOKIE[ self::TTCLID_COOKIE ] );
		}

		if ( isset( $_COOKIE[ self::TTP_COOKIE ] ) ) {
			$user['ttp'] = sanitize_text_field( wp_unslash( $_COOKIE[ self::TTP_COOKIE ] ) );
		}

		if ( $advanced_matching ) {
			$user['city'] = strtolower( str_replace(' ', '', get_user_meta( $user_id, 'billing_city', true ) ) );
			$user['state'] = strtolower( get_user_meta( $user_id, 'billing_state', true ) );
			$user['country'] = strtolower( get_user_meta( $user_id, 'billing_country', true ) );

			// hash email, phone, first name, last name, zip, and add to $user object.
			$user = $pixel_obj->add_advanced_matching_hashed_info( $email, $user, 'email' );
			$user = $pixel_obj->add_advanced_matching_hashed_info( $phone_number, $user, 'phone' );
			$user = $pixel_obj->add_advanced_matching_hashed_info( $first_name, $user, 'first_name' );
			$user = $pixel_obj->add_advanced_matching_hashed_info( $last_name, $user, 'last_name' );
			$user = $pixel_obj->add_advanced_matching_hashed_info( $zip_code, $user, 'zip_code' );
		}

		return $user;
	}

	public static function get_event_id( $content_id ) {
		$external_business_id = get_option( 'tt4b_external_business_id' );
		$unique_id            = uniqid();
		if ( '' !== $content_id ) {
			return sprintf( '%s_%s_%s', $unique_id, $external_business_id, $content_id );
		}

		return sprintf( '%s_%s', $unique_id, $external_business_id );
	}

	/**
	 *  Gets all pixels associated to an ad account.
	 *
	 * @param string $access_token The MAPI issued access token.
	 * @param string $advertiser_id The users advertiser id.
	 * @param string $pixel_code The users pixel code.
	 */
	public function get_pixels( $access_token, $advertiser_id, $pixel_code ) {
		// returns a raw API response from TikTok pixel/list/ endpoint
		$params = [
			'advertiser_id' => $advertiser_id,
			'code'          => $pixel_code,
		];
		$url    = 'https://business-api.tiktok.com/open_api/v1.3/pixel/list/?' . http_build_query( $params );
		$args   = [
			'method'  => 'GET',
			'headers' => [
				'Access-Token' => $access_token,
				'Content-Type' => 'application/json',
			],
		];
		$logger = new Logger();
		$logger->log_request( $url, $args );
		$result = wp_remote_get( $url, $args );
		$logger->log_response( __METHOD__, $result );

		return wp_remote_retrieve_body( $result );
	}

	/**
	 * Gets whether advanced matching is enabled for the user.
	 *
	 * @param string $info The users email or phone
	 *
	 * @return false|string
	 */
	public function add_advanced_matching_hashed_info( $info, $user, $identifier ) {
		if ( '' === $info ) {
			$user[$identifier] = $info;
			return $user;
		}
		$hashed_info = hash( 'SHA256', strtolower( $info ) );
		$user[$identifier] = $hashed_info;

		return $user;
	}

	/**
	 *  Preprocess to ensure we have the required fields to call the event track API
	 *
	 * @param string $method The hook that is executed.
	 *
	 * @return array
	 */
	public static function pixel_event_tracking_field_track( $method ) {
		$logger = new Logger();
		try {
			$access_token  = self::get_and_validate_option( 'access_token' );
			$pixel_code    = self::get_and_validate_option( 'pixel_code' );
			$advertiser_id = self::get_and_validate_option( 'advertiser_id' );
		} catch ( Exception $e ) {
			$logger->log( $method, $e->getMessage() );

			return [];
		}

		return [
			'access_token'  => $access_token,
			'advertiser_id' => $advertiser_id,
			'pixel_code'    => $pixel_code,
		];
	}

	/**
	 *  Validates to ensure tt4b options are stored, and return the option if it is.
	 *
	 * @param string $option_name The tt4b data option
	 * @param bool   $default The default option boolean
	 *
	 * @return string
	 * @throws Exception          Throws exception when the given option is missing.
	 */
	protected static function get_and_validate_option( $option_name, $default = false ) {
		$option = get_option( "tt4b_{$option_name}", $default );
		if ( false === $option ) {
			throw new Exception( sprintf( 'Missing option "%s"', $option_name ) );
		}

		return $option;
	}

	/**
	 *  Checks to see whether to track events s2s
	 *
	 * @param string $access_token The access token
	 * @param string $advertiser_id The advertiser_id
	 * @param string $pixel_code The pixel_code
	 *
	 * @return bool
	 */
	public function confirm_to_send_s2s_events( $access_token, $advertiser_id, $pixel_code ) {
		$should_send_events = get_option( 'tt4b_should_send_s2s_events' );
		if ( false === $should_send_events ) {
			$pixel_obj = new Tt4b_Pixel_Class();
			$pixel_rsp = $pixel_obj->get_pixels(
				$access_token,
				$advertiser_id,
				$pixel_code
			);
			$pixel     = json_decode( $pixel_rsp, true );
			// case 1: always send events for woo_commerce pixels
			update_option( 'tt4b_should_send_s2s_events', 'YES' );
			if ( '' !== $pixel ) {
				$connected_pixel = $pixel['data']['pixels'][0];
				$partner         = $connected_pixel['partner_name'];
				if ( 'WOO_COMMERCE' !== $partner ) {
					update_option( 'tt4b_should_send_s2s_events', 'NO' );
					// case 2: if the pixel is not a partner pixel, send events if no recent activity
					if ( 'ACTIVE' !== $connected_pixel['activity_status'] ) {
						update_option( 'tt4b_should_send_s2s_events', 'YES' );
					}
				}
			}
		}

		$should_send_event_data = get_option( 'tt4b_should_send_s2s_events' );
		if ( 'NO' === $should_send_event_data ) {
			return false;
		}

		return true;
	}

	/**
	 *  Grab ttclid from URL and set cookie for 30 days
	 */
	public static function set_ttclid() {
		if ( isset( $_GET['ttclid'] ) ) {
			setcookie( self::TTCLID_COOKIE, sanitize_text_field( $_GET['ttclid'] ), time() + 30 * 86400, '/' );
		}
	}

	public static function get_user_ip_address() {
		foreach ( ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
			if ( array_key_exists( $key, $_SERVER ) ) {
				foreach ( explode( ',', sanitize_text_field( $_SERVER[$key] ) ) as $ip ) {
					$ip = trim( $ip );
					if ( false !== filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
						return $ip;
					}
				}
			}
		}
		return '';
	}

	/**
	 *  Add ajax event tracking
	 */
	public static function add_ajax_snippet() {
		$pixel_code = get_option( 'tt4b_pixel_code' );
		if ( ! $pixel_code ) {
			return;
		}

		$currency = '';
		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			$currency = get_woocommerce_currency();
		}

		$country           = get_option( 'tt4b_user_country' );
		$advanced_matching = get_option( 'tt4b_advanced_matching' );
		wp_register_script( 'tt4b_ajax_script', plugins_url( '/admin/js/ajaxSnippet.js', dirname( __DIR__ ) . '/tiktok-for-woocommerce.php' ), [ 'jquery' ], 'v1', false );
		wp_enqueue_script( 'tt4b_ajax_script' );
		wp_localize_script(
			'tt4b_ajax_script',
			'tt4b_script_vars',
			[
				'pixel_code'        => $pixel_code,
				'currency'          => $currency,
				'country'           => $country,
				'advanced_matching' => $advanced_matching,
			]
		);
	}

	/**
	 * Get cart subtotal for a product with tax if appropriate
	 *
	 * @param WC_Product $product  the product to calculate row subtotal
	 * @param int        $quantity quantity of product being purchase
	 *
	 * @return int the appropriate price with tax for the product row subtotal
	 */
	protected static function get_product_subtotal_as_float( $product ) {
		$row_price = $product->get_price();

		if ( $product->is_taxable() ) {
			if ( WC()->cart->display_prices_including_tax() ) {
				$row_price = wc_get_price_including_tax( $product, [ 'qty' => 1 ] );
			} else {
				$row_price = wc_get_price_excluding_tax( $product, [ 'qty' => 1 ] );
			}
		}

		return (float) $row_price;
	}

	/**
	 * Gets the event's JS code to be enqueued or printed.
	 *
	 * @param string $event The event's type.
	 * @param string $pixel_code The pixel code
	 * @param array  $data The data to be passed to the JS function.
	 * @param string $event_id The unique id corresponding to the event.
	 *
	 * @return string
	 */
	private static function prepare_event_code( $event, $pixel_code, $data, $event_id ) {
		if ( [] === $data ) {
			return sprintf(
				'ttq.instance(\'%s\').track(\'%s\', {\'event_id\': \'%s\'})',
				$pixel_code,
				$event,
				$event_id
			);
		}

		$data_string = empty( $data ) ? null : wp_json_encode( $data );
		return sprintf(
			'ttq.instance(\'%s\').track(\'%s\', %s, {\'event_id\': \'%s\'})',
			$pixel_code,
			$event,
			$data_string,
			$event_id
		);
	}

	/**
	 * Gets the AM to be enqueued or printed.
	 *
	 * @param string $pixel_code The pixel code.
	 * @param string $hashed_email The hashed email.
	 * @param string $hashed_phone The hashed phone.
	 * @param string $first_name The hashed first_name.
	 * @param string $last_name The hashed last_name
	 * @param string $city The city.
	 * @param string $state The state.
	 * @param string $country The country.
	 * @param string $zip_code The zip_code.
	 *
	 * @return string
	 */
	private static function prepare_advanced_matching( $pixel_code, $hashed_email, $hashed_phone, $first_name, $last_name, $city, $state, $country, $zip_code ) {
		return sprintf(
			'ttq.instance(\'%s\').identify({
            email: \'%s\',
            phone_number: \'%s\',
            first_name: \'%s\',
            last_name: \'%s\',
            city: \'%s\',
            state: \'%s\',
            country: \'%s\',
            zip_code: \'%s\'
            })',
			$pixel_code,
			$hashed_email,
			$hashed_phone,
			$first_name,
			$last_name,
			$city,
			$state,
			$country,
			$zip_code
		);
	}

	/**
	 * Prints the given event.
	 *
	 * @param string $event The event's type.
	 * @param string $pixel_code The pixel code.
	 * @param array  $data The data to be passed to the JS function.
	 * @param string $hashed_email The hashed email.
	 * @param string $hashed_phone The hashed phone.
	 *
	 * @return void
	 */
	private static function print_event( $event, $pixel_code, $data, $hashed_email, $hashed_phone, $event_id ) {
		wp_register_script( 'tiktok-tracking-handle-header', '', '', 'v1' );
		wp_enqueue_script( 'tiktok-tracking-handle-header' );
		$event_code_script = '<script>' . self::prepare_event_code( $event, $pixel_code, $data, $event_id ) . '</script>';
		wp_add_inline_script( 'tiktok-tracking-handle-header', $event_code_script );
		$advanced_matching_script = '<script>' . self::prepare_advanced_matching( $pixel_code, $hashed_email, $hashed_phone ) . '</script>';
		wp_add_inline_script( 'tiktok-tracking-handle-header', $advanced_matching_script );

	}

	/**
	 * Enqueues the given event.
	 *
	 * @param string $event The event's type.
	 * @param string $pixel_code The pixel code.
	 * @param array  $data The data to be passed to the JS function.
	 * @param string $hashed_email The hashed email.
	 * @param string $hashed_phone The hashed phone.
	 *
	 * @return void
	 */
	private static function enqueue_event( $event, $pixel_code, $data, $hashed_email, $hashed_phone, $event_id, $first_name, $last_name, $city, $state, $country, $zip_code ) {
		self::$events[ self::prepare_event_code( $event, $pixel_code, $data, $event_id ) ] = self::prepare_advanced_matching( $pixel_code, $hashed_email, $hashed_phone, $first_name, $last_name, $city, $state, $country, $zip_code );
	}

	/**
	 * Prints the enqueued base code and events snippets.
	 * Meant to be used in wp_head.
	 *
	 * @return void
	 */
	public static function print_script() {
		$pixel_code = get_option( 'tt4b_pixel_code' );
		if ( ! $pixel_code ) {
			return;
		}

		if ( did_action( 'woocommerce_loaded' ) > 0 ) {
			$script = '!function (w, d, t) {
		 w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{},ttq._partner=ttq._partner||"WooCommerce";var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
		 ttq.load(';
		} else {
			$script = '!function (w, d, t) {
		 w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{},ttq._partner=ttq._partner||"WordPress";var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
		 ttq.load(';
		}

		$script = $script . "'$pixel_code'";
		$script = $script . ');
		 }(window, document, \'ttq\');';
		wp_register_script( 'tiktok-pixel-tracking-handle-header', '', '', 'v1' );
		wp_enqueue_script( 'tiktok-pixel-tracking-handle-header' );
		wp_add_inline_script( 'tiktok-pixel-tracking-handle-header', $script );

		self::track_page_view();
		if ( ! empty( self::$events ) ) {
			foreach ( self::$events as $key => $value ) {
				// register a dummy script to add small inline snippet
				wp_register_script( 'tiktok-tracking-handle-header', '', '', 'v1' );
				wp_enqueue_script( 'tiktok-tracking-handle-header' );
				wp_add_inline_script( 'tiktok-tracking-handle-header', $key );
				wp_add_inline_script( 'tiktok-tracking-handle-header', $value );
			}
			self::$events = [];
		}
	}

	public static function track_page_view() {
		$event  = 'Pageview';
		$logger = new Logger();
		//      $logger->log( __METHOD__, "hit $event" );
		$mapi = new Tt4b_Mapi_Class( $logger );
		$fields = self::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		$event_id = self::get_event_id( '' );
		$user         = self::get_user();
		$hashed_email = $user['email'];
		$hashed_phone = $user['phone'];

		$url = '';
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$url = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		$referrer = wp_get_referer();
		$page = [
			'url' => $url,
		];
		if ( $referrer ) {
			$page['referrer'] = $referrer;
		}

		$data = [
			[
				'event'      => $event,
				'event_id'   => $event_id,
				'event_time' => time(),
				'user'       => $user,
				'page'       => $page,
			],
		];

		$partner_name = 'WooCommerce';
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			$partner_name = 'WordPress';
		}

		$params = [
			'partner_name'    => $partner_name,
			'event_source'    => 'web',
			'event_source_id' => $fields['pixel_code'],
			'data'            => $data,
		];

		// events API track
		$mapi->mapi_post( 'event/track/', $fields['access_token'], $params, 'v1.3' );

		// js pixel track
		self::enqueue_event( $event, $fields['pixel_code'], [], $hashed_email, $hashed_phone, $event_id, $user['first_name'], $user['last_name'], $user['city'], $user['state'], $user['country'], $user['zip_code'] );
	}

	public function get_key( $key ) {
		return $key;
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
		$attributes = [
			'data-product_name' => $product->get_name(),
			'data-price'        => $product->get_price(),
		];

		$args['attributes'] = array_merge( $args['attributes'], $attributes );

		return $args;
	}

}
