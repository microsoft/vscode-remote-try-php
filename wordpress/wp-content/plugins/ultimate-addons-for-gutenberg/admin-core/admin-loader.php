<?php
/**
 * Uag Admin.
 *
 * @package Uag
 */

namespace UagAdmin;

use UagAdmin\Api\Api_Init;
use UagAdmin\Ajax\Ajax_Init;
use UagAdmin\Inc\Admin_Menu;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Admin_Loader.
 */
class Admin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

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
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		if ( ! class_exists( $class_to_load ) ) {
			$filename = strtolower(
				preg_replace(
					array( '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
					array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
					$class_to_load
				)
			);

			$file = UAG_ADMIN_DIR . $filename . '.php';

			// if the file redable, include it.
			if ( is_readable( $file ) ) {
				include $file;
			}
		}
	}

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->define_constants();
		$this->setup_classes();
	}

	/**
	 * Include required classes.
	 */
	public function define_constants() {
		define( 'UAG_ADMIN_DIR', UAGB_DIR . 'admin-core/' );
		define( 'UAG_ADMIN_URL', UAGB_URL . 'admin-core/' );
	}

	/**
	 * Include required classes.
	 */
	public function setup_classes() {

		/* Init API */
		Api_Init::get_instance();

		if ( is_admin() ) {
			/* Setup Menu */
			Admin_Menu::get_instance();

			/* Ajax init */
			Ajax_Init::get_instance();
		}
	}
}
Admin_Loader::get_instance();
