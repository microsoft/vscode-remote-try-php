<?php
/**
 * Image Background Process
 *
 * @package Astra Sites
 * @since 1.0.11
 */

if ( class_exists( 'WP_Background_Process' ) ) :
	/**
	 * Image Background Process
	 *
	 * @since 1.0.11
	 */
	class WP_Background_Process_Astra extends WP_Background_Process {

		/**
		 * Image Process
		 *
		 * @var string
		 */
		protected $action = 'image_process';

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @since 1.0.11
		 *
		 * @param object $process Queue item object.
		 * @return mixed
		 */
		protected function task( $process ) {

			if ( method_exists( $process, 'import' ) ) {
				$process->import();
			}

			return false;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 *
		 * @since 1.0.11
		 */
		protected function complete() {

			parent::complete();

			do_action( 'astra_sites_batch_process_complete' );

			update_option( 'astra_sites_batch_process_complete', 'yes' );

			// Delete Log file.
			delete_option( 'astra_sites_recent_import_log_file' );
		}

	}

endif;
