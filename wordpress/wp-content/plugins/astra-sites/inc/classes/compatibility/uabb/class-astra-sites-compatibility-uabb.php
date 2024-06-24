<?php
/**
 * Astra Sites Compatibility for 'UABB - Lite'
 *
 * @see  https://wordpress.org/plugins/ultimate-addons-for-beaver-builder-lite/
 *
 * @package Astra Sites
 * @since 3.0.23
 */

/**
 * UABB compatibility for Starter Templates.
 */
class Astra_Sites_Compatibility_UABB {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 3.0.23
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 3.0.23
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'astra_sites_after_plugin_activation', array( $this, 'uabb_activation' ), 10, 2 );
	}

	/**
	 * Disable redirec after installing and activating UABB.
	 *
	 * @return void
	 */
	public function uabb_activation() {
		update_option( 'uabb_lite_redirect', false );
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Sites_Compatibility_UABB::get_instance();
