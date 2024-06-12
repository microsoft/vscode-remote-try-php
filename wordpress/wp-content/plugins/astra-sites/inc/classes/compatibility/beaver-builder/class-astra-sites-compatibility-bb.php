<?php
/**
 * Astra Sites Compatibility for 'Beaver Builder'
 *
 * @package Astra Sites
 * @since 3.0.21
 */

defined( 'ABSPATH' ) || exit;

/**
 * Beaver Builder Compatibility
 *
 * @since 3.0.21
 */
class Astra_Sites_Compatibility_BB {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 3.0.21
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 3.0.21
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
	 * @since 3.0.21
	 */
	public function __construct() {
		add_action( 'fl_builder_activated', array( $this, 'bb_activated' ) );
	}

	/**
	 * Disable redirection for Beaver Builder plugin when activated via Starter templates import process.
	 */
	public function bb_activated() {
		if ( astra_sites_has_import_started() ) {
			delete_transient( '_fl_builder_activation_admin_notice' );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Sites_Compatibility_BB::get_instance();
