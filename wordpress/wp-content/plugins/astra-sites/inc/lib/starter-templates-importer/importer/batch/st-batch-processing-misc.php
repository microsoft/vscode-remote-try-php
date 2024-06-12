<?php
/**
 * Misc batch import tasks.
 *
 * @package Astra Sites
 * @since 1.1.6
 */

namespace STImporter\Importer\Batch;

use STImporter\Importer\Batch\ST_Replace_Images;

if ( ! class_exists( 'ST_Batch_Processing_Misc' ) ) :

	/**
	 * ST_Batch_Processing_Misc
	 *
	 * @since 1.1.6
	 */
	class ST_Batch_Processing_Misc {

		/**
		 * Instance
		 *
		 * @since 1.1.6
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.1.6
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
		 * @since 1.1.6
		 */
		public function __construct() {}

		/**
		 * Import
		 *
		 * @since 1.1.6
		 * @return array<string, mixed>
		 */
		public function import() {

			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'Processing "MISC" Batch Import' );
			}

			if ( 'ai' !== get_transient( 'astra_sites_current_import_template_type' ) ) {
				return array(
					'success' => true,
					'msg'     => __( 'Template Type is not a AI.', 'st-importer', 'astra-sites' ),
				);
			}

			return self::replace_images();
		}

		/**
		 * Replace Images
		 *
		 * @since 4.1.0
		 * @return mixed
		 */
		public static function replace_images() {

			if ( false === get_option( 'astra_sites_ai_import_started', false ) ) {
				return array(
					'success' => false,
					'msg'     => __( 'Required flags are not set.', 'st-importer', 'astra-sites' ),
				);
			}

			return ST_Replace_Images::get_instance()->replace_images();

		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	ST_Batch_Processing_Misc::get_instance();

endif;
