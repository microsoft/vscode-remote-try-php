<?php
/**
 * Api Base.
 *
 * @package uag
 */

namespace UagAdmin\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Api_Base.
 */
abstract class Api_Base extends \WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'uag/v1';

	/**
	 * Register API routes.
	 */
	public function get_api_namespace() {

		return $this->namespace;
	}
}
