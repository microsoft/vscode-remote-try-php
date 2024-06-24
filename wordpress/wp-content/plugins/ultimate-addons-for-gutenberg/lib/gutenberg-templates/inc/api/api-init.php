<?php
/**
 * INitialize API.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace Gutenberg_Templates\Inc\Api;

use Gutenberg_Templates\Inc\Traits\Instance;

/**
 * Api_Base
 *
 * @since 0.0.1
 */
class Api_Init {

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
			'\Gutenberg_Templates\Inc\Api\Category',
			'\Gutenberg_Templates\Inc\Api\Description',
			'\Gutenberg_Templates\Inc\Api\PageDescription',
			'\Gutenberg_Templates\Inc\Api\Keywords',
			'\Gutenberg_Templates\Inc\Api\Images',
			'\Gutenberg_Templates\Inc\Api\Settings',
			'\Gutenberg_Templates\Inc\Api\Favorite',
			'Gutenberg_Templates\Inc\Api\Do_It_Later',
			'\Gutenberg_Templates\Inc\Api\Pages',
			'\Gutenberg_Templates\Inc\Api\RevokeAccess',
			'\Gutenberg_Templates\Inc\Api\Blocks',
			'\Gutenberg_Templates\Inc\Api\Initialize_Setup',
		);

		foreach ( $controllers as $controller ) {

			$this->controller = $controller::instance();

			$this->controller->register_routes();
		}
	}
}
