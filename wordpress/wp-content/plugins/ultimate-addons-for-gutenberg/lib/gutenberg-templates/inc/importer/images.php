<?php
/**
 * AI content generator and Images file.
 *
 * @package {{package}}
 * @since {{since}}
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Importer_Helper;

/**
 * Images
 *
 * @since {{since}}
 */
class Images {

	use Instance;

	/**
	 * Images
	 *
	 * @since {{since}}
	 * @var (array) images
	 */
	public static $images = array(
		'landscape' => array(),
		'portrait'  => array(),
		'square'    => array(),
	);

	/**
	 * Image index
	 *
	 * @since {{since}}
	 * @var (int) image_index
	 */
	public static $image_index = 0;

	/**
	 * Get Images
	 *
	 * @return array Array of images.
	 * @since {{since}}
	 */
	public function get_images() {

		return Importer_Helper::get_business_details( 'images' );
	}

	/**
	 * Get Image for the specified index and orientation
	 *
	 * @param int $index Index of the image.
	 * @return array|boolean Array of images or false.
	 * @since {{since}}
	 */
	public function get_image( $index = 0 ) {
		$images = $this->get_images();
		Helper::instance()->ast_block_templates_log( 'Fetching image with index ' . $index );
		return ( isset( $images[ $index ] ) ) ? $images[ $index ] : false;
	}

	/**
	 * Download image from URL.
	 *
	 * @param array $image Image data.
	 * @return int|\WP_Error Image ID or WP_Error.
	 * @since {{since}}
	 */
	public function download_image( $image ) {
		$id = $image['id'];
		$downloaded_ids = get_option( 'ast_block_downloaded_images', array() );

		$downloaded_ids = ( is_array( $downloaded_ids ) ) ? $downloaded_ids : array();
		if ( array_key_exists( $id, $downloaded_ids ) ) {
			// Return already downloaded image.
			return $downloaded_ids[ $id ];
		}
		/* This is a Pixabay code $name = $image['tags']; Pixabay. */
		$name = 'zipwp-image-' . sanitize_title( $id );
		/* This is a Pixabay code $url  = $image['largeImageURL']; Pixabay. */
		$url = $image['url']; // Unsplash.

		$description = isset( $image['description'] ) ? $image['description'] : '';

		$name = preg_replace( '/\.[^.]+$/', '', $name ) . '.jpg';

		Helper::instance()->ast_block_templates_log( 'Downloading Image as "' . $name . '" : ' . $url );

		$wp_id = $this->create_image_from_url( $url, $name, $id, $description );
		$downloaded_ids[ $id ] = $wp_id;
		update_option( 'ast_block_downloaded_images', $downloaded_ids );
		return $wp_id;

	}

	/**
	 * Create the image and return the new media upload id.
	 *
	 * @param String $url URL to the image.
	 * @param String $name Name to the image.
	 * @param String $photo_id Photo ID to the image.
	 * @param String $description Description to the image.
	 * @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
	 */
	public function create_image_from_url( $url, $name, $photo_id, $description ) {
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
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );

		return $id;
	}
}
