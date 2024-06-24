<?php
/**
 * INitialize API.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace AiBuilder\Inc\Api;

use AiBuilder\Inc\Traits\Instance;

/**
 * Api_Base
 *
 * @since 0.0.1
 */
class ApiInit {

	use Instance;

	/**
	 * Controller object.
	 *
	 * @var object class.
	 */
	public $controller = null;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		// REST API extensions init.
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register API routes.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function register_routes() {

		$controllers = array(
			// '\Ai_Builder\Inc\Api\Category',
		);

		foreach ( $controllers as $controller ) {

			$this->controller = $controller::instance();

			$this->controller->register_routes();
		}
	}
}
