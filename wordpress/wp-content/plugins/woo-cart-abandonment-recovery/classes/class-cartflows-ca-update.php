<?php
/**
 * Update Compatibility
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Cartflows_Ca_Update' ) ) :

	/**
	 * CartFlows CA Update initial setup
	 *
	 * @since 1.0.0
	 */
	class Cartflows_Ca_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
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
			add_action( 'admin_init', __CLASS__ . '::init' );
		}

		/**
		 *  Create tables if not exists and seed default settings.
		 */
		public static function update_table_with_default_settings() {

			$cartflows_loader = CARTFLOWS_CA_Loader::get_instance();
			$cartflows_loader->initialize_cart_abandonment_tables();
			$cartflows_loader->update_default_settings();
		}


		/**
		 * Init
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function init() {

			do_action( 'cartflows_ca_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'wcf_ca_version', false );

			// Update auto saved version number.
			if ( ! $saved_version ) {
				self::update_table_with_default_settings();
				update_option( 'wcf_ca_version', CARTFLOWS_CA_VER );
				return;
			}

			// If equals then return.
			if ( version_compare( $saved_version, CARTFLOWS_CA_VER, '=' ) ) {
				return;
			}

			if ( version_compare( $saved_version, '1.2.16', '<' ) ) {
				update_option( 'wcf_ca_show_weekly_report_email_notice', 'yes' );
			}

			// Update auto saved version number.
			update_option( 'wcf_ca_version', CARTFLOWS_CA_VER );

			self::update_table_with_default_settings();

			do_action( 'cartflows_ca_update_after' );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Cartflows_Ca_Update::get_instance();

endif;
