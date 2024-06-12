<?php
/**
 * Astra Sites
 *
 * @since  3.0.23
 * @package Astra Sites
 */

namespace AiBuilder\Inc\Classes\Importer;

use AiBuilder\Inc\Traits\Instance;
use AiBuilder\Inc\Classes\Ai_Builder_Importer_Log;

define( 'ST_ERROR_FATALS', E_ERROR | E_PARSE | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR );

/**
 * Ai_Builder_Error_Handler
 */
class Ai_Builder_Error_Handler {

	use Instance;

	/**
	 * Constructor
	 */
	public function __construct() {

		require_once AI_BUILDER_DIR . 'inc/classes/ai-builder-importer-log.php';
		if ( true === astra_sites_has_import_started() ) {
			$this->start_error_handler();
		}

		add_action( 'shutdown', array( $this, 'stop_handler' ) );
	}

	/**
	 * Stop the shutdown handlers.
	 *
	 * @return void
	 */
	public function stop_handler() {
		if ( true === astra_sites_has_import_started() ) {
			$this->stop_error_handler();
		}
	}

	/**
	 * Start the error handling.
	 */
	public function start_error_handler() {
		if ( ! interface_exists( 'Throwable' ) ) {
			// Fatal error handler for PHP < 7.
			register_shutdown_function( array( $this, 'shutdown_handler' ) );
		}

		// Fatal error handler for PHP >= 7, and uncaught exception handler for all PHP versions.
		set_exception_handler( array( $this, 'exception_handler' ) );
	}

	/**
	 * Stop and restore the error handlers.
	 */
	public function stop_error_handler() {
		// Restore the error handlers.
		restore_error_handler();
		restore_exception_handler();
	}

	/**
	 * Uncaught exception handler.
	 *
	 * In PHP >= 7 this will receive a Throwable object.
	 * In PHP < 7 it will receive an Exception object.
	 *
	 * @throws Exception Exception that is catched.
	 * @param Throwable|Exception $e The error or exception.
	 */
	public function exception_handler( $e ) {
		if ( is_a( $e, 'Exception' ) ) {
			$error = 'Uncaught Exception';
		} else {
			$error = 'Uncaught Error';
		}

		Ai_Builder_Importer_Log::add( 'There was an error on website: ' . $error );
		Ai_Builder_Importer_Log::add( $e );

		if ( wp_doing_ajax() ) {
			wp_send_json_error(
				array(
					'message' => __( 'There was an error on your website.', 'ai-builder', 'astra-sites' ),
					'stack'   => array(
						'error-message' => sprintf(
							'%s: %s',
							$error,
							$e->getMessage()
						),
						'file'          => $e->getFile(),
						'line'          => $e->getLine(),
						'trace'         => $e->getTrace(),
					),
				)
			);
		}

		throw $e;
	}

	/**
	 * Displays fatal error output for sites running PHP < 7.
	 */
	public function shutdown_handler() {
		$e = error_get_last();

		if ( empty( $e ) || ! ( $e['type'] & ST_ERROR_FATALS ) ) {
			return;
		}

		if ( $e['type'] & E_RECOVERABLE_ERROR ) {
			$error = 'Catchable fatal error';
		} else {
			$error = 'Fatal error';
		}

		Ai_Builder_Importer_Log::add( 'There was an error on website: ' . $error );
		Ai_Builder_Importer_Log::add( $e );

		if ( wp_doing_ajax() ) {
			wp_send_json_error(
				array(
					'message' => __( 'There was an error your website.', 'ai-builder', 'astra-sites' ),
					'stack'   => array(
						'error-message' => $error,
						'error'         => $e,
					),
				)
			);
		}
	}
}

/**
* Kicking this off by calling 'get_instance()' method
*/
Ai_Builder_Error_Handler::Instance();
