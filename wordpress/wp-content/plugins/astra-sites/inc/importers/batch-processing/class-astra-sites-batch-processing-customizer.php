<?php
/**
 * Customizer batch import tasks.
 *
 * @package Astra Sites
 * @since 3.0.22
 */

use STImporter\Importer\Helpers\ST_Image_Importer;

/**
 * Astra_Sites_Batch_Processing_Customizer
 *
 * @since 3.0.22
 */
class Astra_Sites_Batch_Processing_Customizer {

	/**
	 * Instance
	 *
	 * @since 3.0.22
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 3.0.22
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
	 * @since 3.0.22
	 */
	public function __construct() {}

	/**
	 * Import
	 *
	 * @since 3.0.22
	 * @return void
	 */
	public function import() {

		if ( defined( 'WP_CLI' ) ) {
			WP_CLI::line( 'Processing "Customizer" Batch Import' );
		}

		Astra_Sites_Importer_Log::add( '---- Processing batch process for Customizer start ----' );
		self::images_download();
		Astra_Sites_Importer_Log::add( '---- Processing batch process for Customizer done ----' );
	}

	/**
	 * Downloads images from customizer.
	 */
	public static function images_download() {
		$options = get_option( 'astra-settings', array() );
		array_walk_recursive(
			$options,
			function ( &$value ) {
				if ( ! is_array( $value ) && astra_sites_is_valid_image( $value ) ) {
					$downloaded_image = ST_Image_Importer::get_instance()->import(
						array(
							'url' => $value,
							'id'  => 0,
						)
					);
					$value            = $downloaded_image['url'];
				}
			}
		);

		// Updated settings.
		update_option( 'astra-settings', $options );
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Sites_Batch_Processing_Customizer::get_instance();
