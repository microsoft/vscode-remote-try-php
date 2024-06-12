<?php
/**
 * Cleanup batch import tasks.
 *
 * @package Astra Sites
 * @since 4.0.11
 */

if ( ! class_exists( 'Astra_Sites_Batch_Processing_Cleanup' ) ) :

	/**
	 * Astra_Sites_Batch_Processing_Cleanup
	 *
	 * @since 4.0.11
	 */
	class Astra_Sites_Batch_Processing_Cleanup {

		/**
		 * Constructor
		 *
		 * @since 4.0.11
		 */
		public function __construct() {}

		/**
		 * Import
		 *
		 * @since 4.0.11
		 * @return void
		 */
		public function import() {

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Processing "Cleanup" Batch Import' );
			}

			update_option( 'st_attachments', array(), 'no' );
			delete_option( 'st_attachments_offset' );
		}
	}

endif;
