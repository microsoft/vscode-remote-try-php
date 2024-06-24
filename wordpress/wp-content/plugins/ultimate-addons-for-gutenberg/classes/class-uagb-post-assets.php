<?php
/**
 * UAGB Post Base.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAGB_Post_Assets.
 */
class UAGB_Post_Assets {

	/**
	 * Current Block List
	 *
	 * @since 1.13.4
	 * @var array
	 */
	public $current_block_list = array();

	/**
	 * UAG Block Flag
	 *
	 * @since 1.13.4
	 * @var uag_flag
	 */
	public $uag_flag = false;

	/**
	 * UAG FAQ Layout Flag
	 *
	 * @since 1.18.1
	 * @var uag_faq_layout
	 */
	public $uag_faq_layout = false;

	/**
	 * UAG File Generation Flag
	 *
	 * @since 1.14.0
	 * @var string
	 */
	public $file_generation = 'disabled';

	/**
	 * UAG File Generation Flag
	 *
	 * @since 1.14.0
	 * @var file_generation
	 */
	public $is_allowed_assets_generation = false;

	/**
	 * UAG File Generation Fallback Flag for CSS
	 *
	 * @since 1.15.0
	 * @var file_generation
	 */
	public $fallback_css = false;

	/**
	 * UAG File Generation Fallback Flag for JS
	 *
	 * @since 1.15.0
	 * @var file_generation
	 */
	public $fallback_js = false;

	/**
	 * Enqueue Style and Script Variable
	 *
	 * @since 1.14.0
	 * @var instance
	 */
	public $assets_file_handler = array();

	/**
	 * Stylesheet
	 *
	 * @since 1.13.4
	 * @var string
	 */
	public $stylesheet = '';

	/**
	 * Script
	 *
	 * @since 1.13.4
	 * @var script
	 */
	public $script = '';

	/**
	 * Page Blocks Variable
	 *
	 * @since 1.6.0
	 * @var instance
	 */
	public $page_blocks;

	/**
	 * Google fonts to enqueue
	 *
	 * @var array
	 */
	public $gfonts = array();

	/**
	 * Google fonts preload files
	 *
	 * @var array
	 */
	public $gfonts_files = array();

	/**
	 * Google fonts url to enqueue
	 *
	 * @var string
	 */
	public $gfonts_url = '';


	/**
	 * Load Google fonts locally
	 *
	 * @var string
	 */
	public $load_gfonts_locally = '';

	/**
	 * Preload google fonts files from local
	 *
	 * @var string
	 */
	public $preload_local_fonts = '';

	/**
	 * Static CSS Added Array
	 *
	 * @since 1.23.0
	 * @var array
	 */
	public $static_css_blocks = array();

	/**
	 * Static CSS Added Array
	 *
	 * @since 1.23.0
	 * @var array
	 */
	public static $conditional_blocks_printed = false;

	/**
	 * Post ID
	 *
	 * @since 1.23.0
	 * @var integer
	 */
	protected $post_id;

	/**
	 * Preview
	 *
	 * @since 1.24.2
	 * @var preview
	 */
	public $preview = false;

	/**
	 * Load UAG Fonts Flag.
	 *
	 * @since 2.0.0
	 * @var preview
	 */
	public $load_uag_fonts = true;

	/**
	 * Common Assets Added.
	 *
	 * @since 2.0.0
	 * @var preview
	 */
	public static $common_assets_added = false;

	/**
	 * Custom CSS Appended Flag
	 *
	 * @since 2.1.0
	 * @var custom_css_appended
	 */
	public static $custom_css_appended = false;

	/**
	 * Is current post a revision.
	 *
	 * @since 2.6.2
	 * @var is_post_revision
	 */
	public $is_post_revision = false;

	/**
	 * Constructor
	 *
	 * @param int $post_id Post ID.
	 */
	public function __construct( $post_id ) {

		$this->post_id = intval( $post_id );

		// For Spectra Global Block Styles.
		$this->spectra_gbs_load_gfonts();

		if ( wp_is_post_revision( $this->post_id ) ) {
			$this->is_post_revision = true;
		}

		$this->preview = isset( $_GET['preview'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->load_uag_fonts = apply_filters( 'uagb_enqueue_google_fonts', $this->load_uag_fonts );

		if ( $this->preview || $this->is_post_revision ) {
			$this->file_generation              = 'disabled';
			$this->is_allowed_assets_generation = true;
		} else {
			$this->file_generation              = UAGB_Helper::$file_generation;
			$this->is_allowed_assets_generation = $this->allow_assets_generation();
		}
		// Set other options.
		$this->load_gfonts_locally = UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_gfonts_locally', 'disabled' );
		$this->preload_local_fonts = UAGB_Admin_Helper::get_admin_settings_option( 'uag_preload_local_fonts', 'disabled' );

		if ( $this->is_allowed_assets_generation ) {
			global $post;
			$this_post = $this->preview ? $post : get_post( $this->post_id );
			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) { // Check if block theme is active.
				$what_post_type = $this->determine_template_post_type( $this->post_id ); // Determine template post type.
				$this->prepare_assets_for_templates_based_post_type( $what_post_type ); // Prepare assets for templates based on post type.
			}
			$this->prepare_assets( $this_post );
			if ( $this->preview ) { // Load CSS only in preview mode of block editor.
				$this->prepare_ast_custom_layout_post_assets();
			}
			$content = get_option( 'widget_block' );
			$this->prepare_widget_area_assets( $content );
		}
	}

	/**
	 * Get WooCommerce Template.
	 *
	 * @since 2.9.1
	 * @return bool|string The WooCommerce template if found, or false if not found.
	 */
	public function get_woocommerce_template() {
		// Check if WooCommerce is active.
		if ( class_exists( 'WooCommerce' ) ) {
			$is_order_received_page            = function_exists( 'is_order_received_page' ) && is_order_received_page();
			$is_checkout                       = function_exists( 'is_checkout' ) && is_checkout();
			$is_wc_order_received_endpoint_url = function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' );
			// Check other WooCommerce pages.
			switch ( true ) {
				// Check if the current page is the shop page.
				case is_cart():
					return 'page-cart';

				// Check if the current page is the checkout page.
				case $is_checkout:
					// Check if the current page is the order received page.
					if ( $is_order_received_page ) {
						return 'order-confirmation';
					}
					return 'page-checkout';

				// Check if the current page is the order received page.
				case $is_wc_order_received_endpoint_url:
					return 'order-confirmation';

				// Check if the current page is a product page.
				case is_product():
					return 'single-product';

				// Check if the current page is an archive page.
				case is_archive():
					// Retrieve the queried object.
					$object = get_queried_object();

					// Get all block templates.
					$template_types = get_block_templates();

					// Extract the 'slug' column from the block templates array.
					$template_type_slug = array_column( $template_types, 'slug' );

					// Check if the current request is a search and if the post type archive is for 'product'.
					$searchCondition = is_search() && is_post_type_archive( 'product' );

					// Switch statement to determine the template based on various conditions.
					switch ( true ) {
						// Case when the current page is a product taxonomy and the taxonomy is 'product_tag'.
						case ( is_product_taxonomy() && is_tax( 'product_tag' ) ) && $object instanceof WP_Term && ! in_array( 'taxonomy-' . $object->taxonomy . '-' . $object->slug, $template_type_slug ):
							// Check if 'taxonomy-product_tag' template exists in the template type slugs array.
							if ( in_array( 'taxonomy-product_tag', $template_type_slug ) ) {
								// Prepare assets for the 'taxonomy-product_tag' template.
								$this->prepare_assets_for_templates_based_post_type( 'taxonomy-product_tag' );
							}
							// Return the appropriate template based on the search condition.
							return $searchCondition ? 'product-search-results' : 'archive-product';

						// Case when the current page is a product taxonomy and the object is a term.
						case is_product_taxonomy() && $object instanceof WP_Term:
							// Check if the taxonomy is a product attribute.
							if ( taxonomy_is_product_attribute( $object->taxonomy ) ) {
								// Prepare assets for the 'archive-product' template if it exists in the template type slugs array.
								if ( in_array( 'archive-product', $template_type_slug ) ) {
									$this->prepare_assets_for_templates_based_post_type( 'archive-product' );
								}
								// Return the 'product-search-results' or 'taxonomy-product_attribute' template based on the search condition.
								return $searchCondition ? 'product-search-results' : 'taxonomy-product_attribute';
							} elseif ( ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) && in_array( 'taxonomy-' . $object->taxonomy . '-' . $object->slug, $template_type_slug ) ) {
								// Return the specific taxonomy template based on the search condition.
								return $searchCondition ? 'product-search-results' : 'taxonomy-' . $object->taxonomy . '-' . $object->slug;
							} else {
								// Prepare assets for the 'taxonomy-product_cat' template if it exists in the template type slugs array.
								if ( in_array( 'taxonomy-product_cat', $template_type_slug ) ) {
									$this->prepare_assets_for_templates_based_post_type( 'taxonomy-product_cat' );
								}
								// Return the appropriate template based on the search condition.
								return $searchCondition ? 'product-search-results' : 'archive-product';
							}
							break;

						// Case when the current page is the shop page.
						case is_shop():
							// Return the appropriate template based on the search condition.
							return $searchCondition ? 'product-search-results' : 'archive-product';

						default:
							// Return the appropriate template based on the search condition and the type of the queried object.
							return $searchCondition ? 'product-search-results' : ( ( $object instanceof WP_Post || $object instanceof WP_Post_Type || $object instanceof WP_Term || $object instanceof WP_User ) ? $this->get_archive_page_template( $object, $template_type_slug ) : 'archive-product' );
					}
					break;

				default:
					// Handle other cases if needed.
					break;
			}
		}
		return false;
	}

	/**
	 * Get archive page template for current post.
	 *
	 * @param object $archive_object of current post.
	 * @param array  $template_type_slug name.
	 * @since 2.12.8
	 * @return string The determined archive post type.
	 */
	public function get_archive_page_template( $archive_object, $template_type_slug ) {
		if ( is_author() && $archive_object instanceof WP_User ) { // For author archive or more specific author template.
			$author_slug = 'author-' . $archive_object->user_nicename;
			return in_array( $author_slug, $template_type_slug ) ? $author_slug : ( in_array( 'author', $template_type_slug ) ? 'author' : 'archive' );
		} elseif ( $archive_object instanceof WP_Term ) {
			if ( is_category() ) { // For category archive or more specific category template.
				$category_slug = 'category-' . $archive_object->slug;
				return in_array( $category_slug, $template_type_slug ) ? $category_slug : ( in_array( 'category', $template_type_slug ) ? 'category' : 'archive' );
			} elseif ( is_tag() ) { // For tag archive or more specific tag template.
				$tag_slug = 'tag-' . $archive_object->slug;
				return in_array( $tag_slug, $template_type_slug ) ? $tag_slug : ( in_array( 'tag', $template_type_slug ) ? 'tag' : 'archive' );
			}
		} elseif ( is_date() && in_array( 'date', $template_type_slug ) ) { // For date archive template.
			return 'date';
		} elseif ( $archive_object instanceof WP_Post_Type && is_post_type_archive() ) { // For custom post type archive or more specific custom post type archive template.
			$post_type_archive_slug = 'archive-' . $archive_object->name;
			return in_array( $post_type_archive_slug, $template_type_slug ) ? $post_type_archive_slug : ( in_array( 'archive', $template_type_slug ) ? 'archive' : 'archive-' . $archive_object->name );
		}
		return 'archive';
	}

	/**
	 * Determine template post type function.
	 *
	 * @param int $post_id of current post.
	 * @since 2.9.1
	 * @return string The determined post type.
	 */
	private function determine_template_post_type( $post_id ) {
		$get_woocommerce_template = $this->get_woocommerce_template(); // Get WooCommerce template.
		if ( is_string( $get_woocommerce_template ) ) { // Check if WooCommerce template is found.
			return $get_woocommerce_template; // WooCommerce templates to post type.
		}

		// Check if post id is passed.
		if ( ! empty( $post_id ) ) {
			$template_slug = get_page_template_slug( $post_id );
			if ( ! empty( $template_slug ) ) {
				return $template_slug;
			}
		}

		$conditional_to_post_type = array(
			'is_attachment' => 'attachment',
			'is_embed'      => 'embed',
			'is_front_page' => 'home',
			'is_home'       => 'home',
			'is_search'     => 'search',
			'is_paged'      => 'paged',
		); // Conditional tags to post type.

		$what_post_type     = '404'; // Default to '404' if no condition matches.
		$object             = get_queried_object();
		$template_types     = get_block_templates();
		$template_type_slug = array_column( $template_types, 'slug' );

		// Determines whether the query is for an existing single page.
		$is_regular_page        = is_page() && ! is_front_page();
		$is_front_page_template = is_front_page() && get_front_page_template();
		$is_static_front_page   = 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && is_front_page() && ! is_home() && ! $is_front_page_template;

		if ( $is_regular_page || $is_static_front_page ) { // Run only for page and any page selected as home page from settings > reading > static page.
			return 'page';
		} elseif ( $is_front_page_template ) { // Run only when is_home and is_front_page() and get_front_page_template() is true. i.e front-page template.
			return 'front-page';
		} elseif ( is_archive() ) { // Applies to archive pages.
			// If none of the above condition matches, return archive template.
			return ( $object instanceof WP_Post || $object instanceof WP_Post_Type || $object instanceof WP_Term || $object instanceof WP_User ) ? $this->get_archive_page_template( $object, $template_type_slug ) : 'archive';
		} else {
			if ( $object instanceof WP_Post && ! empty( $object->post_type ) ) {
				if ( is_singular() ) { // Applies to single post of any post type ( attachment, page, custom post types).
					$name_decoded = urldecode( $object->post_name );
					if ( in_array( 'single-' . $object->post_type . '-' . $name_decoded, $template_type_slug ) ) {
						return 'single-' . $object->post_type . '-' . $name_decoded;
					} elseif ( in_array( 'single-' . $object->post_type, $template_type_slug ) ) {
						return 'single-' . $object->post_type;
					} else { // If none of the above condition matches, return single template.
						return 'single';
					}
				}
			}
		}

		foreach ( $conditional_to_post_type as $conditional => $post_type ) {
			if ( $conditional() ) {
				$what_post_type = $post_type;
				break;
			}
		}

		return $what_post_type;
	}

	/**
	 * Generates assets for templates based on post type.
	 *
	 * @param string $post_type of current template.
	 * @since 2.9.1
	 * @return void
	 */
	public function prepare_assets_for_templates_based_post_type( $post_type ) {
		$template_slug    = $post_type;
		$current_template = get_block_templates( array( 'slug__in' => array( $template_slug ) ) ); // Get block templates based on post type.
		// Check if block templates were found.
		if ( ! empty( $current_template ) && is_array( $current_template ) ) {
			// Ensure the first template has content.
			if ( isset( $current_template[0]->content ) && has_blocks( $current_template[0]->content ) ) {
				$this->common_function_for_assets_preparation( $current_template[0]->content );
			}
		}
	}

	/**
	 * Generate assets of Astra custom layout post in preview
	 *
	 * @since 2.6.0
	 * @return void
	 */
	public function prepare_ast_custom_layout_post_assets() {

		if ( ! defined( 'ASTRA_ADVANCED_HOOKS_POST_TYPE' ) ) {
			return;
		}

		$option = array(
			'location'  => 'ast-advanced-hook-location',
			'exclusion' => 'ast-advanced-hook-exclusion',
			'users'     => 'ast-advanced-hook-users',
		);
		$result = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( ASTRA_ADVANCED_HOOKS_POST_TYPE, $option );

		if ( empty( $result ) || ! is_array( $result ) ) {
			return;
		}
		foreach ( $result as $post_id => $post_data ) {
			$custom_post = get_post( $post_id );
			$this->prepare_assets( $custom_post );
		}
	}

	/**
	 * Load Styles for Spectra Global Block Styles.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	public function spectra_gbs_load_styles() {
		// Check if GBS is enabled.
		$gbs_status                  = \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' );
		$spectra_global_block_styles = get_option( 'spectra_global_block_styles', array() );
		if ( empty( $spectra_global_block_styles ) || ! is_array( $spectra_global_block_styles ) ) {
			return;
		}

		if ( 'disabled' === $gbs_status ) {
			// Enqueue GBS default styles.
			foreach ( $spectra_global_block_styles as $style ) {

				if ( empty( $style['blockName'] ) || ! is_string( $style['blockName'] ) ) {
					continue;
				}

				// Check if uagb string exist in $block_name or not.
				if ( 0 !== strpos( $style['blockName'], 'uagb/' ) ) {
					continue;
				}

				$_block_slug = str_replace( 'uagb/', '', $style['blockName'] );

				// This is class name and file name.
				$file_names    = 'uagb-gbs-default-' . $_block_slug;
				$wp_upload_dir = \UAGB_Helper::get_uag_upload_dir_path();
				$wp_upload_url = UAGB_Helper::get_uag_upload_url_path();
				$file_dir      = $wp_upload_dir . $file_names . '.css';
				if ( file_exists( $file_dir ) ) {
					$file_url = $wp_upload_url . $file_names . '.css';
					wp_enqueue_style( $file_names, $file_url, array(), UAGB_VER, 'all' );
				}
			}
		}

		if ( 'enabled' !== $gbs_status ) {
			return;
		}

		$should_render_styles_in_fse_page = wp_is_block_theme() && ! get_queried_object();

		foreach ( $spectra_global_block_styles as $style ) {
			if ( ! empty( $style['value'] ) && ! empty( $style['frontendStyles'] ) ) {

				if ( ! empty( $style['post_ids'] ) && in_array( $this->post_id, $style['post_ids'] ) ) {
					$this->stylesheet = $style['frontendStyles'] . $this->stylesheet;
				} elseif ( $should_render_styles_in_fse_page && isset( $style['page_template_slugs'] ) && ! empty( $style['page_template_slugs'] ) ) {
					// Render in fse template.
					$this->stylesheet = $style['frontendStyles'] . $this->stylesheet;
				} elseif ( isset( $style['styleForGlobal'] ) && ! empty( $style['styleForGlobal'] ) ) {
					$this->stylesheet = $style['frontendStyles'] . $this->stylesheet;
				}
			}
		}
	}


	/**
	 * Load Google Fonts for Spectra Global Block Styles.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	public function spectra_gbs_load_gfonts() {

		$spectra_gbs_google_fonts = get_option( 'spectra_gbs_google_fonts', array() );

		if ( ! is_array( $spectra_gbs_google_fonts ) ) {
			return;
		}

		$families = array();
		foreach ( $spectra_gbs_google_fonts as $style ) {
			if ( is_array( $style ) ) {
				foreach ( $style as $family ) {
					if ( ! in_array( $family, $families, true ) ) {
						UAGB_Helper::blocks_google_font( true, $family, '' );
						$families[] = $family;
					}
				}
			}
		}
	}

	/**
	 * Generates stylesheet for widget area.
	 *
	 * @param object $content Current Post Object.
	 * @since 2.0.0
	 */
	public function prepare_widget_area_assets( $content ) {

		if ( empty( $content ) ) {
			return;
		}

		foreach ( $content as $value ) {
			if ( is_array( $value ) && isset( $value['content'] ) && has_blocks( $value['content'] ) ) {
				$this->common_function_for_assets_preparation( $value['content'] );
			}
		}

	}

	/**
	 * This function determines wether to generate new assets or not.
	 *
	 * @since 1.23.0
	 */
	public function allow_assets_generation() {

		$page_assets     = get_post_meta( $this->post_id, '_uag_page_assets', true );
		$version_updated = false;
		$css_asset_info  = array();
		$js_asset_info   = array();

		if ( empty( $page_assets ) || empty( $page_assets['uag_version'] ) ) {
			return true;
		}

		if ( UAGB_ASSET_VER !== $page_assets['uag_version'] ) {
			$version_updated = true;
		}

		if ( 'enabled' === $this->file_generation ) {

			$css_file_name = get_post_meta( $this->post_id, '_uag_css_file_name', true );
			$js_file_name  = get_post_meta( $this->post_id, '_uag_js_file_name', true );

			if ( ! empty( $css_file_name ) ) {
				$css_asset_info = UAGB_Scripts_Utils::get_asset_info( 'css', $this->post_id );
				$css_file_path  = $css_asset_info['css'];
			}

			if ( ! empty( $js_file_name ) ) {
				$js_asset_info = UAGB_Scripts_Utils::get_asset_info( 'js', $this->post_id );
				$js_file_path  = $js_asset_info['js'];
			}

			if ( $version_updated ) {
				$uagb_filesystem = uagb_filesystem();

				if ( ! empty( $css_file_path ) ) {
					$uagb_filesystem->delete( $css_file_path );
				}

				if ( ! empty( $js_file_path ) ) {
					$uagb_filesystem->delete( $js_file_path );
				}

				// Delete keys.
				delete_post_meta( $this->post_id, '_uag_css_file_name' );
				delete_post_meta( $this->post_id, '_uag_js_file_name' );
			}

			if ( empty( $css_file_path ) || ! file_exists( $css_file_path ) ) {
				return true;
			}

			if ( ! empty( $js_file_path ) && ! file_exists( $js_file_path ) ) {
				return true;
			}
		}

		// If version is updated, return true.
		if ( $version_updated ) {
			// Delete cached meta.
			$unique_ids = get_option( '_uagb_fse_uniqids' );
			if ( ! empty( $unique_ids ) && is_array( $unique_ids ) ) {
				foreach ( $unique_ids as $id ) {
					delete_post_meta( (int) $id, '_uag_page_assets' );
				}
			}
			delete_post_meta( $this->post_id, '_uag_page_assets' );
			return true;
		}

		// Set required varibled from stored data.
		$this->current_block_list  = $page_assets['current_block_list'];
		$this->uag_flag            = $page_assets['uag_flag'];
		$this->stylesheet          = apply_filters( 'uag_page_assets_css', $page_assets['css'] );
		$this->script              = apply_filters( 'uag_page_assets_js', $page_assets['js'] );
		$this->gfonts              = $page_assets['gfonts'];
		$this->gfonts_files        = $page_assets['gfonts_files'];
		$this->gfonts_url          = $page_assets['gfonts_url'];
		$this->uag_faq_layout      = $page_assets['uag_faq_layout'];
		$this->assets_file_handler = array_merge( $css_asset_info, $js_asset_info );

		return false;
	}

	/**
	 * Enqueue all page assets.
	 *
	 * @since 1.23.0
	 */
	public function enqueue_scripts() {
		$blocks = array();
		if ( UAGB_Admin_Helper::is_block_theme() ) {
			global $_wp_current_template_content;
			if ( isset( $_wp_current_template_content ) ) {
				$blocks = parse_blocks( $_wp_current_template_content );
			}
		}
		// Global Required assets.
		// If the current template has content and contains blocks, execute this code block.
		if ( has_blocks( $this->post_id ) || has_blocks( $blocks ) ) {
			/* Print conditional css for all blocks */
			add_action( 'wp_head', array( $this, 'print_conditional_css' ), 80 );
		}

		// For Spectra Global Block Styles.
		$this->spectra_gbs_load_styles();

		// UAG Flag specific.
		if ( $this->is_allowed_assets_generation ) {

			// Prepare font css and files.
			$this->generate_fonts();

			$this->generate_assets();
			$this->generate_asset_files();
		}
		if ( $this->uag_flag ) {

			// Register Assets for Frontend & Enqueue for Editor.
			UAGB_Scripts_Utils::enqueue_blocks_dependency_both();

			// Enqueue all dependency assets.
			$this->enqueue_blocks_dependency_frontend();

			// RTL Styles Support.
			UAGB_Scripts_Utils::enqueue_blocks_rtl_styles();

			if ( $this->load_uag_fonts ) {
				// Render google fonts.
				$this->render_google_fonts();
			}

			if ( 'enabled' === $this->file_generation ) {
				// Enqueue File Generation Assets Files.
				$this->enqueue_file_generation_assets();
			}

			// Print Dynamic CSS.
			if ( 'disabled' === $this->file_generation || $this->fallback_css ) {
				UAGB_Scripts_Utils::enqueue_blocks_styles(); // Enqueue block styles.
				add_action( 'wp_head', array( $this, 'print_stylesheet' ), 80 );
			}
			// Print Dynamic JS.
			if ( 'disabled' === $this->file_generation || $this->fallback_js ) {
				add_action( 'wp_footer', array( $this, 'print_script' ), 1000 );
			}
		} else {
			// this custom css load,if only WP core block is present on the page.
			if ( $this->stylesheet ) {
				add_action( 'wp_head', array( $this, 'print_stylesheet' ), 80 );
			}
		}
	}
	/**
	 * Get saved fonts.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_fonts() {

		return $this->gfonts;
	}

	/**
	 * This function updates the Page assets in the Page Meta Key.
	 *
	 * @since 1.23.0
	 */
	public function update_page_assets() {

		if ( $this->preview || $this->is_post_revision ) {
			return;
		}

		$meta_array = array(
			'css'                => wp_slash( $this->stylesheet ),
			'js'                 => $this->script,
			'current_block_list' => $this->current_block_list,
			'uag_flag'           => $this->uag_flag,
			'uag_version'        => UAGB_ASSET_VER,
			'gfonts'             => $this->gfonts,
			'gfonts_url'         => $this->gfonts_url,
			'gfonts_files'       => $this->gfonts_files,
			'uag_faq_layout'     => $this->uag_faq_layout,
		);

		update_post_meta( $this->post_id, '_uag_page_assets', $meta_array );
	}
	/**
	 * This is the action where we create dynamic asset files.
	 * CSS Path : uploads/uag-plugin/uag-style-{post_id}.css
	 * JS Path : uploads/uag-plugin/uag-script-{post_id}.js
	 *
	 * @since 1.15.0
	 */
	public function generate_asset_files() {

		if ( 'enabled' === $this->file_generation ) {
			$this->file_write( $this->stylesheet, 'css', $this->post_id );
			$this->file_write( $this->script, 'js', $this->post_id );
		}

		$this->update_page_assets();
	}

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 *
	 * @since 1.13.4
	 */
	public function enqueue_blocks_dependency_frontend() {

		$block_list_for_assets = $this->current_block_list;

		$blocks = UAGB_Block_Module::get_blocks_info();

		$block_assets = UAGB_Block_Module::get_block_dependencies();

		foreach ( $block_list_for_assets as $key => $curr_block_name ) {

			$static_dependencies = ( isset( $blocks[ $curr_block_name ]['static_dependencies'] ) ) ? $blocks[ $curr_block_name ]['static_dependencies'] : array();

			foreach ( $static_dependencies as $asset_handle => $asset_info ) {

				if ( 'js' === $asset_info['type'] ) {
					// Scripts.
					if ( 'uagb-faq-js' === $asset_handle ) {
							wp_enqueue_script( 'uagb-faq-js' );
					} else {

						wp_enqueue_script( $asset_handle );
					}
				}

				if ( 'css' === $asset_info['type'] ) {
					// Styles.
					wp_enqueue_style( $asset_handle );
				}
			}
		}

		$uagb_masonry_ajax_nonce = wp_create_nonce( 'uagb_masonry_ajax_nonce' );
		$uagb_grid_ajax_nonce    = wp_create_nonce( 'uagb_grid_ajax_nonce' );
		wp_localize_script(
			'uagb-post-js',
			'uagb_data',
			array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'uagb_masonry_ajax_nonce' => $uagb_masonry_ajax_nonce,
				'uagb_grid_ajax_nonce'    => $uagb_grid_ajax_nonce,
			)
		);

		$uagb_forms_ajax_nonce = wp_create_nonce( 'uagb_forms_ajax_nonce' );
		wp_localize_script(
			'uagb-forms-js',
			'uagb_forms_data',
			array(
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'uagb_forms_ajax_nonce' => $uagb_forms_ajax_nonce,
				'recaptcha_site_key_v2' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v2', '' ),
				'recaptcha_site_key_v3' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v3', '' ),
			)
		);

		wp_localize_script(
			'uagb-container-js',
			'uagb_container_data',
			array(
				'tablet_breakpoint' => UAGB_TABLET_BREAKPOINT,
				'mobile_breakpoint' => UAGB_MOBILE_BREAKPOINT,
			)
		);

		wp_localize_script(
			'uagb-timeline-js',
			'uagb_timeline_data',
			array(
				'tablet_breakpoint' => UAGB_TABLET_BREAKPOINT,
				'mobile_breakpoint' => UAGB_MOBILE_BREAKPOINT,
			)
		);

		do_action( 'spectra_localize_pro_block_ajax' );

	}

	/**
	 * Enqueue File Generation Files.
	 */
	public function enqueue_file_generation_assets() {

		$file_handler = $this->assets_file_handler;

		/*
		* Added filter to allows developers and users to adjust constant values for theme compatibility, easy updates, and compatibility with other plugins.
		*/
		$uagb_asset_ver = apply_filters( 'uagb_asset_version', UAGB_ASSET_VER );

		if ( empty( $uagb_asset_ver ) || ! is_string( $uagb_asset_ver ) ) {
			$uagb_asset_ver = UAGB_ASSET_VER;
		}

		if ( isset( $file_handler['css_url'] ) ) {
			wp_enqueue_style( 'uag-style-' . $this->post_id, $file_handler['css_url'], array(), $uagb_asset_ver, 'all' );
		} else {
			$this->fallback_css = true;
		}
		if ( isset( $file_handler['js_url'] ) ) {
			wp_enqueue_script( 'uag-script-' . $this->post_id, $file_handler['js_url'], array(), $uagb_asset_ver, true );
		} else {
			$this->fallback_js = true;
		}
	}
	/**
	 * Print the Script in footer.
	 */
	public function print_script() {

		if ( empty( $this->script ) ) {
			return;
		}

		echo '<script type="text/javascript" id="uagb-script-frontend-' . esc_attr( $this->post_id ) . '">' . $this->script . '</script>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Print the Stylesheet in header.
	 */
	public function print_stylesheet() {

		if ( empty( $this->stylesheet ) ) {
			return;
		}
		echo '<style id="uagb-style-frontend-' . esc_attr( $this->post_id ) . '">' . $this->stylesheet . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Print Conditional blocks css.
	 */
	public function print_conditional_css() {

		if ( self::$conditional_blocks_printed ) {
			return;
		}

		$conditional_block_css = UAGB_Block_Helper::get_condition_block_css();

		if ( in_array( 'uagb/masonry-gallery', $this->current_block_list, true ) ) {
			$conditional_block_css .= UAGB_Block_Helper::get_masonry_gallery_css();
		}
		echo '<style id="uagb-style-conditional-extension">' . $conditional_block_css . '</style>'; //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		self::$conditional_blocks_printed = true;

	}

	/**
	 * Generate google fonts link and font files
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function generate_fonts() {

		if ( ! $this->load_uag_fonts || empty( $this->gfonts ) ) {
			return;
		}

		$fonts_link = '';
		$fonts_attr = '';
		$extra_attr = '';
		$fonts_slug = array();

		// Sort key for same md5 id while loading native fonts.
		ksort( $this->gfonts );

		foreach ( $this->gfonts as $key => $gfont_values ) {
			if ( ! empty( $fonts_attr ) ) {
				$fonts_attr .= '|'; // Append a new font to the string.
			}
			if ( empty( $gfont_values['fontfamily'] ) && is_string( $gfont_values['fontfamily'] ) ) {
				continue;
			}
			$fonts_attr  .= str_replace( ' ', '+', $gfont_values['fontfamily'] );
			$fonts_slug[] = sanitize_key( str_replace( ' ', '-', strtolower( $gfont_values['fontfamily'] ) ) );
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$fonts_attr .= ':';
				$fonts_attr .= implode( ',', $gfont_values['fontvariants'] );
				foreach ( $gfont_values['fontvariants'] as $key => $font_variants ) {
					$fonts_attr .= ',' . $font_variants . 'italic';
				}
			}
		}

		$subsets = apply_filters( 'uag_font_subset', array() );

		if ( ! empty( $subsets ) ) {
			$extra_attr .= '&subset=' . implode( ',', $subsets );
		} else {
			$extra_attr .= '&subset=latin';
		}

		$display = apply_filters( 'uag_font_disaply', 'fallback' );

		if ( ! empty( $display ) ) {
			$extra_attr .= '&display=' . $display;
		}

		if ( isset( $fonts_attr ) && ! empty( $fonts_attr ) ) {

			// link without https protocol.
			$fonts_link = '//fonts.googleapis.com/css?family=' . esc_attr( $fonts_attr ) . $extra_attr;

			if ( 'enabled' === $this->load_gfonts_locally ) {

				// Include the font loader file.
				require_once UAGB_DIR . 'lib/uagb-webfont/uagb-webfont-loader.php';

				// link with https protocol to download fonts.
				$fonts_link = 'https:' . $fonts_link;

				$fonts_data = uagb_get_webfont_remote_styles( $fonts_link );

				$this->stylesheet = $fonts_data . $this->stylesheet;

				if ( 'enabled' === $this->preload_local_fonts ) {

					$font_files = uagb_get_preload_local_fonts( $fonts_link );

					if ( is_array( $font_files ) && ! empty( $font_files ) ) {
						foreach ( $font_files as $file_data ) {

							if ( isset( $file_data['font_family'] ) && in_array( $file_data['font_family'], $fonts_slug, true ) ) {

								$this->gfonts_files[ $file_data['font_family'] ] = $file_data['font_url'];
							}
						}
					}
				}
			}

			// Set fonts url.
			$this->gfonts_url = $fonts_link;
		}

		/* Update page assets */
		$this->update_page_assets();
	}

	/**
	 * Load the Google Fonts.
	 */
	public function render_google_fonts() {

		if ( empty( $this->gfonts ) || empty( $this->gfonts_url ) ) {
			return;
		}

		$show_google_fonts = apply_filters( 'uagb_blocks_show_google_fonts', true );

		if ( ! $show_google_fonts ) {
			return;
		}

		// Load remote google fonts if local font is disabled.
		if ( 'disabled' === $this->load_gfonts_locally ) {

			// Enqueue google fonts.
			wp_enqueue_style( 'uag-google-fonts-' . $this->post_id, $this->gfonts_url, array(), UAGB_VER, 'all' );

		} else {

			// Preload woff files local font preload is enabled.
			if ( 'enabled' === $this->preload_local_fonts ) {

				if ( is_array( $this->gfonts_files ) && ! empty( $this->gfonts_files ) ) {

					foreach ( $this->gfonts_files as $gfont_file_url ) {
						echo '<link rel="preload" href="' . esc_url( $gfont_file_url ) . '" as="font" type="font/woff2">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
			}
		}
	}

	/**
	 * Load the front end Google Fonts.
	 */
	public function print_google_fonts() {

		if ( empty( $this->gfonts_url ) ) {
			return;
		}

		$show_google_fonts = apply_filters( 'uagb_blocks_show_google_fonts', true );
		if ( ! $show_google_fonts ) {
			return;
		}

		if ( ! empty( $this->gfonts_url ) ) {
			echo '<link href="' . esc_url( $this->gfonts_url ) . '" rel="stylesheet">'; //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		}
	}

	/**
	 * Generates CSS recurrsively.
	 *
	 * @param object $block The block object.
	 * @since 0.0.1
	 */
	public function get_block_css_and_js( $block ) {

		$block = (array) $block;

		$name     = $block['blockName'];
		$css      = array();
		$js       = '';
		$block_id = '';

		if ( ! isset( $name ) ) {
			return array(
				'css' => array(),
				'js'  => '',
			);
		}

		if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			/**
			 * Filters the block attributes for CSS and JS generation.
			 *
			 * @param array  $block_attributes The block attributes to be filtered.
			 * @param string $name             The block name.
			 */
			$blockattr = apply_filters( 'uagb_block_attributes_for_css_and_js', $block['attrs'], $name );
			if ( isset( $blockattr['block_id'] ) ) {
				$block_id = $blockattr['block_id'];
			}
		}

		$this->current_block_list[] = $name;

		if ( 'core/gallery' === $name && isset( $block['attrs']['masonry'] ) && true === $block['attrs']['masonry'] ) {
			$this->current_block_list[] = 'uagb/masonry-gallery';
			$this->uag_flag             = true;
			$css                       += UAGB_Block_Helper::get_gallery_css( $blockattr, $block_id );
		}

		// If UAGAnimationType is set and is not equal to none, explicitly load the extension (and it's assets) on frontend.
		// Also check if animations extension is enabled.
		if (
			'enabled' === \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_animations_extension', 'enabled' ) &&
			! empty( $block['attrs']['UAGAnimationType'] )
		) {
			$this->current_block_list[] = 'uagb/animations-extension';
		}

		if ( strpos( $name, 'uagb/' ) !== false ) {
			$this->uag_flag = true;
		}

		// Add static css here.
		$blocks = UAGB_Block_Module::get_blocks_info();

		$block_css_file_name = isset( $blocks[ $name ]['static_css'] ) ? $blocks[ $name ]['static_css'] : str_replace( 'uagb/', '', $name );

		if ( 'enabled' === $this->file_generation && ! in_array( $block_css_file_name, $this->static_css_blocks, true ) ) {
			$common_css = array(
				'common' => $this->get_block_static_css( $block_css_file_name ),
			);
			$css       += $common_css;
		}

		if ( strpos( $name, 'uagb/' ) !== false ) {
			$_block_slug = str_replace( 'uagb/', '', $name );

			$blockattr = isset( $blockattr ) && is_array( $blockattr ) ? $blockattr : array();

			$_block_css = UAGB_Block_Module::get_frontend_css( $_block_slug, $blockattr, $block_id );
			$_block_js  = UAGB_Block_Module::get_frontend_js( $_block_slug, $blockattr, $block_id, 'js' );
			$css        = $this->merge_array_string_values( $css, $_block_css );
			if ( ! empty( $_block_js ) ) {
				$js .= $_block_js;
			}

			if ( 'uagb/faq' === $name && ! isset( $blockattr['layout'] ) ) {
				$this->uag_faq_layout = true;
			}
		}

		if ( isset( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $j => $inner_block ) {
				if ( 'core/block' === $inner_block['blockName'] ) {
					$id = ( isset( $inner_block['attrs']['ref'] ) ) ? $inner_block['attrs']['ref'] : 0;
					if ( $id ) {
						$assets = $this->get_assets_using_post_content( $id );
						if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
							$reuse_block_css             = array(
								'desktop' => '',
								'tablet'  => '',
								'mobile'  => '',
							);
							$reuse_block_css['desktop'] .= $assets['css'];
							$css                         = $this->merge_array_string_values( $css, $reuse_block_css );
							$js                         .= $assets['js'];
						} else {
							$this->stylesheet .= $assets['css'];
							$this->script     .= $assets['js'];
						}
					}
				} else {
					// Get CSS for the Block.
					$inner_assets    = $this->get_block_css_and_js( $inner_block );
					$inner_block_css = $inner_assets['css'];

					$css_desktop = ( isset( $css['desktop'] ) ? $css['desktop'] : '' );
					$css_tablet  = ( isset( $css['tablet'] ) ? $css['tablet'] : '' );
					$css_mobile  = ( isset( $css['mobile'] ) ? $css['mobile'] : '' );

					if ( 'enabled' === $this->file_generation ) { // Get common CSS for the block when file generation is enabled.
						$css_common = ( isset( $css['common'] ) ? $css['common'] : '' );
						if ( isset( $inner_block_css['common'] ) ) {
							$css['common'] = $css_common . $inner_block_css['common'];
						}
					}

					if ( isset( $inner_block_css['desktop'] ) ) {
						$css['desktop'] = $css_desktop . $inner_block_css['desktop'];
						$css['tablet']  = $css_tablet . $inner_block_css['tablet'];
						$css['mobile']  = $css_mobile . $inner_block_css['mobile'];
					}

					$js .= $inner_assets['js'];
				}
			}
		}

		$this->current_block_list = array_unique( $this->current_block_list );

		return array(
			'css' => $css,
			'js'  => $js,
		);

	}

	/**
	 * Generates stylesheet and appends in head tag.
	 *
	 * @since 0.0.1
	 */
	public function generate_assets() {

		/* Finalize prepared assets and store in static variable */
		global $content_width;

		$this->stylesheet = str_replace( '#CONTENT_WIDTH#', $content_width . 'px', $this->stylesheet );

		if ( '' !== $this->script ) {
			$this->script = 'document.addEventListener("DOMContentLoaded", function(){ ' . $this->script . ' });';
		}

		/* Update page assets */
		$this->update_page_assets();
	}

	/**
	 * Generates stylesheet in loop.
	 *
	 * @param object $this_post Current Post Object.
	 * @since 1.7.0
	 */
	public function prepare_assets( $this_post ) {

		if ( empty( $this_post ) || empty( $this_post->ID ) ) {
			return;
		}

		if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {
			$this->common_function_for_assets_preparation( $this_post->post_content );
		}
	}

	/**
	 * Common function to generate stylesheet.
	 *
	 * @param array $post_content Current Post Object.
	 * @since 2.0.0
	 */
	public function common_function_for_assets_preparation( $post_content ) {

		$blocks = $this->parse_blocks( $post_content );

		$this->page_blocks = $blocks;

		$enable_on_page_css_button = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' );

		if ( 'yes' === $enable_on_page_css_button ) {
			$custom_css = get_post_meta( $this->post_id, '_uag_custom_page_level_css', true );
			$custom_css = ! empty( $custom_css ) && is_string( $custom_css ) ? wp_kses_post( $custom_css ) : '';

			if ( ! empty( $custom_css ) && ! self::$custom_css_appended ) {
				$this->stylesheet         .= $custom_css;
				self::$custom_css_appended = true;
			}
		}

		if ( ! is_array( $blocks ) || empty( $blocks ) ) {
			return;
		}

		$assets = $this->get_blocks_assets( $blocks );

		if ( 'enabled' === $this->file_generation && isset( $assets['css'] ) && ! self::$common_assets_added ) {

			$common_static_css_all_blocks = $this->get_block_static_css( 'extensions' );
			$assets['css']                = $assets['css'] . $common_static_css_all_blocks;
			self::$common_assets_added    = true;
		}

		$this->stylesheet .= $assets['css'];
		$this->script     .= $assets['js'];

		// Update fonts.
		$this->gfonts = array_merge( $this->gfonts, UAGB_Helper::$gfonts );
	}

	/**
	 * Parse Guten Block.
	 *
	 * @param string $content the content string.
	 * @since 1.1.0
	 */
	public function parse_blocks( $content ) {

		global $wp_version;

		return ( version_compare( $wp_version, '5', '>=' ) ) ? parse_blocks( $content ) : gutenberg_parse_blocks( $content );
	}

	/**
	 * Generates ids for all wp template part.
	 *
	 * @param array $block the content array.
	 * @since 2.4.1
	 */
	public function get_fse_template_part( $block ) {
		if ( empty( $block['attrs']['slug'] ) ) {
			return;
		}

		$slug            = $block['attrs']['slug'];
		$templates_parts = get_block_templates( array( 'slugs__in' => $slug ), 'wp_template_part' );
		foreach ( $templates_parts as $templates_part ) {
			if ( $slug === $templates_part->slug ) {
				$id = $templates_part->wp_id;
				return $id;
			}
		}
	}

	/**
	 * Generates parse content for all blocks including reusable blocks.
	 *
	 * @param int $id of blocks.
	 * @since 2.4.1
	 */
	public function get_assets_using_post_content( $id ) {

		$content = get_post_field( 'post_content', $id );

		$reusable_blocks = $this->parse_blocks( $content );

		$assets = $this->get_blocks_assets( $reusable_blocks );

		return $assets;
	}

	/**
	 * Generates assets for all blocks including reusable blocks.
	 *
	 * @param array $blocks Blocks array.
	 * @since 1.1.0
	 */
	public function get_blocks_assets( $blocks ) {
		$static_and_dynamic_assets = $this->get_static_and_dynamic_assets( $blocks );
		return array(
			'css' => $static_and_dynamic_assets['static'] . $static_and_dynamic_assets['dynamic'],
			'js'  => $static_and_dynamic_assets['js'],
		);
	}

	/**
	 * Get static & dynamic css for block.
	 *
	 * @param array $blocks Blocks array.
	 * @since 2.12.3
	 * @return array Of static and dynamic css and js.
	 */
	public function get_static_and_dynamic_assets( $blocks ) {
		$desktop    = '';
		$tablet     = '';
		$mobile     = '';
		$static_css = '';

		$tab_styling_css = '';
		$mob_styling_css = '';
		$block_css       = '';
		$js              = '';

		foreach ( $blocks as $i => $block ) {

			if ( is_array( $block ) ) {

				if ( empty( $block['blockName'] ) || ! isset( $block['attrs'] ) ) {
					continue;
				}

				if ( 'core/block' === $block['blockName'] ) {
					$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

					if ( $id ) {
						$assets            = $this->get_assets_using_post_content( $id );
						$this->stylesheet .= $assets['css'];
						$this->script     .= $assets['js'];
					}
				} elseif ( 'core/template-part' === $block['blockName'] ) {
					$id = $this->get_fse_template_part( $block );

					if ( $id ) {
						$assets     = $this->get_assets_using_post_content( $id );
						$block_css .= $assets['css'];
						$js        .= $assets['js'];
					}
				} elseif ( 'core/pattern' === $block['blockName'] ) {
					$get_assets = $this->get_core_pattern_assets( $block );

					if ( ! empty( $get_assets['css'] ) ) {
						$block_css .= $get_assets['css'];
						$js        .= $get_assets['js'];
					}
				} else {
					// Add your block specif css here.
					$block_assets = $this->get_block_css_and_js( $block );
					// Get CSS for the Block.
					$css = $block_assets['css'];

					if ( ! empty( $css['common'] ) ) {
						$static_css .= $css['common'];
					}

					if ( isset( $css['desktop'] ) ) {
						$desktop .= $css['desktop'];
						$tablet  .= $css['tablet'];
						$mobile  .= $css['mobile'];
					}
					$js .= $block_assets['js'];
				}
			}
		}

		if ( ! empty( $tablet ) ) {
			$tab_styling_css .= '@media only screen and (max-width: ' . UAGB_TABLET_BREAKPOINT . 'px) {';
			$tab_styling_css .= $tablet;
			$tab_styling_css .= '}';
		}

		if ( ! empty( $mobile ) ) {
			$mob_styling_css .= '@media only screen and (max-width: ' . UAGB_MOBILE_BREAKPOINT . 'px) {';
			$mob_styling_css .= $mobile;
			$mob_styling_css .= '}';
		}
		return array(
			'static'  => $static_css,
			'dynamic' => $block_css . $desktop . $tab_styling_css . $mob_styling_css,
			'js'      => $js,
		);
	}

	/**
	 * Creates a new file for Dynamic CSS/JS.
	 *
	 * @param  string $file_data The data that needs to be copied into the created file.
	 * @param  string $type Type of file - CSS/JS.
	 * @param  string $file_state Wether File is new or old.
	 * @param  string $old_file_name Old file name timestamp.
	 * @since 1.15.0
	 * @return boolean true/false
	 */
	public function create_file( $file_data, $type, $file_state = 'new', $old_file_name = '' ) {

		$uploads_dir = UAGB_Helper::get_upload_dir();
		$file_system = uagb_filesystem();

		// Example 'uag-css-15.css'.
		$file_name = 'uag-' . $type . '-' . $this->post_id . '.' . $type;

		if ( 'old' === $file_state ) {
			$file_name = $old_file_name;
		}

		$folder_name    = UAGB_Scripts_Utils::get_asset_folder_name( $this->post_id );
		$base_file_path = $uploads_dir['path'] . 'assets/' . $folder_name . '/';
		$file_path      = $uploads_dir['path'] . 'assets/' . $folder_name . '/' . $file_name;

		$result = false;

		// TODO: This old_assets removal code need to be removed after 3 major releases. from v2.11.0.
		// Remove if any old file exists for same post.
		$old_assets = glob( $base_file_path . 'uag-' . $type . '-' . $this->post_id . '-*' );
		if ( ! empty( $old_assets ) && is_array( $old_assets ) ) {
			foreach ( $old_assets as $old_asset ) {
				if ( file_exists( $old_asset ) ) {
					$file_system->delete( $old_asset );
				}
			}
		}

		if ( wp_mkdir_p( $base_file_path ) ) {

			// Create a new file.
			$result = $file_system->put_contents( $file_path, $file_data, FS_CHMOD_FILE );

			if ( $result && ! $this->is_post_revision ) {
				// Update meta with current timestamp.
				update_post_meta( $this->post_id, '_uag_' . $type . '_file_name', $file_name );
			}
		}

		return $result;
	}

	/**
	 * Creates css and js files.
	 *
	 * @param  var    $file_data    Gets the CSS\JS for the current Page.
	 * @param  string $type    Gets the CSS\JS type.
	 * @param  int    $post_id Post ID.
	 * @since  1.14.0
	 */
	public function file_write( $file_data, $type = 'css', $post_id = 0 ) {

		if ( ! $this->post_id ) {
			return false;
		}

		$file_system = uagb_filesystem();

		// Get timestamp - Already saved OR new one.
		$file_name   = get_post_meta( $this->post_id, '_uag_' . $type . '_file_name', true );
		$file_name   = empty( $file_name ) ? '' : $file_name;
		$assets_info = UAGB_Scripts_Utils::get_asset_info( $type, $this->post_id );
		$file_path   = $assets_info[ $type ];

		if ( '' === $file_data ) {
			/**
			 * This is when the generated CSS/JS is blank.
			 * This means this page does not use UAG block.
			 * In this scenario we need to delete the existing file.
			 * This will ensure there are no extra files added for user.
			*/

			if ( ! empty( $file_name ) && file_exists( $file_path ) ) {
				// Delete old file.
				wp_delete_file( $file_path );
			}

			return true;
		}

		/**
		 * Timestamp present but file does not exists.
		 * This is the case where somehow the files are delete or not created in first place.
		 * Here we attempt to create them again.
		 */
		if ( ! $file_system->exists( $file_path ) && '' !== $file_name ) {

			$did_create = $this->create_file( $file_data, $type, 'old', $file_name );

			if ( $did_create ) {
				$this->assets_file_handler = array_merge( $this->assets_file_handler, $assets_info );
			}

			return $did_create;
		}

		/**
		 * Need to create new assets.
		 * No such assets present for this current page.
		 */
		if ( '' === $file_name ) {

			// Create a new file.
			$did_create = $this->create_file( $file_data, $type );

			if ( $did_create ) {
				$new_assets_info           = UAGB_Scripts_Utils::get_asset_info( $type, $this->post_id );
				$this->assets_file_handler = array_merge( $this->assets_file_handler, $new_assets_info );
			}

			return $did_create;

		}

		/**
		 * File already exists.
		 * Need to match the content.
		 * If new content is present we update the current assets.
		 */
		if ( file_exists( $file_path ) ) {

			$old_data = $file_system->get_contents( $file_path );

			if ( $old_data !== $file_data ) {

				// Delete old file.
				wp_delete_file( $file_path );

				// Create a new file.
				$did_create = $this->create_file( $file_data, $type );

				if ( $did_create ) {
					$new_assets_info           = UAGB_Scripts_Utils::get_asset_info( $type, $this->post_id );
					$this->assets_file_handler = array_merge( $this->assets_file_handler, $new_assets_info );
				}

				return $did_create;
			}
		}

		$this->assets_file_handler = array_merge( $this->assets_file_handler, $assets_info );

		return true;
	}

	/**
	 * Get Static CSS of Block.
	 *
	 * @param string $block_name Block Name.
	 *
	 * @return string Static CSS.
	 * @since 1.23.0
	 */
	public function get_block_static_css( $block_name ) {

		$css = '';

		$block_static_css_path = UAGB_DIR . 'assets/css/blocks/' . $block_name . '.css';

		if ( file_exists( $block_static_css_path ) ) {

			$file_system = uagb_filesystem();

			$css = $file_system->get_contents( $block_static_css_path );
		}

		array_push( $this->static_css_blocks, $block_name );

		return apply_filters( 'spectra_frontend_static_style', $css, $block_name );
	}

	/**
	 * Merge two arrays with string values.
	 *
	 * @param array $array1 First array.
	 * @param array $array2 Second array.
	 * @since 2.7.3
	 * @return array
	 */
	public function merge_array_string_values( $array1, $array2 ) {
		foreach ( $array1 as $key => $value ) {
			if ( isset( $array2[ $key ] ) ) {
				$array1[ $key ] = $value . $array2[ $key ];
			}
			unset( $array2[ $key ] );
		}

		return array_merge( $array1, $array2 );
	}

	/**
	 * Handle the block assets when blocks type will be core/pattern.
	 *
	 * @param array $block The block array.
	 * @since 2.9.1
	 * @return array
	 */
	public function get_core_pattern_assets( $block ) {
		if ( empty( $block['attrs']['slug'] ) ) {
			return array();
		}

		$slug = $block['attrs']['slug'];

		// Check class and function exists.
		if ( ! class_exists( 'WP_Block_Patterns_Registry' ) || ! method_exists( 'WP_Block_Patterns_Registry', 'get_instance' ) ) {
			return array();
		}

		$registry = WP_Block_Patterns_Registry::get_instance();

		// Check is_registered method exists.
		if ( ! method_exists( $registry, 'is_registered' ) || ! method_exists( $registry, 'get_registered' ) || ! $registry->is_registered( $slug ) ) {
			return array();
		}

		$pattern = $registry->get_registered( $slug );

		return $this->get_blocks_assets( parse_blocks( $pattern['content'] ) );
	}

	/**
	 * Get static and dynamic assets data for a post. Its a helper function used by starter templates and GT library.
	 *
	 * @param int $post_id The post id.
	 * @since 2.12.3
	 * @return array of Static and dynamic css and js.
	 */
	public function get_static_and_dynamic_css( $post_id ) {

		$this_post = get_post( $post_id );

		if ( empty( $this_post ) || empty( $this_post->ID ) ) {
			return array();
		}

		if ( has_blocks( $this_post->ID ) && ! empty( $this_post->post_content ) ) {

			$blocks = $this->parse_blocks( $this_post->post_content );

			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return array();
			}

			return $this->get_static_and_dynamic_assets( $blocks );
		}

		return array();

	}
}
