<?php
/**
 * Pinterest Vendor API
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use \WP_REST_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base Class for registering our endpoints.
 */
class VendorAPI {

	/**
	 * The API namespace
	 *
	 * @var string
	 */
	private $api_namespace = \PINTEREST_FOR_WOOCOMMERCE_API_NAMESPACE . '/v';

	/**
	 * The API version
	 *
	 * @var string
	 */
	private $api_version = \PINTEREST_FOR_WOOCOMMERCE_API_VERSION;

	/**
	 * The base of the endpoint
	 *
	 * @var string
	 */
	public $base;

	/**
	 * The endpoint's methods
	 *
	 * @var string
	 */
	public $methods = 'POST';

	/**
	 * The endpoint_callback
	 *
	 * @var string
	 */
	public $endpoint_callback;

	/**
	 * Specify if the endpoint supports multiple methods
	 *
	 * @var bool
	 */
	protected $supports_multiple_endpoints = false;

	/**
	 * Map with callbacks for each supported method
	 *
	 * @var array
	 */
	protected $endpoint_callbacks_map = array();

	/**
	 * Returns the namespace.
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->api_namespace;
	}

	/**
	 * Returns the version.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->api_version;
	}

	/**
	 * Register endpoint Routes
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		if ( $this->supports_multiple_endpoints ) {
			$this->register_router_multiple_methods();
		} else {
			$this->register_router_single_method();
		}
	}

	/**
	 * Register endpoint route with single method
	 *
	 * @param string $methods The endpoint's methods.
	 * @param string $endpoint_callback The endpoint's callback.
	 *
	 * @since 1.0.13
	 */
	public function register_router_single_method( $methods = '', $endpoint_callback = '' ) {
		$namespace         = $this->api_namespace . $this->api_version;
		$endpoint_callback = empty( $endpoint_callback ) ? $this->endpoint_callback : $endpoint_callback;

		register_rest_route(
			$namespace,
			'/' . $this->base,
			array(
				array(
					'methods'             => empty( $methods ) ? $this->methods : $methods,
					'callback'            => array( $this, $endpoint_callback ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
			)
		);
	}

	/**
	 * Register endpoint route with multiple methods
	 *
	 * @since 1.0.13
	 */
	public function register_router_multiple_methods() {
		foreach ( $this->endpoint_callbacks_map as $callback => $method ) {
			$this->register_router_single_method( $method, $callback );
		}
	}

	/**
	 * Authenticate request
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return boolean
	 */
	public function permissions_check( WP_REST_Request $request ) {
		return current_user_can( 'manage_woocommerce' );
	}
}
