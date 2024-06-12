<?php
/**
 * Zip AI - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package zip-ai
 */

namespace STImporter\Resetter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Module Class.
 */
class ST_Resetter {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator of this class.
	 *
	 * @since 1.0.0
	 * @return object initialized object of this class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Backup our existing settings.
	 */
	public static function backup_settings() {

		$file_name    = 'astra-sites-backup-' . gmdate( 'd-M-Y-h-i-s' ) . '.json';
		$old_settings = get_option( 'astra-settings', array() );
		$upload_dir   = self::log_dir();
		$upload_path  = trailingslashit( $upload_dir['path'] );
		$log_file     = $upload_path . $file_name;
		$file_system  = self::get_filesystem();

		// If file system fails? Then take a backup in site option.
		if ( false === $file_system->put_contents( $log_file, wp_json_encode( $old_settings ), FS_CHMOD_FILE ) ) {
			update_option( 'astra_sites_' . $file_name, $old_settings, 'no' );
		}

		return $log_file;
	}

	/**
	 * Log file directory
	 *
	 * @since 1.0.0
	 * @param  string $dir_name Directory Name.
	 * @return array    Uploads directory array.
	 */
	public static function log_dir( $dir_name = 'st-importer' ) {

		$upload_dir = wp_upload_dir();

		// Build the paths.
		$dir_info = array(
			'path' => $upload_dir['basedir'] . '/' . $dir_name . '/',
			'url'  => $upload_dir['baseurl'] . '/' . $dir_name . '/',
		);

		// Create the upload dir if it doesn't exist.
		if ( ! file_exists( $dir_info['path'] ) ) {

			// Create the directory.
			wp_mkdir_p( $dir_info['path'] );

			// Add an index file for security.
			self::get_filesystem()->put_contents( $dir_info['path'] . 'index.html', '' );

			// Add an .htaccess for security.
			self::get_filesystem()->put_contents( $dir_info['path'] . '.htaccess', 'deny from all' );
		}

		return $dir_info;
	}

	/**
	 * Get an instance of WP_Filesystem_Direct.
	 *
	 * @since 1.0.0
	 * @return object A WP_Filesystem_Direct instance.
	 */
	public static function get_filesystem() {
		global $wp_filesystem;

		require_once ABSPATH . '/wp-admin/includes/file.php';

		WP_Filesystem();

		return $wp_filesystem;
	}

	/**
	 * Reset site options
	 *
	 * @since 1.0.0
	 *
	 * @param array $options option aray.
	 * @return void
	 */
	public static function reset_site_options( $options = array() ) {

		if ( is_array( $options ) && ! empty( $options ) ) {
			return;
		}

		if ( $options ) {
			foreach ( $options as $option_key => $option_value ) {
				delete_option( $option_key );
			}
		}
	}

	/**
	 * Reset customizer data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function reset_customizer_data() {
		delete_option( 'astra-settings' );
	}

	/**
	 * Reset widgets data
	 *
	 * @since 1.0.0
	 *
	 * @param array $old_widgets_data widget data.
	 * @return void
	 */
	public static function reset_widgets_data( $old_widgets_data = array() ) {

		$old_widget_ids = array();
		foreach ( $old_widgets_data as $old_sidebar_key => $old_widgets ) {
			if ( ! empty( $old_widgets ) && is_array( $old_widgets ) ) {
				$old_widget_ids = array_merge( $old_widget_ids, $old_widgets );
			}
		}

		// Process if not empty.
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );

		if ( ! empty( $old_widget_ids ) && ! empty( $sidebars_widgets ) ) {

			foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
				$widgets = (array) $widgets;

				if ( ! empty( $widgets ) && is_array( $widgets ) ) {
					foreach ( $widgets as $widget_id ) {

						if ( in_array( $widget_id, $old_widget_ids, true ) ) {

							// Move old widget to inacitve list.
							$sidebars_widgets['wp_inactive_widgets'][] = $widget_id;

							// Remove old widget from sidebar.
							$sidebars_widgets[ $sidebar_id ] = array_diff( $sidebars_widgets[ $sidebar_id ], array( $widget_id ) );
						}
					}
				}
			}

			update_option( 'sidebars_widgets', $sidebars_widgets );
		}
	}

	/**
	 * Reset posts in chunks.
	 *
	 * @since 1.0.0
	 */
	public static function reset_posts() {

		ST_Resetter::get_instance()->start_error_handler();

		// Suspend bunches of stuff in WP core.
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		wp_suspend_cache_invalidation( true );

		$posts = json_decode( stripslashes( sanitize_text_field( $_POST['ids'] ) ), true ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $key => $post_id ) {
				$post_id = absint( $post_id );
				if ( $post_id ) {
					$post_type = get_post_type( $post_id );
					do_action( 'astra_sites_before_delete_imported_posts', $post_id, $post_type );
					wp_delete_post( $post_id, true );
				}
			}
		}

		// Re-enable stuff in core.
		wp_suspend_cache_invalidation( false );
		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		self::stop_error_handler();
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
	public static function stop_error_handler() {
		// Restore the error handlers.
		restore_error_handler();
		restore_exception_handler();
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

		if ( wp_doing_ajax() ) {
			wp_send_json_error(
				array(
					'message' => __( 'There was an error your website.', 'st-importer', 'astra-sites' ),
					'stack'   => array(
						'error-message' => $error,
						'error'         => $e,
					),
				)
			);
		}
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

		if ( wp_doing_ajax() ) {
			wp_send_json_error(
				array(
					'message' => __( 'There was an error on your website.', 'st-importer', 'astra-sites' ),
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
	 * Reset terms and forms.
	 *
	 * @since 1.0.0
	 */
	public static function reset_terms_and_forms() {

		ST_Resetter::get_instance()->start_error_handler();

		$terms = self::astra_sites_get_reset_term_data();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $key => $term_id ) {
				$term_id = absint( $term_id );
				if ( $term_id ) {
					$term = get_term( $term_id );
					if ( ! is_wp_error( $term ) && ! empty( $term ) && is_object( $term ) ) {

						do_action( 'astra_sites_before_delete_imported_terms', $term_id, $term );

						wp_delete_term( $term_id, $term->taxonomy );
					}
				}
			}
		}

		$forms = self::astra_sites_get_reset_form_data();

		if ( ! empty( $forms ) ) {
			foreach ( $forms as $key => $post_id ) {
				$post_id = absint( $post_id );
				if ( $post_id ) {

					do_action( 'astra_sites_before_delete_imported_wp_forms', $post_id );
					wp_delete_post( $post_id, true );
				}
			}
		}

		self::stop_error_handler();
	}

	/**
	 * Get all the forms to be reset.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function astra_sites_get_reset_form_data() {
		global $wpdb;

		$form_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_astra_sites_imported_wp_forms'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need this to get all the WP forms. Traditional WP_Query would have been expensive here.

		return $form_ids;
	}

	/**
	 * Get all the terms to be reset.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function astra_sites_get_reset_term_data() {
		global $wpdb;

		$term_ids = $wpdb->get_col( "SELECT term_id FROM {$wpdb->termmeta} WHERE meta_key='_astra_sites_imported_term'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need this to get all the terms and taxonomy. Traditional WP_Query would have been expensive here.

		return $term_ids;
	}
}
