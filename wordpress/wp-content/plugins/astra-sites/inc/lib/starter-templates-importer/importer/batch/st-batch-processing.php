<?php
/**
 * Batch Processing
 *
 * @package ST Importer
 * @since 1.0.14
 */

namespace STImporter\Importer\Batch;

use STImporter\Importer\Batch\ST_Batch_Processing_Gutenberg;


if ( ! class_exists( 'ST_Batch_Processing' ) ) :

	/**
	 * St_Batch_Processing
	 *
	 * @since 1.0.14
	 */
	class ST_Batch_Processing {

		/**
		 * Instance
		 *
		 * @since 1.0.14
		 * @var object Class object.
		 * @access private
		 */
		private static $instance;

		/**
		 * Process All
		 *
		 * @since 1.0.14
		 * @var object Class object.
		 * @access public
		 */
		public static $process_all;

		/**
		 * Last Export Checksums
		 *
		 * @since 2.0.0
		 * @var object Class object.
		 * @access public
		 */
		public $last_export_checksums;

		/**
		 * Sites Importer
		 *
		 * @since 2.0.0
		 * @var object Class object.
		 * @access public
		 */
		public static $process_site_importer;

		/**
		 * Process Single Page
		 *
		 * @since 2.0.0
		 * @var object Class object.
		 * @access public
		 */
		public static $process_single;

		/**
		 * Initiator
		 *
		 * @since 1.0.14
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
		 * @since 1.0.14
		 */
		public function __construct() {

			$this->includes();

			// Start image importing after site import complete.
			add_filter( 'astra_sites_image_importer_skip_image', array( $this, 'skip_image' ), 10, 2 );
			add_action( 'astra_sites_import_complete', array( $this, 'start_process' ) );
			add_action( 'astra_sites_process_single', array( $this, 'start_process_single' ) );
		}

		/**
		 * Include Files
		 *
		 * @since 2.5.0
		 */
		public function includes() {

			// Core Helpers - Image Downloader.
			require_once ST_IMPORTER_DIR . 'importer/helpers/st-importer-image-importer.php';

			self::$process_all           = new \WP_Background_Process_Astra();
			self::$process_single        = new \WP_Background_Process_Astra_Single();
			self::$process_site_importer = new \WP_Background_Process_Astra_Site_Importer();
		}


		/**
		 * Log
		 *
		 * @since 2.0.0
		 *
		 * @param  string $message Log message.
		 * @return void.
		 */
		public function log( $message = '' ) {
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( $message );
			} else {
				astra_sites_error_log( $message );
				update_site_option( 'astra-sites-batch-status-string', $message );
			}
		}

		/**
		 * Start Single Page Import
		 *
		 * @param  int $page_id Page ID .
		 * @since 2.0.0
		 * @return void
		 */
		public function start_process_single( $page_id ) {

			$default_page_builder = 'gutenbrg';

			if ( 'gutenberg' === $default_page_builder ) {
				// Add "gutenberg" in import [queue].
				self::$process_single->push_to_queue(
					array(
						'page_id'  => $page_id,
						'instance' => ST_Batch_Processing_Gutenberg::get_instance(),
					)
				);
			}

			// Dispatch Queue.
			self::$process_single->save()->dispatch();
		}

		/**
		 * Skip Image from Batch Processing.
		 *
		 * @since 1.0.14
		 *
		 * @param  boolean $can_process Batch process image status.
		 * @param  array   $attachment  Batch process image input.
		 * @return boolean
		 */
		public function skip_image( $can_process, $attachment ) {

			if ( isset( $attachment['url'] ) && ! empty( $attachment['url'] ) ) {

				// If image URL contain current site URL? then return true to skip that image from import.
				if ( strpos( $attachment['url'], site_url() ) !== false ) {
					return true;
				}

				$ai_site_url = get_option( 'ast_ai_import_current_url', '' );
				$ai_host_url = '';

				if ( ! empty( $ai_site_url ) ) {
					$url         = wp_parse_url( $ai_site_url );
					$ai_host_url = ! empty( $url['host'] ) ? $url['host'] : '';
				}

				if (
					strpos( $attachment['url'], 'brainstormforce.com' ) !== false ||
					strpos( $attachment['url'], 'wpastra.com' ) !== false ||
					strpos( $attachment['url'], 'sharkz.in' ) !== false ||
					strpos( $attachment['url'], 'websitedemos.net' ) !== false ||
					( ! empty( $ai_host_url ) && strpos( $attachment['url'], $ai_host_url ) !== false )
				) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Start Image Import
		 *
		 * @since 1.0.14
		 *
		 * @return void
		 */
		public function start_process() {

			if ( 'ai' === get_transient( 'astra_sites_current_import_template_type' ) ) {
				return;
			}

			set_transient( 'astra_sites_batch_process_started', 'yes', HOUR_IN_SECONDS );

			/** WordPress Plugin Administration API */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once ABSPATH . 'wp-admin/includes/update.php';

			$this->includes();

			$wxr_id = get_site_option( 'astra_sites_imported_wxr_id', 0 );
			if ( $wxr_id ) {
				wp_delete_attachment( $wxr_id, true );
				delete_option( 'astra_sites_imported_wxr_id' );
			}

			$classes = array();

			// Add "gutenberg" in import [queue].
			$classes[] = ST_Batch_Processing_Gutenberg::get_instance();

			// Add "brizy" in import [queue].

			// Add "misc" in import [queue].
			$classes[] = ST_Batch_Processing_Misc::get_instance();

			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'Batch Process Started..' );
				// Process all classes.
				foreach ( $classes as $key => $class ) {
					if ( method_exists( $class, 'import' ) ) {
						$class->import();
					}
				}
				\WP_CLI::line( 'Batch Process Complete!' );
			} else {
				// Add all classes to batch queue.
				foreach ( $classes as $key => $class ) {
					self::$process_all->push_to_queue( $class );
				}

				// Dispatch Queue.
				self::$process_all->save()->dispatch();
			}

		}

		/**
		 * Get all post id's
		 *
		 * @since 1.0.14
		 *
		 * @param  array $post_types Post types.
		 * @return array
		 */
		public static function get_pages( $post_types = array() ) {

			if ( $post_types ) {
				$args = array(
					'post_type'      => $post_types,

					// Query performance optimization.
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => '_astra_sites_imported_post',
							'value'   => '1',
							'compare' => '=',
						),
					),
				);

				$query = new \WP_Query( $args );

				// Have posts?
				if ( $query->have_posts() ) :

					return $query->posts;

				endif;
			}

			return null;
		}

		/**
		 * Get Supporting Post Types..
		 *
		 * @since 1.3.7
		 * @param  integer $feature Feature.
		 * @return array
		 */
		public static function get_post_types_supporting( $feature ) {
			global $_wp_post_type_features;

			$post_types = array_keys(
				wp_filter_object_list( $_wp_post_type_features, array( $feature => true ) )
			);

			return $post_types;
		}





	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	ST_Batch_Processing::get_instance();

endif;
