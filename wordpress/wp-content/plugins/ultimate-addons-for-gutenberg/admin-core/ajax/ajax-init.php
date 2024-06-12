<?php
/**
 * Ajax Initialize.
 *
 * @package uag
 */

namespace UagAdmin\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Ajax_Init.
 */
class Ajax_Init {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Dynamic properties container
	 *
	 * @var array
	 * @since 2.7.10
	 */
	private $dynamic_properties = array();

	/**
	 * Initiator
	 *
	 * @since 2.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->initialize_hooks();
	}

	/**
	 * Init Hooks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function initialize_hooks() {
		$this->register_all_ajax_events();
	}

	/**
	 * Init dynamic property setter
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 *
	 * @since 2.7.10
	 * @return void
	 */
	public function __set( $name, $value ) {
		$this->dynamic_properties[ $name ] = $value;
	}

	/**
	 * Init dynamic property getter
	 *
	 * @param string $name Property name.
	 *
	 * @since 2.7.10
	 * @return mixed Property value if set, null otherwise.
	 */
	public function __get( $name ) {
		return $this->dynamic_properties[ $name ] ? $this->dynamic_properties[ $name ] : null;
	}

	/**
	 * Register Ajax actions.
	 */
	public function register_all_ajax_events() {

		$controllers = array(
			'UagAdmin\Ajax\Common_Settings',
		);

		foreach ( $controllers as $controller ) {
			$this->$controller = $controller::get_instance();
			$this->{$controller}->register_ajax_events();
		}
	}
}

Ajax_Init::get_instance();
