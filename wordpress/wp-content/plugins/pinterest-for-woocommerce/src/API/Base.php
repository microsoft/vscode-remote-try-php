<?php
/**
 * Pinterest API
 *
 * @class       Pinterest_For_Woocommerce_API
 * @version     1.0.0
 * @package     Pinterest_For_WordPress/Classes/
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest as Pinterest;
use Automattic\WooCommerce\Pinterest\Logger as Logger;
use Automattic\WooCommerce\Pinterest\PinterestApiException as ApiException;
use \Exception;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base API Methods
 */
class Base {

	const API_DOMAIN      = 'https://api.pinterest.com';
	const API_VERSION     = 3;
	const API_ADS_VERSION = 4;

	/**
	 * Holds the instance of the class.
	 *
	 * @var Base
	 */
	protected static $instance = null;


	/**
	 * The token as saved in the settings.
	 *
	 * @var array
	 */
	protected static $token = null;


	/**
	 * Initialize class
	 */
	public function __construct() {}


	/**
	 * Initialize and/or return the instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * API requests wrapper
	 *
	 * @since 1.0.0
	 *
	 * Request parameter:
	 * $endpoint
	 *
	 * @param string $endpoint        the endpoint to perform the request on.
	 * @param string $method          eg, POST, GET, PUT etc.
	 * @param array  $payload         Payload to be sent on the request's body.
	 * @param string $api             The specific Endpoints subset.
	 * @param int    $cache_expiry    When set, enables caching on the request and the value is used as the cache's TTL (in seconds).
	 *
	 * @return array
	 *
	 * @throws ApiException PHP exception.
	 * @throws Exception    PHP exception.
	 */
	public static function make_request( $endpoint, $method = 'POST', $payload = array(), $api = '', $cache_expiry = false ) {

		$api         = empty( $api ) ? '' : trailingslashit( $api );
		$api_version = 'ads/' === $api ? self::API_ADS_VERSION : self::API_VERSION;

		if ( ! empty( $cache_expiry ) ) {
			$cache = self::get_cached_response( $endpoint, $method, $payload, $api );

			if ( $cache ) {
				return $cache;
			}
		}

		$request = array(
			'url'     => self::API_DOMAIN . "/{$api}v{$api_version}/{$endpoint}",
			'method'  => $method,
			'args'    => $payload,
			'headers' => array(
				'Pinterest-Woocommerce-Version' => PINTEREST_FOR_WOOCOMMERCE_VERSION,
			),
		);

		if ( 'ads/' === $api && in_array( $method, array( 'POST', 'PATCH' ), true ) ) {
			// Force json content-type header and json encode payload.
			$request['headers']['Content-Type'] = 'application/json';

			$request['args'] = wp_json_encode( $payload );
		}

		try {

			$response = self::handle_request( $request );
			self::maybe_cache_api_response( $endpoint, $method, $payload, $api, $response, $cache_expiry );
			return $response;

		} catch ( ApiException $e ) {

			if ( ! empty( Pinterest_For_WooCommerce()::get_setting( 'enable_debug_logging' ) ) ) {
				/* Translators: 1: Error message 2: Stack trace */
				Logger::log( sprintf( "%1\$s\n%2\$s", $e->getMessage(), $e->getTraceAsString() ), 'error' );
			} else {

				Logger::log(
					sprintf(
						/* Translators: 1: Request method 2: Request endpoint 3: Response status code 4: Response message 5: Pinterest code */
						esc_html__( "%1\$s Request: %2\$s\nStatus Code: %3\$s\nAPI response: %4\$s\nPinterest Code: %5\$s", 'pinterest-for-woocommerce' ),
						$method,
						$request['url'],
						$e->getCode(),
						$e->getMessage(),
						$e->get_pinterest_code(),
					),
					'error'
				);
			}

			throw $e;
		} catch ( Exception $e ) {

			if ( ! empty( Pinterest_For_WooCommerce()::get_setting( 'enable_debug_logging' ) ) ) {
				/* Translators: 1: Error message 2: Stack trace */
				Logger::log( sprintf( "%1\$s\n%2\$s", $e->getMessage(), $e->getTraceAsString() ), 'error' );
			} else {

				Logger::log(
					sprintf(
						/* Translators: 1: Request method 2: Request endpoint 3: Response status code 4: Response message */
						esc_html__( "%1\$s Request: %2\$s\nStatus Code: %3\$s\nAPI response: %4\$s", 'pinterest-for-woocommerce' ),
						$method,
						$request['url'],
						$e->getCode(),
						$e->getMessage(),
					),
					'error'
				);
			}

			throw $e;
		}

	}

	/**
	 * Get the cache key.
	 *
	 * @since 1.2.13
	 * @param string $endpoint Endpoint.
	 * @param string $method   Request method.
	 * @param array  $payload  Request payload.
	 * @param string $api      Request API.
	 *
	 * @return string The cache key.
	 */
	public static function get_cache_key( $endpoint, $method, $payload, $api ) {
		return PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_request_' . md5( $endpoint . $method . wp_json_encode( $payload ) . $api );
	}

	/**
	 * Get the cached value.
	 *
	 * @since 1.2.13
	 * @param string $endpoint Endpoint.
	 * @param string $method   Request method.
	 * @param array  $payload  Request payload.
	 * @param string $api      Request API.
	 *
	 * @return mixed Value of the transient or false if it doesn't exist.
	 */
	public static function get_cached_response( $endpoint, $method, $payload, $api ) {
		$cache_key = self::get_cache_key( $endpoint, $method, $payload, $api );
		return get_transient( $cache_key );
	}

	/**
	 * Caches the API response if cache expiry is set.
	 *
	 * @since 1.3.8
	 * @param string $endpoint     The API endpoint.
	 * @param string $method       The HTTP method.
	 * @param array  $payload      The API request payload.
	 * @param string $api          The API version.
	 * @param mixed  $response     The API response.
	 * @param int    $cache_expiry The cache expiry in seconds.
	 *
	 * @return void
	 */
	private static function maybe_cache_api_response( $endpoint, $method, $payload, $api, $response, $cache_expiry ) {
		if ( ! empty( $cache_expiry ) ) {
			$cache_key = self::get_cache_key( $endpoint, $method, $payload, $api );
			set_transient( $cache_key, $response, $cache_expiry );
		}
	}

	/**
	 * Invalidate the cached value.
	 *
	 * @since 1.2.13
	 * @param string $endpoint Endpoint.
	 * @param string $method   Request method.
	 * @param array  $payload  Request payload.
	 * @param string $api      Request API.
	 *
	 * @return void
	 */
	public static function invalidate_cached_response( $endpoint, $method, $payload, $api ) {
		$cache_key = self::get_cache_key( $endpoint, $method, $payload, $api );
		delete_transient( $cache_key );
	}

	/**
	 * Handle the request
	 *
	 * @since 1.0.0
	 *
	 * Request parameter:
	 * array['url']               string
	 * array['method']            string    Default: POST
	 * array['auth_header']       boolean   Defines if must send the token in the header. Default: true
	 * array['args']              array
	 * array['headers']           array
	 *          ['content-type']  string    Default: application/json
	 *
	 * @param array $request (See above).
	 *
	 * @return array
	 *
	 * @throws Exception PHP exception.
	 * @throws ApiException PHP exception.
	 */
	public static function handle_request( $request ) {

		$request = wp_parse_args(
			$request,
			array(
				'url'         => '',
				'method'      => 'POST',
				'auth_header' => true,
				'args'        => array(),
			)
		);

		$body = '';

		try {

			self::get_token();

			if ( $request['auth_header'] ) {
				$request['headers']['Authorization'] = 'Bearer ' . self::$token['access_token'];
			}

			$request_args = array(
				'method'    => $request['method'],
				'headers'   => $request['headers'],
				'sslverify' => false,
				'body'      => $request['args'],
				'timeout'   => 15,
			);

			// Log request.
			Logger::log_request( $request['url'], $request_args, 'debug' );

			$response = wp_remote_request( $request['url'], $request_args );

			if ( is_wp_error( $response ) ) {
				$error_message = ( is_wp_error( $response ) ) ? $response->get_error_message() : $response['body'];

				throw new Exception( $error_message, 1 );
			}

			// Log response.
			Logger::log_response( $response, 'debug' );

			$body = self::parse_response( $response );

		} catch ( Exception $e ) {

			throw new Exception( $e->getMessage(), $e->getCode() );
		}

		$response_code = absint( wp_remote_retrieve_response_code( $response ) );

		if ( 401 === $response_code ) {
			throw new Exception( __( 'Reconnect to your Pinterest account', 'pinterest-for-woocommerce' ), 401 );
		}

		if ( ! in_array( absint( $response_code ), array( 200, 201, 204 ), true ) ) {

			$message = '';
			if ( ! empty( $body['message'] ) ) {
				$message = $body['message'];
			}
			if ( ! empty( $body['error_description'] ) ) {
				$message = $body['error_description'];
			}

			/* Translators: Additional message */
			throw new ApiException(
				array(
					'message'       => $message,
					'response_body' => $body,
				),
				$response_code
			);
		}

		return $body;
	}


	/**
	 * Gets and caches the Token from the plugin's settings.
	 *
	 * @return mixed
	 */
	public static function get_token() {
		if ( is_null( self::$token ) ) {
			self::$token = Pinterest_For_Woocommerce()::get_token();
		}

		return self::$token;
	}


	/**
	 * Return array with response body
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response The response to parse.
	 *
	 * @return array
	 *
	 * @throws Exception PHP exception.
	 */
	protected static function parse_response( $response ) {

		if ( ! array_key_exists( 'body', (array) $response ) ) {
			throw new Exception( __( 'Empty body', 'pinterest-for-woocommerce' ), 204 );
		}

		return (array) json_decode( $response['body'] );
	}


	/**
	 * Disconnect the merchant from the Pinterest platform.
	 *
	 * @return mixed
	 */
	public static function disconnect_merchant() {
		return self::make_request( 'catalogs/partner/disconnect', 'POST' );
	}

	/**
	 * Request the verification data from the API and return the response.
	 *
	 * @return mixed
	 */
	public static function domain_verification_data() {
		return self::make_request( 'websites/verification', 'GET' );
	}


	/**
	 * Trigger the (realtime) verification process using the API and return the response.
	 *
	 * @return mixed
	 */
	public static function trigger_verification() {

		$request_url = 'websites/verification/metatag/realtime/';

		$parsed_website = wp_parse_url( get_home_url() );
		$request_url    = add_query_arg( 'website', $parsed_website['host'] . ( $parsed_website['path'] ?? '' ), $request_url );

		return self::make_request( $request_url, 'POST' );
	}


	/**
	 * Request the account data from the API and return the response.
	 *
	 * @return mixed
	 */
	public static function get_account_info() {
		return self::make_request( 'users/me', 'GET' );
	}


	/**
	 * Get the linked business accounts from the API.
	 *
	 * @return array
	 *
	 * @throws ApiException|Exception Pinterest API or PHP exceptions.
	 */
	public static function get_linked_businesses() {
		return self::make_request( 'users/me/businesses', 'GET' );
	}


	/**
	 * Create an advertiser given the accepted TOS terms ID.
	 *
	 * @param string $tos_id The ID of the accepted TOS terms.
	 *
	 * @return mixed
	 */
	public static function create_advertiser( $tos_id ) {

		/**
		 * Advertiser name.
		 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		 */
		$advertiser_name = apply_filters( 'pinterest_for_woocommerce_default_advertiser_name', esc_html__( 'Auto-created by Pinterest for WooCommerce', 'pinterest-for-woocommerce' ) );

		return self::make_request(
			'advertisers/',
			'POST',
			array(
				'tos_id' => $tos_id,
				'name'   => $advertiser_name,
			),
			'ads'
		);
	}


	/**
	 * Connect the advertiser with the platform.
	 *
	 * @param string $advertiser_id The advertiser ID.
	 * @param string $tag_id        The tag ID.
	 *
	 * @return mixed
	 */
	public static function connect_advertiser( $advertiser_id, $tag_id ) {
		return self::make_request(
			"advertisers/{$advertiser_id}/connect/",
			'POST',
			array(
				'tag_id' => $tag_id,
			),
			'ads'
		);
	}


	/**
	 * Disconnect advertiser from the platform.
	 *
	 * @param string $advertiser_id The advertiser ID.
	 * @param string $tag_id        The tag ID.
	 *
	 * @return mixed
	 */
	public static function disconnect_advertiser( $advertiser_id, $tag_id ) {
		return self::make_request(
			"advertisers/{$advertiser_id}/disconnect/",
			'POST',
			array(
				'tag_id' => $tag_id,
			),
			'ads'
		);
	}


	/**
	 * Get the advertiser object from the Pinterest API for the given User ID.
	 *
	 * @param string $pinterest_user the user to request the Advertiser for.
	 *
	 * @return mixed
	 */
	public static function get_advertisers( $pinterest_user = null ) {
		$pinterest_user = ! is_null( $pinterest_user ) ? $pinterest_user : Pinterest_For_Woocommerce()::get_account_id();
		return self::make_request( "advertisers/?owner_user_id={$pinterest_user}", 'GET', array(), 'ads' );
	}


	/**
	 * Get the advertiser's tracking tags.
	 *
	 * @param string $advertiser_id the advertiser_id to request the tags for.
	 *
	 * @return mixed
	 */
	public static function get_advertiser_tags( $advertiser_id ) {
		return self::make_request( "advertisers/{$advertiser_id}/conversion_tags/", 'GET', array(), 'ads' );
	}


	/**
	 * Get the parameters of an existing tag.
	 *
	 * @param string $advertiser_id The advertiser_id to request the tag for.
	 * @param string $tag_id        The tag_id for which we want to get.
	 *
	 * @return mixed
	 */
	public static function get_advertiser_tag( $advertiser_id, $tag_id ) {
		return self::make_request( "advertisers/{$advertiser_id}/conversion_tags/{$tag_id}", 'GET', array(), 'ads' );
	}


	/**
	 * Create a tag for the given advertiser.
	 *
	 * @param string $advertiser_id the advertiser_id to create a tag for.
	 *
	 * @return mixed
	 */
	public static function create_tag( $advertiser_id ) {
		/**
		 * Tag name.
		 */
		$tag_name = apply_filters( 'pinterest_for_woocommerce_default_tag_name', esc_html__( 'Auto-created by Pinterest for WooCommerce', 'pinterest-for-woocommerce' ) );

		return self::make_request(
			"advertisers/{$advertiser_id}/conversion_tags",
			'POST',
			array(
				'name' => $tag_name,
			),
			'ads'
		);
	}


	/**
	 * Update the parameters of an existing tag.
	 *
	 * @param string $tag_id The tag_id for which we want to update the parameters.
	 * @param array  $params The parameters to update.
	 *
	 * @return mixed
	 */
	public static function update_tag( $tag_id, $params = array() ) {
		$advertiser_id = Pinterest_For_Woocommerce()::get_setting( 'tracking_advertiser' );

		if ( ! $advertiser_id || empty( $params ) ) {
			return false;
		}

		$params['id'] = (string) $tag_id;

		return self::make_request(
			"advertisers/{$advertiser_id}/conversion_tags",
			'PATCH',
			$params,
			'ads'
		);
	}


	/**
	 * Update the tags configuration.
	 *
	 * @param string $tag_id The tag_id for which we want to update the configuration.
	 * @param array  $config The configuration to set.
	 *
	 * @return mixed
	 */
	public static function update_tag_config( $tag_id, $config = array() ) {

		if ( empty( $config ) ) {
			return false;
		}

		return self::make_request( "tags/{$tag_id}/configs/", 'PUT', $config, 'ads' );
	}


	/**
	 * Request the account data from the API and return the response.
	 *
	 * @param string $merchant_id The ID of the merchant for the request.
	 *
	 * @return mixed
	 */
	public static function get_merchant( $merchant_id ) {
		return self::make_request( "commerce/product_pin_merchants/{$merchant_id}/", 'GET' );
	}


	/**
	 * Creates a merchant for the authenticated user or updates the existing one. On success the Merchant ID is returned instead of the full merchant object.
	 *
	 * @param array $args Payload to be sent to the request.
	 *
	 * @return mixed
	 */
	public static function update_or_create_merchant( $args ) {
		return self::make_request(
			'catalogs/partner/connect/',
			'POST',
			$args,
		);
	}


	/**
	 * Updates the merchant's feed using the given arguments.
	 *
	 * @param string $merchant_id The merchant ID the feed belongs to.
	 * @param string $feed_id     The ID of the feed to be updated.
	 * @param array  $args        The arguments to be passed to the API request.
	 *
	 * @return mixed
	 */
	public static function update_merchant_feed( $merchant_id, $feed_id, $args ) {

		// phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
		// nosemgrep: audit.php.wp.security.xss.query-arg
		return self::make_request(
			add_query_arg( $args, 'commerce/product_pin_merchants/' . $merchant_id . '/feed/' . $feed_id . '/' ),
			'PUT'
		);
	}

	/**
	 * Disable a feed.
	 *
	 * @since 1.2.13
	 *
	 * @param string $merchant_id     The merchant ID the feed belongs to.
	 * @param string $feed_profile_id The ID of the feed to be disabled.
	 *
	 * @return mixed
	 */
	public static function disable_merchant_feed( $merchant_id, $feed_profile_id ) {
		return self::make_request(
			"catalogs/disable_feed_profile/{$merchant_id}/{$feed_profile_id}/"
		);
	}

	/**
	 * Enable a feed.
	 *
	 * @since 1.2.13
	 *
	 * @param string $merchant_id     The merchant ID the feed belongs to.
	 * @param string $feed_profile_id The ID of the feed to be enabled.
	 *
	 * @return mixed
	 */
	public static function enable_merchant_feed( $merchant_id, $feed_profile_id ) {
		return self::make_request(
			"catalogs/enable_feed_profile/{$merchant_id}/{$feed_profile_id}/"
		);
	}

	/**
	 * Get a merchant's feeds.
	 *
	 * @param string $merchant_id The merchant ID the feed belongs to.
	 * @param bool   $include_disabled Whether to include disabled feeds.
	 *
	 * @return mixed
	 */
	public static function get_merchant_feeds( $merchant_id, $include_disabled = false ) {

		$args = array();

		if ( $include_disabled ) {
			$args['include_disabled'] = 'true';
		}

		return self::make_request(
			"catalogs/{$merchant_id}/feed_profiles/",
			'GET',
			$args,
			'',
			MINUTE_IN_SECONDS
		);
	}

	/**
	 * Invalidate the merchant's feeds cache.
	 *
	 * @param string $merchant_id The merchant ID the feed belongs to.
	 * @param bool   $include_disabled Whether to include disabled feeds.
	 *
	 * @return void
	 */
	public static function invalidate_merchant_feeds_cache( $merchant_id, $include_disabled = false ) {

		$args = array();

		if ( $include_disabled ) {
			$args['include_disabled'] = 'true';
		}

		self::invalidate_cached_response(
			"catalogs/{$merchant_id}/feed_profiles/",
			'GET',
			$args,
			'',
		);
	}

	/**
	 * Get a specific merchant's feed report using the given arguments.
	 *
	 * @param string $merchant_id The merchant ID the feed belongs to.
	 * @param string $feed_id     The ID of the feed.
	 *
	 * @return mixed
	 */
	public static function get_merchant_feed_report( $merchant_id, $feed_id ) {
		return self::make_request(
			"catalogs/datasource/feed_report/{$merchant_id}/",
			'GET',
			array(
				'feed_profile' => $feed_id,
			),
			'',
			MINUTE_IN_SECONDS
		);
	}


	/**
	 * Request the managed map representing all of the error, recommendation, and status messages for catalogs.
	 *
	 * @return mixed
	 */
	public static function get_message_map() {
		return self::make_request( 'catalogs/message_map', 'GET', array(), '', DAY_IN_SECONDS );
	}


	/**
	 * Get billing data information from the advertiser.
	 *
	 * @param string $advertiser_id The advertiser id for which to get the billing data.
	 *
	 * @return mixed
	 */
	public static function get_advertiser_billing_data( $advertiser_id ) {
		return self::make_request( "/advertisers/{$advertiser_id}/billing_data", 'GET', array(), 'ads' );
	}

	/**
	 * Get billing data information from the advertiser.
	 *
	 * @param string $advertiser_id The advertiser id for which to get the billing data.
	 *
	 * @return mixed
	 */
	public static function get_advertiser_billing_profile( $advertiser_id ) {
		return self::make_request( "advertisers/{$advertiser_id}/partners/billing_profiles", 'GET', array(), 'ads' );
	}

	/**
	 * Redeem advertisement offer code ( ads credit ).
	 *
	 * @param string $advertiser_id The advertiser id for which we redeem the offer code.
	 * @param string $offer_code Promotional ads credit offer code.
	 *
	 * @return mixed
	 */
	public static function redeem_ads_offer_code( $advertiser_id, $offer_code ) {
		$request_url = "advertisers/{$advertiser_id}/marketing_offer/{$offer_code}/redeem?is_encoded=true";
		return self::make_request( $request_url, 'POST', array(), 'ads' );
	}

	/**
	 * Validate advertisement offer code ( ads credit ).
	 *
	 * @param string $advertiser_id The advertiser id for which we validate the offer code.
	 * @param string $offer_code Promotional ads credit offer code.
	 *
	 * @return mixed
	 */
	public static function validate_ads_offer_code( $advertiser_id, $offer_code ) {
		$url = "advertisers/{$advertiser_id}/marketing_offer/{$offer_code}/redeem?validate_only=true";
		return self::make_request( $url, 'POST', array(), 'ads' );
	}

	/**
	 * Pull information about available ads credits for advertiser.
	 *
	 * @param string $advertiser_id The advertiser id for which we check the available ads credits.
	 *
	 * @return mixed
	 */
	public static function get_available_discounts( $advertiser_id ) {
		$request_url = "advertisers/{$advertiser_id}/discounts";
		$request_url = add_query_arg( 'active', 'true', $request_url );

		return self::make_request( $request_url, 'GET', array(), 'ads' );
	}

	/**
	 * Pull ads supported countries information from the API.
	 *
	 * @since 1.2.10
	 *
	 * @return array
	 */
	public static function get_list_of_ads_supported_countries() {
		$request_url = 'advertisers/countries';
		return self::make_request( $request_url, 'GET', array(), 'ads', 2 * DAY_IN_SECONDS );
	}
}
