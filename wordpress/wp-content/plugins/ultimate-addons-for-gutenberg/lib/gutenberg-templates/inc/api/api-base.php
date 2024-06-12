<?php
/**
 * API base.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace Gutenberg_Templates\Inc\Api;

/**
 * Api_Base
 *
 * @since 0.0.1
 */
abstract class Api_Base extends \WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'gutenberg-templates/v1';

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
	}

	/**
	 * Get API namespace.
	 *
	 * @since 0.0.1
	 * @return string
	 */
	public function get_api_namespace() {

		return $this->namespace;
	}   
}
