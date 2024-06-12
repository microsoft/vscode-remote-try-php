<?php
/**
 * Cart Abandonment DB
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cart Abandonment DB class.
 */
class Cartflows_Ca_Module_Loader {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor
	 */
	public function __construct() {

		$this->load_module_files();
	}




	/**
	 *  Load required files for module.
	 */
	private function load_module_files() {

		$module_files = array(
			'class-cartflows-ca-tracking.php',
			'class-cartflows-ca-cron.php',
			'class-cartflows-ca-email-templates-table.php',
			'class-cartflows-ca-email-templates.php',
			'class-cartflows-ca-email-schedule.php',
			'class-cartflows-ca-helper.php',
			'class-cartflows-ca-order-table.php',
			'class-cartflows-ca-setting-functions.php',
		);

		foreach ( $module_files as $index => $file ) {

			$filename = CARTFLOWS_CA_DIR . '/modules/cart-abandonment/classes/' . $file;

			if ( file_exists( $filename ) ) {
				include_once $filename;
			}
		}

	}

}

Cartflows_Ca_Module_Loader::get_instance();
