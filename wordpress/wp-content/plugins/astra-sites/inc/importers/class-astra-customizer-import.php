<?php
/**
 * Customizer Data importer class.
 *
 * @since  1.0.0
 * @package Astra Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Data importer class.
 *
 * @since  1.0.0
 */
class Astra_Customizer_Import {

	/**
	 * Instance of Astra_Customizer_Import
	 *
	 * @since  1.0.0
	 * @var Astra_Customizer_Import
	 */
	private static $instance = null;

	/**
	 * Instantiate Astra_Customizer_Import
	 *
	 * @since  1.0.0
	 * @return (Object) Astra_Customizer_Import
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Import customizer options.
	 *
	 * @since  1.0.0
	 *
	 * @param  (Array) $options customizer options from the demo.
	 */
	public function import( $options ) {

		// Update Astra Theme customizer settings.
		if ( isset( $options['astra-settings'] ) ) {
			update_option( 'astra-settings', $options['astra-settings'] );
		}

		// Add Custom CSS.
		if ( isset( $options['custom-css'] ) ) {
			wp_update_custom_css_post( $options['custom-css'] );
		}

	}
}
