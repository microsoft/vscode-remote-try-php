<?php
/**
 * AiBuilder Ajax Initialize.
 *
 * @package AiBuilder
 */

namespace AiBuilder\Inc\Ajax;

use AiBuilder\Inc\Traits\Instance;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Init.
 */
class AjaxInit {


	use Instance;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->initialize_hooks();
	}

	/**
	 * Init Hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function initialize_hooks() {

		$this->register_all_ajax_events();
	}

	/**
	 * Register API routes.
	 */
	public function register_all_ajax_events() {

		$controllers = array(
			'AiBuilder\Inc\Ajax\Importer',
			'AiBuilder\Inc\Ajax\Plugin',
		);

		foreach ( $controllers as $controller ) {
			$this->$controller = $controller::Instance();
			$this->$controller->register_ajax_events();
		}
	}
}

AjaxInit::Instance();
