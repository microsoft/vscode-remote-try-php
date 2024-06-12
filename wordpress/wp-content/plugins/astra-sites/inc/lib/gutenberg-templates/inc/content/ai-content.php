<?php
/**
 * Sync Library
 *
 * @package Ast Block Templates
 * @since 1.0.0
 */

namespace Gutenberg_Templates\Inc\Content;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Importer\Importer_Helper;
use Gutenberg_Templates\Inc\Importer\Plugin;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Images;

/**
 * Sync Library
 *
 * @since 1.0.0
 */
class Ai_Content {

	use Instance;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->define_required_constants();
		add_action( 'wp_ajax_ast-block-templates-ai-content', array( $this, 'save_user_details' ) );
		add_action( 'wp_ajax_ast-block-templates-regenerate', array( $this, 'generate_ai_content' ) );
		add_action( 'wp_ajax_ast-block-templates-reset-business-details', array( $this, 'reset_business_details' ) );
		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'ast_templates_download_selected_images', array( $this, 'download_selected_images' ) );
	}

	/**
	 * Debug mode for testing.
	 *
	 * @return void
	 */
	public function footer() {
		if ( isset( $_GET['debug'] ) && Helper::instance()->is_debug_mode() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<xmp>';
			print_r( get_option( 'ast_block_ai_content_log', array() ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			print_r( get_option( 'zipwp_user_business_details' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			echo '</xmp>';
		}
	}

	/**
	 * Define Required Constants
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function define_required_constants() {
		define( 'AST_BLOCK_TEMPLATES_IMAGE_COUNT', 20 );
	}

	/**
	 * Reset Business details
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function reset_business_details() {

		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-reset-business-details', 'security' );

		delete_option( 'ast-block-templates-show-onboarding' );
		delete_option( 'zipwp_user_business_details' );
		delete_option( 'ast_block_ai_content_log' );
		delete_option( 'ast-templates-ai-content' );

		wp_send_json_success(
			array(
				'status'  => true,
			)
		);
	}

	/**
	 * Generate AI based blocks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function generate_ai_content() {
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ai-content', 'security' );

		$details = Importer_Helper::get_business_details();

		$post_data = array(
			'business_name' => isset( $details['business_name'] ) ? sanitize_text_field( $details['business_name'] ) : '',
			'business_description' => isset( $details['business_description'] ) ? sanitize_text_field( $details['business_description'] ) : '',
			'business_category' => isset( $details['business_category'] ) ? sanitize_text_field( $details['business_category'] ) : '',
			'category' => isset( $_POST['category'] ) ? intval( $_POST['category'] ) : '',
			'token' => isset( $details['token'] ) ? $details['token'] : '',
			'regenerate' => isset( $_POST['regenerate'] ) ? filter_var( $_POST['regenerate'], FILTER_VALIDATE_BOOLEAN ) : false,
			'block_type' => isset( $_POST['block_type'] ) ? sanitize_text_field( $_POST['block_type'] ) : 'block',
			'is_last_category' => isset( $_POST['is_last_category'] ) ? filter_var( $_POST['is_last_category'], FILTER_VALIDATE_BOOLEAN ) : false,
			'language_slug' => isset( $details['language'] ) ? sanitize_text_field( $details['language']['code'] ) : '',
			'language_name' => isset( $details['language'] ) ? sanitize_text_field( $details['language']['name'] ) : '',
		);

		$category_content = get_option( 'ast-templates-ai-content', array() );

		if ( ! $post_data['regenerate'] ) {
			if ( isset( $category_content[ $post_data['category'] ] ) ) {
				wp_send_json_success(
					array(
						'data' => $category_content,
						'status'  => true,
						'extra' => 'from club saved already',
						'single' => $category_content[ $post_data['category'] ],
						'spec_credit_details'   => Plugin::instance()->get_spec_credit_details(),
					)
				);
			}
		}

		$this->get_ai_content( $post_data, $category_content );
	}

	/**
	 * Get Club of Category
	 *
	 * @since 2.0.0
	 * @param array $categories Categories.
	 * @param array $post_data Post Data.
	 *
	 * @return string
	 */
	public function get_club_of_category( $categories, $post_data ) {

		$club_categories = null;

		foreach ( $categories as $single_category ) {
			if ( $single_category['id'] === $post_data['category'] ) {
				$club_categories = $single_category['club'] ?? null;
				break;
			}
		}
		return $club_categories;
	}

	/**
	 * Get Matching Categories
	 *
	 * @since 2.0.0
	 * @param array  $categories Categories.
	 * @param string $club_categories Club Categories.
	 *
	 * @return array
	 */
	public function get_matching_categories( $categories, $club_categories ) {

		$matching_categories = array();

		if ( null !== $club_categories ) {
			// Find all other categories with the same club.
			foreach ( $categories as $single_category ) {
				if ( isset( $single_category['club'] ) && $single_category['club'] === $club_categories ) {
					$matching_categories[] = $single_category;
				}
			}
		}

		return $matching_categories;
	}

	/**
	 * Get AI Content
	 *
	 * @since 2.0.0
	 * @param array  $post_data Post Data.
	 * @param string $category_content Categories content.
	 *
	 * @return void
	 */
	public function get_ai_content( $post_data, $category_content ) {

		$api_endpoint = Helper::instance()->is_debug_mode() ? AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/ai/v1/content?debug=yes' : AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/ai/v1/content';

		$request_args = array(
			'body' => wp_json_encode( $post_data ),
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'timeout' => 50,
		);
		$response = wp_safe_remote_post( $api_endpoint, $request_args );

		$log = get_option( 'ast_block_ai_content_log', array() );

		$log[ $post_data['category'] ] = array();
		if ( is_wp_error( $response ) ) {
			$log[ $post_data['category'] ] = 'Error: ' . $response->get_error_message();
			update_option( 'ast_block_ai_content_log', $log );
			wp_send_json_error(
				array(
					'data' => 'Error: ' . $response->get_error_message(),
					'status'  => false,

				)
			);
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );

			$response_data = json_decode( $response_body, true );

			if ( 200 === $response_code ) {
				if ( $response_data['status'] ) {

					if ( ! $post_data['regenerate'] ) {
						$categories = Helper::instance()->get_block_template_category();
						// Find the club of the current category.
						$club = $this->get_club_of_category( $categories, $post_data );
						// Find all other categories with the same club.
						$matching_categories = $this->get_matching_categories( $categories, $club );

						// Save same content for all categories with same club.
						foreach ( $matching_categories as $m_category ) {
							$category_content[ $m_category['id'] ] = $response_data['content'];
						}
						$category_content[ $post_data['category'] ] = $response_data['content'];
						update_option( 'ast-templates-ai-content', $category_content );
					} else {
						$category_content[ $post_data['category'] ] = $response_data['content'];
						update_option( 'ast-templates-ai-content', $category_content );
					}

					$log[ $post_data['category'] ] = $response_data['debug'];
					update_option( 'ast_block_ai_content_log', $log );

					// Set the new user flag to 'no'.
					if ( get_option( 'ast-block-templates-new-user', 'yes' ) === 'yes' ) {
						update_option( 'ast-block-templates-new-user', 'no' );
					}

					if ( Helper::instance()->is_debug_mode() ) {
						wp_send_json_success(
							array(
								'data' => $category_content,
								'status'  => true,
								'extra' => $response_data['debug'],
								'single' => $category_content[ $post_data['category'] ],
								'spec_credit_details'   => Plugin::instance()->get_spec_credit_details(),
							)
						);
					} else {
						wp_send_json_success(
							array(
								'data' => $category_content,
								'status'  => true,
								'spec_credit_details'   => Plugin::instance()->get_spec_credit_details(),
							)
						);
					}
				} else {
					$log[ $post_data['category'] ] = $response_data['data'];
					update_option( 'ast_block_ai_content_log', $log );
					wp_send_json_error(
						array(
							'data' => 'Failed ' . $response_data['data'],
							'status'  => false,
						)
					);
				}
			} else {
				$log[ $post_data['category'] ] = $response_code . ' - Failed';
				update_option( 'ast_block_ai_content_log', $log );
				wp_send_json_error(
					array(
						'data' => 'Failed',
						'status'  => false,
						'code' => $response_data['code'],
						'error' => $response_data['message'],
					)
				);
			}
		}
	}

	/**
	 * Save user details
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function save_user_details() {

		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ai-content', 'security' );

		$keywords = isset( $_POST['image_keyword'] ) ? json_decode( wp_unslash( $_POST['image_keyword'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$images = isset( $_POST['images'] ) ? json_decode( wp_unslash( $_POST['images'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$social_profiles = isset( $_POST['social_profiles'] ) ? json_decode( wp_unslash( $_POST['social_profiles'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$save_only = isset( $_POST['save_only'] ) ? filter_var( $_POST['save_only'], FILTER_VALIDATE_BOOLEAN ) : false;

		foreach ( $keywords as $key => $keyword ) {
			$keywords[ $key ] = sanitize_text_field( wp_unslash( $keyword ) );
		}

		if ( ! empty( $images ) ) {
			foreach ( $images as $key => $image ) {
				foreach ( $image as $j => $image_attr ) {
					switch ( $image_attr ) {
						case 'author_name':
						case 'orientation':
						case 'author_url':
						case 'description':
						case 'engine':
						case 'id':
							$images[ $key ][ $image_attr ] = sanitize_text_field( wp_unslash( $image_attr ) );
							break;

						case 'engine_url':
						case 'author_url':
						case 'optimized_url':
						case 'url':
							$images[ $key ][ $image_attr ] = esc_url_raw( wp_unslash( $image_attr ) );
							break;
					}
				}
			}
		}

		$language = isset( $_POST['language'] ) ? json_decode( wp_unslash( $_POST['language'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$business_details = get_option( 'zipwp_user_business_details', array() );
		$business_details['business_name'] = isset( $_POST['business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['business_name'] ) ) : '';
		$business_details['business_description'] = isset( $_POST['business_desc'] ) ? sanitize_text_field( wp_unslash( $_POST['business_desc'] ) ) : '';
		$business_details['business_category'] = isset( $_POST['business_category'] ) ? sanitize_text_field( wp_unslash( $_POST['business_category'] ) ) : '';
		$business_details['images'] = $images;
		$business_details['image_keyword'] = $keywords;
		$business_details['business_address'] = ( isset( $_POST['business_address'] ) ) ? sanitize_text_field( wp_unslash( $_POST['business_address'] ) ) : '';
		$business_details['business_phone']   = ( isset( $_POST['business_phone'] ) ) ? sanitize_text_field( wp_unslash( $_POST['business_phone'] ) ) : '';
		$business_details['business_email']   = ( isset( $_POST['business_email'] ) ) ? sanitize_email( wp_unslash( $_POST['business_email'] ) ) : '';
		$business_details['social_profiles']  = $social_profiles;
		$business_details['language']  = $language;

		update_option( 'ast-block-templates-show-onboarding', 'no' );
		update_option( 'zipwp_user_business_details', $business_details );

		if ( ! $save_only ) {
			delete_option( 'ast-templates-ai-content' );
		}

		// Schedule event to download images in background.
		wp_schedule_single_event( time() + 1, 'ast_templates_download_selected_images' );

		wp_send_json_success(
			array(
				'status'  => true,
				'images' => $images,
			)
		);
	}

	/**
	 * Download Selected Images
	 *
	 * @since 2.0.17
	 *
	 * @return void
	 */
	public function download_selected_images() {

		$image = get_option( 'zipwp_user_business_details', array() );
		$all_images = isset( $image['images'] ) ? $image['images'] : array();

		if ( empty( $all_images ) ) {
			return;
		}

		$images = $all_images;
		$downloaded_ids = array();

		foreach ( $images as $image ) {

			$image = array(
				'url' => $image['url'],
				'id'  => $image['id'],
				'description'  => $image['description'],
				'engine'  => $image['engine'],
			);
			$image_id = Images::instance()->download_image( $image );
			$downloaded_ids[ $image['id'] ] = $image_id;
		}

		update_option( 'ast_block_downloaded_images', $downloaded_ids );
	}

}
