<?php
/**
 * Template Lit Importer
 *
 * @package Ast Block Templates
 * @since 2.1.0
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Image_Importer;

/**
 * Ast_Block Templates Kit Importer
 *
 * @since 2.1.0
 */
class Template_Kit_Importer {

	use Instance;

	/**
	 * Constructor
	 *
	 * @since 2.1.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_ast_block_templates_kit_importer', array( $this, 'template_importer' ) );
		add_action( 'wp_ajax_ast_block_templates_import_template_kit', array( $this, 'import_template_kit' ) );
	}

	/**
	 * Template kit Importer
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function template_importer() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$api_uri = ( isset( $_REQUEST['api_uri'] ) ) ? esc_url_raw( $_REQUEST['api_uri'] ) : '';

		if ( ! Plugin::instance()->is_valid_url( $api_uri ) ) {
			wp_send_json_error(
				array(
					/* Translators: %s is API URL. */
					'message' => sprintf( __( 'Invalid Request URL - %s', 'ast-block-templates' ), $api_uri ),
					'code'    => 'Error',
				)
			);
		}

		$api_args = apply_filters(
			'ast_block_templates_api_args',
			array(
				'timeout' => 15,
			)
		);

		$request_params = apply_filters(
			'ast_block_templates_api_params',
			array(
				'_fields' => 'original_content',
			)
		);

		$demo_api_uri = esc_url_raw( add_query_arg( $request_params, $api_uri ) );

		// API Call.
		$response = wp_safe_remote_get( $demo_api_uri, $api_args );

		if ( is_wp_error( $response ) ) {
			if ( isset( $response->status ) ) {
				wp_send_json_error( json_decode( $response->status, true ) );
			} else {
				wp_send_json_error( $response->get_error_message() );
			}
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			wp_send_json_error( wp_remote_retrieve_body( $response ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		wp_send_json_success( $data['original_content'] );
	}

	/**
	 * Import Block
	 * 
	 * @return void
	 */
	public function import_template_kit() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		// Allow the SVG tags in batch update process.
		add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_tags_and_attributes' ), 10, 2 );

		$ids_mapping = get_option( 'ast_block_templates_wpforms_ids_mapping', array() );

		// Post content.
		$content = isset( $_REQUEST['content'] ) ? stripslashes( $_REQUEST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Empty mapping? Then return.
		if ( ! empty( $ids_mapping ) ) {
			// Replace ID's.
			foreach ( $ids_mapping as $old_id => $new_id ) {
				$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
				$content = str_replace( '{"formId":"' . $old_id . '"}', '{"formId":"' . $new_id . '"}', $content );
			}
		}

		// # Tweak
		// Gutenberg break block markup from render. Because the '&' is updated in database with '&amp;' and it
		// expects as 'u0026amp;'. So, Converted '&amp;' with 'u0026amp;'.
		//
		// @todo This affect for normal page content too. Detect only Gutenberg pages and process only on it.
		// $content = str_replace( '&amp;', "\u0026amp;", $content );
		$content = $this->get_content( $content );

		// Update content.
		wp_send_json_success( $content );
	}

	/**
	 * Allowed tags for the batch update process.
	 *
	 * @param  array<string, array<string, boolean>> $allowedposttags   Array of default allowable HTML tags.
	 * @param  string                                $context    The context for which to retrieve tags. Allowed values are 'post',
	 *                                                                 'strip', 'data', 'entities', or the name of a field filter such as
	 *                                                                 'pre_user_description'.
	 * @return array<string, array<string, boolean>> Array of allowed HTML tags and their allowed attributes.
	 */
	public function allowed_tags_and_attributes( $allowedposttags, $context ) {

		// Keep only for 'post' contenxt.
		if ( 'post' === $context ) {

			// <svg> tag and attributes.
			$allowedposttags['svg'] = array(
				'xmlns'   => true,
				'viewbox' => true,
			);

			// <path> tag and attributes.
			$allowedposttags['path'] = array(
				'd' => true,
			);
		}

		return $allowedposttags;
	}


	/**
	 * Download and Replace hotlink images
	 *
	 * @since 1.0.0
	 *
	 * @param  string $content Mixed post content.
	 * @return string
	 */
	public function get_content( $content = '' ) {

		// Extract all links.
		preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $match );

		$all_links = array_unique( $match[0] );

		// Not have any link.
		if ( empty( $all_links ) ) {
			return $content;
		}

		$link_mapping = array();
		$image_links  = array();
		$other_links  = array();

		// Extract normal and image links.
		foreach ( $all_links as $key => $link ) {
			if ( Helper::instance()->ast_block_templates_is_valid_image( $link ) ) {

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

		// Step 1: Download images.
		if ( ! empty( $image_links ) ) {
			foreach ( $image_links as $key => $image_url ) {
				// Download remote image.
				$image            = array(
					'url' => $image_url,
					'id'  => 0,
				);
				$downloaded_image = Image_Importer::instance()->import( $image );

				// Old and New image mapping links.
				$link_mapping[ $image_url ] = $downloaded_image['url'];
			}
		}

		// Step 3: Replace mapping links.
		foreach ( $link_mapping as $old_url => $new_url ) {
			$content = str_replace( strval( $old_url ), $new_url, $content );

			// Replace the slashed URLs if any exist.
			$old_url = str_replace( '/', '/\\', strval( $old_url ) );
			$new_url = str_replace( '/', '/\\', $new_url );
			$content = str_replace( $old_url, $new_url, $content );
		}

		return $content;
	}
}
