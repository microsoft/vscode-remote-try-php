<?php
/**
 * Astra Site Helper
 *
 * @since  1.0.0
 * @package Astra Sites
 */

if ( ! class_exists( 'Astra_Sites_Helper' ) ) :

	/**
	 * Astra_Sites_Helper
	 *
	 * @since 1.0.0
	 */
	class Astra_Sites_Helper {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Instance
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'wie_import_data', array( $this, 'custom_menu_widget' ) );
			add_filter( 'wp_prepare_attachment_for_js', array( $this, 'add_svg_image_support' ), 10, 3 );
		}

		/**
		 * Add svg image support
		 *
		 * @since 1.1.5
		 *
		 * @param array  $response    Attachment response.
		 * @param object $attachment Attachment object.
		 * @param array  $meta        Attachment meta data.
		 */
		public function add_svg_image_support( $response, $attachment, $meta ) {
			if ( ! function_exists( 'simplexml_load_file' ) ) {
				return $response;
			}

			if ( ! empty( $response['sizes'] ) ) {
				return $response;
			}

			if ( 'image/svg+xml' !== $response['mime'] ) {
				return $response;
			}

			$svg_path = get_attached_file( $attachment->ID );

			$dimensions = self::get_svg_dimensions( $svg_path );

			$response['sizes'] = array(
				'full' => array(
					'url'         => $response['url'],
					'width'       => $dimensions->width,
					'height'      => $dimensions->height,
					'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait',
				),
			);

			return $response;
		}

		/**
		 * Get SVG Dimensions
		 *
		 * @since 1.1.5
		 *
		 * @param  string $svg SVG file path.
		 * @return array      Return SVG file height & width for valid SVG file.
		 */
		public static function get_svg_dimensions( $svg ) {

			$svg = simplexml_load_file( $svg );

			if ( false === $svg ) {
				$width  = '0';
				$height = '0';
			} else {
				$attributes = $svg->attributes();
				$width      = (string) $attributes->width;
				$height     = (string) $attributes->height;
			}

			return (object) array(
				'width'  => $width,
				'height' => $height,
			);
		}

		/**
		 * Custom Menu Widget
		 *
		 * In widget export we set the nav menu slug instead of ID.
		 * So, In import process we check get menu id by slug and set
		 * it in import widget process.
		 *
		 * @since 1.0.7
		 *
		 * @param  object $all_sidebars Widget data.
		 * @return object               Set custom menu id by slug.
		 */
		public function custom_menu_widget( $all_sidebars ) {

			// Get current menu ID & Slugs.
			$menu_locations = array();
			$nav_menus      = (object) wp_get_nav_menus();
			if ( isset( $nav_menus ) ) {
				foreach ( $nav_menus as $menu_key => $menu ) {
					if ( is_object( $menu ) ) {
						$menu_locations[ $menu->term_id ] = $menu->slug;
					}
				}
			}

			// Import widget data.
			$all_sidebars = (object) $all_sidebars;
			foreach ( $all_sidebars as $widgets_key => $widgets ) {
				foreach ( $widgets as $widget_key => $widget ) {

					// Found slug in current menu list.
					if ( isset( $widget->nav_menu ) ) {
						$menu_id = array_search( $widget->nav_menu, $menu_locations, true );
						if ( ! empty( $menu_id ) ) {
							$all_sidebars->$widgets_key->$widget_key->nav_menu = $menu_id;
						}
					}
				}
			}

			return $all_sidebars;
		}

		/**
		 * Downloads an image from the specified URL.
		 *
		 * Taken from the core media_sideload_image() function and
		 * modified to return an array of data instead of html.
		 *
		 * @since 1.0.10
		 *
		 * @param string $file The image file path.
		 * @return array An array of image data.
		 */
		public static function sideload_image( $file ) {
			$data = new stdClass();

			if ( ! function_exists( 'media_handle_sideload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}

			if ( ! empty( $file ) ) {

				// Set variables for storage, fix file filename for query strings.
				preg_match( '/[^\?]+\.(jpe?g|jpe|svg|gif|png)\b/i', $file, $matches );
				$file_array         = array();
				$file_array['name'] = basename( $matches[0] );

				// Download file to temp location.
				$file_array['tmp_name'] = download_url( $file );

				// If error storing temporarily, return the error.
				if ( is_wp_error( $file_array['tmp_name'] ) ) {
					return $file_array['tmp_name'];
				}

				// Do the validation and storage stuff.
				$id = media_handle_sideload( $file_array, 0 );

				// If error storing permanently, unlink.
				if ( is_wp_error( $id ) ) {
					unlink( $file_array['tmp_name'] ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- Deleting the file from temp location.
					return $id;
				}

				// Build the object to return.
				$meta                = wp_get_attachment_metadata( $id );
				$data->attachment_id = $id;
				$data->url           = wp_get_attachment_url( $id );
				$data->thumbnail_url = wp_get_attachment_thumb_url( $id );
				$data->height        = isset( $meta['height'] ) ? $meta['height'] : '';
				$data->width         = isset( $meta['width'] ) ? $meta['width'] : '';
			}

			return $data;
		}

		/**
		 * Extract image URLs and other URLs from a given HTML content.
		 *
		 * @since 2.6.10
		 *
		 * @param string $content HTML content string.
		 * @return array Array of URLS.
		 */
		public static function extract_segregated_urls( $content ) {
			// Extract all links.
			preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $match );

			$extracts = array(
				'image' => array(),
				'other' => array(),
			);

			$all_links = array_unique( $match[0] );

			// Not have any link.
			if ( empty( $all_links ) ) {
				return array();
			}

			$image_links = array();
			$other_links = array();

			// Extract normal and image links.
			foreach ( $all_links as $key => $link ) {
				if ( preg_match( '/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-]+\.(jpg|png|gif|jpeg)\/?$/i', $link ) ) {

					// Get all image links.
					// Avoid *-150x, *-300x and *-1024x images.
					if (
						false === strpos( $link, '-150x' ) &&
						false === strpos( $link, '-300x' ) &&
						false === strpos( $link, '-1024x' )
					) {
						$image_links[] = $link;
					}
				} else {

					// Collect other links.
					$other_links[] = $link;
				}
			}

			$extracts['image'] = $image_links;
			$extracts['other'] = $other_links;

			return $extracts;
		}

		/**
		 * Get the client IP address.
		 *
		 * @since 2.6.4
		 */
		public static function get_client_ip() {
			$ipaddress = '';
			if ( getenv( 'HTTP_CLIENT_IP' ) ) {
				$ipaddress = getenv( 'HTTP_CLIENT_IP' );
			} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
				$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
			} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
				$ipaddress = getenv( 'HTTP_X_FORWARDED' );
			} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
				$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
			} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
				$ipaddress = getenv( 'HTTP_FORWARDED' );
			} elseif ( getenv( 'REMOTE_ADDR' ) ) {
				$ipaddress = getenv( 'REMOTE_ADDR' );
			} else {
				$ipaddress = 'UNKNOWN';
			}
			return $ipaddress;
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Helper::get_instance();

endif;
