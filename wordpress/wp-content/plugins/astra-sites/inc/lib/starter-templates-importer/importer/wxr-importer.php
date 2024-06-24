<?php
/**
 * WordPress Importer
 *
 * @package WXR Importer
 *
 * WordPress Importer
 * https://github.com/humanmade/WordPress-Importer
 *
 * Released under the GNU General Public License v2.0
 * https://github.com/humanmade/WordPress-Importer/blob/master/LICENSE
 */

/**
 * All the PHPCS errors are ignored in this file as it is a third party file.
 * Forked from WP importer v2 - https://github.com/humanmade/WordPress-Importer
 */

/**
 * WXR Importer
 */
if ( ! class_exists( 'WXR_Importer' ) && class_exists( 'WP_Importer' ) ) :

	/**
	 * WXR Importer
	 */
	class WXR_Importer extends WP_Importer {
		/**
		 * Maximum supported WXR version
		 */
		const MAX_WXR_VERSION = '1.2';

		/**
		 * Regular expression for checking if a post references an attachment
		 *
		 * Note: This is a quick, weak check just to exclude text-only posts. More
		 * vigorous checking is done later to verify.
		 */
		const REGEX_HAS_ATTACHMENT_REFS = '!
			(
				# Match anything with an image or attachment class
				class=[\'"].*?\b(wp-image-\d+|attachment-[\w\-]+)\b
			|
				# Match anything that looks like an upload URL
				src=[\'"][^\'"]*(
					[0-9]{4}/[0-9]{2}/[^\'"]+\.(jpg|jpeg|png|gif|svg)
				|
					content/uploads[^\'"]+
				)[\'"]
			)!ix';

		/**
		 * Version of WXR we're importing.
		 *
		 * Defaults to 1.0 for compatibility. Typically overridden by a
		 * `<wp:wxr_version>` tag at the start of the file.
		 *
		 * @var string
		 */
		protected $version = '1.0';

		// information to import from WXR file.
		/**
		 * Categories
		 *
		 * @var array
		 */
		protected $categories = array();

		/**
		 * Tags
		 *
		 * @var array
		 */
		protected $tags = array();

		/**
		 * Base Url
		 *
		 * @var string
		 */
		protected $base_url = '';

		// TODO: REMOVE THESE.
		/**
		 * Processed Terms
		 *
		 * @var array
		 */
		protected $processed_terms = array();

		/**
		 * Processed Posts
		 *
		 * @var array
		 */
		protected $processed_posts = array();

		/**
		 * Processed Menu Items
		 *
		 * @var array
		 */
		protected $processed_menu_items = array();

		/**
		 * Menu Item Orphans
		 *
		 * @var array
		 */
		protected $menu_item_orphans = array();

		/**
		 * Missing Menu Items
		 *
		 * @var array
		 */
		protected $missing_menu_items = array();


		// NEW STYLE.
		/**
		 * Mapping
		 *
		 * @var array
		 */
		protected $mapping = array();

		/**
		 * Requires Remapping
		 *
		 * @var array
		 */
		protected $requires_remapping = array();

		/**
		 * Exists
		 *
		 * @var array
		 */
		protected $exists = array();

		/**
		 * User Slug Override
		 *
		 * @var array
		 */
		protected $user_slug_override = array();


		/**
		 * Url Remap
		 *
		 * @var array
		 */
		protected $url_remap = array();

		/**
		 * Featured Images
		 *
		 * @var array
		 */
		protected $featured_images = array();


		/**
		 * Logger instance.
		 *
		 * @var WP_Importer_Logger
		 */
		protected $logger;

		/**
		 * Constructor
		 *
		 * @param array $options {.
		 *     @var bool $prefill_existing_posts Should we prefill `post_exists` calls? (True prefills and uses more memory, false checks once per imported post and takes longer. Default is true.).
		 *     @var bool $prefill_existing_comments Should we prefill `comment_exists` calls? (True prefills and uses more memory, false checks once per imported comment and takes longer. Default is true.).
		 *     @var bool $prefill_existing_terms Should we prefill `term_exists` calls? (True prefills and uses more memory, false checks once per imported term and takes longer. Default is true.).
		 *     @var bool $update_attachment_guids Should attachment GUIDs be updated to the new URL? (True updates the GUID, which keeps compatibility with v1, false doesn't update, and allows deduplication and reimporting. Default is false.).
		 *     @var bool $fetch_attachments Fetch attachments from the remote server. (True fetches and creates attachment posts, false skips attachments. Default is false.).
		 *     @var bool $aggressive_url_search Should we search/replace for URLs aggressively? (True searches all posts' content for old URLs and replaces, false checks for `<img class="wp-image-*">` only. Default is false.).
		 *     @var int $default_author User ID to use if author is missing or invalid. (Default is null, which leaves posts unassigned.).
		 * }
		 */
		public function __construct( $options = array() ) {
			// Initialize some important variables.
			$empty_types = array(
				'post'    => array(),
				'comment' => array(),
				'term'    => array(),
				'user'    => array(),
			);

			$this->mapping              = $empty_types;
			$this->mapping['user_slug'] = array();
			$this->mapping['term_id']   = array();
			$this->requires_remapping   = $empty_types;
			$this->exists               = $empty_types;

			$this->options = wp_parse_args(
				$options,
				array(
					'prefill_existing_posts'    => true,
					'prefill_existing_comments' => true,
					'prefill_existing_terms'    => true,
					'update_attachment_guids'   => false,
					'fetch_attachments'         => false,
					'aggressive_url_search'     => false,
					'default_author'            => null,
				)
			);
		}

		/**
		 * Set Logger
		 *
		 * @param object $logger Logger object.
		 */
		public function set_logger( $logger ) {
			$this->logger = $logger;
		}

		/**
		 * Get a stream reader for the file.
		 *
		 * @param string $file Path to the XML file.
		 * @return XMLReader|WP_Error Reader instance on success, error otherwise.
		 */
		protected function get_reader( $file ) {
			// Avoid loading external entities for security.
			$old_value = null;

			$reader = new XMLReader();
			$status = $reader->open( $file );

			if ( ! $status ) {
				return new WP_Error( 'wxr_importer.cannot_parse', __( 'Could not open the file for parsing', 'st-importer', 'astra-sites' ) );
			}

			return $reader;
		}

		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing.
		 */
		public function get_preliminary_information( $file ) {
			// Let's run the actual importer now, woot.
			$reader = $this->get_reader( $file );
			if ( is_wp_error( $reader ) ) {
				return $reader;
			}

			// Set the version to compatibility mode first.
			$this->version = '1.0';

			// Start parsing!
			$data = new WXR_Import_Info();
			while ( $reader->read() ) {
				// Only deal with element opens.
				if ( XMLReader::ELEMENT !== $reader->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $reader->name ) {
					case 'wp:wxr_version':
						// Upgrade to the correct version.
						$this->version = $reader->readString();

						if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
							$this->logger->warning(
								sprintf(
									/* translators: %1$s is WXR version, %2$s is max supported WXR version. */
									__( 'This WXR file (version %1$s) is newer than the importer (version %2$s) and may not be supported. Please consider updating.', 'st-importer', 'astra-sites' ),
									$this->version,
									self::MAX_WXR_VERSION
								)
							);
						}

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'generator':
						$data->generator = $reader->readString();
						$reader->next();
						break;

					case 'title':
						$data->title = $reader->readString();
						$reader->next();
						break;

					case 'wp:base_site_url':
						$data->siteurl = $reader->readString();
						$reader->next();
						break;

					case 'wp:base_blog_url':
						$data->home = $reader->readString();
						$reader->next();
						break;

					case 'wp:author':
						$node = $reader->expand();

						$parsed = $this->parse_author_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$data->users[] = $parsed;

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'item':
						$node   = $reader->expand();
						$parsed = $this->parse_post_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						if ( 'attachment' === $parsed['data']['post_type'] ) {
							$data->media_count++;
						} else {
							$data->post_count++;
						}
						$data->comment_count += count( $parsed['comments'] );

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:category':
					case 'wp:tag':
					case 'wp:term':
						$data->term_count++;

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;
				}
			}

			$data->version = $this->version;

			return $data;
		}

		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing.
		 */
		public function parse_authors( $file ) {
			// Let's run the actual importer now, woot.
			$reader = $this->get_reader( $file );
			if ( is_wp_error( $reader ) ) {
				return $reader;
			}

			// Set the version to compatibility mode first.
			$this->version = '1.0';

			// Start parsing!
			$authors = array();
			while ( $reader->read() ) {
				// Only deal with element opens.
				if ( XMLReader::ELEMENT !== $reader->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $reader->name ) {
					case 'wp:wxr_version':
						// Upgrade to the correct version.
						$this->version = $reader->readString();

						if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
							$this->logger->warning(
								sprintf(
									/* translators: %1$s is WXR version, %2$s is max supported WXR version. */
									__( 'This WXR file (version %1$s) is newer than the importer (version %2$s) and may not be supported. Please consider updating.', 'st-importer', 'astra-sites' ),
									$this->version,
									self::MAX_WXR_VERSION
								)
							);
						}

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:author':
						$node = $reader->expand();

						$parsed = $this->parse_author_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$authors[] = $parsed;

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;
				}
			}

			return $authors;
		}

		/**
		 * The main controller for the actual import stage.
		 *
		 * @param string $file Path to the WXR file for importing.
		 */
		public function import( $file ) {
			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) ); //phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.http_request_timeout -- We need this to avoid timeout on slow servers while installing theme, plugin etc.

			$result = $this->import_start( $file );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			// Let's run the actual importer now, woot.
			$reader = $this->get_reader( $file );
			if ( is_wp_error( $reader ) ) {
				return $reader;
			}

			// Set the version to compatibility mode first.
			$this->version = '1.0';

			// Reset other variables.
			$this->base_url = '';

			// Start parsing!
			while ( $reader->read() ) {
				// Only deal with element opens.
				if ( XMLReader::ELEMENT !== $reader->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $reader->name ) {
					case 'wp:wxr_version':
						// Upgrade to the correct version.
						$this->version = $reader->readString();

						if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
							$this->logger->warning(
								sprintf(
									/* translators: %1$s is WXR version, %2$s is max supported WXR version. */
									__( 'This WXR file (version %1$s) is newer than the importer (version %2$s) and may not be supported. Please consider updating.', 'st-importer', 'astra-sites' ),
									$this->version,
									self::MAX_WXR_VERSION
								)
							);
						}

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:base_site_url':
						$this->base_url = $reader->readString();

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'item':
						$node   = $reader->expand();
						$parsed = $this->parse_post_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$this->process_post( $parsed['data'], $parsed['meta'], $parsed['comments'], $parsed['terms'] );

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:author':
						$node = $reader->expand();

						$parsed = $this->parse_author_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$status = $this->process_author( $parsed['data'], $parsed['meta'] );
						if ( is_wp_error( $status ) ) {
							$this->log_error( $status );
						}

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:category':
						$node = $reader->expand();

						$parsed = $this->parse_term_node( $node, 'category' );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$status = $this->process_term( $parsed['data'], $parsed['meta'] );

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:tag':
						$node = $reader->expand();

						$parsed = $this->parse_term_node( $node, 'tag' );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$status = $this->process_term( $parsed['data'], $parsed['meta'] );

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					case 'wp:term':
						$node = $reader->expand();

						$parsed = $this->parse_term_node( $node );
						if ( is_wp_error( $parsed ) ) {
							$this->log_error( $parsed );

							// Skip the rest of this post.
							$reader->next();
							break;
						}

						$status = $this->process_term( $parsed['data'], $parsed['meta'] );

						// Handled everything in this node, move on to the next.
						$reader->next();
						break;

					default:
						// Skip this node, probably handled by something already.
						break;
				}
			}

			// Now that we've done the main processing, do any required
			// post-processing and remapping.
			$this->post_process();

			if ( $this->options['aggressive_url_search'] ) {
				$this->replace_attachment_urls_in_content();
			}
			// phpcs:disable
			// $this->remap_featured_images();
			// phpcs:enable
			$this->import_end();
		}

		/**
		 * Log an error instance to the logger.
		 *
		 * @param WP_Error $error Error instance to log.
		 */
		protected function log_error( WP_Error $error ) {
			$this->logger->warning( $error->get_error_message() );

			// Log the data as debug info too.
			$data = $error->get_error_data();
			if ( ! empty( $data ) ) {
				$this->logger->debug( var_export( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- 3rd party library.
			}
		}

		/**
		 * Parses the WXR file and prepares us for the task of processing parsed data
		 *
		 * @param string $file Path to the WXR file for importing.
		 */
		protected function import_start( $file ) {
			if ( ! is_file( $file ) ) {
				return new WP_Error( 'wxr_importer.file_missing', __( 'The file does not exist, please try again.', 'st-importer', 'astra-sites' ) );
			}

			// Suspend bunches of stuff in WP core.
			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );
			wp_suspend_cache_invalidation( true );

			// Prefill exists calls if told to.
			if ( $this->options['prefill_existing_posts'] ) {
				$this->prefill_existing_posts();
			}
			if ( $this->options['prefill_existing_comments'] ) {
				$this->prefill_existing_comments();
			}
			if ( $this->options['prefill_existing_terms'] ) {
				$this->prefill_existing_terms();
			}

			/**
			 * Begin the import.
			 *
			 * Fires before the import process has begun. If you need to suspend
			 * caching or heavy processing on hooks, do so here.
			 */
			do_action( 'import_start' );
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		protected function import_end() {
			// Re-enable stuff in core.
			wp_suspend_cache_invalidation( false );
			wp_cache_flush();
			foreach ( get_taxonomies() as $tax ) {
				delete_option( "{$tax}_children" );
				_get_term_hierarchy( $tax );
			}

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			/**
			 * Complete the import.
			 *
			 * Fires after the import process has finished. If you need to update
			 * your cache or re-enable processing, do so here.
			 */
			do_action( 'import_end' );
		}

		/**
		 * Set the user mapping.
		 *
		 * @param array $mapping List of map arrays (containing `old_slug`, `old_id`, `new_id`).
		 */
		public function set_user_mapping( $mapping ) {
			foreach ( $mapping as $map ) {
				if ( empty( $map['old_slug'] ) || empty( $map['old_id'] ) || empty( $map['new_id'] ) ) {
					$this->logger->warning( __( 'Invalid author mapping', 'st-importer', 'astra-sites' ) );
					$this->logger->debug( var_export( $map, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- 3rd party library.
					continue;
				}

				$old_slug = $map['old_slug'];
				$old_id   = $map['old_id'];
				$new_id   = $map['new_id'];

				$this->mapping['user'][ $old_id ]        = $new_id;
				$this->mapping['user_slug'][ $old_slug ] = $new_id;
			}
		}

		/**
		 * Set the user slug overrides.
		 *
		 * Allows overriding the slug in the import with a custom/renamed version.
		 *
		 * @param string[] $overrides Map of old slug to new slug.
		 */
		public function set_user_slug_overrides( $overrides ) {
			foreach ( $overrides as $original => $renamed ) {
				$this->user_slug_override[ $original ] = $renamed;
			}
		}

		/**
		 * Parse a post node into post data.
		 *
		 * @param DOMElement $node Parent node of post data (typically `item`).
		 * @return array|WP_Error Post data array on success, error otherwise.
		 */
		protected function parse_post_node( $node ) {
			$data     = array();
			$meta     = array();
			$comments = array();
			$terms    = array();

			foreach ( $node->childNodes as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				// We only care about child elements.
				if ( XML_ELEMENT_NODE !== $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $child->tagName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					case 'wp:post_type':
						$data['post_type'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'title':
						$data['post_title'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'guid':
						$data['guid'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'dc:creator':
						$data['post_author'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'content:encoded':
						$data['post_content'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'excerpt:encoded':
						$data['post_excerpt'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:post_id':
						$data['post_id'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:post_date':
						$data['post_date'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:post_date_gmt':
						$data['post_date_gmt'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_status':
						$data['comment_status'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:ping_status':
						$data['ping_status'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:post_name':
						$data['post_name'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:status':
						$data['post_status'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.

						if ( 'auto-draft' === $data['post_status'] ) {
							// Bail now.
							return new WP_Error(
								'wxr_importer.post.cannot_import_draft',
								__( 'Cannot import auto-draft posts', 'st-importer', 'astra-sites' ),
								$data
							);
						}
						break;

					case 'wp:post_parent':
						$data['post_parent'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:menu_order':
						$data['menu_order'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:post_password':
						$data['post_password'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:is_sticky':
						$data['is_sticky'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:attachment_url':
						$data['attachment_url'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:postmeta':
						$meta_item = $this->parse_meta_node( $child );
						if ( ! empty( $meta_item ) ) {
							$meta[] = $meta_item;
						}
						break;

					case 'wp:comment':
						$comment_item = $this->parse_comment_node( $child );
						if ( ! empty( $comment_item ) ) {
							$comments[] = $comment_item;
						}
						break;

					case 'category':
						$term_item = $this->parse_category_node( $child );
						if ( ! empty( $term_item ) ) {
							$terms[] = $term_item;
						}
						break;
				}
			}

			return compact( 'data', 'meta', 'comments', 'terms' );
		}

		/**
		 * Create new posts based on import information
		 *
		 * Posts marked as having a parent which doesn't exist will become top level items.
		 * Doesn't create a new post if: the post type doesn't exist, the given post ID
		 * is already noted as imported or a post with the same title and date already exists.
		 * Note that new/updated terms, comments and meta are imported for the last of the above.
		 *
		 * @param array $data Post data. (Return empty to skip.).
		 * @param array $meta Meta data.
		 * @param array $comments Comments on the post.
		 * @param array $terms Terms on the post.
		 */
		protected function process_post( $data, $meta, $comments, $terms ) {
			/**
			 * Pre-process post data.
			 *
			 * @param array $data Post data. (Return empty to skip.)
			 * @param array $meta Meta data.
			 * @param array $comments Comments on the post.
			 * @param array $terms Terms on the post.
			 */
			$data = apply_filters( 'wxr_importer.pre_process.post', $data, $meta, $comments, $terms ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
			if ( empty( $data ) ) {
				return false;
			}

			$original_id = isset( $data['post_id'] ) ? (int) $data['post_id'] : 0;
			$parent_id   = isset( $data['post_parent'] ) ? (int) $data['post_parent'] : 0;
			$author_id   = isset( $data['post_author'] ) ? (int) $data['post_author'] : 0;

			// Have we already processed this?
			if ( isset( $this->mapping['post'][ $original_id ] ) ) {
				return;
			}

			// If post status as 'dp-rewrite-republish' OR trash OR draft then skip.
			if ( 'dp-rewrite-republish' === $data['post_status'] || 'draft' === $data['post_status'] || 'trash' === $data['post_status'] ) {
				return;
			}

			$post_type_object = get_post_type_object( $data['post_type'] );

			// Is this type even valid?
			if ( ! $post_type_object ) {
				$this->logger->warning(
					sprintf(
						/* translators: %1$s is the import message, %2$s is post type. */
						__( 'Failed to import "%1$s": Invalid post type %2$s', 'st-importer', 'astra-sites' ),
						$data['post_title'],
						$data['post_type']
					)
				);
				return false;
			}

			$post_exists = $this->post_exists( $data );
			if ( $post_exists ) {
				$message = sprintf(
					/* translators: %1$s single post type, %2$s is post title. */
					__( '%1$s "%2$s" already exists.', 'st-importer', 'astra-sites' ),
					$post_type_object->labels->singular_name,
					$data['post_title']
				);

				$this->logger->info( $message );

				/**
				 * Post processing already imported.
				 *
				 * @param array $data Raw data imported for the post.
				 */
				do_action( 'wxr_importer.process_already_imported.post', $data ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.

				// Even though this post already exists, new comments might need importing.
				$this->process_comments( $comments, $original_id, $data, $post_exists );

				return false;
			}

			// Map the parent post, or mark it as one we need to fix.
			$requires_remapping = false;
			if ( $parent_id ) {
				if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
					$data['post_parent'] = $this->mapping['post'][ $parent_id ];
				} else {
					$meta[]             = array(
						'key'   => '_wxr_import_parent',
						'value' => $parent_id,
					);
					$requires_remapping = true;

					$data['post_parent'] = 0;
				}
			}

			// Map the author, or mark it as one we need to fix.
			$author = sanitize_user( $data['post_author'], true );
			if ( empty( $author ) ) {
				// Missing or invalid author, use default if available.
				$data['post_author'] = $this->options['default_author'];
			} elseif ( isset( $this->mapping['user_slug'][ $author ] ) ) {
				$data['post_author'] = $this->mapping['user_slug'][ $author ];
			} else {
				$meta[]             = array(
					'key'   => '_wxr_import_user_slug',
					'value' => $author,
				);
				$requires_remapping = true;

				$data['post_author'] = (int) get_current_user_id();
			}

			// Does the post look like it contains attachment images?
			if ( preg_match( self::REGEX_HAS_ATTACHMENT_REFS, $data['post_content'] ) ) {
				$meta[]             = array(
					'key'   => '_wxr_import_has_attachment_refs',
					'value' => true,
				);
				$requires_remapping = true;
			}

			// Whitelist to just the keys we allow.
			$postdata = array(
				'import_id' => $data['post_id'],
			);
			$allowed  = array(
				'post_author'    => true,
				'post_date'      => true,
				'post_date_gmt'  => true,
				'post_content'   => true,
				'post_excerpt'   => true,
				'post_title'     => true,
				'post_status'    => true,
				'post_name'      => true,
				'comment_status' => true,
				'ping_status'    => true,
				'guid'           => true,
				'post_parent'    => true,
				'menu_order'     => true,
				'post_type'      => true,
				'post_password'  => true,
			);
			foreach ( $data as $key => $value ) {
				if ( ! isset( $allowed[ $key ] ) ) {
					continue;
				}

				$postdata[ $key ] = $data[ $key ];
			}

			$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $data );

			if ( 'attachment' === $postdata['post_type'] ) {
				if ( ! $this->options['fetch_attachments'] ) {
					$this->logger->notice(
						sprintf(
							/* translators: %s is post title */
							__( 'Skipping attachment "%s", fetching attachments disabled', 'st-importer', 'astra-sites' ),
							$data['post_title']
						)
					);
					/**
					 * Post processing skipped.
					 *
					 * @param array $data Raw data imported for the post.
					 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
					 */
					do_action( 'wxr_importer.process_skipped.post', $data, $meta ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
					return false;
				}
				$remote_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];
				$post_id    = $this->process_attachment( $postdata, $meta, $remote_url );
			} else {
				$post_id = wp_insert_post( $postdata, true );
				do_action( 'wp_import_insert_post', $post_id, $original_id, $postdata, $data );
			}

			if ( is_wp_error( $post_id ) ) {
				$this->logger->error(
					sprintf(
						/* translators: %1$s is the post title, %2$s is post type. */
						__( 'Failed to import "%1$s" (%2$s)', 'st-importer', 'astra-sites' ),
						$data['post_title'],
						$post_type_object->labels->singular_name
					)
				);
				$this->logger->debug( $post_id->get_error_message() );

				/**
				 * Post processing failed.
				 *
				 * @param WP_Error $post_id Error object.
				 * @param array $data Raw data imported for the post.
				 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
				 * @param array $comments Raw comment data, already processed by {@see process_comments}.
				 * @param array $terms Raw term data, already processed.
				 */
				do_action( 'wxr_importer.process_failed.post', $post_id, $data, $meta, $comments, $terms ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
				return false;
			}

			// Ensure stickiness is handled correctly too.
			if ( '1' === $data['is_sticky'] ) {
				stick_post( $post_id );
			}

			// map pre-import ID to local ID.
			$this->mapping['post'][ $original_id ] = (int) $post_id;
			if ( $requires_remapping ) {
				$this->requires_remapping['post'][ $post_id ] = true;
			}
			$this->mark_post_exists( $data, $post_id );

			$this->logger->info(
				sprintf(
					/* translators: %1$s is the post title, %2$s is post type. */
					__( 'Imported "%1$s" (%2$s)', 'st-importer', 'astra-sites' ),
					$data['post_title'],
					$post_type_object->labels->singular_name
				)
			);
			$this->logger->debug(
				sprintf(
					/* translators: %1$s is the original post id, %2$s is old post id. */
					__( 'Post %1$d remapped to %2$d', 'st-importer', 'astra-sites' ),
					$original_id,
					$post_id
				)
			);

			// Handle the terms too.
			$terms = apply_filters( 'wp_import_post_terms', $terms, $post_id, $data );

			if ( ! empty( $terms ) ) {
				$term_ids = array();
				foreach ( $terms as $term ) {
					$taxonomy = $term['taxonomy'];
					$key      = sha1( $taxonomy . ':' . $term['slug'] );

					if ( isset( $this->mapping['term'][ $key ] ) ) {
						$term_ids[ $taxonomy ][] = (int) $this->mapping['term'][ $key ];
					} else {
						$meta[]             = array(
							'key'   => '_wxr_import_term',
							'value' => $term,
						);
						$requires_remapping = true;
					}
				}

				foreach ( $term_ids as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $data );
				}
			}

			$this->process_comments( $comments, $post_id, $data );
			$this->process_post_meta( $meta, $post_id, $data );

			if ( 'nav_menu_item' === $data['post_type'] ) {
				$this->process_menu_item_meta( $post_id, $data, $meta );
			}

			/**
			 * Post processing completed.
			 *
			 * @param int $post_id New post ID.
			 * @param array $data Raw data imported for the post.
			 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
			 * @param array $comments Raw comment data, already processed by {@see process_comments}.
			 * @param array $terms Raw term data, already processed.
			 */
			do_action( 'wxr_importer.processed.post', $post_id, $data, $meta, $comments, $terms ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
		}

		/**
		 * Attempt to create a new menu item from import data
		 *
		 * Fails for draft, orphaned menu items and those without an associated nav_menu
		 * or an invalid nav_menu term. If the post type or term object which the menu item
		 * represents doesn't exist then the menu item will not be imported (waits until the
		 * end of the import to retry again before discarding).
		 *
		 * @param int   $post_id New post ID.
		 * @param array $data Raw data imported for the post.
		 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
		 */
		protected function process_menu_item_meta( $post_id, $data, $meta ) {

			$item_type          = get_post_meta( $post_id, '_menu_item_type', true );
			$original_object_id = get_post_meta( $post_id, '_menu_item_object_id', true );
			$object_id          = null;

			$this->logger->debug( sprintf( 'Processing menu item %s', $item_type ) );

			$requires_remapping = false;
			switch ( $item_type ) {
				case 'taxonomy':
					if ( isset( $this->mapping['term_id'][ $original_object_id ] ) ) {
						$object_id = $this->mapping['term_id'][ $original_object_id ];
					} else {
						add_post_meta( $post_id, '_wxr_import_menu_item', wp_slash( $original_object_id ) );
						$requires_remapping = true;
					}
					break;

				case 'post_type':
					if ( isset( $this->mapping['post'][ $original_object_id ] ) ) {
						$object_id = $this->mapping['post'][ $original_object_id ];
					} else {
						add_post_meta( $post_id, '_wxr_import_menu_item', wp_slash( $original_object_id ) );
						$requires_remapping = true;
					}
					break;

				case 'custom':
					// Custom refers to itself, wonderfully easy.
					$object_id = $post_id;
					break;

				default:
					// associated object is missing or not imported yet, we'll retry later.
					$this->missing_menu_items[] = $item;
					$this->logger->debug( 'Unknown menu item type' );
					break;
			}

			if ( $requires_remapping ) {
				$this->requires_remapping['post'][ $post_id ] = true;
			}

			if ( empty( $object_id ) ) {
				// Nothing needed here.
				return;
			}

			$this->logger->debug( sprintf( 'Menu item %d mapped to %d', $original_object_id, $object_id ) );
			update_post_meta( $post_id, '_menu_item_object_id', wp_slash( $object_id ) );
		}

		/**
		 * If fetching attachments is enabled then attempt to create a new attachment
		 *
		 * @param array  $post Attachment post details from WXR.
		 * @param string $meta Raw meta data, already processed by {@see process_post_meta}.
		 * @param string $remote_url URL to fetch attachment from.
		 * @return int|WP_Error Post ID on success, WP_Error otherwise.
		 */
		protected function process_attachment( $post, $meta, $remote_url ) {
			// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
			// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload().
			$post['upload_date'] = $post['post_date'];
			foreach ( $meta as $meta_item ) {
				if ( '_wp_attached_file' !== $meta_item['key'] ) {
					continue;
				}

				if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_item['value'], $matches ) ) {
					$post['upload_date'] = $matches[0];
				}
				break;
			}

			// if the URL is absolute, but does not contain address, then upload it assuming base_site_url.
			if ( preg_match( '|^/[\w\W]+$|', $remote_url ) ) {
				$remote_url = rtrim( $this->base_url, '/' ) . $remote_url;
			}

			$upload = $this->fetch_remote_file( $remote_url, $post );
			if ( is_wp_error( $upload ) ) {
				return $upload;
			}

			$info = wp_check_filetype( $upload['file'] );
			if ( ! $info ) {
				return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'st-importer', 'astra-sites' ) );
			}

			$post['post_mime_type'] = $info['type'];

			// WP really likes using the GUID for display. Allow updating it.
			// See https://core.trac.wordpress.org/ticket/33386.
			if ( $this->options['update_attachment_guids'] ) {
				$post['guid'] = $upload['url'];
			}

			$post_id = wp_insert_attachment( $post, $upload['file'] );
			if ( is_wp_error( $post_id ) ) {
				return $post_id;
			}

			$attachment_metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
			wp_update_attachment_metadata( $post_id, $attachment_metadata );

			// Map this image URL later if we need to.
			$this->url_remap[ $remote_url ] = $upload['url'];

			// If we have a HTTPS URL, ensure the HTTP URL gets replaced too.
			if ( substr( $remote_url, 0, 8 ) === 'https://' ) {
				$insecure_url                     = 'http' . substr( $remote_url, 5 );
				$this->url_remap[ $insecure_url ] = $upload['url'];
			}

			return $post_id;
		}

		/**
		 * Parse a meta node into meta data.
		 *
		 * @param DOMElement $node Parent node of meta data (typically `wp:postmeta` or `wp:commentmeta`).
		 * @return array|null Meta data array on success, or null on error.
		 */
		protected function parse_meta_node( $node ) {
			foreach ( $node->childNodes as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				// We only care about child elements.
				if ( XML_ELEMENT_NODE !== $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $child->tagName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					case 'wp:meta_key':
						$key = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:meta_value':
						$value = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;
				}
			}

			if ( empty( $key ) || empty( $value ) ) {
				return null;
			}

			return compact( 'key', 'value' );
		}

		/**
		 * Process and import post meta items.
		 *
		 * @param array $meta List of meta data arrays.
		 * @param int   $post_id Post to associate with.
		 * @param array $post Post data.
		 * @return int|WP_Error Number of meta items imported on success, error otherwise.
		 */
		protected function process_post_meta( $meta, $post_id, $post ) {
			if ( empty( $meta ) ) {
				return true;
			}

			foreach ( $meta as $meta_item ) {
				/**
				 * Pre-process post meta data.
				 *
				 * @param array $meta_item Meta data. (Return empty to skip.)
				 * @param int $post_id Post the meta is attached to.
				 */
				$meta_item = apply_filters( 'wxr_importer.pre_process.post_meta', $meta_item, $post_id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
				if ( empty( $meta_item ) ) {
					return false;
				}

				$key   = apply_filters( 'import_post_meta_key', $meta_item['key'], $post_id, $post );
				$value = false;

				if ( '_edit_last' === $key ) {
					$value = intval( $meta_item['value'] );
					if ( ! isset( $this->mapping['user'][ $value ] ) ) {
						// Skip!
						continue;
					}

					$value = $this->mapping['user'][ $value ];
				}

				if ( $key ) {
					// export gets meta straight from the DB so could have a serialized string.
					if ( ! $value ) {
						$value = maybe_unserialize( $meta_item['value'] );
					}

					add_post_meta( $post_id, $key, $value );
					do_action( 'import_post_meta', $post_id, $key, $value );

					// if the post has a featured image, take note of this in case of remap.
					if ( '_thumbnail_id' === $key ) {
						$this->featured_images[ $post_id ] = (int) $value;
					}
				}
			}

			return true;
		}

		/**
		 * Parse a comment node into comment data.
		 *
		 * @param DOMElement $node Parent node of comment data (typically `wp:comment`).
		 * @return array Comment data array.
		 */
		protected function parse_comment_node( $node ) {
			$data = array(
				'commentmeta' => array(),
			);

			foreach ( $node->childNodes as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				// We only care about child elements.
				if ( XML_ELEMENT_NODE !== $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $child->tagName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					case 'wp:comment_id':
						$data['comment_id'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;
					case 'wp:comment_author':
						$data['comment_author'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_author_email':
						$data['comment_author_email'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_author_IP':
						$data['comment_author_IP'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_author_url':
						$data['comment_author_url'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_user_id':
						$data['comment_user_id'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_date':
						$data['comment_date'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_date_gmt':
						$data['comment_date_gmt'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_content':
						$data['comment_content'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_approved':
						$data['comment_approved'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_type':
						$data['comment_type'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:comment_parent':
						$data['comment_parent'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:commentmeta':
						$meta_item = $this->parse_meta_node( $child );
						if ( ! empty( $meta_item ) ) {
							$data['commentmeta'][] = $meta_item;
						}
						break;
				}
			}

			return $data;
		}

		/**
		 * Process and import comment data.
		 *
		 * @param array   $comments List of comment data arrays.
		 * @param int     $post_id Post to associate with.
		 * @param array   $post Post data.
		 * @param boolean $post_exists Post exist status.
		 * @return int|WP_Error Number of comments imported on success, error otherwise.
		 */
		protected function process_comments( $comments, $post_id, $post, $post_exists = false ) {

			$comments = apply_filters( 'wp_import_post_comments', $comments, $post_id, $post );
			if ( empty( $comments ) ) {
				return 0;
			}

			$num_comments = 0;

			// Sort by ID to avoid excessive remapping later.
			usort( $comments, array( $this, 'sort_comments_by_id' ) );

			foreach ( $comments as $key => $comment ) {
				/**
				 * Pre-process comment data
				 *
				 * @param array $comment Comment data. (Return empty to skip.)
				 * @param int $post_id Post the comment is attached to.
				 */
				$comment = apply_filters( 'wxr_importer.pre_process.comment', $comment, $post_id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
				if ( empty( $comment ) ) {
					return false;
				}

				$original_id = isset( $comment['comment_id'] ) ? (int) $comment['comment_id'] : 0;
				$parent_id   = isset( $comment['comment_parent'] ) ? (int) $comment['comment_parent'] : 0;
				$author_id   = isset( $comment['comment_user_id'] ) ? (int) $comment['comment_user_id'] : 0;

				// if this is a new post we can skip the comment_exists() check
				// TODO: Check comment_exists for performance.
				if ( $post_exists ) {
					$existing = $this->comment_exists( $comment );
					if ( $existing ) {

						/**
						 * Comment processing already imported.
						 *
						 * @param array $comment Raw data imported for the comment.
						 */
						do_action( 'wxr_importer.process_already_imported.comment', $comment ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.

						$this->mapping['comment'][ $original_id ] = $existing;
						continue;
					}
				}

				// Remove meta from the main array.
				$meta = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
				unset( $comment['commentmeta'] );

				// Map the parent comment, or mark it as one we need to fix.
				$requires_remapping = false;
				if ( $parent_id ) {
					if ( isset( $this->mapping['comment'][ $parent_id ] ) ) {
						$comment['comment_parent'] = $this->mapping['comment'][ $parent_id ];
					} else {
						// Prepare for remapping later.
						$meta[]             = array(
							'key'   => '_wxr_import_parent',
							'value' => $parent_id,
						);
						$requires_remapping = true;

						// Wipe the parent for now.
						$comment['comment_parent'] = 0;
					}
				}

				// Map the author, or mark it as one we need to fix.
				if ( $author_id ) {
					if ( isset( $this->mapping['user'][ $author_id ] ) ) {
						$comment['user_id'] = $this->mapping['user'][ $author_id ];
					} else {
						// Prepare for remapping later.
						$meta[]             = array(
							'key'   => '_wxr_import_user',
							'value' => $author_id,
						);
						$requires_remapping = true;

						// Wipe the user for now.
						$comment['user_id'] = 0;
					}
				}

				// Run standard core filters.
				$comment['comment_post_ID'] = $post_id;
				$comment                    = wp_filter_comment( $comment );

				// wp_insert_comment expects slashed data.
				$comment_id                               = wp_insert_comment( wp_slash( $comment ) );
				$this->mapping['comment'][ $original_id ] = $comment_id;
				if ( $requires_remapping ) {
					$this->requires_remapping['comment'][ $comment_id ] = true;
				}
				$this->mark_comment_exists( $comment, $comment_id );

				/**
				 * Comment has been imported.
				 *
				 * @param int $comment_id New comment ID
				 * @param array $comment Comment inserted (`comment_id` item refers to the original ID)
				 * @param int $post_id Post parent of the comment
				 * @param array $post Post data
				 */
				do_action( 'wp_import_insert_comment', $comment_id, $comment, $post_id, $post );

				// Process the meta items.
				foreach ( $meta as $meta_item ) {
					$value = maybe_unserialize( $meta_item['value'] );
					add_comment_meta( $comment_id, wp_slash( $meta_item['key'] ), wp_slash( $value ) );
				}

				/**
				 * Post processing completed.
				 *
				 * @param int $post_id New post ID.
				 * @param array $comment Raw data imported for the comment.
				 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
				 * @param array $post_id Parent post ID.
				 */
				do_action( 'wxr_importer.processed.comment', $comment_id, $comment, $meta, $post_id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.

				$num_comments++;
			}

			return $num_comments;
		}

		/**
		 * Parse Category Node
		 *
		 * @param  object $node Category Node.
		 * @return array
		 */
		protected function parse_category_node( $node ) {
			$data = array(
				// Default taxonomy to "category", since this is a `<category>` tag.
				'taxonomy' => 'category',
			);
			$meta = array();

			if ( $node->hasAttribute( 'domain' ) ) {
				$data['taxonomy'] = $node->getAttribute( 'domain' );
			}
			if ( $node->hasAttribute( 'nicename' ) ) {
				$data['slug'] = $node->getAttribute( 'nicename' );
			}

			$data['name'] = $node->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.

			if ( empty( $data['slug'] ) ) {
				return null;
			}

			// Just for extra compatibility.
			if ( 'tag' === $data['taxonomy'] ) {
				$data['taxonomy'] = 'post_tag';
			}

			return $data;
		}

		/**
		 * Callback for `usort` to sort comments by ID
		 *
		 * @param array $a Comment data for the first comment.
		 * @param array $b Comment data for the second comment.
		 * @return int
		 */
		public static function sort_comments_by_id( $a, $b ) {
			if ( empty( $a['comment_id'] ) ) {
				return 1;
			}

			if ( empty( $b['comment_id'] ) ) {
				return -1;
			}

			return $a['comment_id'] - $b['comment_id'];
		}

		/**
		 * Parse Author Node
		 *
		 * @param  object $node Author Node.
		 * @return array
		 */
		protected function parse_author_node( $node ) {
			$data = array();
			$meta = array();
			foreach ( $node->childNodes as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				// We only care about child elements.
				if ( XML_ELEMENT_NODE !== $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				switch ( $child->tagName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					case 'wp:author_login':
						$data['user_login'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:author_id':
						$data['ID'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:author_email':
						$data['user_email'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:author_display_name':
						$data['display_name'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:author_first_name':
						$data['first_name'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;

					case 'wp:author_last_name':
						$data['last_name'] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
						break;
				}
			}

			return compact( 'data', 'meta' );
		}

		/**
		 * Process Author
		 *
		 * @param array $data User data. (Return empty to skip.).
		 * @param array $meta Meta data.
		 * @return boolean
		 */
		protected function process_author( $data, $meta ) {
			/**
			 * Pre-process user data.
			 *
			 * @param array $data User data. (Return empty to skip.)
			 * @param array $meta Meta data.
			 */
			$data = apply_filters( 'wxr_importer.pre_process.user', $data, $meta ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
			if ( empty( $data ) ) {
				return false;
			}

			// Have we already handled this user?
			$original_id   = isset( $data['ID'] ) ? $data['ID'] : 0;
			$original_slug = $data['user_login'];

			if ( isset( $this->mapping['user'][ $original_id ] ) ) {
				$existing = $this->mapping['user'][ $original_id ];

				// Note the slug mapping if we need to too.
				if ( ! isset( $this->mapping['user_slug'][ $original_slug ] ) ) {
					$this->mapping['user_slug'][ $original_slug ] = $existing;
				}

				return false;
			}

			if ( isset( $this->mapping['user_slug'][ $original_slug ] ) ) {
				$existing = $this->mapping['user_slug'][ $original_slug ];

				// Ensure we note the mapping too.
				$this->mapping['user'][ $original_id ] = $existing;

				return false;
			}

			// Allow overriding the user's slug.
			$login = $original_slug;
			if ( isset( $this->user_slug_override[ $login ] ) ) {
				$login = $this->user_slug_override[ $login ];
			}

			$userdata = array(
				'user_login' => sanitize_user( $login, true ),
				'user_pass'  => wp_generate_password(),
			);

			$allowed = array(
				'user_email'   => true,
				'display_name' => true,
				'first_name'   => true,
				'last_name'    => true,
			);
			foreach ( $data as $key => $value ) {
				if ( ! isset( $allowed[ $key ] ) ) {
					continue;
				}

				$userdata[ $key ] = $data[ $key ];
			}

			$user_id = wp_insert_user( wp_slash( $userdata ) );
			if ( is_wp_error( $user_id ) ) {
				$this->logger->error(
					sprintf(
						/* translators: %s user login name  */
						__( 'Failed to import user "%s"', 'st-importer', 'astra-sites' ),
						$userdata['user_login']
					)
				);
				$this->logger->debug( $user_id->get_error_message() );

				/**
				 * User processing failed.
				 *
				 * @param WP_Error $user_id Error object.
				 * @param array $userdata Raw data imported for the user.
				 */
				do_action( 'wxr_importer.process_failed.user', $user_id, $userdata ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
				return false;
			}

			if ( $original_id ) {
				$this->mapping['user'][ $original_id ] = $user_id;
			}
			$this->mapping['user_slug'][ $original_slug ] = $user_id;

			$this->logger->info(
				sprintf(
					/* translators: %s user login name  */
					__( 'Imported user "%s"', 'st-importer', 'astra-sites' ),
					$userdata['user_login']
				)
			);
			$this->logger->debug(
				sprintf(
					/* translators: %1$s original user Id, %2$s old user Id.  */
					__( 'User %1$d remapped to %2$d', 'st-importer', 'astra-sites' ),
					$original_id,
					$user_id
				)
			);

			// TODO: Implement meta handling once WXR includes it.
			/**
			 * User processing completed.
			 *
			 * @param int $user_id New user ID.
			 * @param array $userdata Raw data imported for the user.
			 */
			do_action( 'wxr_importer.processed.user', $user_id, $userdata ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
		}

		/**
		 * Process Term
		 *
		 * @param object $node Term node.
		 * @param string $type Term type.
		 * @return array
		 */
		protected function parse_term_node( $node, $type = 'term' ) {
			$data = array();
			$meta = array();

			$tag_name = array(
				'id'          => 'wp:term_id',
				'taxonomy'    => 'wp:term_taxonomy',
				'slug'        => 'wp:term_slug',
				'parent'      => 'wp:term_parent',
				'name'        => 'wp:term_name',
				'description' => 'wp:term_description',
			);
			$taxonomy = null;

			// Special casing!
			switch ( $type ) {
				case 'category':
					$tag_name['slug']        = 'wp:category_nicename';
					$tag_name['parent']      = 'wp:category_parent';
					$tag_name['name']        = 'wp:cat_name';
					$tag_name['description'] = 'wp:category_description';
					$tag_name['taxonomy']    = null;

					$data['taxonomy'] = 'category';
					break;

				case 'tag':
					$tag_name['slug']        = 'wp:tag_slug';
					$tag_name['parent']      = null;
					$tag_name['name']        = 'wp:tag_name';
					$tag_name['description'] = 'wp:tag_description';
					$tag_name['taxonomy']    = null;

					$data['taxonomy'] = 'post_tag';
					break;
			}

			foreach ( $node->childNodes as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				// We only care about child elements.
				if ( XML_ELEMENT_NODE !== $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
					continue;
				}

				$key = array_search( $child->tagName, $tag_name ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.PHP.StrictInArray.MissingTrueStrict -- 3rd party library.
				if ( $key ) {
					$data[ $key ] = $child->textContent; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- 3rd party library.
				}
			}

			if ( empty( $data['taxonomy'] ) ) {
				return null;
			}

			// Compatibility with WXR 1.0.
			if ( 'tag' === $data['taxonomy'] ) {
				$data['taxonomy'] = 'post_tag';
			}

			return compact( 'data', 'meta' );
		}

		/**
		 * Process Term
		 *
		 * @param array $data Term data. (Return empty to skip.).
		 * @param array $meta Meta data.
		 * @return boolean
		 */
		protected function process_term( $data, $meta ) {
			/**
			 * Pre-process term data.
			 *
			 * @param array $data Term data. (Return empty to skip.)
			 * @param array $meta Meta data.
			 */
			$data = apply_filters( 'wxr_importer.pre_process.term', $data, $meta ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
			if ( empty( $data ) ) {
				return false;
			}

			$original_id = isset( $data['id'] ) ? (int) $data['id'] : 0;
			$parent_id   = isset( $data['parent'] ) ? (int) $data['parent'] : 0;

			$mapping_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );
			$existing    = $this->term_exists( $data );
			if ( $existing ) {

				/**
				 * Term processing already imported.
				 *
				 * @param array $data Raw data imported for the term.
				 */
				do_action( 'wxr_importer.process_already_imported.term', $data ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.

				$this->mapping['term'][ $mapping_key ]    = $existing;
				$this->mapping['term_id'][ $original_id ] = $existing;
				return false;
			}

			// WP really likes to repeat itself in export files.
			if ( isset( $this->mapping['term'][ $mapping_key ] ) ) {
				return false;
			}

			$termdata = array();
			$allowed  = array(
				'slug'        => true,
				'description' => true,
			);

			// Map the parent comment, or mark it as one we need to fix
			// TODO: add parent mapping and remapping.
			// phpcs:disable
			/*
			$requires_remapping = false;
			if ( $parent_id ) {
				if ( isset( $this->mapping['term'][ $parent_id ] ) ) {
					$data['parent'] = $this->mapping['term'][ $parent_id ];
				} else {
					// Prepare for remapping later
					$meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
					$requires_remapping = true;

					// Wipe the parent for now
					$data['parent'] = 0;
				}
			}*/
			// phpcs:enable

			foreach ( $data as $key => $value ) {
				if ( ! isset( $allowed[ $key ] ) ) {
					continue;
				}

				$termdata[ $key ] = $data[ $key ];
			}

			$result = wp_insert_term( $data['name'], $data['taxonomy'], $termdata );
			if ( is_wp_error( $result ) ) {
				$this->logger->warning(
					sprintf(
						/* translators: %1$s is the taxonomy, %2$s is taxonomy name. */
						__( 'Failed to import %1$s %2$s', 'st-importer', 'astra-sites' ),
						$data['taxonomy'],
						$data['name']
					)
				);
				$this->logger->debug( $result->get_error_message() );
				do_action( 'wp_import_insert_term_failed', $result, $data );

				/**
				 * Term processing failed.
				 *
				 * @param WP_Error $result Error object.
				 * @param array $data Raw data imported for the term.
				 * @param array $meta Meta data supplied for the term.
				 */
				do_action( 'wxr_importer.process_failed.term', $result, $data, $meta ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
				return false;
			}

			$term_id = $result['term_id'];

			$this->mapping['term'][ $mapping_key ]    = $term_id;
			$this->mapping['term_id'][ $original_id ] = $term_id;

			$this->logger->info(
				sprintf(
					/* translators: %1$s is the taxonomy name, %2$s is taxonomy. */
					__( 'Imported "%1$s" (%2$s)', 'st-importer', 'astra-sites' ),
					$data['name'],
					$data['taxonomy']
				)
			);
			$this->logger->debug(
				sprintf(
					/* translators: %1$s is term original id, %2$s is term id. */
					__( 'Term %1$d remapped to %2$d', 'st-importer', 'astra-sites' ),
					$original_id,
					$term_id
				)
			);

			do_action( 'wp_import_insert_term', $term_id, $data );

			/**
			 * Term processing completed.
			 *
			 * @param int $term_id New term ID.
			 * @param array $data Raw data imported for the term.
			 */
			do_action( 'wxr_importer.processed.term', $term_id, $data ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- 3rd party library.
		}

		/**
		 * Attempt to download a remote file attachment
		 *
		 * @param string $url URL of item to fetch.
		 * @param array  $post Attachment details.
		 * @return array|WP_Error Local file location details on success, WP_Error otherwise.
		 */
		protected function fetch_remote_file( $url, $post ) {
			// extract the file name and extension from the url.
			$file_name = basename( $url );

			// get placeholder file in the upload dir with a unique, sanitized filename.
			$upload = wp_upload_bits( $file_name, null, '', $post['upload_date'] );
			if ( $upload['error'] ) {
				return new WP_Error( 'upload_dir_error', $upload['error'] );
			}

			// fetch the remote url and write it to the placeholder file.
			$response = wp_remote_get(
				$url,
				array(
					'stream'   => true,
					'filename' => $upload['file'],
				)
			);

			// request failed.
			if ( is_wp_error( $response ) ) {
				unlink( $upload['file'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- 3rd party library.
				return $response;
			}

			$code = (int) wp_remote_retrieve_response_code( $response );

			// make sure the fetch was successful.
			if ( 200 !== $code ) {
				unlink( $upload['file'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- 3rd party library.
				return new WP_Error(
					'import_file_error',
					sprintf(
						/* translators: %1$s is error code, %2$s is error code header, %3$s is url. */
						__( 'Remote server returned %1$d %2$s for %3$s', 'st-importer', 'astra-sites' ),
						$code,
						get_status_header_desc( $code ),
						$url
					)
				);
			}

			$filesize = filesize( $upload['file'] );
			$headers  = wp_remote_retrieve_headers( $response );

			if ( isset( $headers['content-length'] ) && $filesize !== (int) $headers['content-length'] ) {
				unlink( $upload['file'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- 3rd party library.
				return new WP_Error( 'import_file_error', __( 'Remote file is incorrect size', 'st-importer', 'astra-sites' ) );
			}

			if ( 0 === $filesize ) {
				unlink( $upload['file'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- 3rd party library.
				return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'st-importer', 'astra-sites' ) );
			}

			$max_size = (int) $this->max_attachment_size();
			if ( ! empty( $max_size ) && $filesize > $max_size ) {
				unlink( $upload['file'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- 3rd party library.
				/* translators: %s max file size. */
				$message = sprintf( __( 'Remote file is too large, limit is %s', 'st-importer', 'astra-sites' ), size_format( $max_size ) );
				return new WP_Error( 'import_file_error', $message );
			}

			return $upload;
		}

		/**
		 * Post process
		 */
		protected function post_process() {
			// Time to tackle any left-over bits.
			if ( ! empty( $this->requires_remapping['post'] ) ) {
				$this->post_process_posts( $this->requires_remapping['post'] );
			}
			if ( ! empty( $this->requires_remapping['comment'] ) ) {
				$this->post_process_comments( $this->requires_remapping['comment'] );
			}
		}

		/**
		 * Post Process Posts
		 *
		 * @param  array $todo Todo items.
		 * @return void
		 */
		protected function post_process_posts( $todo ) {
			foreach ( $todo as $post_id => $_ ) {
				$this->logger->debug(
					sprintf(
						// Note: title intentionally not used to skip extra processing.
						// for when debug logging is off.
						/* translators: %d is post id. */
						__( 'Running post-processing for post %d', 'st-importer', 'astra-sites' ),
						$post_id
					)
				);

				$data = array();

				$parent_id = get_post_meta( $post_id, '_wxr_import_parent', true );
				if ( ! empty( $parent_id ) ) {
					// Have we imported the parent now?
					if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
						$data['post_parent'] = $this->mapping['post'][ $parent_id ];
					} else {
						$this->logger->warning(
							sprintf(
								/* translators: %1$s is post title, %2$s is post id. */
								__( 'Could not find the post parent for "%1$s" (post #%2$d)', 'st-importer', 'astra-sites' ),
								get_the_title( $post_id ),
								$post_id
							)
						);
						$this->logger->debug(
							sprintf(
								/* translators: %1$d is post id, %2$d is parent post id. */
								__( 'Post %1$d was imported with parent %2$d, but could not be found', 'st-importer', 'astra-sites' ),
								$post_id,
								$parent_id
							)
						);
					}
				}

				$author_slug = get_post_meta( $post_id, '_wxr_import_user_slug', true );
				if ( ! empty( $author_slug ) ) {
					// Have we imported the user now?
					if ( isset( $this->mapping['user_slug'][ $author_slug ] ) ) {
						$data['post_author'] = $this->mapping['user_slug'][ $author_slug ];
					} else {
						$this->logger->warning(
							sprintf(
								/* translators: %1$s is the post title, %2$s is post id. */
								__( 'Could not find the author for "%1$s" (post #%2$d)', 'st-importer', 'astra-sites' ),
								get_the_title( $post_id ),
								$post_id
							)
						);
						$this->logger->debug(
							sprintf(
								/* translators: %1$d is post id, %2$s is author slug. */
								__( 'Post %1$d was imported with author "%2$s", but could not be found', 'st-importer', 'astra-sites' ),
								$post_id,
								$author_slug
							)
						);
					}
				}

				$has_attachments = get_post_meta( $post_id, '_wxr_import_has_attachment_refs', true );
				if ( ! empty( $has_attachments ) ) {
					$post    = get_post( $post_id );
					$content = $post->post_content;

					// Replace all the URLs we've got.
					$new_content = str_replace( array_keys( $this->url_remap ), $this->url_remap, $content );
					if ( $new_content !== $content ) {
						$data['post_content'] = $new_content;
					}
				}

				if ( get_post_type( $post_id ) === 'nav_menu_item' ) {
					$this->post_process_menu_item( $post_id );
				}

				// Do we have updates to make?
				if ( empty( $data ) ) {
					$this->logger->debug(
						sprintf(
							/* translators: %d is post id. */
							__( 'Post %d was marked for post-processing, but none was required.', 'st-importer', 'astra-sites' ),
							$post_id
						)
					);
					continue;
				}

				// Run the update.
				$data['ID'] = $post_id;

				$data = wp_slash( $data );

				$result = wp_update_post( $data, true );
				if ( is_wp_error( $result ) ) {
					$this->logger->warning(
						sprintf(
							/* translators: %1$s is the post title, %2$s is post id. */
							__( 'Could not update "%1$s" (post #%2$d) with mapped data', 'st-importer', 'astra-sites' ),
							get_the_title( $post_id ),
							$post_id
						)
					);
					$this->logger->debug( $result->get_error_message() );
					continue;
				}

				// Clear out our temporary meta keys.
				delete_post_meta( $post_id, '_wxr_import_parent' );
				delete_post_meta( $post_id, '_wxr_import_user_slug' );
				delete_post_meta( $post_id, '_wxr_import_has_attachment_refs' );
			}
		}

		/**
		 * Post process menu item
		 *
		 * @param  int $post_id Post id.
		 * @return mixed
		 */
		protected function post_process_menu_item( $post_id ) {
			$menu_object_id = get_post_meta( $post_id, '_wxr_import_menu_item', true );
			if ( empty( $menu_object_id ) ) {
				// No processing needed!
				return;
			}

			$menu_item_type = get_post_meta( $post_id, '_menu_item_type', true );
			switch ( $menu_item_type ) {
				case 'taxonomy':
					if ( isset( $this->mapping['term_id'][ $menu_object_id ] ) ) {
						$menu_object = $this->mapping['term_id'][ $menu_object_id ];
					}
					break;

				case 'post_type':
					if ( isset( $this->mapping['post'][ $menu_object_id ] ) ) {
						$menu_object = $this->mapping['post'][ $menu_object_id ];
					}
					break;

				default:
					// Cannot handle this.
					return;
			}

			if ( ! empty( $menu_object ) ) {
				update_post_meta( $post_id, '_menu_item_object_id', wp_slash( $menu_object ) );
			} else {
				$this->logger->warning(
					sprintf(
						/* translators: %1$s is the post title, %2$s is post id. */
						__( 'Could not find the menu object for "%1$s" (post #%2$d)', 'st-importer', 'astra-sites' ),
						get_the_title( $post_id ),
						$post_id
					)
				);
				$this->logger->debug(
					sprintf(
						/* translators: %1$s is post id, %2$s is post object id, %3$s is menu type. */
						__( 'Post %1$d was imported with object "%2$d" of type "%3$s", but could not be found', 'st-importer', 'astra-sites' ),
						$post_id,
						$menu_object_id,
						$menu_item_type
					)
				);
			}

			delete_post_meta( $post_id, '_wxr_import_menu_item' );
		}


		/**
		 * Post process comments
		 *
		 * @param  array $todo Todo items.
		 * @return void
		 */
		protected function post_process_comments( $todo ) {
			foreach ( $todo as $comment_id => $_ ) {
				$data = array();

				$parent_id = get_comment_meta( $comment_id, '_wxr_import_parent', true );
				if ( ! empty( $parent_id ) ) {
					// Have we imported the parent now?
					if ( isset( $this->mapping['comment'][ $parent_id ] ) ) {
						$data['comment_parent'] = $this->mapping['comment'][ $parent_id ];
					} else {
						$this->logger->warning(
							sprintf(
								/* translators: %d is comment id. */
								__( 'Could not find the comment parent for comment #%d', 'st-importer', 'astra-sites' ),
								$comment_id
							)
						);
						$this->logger->debug(
							sprintf(
								/* translators: %1$s is comment id, %2$s is parent comment id. */
								__( 'Comment %1$d was imported with parent %2$d, but could not be found', 'st-importer', 'astra-sites' ),
								$comment_id,
								$parent_id
							)
						);
					}
				}

				$author_id = get_comment_meta( $comment_id, '_wxr_import_user', true );
				if ( ! empty( $author_id ) ) {
					// Have we imported the user now?
					if ( isset( $this->mapping['user'][ $author_id ] ) ) {
						$data['user_id'] = $this->mapping['user'][ $author_id ];
					} else {
						$this->logger->warning(
							sprintf(
								/* translators: %d is comment id. */
								__( 'Could not find the author for comment #%d', 'st-importer', 'astra-sites' ),
								$comment_id
							)
						);
						$this->logger->debug(
							sprintf(
								/* translators: %1$d is comment id, %2$d is author id. */
								__( 'Comment %1$d was imported with author %2$d, but could not be found', 'st-importer', 'astra-sites' ),
								$comment_id,
								$author_id
							)
						);
					}
				}

				// Do we have updates to make?
				if ( empty( $data ) ) {
					continue;
				}

				// Run the update.
				$data['comment_ID'] = $comment_ID; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- 3rd party library.
				$result             = wp_update_comment( wp_slash( $data ) );
				if ( empty( $result ) ) {
					$this->logger->warning(
						sprintf(
							/* translators: %d is comment id. */
							__( 'Could not update comment #%d with mapped data', 'st-importer', 'astra-sites' ),
							$comment_id
						)
					);
					continue;
				}

				// Clear out our temporary meta keys.
				delete_comment_meta( $comment_id, '_wxr_import_parent' );
				delete_comment_meta( $comment_id, '_wxr_import_user' );
			}
		}

		/**
		 * Use stored mapping information to update old attachment URLs
		 */
		protected function replace_attachment_urls_in_content() {
			global $wpdb;
			// make sure we do the longest urls first, in case one is a substring of another.
			uksort( $this->url_remap, array( $this, 'cmpr_strlen' ) );

			foreach ( $this->url_remap as $from_url => $to_url ) {
				// remap urls in post_content.
				$query = $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url );
				$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- 3rd party library.

				// remap enclosure urls.
				$query  = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url );
				$result = $wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- 3rd party library.
			}
		}

		/**
		 * Update _thumbnail_id meta to new, imported attachment IDs
		 */
		public function remap_featured_images() {
			// cycle through posts that have a featured image.
			foreach ( $this->featured_images as $post_id => $value ) {
				if ( isset( $this->processed_posts[ $value ] ) ) {
					$new_id = $this->processed_posts[ $value ];

					// only update if there's a difference.
					if ( $new_id !== $value ) {
						update_post_meta( $post_id, '_thumbnail_id', $new_id );
					}
				}
			}
		}

		/**
		 * Decide if the given meta key maps to information we will want to import
		 *
		 * @param string $key The meta key to check.
		 * @return string|bool The key if we do want to import, false if not.
		 */
		public function is_valid_meta_key( $key ) {
			// skip attachment metadata since we'll regenerate it from scratch
			// skip _edit_lock as not relevant for import.
			if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- 3rd party library.
				return false;
			}

			return $key;
		}

		/**
		 * Decide what the maximum file size for downloaded attachments is.
		 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
		 *
		 * @return int Maximum attachment file size to import
		 */
		protected function max_attachment_size() {
			return apply_filters( 'import_attachment_size_limit', 0 );
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 *
		 * @param  string $val Timeout value.
		 * @access public
		 * @return int 60
		 */
		public function bump_request_timeout( $val ) {
			return 60;
		}

		/**
		 * Return the difference in length between two strings.
		 *
		 * @param  string $a String one.
		 * @param  string $b String two.
		 * @return string
		 */
		public function cmpr_strlen( $a, $b ) {
			return strlen( $b ) - strlen( $a );
		}

		/**
		 * Prefill existing post data.
		 *
		 * This preloads all GUIDs into memory, allowing us to avoid hitting the
		 * database when we need to check for existence. With larger imports, this
		 * becomes prohibitively slow to perform SELECT queries on each.
		 *
		 * By preloading all this data into memory, it's a constant-time lookup in
		 * PHP instead. However, this does use a lot more memory, so for sites doing
		 * small imports onto a large site, it may be a better tradeoff to use
		 * on-the-fly checking instead.
		 */
		protected function prefill_existing_posts() {
			global $wpdb;
			$posts = $wpdb->get_results( "SELECT ID, guid FROM {$wpdb->posts}" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- 3rd party library.

			foreach ( $posts as $item ) {
				$this->exists['post'][ $item->guid ] = $item->ID;
			}
		}

		/**
		 * Does the post exist?
		 *
		 * @param array $data Post data to check against.
		 * @return int|bool Existing post ID if it exists, false otherwise.
		 */
		protected function post_exists( $data ) {
			// Constant-time lookup if we prefilled.
			$exists_key = $data['guid'];

			if ( $this->options['prefill_existing_posts'] ) {
				return isset( $this->exists['post'][ $exists_key ] ) ? $this->exists['post'][ $exists_key ] : false;
			}

			// No prefilling, but might have already handled it.
			if ( isset( $this->exists['post'][ $exists_key ] ) ) {
				return $this->exists['post'][ $exists_key ];
			}

			// Still nothing, try post_exists, and cache it.
			$exists                              = post_exists( $data['post_title'], $data['post_content'], $data['post_date'], $data['post_type'] );
			$this->exists['post'][ $exists_key ] = $exists;

			return $exists;
		}

		/**
		 * Mark the post as existing.
		 *
		 * @param array $data Post data to mark as existing.
		 * @param int   $post_id Post ID.
		 */
		protected function mark_post_exists( $data, $post_id ) {
			$exists_key                          = $data['guid'];
			$this->exists['post'][ $exists_key ] = $post_id;
		}

		/**
		 * Prefill existing comment data.
		 *
		 * @see self::prefill_existing_posts() for justification of why this exists.
		 */
		protected function prefill_existing_comments() {
			global $wpdb;
			$posts = $wpdb->get_results( "SELECT comment_ID, comment_author, comment_date FROM {$wpdb->comments}" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- 3rd party library.

			foreach ( $posts as $item ) {
				$exists_key                             = sha1( $item->comment_author . ':' . $item->comment_date );
				$this->exists['comment'][ $exists_key ] = $item->comment_ID;
			}
		}

		/**
		 * Does the comment exist?
		 *
		 * @param array $data Comment data to check against.
		 * @return int|bool Existing comment ID if it exists, false otherwise.
		 */
		protected function comment_exists( $data ) {
			$exists_key = sha1( $data['comment_author'] . ':' . $data['comment_date'] );

			// Constant-time lookup if we prefilled.
			if ( $this->options['prefill_existing_comments'] ) {
				return isset( $this->exists['comment'][ $exists_key ] ) ? $this->exists['comment'][ $exists_key ] : false;
			}

			// No prefilling, but might have already handled it.
			if ( isset( $this->exists['comment'][ $exists_key ] ) ) {
				return $this->exists['comment'][ $exists_key ];
			}

			// Still nothing, try comment_exists, and cache it.
			$exists                                 = comment_exists( $data['comment_author'], $data['comment_date'] );
			$this->exists['comment'][ $exists_key ] = $exists;

			return $exists;
		}

		/**
		 * Mark the comment as existing.
		 *
		 * @param array $data Comment data to mark as existing.
		 * @param int   $comment_id Comment ID.
		 */
		protected function mark_comment_exists( $data, $comment_id ) {
			$exists_key                             = sha1( $data['comment_author'] . ':' . $data['comment_date'] );
			$this->exists['comment'][ $exists_key ] = $comment_id;
		}

		/**
		 * Prefill existing term data.
		 *
		 * @see self::prefill_existing_posts() for justification of why this exists.
		 */
		protected function prefill_existing_terms() {
			global $wpdb;
			$query  = "SELECT t.term_id, tt.taxonomy, t.slug FROM {$wpdb->terms} AS t";
			$query .= " JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
			$terms  = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- 3rd party library.

			foreach ( $terms as $item ) {
				$exists_key                          = sha1( $item->taxonomy . ':' . $item->slug );
				$this->exists['term'][ $exists_key ] = $item->term_id;
			}
		}

		/**
		 * Does the term exist?
		 *
		 * @param array $data Term data to check against.
		 * @return int|bool Existing term ID if it exists, false otherwise.
		 */
		protected function term_exists( $data ) {
			$exists_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );

			// Constant-time lookup if we prefilled.
			if ( $this->options['prefill_existing_terms'] ) {
				return isset( $this->exists['term'][ $exists_key ] ) ? $this->exists['term'][ $exists_key ] : false;
			}

			// No prefilling, but might have already handled it.
			if ( isset( $this->exists['term'][ $exists_key ] ) ) {
				return $this->exists['term'][ $exists_key ];
			}

			// Still nothing, try comment_exists, and cache it.
			$exists = term_exists( $data['slug'], $data['taxonomy'] );
			if ( is_array( $exists ) ) {
				$exists = $exists['term_id'];
			}

			$this->exists['term'][ $exists_key ] = $exists;

			return $exists;
		}

		/**
		 * Mark the term as existing.
		 *
		 * @param array $data Term data to mark as existing.
		 * @param int   $term_id Term ID.
		 */
		protected function mark_term_exists( $data, $term_id ) {
			$exists_key                          = sha1( $data['taxonomy'] . ':' . $data['slug'] );
			$this->exists['term'][ $exists_key ] = $term_id;
		}
	}
endif;
