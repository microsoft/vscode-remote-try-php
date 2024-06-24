<?php
/**
 * Image Importer
 *
 * => How to use?
 *
 *  $image = array(
 *      'url' => '<image-url>',
 *      'id'  => '<image-id>',
 *  );
 *
 *  $downloaded_image = Image_Importer::get_instance()->import( $image );
 *
 * @package Ast Block Templates
 * @since 1.0.0
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;

/**
 * Ast_Block Templates Image Importer
 *
 * @since 1.0.0
 */
class Image_Importer {

	use Instance;

	/**
	 * Images IDs
	 *
	 * @var array   The Array of already image IDs.
	 * @since 1.0.0
	 */
	private $already_imported_ids = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();
	}

	/**
	 * Process Image Download
	 *
	 * @since 1.0.0
	 * @param  array $attachments Attachment array.
	 * @return array              Attachment array.
	 */
	public function process( $attachments ) {

		$downloaded_images = array();

		foreach ( $attachments as $key => $attachment ) {
			$downloaded_images[] = $this->import( $attachment );
		}

		return $downloaded_images;
	}

	/**
	 * Get Hash Image.
	 *
	 * @since 1.0.0
	 * @param  string $attachment_url Attachment URL.
	 * @return string                 Hash string.
	 */
	public function get_hash_image( $attachment_url ) {
		return sha1( $attachment_url );
	}

	/**
	 * Get Saved Image.
	 *
	 * @since 1.0.0
	 * @param  string $attachment   Attachment Data.
	 * @return string                 Hash string.
	 */
	private function get_saved_image( $attachment ) {

		if ( apply_filters( 'ast_block_templates_image_importer_skip_image', false, $attachment ) ) {
			Helper::instance()->ast_block_templates_log( 'BATCH - SKIP Image - {from filter} - ' . $attachment['url'] . ' - Filter name `ast_block_templates_image_importer_skip_image`.' );
			return array(
				'status'     => true,
				'attachment' => $attachment,
			);
		}

		global $wpdb;

		// 1. Is already imported in Batch Import Process?
		$post_id = $wpdb->get_var( $wpdb->prepare( 'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = \'_ast_block_templates_image_hash\' AND `meta_value` = %s;', $this->get_hash_image( $attachment['url'] ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		// 2. Is image already imported though XML?
		if ( empty( $post_id ) ) {

			// Get file name without extension.
			// To check it exist in attachment.
			$filename = basename( $attachment['url'] );

			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s", '%/' . $filename . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			Helper::instance()->ast_block_templates_log( 'BATCH - SKIP Image {already imported from xml} - ' . $attachment['url'] );
		}

		if ( $post_id ) {
			$new_attachment               = array(
				'id'  => $post_id,
				'url' => wp_get_attachment_url( $post_id ),
			);
			$this->already_imported_ids[] = $post_id;

			return array(
				'status'     => true,
				'attachment' => $new_attachment,
			);
		}

		return array(
			'status'     => false,
			'attachment' => $attachment,
		);
	}

	/**
	 * Import Image
	 *
	 * @since 1.0.0
	 * @param  array $attachment Attachment array.
	 * @return array              Attachment array.
	 */
	public function import( $attachment ) {

		Helper::instance()->ast_block_templates_log( 'Source - ' . $attachment['url'] );
		$saved_image = $this->get_saved_image( $attachment );
		Helper::instance()->ast_block_templates_log( 'Log - ' . wp_json_encode( $saved_image['attachment'] ) );

		if ( $saved_image['status'] ) {
			return $saved_image['attachment'];
		}

		// Extract the file name and extension from the URL.
		$filename = basename( $attachment['url'] );

		if ( 'unsplash' === $attachment['engine'] ) {
			$filename = 'unsplash-photo-' . $attachment['id'] . '.jpg';
		}

		$file_content = wp_remote_retrieve_body(
			wp_safe_remote_get(
				$attachment['url'],
				array(
					'timeout'   => '60',
				)
			)
		);

		// Empty file content?
		if ( empty( $file_content ) ) {

			Helper::instance()->ast_block_templates_log( 'BATCH - FAIL Image {Error: Failed wp_remote_retrieve_body} - ' . $attachment['url'] );
			return $attachment;
		}

		$upload = wp_upload_bits( $filename, null, $file_content );

		Helper::instance()->ast_block_templates_log( $filename );
		Helper::instance()->ast_block_templates_log( wp_json_encode( $upload ) );

		$post = array(
			'post_title' => $filename,
			'guid'       => $upload['url'],
		);
		Helper::instance()->ast_block_templates_log( wp_json_encode( $post ) );

		$info = wp_check_filetype( $upload['file'] );
		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			// For now just return the origin attachment.
			return $attachment;
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			include ABSPATH . 'wp-admin/includes/image.php';
		}

		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata(
			$post_id,
			wp_generate_attachment_metadata( $post_id, $upload['file'] )
		);
		update_post_meta( $post_id, '_ast_block_templates_image_hash', $this->get_hash_image( $attachment['url'] ) );

		$new_attachment = array(
			'id'  => $post_id,
			'url' => $upload['url'],
		);

		Helper::instance()->ast_block_templates_log( 'BATCH - SUCCESS Image {Imported} - ' . $new_attachment['url'] );

		$this->already_imported_ids[] = $post_id;

		return $new_attachment;
	}

	/**
	 * Is Image URL
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url URL.
	 * @return boolean
	 */
	public function is_image_url( $url = '' ) {
		if ( empty( $url ) ) {
			return false;
		}

		if ( Helper::instance()->ast_block_templates_is_valid_image( $url ) ) {
			return true;
		}

		return false;
	}
}
