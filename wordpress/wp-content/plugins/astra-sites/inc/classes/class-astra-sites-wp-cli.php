<?php
/**
 * Astra Sites WP CLI
 *
 * 1. Run `wp astra-sites list`                     List of all astra sites.
 * 2. Run `wp astra-sites import <id>`    Import site.
 *
 * @package Astra Sites
 * @since 1.4.0
 */

use STImporter\Importer\WXR_Importer\ST_WXR_Importer;
use STImporter\Importer\Batch\ST_Batch_Processing;
use AiBuilder\Inc\Traits\Helper;

if ( class_exists( 'WP_CLI_Command' ) && ! class_exists( 'Astra_Sites_WP_CLI' ) ) :

	/**
	 * WP-Cli commands to manage Astra Starter Sites.
	 *
	 * @since 1.4.0
	 */
	class Astra_Sites_WP_CLI extends WP_CLI_Command {

		/**
		 * Site Data
		 *
		 * @var array
		 */
		protected $current_site_data;

		/**
		 * Process Batch
		 *
		 * ## EXAMPLES
		 *
		 *     $ wp astra-sites batch
		 *      Processing Site: http://example.com/
		 *      Batch Process Started..
		 *      ..
		 *
		 * @since 2.1.0
		 * @param  array $args        Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 */
		public function batch( $args, $assoc_args ) {

			WP_CLI::line( 'Processing Site: ' . site_url() );

			ST_Batch_Processing::get_instance()->start_process();
		}

		/**
		 * Generates the list of all Astra Sites.
		 *
		 * ## OPTIONS
		 *
		 * [--per-page=<number>]
		 * : No of sites to show in the list. Default its showing 10 sites.
		 *
		 * [--search=<text>]
		 * : Show the sites from particular search term.
		 *
		 * [--category=<text>]
		 * : Show the site from the specific category.
		 *
		 * [--page-builder=<text>]
		 * : List the sites from the particular page builder.
		 *
		 * [--type=<text>]
		 * : List the sites from the particular site type.
		 *
		 * ## EXAMPLES
		 *
		 *     # List all the sites.
		 *     $ wp astra-sites list
		 *     +-------+-------------------+-----------------------------------------+---------+----------------+--------------+
		 *     | id    | title             | url                                     | type    | categories     | page-builder |
		 *     +-------+-------------------+-----------------------------------------+---------+----------------+--------------+
		 *     | 34184 | Nutritionist      | //websitedemos.net/nutritionist-01      | free    | Business, Free | Elementor    |
		 *     | 34055 | Law Firm          | //websitedemos.net/law-firm-03          | premium | Business       | Elementor    |
		 *     +-------+-------------------+-----------------------------------------+---------+----------------+--------------+
		 *
		 * @since 1.4.0
		 * @param  array $args        Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 *
		 * @alias list
		 */
		public function list_sites( $args, $assoc_args ) {

			$per_page = isset( $assoc_args['per-page'] ) ? $assoc_args['per-page'] : 10;
			$search   = isset( $assoc_args['search'] ) ? $assoc_args['search'] : '';

			$rest_args = array(
				'_fields'  => 'id,title,slug,astra-sites-site-category,astra-site-page-builder,astra-site-type,astra-site-url',
				'per_page' => $per_page,
			);

			if ( ! empty( $search ) ) {
				$rest_args['search'] = $search;
			}

			$list = (array) $this->get_sites( 'astra-sites', $rest_args, true, $assoc_args );

			// Modify the output.
			foreach ( $list as $key => $item ) {
				$list[ $key ]['categories']   = implode( ', ', $list[ $key ]['categories'] );
				$list[ $key ]['page-builder'] = implode( ', ', $list[ $key ]['page_builders'] );
			}

			if ( ! empty( $list ) ) {
				$display_fields = array(
					'id',
					'title',
					'url',
					'type',
					'categories',
					'page-builder',
				);
				$formatter      = $this->get_formatter( $assoc_args, $display_fields );
				$formatter->display_items( $list );
			} else {
				WP_CLI::error( __( 'No sites found! Try another query.', 'astra-sites' ) );
			}
		}

		/**
		 * Import the site by site ID.
		 *
		 * ## OPTIONS
		 *
		 * <id>
		 * : Site id of the import site.
		 *
		 * [--reset]
		 * : Reset the recently imported site data. Including post, pages, customizer settings, widgets etc.
		 *
		 * [--yes]
		 * : Forcefully import the site without asking any prompt message.
		 * 
		 *
		 * ## EXAMPLES
		 *
		 *     # Import demo site.
		 *     $ wp astra-sites import 34184 --reset --yes --license_key={{YOUR_KEY}}
		 *     Activating Plugins..
		 *     Reseting Posts..
		 *     ..
		 *
		 * @since 1.4.0
		 * @param  array $args        Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 */
		public function import( $args, $assoc_args ) {

			// Force import.
			$yes = isset( $assoc_args['yes'] ) ? true : false;
			if ( ! $yes ) {
				WP_CLI::confirm( __( 'Are you sure you want to import the site?', 'astra-sites' ) );
			}

			// Valid site ID?
			$id = isset( $args[0] ) ? absint( $args[0] ) : 0;
			if ( ! $id ) {
				WP_CLI::error( __( 'Invalid Site ID,', 'astra-sites' ) );
			}

			$reset     = isset( $assoc_args['reset'] ) ? true : false;
			$site_url  = get_site_url();
			$demo_data = $this->get_site_data( $id );

			// Invalid Site ID.
			if ( is_wp_error( $demo_data ) ) {
				/* Translators: %s is the error message. */
				WP_CLI::error( sprintf( __( 'Site Import failed due to error: %s', 'astra-sites' ), $demo_data->get_error_message() ) );
			}

			// License Status.
			$license_status = false;
			if ( is_callable( 'BSF_License_Manager::bsf_is_active_license' ) ) {
				$license_status = BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' );
			}

			if ( 'free' !== $demo_data['site-type'] && 'upgrade' === $demo_data['license-status'] && ! $license_status ) {

				if ( ! defined( 'ASTRA_PRO_SITES_NAME' ) ) {
					WP_CLI::line( __( 'This is Premium site. Please activate the "Starter Templates" license!', 'astra-sites' ) );
					WP_CLI::line( __( 'Use `wp plugin deactivate astra-sites` and then `wp plugin activate astra-pro-sites`', 'astra-sites' ) );
				}

				/* translators: %s is the activate plugin license link. */
				WP_CLI::error( __( 'Use CLI command `wp brainstormforce license activate astra-pro-sites {YOUR_LICENSE_KEY}`', 'astra-sites' ) );
			}

			/**
			 * Check File System permissions.
			 */
			Helper::filesystem_permission();

			/**
			 * Install & Activate Required Plugins.
			 */
			if ( isset( $demo_data['required-plugins'] ) ) {
				$plugins = (array) $demo_data['required-plugins'];
				if ( ! empty( $plugins ) ) {
					$plugin_status = Helper::required_plugins( $plugins, $demo_data['astra-site-options-data'], $demo_data['astra-enabled-extensions'] );

					// Install Plugins.
					if ( ! empty( $plugin_status['required_plugins']['notinstalled'] ) ) {
						WP_CLI::line( __( 'Installing Plugins..', 'astra-sites' ) );
						foreach ( $plugin_status['required_plugins']['notinstalled'] as $key => $plugin ) {
							if ( isset( $plugin['slug'] ) ) {

								// Install plugin.
								WP_CLI::runcommand( 'plugin install ' . $plugin['slug'] );

								// Activate plugin.
								WP_CLI::runcommand( 'plugin activate ' . $plugin['slug'] );
							}
						}
					}

					// Activate Plugins.
					if ( ! empty( $plugin_status['required_plugins']['inactive'] ) ) {
						WP_CLI::line( __( 'Activating Plugins..', 'astra-sites' ) );
						foreach ( $plugin_status['required_plugins']['inactive'] as $key => $plugin ) {
							if ( isset( $plugin['init'] ) ) {
								Helper::required_plugin_activate( $plugin['init'], $demo_data['astra-site-options-data'], $demo_data['astra-enabled-extensions'] );
							}
						}
					}
				}
			}

			/**
			 * Backup Customizer Settings
			 */
			Helper::backup_settings();

			/**
			 * Reset Site Data
			 */
			if ( $reset ) {
				WP_CLI::runcommand( 'astra-sites reset --yes' );
			}

			/**
			 * Import Flows & Steps for CartFlows.
			 */
			if ( isset( $demo_data['astra-site-cartflows-path'] ) && ! empty( $demo_data['astra-site-cartflows-path'] ) ) {
				Astra_Sites_Importer::get_instance()->import_cartflows( $demo_data['astra-site-cartflows-path'] );
			}

			/**
			 * Import WP Forms.
			 */
			if ( isset( $demo_data['astra-site-wpforms-path'] ) && ! empty( $demo_data['astra-site-wpforms-path'] ) ) {
				Astra_Sites_Importer::get_instance()->import_wpforms( $demo_data['astra-site-wpforms-path'] );
			}

			/**
			 * Import Customizer Settings.
			 */
			WP_CLI::runcommand( 'astra-sites import_customizer_settings ' . $id );

			/**
			 * Import Content from XML/WXR.
			 */
			if ( isset( $demo_data['astra-site-wxr-path'] ) && ! empty( $demo_data['astra-site-wxr-path'] ) ) {
				WP_CLI::runcommand( 'astra-sites import_wxr ' . $demo_data['astra-site-wxr-path'] );
			}

			/**
			 * Import Site Options.
			 */
			if ( isset( $demo_data['astra-site-options-data'] ) && ! empty( $demo_data['astra-site-options-data'] ) ) {
				WP_CLI::line( __( 'Importing Site Options..', 'astra-sites' ) );
				Helper::import_options( $demo_data['astra-site-options-data'] );
			}

			/**
			 * Import Widgets.
			 */
			if ( isset( $demo_data['astra-site-widgets-data'] ) && ! empty( $demo_data['astra-site-widgets-data'] ) ) {
				WP_CLI::line( __( 'Importing Widgets..', 'astra-sites' ) );
				Helper::import_widgets( $demo_data['astra-site-widgets-data'] );
			}

			/**
			 * Import End.
			 */
			WP_CLI::runcommand( 'astra-sites import_end' );

			/* translators: %s is the site URL. */
			WP_CLI::line( sprintf( __( "Site Imported Successfully!\nVisit: %s", 'astra-sites' ), $site_url ) );
		}

		/**
		 * Import End
		 *
		 * @since 1.4.3
		 * @return void
		 */
		public function import_end() {
			Helper::import_end();
		}

		/**
		 * Import form XML.
		 *
		 * ## OPTIONS
		 *
		 * <url>
		 * : XML/WXR file URL.
		 *
		 * ## EXAMPLES
		 *
		 *      $ wp astra-sites import_wxr <url>
		 *
		 * @since 1.4.3
		 * @param  array $args       Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 * @return void.
		 */
		public function import_wxr( $args = array(), $assoc_args = array() ) {

			// Valid site ID?
			$url = isset( $args[0] ) ? esc_url_raw( $args[0] ) : '';
			if ( empty( $url ) ) {
				WP_CLI::error( esc_html__( 'Invalid XML URL.', 'astra-sites' ) );
			}

			// Download XML file.
			/* translators: %s is the XML file URL. */
			WP_CLI::line( sprintf( esc_html__( 'Downloading %s', 'astra-sites' ), $url ) );
			$xml_path = ST_WXR_Importer::download_file( $url );

			if ( $xml_path['success'] && isset( $xml_path['data']['file'] ) ) {
				WP_CLI::line( esc_html__( 'Importing WXR..', 'astra-sites' ) );
				ST_WXR_Importer::get_instance()->sse_import( $xml_path['data']['file'] );
			} else {
				/* translators: %s is error message. */
				WP_CLI::line( printf( esc_html__( 'WXR file Download Failed. Error %s', 'astra-sites' ), esc_html( $xml_path['data'] ) ) );
			}
		}

		/**
		 * Reset
		 *
		 * Delete all pages, post, custom post type, customizer settings and site options.
		 *
		 * ## OPTIONS
		 *
		 * [--yes]
		 * : Reset previously imported site data without asking the prompt message.
		 *
		 * ## EXAMPLES
		 *
		 *      $ wp astra-sites reset
		 *
		 * @since 1.4.0
		 * @param  array $args       Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 * @return void.
		 */
		public function reset( $args = array(), $assoc_args = array() ) {

			$yes = isset( $assoc_args['yes'] ) ? true : false;
			if ( ! $yes ) {
				WP_CLI::confirm( __( 'Are you sure you want to delete imported site data?', 'astra-sites' ) );
			}

			// Get tracked data.
			$reset_data = Astra_Sites::get_instance()->get_reset_data();

			// Delete tracked posts.
			if ( isset( $reset_data['reset_posts'] ) && ! empty( $reset_data['reset_posts'] ) ) {
				WP_CLI::line( __( 'Reseting Posts..', 'astra-sites' ) );
				foreach ( $reset_data['reset_posts'] as $key => $post_id ) {
					Astra_Sites_Importer::get_instance()->delete_imported_posts( $post_id );
				}
			}
			// Delete tracked terms.
			if ( isset( $reset_data['reset_terms'] ) && ! empty( $reset_data['reset_terms'] ) ) {
				WP_CLI::line( __( 'Reseting Terms..', 'astra-sites' ) );
				foreach ( $reset_data['reset_terms'] as $key => $post_id ) {
					Astra_Sites_Importer::get_instance()->delete_imported_terms( $post_id );
				}
			}
			// Delete tracked WP forms.
			if ( isset( $reset_data['reset_wp_forms'] ) && ! empty( $reset_data['reset_wp_forms'] ) ) {
				WP_CLI::line( __( 'Resting WP Forms...', 'astra-sites' ) );
				foreach ( $reset_data['reset_wp_forms'] as $key => $post_id ) {
					Astra_Sites_Importer::get_instance()->delete_imported_terms( $post_id );
				}
			}

			// Delete Customizer Data.
			Helper::reset_customizer_data();

			// Delete Site Options.
			Helper::reset_site_options();

			// Delete Widgets Data.
			Helper::reset_widgets_data();
		}

		/**
		 * Import Customizer Settings
		 *
		 * ## OPTIONS
		 *
		 * <id>
		 * : Site ID.
		 *
		 * ## EXAMPLES
		 *
		 *      $ wp astra-sites import_customizer_settings <id>
		 *
		 * @since 1.4.0
		 *
		 * @param  array $args        Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 * @return void
		 */
		public function import_customizer_settings( $args, $assoc_args ) {

			// Valid site ID?
			$id = isset( $args[0] ) ? absint( $args[0] ) : 0;
			if ( ! $id ) {
				WP_CLI::error( __( 'Invalid Site ID,', 'astra-sites' ) );
			}

			$demo_data = $this->get_site_data( $id );

			WP_CLI::line( __( 'Importing customizer settings..', 'astra-sites' ) );

			Helper::import_customizer_settings( $demo_data['astra-site-customizer-data'] );
		}

		/**
		 * Page Builders
		 *
		 * ### OPTIONS
		 *
		 * [<list>]
		 * : List all page builders.
		 *
		 * OR
		 *
		 * [<set>]
		 * : Set the current page builder with given page builder slug.
		 *
		 * [<slug>]
		 * : Page builder slug.
		 *
		 * ### EXAMPLES
		 *
		 *     # List all the page builders.
		 *     λ wp astra-sites page_builder list
		 *     +----------------+----------------+
		 *     | slug           | name           |
		 *     +----------------+----------------+
		 *     | gutenberg      | Gutenberg      |
		 *     | elementor      | Elementor      |
		 *     | beaver-builder | Beaver Builder |
		 *     | brizy          | Brizy          |
		 *     +----------------+----------------+
		 *
		 *     # Set `Elementor` as default page builder.
		 *     λ wp astra-sites page_builder set elementor
		 *     "Elementor" is set as default page builder.
		 *
		 *     # Set `Beaver Builder` as default page builder.
		 *     λ wp astra-sites page_builder set beaver-builder
		 *     "Beaver Builder" is set as default page builder.
		 *
		 * @since 1.4.0
		 * @param  array $args        Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 */
		public function page_builder( $args, $assoc_args ) {
			$action = isset( $args[0] ) ? $args[0] : '';

			if ( empty( $action ) ) {
				WP_CLI::error( __( 'Please add valid parameter.', 'astra-sites' ) );
			}

			$page_builders = Astra_Sites_Page::get_instance()->get_page_builders();

			if ( 'list' === $action ) {
				$display_fields = array(
					'slug',
					'name',
				);
				$formatter      = $this->get_formatter( $assoc_args, $display_fields );
				$formatter->display_items( $page_builders );

				$default_page_builder = isset( $page_builders[ Astra_Sites_Page::get_instance()->get_setting( 'page_builder' ) ] ) ? $page_builders[ Astra_Sites_Page::get_instance()->get_setting( 'page_builder' ) ]['name'] : '';

				if ( ! empty( $default_page_builder ) ) {
					/* translators: %s is the current page builder name. */
					WP_CLI::line( sprintf( __( 'Default page builder is "%s".', 'astra-sites' ), $default_page_builder ) );
				}
			} elseif ( 'set' === $action ) {
				$page_builder_slugs = array_keys( $page_builders );
				$page_builder_slug  = isset( $args[1] ) ? $args[1] : '';
				if ( in_array( $page_builder_slug, $page_builder_slugs, true ) ) {
					Astra_Sites_Page::get_instance()->save_page_builder_on_submit( $page_builder_slug );
					/* translators: %s is the page builder name. */
					WP_CLI::line( sprintf( __( '"%s" is set as default page builder.', 'astra-sites' ), $page_builders[ $page_builder_slug ]['name'] ) );

				} else {
					WP_CLI::error( __( "Invalid page builder slug. \nCheck all page builder slugs with command `wp astra-sites page_builder list`", 'astra-sites' ) );
				}
			} else {
				WP_CLI::error( __( "Invalid parameter! \nPlease use `list` or `set` parameter.", 'astra-sites' ) );
			}
		}

		/**
		 * Get Formatter
		 *
		 * @since 1.4.0
		 * @param  array  $assoc_args Associate arguments.
		 * @param  string $fields    Fields.
		 * @param  string $prefix    Prefix.
		 * @return object            Class object.
		 */
		protected function get_formatter( &$assoc_args, $fields = '', $prefix = '' ) {
			return new \WP_CLI\Formatter( $assoc_args, $fields, $prefix );
		}

		/**
		 * Get Site Data by Site ID
		 *
		 * @since 1.4.0
		 *
		 * @param  int $id        Site ID.
		 * @return array
		 */
		private function get_site_data( $id ) {
			if ( empty( $this->current_site_data ) ) {
				// @todo Use Astra_Sites::get_instance()->api_request() instead of below function.
				$this->current_site_data = Astra_Sites_Importer::get_instance()->get_single_demo( $id );
				Astra_Sites_File_System::get_instance()->update_demo_data( $this->current_site_data );
				
			}

			return $this->current_site_data;
		}

		/**
		 * Get Sites
		 *
		 * @since 1.4.0
		 *
		 * @param  string  $post_slug  Post slug.
		 * @param  array   $args       Post query arguments.
		 * @param  boolean $force      Force import.
		 * @param  array   $assoc_args Associate arguments.
		 * @return array
		 */
		private function get_sites( $post_slug = '', $args = array(), $force = false, $assoc_args = array() ) {

			// Add page builders.
			$page_builder  = isset( $assoc_args['page-builder'] ) ? $assoc_args['page-builder'] : Astra_Sites_Page::get_instance()->get_setting( 'page_builder' );
			$response      = $this->get_term_ids( 'astra-site-page-builder', $page_builder, $args );
			$args          = $response['args'];
			$page_builders = $response['terms'];
			if ( empty( $page_builders['data'] ) ) {
				WP_CLI::error( __( 'This page builder plugin is not installed. Please try a different page builder.', 'astra-sites' ) );
			}

			// Add type.
			$type     = isset( $assoc_args['type'] ) ? $assoc_args['type'] : '';
			$response = $this->get_term_ids( 'astra-sites-type', $type, $args );
			$args     = $response['args'];
			$types    = $response['terms'];
			if ( empty( $types['data'] ) ) {
				WP_CLI::error( __( 'This site type does not exist. Please try a different site type.', 'astra-sites' ) );
			}

			// Add categories.
			$category   = isset( $assoc_args['category'] ) ? $assoc_args['category'] : '';
			$response   = $this->get_term_ids( 'astra-sites-site-category', $category, $args );
			$args       = $response['args'];
			$categories = $response['terms'];
			if ( empty( $categories['data'] ) ) {
				WP_CLI::error( __( 'This site category does not exist. Please try a different site category.', 'astra-sites' ) );
			}

			// Site list.
			$sites = (array) $this->get_posts( 'astra-sites', $args, $force );

			$list = array();
			if ( $sites['success'] ) {
				foreach ( $sites['data'] as $key => $site ) {
					$single_site = array(
						'id'            => $site['id'],
						'slug'          => $site['slug'],
						'title'         => $site['title']['rendered'],
						'url'           => $site['astra-site-url'],
						'type'          => ( 'premium' === $site['astra-site-type'] ) ? 'Premium' : ucwords( $site['astra-site-type'] ),
						'categories'    => array(),
						'page_builders' => array(),
					);

					if ( isset( $site['astra-sites-site-category'] ) && ! empty( $categories['data'] ) ) {
						foreach ( $site['astra-sites-site-category'] as $category_key => $category_id ) {
							if ( isset( $categories['data'][ $category_id ] ) ) {
								$single_site['categories'][ $category_id ] = $categories['data'][ $category_id ];
							}
						}
					}

					if ( isset( $site['astra-site-page-builder'] ) && ! empty( $page_builders['data'] ) ) {
						foreach ( $site['astra-site-page-builder'] as $page_builder_key => $page_builder_id ) {
							if ( isset( $page_builders['data'][ $page_builder_id ] ) ) {
								$single_site['page_builders'][ $page_builder_id ] = $page_builders['data'][ $page_builder_id ];
							}
						}
					}

					$list[] = $single_site;
				}
			}

			return $list;
		}


		/**
		 * Get Term IDs
		 *
		 * @since 1.4.0
		 *
		 * @param  string $term_slug   Term slug.
		 * @param  string $search_term Search term.
		 * @param  array  $args        Term query arguments.
		 * @return array               Term response.
		 */
		private function get_term_ids( $term_slug = '', $search_term = '', $args = array() ) {
			$term_args = array();

			if ( ! empty( $search_term ) ) {
				$term_args = array(
					'search' => $search_term,
				);
			}

			$term_response = (array) $this->get_terms( $term_slug, $term_args, true );

			if ( ! empty( $search_term ) ) {
				if ( ! empty( $term_response ) && is_array( $term_response['data'] ) ) {
					$args[ $term_slug ] = implode( ',', array_keys( $term_response['data'] ) );
				}
			}

			return array(
				'args'  => $args,
				'terms' => $term_response,
			);
		}

		/**
		 * Get Terms
		 *
		 * @since 1.0.0
		 *
		 * @param  array  $term_slug Term Slug.
		 * @param  array  $args      For selecting the demos (Search terms, pagination etc).
		 * @param  string $force     Force import.
		 * @return $array            Term response.
		 */
		private function get_terms( $term_slug = '', $args = array(), $force = false ) {

			$defaults = array(
				'_fields' => 'id,name,slug,count',
			);
			$args     = wp_parse_args( (array) $args, $defaults );

			$success    = false;
			$terms_data = get_transient( 'astra-sites-term-' . $term_slug );
			if ( empty( $terms_data ) || $force ) {
				$url = add_query_arg( $args, Astra_Sites::get_instance()->get_api_url() . $term_slug );

				$api_args = array(
					'timeout' => 60,
				);

				$response = wp_safe_remote_get( $url, $api_args );
				if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
					$request_term_data = json_decode( wp_remote_retrieve_body( $response ), true );

					if ( ! isset( $request_term_data['code'] ) ) {
						$success        = true;
						$new_terms_data = array();
						foreach ( $request_term_data as $key => $request_term ) {
							$new_terms_data[ $request_term['id'] ] = $request_term['name'];
						}
						if ( set_transient( 'astra-sites-term-' . $term_slug, $new_terms_data, WEEK_IN_SECONDS ) ) {
							return array(
								'success' => $success,
								'data'    => $new_terms_data,
							);
						}
					}
				}
			}

			return array(
				'success' => $success,
				'data'    => $terms_data,
			);
		}

		/**
		 * Get Posts
		 *
		 * @since 1.4.0
		 *
		 * @param  string  $post_slug  Post slug.
		 * @param  array   $args       Post query arguments.
		 * @param  boolean $force      Force import.
		 * @return array
		 */
		private function get_posts( $post_slug = '', $args = array(), $force = false ) {

			$args = wp_parse_args( (array) $args, array() );

			$all_posts = get_transient( 'astra-sites-post-' . $post_slug );

			if ( empty( $all_posts ) || $force ) {
				$url = add_query_arg( $args, Astra_Sites::get_instance()->get_api_url() . $post_slug );

				$api_args = array(
					'timeout' => 60,
				);

				$success  = false;
				$response = wp_safe_remote_get( $url, $api_args );
				if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
					$all_posts = json_decode( wp_remote_retrieve_body( $response ), true );

					if ( ! isset( $all_posts['code'] ) ) {
						$success = true;
						set_transient( 'astra-sites-post-' . $post_slug, $all_posts, WEEK_IN_SECONDS );
					}
				}
			} else {
				$success = true;
			}

			return array(
				'success' => $success,
				'data'    => $all_posts,
			);
		}

		/**
		 * Sync Library.
		 *
		 * Sync the library and create the .json files.
		 *
		 * Use: `wp astra-sites sync`
		 *
		 * @since 2.0.0
		 * @param  array $args       Arguments.
		 * @param  array $assoc_args Associated Arguments.
		 * @return void.
		 */
		public function sync( $args = array(), $assoc_args = array() ) {
			Astra_Sites_Batch_Processing::get_instance()->process_batch();
		}

		/**
		 * Init.
		 */
		public static function init() {
			add_filter( 'wp_check_filetype_and_ext', array( 'Astra_Sites_WP_CLI', 'real_mime_types' ), 10, 5 );
		}

		/**
		 * Different MIME type of different PHP version
		 *
		 * Filters the "real" file type of the given file.
		 *
		 * @since 1.2.9
		 *
		 * @param array                 $defaults File data array containing 'ext', 'type', and
		 *                                                         'proper_filename' keys.
		 * @param string                $file                      Full path to the file.
		 * @param string                $filename                  The name of the file (may differ from $file due to
		 *                                                         $file being in a tmp directory).
		 * @param array<string, string> $mimes                     Key is the file extension with value as the mime type.
		 * @param string                $real_mime                Real MIME type of the uploaded file.
		 */
		public static function real_mime_types( $defaults, $file, $filename, $mimes, $real_mime ) {
			return ST_WXR_Importer::get_instance()->real_mime_types_5_1_0( $defaults, $file, $filename, $mimes, $real_mime );
		}
	}

	/**
	 * Add Command
	 */
	WP_CLI::add_command( 'starter-templates', 'Astra_Sites_WP_CLI' );
	WP_CLI::add_command( 'astra-sites', 'Astra_Sites_WP_CLI' );
	Astra_Sites_WP_CLI::init();

endif;
