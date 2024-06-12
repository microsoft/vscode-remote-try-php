<?php
/**
 * UAGB Helper.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Helper' ) ) {

	/**
	 * Class UAGB_Helper.
	 */
	final class UAGB_Helper {


		/**
		 * Member Variable
		 *
		 * @since 0.0.1
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 0.0.1
		 * @var instance
		 */
		public static $block_list;

		/**
		 * UAG File Generation Flag
		 *
		 * @since 1.14.0
		 * @var string
		 */
		public static $file_generation = 'disabled';

		/**
		 * Store Json variable
		 *
		 * @since 1.8.1
		 * @var array
		 */
		public static $icon_json;

		/**
		 * Google fonts to enqueue
		 *
		 * @var array
		 */
		public static $gfonts = array();

		/**
		 * Current Block List
		 *
		 * @since 1.13.4
		 * @var current_block_list
		 * @deprecated 1.23.0
		 */
		public static $current_block_list = array();

		/**
		 * UAG Block Flag
		 *
		 * @since 1.13.4
		 * @var uag_flag
		 * @deprecated 1.23.0
		 */
		public static $uag_flag = false;

		/**
		 * Page Blocks Variable
		 *
		 * @since 1.6.0
		 * @var page_blocks
		 * @deprecated 1.23.0
		 */
		public static $page_blocks;

		/**
		 * Stylesheet
		 *
		 * @since 1.13.4
		 * @var stylesheet
		 * @deprecated 1.23.0
		 */
		public static $stylesheet = '';

		/**
		 * Script
		 *
		 * @since 1.13.4
		 * @var script
		 * @deprecated 1.23.0
		 */
		public static $script = '';

		/**
		 * UAG FAQ Layout Flag
		 *
		 * @since 1.18.1
		 * @deprecated 1.23.0
		 * @var uag_faq_layout
		 */
		public static $uag_faq_layout = false;

		/**
		 * UAG TOC Flag
		 *
		 * @since 1.18.1
		 * @deprecated 1.23.0
		 * @var table_of_contents_flag
		 */
		public static $table_of_contents_flag = false;

		/**
		 * As our svg icon is too long array so we will divide that into number of icon chunks.
		 *
		 * @var int
		 * @since 2.7.0
		 */
		public static $number_of_icon_chunks = 4;

		/**
		 * We have icon list in chunks in this variable we will merge all insides array into one single array.
		 *
		 * @var array
		 * @since 2.7.0
		 */
		public static $icon_array_merged = array();

		/**
		 *  Initiator
		 *
		 * @since 0.0.1
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			require UAGB_DIR . 'classes/class-uagb-block-helper.php';
			require UAGB_DIR . 'classes/class-uagb-block-js.php';

			self::$block_list      = UAGB_Block_Module::get_blocks_info();
			self::$file_generation = self::allow_file_generation();
			// Condition is only needed when we are using block based theme and Reading setting is updated.
			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() && isset( $_POST['option_page'] ) && 'reading' === $_POST['option_page'] && isset( $_POST['action'] ) && 'update' === $_POST['action'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				/* Update the asset version */
				UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() ); // Update the asset version when reading settings is updated.
			}
		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $selectors The block selectors.
		 * @param string $id The selector ID.
		 * @since 0.0.1
		 */
		public static function generate_css( $selectors, $id ) {
			$styling_css = '';

			if ( empty( $selectors ) ) {
				return '';
			}

			foreach ( $selectors as $key => $value ) {

				$css = '';

				foreach ( $value as $j => $val ) {

					if ( 'font-family' === $j && 'Default' === $val ) {
						continue;
					}

					if ( ! empty( $val ) || ( empty( $val ) && 'content' === $j ) || 0 === $val ) {
						if ( 'font-family' === $j ) {
							$css .= $j . ': "' . $val . '";';
						} else {
							if ( is_array( $val ) ) {
								// Convert $val array property to string.
								foreach ( $val as $index => $property ) {
									$properties = is_string( $property ) ? $property : (string) $property;
									$css       .= $j . ': ' . $properties . ';';
								}
							} else {
								$css .= $j . ': ' . $val . ';';
							}
						}
					}
				}

				if ( ! empty( $css ) ) {
					$styling_css     .= $id;
					$styling_css     .= $key . '{';
						$styling_css .= $css . '}';
				}
			}

			return $styling_css;
		}

		/**
		 * Get CSS value
		 *
		 * Syntax:
		 *
		 *  get_css_value( VALUE, UNIT );
		 *
		 * E.g.
		 *
		 *  get_css_value( VALUE, 'em' );
		 *
		 * @param mixed  $value  CSS value.
		 * @param string $unit  CSS unit.
		 * @since 1.13.4
		 */
		public static function get_css_value( $value = '', $unit = '' ) {
			if ( ! is_numeric( $value ) ) {
				return '';
			}

			$unit = sanitize_text_field( $unit );

			if ( empty( $unit ) ) {
				return $value;
			}

			return esc_attr( $value . $unit );
		}


		/**
		 * Adds Google fonts all blocks.
		 *
		 * @param bool       $load_google_font the blocks attr.
		 * @param array      $font_family the blocks attr.
		 * @param int|string $font_weight the blocks attr.
		 */
		public static function blocks_google_font( $load_google_font, $font_family, $font_weight ) {

			if ( true === $load_google_font ) {
				if ( ! array_key_exists( $font_family, self::$gfonts ) ) {
					$add_font                     = array(
						'fontfamily'   => $font_family,
						'fontvariants' => ( isset( $font_weight ) && ! empty( $font_weight ) ? array( $font_weight ) : array() ),
					);
					self::$gfonts[ $font_family ] = $add_font;
				} else {
					if ( isset( $font_weight ) && ! empty( $font_weight ) && ! in_array( $font_weight, self::$gfonts[ $font_family ]['fontvariants'], true ) ) {
						array_push( self::$gfonts[ $font_family ]['fontvariants'], $font_weight );
					}
				}
			}
		}

		/**
		 * Get Json Data.
		 * Customize and add icons via 'uagb_icons_chunks' filter.
		 *
		 * @since 1.8.1
		 * @return array
		 */
		public static function backend_load_font_awesome_icons() {

			if ( null !== self::$icon_json ) {
				return self::$icon_json;
			}

			$icons_chunks = array();
			for ( $i = 0; $i < self::$number_of_icon_chunks; $i++ ) {
				$json_file = UAGB_DIR . "blocks-config/uagb-controls/spectra-icons-v6-{$i}.php";
				if ( file_exists( $json_file ) ) {
					$icons_chunks[] = include $json_file;
				}
			}

			$icons_chunks = apply_filters( 'uagb_icons_chunks', $icons_chunks );
			
			if ( ! is_array( $icons_chunks ) || empty( $icons_chunks ) ) {
				$icons_chunks = array();
			}

			self::$icon_json = $icons_chunks;
			return self::$icon_json;
		}

		/**
		 * Generate SVG.
		 *
		 * @since 1.8.1
		 * @param  array $icon Decoded fontawesome json file data.
		 */
		public static function render_svg_html( $icon ) {
			$icon = sanitize_text_field( esc_attr( $icon ) );

			$json = self::backend_load_font_awesome_icons();

			if ( ! empty( $json ) ) {
				if ( empty( $icon_array_merged ) ) {
					foreach ( $json as $value ) {
						self::$icon_array_merged = array_merge( self::$icon_array_merged, $value );
					}
				}
				$json = self::$icon_array_merged;
			}

			// Load Polyfiller Array if needed.
			$load_font_awesome_5 = UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_font_awesome_5', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'enabled' : 'disabled' );

			if ( 'disabled' !== $load_font_awesome_5 ) {
				// If Icon doesn't need Polyfilling, use the Original.
				$font_awesome_5_polyfiller = get_spectra_font_awesome_polyfiller();
				$icon                      = ! empty( $font_awesome_5_polyfiller[ $icon ] ) ? $font_awesome_5_polyfiller[ $icon ] : $icon;
			}

			$icon_brand_or_solid = isset( $json[ $icon ]['svg']['brands'] ) ? $json[ $icon ]['svg']['brands'] : ( isset( $json[ $icon ]['svg']['solid'] ) ? $json[ $icon ]['svg']['solid'] : array() );
			$path                = isset( $icon_brand_or_solid['path'] ) ? $icon_brand_or_solid['path'] : '';
			$view                = isset( $icon_brand_or_solid['width'] ) && isset( $icon_brand_or_solid['height'] ) ? '0 0 ' . $icon_brand_or_solid['width'] . ' ' . $icon_brand_or_solid['height'] : null;

			if ( $path && $view ) {
				?>
				<svg xmlns="https://www.w3.org/2000/svg" viewBox= "<?php echo esc_attr( $view ); ?>"><path d="<?php echo esc_attr( $path ); ?>"></path></svg>
				<?php
			}
		}

		/**
		 *  Check MIME Type
		 *
		 *  @since 1.20.0
		 */
		public static function get_mime_type() {

			$allowed_types = get_allowed_mime_types();

			return ( array_key_exists( 'json', $allowed_types ) ) ? true : false;

		}

		/**
		 * Returns Query.
		 *
		 * @param array  $attributes The block attributes.
		 * @param string $block_type The Block Type.
		 * @since 1.8.2
		 */
		public static function get_query( $attributes, $block_type ) {
			$fallback_for_posts_to_show = UAGB_Block_Helper::get_fallback_number( $attributes['postsToShow'], 'postsToShow', $attributes['blockName'] );
			$fallback_for_offset        = UAGB_Block_Helper::get_fallback_number( $attributes['postsOffset'], 'postsOffset', $attributes['blockName'] );
			// Block type is grid/masonry/carousel/timeline.
			$query_args = array(
				'posts_per_page'      => $fallback_for_posts_to_show,
				'post_status'         => 'publish',
				'post_type'           => ( isset( $attributes['postType'] ) ) ? $attributes['postType'] : 'post',
				'order'               => ( isset( $attributes['order'] ) ) ? $attributes['order'] : 'desc',
				'orderby'             => ( isset( $attributes['orderBy'] ) ) ? $attributes['orderBy'] : 'date',
				'ignore_sticky_posts' => 1,
				'paged'               => 1,
			);

			if ( isset( $attributes['enableOffset'] ) && false !== $attributes['enableOffset'] && 0 !== $attributes['postsOffset'] ) {
				$query_args['offset'] = $fallback_for_offset;
			}

			if ( $attributes['excludeCurrentPost'] ) {
				$query_args['post__not_in'] = array( get_the_ID() );
			}

			if ( isset( $attributes['categories'] ) && '' !== $attributes['categories'] ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => ( isset( $attributes['taxonomyType'] ) ) ? $attributes['taxonomyType'] : 'category',
					'field'    => 'id',
					'terms'    => $attributes['categories'],
					'operator' => 'IN',
				);
			}

			if ( 'grid' === $block_type && isset( $attributes['postPagination'] ) && true === $attributes['postPagination'] ) {

				if ( get_query_var( 'paged' ) ) {

					$paged = get_query_var( 'paged' );

				} elseif ( get_query_var( 'page' ) ) {

					$paged = get_query_var( 'page' );

				} else {

					$paged = isset( $attributes['paged'] ) ? $attributes['paged'] : 1;

				}
				$query_args['posts_per_page'] = $attributes['postsToShow'];
				$query_args['paged']          = $paged;

			}

			if ( 'masonry' === $block_type && isset( $attributes['paginationType'] ) && 'none' !== $attributes['paginationType'] && isset( $attributes['paged'] ) ) {

				$query_args['paged'] = $attributes['paged'];

			}

			$query_args = apply_filters( "uagb_post_query_args_{$block_type}", $query_args, $attributes );

			return new WP_Query( $query_args );
		}

		/**
		 * Get size information for all currently-registered image sizes.
		 *
		 * @global $_wp_additional_image_sizes
		 * @uses   get_intermediate_image_sizes()
		 * @link   https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
		 * @since  1.9.0
		 * @return array $sizes Data for all currently-registered image sizes.
		 */
		public static function get_image_sizes() {

			global $_wp_additional_image_sizes;

			$sizes       = get_intermediate_image_sizes();
			$image_sizes = array();

			$image_sizes[] = array(
				'value' => 'full',
				'label' => esc_html__( 'Full', 'ultimate-addons-for-gutenberg' ),
			);

			foreach ( $sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
					$image_sizes[] = array(
						'value' => $size,
						'label' => ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					);
				} else {
					$image_sizes[] = array(
						'value' => $size,
						'label' => sprintf(
							'%1$s (%2$sx%3$s)',
							ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
							$_wp_additional_image_sizes[ $size ]['width'],
							$_wp_additional_image_sizes[ $size ]['height']
						),
					);
				}
			}

			$image_sizes = apply_filters( 'uagb_post_featured_image_sizes', $image_sizes );

			return $image_sizes;
		}

		/**
		 * Get Post Types.
		 *
		 * @since 1.11.0
		 * @access public
		 */
		public static function get_post_types() {

			$post_types = get_post_types(
				array(
					'public'       => true,
					'show_in_rest' => true,
				),
				'objects'
			);

			$options = array();

			foreach ( $post_types as $post_type ) {

				if ( 'attachment' === $post_type->name ) {
					continue;
				}

				$options[] = array(
					'value' => $post_type->name,
					'label' => $post_type->label,
				);
			}

			return apply_filters( 'uagb_loop_post_types', $options );
		}

		/**
		 *  Get - RGBA Color
		 *
		 *  Get HEX color and return RGBA. Default return RGB color.
		 *
		 * @param  var   $color      Gets the color value.
		 * @param  var   $opacity    Gets the opacity value.
		 * @param  array $is_array Gets an array of the value.
		 * @since   1.11.0
		 */
		public static function hex2rgba( $color, $opacity = false, $is_array = false ) {

			$default = $color;

			// Return default if no color provided.
			if ( empty( $color ) ) {
				return $default;
			}

			// Sanitize $color if "#" is provided.
			if ( '#' === $color[0] ) {
				$color = substr( $color, 1 );
			}

			// Check if color has 6 or 3 characters and get values.
			if ( strlen( $color ) === 6 ) {
					$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) === 3 ) {
					$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
					return $default;
			}

			// Convert hexadec to rgb.
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb).
			if ( false !== $opacity && '' !== $opacity ) {
				if ( abs( $opacity ) >= 1 ) {
					$opacity = $opacity / 100;
				}
				$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ',', $rgb ) . ')';
			}

			if ( $is_array ) {
				return $rgb;
			} else {
				// Return rgb(a) color string.
				return $output;
			}
		}

		/**
		 * Returns an array of paths for the upload directory
		 * of the current site.
		 *
		 * @since 1.14.0
		 * @return array
		 */
		public static function get_upload_dir() {

			$wp_info = wp_upload_dir( null, false );

			// SSL workaround.
			if ( self::is_ssl() ) {
				$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
			}

			// Build the paths.
			$dir_info = array(
				'path' => trailingslashit( trailingslashit( $wp_info['basedir'] ) . UAGB_UPLOAD_DIR_NAME ),
				'url'  => trailingslashit( trailingslashit( $wp_info['baseurl'] ) . UAGB_UPLOAD_DIR_NAME ),
			);

			// Create the upload dir if it doesn't exist.
			if ( ! file_exists( $dir_info['path'] ) ) {

				uagb_install()->create_files();
			}

			return apply_filters( 'uag_get_upload_dir', $dir_info );
		}

		/**
		 * Deletes the upload dir.
		 *
		 * @since 1.18.0
		 * @return array
		 */
		public static function delete_upload_dir() {

			$wp_info = wp_upload_dir( null, false );

			// Build the paths.
			$dir_info = array(
				'path' => trailingslashit( trailingslashit( $wp_info['basedir'] ) . UAGB_UPLOAD_DIR_NAME ),
			);

			// Check the upload dir if it doesn't exist or not.
			if ( file_exists( $dir_info['path'] ) ) {
				// Remove the directory.
				$wp_filesystem = uagb_filesystem();
				return $wp_filesystem->rmdir( $dir_info['path'], true );
			}

			return false;
		}

		/**
		 * Get UAG upload dir path.
		 *
		 * @since 1.23.0
		 * @return string
		 */
		public static function get_uag_upload_dir_path() {

			$wp_info = self::get_upload_dir();

			// Build the paths.
			return $wp_info['path'];
		}

		/**
		 * Get UAG upload url path.
		 *
		 * @since 1.23.0
		 * @return string
		 */
		public static function get_uag_upload_url_path() {

			$wp_info = self::get_upload_dir();

			// Build the paths.
			return $wp_info['url'];
		}

		/**
		 * Delete all files from UAG upload dir.
		 *
		 * @since 1.23.0
		 * @return string
		 */
		public static function delete_uag_asset_dir() {

			// Build the paths.
			$base_path = self::get_uag_upload_dir_path();

			// Get all files.
			$paths = glob( $base_path . 'assets/*' );

			foreach ( $paths as $path ) {

				// Check the dir if it exists or not.
				if ( file_exists( $path ) ) {

					$wp_filesystem = uagb_filesystem();

					// Remove the directory.
					$wp_filesystem->rmdir( $path, true );
				}
			}

			// Create empty files.
			uagb_install()->create_files();
			UAGB_Admin_Helper::create_specific_stylesheet();
			do_action( 'uagb_delete_uag_asset_dir' );
			return true;
		}

		/**
		 * Checks to see if the site has SSL enabled or not.
		 *
		 * @since 1.14.0
		 * @return bool
		 */
		public static function is_ssl() {
			if (
				is_ssl() ||
				( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) ||
				( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] )
			) {
				return true;
			}
			return false;
		}

		/**
		 * Allow File Geranation flag.
		 *
		 * @since  1.14.0
		 */
		public static function allow_file_generation() {
			return apply_filters( 'uagb_allow_file_generation', get_option( '_uagb_allow_file_generation', 'disabled' ) );
		}

		/**
		 * Check if UAG upload folder has write permissions or not.
		 *
		 * @since  1.14.9
		 * @return bool true or false.
		 */
		public static function is_uag_dir_has_write_permissions() {

			$upload_dir = self::get_upload_dir();

			return uagb_filesystem()->is_writable( $upload_dir['path'] );
		}
		/**
		 * Gives the paged Query var.
		 *
		 * @param Object $query Query.
		 * @return int $paged Paged Query var.
		 * @since 1.14.9
		 */
		public static function get_paged( $query ) {

			global $paged;

			// Check the 'paged' query var.
			$paged_qv = $query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		}
		/**
		 * Builds the base url.
		 *
		 * @param string $permalink_structure Premalink Structure.
		 * @param string $base Base.
		 * @since 1.14.9
		 */
		public static function build_base_url( $permalink_structure, $base ) {
			// Check to see if we are using pretty permalinks.
			if ( ! empty( $permalink_structure ) ) {

				if ( strrpos( $base, 'paged-' ) ) {
					$base = substr_replace( $base, '', strrpos( $base, 'paged-' ), strlen( $base ) );
				}

				// Remove query string from base URL since paginate_links() adds it automatically.
				// This should also fix the WPML pagination issue that was added since 1.10.2.
				if ( count( $_GET ) > 0 ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$base = strtok( $base, '?' );
				}

				// Add trailing slash when necessary.
				if ( '/' === substr( $permalink_structure, -1 ) ) {
					$base = trailingslashit( $base );
				} else {
					$base = untrailingslashit( $base );
				}
			} else {
				$url_params = wp_parse_url( $base, PHP_URL_QUERY );

				if ( empty( $url_params ) ) {
					$base = trailingslashit( $base );
				}
			}

			return $base;
		}
		/**
		 * Returns the Paged Format.
		 *
		 * @param string $permalink_structure Premalink Structure.
		 * @param string $base Base.
		 * @since 1.14.9
		 */
		public static function paged_format( $permalink_structure, $base ) {

			$page_prefix = empty( $permalink_structure ) ? 'paged' : 'page';

			if ( ! empty( $permalink_structure ) ) {
				$format  = substr( $base, -1 ) !== '/' ? '/' : '';
				$format .= $page_prefix . '/';
				$format .= '%#%';
				$format .= substr( $permalink_structure, -1 ) === '/' ? '/' : '';
			} elseif ( empty( $permalink_structure ) || is_search() ) {
				$parse_url = wp_parse_url( $base, PHP_URL_QUERY );
				$format    = empty( $parse_url ) ? '?' : '&';
				$format   .= $page_prefix . '=%#%';
			}

			return $format;
		}

		/**
		 * Get Typography Dynamic CSS.
		 *
		 * @param  array  $attr The Attribute array.
		 * @param  string $slug The field slug.
		 * @param  string $selector The selector array.
		 * @param  array  $combined_selectors The combined selector array.
		 * @since  1.15.0
		 * @return array
		 */
		public static function get_typography_css( $attr, $slug, $selector, $combined_selectors ) {

			$typo_css_desktop = array();
			$typo_css_tablet  = array();
			$typo_css_mobile  = array();

			$already_selectors_desktop = ( isset( $combined_selectors['desktop'][ $selector ] ) ) ? $combined_selectors['desktop'][ $selector ] : array();
			$already_selectors_tablet  = ( isset( $combined_selectors['tablet'][ $selector ] ) ) ? $combined_selectors['tablet'][ $selector ] : array();
			$already_selectors_mobile  = ( isset( $combined_selectors['mobile'][ $selector ] ) ) ? $combined_selectors['mobile'][ $selector ] : array();

			$family_slug     = ( '' === $slug ) ? 'fontFamily' : $slug . 'FontFamily';
			$weight_slug     = ( '' === $slug ) ? 'fontWeight' : $slug . 'FontWeight';
			$transform_slug  = ( '' === $slug ) ? 'fontTransform' : $slug . 'Transform';
			$decoration_slug = ( '' === $slug ) ? 'fontDecoration' : $slug . 'Decoration';
			$style_slug      = ( '' === $slug ) ? 'fontStyle' : $slug . 'FontStyle';

			$l_ht_slug        = ( '' === $slug ) ? 'lineHeight' : $slug . 'LineHeight';
			$f_sz_slug        = ( '' === $slug ) ? 'fontSize' : $slug . 'FontSize';
			$l_ht_type_slug   = ( '' === $slug ) ? 'lineHeightType' : $slug . 'LineHeightType';
			$f_sz_type_slug   = ( '' === $slug ) ? 'fontSizeType' : $slug . 'FontSizeType';
			$f_sz_type_t_slug = ( '' === $slug ) ? 'fontSizeTypeTablet' : $slug . 'FontSizeTypeTablet';
			$f_sz_type_m_slug = ( '' === $slug ) ? 'fontSizeTypeMobile' : $slug . 'FontSizeTypeMobile';
			$l_sp_slug        = ( '' === $slug ) ? 'letterSpacing' : $slug . 'LetterSpacing';
			$l_sp_type_slug   = ( '' === $slug ) ? 'letterSpacingType' : $slug . 'LetterSpacingType';

			$text_transform  = isset( $attr[ $transform_slug ] ) ? $attr[ $transform_slug ] : 'normal';
			$text_decoration = isset( $attr[ $decoration_slug ] ) ? $attr[ $decoration_slug ] : 'none';
			$font_style      = isset( $attr[ $style_slug ] ) ? $attr[ $style_slug ] : 'normal';

			$typo_css_desktop[ $selector ] = array(
				'font-family'     => $attr[ $family_slug ],
				'text-transform'  => $text_transform,
				'text-decoration' => $text_decoration,
				'font-style'      => $font_style,
				'font-weight'     => $attr[ $weight_slug ],
				'font-size'       => ( isset( $attr[ $f_sz_slug ] ) ) ? self::get_css_value( $attr[ $f_sz_slug ], $attr[ $f_sz_type_slug ] ) : '',
				'line-height'     => ( isset( $attr[ $l_ht_slug ] ) ) ? self::get_css_value( $attr[ $l_ht_slug ], $attr[ $l_ht_type_slug ] ) : '',
				'letter-spacing'  => ( isset( $attr[ $l_sp_slug ] ) ) ? self::get_css_value( $attr[ $l_sp_slug ], $attr[ $l_sp_type_slug ] ) : '',
			);

			$typo_css_desktop[ $selector ] = array_merge(
				$typo_css_desktop[ $selector ],
				$already_selectors_desktop
			);

			$typo_css_tablet[ $selector ] = array(
				'font-size'      => ( isset( $attr[ $f_sz_slug . 'Tablet' ] ) ) ? self::get_css_value( $attr[ $f_sz_slug . 'Tablet' ], ( isset( $attr[ $f_sz_type_t_slug ] ) ) ? $attr[ $f_sz_type_t_slug ] : $attr[ $f_sz_type_slug ] ) : '',
				'line-height'    => ( isset( $attr[ $l_ht_slug . 'Tablet' ] ) ) ? self::get_css_value( $attr[ $l_ht_slug . 'Tablet' ], $attr[ $l_ht_type_slug ] ) : '',
				'letter-spacing' => ( isset( $attr[ $l_sp_slug . 'Tablet' ] ) ) ? self::get_css_value( $attr[ $l_sp_slug . 'Tablet' ], $attr[ $l_sp_type_slug ] ) : '',
			);

			$typo_css_tablet[ $selector ] = array_merge(
				$typo_css_tablet[ $selector ],
				$already_selectors_tablet
			);

			$typo_css_mobile[ $selector ] = array(
				'font-size'      => ( isset( $attr[ $f_sz_slug . 'Mobile' ] ) ) ? self::get_css_value( $attr[ $f_sz_slug . 'Mobile' ], ( isset( $attr[ $f_sz_type_m_slug ] ) ) ? $attr[ $f_sz_type_m_slug ] : $attr[ $f_sz_type_slug ] ) : '',
				'line-height'    => ( isset( $attr[ $l_ht_slug . 'Mobile' ] ) ) ? self::get_css_value( $attr[ $l_ht_slug . 'Mobile' ], $attr[ $l_ht_type_slug ] ) : '',
				'letter-spacing' => ( isset( $attr[ $l_sp_slug . 'Mobile' ] ) ) ? self::get_css_value( $attr[ $l_sp_slug . 'Mobile' ], $attr[ $l_sp_type_slug ] ) : '',
			);

			$typo_css_mobile[ $selector ] = array_merge(
				$typo_css_mobile[ $selector ],
				$already_selectors_mobile
			);

			return array(
				'desktop' => array_merge(
					$combined_selectors['desktop'],
					$typo_css_desktop
				),
				'tablet'  => array_merge(
					$combined_selectors['tablet'],
					$typo_css_tablet
				),
				'mobile'  => array_merge(
					$combined_selectors['mobile'],
					$typo_css_mobile
				),
			);
		}

		/**
		 * Sets the selector to Global Block Styles Selector if applicable.
		 *
		 * @param string $selector Selector.
		 * @param array  $gbs_attributes GBS attributes array.
		 * @since 2.9.0
		 * @return string $selector Updated selector.
		 */
		public static function add_gbs_selector_if_applicable( $selector, $gbs_attributes ) {
			if ( empty( $gbs_attributes['globalBlockStyleId'] ) ) {
				return $selector;
			}

			return self::get_gbs_selector( $gbs_attributes['globalBlockStyleId'] );
		}

		/**
		 * Get the Global block styles CSS selector.
		 *
		 * @param string $style_name Style Name.
		 *
		 * @since 2.9.0
		 * @return string $selector Styles Selector.
		 */
		public static function get_gbs_selector( $style_name ) {

			if ( $style_name ) {
				return '.spectra-gbs-' . $style_name;
			}
			return '';
		}

		/**
		 * Parse CSS into correct CSS syntax.
		 *
		 * @param array  $combined_selectors The combined selector array.
		 * @param string $id The selector ID.
		 * @param string $gbs_class The GBS class as string.
		 *
		 * @since 1.15.0
		 * @return array $css CSS.
		 */
		public static function generate_all_css( $combined_selectors, $id, $gbs_class = '' ) {

			if ( ! empty( $gbs_class ) ) {
				$id = $gbs_class;
			}

			return array(
				'desktop' => self::generate_css( $combined_selectors['desktop'], $id ),
				'tablet'  => self::generate_css( $combined_selectors['tablet'], $id ),
				'mobile'  => self::generate_css( $combined_selectors['mobile'], $id ),
			);
		}
		/**
		 * Get Post Assets Instance.
		 */
		public function get_post_assets_instance() {
			return uagb_get_front_post_assets();
		}

		/** Generates stylesheet in loop.
		 *
		 * @since 1.7.0
		 * @param object $this_post Post Object.
		 * @deprecated 1.23.0
		 * @access public
		 */
		public function get_generated_stylesheet( $this_post ) {
			_deprecated_function( __METHOD__, '1.23.0' );

			if ( ! is_object( $this_post ) ) {
				return;
			}

			if ( ! isset( $this_post->ID ) ) {
				return;
			}

			if ( has_blocks( $this_post->ID ) && isset( $this_post->post_content ) ) {

				$blocks            = parse_blocks( $this_post->post_content );
				self::$page_blocks = $blocks;

				if ( ! is_array( $blocks ) || empty( $blocks ) ) {
					return;
				}

				$assets = $this->get_assets( $blocks );

				self::$stylesheet .= $assets['css'];
				self::$script     .= $assets['js'];
			}
		}

		/**
		 * Generates stylesheet for reusable blocks.
		 *
		 * @since 1.1.0
		 * @param array $blocks Blocks.
		 * @deprecated 1.23.0
		 * @access public
		 */
		public function get_assets( $blocks ) {
			_deprecated_function( __METHOD__, '1.23.0' );

			$desktop = '';
			$tablet  = '';
			$mobile  = '';

			$tab_styling_css = '';
			$mob_styling_css = '';

			$js = '';

			foreach ( $blocks as $i => $block ) {

				if ( is_array( $block ) ) {

					if ( empty( $block['blockName'] ) ) {
						continue;
					}

					if ( 'core/block' === $block['blockName'] ) {
						$id = ( isset( $block['attrs']['ref'] ) ) ? $block['attrs']['ref'] : 0;

						if ( $id ) {
							$content = get_post_field( 'post_content', $id );

							$reusable_blocks = parse_blocks( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];

						}
					} else {

						$block_assets = $this->get_block_css_and_js( $block );
						// Get CSS for the Block.
						$css = $block_assets['css'];

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

			$post_assets_instance = $this->get_post_assets_instance();
			if ( $post_assets_instance ) {

				$post_assets_instance->stylesheet .= $desktop . $tab_styling_css . $mob_styling_css;
				$post_assets_instance->script     .= $js;
			}

			return array(
				'css' => $desktop . $tab_styling_css . $mob_styling_css,
				'js'  => $js,
			);
		}

		/**
		 * Parse Guten Block.
		 *
		 * @since 1.1.0
		 * @param string $content the content string.
		 * @deprecated 1.23.0 Use `parse_blocks()` instead
		 * @access public
		 */
		public function parse( $content ) {
			_deprecated_function( __METHOD__, '1.23.0', 'parse_blocks()' );

			return parse_blocks( $content );
		}
		/**
		 * This is the action where we create dynamic asset files.
		 * CSS Path : uploads/uag-plugin/uag-style-{post_id}-{timestamp}.css
		 * JS Path : uploads/uag-plugin/uag-script-{post_id}-{timestamp}.js
		 *
		 * @since 1.15.0
		 * @deprecated 1.23.0
		 */
		public function generate_asset_files() {
			_deprecated_function( __METHOD__, '1.23.0' );

			global $content_width;
			self::$stylesheet = str_replace( '#CONTENT_WIDTH#', $content_width . 'px', self::$stylesheet );
			if ( '' !== self::$script ) {
				self::$script = 'document.addEventListener("DOMContentLoaded", function(){ ' . self::$script . ' })';
			}

			if ( 'enabled' === self::$file_generation ) {

				$post_assets_instance = $this->get_post_assets_instance();

				if ( $post_assets_instance ) {
					$post_assets_instance->stylesheet .= self::$stylesheet;
					$post_assets_instance->script     .= self::$script;
				}
			}
		}

		/**
		 * Enqueue Gutenberg block assets for both frontend + backend.
		 *
		 * @since 1.13.4
		 * @deprecated 1.23.0
		 */
		public function block_assets() {
			_deprecated_function( __METHOD__, '1.23.0' );

			$this->get_post_assets_instance()->enqueue_blocks_dependency_frontend();

		}
		/**
		 * Print the Script in footer.
		 *
		 * @since 1.15.0
		 * @deprecated 1.23.0
		 */
		public function print_script() {
			_deprecated_function( __METHOD__, '1.23.0' );

			$this->get_post_assets_instance()->print_script();

		}
		/**
		 * Print the Stylesheet in header.
		 *
		 * @since 1.15.0
		 * @deprecated 1.23.0
		 */
		public function print_stylesheet() {
			_deprecated_function( __METHOD__, '1.23.0' );

			$this->get_post_assets_instance()->print_stylesheet();

		}
		/**
		 * Load the front end Google Fonts.
		 *
		 * @since 1.15.0
		 * @deprecated 1.23.0
		 */
		public function frontend_gfonts() {
			_deprecated_function( __METHOD__, '1.23.0' );

			$this->get_post_assets_instance()->print_google_fonts();

		}
		/**
		 * Generates CSS recurrsively.
		 *
		 * @param object $block The block object.
		 * @since 0.0.1
		 * @deprecated 1.23.0
		 */
		public function get_block_css_and_js( $block ) {

			_deprecated_function( __METHOD__, '1.23.0' );

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

			self::$current_block_list[] = $name;

			if ( strpos( $name, 'uagb/' ) !== false ) {

				self::$uag_flag = true;
				$_block_slug    = str_replace( 'uagb/', '', $name );
				$_block_css     = UAGB_Block_Module::get_frontend_css( $_block_slug, $blockattr, $block_id );
				$_block_js      = UAGB_Block_Module::get_frontend_js( $_block_slug, $blockattr, $block_id );
				$css            = array_merge( $css, $_block_css );
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
							$content = get_post_field( 'post_content', $id );

							$reusable_blocks = $this->parse( $content );

							$assets = $this->get_assets( $reusable_blocks );

							self::$stylesheet .= $assets['css'];
							self::$script     .= $assets['js'];
						}
					} else {
						// Get CSS for the Block.
						$inner_assets    = $this->get_block_css_and_js( $inner_block );
						$inner_block_css = $inner_assets['css'];

						$css_desktop = ( isset( $css['desktop'] ) ? $css['desktop'] : '' );
						$css_tablet  = ( isset( $css['tablet'] ) ? $css['tablet'] : '' );
						$css_mobile  = ( isset( $css['mobile'] ) ? $css['mobile'] : '' );

						if ( isset( $inner_block_css['desktop'] ) ) {
							$css['desktop'] = $css_desktop . $inner_block_css['desktop'];
							$css['tablet']  = $css_tablet . $inner_block_css['tablet'];
							$css['mobile']  = $css_mobile . $inner_block_css['mobile'];
						}

						$js .= $inner_assets['js'];
					}
				}
			}

			self::$current_block_list = array_unique( self::$current_block_list );

			return array(
				'css' => $css,
				'js'  => $js,
			);
		}

		/**
		 * Generates stylesheet and appends in head tag.
		 *
		 * @since 0.0.1
		 * @deprecated 1.23.0
		 */
		public function generate_assets() {
			_deprecated_function( __METHOD__, '1.23.0' );

			$this_post = array();

			if ( class_exists( 'WooCommerce' ) ) {

				if ( is_cart() ) {

					$id        = get_option( 'woocommerce_cart_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_account_page() ) {

					$id        = get_option( 'woocommerce_myaccount_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout() ) {

					$id        = get_option( 'woocommerce_checkout_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_checkout_pay_page() ) {

					$id        = get_option( 'woocommerce_pay_page_id' );
					$this_post = get_post( $id );

				} elseif ( is_shop() ) {

					$id        = get_option( 'woocommerce_shop_page_id' );
					$this_post = get_post( $id );
				}

				if ( is_object( $this_post ) ) {
					$this->get_generated_stylesheet( $this_post );
				}
			}

			if ( is_single() || is_page() || is_404() ) {

				global $post;
				$this_post = $post;

				if ( ! is_object( $this_post ) ) {
					return;
				}

				/**
				 * Filters the post to build stylesheet for.
				 *
				 * @param \WP_Post $this_post The global post.
				 */
				$this_post = apply_filters( 'uagb_post_for_stylesheet', $this_post );

				$this->get_generated_stylesheet( $this_post );

			} elseif ( is_archive() || is_home() || is_search() ) {

				global $wp_query;
				$cached_wp_query = $wp_query;

				foreach ( $cached_wp_query as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$this->get_generated_stylesheet( $post );
				}
			}
		}

		/**
		 * Get the excerpt.
		 *
		 * @param int    $post_id          for the block.
		 * @param string $content          for post content.
		 * @param int    $length_fallback  for excerpt, after fallback has been done.
		 *
		 * @since 2.0.0
		 */
		public static function uagb_get_excerpt( $post_id, $content, $length_fallback ) {

			// If there's an excerpt provided from meta, use it.
			$excerpt = get_post_field( 'post_excerpt', $post_id );

			if ( empty( $excerpt ) ) { // If no excerpt provided from meta.
				$max_excerpt = 100;
				// If the content present on post, then trim it and use that.
				if ( ! empty( $content ) ) {
					$excerpt = apply_filters( 'the_excerpt', wp_trim_words( $content, $max_excerpt ) );
				}
			}
			// Trim the excerpt.
			if ( ! empty( $excerpt ) ) {
				$excerpt = explode( ' ', $excerpt );
				if ( count( $excerpt ) > $length_fallback ) {
					$excerpt = implode( ' ', array_slice( $excerpt, 0, $length_fallback ) ) . '...';
				} else {
					$excerpt = implode( ' ', $excerpt );
				}
			}

			return empty( $excerpt ) ? '' : $excerpt;
		}

		/**
		 * Get User Browser name
		 *
		 * @param string $user_agent Browser names.
		 * @return string
		 * @since 2.0.8
		 */
		public static function get_browser_name( $user_agent ) {

			if ( strpos( $user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) {
				return 'opera';
			} elseif ( strpos( $user_agent, 'Edg' ) || strpos( $user_agent, 'Edge' ) ) {
				return 'edge';
			} elseif ( strpos( $user_agent, 'Chrome' ) ) {
				return 'chrome';
			} elseif ( strpos( $user_agent, 'Safari' ) ) {
				return 'safari';
			} elseif ( strpos( $user_agent, 'Firefox' ) ) {
				return 'firefox';
			} elseif ( strpos( $user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) {
				return 'ie';
			}
		}

		/**
		 * Get block dynamic CSS selector with filters applied for extending it.
		 *
		 * @param string $block_name Block name to filter.
		 * @param array  $selectors Array of selectors to filter.
		 * @param array  $attr Attributes.
		 * @return array Combined selectors array.
		 * @since 2.4.0
		 */
		public static function get_combined_selectors( $block_name, $selectors, $attr ) {
			if ( ! is_array( $selectors ) ) {
				return $selectors;
			}

			$combined_selectors = array();

			foreach ( $selectors as $key => $selector ) {
				$hook_prefix                = ( 'desktop' === $key ) ? '' : '_' . $key;
				$combined_selectors[ $key ] = apply_filters( 'spectra_' . $block_name . $hook_prefix . '_styling', $selector, $attr );
			}

			return $combined_selectors;
		}

		/**
		 * This function deletes the Page assets from the Page Meta Key.
		 *
		 * @param int $post_id Post Id.
		 *
		 * @return void
		 * @since 1.23.0
		 */
		public static function delete_page_assets( $post_id ) {
			$current_post_type = get_post_type( $post_id );
			if ( 'wp_template_part' === $current_post_type || 'wp_template' === $current_post_type ) {

				// Delete all the TOC Post Meta on update of the template.
				delete_post_meta_by_key( '_uagb_toc_options' );

				UAGB_Admin_Helper::create_specific_stylesheet();

				/* Update the asset version */
				UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );
				return;
			}

			$unique_ids = get_option( '_uagb_fse_uniqids' );
			if ( ! empty( $unique_ids ) && is_array( $unique_ids ) ) {
				foreach ( $unique_ids as $id ) {
					delete_post_meta( (int) $id, '_uag_page_assets' );
				}
			}

			delete_post_meta( $post_id, '_uag_page_assets' );
			delete_post_meta( $post_id, '_uag_css_file_name' );
			delete_post_meta( $post_id, '_uag_js_file_name' );

			/* Update the asset version */
			UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );

			do_action( 'uagb_delete_page_assets' );
		}

		/**
		 * Does Post contains reusable blocks.
		 *
		 * @param int $post_id Post ID.
		 *
		 * @since 1.23.5
		 *
		 * @return boolean Wether the Post contains any Reusable blocks or not.
		 */
		public static function does_post_contain_reusable_blocks( $post_id ) {

			$post_content = get_post_field( 'post_content', $post_id, 'raw' );
			$tag          = '<!-- wp:block';
			$flag         = strpos( $post_content, $tag );
			return ( 0 === $flag || is_numeric( $flag ) );
		}

		/**
		 * Set alignment css function.
		 *
		 * @param string $align passed.
		 * @since 2.7.7
		 * @return array
		 */
		public static function alignment_css( $align ) {
			$align_css = array();
			switch ( $align ) {
				case 'left':
					$align_css = array(
						'margin-left'  => 0,
						'margin-right' => 'auto',
					);
					break;
				case 'center':
					$align_css = array(
						'margin-left'  => 'auto',
						'margin-right' => 'auto',
					);
					break;
				case 'right':
					$align_css = array(
						'margin-right' => 0,
						'margin-left'  => 'auto',
					);
					break;
			}
			return $align_css;
		}

		/**
		 * Get allowed HTML title tag.
		 *
		 * @param string $title_Tag HTML tag of title.
		 * @param array  $allowed_array Array of allowed HTML tags.
		 * @param string $default_tag Default HTML tag.
		 * @since 2.7.10
		 * @return string $title_Tag | $default_tag.
		 */
		public static function title_tag_allowed_html( $title_Tag, $allowed_array, $default_tag ) {
			return in_array( $title_Tag, $allowed_array, true ) ? sanitize_key( $title_Tag ) : $default_tag;
		}

		/**
		 * Check if file exists and delete it.
		 *
		 * @param string $file_name File name.
		 * @since 2.9.0
		 * @return void
		 */
		public static function remove_file( $file_name ) {
			if ( file_exists( $file_name ) ) {
				wp_delete_file( $file_name );
			}
		}
	}

	/**
	 *  Prepare if class 'UAGB_Helper' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Helper::get_instance();
}
