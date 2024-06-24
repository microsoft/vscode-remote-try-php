<?php
/**
 * Starter Templates Importer - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package Starter Templates Importer
 */

namespace STImporter\Importer;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Module Class.
 */
class ST_Importer_Helper {

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
	 * @return self initialized object of this class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get theme install, active or inactive status.
	 *
	 * @since 1.0.0
	 *
	 * @return string Theme status
	 */
	public static function get_theme_status() {

		$theme = wp_get_theme();

		// Theme installed and activate.
		if ( 'Astra' === $theme->name || 'Astra' === $theme->parent_theme ) {
			return 'installed-and-active';
		}

		// Theme installed but not activate.
		foreach ( (array) wp_get_themes() as $theme_dir => $theme ) {
			if ( 'Astra' === $theme->name || 'Astra' === $theme->parent_theme ) {
				return 'installed-but-inactive';
			}
		}

		return 'not-installed';
	}

	/**
	 * Get the API URL.
	 *
	 * @since  1.0.0
	 */
	public static function get_api_domain() {
		return defined( 'STARTER_TEMPLATES_REMOTE_URL' ) ? STARTER_TEMPLATES_REMOTE_URL : apply_filters( 'astra_sites_api_domain', 'https://websitedemos.net/' );
	}

	/**
	 * Get Hash Image.
	 *
	 * @since 1.0.0
	 * @param  string $attachment_url Attachment URL.
	 * @return string                 Hash string.
	 */
	public static function get_hash_image( $attachment_url ) {
		return sha1( $attachment_url );
	}

	/**
	 * Track Imported Post
	 *
	 * @param  int   $post_id Post ID.
	 * @param array $data Raw data imported for the post.
	 * @return void
	 */
	public static function track_post( $post_id = 0, $data = array() ) {

		update_post_meta( $post_id, '_astra_sites_imported_post', true );
		update_post_meta( $post_id, '_astra_sites_enable_for_batch', true );

		// Set the full width template for the pages.
		if ( isset( $data['post_type'] ) && 'page' === $data['post_type'] ) {
			$is_elementor_page = get_post_meta( $post_id, '_elementor_version', true );
			$theme_status      = ST_Importer_Helper::get_theme_status();
			if ( 'installed-and-active' !== $theme_status && $is_elementor_page ) {
				update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
			}
		} elseif ( isset( $data['post_type'] ) && 'attachment' === $data['post_type'] ) {
			$remote_url          = isset( $data['guid'] ) ? $data['guid'] : '';
			$attachment_hash_url = ST_Importer_Helper::get_hash_image( $remote_url );
			if ( ! empty( $attachment_hash_url ) ) {
				update_post_meta( $post_id, '_astra_sites_image_hash', $attachment_hash_url );
				update_post_meta( $post_id, '_elementor_source_image_hash', $attachment_hash_url );
			}
		}

	}

	/**
	 * Download image from URL.
	 *
	 * @param array $image Image data.
	 * @return int|\WP_Error Image ID or WP_Error.
	 * @since {{since}}
	 */
	public static function download_image( $image ) {

		$image_url = $image['url'];

		$id = $image['id'];

		$downloaded_ids = get_option( 'ast_sites_downloaded_images', array() );
		$downloaded_ids = ( is_array( $downloaded_ids ) ) ? $downloaded_ids : array();

		if ( array_key_exists( $id, $downloaded_ids ) ) {
			return $downloaded_ids[ $id ];
		}

		// Check if image is uploaded/downloaded already. If yes the update meta and mark it as downloaded.
		$site_domain = wp_parse_url( get_home_url(), PHP_URL_HOST );

		if ( strpos( $image_url, $site_domain ) !== false ) {

			$downloaded_ids[ $id ] = $id;

			// Add our meta data for uploaded image.
			if ( '1' !== get_post_meta( intval( $downloaded_ids[ $id ] ), '_astra_sites_imported_post', true ) ) {
				update_post_meta( $downloaded_ids[ $id ], '_astra_sites_imported_post', true );
			}

			update_option( 'ast_sites_downloaded_images', $downloaded_ids );

			return $downloaded_ids[ $id ];
		}

		// Use parse_url to get the path component of the URL.
		$path = wp_parse_url( $image_url, PHP_URL_PATH );

		if ( empty( $path ) ) {
			return new \WP_Error( 'parse_url', 'Unable to parse URL' );
		}

		// Use basename to extract the file name from the path.
		$image_name = basename( $path );

		// Fallback name.
		$image_name = $image_name ? $image_name : sanitize_title( $id );

		// Use pathinfo to get the file name without the extension.
		$image_extension = pathinfo( $image_name, PATHINFO_EXTENSION );

		// If the extension is empty, default to jpg. Set image_name with the extension.
		if ( empty( $image_extension ) ) {
			$image_extension = 'jpeg';
			$image_name      = $image_name . '.' . $image_extension;
		}

		$description = $image['description'] ?? '';

		$new_attachment_id = self::create_image_from_url( $image_url, $image_name, $id, $description );

		// Mark image downloaded.
		$downloaded_ids[ $id ] = $new_attachment_id;
		update_option( 'ast_sites_downloaded_images', $downloaded_ids );

		return $new_attachment_id;
	}

	/**
	 * Create the image and return the new media upload id.
	 *
	 * @param String $url URL to pixabay image.
	 * @param String $name Name to pixabay image.
	 * @param String $photo_id Photo ID to pixabay image.
	 * @param String $description Description to pixabay image.
	 * @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
	 */
	public static function create_image_from_url( $url, $name, $photo_id, $description = '' ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$file_array         = array();
		$file_array['name'] = wp_basename( $name );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $url );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array;
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, 0, null );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- Deleting the file from temp location.
			return $id;
		}

		$alt = ( '' === $description ) ? $name : $description;

		// Store the original attachment source in meta.
		add_post_meta( $id, '_source_url', $url );

		update_post_meta( $id, 'astra-images', $photo_id );
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		update_post_meta( $id, '_astra_sites_imported_post', true );
		return $id;
	}
}
