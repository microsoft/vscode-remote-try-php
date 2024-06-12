<?php
/**
 * AI Builder Compatibility for 'UAG'
 *
 * @see  https://wordpress.org/plugins/ultimate-addons-for-gutenberg/
 *
 * @package AI Builder
 * @since 3.0.15
 */

/**
 * UAG compatibility for Starter Templates.
 */
class Ai_Builder_Compatibility_UAG {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 3.0.15
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 3.0.15
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
		add_action( 'astra_sites_after_plugin_activation', array( $this, 'uag_activation' ), 10, 2 );
	}

	/**
	 * Disable redirec after installing and activating UAG.
	 *
	 * @return void
	 */
	public function uag_activation() {
		update_option( '__uagb_do_redirect', false );
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Ai_Builder_Compatibility_UAG::get_instance();
