<?php
/**
 * Astra Sites Importer
 *
 * @since  1.0.0
 * @package Astra Sites
 */

use STImporter\Importer\ST_Importer_Helper;
use STImporter\Importer\WXR_Importer\ST_WXR_Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Sites_Importer' ) ) {

	/**
	 * Astra Sites Importer
	 */
	class Astra_Sites_Importer {

		/**
		 * Instance
		 *
		 * @since  1.0.0
		 * @var (Object) Class object
		 */
		public static $instance = null;

		/**
		 * Set Instance
		 *
		 * @since  1.0.0
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-importer-log.php';
			require_once ASTRA_SITES_DIR . 'inc/importers/class-astra-sites-helper.php';
			require_once ASTRA_SITES_DIR . 'inc/importers/class-astra-widget-importer.php';
			require_once ASTRA_SITES_DIR . 'inc/importers/class-astra-customizer-import.php';
			require_once ASTRA_SITES_DIR . 'inc/importers/class-astra-site-options-import.php';

			// Hooks in AJAX.
			add_action( 'wp_ajax_astra-sites-import-wpforms', array( $this, 'import_wpforms' ) );
			add_action( 'wp_ajax_astra-sites-import-cartflows', array( $this, 'import_cartflows' ) );
			add_action( 'astra_sites_import_complete', array( $this, 'clear_related_cache' ) );

			require_once ASTRA_SITES_DIR . 'inc/importers/batch-processing/class-astra-sites-batch-processing.php';

			if ( version_compare( get_bloginfo( 'version' ), '5.1.0', '>=' ) ) {
				add_filter( 'http_request_timeout', array( $this, 'set_timeout_for_images' ), 10, 2 ); //phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.http_request_timeout -- We need this to avoid timeout on slow servers while installing theme, plugin etc.
			}

			add_action( 'init', array( $this, 'disable_default_woo_pages_creation' ), 2 );
			add_filter( 'upgrader_package_options', array( $this, 'plugin_install_clear_directory' ) );
		}

		/**
		 * Delete imported posts
		 *
		 * @since 1.3.0
		 * @since 1.4.0 The `$post_id` was added.
		 * Note: This function can be deleted after a few releases since we are performing the delete operation in chunks.
		 *
		 * @param  integer $post_id Post ID.
		 * @return void
		 */
		public function delete_imported_posts( $post_id = 0 ) {

			if ( wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : $post_id;

			$message = 'Deleted - Post ID ' . $post_id . ' - ' . get_post_type( $post_id ) . ' - ' . get_the_title( $post_id );

			$message = '';
			if ( $post_id ) {

				$post_type = get_post_type( $post_id );
				$message   = 'Deleted - Post ID ' . $post_id . ' - ' . $post_type . ' - ' . get_the_title( $post_id );

				do_action( 'astra_sites_before_delete_imported_posts', $post_id, $post_type );

				Astra_Sites_Importer_Log::add( $message );
				wp_delete_post( $post_id, true );
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}

		/**
		 * Delete imported WP forms
		 *
		 * @since 1.3.0
		 * @since 1.4.0 The `$post_id` was added.
		 * Note: This function can be deleted after a few releases since we are performing the delete operation in chunks.
		 *
		 * @param  integer $post_id Post ID.
		 * @return void
		 */
		public function delete_imported_wp_forms( $post_id = 0 ) {

			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : $post_id;

			$message = '';
			if ( $post_id ) {

				do_action( 'astra_sites_before_delete_imported_wp_forms', $post_id );

				$message = 'Deleted - Form ID ' . $post_id . ' - ' . get_post_type( $post_id ) . ' - ' . get_the_title( $post_id );
				Astra_Sites_Importer_Log::add( $message );
				wp_delete_post( $post_id, true );
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}

		/**
		 * Delete imported terms
		 *
		 * @since 1.3.0
		 * @since 1.4.0 The `$post_id` was added.
		 * Note: This function can be deleted after a few releases since we are performing the delete operation in chunks.
		 *
		 * @param  integer $term_id Term ID.
		 * @return void
		 */
		public function delete_imported_terms( $term_id = 0 ) {
			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$term_id = isset( $_REQUEST['term_id'] ) ? absint( $_REQUEST['term_id'] ) : $term_id;

			$message = '';
			if ( $term_id ) {
				$term = get_term( $term_id );
				if ( ! is_wp_error( $term ) && ! empty( $term ) && is_object( $term ) ) {

					do_action( 'astra_sites_before_delete_imported_terms', $term_id, $term );

					$message = 'Deleted - Term ' . $term_id . ' - ' . $term->name . ' ' . $term->taxonomy;
					Astra_Sites_Importer_Log::add( $message );
					wp_delete_term( $term_id, $term->taxonomy );
				}
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}

		/**
		 * Delete related transients
		 *
		 * @since 3.1.3
		 */
		public function delete_related_transient() {
			delete_transient( 'astra_sites_batch_process_started' );
			Astra_Sites_File_System::get_instance()->delete_demo_content();
			delete_option( 'ast_ai_import_current_url' );
			delete_option( 'astra_sites_ai_import_started' );
		}

		/**
		 * Delete directory when installing plugin.
		 *
		 * Set by enabling `clear_destination` option in the upgrader.
		 *
		 * @since 3.0.10
		 * @param array $options Options for the upgrader.
		 * @return array $options The options.
		 */
		public function plugin_install_clear_directory( $options ) {
			if ( true !== astra_sites_has_import_started() ) {
				return $options;
			}
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', 'ajax_nonce' );
			if ( isset( $_REQUEST['clear_destination'] ) && 'true' === $_REQUEST['clear_destination'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is a callback filter while performing plugin install action - https://developer.wordpress.org/reference/hooks/upgrader_package_options/, We don't quite have access to the nonce here. We are skipping it here.
				$options['clear_destination'] = true;
			}

			return $options;
		}

		/**
		 * Restrict WooCommerce Pages Creation process
		 *
		 * Why? WooCommerce creates set of pages on it's activation
		 * These pages are re created via our XML import step.
		 * In order to avoid the duplicacy we restrict these page creation process.
		 *
		 * @since 3.0.0
		 */
		public function disable_default_woo_pages_creation() {
			if ( astra_sites_has_import_started() ) {
				add_filter( 'woocommerce_create_pages', '__return_empty_array' );
			}
		}

		/**
		 * Set the timeout for the HTTP request by request URL.
		 *
		 * E.g. If URL is images (jpg|png|gif|jpeg) are from the domain `https://websitedemos.net` then we have set the timeout by 30 seconds. Default 5 seconds.
		 *
		 * @since 1.3.8
		 *
		 * @param int    $timeout_value Time in seconds until a request times out. Default 5.
		 * @param string $url           The request URL.
		 */
		public function set_timeout_for_images( $timeout_value, $url ) {

			// URL not contain `https://websitedemos.net` then return $timeout_value.
			if ( strpos( $url, 'https://websitedemos.net' ) === false ) {
				return $timeout_value;
			}

			// Check is image URL of type jpg|png|gif|jpeg.
			if ( astra_sites_is_valid_image( $url ) ) {
				$timeout_value = 300;
			}

			return $timeout_value;
		}

		/**
		 * Change flow status
		 *
		 * @since 2.0.0
		 *
		 * @param  array $args Flow query args.
		 * @return array Flow query args.
		 */
		public function change_flow_status( $args ) {
			$args['post_status'] = 'publish';
			return $args;
		}

		/**
		 * Track Flow
		 *
		 * @since 2.0.0
		 *
		 * @param  integer $flow_id Flow ID.
		 * @return void
		 */
		public function track_flows( $flow_id ) {
			Astra_Sites_Importer_Log::add( 'Flow ID ' . $flow_id );
			ST_Importer_Helper::track_post( $flow_id );
		}


		/**
		 * Import WP Forms
		 *
		 * @since 1.2.14
		 * @since 1.4.0 The `$wpforms_url` was added.
		 *
		 * @param  string $wpforms_url WP Forms JSON file URL.
		 * @return void
		 */
		public function import_wpforms( $wpforms_url = '' ) {

			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$screen = ( isset( $_REQUEST['screen'] ) ) ? sanitize_text_field( $_REQUEST['screen'] ) : '';
			$id = ( isset( $_REQUEST['id'] ) ) ? absint( $_REQUEST['id'] ) : '';

			$wpforms_url = ( 'elementor' === $screen ) ? astra_sites_get_wp_forms_url( $id ) : astra_get_site_data( 'astra-site-wpforms-path' );
			$ids_mapping = array();

			if ( ! astra_sites_is_valid_url( $wpforms_url ) ) {
				/* Translators: %s is WP Forms URL. */
				wp_send_json_error( sprintf( __( 'Invalid WPform Request URL - %s', 'astra-sites' ), $wpforms_url ) );
			}

			if ( ! empty( $wpforms_url ) && function_exists( 'wpforms_encode' ) ) {

				// Download JSON file.
				$file_path = ST_WXR_Importer::download_file( $wpforms_url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$forms = json_decode( Astra_Sites::get_instance()->get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $forms ) ) {

								foreach ( $forms as $form ) {
									$title = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
									$desc  = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';

									$new_id = post_exists( $title );

									if ( ! $new_id ) {
										$new_id = wp_insert_post(
											array(
												'post_title'   => $title,
												'post_status'  => 'publish',
												'post_type'    => 'wpforms',
												'post_excerpt' => $desc,
											)
										);

										if ( defined( 'WP_CLI' ) ) {
											WP_CLI::line( 'Imported Form ' . $title );
										}

										// Set meta for tracking the post.
										update_post_meta( $new_id, '_astra_sites_imported_wp_forms', true );
										Astra_Sites_Importer_Log::add( 'Inserted WP Form ' . $new_id );
									}

									if ( $new_id ) {

										// ID mapping.
										$ids_mapping[ $form['id'] ] = $new_id;

										$form['id'] = $new_id;
										wp_update_post(
											array(
												'ID' => $new_id,
												'post_content' => wpforms_encode( $form ),
											)
										);
									}
								}
							}
						} else {
							wp_send_json_error( __( 'Invalid JSON file for WP Forms.', 'astra-sites' ) );
						}
					} else {
						wp_send_json_error( __( 'There was an error downloading the WP Forms file.', 'astra-sites' ) );
					}
				} else {
					wp_send_json_error( __( 'There was an error downloading the WP Forms file.', 'astra-sites' ) );
				}
			}

			update_option( 'astra_sites_wpforms_ids_mapping', $ids_mapping, 'no' );

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'WP Forms Imported.' );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $ids_mapping );
			}
		}

		/**
		 * Import CartFlows
		 *
		 * @since 2.0.0
		 *
		 * @param  string $url Cartflows JSON file URL.
		 * @return void
		 */
		public function import_cartflows( $url = '' ) {

			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'edit_posts' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			// Disable CartFlows import logging.
			add_filter( 'cartflows_enable_log', '__return_false' );

			// Make the flow publish.
			add_filter( 'cartflows_flow_importer_args', array( $this, 'change_flow_status' ) );
			add_action( 'cartflows_flow_imported', array( $this, 'track_flows' ) );
			add_action( 'cartflows_step_imported', array( $this, 'track_flows' ) );
			add_filter( 'cartflows_enable_imported_content_processing', '__return_false' );

			$url = astra_get_site_data( 'astra-site-cartflows-path' );
			if ( ! empty( $url ) && is_callable( 'CartFlows_Importer::get_instance' ) ) {

				// Download JSON file.
				$file_path = ST_WXR_Importer::download_file( $url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$flows = json_decode( Astra_Sites::get_instance()->get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $flows ) && class_exists( 'CartFlows_Importer' ) ) {
								CartFlows_Importer::get_instance()->import_from_json_data( $flows );
							}
						} else {
							wp_send_json_error( __( 'Invalid file for CartFlows flows', 'astra-sites' ) );
						}
					} else {
						wp_send_json_error( __( 'There was an error downloading the CartFlows flows file.', 'astra-sites' ) );
					}
				} else {
					wp_send_json_error( __( 'There was an error downloading the CartFlows flows file.', 'astra-sites' ) );
				}
			} else {
				wp_send_json_error( __( 'Empty file for CartFlows flows', 'astra-sites' ) );
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Imported from ' . $url );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $url );
			}
		}

		/**
		 * Get single demo.
		 *
		 * @since  1.0.0
		 *
		 * @param  (String) $demo_api_uri API URL of a demo.
		 *
		 * @return (Array) $astra_demo_data demo data for the demo.
		 */
		public static function get_single_demo( $demo_api_uri ) {

			if ( is_int( $demo_api_uri ) ) {
				$demo_api_uri = Astra_Sites::get_instance()->get_api_url() . 'astra-sites/' . $demo_api_uri;
			}

			// default values.
			$remote_args = array();
			$defaults    = array(
				'id'                          => '',
				'astra-site-widgets-data'     => '',
				'astra-site-customizer-data'  => '',
				'astra-site-options-data'     => '',
				'astra-post-data-mapping'     => '',
				'astra-site-wxr-path'         => '',
				'astra-site-wpforms-path'     => '',
				'astra-enabled-extensions'    => '',
				'astra-custom-404'            => '',
				'required-plugins'            => '',
				'astra-site-taxonomy-mapping' => '',
				'license-status'              => '',
				'site-type'                   => '',
				'astra-site-url'              => '',
			);

			$api_args = apply_filters(
				'astra_sites_api_args',
				array(
					'timeout' => 15,
				)
			);

			// Use this for premium demos.
			$request_params = apply_filters(
				'astra_sites_api_params',
				array(
					'purchase_key' => '',
					'site_url'     => '',
				)
			);

			$demo_api_uri = add_query_arg( $request_params, trailingslashit( $demo_api_uri ) );

			// API Call.
			$response = wp_safe_remote_get( $demo_api_uri, $api_args );

			if ( is_wp_error( $response ) || ( isset( $response->status ) && 0 === $response->status ) ) {
				if ( isset( $response->status ) ) {
					$data = json_decode( $response, true );
				} else {
					return new WP_Error( 'api_invalid_response_code', $response->get_error_message() );
				}
			}

			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				return new WP_Error( 'api_invalid_response_code', wp_remote_retrieve_body( $response ) );
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! isset( $data['code'] ) ) {
				$remote_args['id']                          = $data['id'];
				$remote_args['astra-site-widgets-data']     = json_decode( $data['astra-site-widgets-data'] );
				$remote_args['astra-site-customizer-data']  = $data['astra-site-customizer-data'];
				$remote_args['astra-site-options-data']     = $data['astra-site-options-data'];
				$remote_args['astra-post-data-mapping']     = $data['astra-post-data-mapping'];
				$remote_args['astra-site-wxr-path']         = $data['astra-site-wxr-path'];
				$remote_args['astra-site-wpforms-path']     = $data['astra-site-wpforms-path'];
				$remote_args['astra-enabled-extensions']    = $data['astra-enabled-extensions'];
				$remote_args['astra-custom-404']            = $data['astra-custom-404'];
				$remote_args['required-plugins']            = $data['required-plugins'];
				$remote_args['astra-site-taxonomy-mapping'] = $data['astra-site-taxonomy-mapping'];
				$remote_args['license-status']              = $data['license-status'];
				$remote_args['site-type']                   = $data['astra-site-type'];
				$remote_args['astra-site-url']              = $data['astra-site-url'];
			}

			// Merge remote demo and defaults.
			return wp_parse_args( $remote_args, $defaults );
		}

		/**
		 * Clear Cache.
		 *
		 * @since  1.0.9
		 */
		public function clear_related_cache() {

			// Clear 'Builder Builder' cache.
			if ( is_callable( 'FLBuilderModel::delete_asset_cache_for_all_posts' ) ) {
				FLBuilderModel::delete_asset_cache_for_all_posts();
				Astra_Sites_Importer_Log::add( 'Cache for Beaver Builder cleared.' );
			}

			// Clear 'Astra Addon' cache.
			if ( is_callable( 'Astra_Minify::refresh_assets' ) ) {
				Astra_Minify::refresh_assets();
				Astra_Sites_Importer_Log::add( 'Cache for Astra Addon cleared.' );
			}

			Astra_Sites_Utils::third_party_cache_plugins_clear_cache();

			$this->update_latest_checksums();

			// Flush permalinks.
			flush_rewrite_rules(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- This function is called only after import is completed
		}

		/**
		 * Update Latest Checksums
		 *
		 * Store latest checksum after batch complete.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function update_latest_checksums() {
			$latest_checksums = get_site_option( 'astra-sites-last-export-checksums-latest', '' );
			update_site_option( 'astra-sites-last-export-checksums', $latest_checksums );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Importer::get_instance();
}
