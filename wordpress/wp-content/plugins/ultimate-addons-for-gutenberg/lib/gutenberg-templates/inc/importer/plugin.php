<?php
/**
 * Init
 *
 * @since 1.0.0
 * @package Ast Block Templates
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Sync_Library;
use Gutenberg_Templates\Inc\Importer\Importer_Helper;
use Gutenberg_Templates\Inc\Importer\Image_Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const FALLBACK_TEXT = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.';

/**
 * Admin
 */
class Plugin {

	use Instance;

	/**
	 * Default Color Palette
	 *
	 * @since 2.0.0
	 * @access public
	 * @var string Last checksums.
	 */
	public static $color_palette = array();

	/**
	 * Custom Capability
	 *
	 * @since 2.1.14
	 * @access public
	 * @var string capabilities.
	 */
	public static $custom_capability = 'manage_ast_block_templates';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
		$this->define_constants();
		add_action( 'enqueue_block_editor_assets', array( $this, 'template_assets' ), 999 );
		add_action( 'admin_init', array( $this, 'init' ), 999 );
		add_action( 'admin_init', array( $this, 'add_custom_capabilities' ), 10 );

		if ( ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) || ( isset( $_SERVER['PHP_SELF'] ) && 'site-editor.php' === basename( sanitize_text_field( $_SERVER['PHP_SELF'] ) ) ) || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( esc_url_raw( $_SERVER['REQUEST_URI'] ), 'post-new.php' ) !== false ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_filter( 'zip_ai_auth_redirection_flag', '__return_false', 999 );
		}
		add_action( 'wp_ajax_ast_block_templates_importer', array( $this, 'template_importer' ) );
		add_action( 'wp_ajax_ast_block_templates_activate_plugin', array( $this, 'activate_plugin' ) );
		add_action( 'wp_ajax_ast_block_templates_import_wpforms', array( $this, 'import_wpforms' ) );
		add_action( 'wp_ajax_ast_block_templates_import_block', array( $this, 'import_block' ) );
		add_action( 'wp_ajax_ast_block_templates_color_palette', array( $this, 'get_color_palette' ) );
		add_action( 'wp_ajax_ast_block_templates_hide_notices', array( $this, 'hide_notices' ) );
		add_filter( 'upload_mimes', array( $this, 'custom_upload_mimes' ) );
		add_action( 'wp_ajax_ast_block_templates_data_option', array( $this, 'api_request' ) );
		$this->get_default_color_palette();
	}

	/**
	 * Add custom capabilities.
	 *
	 * @since 2.1.14
	 * @return void
	 */
	public function add_custom_capabilities() {
		$this->remove_custom_capability_from_other_roles();
		$roles = apply_filters( 'ast_block_template_capability_additional_roles', array( 'administrator' ) );

		// Loop through each role and add the custom capability.
		foreach ( $roles as $role_slug ) {
			$role_object = get_role( $role_slug );
			if ( $role_object ) {
				$role_object->add_cap( self::$custom_capability );
			}
		}
	}

	/**
	 * Remove custom capabilities.
	 *
	 * @since 2.1.14
	 * @return void
	 */
	public function remove_custom_capability_from_other_roles() {
		// Default role to retain the custom capability.
		$default_role = 'administrator';

		// Get the default role object.
		$default_role_object = get_role( $default_role );
	
		// Remove the custom capability from all roles except the default role.
		if ( $default_role_object ) {
			$roles = wp_roles()->role_names;
			unset( $roles[ $default_role ] ); // Exclude the default role.
	
			foreach ( $roles as $role_slug => $role_name ) {
				$role_object = get_role( $role_slug );
				if ( $role_object && $role_object->has_cap( self::$custom_capability ) ) {
					$role_object->remove_cap( self::$custom_capability );
				}
			}
		}
	}

	/**
	 * Define constants.
	 * 
	 * @return void
	 */
	public function define_constants() {
		if ( ! defined( 'ZIPWP_APP' ) ) {
			define( 'ZIPWP_APP', apply_filters( 'ast_block_templates_zip_app_url', 'https://app.zipwp.com/auth' ) );
		}
	}

	/**
	 * Get Color Palette.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_default_color_palette() {

		if ( empty( self::$color_palette ) ) {
			self::$color_palette = array(
				'#046bd2',
				'#045cb4',
				'#1e293b',
				'#334155',
				'#f9fafb',
				'#FFFFFF',
				'#ADB6BE',
				'#111111',
				'#94a3b8',
			);
		}

		return self::$color_palette;
	}

	/**
	 * Add .json files as supported format in the uploader.
	 *
	 * @param array $mimes Already supported mime types.
	 */
	public function custom_upload_mimes( $mimes ) {

		// Allow JSON files.
		$mimes['json'] = 'application/json';

		return $mimes;
	}

	/**
	 * Save the auth token in business details option.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function init() {
		// Add token when user authorized from GT library.
		if ( isset( $_GET['ast_action'] ) && 'auth' === $_GET['ast_action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// Get the nonce.
			$nonce = ( isset( $_GET['nonce'] ) ) ? sanitize_key( $_GET['nonce'] ) : '';

			// If the nonce is not valid, or if there's no token, then abandon ship.
			if ( false === wp_verify_nonce( $nonce, 'zip_ai_auth_nonce' ) ) {
				return;
			}

			// Update the variables for ZipAI.
			$spec_ai_settings = Helper::get_setting();

			// Update the auth token if needed.
			if ( isset( $_GET['credit_token'] ) && is_string( $_GET['credit_token'] ) ) {
				$spec_ai_settings['auth_token'] = Helper::encrypt( sanitize_text_field( $_GET['credit_token'] ) );
			}

			// Update the Zip token if needed.
			if ( isset( $_GET['token'] ) && is_string( $_GET['token'] ) ) {
				$spec_ai_settings['zip_token'] = Helper::encrypt( sanitize_text_field( $_GET['token'] ) );
			}

			// Update the email if needed.
			if ( isset( $_GET['email'] ) && is_string( $_GET['email'] ) ) {
				$spec_ai_settings['email'] = sanitize_email( $_GET['email'] );
			}

			update_option( 'zip_ai_settings', $spec_ai_settings );
		}
	}


	/**
	 * Update disable AI settings based on AI Design Copilot status.
	 *
	 * @since 2.0.18
	 * @return void
	 */
	public function sync_disable_ai_settings() {
		
		$ast_ai_settings = get_option( 'ast_block_templates_ai_settings', array() );
		$zip_ai_modules_settings = Helper::get_admin_settings_option( 'zip_ai_modules' );

		if ( isset( $zip_ai_modules_settings['ai_design_copilot']['status'] ) ) {

			$zi_copipt_status = $zip_ai_modules_settings['ai_design_copilot']['status'];

			if ( 'disabled' === $zi_copipt_status ) {
				$ast_ai_settings['disable_ai'] = true;
			}
	
			if ( 'enabled' === $zi_copipt_status ) {
				$ast_ai_settings['disable_ai'] = false;
			}

			update_option( 'ast_block_templates_ai_settings', $ast_ai_settings );
		}
	}

	/**
	 * Retrieve block data from an API and update the option with the data.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function api_request() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}

		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );
		$block_id     = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : '';
		$block_type   = isset( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : '';

		if ( 'site-pages' === $block_type ) {
			// Use this for premium pages.
			$request_params = apply_filters(
				'astra_sites_api_params',
				array(
					'purchase_key' => '',
					'site_url'     => site_url(),
				)
			);

			$complete_url = add_query_arg( $request_params, trailingslashit( AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/wp/v2/' . $block_type . '/' . $block_id ) );
		} else {
			$complete_url = AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/wp/v2/' . $block_type . '/' . $block_id . '/?site_url=' . site_url();
		}
		$response = wp_safe_remote_get( $complete_url );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( __( 'Something went wrong', 'ast-block-templates' ) );
		}

		if ( 200 !== $response['response']['code'] ) {
			wp_send_json_error( __( 'Something went wrong', 'ast-block-templates' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		// Create a dynamic option name to save the block data.
		update_option( 'ast-block-templates_data-' . $block_id, $body );
		wp_send_json_success( $body );
	}
	

	/**
	 * Hide notice.
	 *
	 * @since 2.1.1
	 * @return void 
	 */
	public function hide_notices() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}

		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$notice_type = isset( $_REQUEST['notice_type'] ) ? sanitize_text_field( $_REQUEST['notice_type'] ) : '';

		if ( ! empty( $notice_type ) ) {

			switch ( $notice_type ) {
				case 'personalize-ai':
					set_transient( 'ast_block_templates_hide_personalize_ai_notice', true, 30 * DAY_IN_SECONDS );
					break;
				case 'build-page-ai':
					set_transient( 'ast_block_templates_hide_build_page_ai_notice', true, 30 * DAY_IN_SECONDS );
					break;
				case 'credit-warning':
					set_transient( 'ast_block_templates_hide_credit_warning_notice', true, 30 * DAY_IN_SECONDS );
					break;
				case 'credit-danger':
					set_transient( 'ast_block_templates_hide_credit_danger_notice', true, 30 * DAY_IN_SECONDS );
					break;
				
				default:
					break;
			}       
		}

		wp_send_json_success(
			array(
				'status' => true,
			) 
		);
	}

	/**
	 * Get the Color palette.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function get_color_palette() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}

		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		wp_send_json_success(
			array(
				'block' => $this->get_block_palette_colors(),
				'page' => $this->get_page_palette_colors(),
			) 
		);
	}

	/**
	 * Import WP Forms
	 *
	 * @since 1.0.0
	 *
	 * @param  string $wpforms_url WP Forms JSON file URL.
	 * @return void
	 */
	public function import_wpforms( $wpforms_url = '' ) {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$block_id   = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : '';
		$block_data = get_option( 'ast-block-templates_data-' . $block_id );

		$block_data  = null !== $block_data ? $block_data : '';
		$wpforms_url = '';

		if ( 'astra-blocks' === $block_data->{'type'} ) {
			$wpforms_url = $block_data->{'post-meta'}->{'astra-site-wpforms-path'};
		}

		if ( 'site-pages' === $block_data->{'type'} ) {
			$wpforms_url = $block_data->{'astra-site-wpforms-path'};
		}

		$ids_mapping = array();

		if ( ! empty( $wpforms_url ) && function_exists( 'wpforms_encode' ) ) {

			// Download JSON file.
			$file_path = $this->download_file( $wpforms_url );

			if ( $file_path['success'] ) {
				if ( isset( $file_path['data']['file'] ) ) {

					$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

					if ( 'json' === $ext ) {
						$forms = json_decode( Helper::instance()->ast_block_templates_get_filesystem()->get_contents( $file_path['data']['file'] ), true );

						if ( ! empty( $forms ) ) {

							foreach ( $forms as $form ) {
								$title = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
								$desc  = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';

								$new_id = post_exists( $title, '', '', 'wpforms' );

								if ( ! $new_id ) {
									$new_id = wp_insert_post(
										array(
											'post_title'   => $title,
											'post_status'  => 'publish',
											'post_type'    => 'wpforms',
											'post_excerpt' => $desc,
										)
									);

									ast_block_templates_log( 'Imported Form ' . $title );
								}

								if ( $new_id ) {

									// ID mapping.
									$ids_mapping[ $form['id'] ] = $new_id;

									$form['id'] = $new_id;
									wp_update_post(
										array(
											'ID' => $new_id,
											'post_content' => wpforms_encode( $form ),
										)
									);
								}
							}
						}
					}
				}
			} else {
				wp_send_json_error( $file_path );
			}
		} else {
			wp_send_json_error( __( 'Something went wrong', 'ast-block-templates' ) );
		}

		update_option( 'ast_block_templates_wpforms_ids_mapping', $ids_mapping );

		wp_send_json_success( $ids_mapping );
	}

	/**
	 * Import Block
	 */
	public function import_block() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		// Allow the SVG tags in batch update process.
		add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_tags_and_attributes' ), 10, 2 );

		$ids_mapping = get_option( 'ast_block_templates_wpforms_ids_mapping', array() );

		// Post content.
		$content = isset( $_REQUEST['content'] ) ? stripslashes( $_REQUEST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$category = isset( $_REQUEST['category'] ) ? intval( $_REQUEST['category'] ) : '';

		// Empty mapping? Then return.
		if ( ! empty( $ids_mapping ) ) {
			// Replace ID's.
			foreach ( $ids_mapping as $old_id => $new_id ) {
				$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
				$content = str_replace( '{"formId":"' . $old_id . '"}', '{"formId":"' . $new_id . '"}', $content );
			}
		}

		$style = isset( $_REQUEST['style'] ) ? sanitize_text_field( $_REQUEST['style'] ) : 'style-1';
		$block_type = isset( $_REQUEST['block_type'] ) ? sanitize_text_field( $_REQUEST['block_type'] ) : 'block';

		$color_palettes = array();
		$is_astra_theme = class_exists( 'Astra_Global_Palette' );

		if ( 'block' === $block_type ) {
			$mapping_palette = array(
				'style-1' => array( 0, 1, 2, 3, 5, 5, 6, 7, 8 ),
				'style-2' => array( 0, 1, 2, 3, 4, 5, 6, 7, 8 ),
				'style-3' => array( 3, 2, 5, 4, 0, 1, 6, 7, 8 ),
			);

			$color_palettes = ! $is_astra_theme ? $this->get_block_palette_colors()[ $style ]['colors'] : $color_palettes;
		} else {
			$mapping_palette = array(
				'style-1' => array( 0, 1, 2, 3, 4, 5, 6, 7, 8 ),
				'style-2' => array( 0, 1, 5, 4, 3, 2, 6, 7, 8 ),
			);

			$color_palettes = ! $is_astra_theme ? $this->get_page_palette_colors()[ $style ]['colors'] : $color_palettes;
		}

		if ( ! $is_astra_theme ) {
			for ( $i = 0; $i < 9; $i++ ) {
				$target = $color_palettes[ $i ];
				$content = str_replace( 'var(\u002d\u002dast-global-color-' . $i . ')', $target, $content );
				$content = str_replace( 'var(--ast-global-color-' . $i . ')', $target, $content );
			}
		} else {
			for ( $i = 0; $i < 9; $i++ ) {
				$target = $mapping_palette[ $style ][ $i ];
				$content = str_replace( 'var(\u002d\u002dast-global-color-' . $i . ')', 'var(\u002d\u002dast-global-color-temp-' . $target . ')', $content );
				$content = str_replace( 'var(--ast-global-color-' . $i . ')', 'var(--ast-global-color-temp-' . $target . ')', $content );
			}
			$content = str_replace( 'var(\u002d\u002dast-global-color-temp-', 'var(\u002d\u002dast-global-color-', $content );
			$content = str_replace( 'var(--ast-global-color-temp-', 'var(--ast-global-color-', $content );
		}

		$disable_ai = isset( $_REQUEST['disableAI'] ) ? 'true' === $_REQUEST['disableAI'] : false;

		if ( ! $disable_ai && ! empty( Importer_Helper::get_business_details( 'business_description' ) ) ) {
			$category_content = get_option( 'ast-templates-ai-content', array() );
			$dynamic_content = ( isset( $category_content[ $category ] ) ) ? $category_content[ $category ] : array();
			$content = $this->replace( $content, $dynamic_content );
		} else {
			$content = $this->maybe_import_images( $content );
		}

		// Update content.
		wp_send_json_success( $content );
	}

	/**
	 * Import Images if required.
	 *
	 * @param string $content block content.
	 * @return string
	 */
	public function maybe_import_images( $content ) {

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
			$old_url = (string) $old_url;
			$content = str_replace( $old_url, $new_url, $content );

			// Replace the slashed URLs if any exist.
			$old_url = str_replace( '/', '/\\', $old_url );
			$new_url = str_replace( '/', '/\\', $new_url );
			$content = str_replace( $old_url, $new_url, $content );
		}
		return $content;
	}

	/**
	 * Replace content
	 *
	 * @param  string $content         Content.
	 * @param  array  $dynamic_content Dynamic content.
	 * @return string                  Content.
	 */
	public function replace( $content, $dynamic_content ) {
		$blocks = parse_blocks( $content );
		return apply_filters( 'aist/replace_content', serialize_blocks( $this->get_updated_blocks( $blocks, $dynamic_content ) ) ); // phpcs:ignore
	}

	/**
	 * Update the Blocks with new mapping data.
	 *
	 * @since {{since}}
	 * @param array<mixed> $blocks Array of Blocks.
	 * @param array<mixed> $dynamic_content Array of dynamic content.
	 * @return array<mixed> $blocks Modified array of Blocks.
	 */
	public function get_updated_blocks( &$blocks, $dynamic_content ) {

		if ( empty( $dynamic_content ) ) {
			return $blocks;
		}

		foreach ( $blocks as $i => &$block ) {

			if ( is_array( $block ) ) {

				if ( '' === $block['blockName'] ) {
					continue;
				}

				/** Replace images and google map if present in the block */
				switch ( $block['blockName'] ) {
					case 'uagb/container':
						$block = BlockEditor::instance()->parse_spectra_container( $block );
						break;

					case 'uagb/image':
						$block = BlockEditor::instance()->parse_spectra_image( $block );
						break;

					case 'uagb/image-gallery':
						$block = BlockEditor::instance()->parse_spectra_gallery( $block );
						break;

					case 'uagb/info-box':
						$block = BlockEditor::instance()->parse_spectra_infobox( $block );
						break;

					case 'uagb/google-map':
						$block = BlockEditor::instance()->parse_spectra_google_map( $block );
						break;

					case 'uagb/forms':
						$block = BlockEditor::instance()->parse_spectra_form( $block );
						break;

					case 'uagb/icon-list':
						$block = BlockEditor::instance()->parse_spectra_social_icons( $block );
						break;
				}

				if ( ! empty( $block['innerBlocks'] ) ) {
					/** Find the last node of the nested blocks */
					$block['innerBlocks'] = $this->get_updated_blocks( $block['innerBlocks'], $dynamic_content );
				} else {
					foreach ( $dynamic_content as $key => $value ) {
						$ai_content = $value;
						if ( ! str_contains( $block['innerHTML'], $key ) ) {
							continue;
						}

						if ( empty( $ai_content ) ) {
							// Generating random content.
							$ai_content = isset( $key ) ? $key : '';
							if ( '' === $ai_content ) {
								$words          = str_word_count( FALLBACK_TEXT, 1 ); // Split the statement into an array of words.
								$selected_words = array_slice( $words, 0, absint( 10 ) ); // Added atstic 10 words. Here fallback logic will be added.
								$ai_content     = implode( ' ', $selected_words );
							}
							Helper::instance()->ast_block_templates_log( 'No content found for "' . $key );
						}

						$ai_content = BlockEditor::instance()->replace_contact_details( $key, $ai_content );

						if ( ! empty( $ai_content ) ) {
							Helper::instance()->ast_block_templates_log( 'Replacing content from the "' . $key . '" to "' . $ai_content . '"' );
							$text               = str_replace( $key, $ai_content, $block['innerHTML'] );
							$block['innerHTML'] = $text;
							foreach ( $block['innerContent'] as $k => $inner_content ) {
								$block['innerContent'][ $k ] = ( isset( $block['innerContent'][ $k ] ) && is_string( $block['innerContent'][ $k ] ) ) ? str_replace( $key, $ai_content, $block['innerContent'][ $k ] ) : $block['innerContent'][ $k ];
							}
							foreach ( $block['attrs'] as $j => &$attr ) {
								if ( is_string( $attr ) ) {
									$block['attrs'][ $j ] = str_replace( $key, $ai_content, $block['attrs'][ $j ] );
								} elseif ( is_array( $attr ) ) {
									$this->recursively_traverse_attrs( $attr, $key, $ai_content );
								}
							}
						}
					}
				}
			}
		}

		return $blocks;
	}

	/**
	 * Traverse the attributes recursively.
	 *
	 * @since {{since}}
	 * @param array<mixed> $attrs Reference of the attributes array.
	 * @param string       $match Placeholder match.
	 * @param string       $ai_content AI generated content.
	 * @return void
	 */
	public function recursively_traverse_attrs( array &$attrs, $match, $ai_content ) {
		foreach ( $attrs as &$element ) {
			if ( is_array( $element ) ) {
				$this->recursively_traverse_attrs( $element, $match, $ai_content );
			} else {
				$element = is_string( $element ) ? str_replace( $match, $ai_content, $element ) : $element;
			}
		}
	}

	/**
	 * Allowed tags for the batch update process.
	 *
	 * @param  array        $allowedposttags   Array of default allowable HTML tags.
	 * @param  string|array $context    The context for which to retrieve tags. Allowed values are 'post',
	 *                                  'strip', 'data', 'entities', or the name of a field filter such as
	 *                                  'pre_user_description'.
	 * @return array Array of allowed HTML tags and their allowed attributes.
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
	 * Activate Plugin
	 */
	public function activate_plugin() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'ast-block-templates' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', 'security' );

		wp_clean_plugins_cache();

		$plugin_init = ( isset( $_POST['init'] ) ) ? sanitize_text_field( $_POST['init'] ) : '';

		$activate = activate_plugin( $plugin_init, '', false, true );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error( $activate->get_error_message() );
		}

		wp_send_json_success(
			array(
				'message' => 'Plugin activated successfully.',
			)
		);
	}

	/**
	 * Template Importer
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function template_importer() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$block_id   = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : '';
		$block_data = get_option( 'ast-block-templates_data-' . $block_id );

		$api_uri = null !== $block_data ? $block_data->{'astra-page-api-url'} : '';

		// Early return.
		if ( '' == $api_uri ) {
			wp_send_json_error( __( 'Something wrong', 'ast-block-templates' ) );
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

		$demo_api_uri = add_query_arg( $request_params, $api_uri );

		// API Call.
		$response = wp_safe_remote_get( $demo_api_uri, $api_args );

		if ( is_wp_error( $response ) || ( isset( $response->status ) && 0 === $response->status ) ) {
			if ( isset( $response->status ) ) {
				wp_send_json_error( json_decode( $response, true ) );
			} else {
				wp_send_json_error( $response->get_error_message() );
			}
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			wp_send_json_error( wp_remote_retrieve_body( $response ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		// Flush the object when import is successful.
		delete_option( 'ast-block-templates_data-' . $block_id );

		wp_send_json_success( $data['original_content'] );
	}

	/**
	 * Template Assets
	 *
	 * @since 1.0.0
	 */
	public function template_assets() {
		
		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			return;
		}

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		$current_screen = get_current_screen();

		if ( ! is_object( $current_screen ) && is_null( $current_screen ) ) {
			return false;
		}

		if ( 'site-editor' !== $current_screen->base && ! array_key_exists( $current_screen->post_type, $post_types ) ) {
			return;
		}

		$is_white_label = $this->is_white_label();

		if ( $is_white_label ) {
			return;
		}

		$this->sync_disable_ai_settings();

		wp_enqueue_script( 'ast-block-templates', AST_BLOCK_TEMPLATES_URI . 'dist/main.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'masonry', 'imagesloaded', 'updates', 'media-upload', 'wp-util' ), AST_BLOCK_TEMPLATES_VER, true );
		wp_add_inline_script( 'ast-block-templates', 'window.lodash = _.noConflict();', 'after' );
		wp_enqueue_media();
		
		wp_enqueue_style( 'ast-block-templates', AST_BLOCK_TEMPLATES_URI . 'dist/style.css', array(), AST_BLOCK_TEMPLATES_VER, 'all' );

		wp_enqueue_script(
			'ast-block-templates-plugin',
			AST_BLOCK_TEMPLATES_URI . 'admin-assets/js/plugin.js',
			array( 'jquery', 'wp-util', 'updates', 'media-upload' ),
			AST_BLOCK_TEMPLATES_VER,
			true
		);

		$license_status = false;
		if ( is_callable( 'BSF_License_Manager::bsf_is_active_license' ) ) {
			$license_status = \BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' );
		}
		$astra_theme_css = apply_filters( 'astra_dynamic_theme_css', '' );
		$astra_theme_css = str_replace( ':root', '', $astra_theme_css );
		$astra_theme_css = preg_replace( '/(?<!-)(\\bbody\\b)(?!-)/i', '', $astra_theme_css );

		$upload_dir = wp_upload_dir();
		$common_style_url = trailingslashit( $upload_dir['basedir'] ) . 'uag-plugin/custom-style-blocks.css';

		if ( ! file_exists( $common_style_url ) ) {
			$this->regenerate_spectra_css();
			$common_css_content = file_exists( $common_style_url ) ? file_get_contents( $common_style_url ) : '';
		}

		if ( empty( $common_css_content ) ) {
			$common_css_content = Sync_Library::instance()->get_server_spectra_common_css();
		}

		$ast_header = '';
		$ast_footer = '';
		$static_css_path = '';
		$astra_customizer_css = '';

		if ( defined( 'ASTRA_THEME_VERSION' ) ) {
			$astra_customizer_css = ( class_exists( 'Astra_Dynamic_CSS' ) ) ? \Astra_Dynamic_CSS::return_output( '' ) : '';
			//phpcs:disable
			// ob_start();
			// $ast_header = astra_header_markup();
			// $ast_header = ob_get_clean();

			// ob_start();
			// $ast_footer = astra_footer_markup();
			// $ast_footer = ob_get_clean();
			// $static_css_path = ASTRA_THEME_DIR . 'assets/css/minified/main.min.css';
			//phpcs:enable
			
		}

		$server_astra_customizer_css = Helper::instance()->get_block_template_customiser_css();
		if ( empty( $server_astra_customizer_css ) ) {
			Sync_Library::instance()->get_server_astra_customizer_css();
			$server_astra_customizer_css = Helper::instance()->get_block_template_customiser_css();
		}
		
		$settings = get_option( 'ast_block_templates_ai_settings', array() );
		$disable_ai = isset( $settings['disable_ai'] ) ? $settings['disable_ai'] : false;
		$adaptive_mode = isset( $settings['adaptive_mode'] ) ? $settings['adaptive_mode'] : true;
		$disable_preview = isset( $settings['disable_preview'] ) ? $settings['disable_preview'] : false;
		$remove_parameters = array( 'credit_token', 'token', 'email', 'ast_action', 'nonce' );

		$request_params = apply_filters(
			'ast_block_templates_authorization_url_param', array(
				'type' => 'token',
				'scs-authorize' => true,
				'redirect_url' => isset( $_SERVER['REQUEST_URI'] ) ? urlencode( network_home_url() . $_SERVER['REQUEST_URI'] . '&ast_action=auth&nonce=' . wp_create_nonce( 'zip_ai_auth_nonce' ) ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			)
		);
		
		$credit_request_params = array(
			'success_url' => isset( $_SERVER['REQUEST_URI'] ) ? urlencode( $this->remove_query_params( network_home_url() . $_SERVER['REQUEST_URI'], $remove_parameters ) . '&ast_action=credits' ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);

		$spec_ai_auth_url = add_query_arg( $request_params, ZIPWP_APP );
		$spec_ai_signup_url = add_query_arg( $request_params, 'https://app.zipwp.com/register/' );
		$credit_purchase_url = add_query_arg( $credit_request_params, 'https://app.zipwp.com/credits-pricing/' );
		$ai_features = get_option( 'zip_ai_modules', array() );
		$site_host = wp_parse_url( get_site_url(), PHP_URL_HOST );

		$business_details = get_option( 'zipwp_user_business_details', false );
		$business_details = ( $business_details ) ? $business_details : array();
		$business_details = array_merge( $business_details, array( 'token' => Helper::get_setting( 'auth_token', '' ) ) );

		if ( ! empty( $business_details['social_profiles'] ) ) {
			$business_details = $this->maybe_parse_social_profiles( $business_details );
		}
		$pro_url = apply_filters( 'ast_block_templates_pro_url', 'https://wpastra.com/starter-templates-plans/?utm_source=gutenberg-templates&utm_medium=dashboard&utm_campaign=Starter-Template-Backend' );

		wp_localize_script(
			'ast-block-templates',
			'ast_block_template_vars',
			apply_filters(
				'ast_block_templates_localize_vars',
				array(
					'popup_class'             => defined( 'UAGB_PLUGIN_SHORT_NAME' ) ? 'uag-block-templates-lightbox' : 'ast-block-templates-lightbox',
					'ajax_url'                => admin_url( 'admin-ajax.php' ),
					'uri'                     => AST_BLOCK_TEMPLATES_URI,
					'wpforms_status'          => $this->get_plugin_status( 'wpforms-lite/wpforms.php' ),
					'spectra_status'          => $this->get_plugin_status( 'ultimate-addons-for-gutenberg/ultimate-addons-for-gutenberg.php' ),
					'spectra_pro_status'      => $this->get_plugin_status( 'spectra-pro/spectra-pro.php' ),
					'astra_sites_pro_status'  => $this->get_plugin_status( 'astra-pro-sites/astra-pro-sites.php' ),
					'wpforms_status'          => $this->get_plugin_status( 'spectra-pro/spectra-pro.php' ),
					'astra_sites_status'          => $this->get_plugin_status( 'astra-sites/astra-sites.php' ),
					'_ajax_nonce'             => wp_create_nonce( 'ast-block-templates-ajax-nonce' ),
					'button_text'             => esc_html__( 'Design Library', 'ast-block-templates' ),
					'display_button_logo'     => true,
					'popup_logo_uri'          => AST_BLOCK_TEMPLATES_URI . 'dist/spectra-logo.svg',
					'button_logo'             => AST_BLOCK_TEMPLATES_URI . 'dist/spectra.svg',
					'st_button_logo'           => AST_BLOCK_TEMPLATES_URI . 'dist/st.svg',
					'button_class'            => '',
					'display_suggestion_link' => true,
					'suggestion_link'         => 'https://wpastra.com/sites-suggestions/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=suggestions',
					'license_status'          => $license_status,
					'isPro'                   => defined( 'ASTRA_PRO_SITES_NAME' ) ? true : false,
					'getProURL'               => esc_url( defined( 'ASTRA_PRO_SITES_NAME' ) ? ( admin_url( 'plugins.php?bsf-inline-license-form=astra-pro-sites' ) ) : $pro_url ),
					'astra_theme_css'         => isset( $astra_theme_css ) ? $astra_theme_css : '',
					'site_url'                => site_url(),
					'global-styles'           => preg_replace( '/(?<!-)(\\bbody\\b)(?!-)/i', '.st-block-container', wp_get_global_stylesheet() ),
					'spectra_common_styles'   => preg_replace( '/(?<!-)(\\bbody\\b)(?!-)/i', '.st-block-container', $common_css_content ) . ' .st-block-container .uagb-button__wrapper a { text-decoration: none; }',
					'block_color_palette'     => $this->get_block_palette_colors(),
					'page_color_palette'      => $this->get_page_palette_colors(),
					'ai_content_ajax_nonce'             => wp_create_nonce( 'ast-block-templates-ai-content' ),
					'reset_details_ajax_nonce'             => wp_create_nonce( 'ast-block-templates-reset-business-details' ),
					'business_details'  => $business_details,
					'rest_api_nonce' => wp_create_nonce( 'wp_rest' ),
					'default_ai_categories' => Helper::instance()->get_default_ai_categories(),
					'user_email' => get_option( 'admin_email' ),
					'skip_zip_ai_onboarding_nonce'             => wp_create_nonce( 'skip-spectra-pro-onboarding-nonce' ),
					'skip_zip_ai_onboarding' => get_option( 'ast_skip_zip_ai_onboarding', false ),
					'show_onboarding' => ( 'no' !== get_option( 'ast-block-templates-show-onboarding', true ) ),
					'dynamic_content' => get_option( 'ast-templates-ai-content', array() ),
					'favorites' => get_option(
						'ast_block_templates_favorites', array(
							'block' => array(),
							'page' => array(),
							'site' => array(),
						)
					),
					'astra_customizer_css' => preg_replace( '/(?<!-)(\\bbody\\b)(?!-)/i', '.st-block-container', defined( 'ASTRA_THEME_VERSION' ) ? $astra_customizer_css : $server_astra_customizer_css ),
					'disable_ai' => $disable_ai,
					'adaptive_mode' => $adaptive_mode,
					'debug_mode' => Helper::instance()->is_debug_mode() ? 'yes' : 'no',
					'disable_preview' => $disable_preview,
					'images' => AST_BLOCK_TEMPLATES_URI . 'admin-assets/images/',
					'spec_ai_auth_url' => $spec_ai_auth_url,
					'spec_ai_signup_url' => $spec_ai_signup_url,
					'open_ai_auth' => isset( $_GET['ast_action'] ) && 'auth' === sanitize_text_field( $_GET['ast_action'] ) ? true : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'credit_purchased' => isset( $_GET['ast_action'] ) && 'credits' === sanitize_text_field( $_GET['ast_action'] ) ? true : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'show_pages_onboarding' => get_option( 'ast-show-pages-onboarding', 'yes' ) === 'yes',
					'flat_rates' => array(
						'patterns_library' => 5000,
						'pages_library' => 5000,
						'patterns_category' => 500,
						'pages_category' => 1000,
					),
					'spec_credit_details'   => $this->get_spec_credit_details(),
					'get_more_credits_url' => $credit_purchase_url,
					'site_host' => $site_host,
					'sync_progress_status' => get_option( 'ast_blocks_sync_in_progress', 'no' ),
					'is_white_label' => $is_white_label,
					'white_label_name' => $this->get_white_label(),
					'is_new_user' => get_option( 'ast-block-templates-new-user', 'yes' ) === 'yes',
					'header_markup' => $ast_header,
					'footer_markup' => $ast_footer,
					'astra_static_css_path' => $static_css_path,
					'server_astra_customizer_css' => preg_replace( '/(?<!-)(\\bbody\\b)(?!-)/i', '.st-block-container', $server_astra_customizer_css ),
					'is_rtl' => is_rtl(),
					'ai_design_copilot' => isset( $ai_features['ai_design_copilot']['status'] ) ? $ai_features['ai_design_copilot']['status'] : 'disabled',
					'ai_assistant' => isset( $ai_features['ai_assistant']['status'] ) ? $ai_features['ai_assistant']['status'] : 'disabled',
					'hide_notice' => $this->is_show_personalize_ai_notice(),
					'is_sync_business_details' => get_option( 'ast-templates-business-details-synced', false ),
					'bypassAuth' => apply_filters( 'ast_block_templates_bypass_auth', false ),
				)
			)
		);
	}

	/**
	 * Get Spec Credit Details
	 *
	 * @since 2.1.24
	 * @param  array<string, mixed> $business_details business details.
	 * @return array<string, mixed>
	 */
	public function maybe_parse_social_profiles( $business_details ) {

		$social_profiles = $business_details['social_profiles'];
		$save = false;
		foreach ( $social_profiles as $index => $icon ) {

			if ( ! isset( $icon['type'] ) || empty( $icon['type'] ) ) {

				$url_parts = wp_parse_url( $icon['url'] );
				$host = isset( $url_parts['host'] ) ? $url_parts['host'] : false;

				if ( $host ) {
					$domain_parts = explode( '.', $host );
					$type = reset( $domain_parts ); 
					$social_profiles[ $index ]['type'] = strtolower( $type );
					$social_profiles[ $index ]['id'] = strtolower( $type );
				}

				$save = true;
			}       
		}

		if ( $save ) {
			$business_details['social_profiles'] = $social_profiles;
			update_option( 'zipwp_user_business_details', $business_details );
		}

		return $business_details;
	}

	/**
	 * Get is show personalize AI notice.
	 *
	 * @since 2.1.1
	 * @return array<string, bool>
	 */
	public function is_show_personalize_ai_notice() {

		return array(
			'credit_warning' => false === get_transient( 'ast_block_templates_hide_credit_warning_notice' ) ? false : true,
			'credit_danger' => false === get_transient( 'ast_block_templates_hide_credit_danger_notice' ) ? false : true,
			'personalize_ai' => false === get_transient( 'ast_block_templates_hide_personalize_ai_notice' ) ? false : true,
			'build_page_ai' => false === get_transient( 'ast_block_templates_hide_build_page_ai_notice' ) ? false : true,
		);
	}

	/**
	 * Check if white label enabled
	 *
	 * @since 2.0.0
	 *
	 * @return boolean
	 */
	public function is_white_label() {

		$is_white_label = apply_filters( 'ast_block_templates_white_label', false );
		return $is_white_label;
	}

	/**
	 * Regenerate Spectra CSS.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function regenerate_spectra_css() {

		if ( ! defined( 'UAGB_FILE' ) && ! class_exists( 'UAGB_Helper' ) ) {
			return;
		}

		$file_generation = \UAGB_Helper::allow_file_generation();

		if ( 'enabled' === $file_generation ) {

			\UAGB_Helper::delete_uag_asset_dir();
		}

		\UAGB_Admin_Helper::create_specific_stylesheet();

		/* Update the asset version */
		\UAGB_Admin_Helper::update_admin_settings_option( '__uagb_asset_version', time() );
	}

	/**
	 * Get palette colors
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function get_page_palette_colors() {

		$default_palette_color = self::$color_palette;
		// Checking the nonce already.
		if ( isset( $_REQUEST['adaptive_mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$adaptive_mode = 'true' === sanitize_text_field( $_REQUEST['adaptive_mode'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			$settings = get_option( 'ast_block_templates_ai_settings', array() );
			$adaptive_mode = isset( $settings['adaptive_mode'] ) ? $settings['adaptive_mode'] : true;
		}

		if ( class_exists( 'Astra_Global_Palette' ) && $adaptive_mode ) {
			$astra_palette_colors = astra_get_palette_colors();
			$default_palette_color = $astra_palette_colors['palettes'][ $astra_palette_colors['currentPalette'] ];
		}

		$palette_one = $default_palette_color;

		$palette_two = array(
			$default_palette_color[0],
			$default_palette_color[1],
			$default_palette_color[5],
			$default_palette_color[4],
			$default_palette_color[3],
			$default_palette_color[2],
			$default_palette_color[6],
			$default_palette_color[7],
			$default_palette_color[8],
		);

		$color_palettes = array(
			'style-1' =>
				array(
					'slug' => 'style-1',
					'title' => 'Light',
					'default_color' => $default_palette_color[4],
					'colors' => $palette_one,
				),
			'style-2' => array(
				'slug' => 'style-2',
				'title' => 'Dark',
				'default_color' => '#1E293B',
				'colors' => $palette_two,
			),
		);

		return $color_palettes;
	}

	/**
	 * Get palette colors
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function get_block_palette_colors() {

		$default_palette_color = self::$color_palette;

		// Checking the nonce already.
		if ( isset( $_REQUEST['adaptive_mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$adaptive_mode = 'true' === sanitize_text_field( $_REQUEST['adaptive_mode'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			$settings = get_option( 'ast_block_templates_ai_settings', array() );
			$adaptive_mode = isset( $settings['adaptive_mode'] ) ? $settings['adaptive_mode'] : true;
		}
		
		if ( class_exists( 'Astra_Global_Palette' ) && $adaptive_mode ) {
			$astra_palette_colors = astra_get_palette_colors();
			$default_palette_color = $astra_palette_colors['palettes'][ $astra_palette_colors['currentPalette'] ];
		}

		$palette_one = array(
			$default_palette_color[0],
			$default_palette_color[1],
			$default_palette_color[2],
			$default_palette_color[3],
			$default_palette_color[5],
			$default_palette_color[5],
			$default_palette_color[6],
			$default_palette_color[7],
			$default_palette_color[8],
		);

		$palette_two = $default_palette_color;

		$palette_three = array(
			$default_palette_color[3],
			$default_palette_color[2],
			$default_palette_color[5],
			$default_palette_color[4],
			$default_palette_color[0],
			$default_palette_color[1],
			$default_palette_color[6],
			$default_palette_color[7],
			$default_palette_color[8],
		);


		$color_palettes = array(
			'style-1' =>
				array(
					'slug' => 'style-1',
					'title' => 'Light',
					'default_color' => $default_palette_color[5],
					'colors' => $palette_one,
				),
			'style-2' => array(
				'slug' => 'style-2',
				'title' => 'Dark',
				'default_color' => $default_palette_color[4],
				'colors' => $palette_two,
			),
			'style-3' => array(
				'slug' => 'style-3',
				'title' => 'Highlight',
				'default_color' => $default_palette_color[0],
				'colors' => $palette_three,
			),
		);

		return $color_palettes;
	}

	/**
	 * Get plugin status
	 *
	 * @since 1.0.0
	 *
	 * @param  string $plugin_init_file Plguin init file.
	 * @return mixed
	 */
	public function get_plugin_status( $plugin_init_file ) {

		$installed_plugins = get_plugins();

		if ( ! isset( $installed_plugins[ $plugin_init_file ] ) ) {
			return 'not-installed';
		} elseif ( is_plugin_active( $plugin_init_file ) ) {
			return 'active';
		} else {
			return 'inactive';
		}
	}

	/**
	 * Check if white label enabled
	 *
	 * @since 2.0.0
	 *
	 * @return boolean
	 */
	public function get_white_label() {

		$is_white_label = apply_filters( 'ast_block_templates_white_label_name', '' );
		return $is_white_label;
	}

	/**
	 * Get Zip AI Credits.
	 *
	 * @since 1.0.0
	 * @return array<string, int>
	 */
	public function get_spec_credit_details() {

		return Helper::get_credit_details();
	}

	/**
	 * Get all sites
	 *
	 * @since 1.0.0
	 *
	 * @return array page builder sites.
	 */
	public function get_all_sites() {
		$total_requests = (int) Helper::instance()->get_site_request();

		$sites = array();

		if ( $total_requests ) {

			for ( $page = 1; $page <= $total_requests; $page++ ) {
				$current_page_data = Helper::instance()->get_sites_templates( $page );
				if ( ! empty( $current_page_data ) ) {
					foreach ( $current_page_data as $site_id => $site_data ) {

						$exclude_site = false;
						if ( isset( $site_data['required-plugins'] ) ) {
							foreach ( $site_data['required-plugins'] as $plugin ) {
								if ( isset( $plugin['slug'] ) && 'surecart' === $plugin['slug'] ) {
									$exclude_site = true;
									break; // Break the inner loop once 'surecart' is found.
								}
							}
						}

						if ( $exclude_site ) {
							continue; // Skip the current site if 'surecart' is found.
						}

						// Replace `astra-sites-tag` with `tag`.
						if ( isset( $site_data['astra-sites-tag'] ) ) {
							$site_data['tag'] = $site_data['astra-sites-tag'];
							unset( $site_data['astra-sites-tag'] );
						}

						// Replace `id-` from the site ID.
						$site_data['ID'] = str_replace( 'id-', '', (string) $site_id );

						if ( count( $site_data['pages'] ) ) {
							foreach ( $site_data['pages'] as $page_id => $page_data ) {

								$single_page = $page_data;

								// Replace `astra-sites-tag` with `tag`.
								if ( isset( $single_page['astra-sites-tag'] ) ) {
									$single_page['tag'] = $single_page['astra-sites-tag'];
									unset( $single_page['astra-sites-tag'] );
								}

								// Replace `id-` from the site ID.
								$single_page['ID'] = str_replace( 'id-', '', $page_id );

								$site_data['pages'][] = $single_page;

								unset( $site_data['pages'][ $page_id ] );
							}
						}

						$sites[] = $site_data;
					}
				}
			}
		}

		return $sites;
	}

	/**
	 * Get all blocks
	 *
	 * @since 1.0.0
	 * @return array All Elementor Blocks.
	 */
	public function get_all_blocks() {
		$blocks         = array();
		$blocks_pages   = array();
		$blocks_wireframe   = array();
		$total_requests = (int) Helper::instance()->get_block_templates_requests();

		for ( $page = 1; $page <= $total_requests; $page++ ) {
			$current_page_data = Helper::instance()->get_blocks_templates( $page );
			if ( ! empty( $current_page_data ) ) {
				foreach ( $current_page_data as $page_id => $page_data ) {
					$page_data['ID'] = str_replace( 'id-', '', (string) $page_id );

					if ( isset( $page_data['type'] ) && 'wireframe' === $page_data['type'] ) {
						$blocks_wireframe[] = $page_data;
					} elseif ( isset( $page_data['type'] ) && 'page' === $page_data['type'] ) {
						$blocks_pages[] = $page_data;
					} else {
						$blocks[] = $page_data;
					}
				}
			}
		}

		return array(
			'blocks' => $blocks,
			'blocks_pages' => $blocks_pages,
			'blocks_wireframe' => $blocks_wireframe,
		);
	}

	/**
	 * Download File Into Uploads Directory
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file Download File URL.
	 * @param  array  $overrides Upload file arguments.
	 * @param  int    $timeout_seconds Timeout in downloading the XML file in seconds.
	 * @return array        Downloaded file data.
	 */
	public function download_file( $file = '', $overrides = array(), $timeout_seconds = 300 ) {

		// Gives us access to the download_url() and wp_handle_sideload() functions.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// Download file to temp dir.
		$temp_file = download_url( $file, $timeout_seconds );

		// WP Error.
		if ( is_wp_error( $temp_file ) ) {
			return array(
				'success' => false,
				'data'    => $temp_file->get_error_message(),
			);
		}

		// Array based on $_FILE as seen in PHP file uploads.
		$file_args = array(
			'name'     => basename( $file ),
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$defaults = apply_filters(
			'ast_block_templates_wp_handle_sideload',
			array(

				// Tells WordPress to not look for the POST form
				// fields that would normally be present as
				// we downloaded the file from a remote server, so there
				// will be no form fields
				// Default is true.
				'test_form'   => false,

				// Setting this to false lets WordPress allow empty files, not recommended.
				// Default is true.
				'test_size'   => true,

				// A properly uploaded file will pass this test. There should be no reason to override this one.
				'test_upload' => true,

				'mimes'       => array(
					'xml'  => 'text/xml',
					'json' => 'application/json',
				),
			)
		);

		$overrides = wp_parse_args( $overrides, $defaults );

		// Move the temporary file into the uploads directory.
		$results = wp_handle_sideload( $file_args, $overrides );

		if ( isset( $results['error'] ) ) {
			return array(
				'success' => false,
				'data'    => $results,
			);
		}

		// Success.
		return array(
			'success' => true,
			'data'    => $results,
		);
	}

	/**
	 * Remove query parameters from the URL.
	 * 
	 * @param  String   $url URL.
	 * @param  String[] $params Query parameters.
	 *
	 * @return string       URL.
	 */
	public function remove_query_params( $url, $params ): string {
		$parts = wp_parse_url( $url );
		$query = array();

		if ( isset( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
		}

		foreach ( $params as $param ) {
			unset( $query[ $param ] );
		}

		$query = http_build_query( $query );

		if ( ! empty( $query ) ) {
			$query = '?' . $query;
		}

		if ( ! isset( $parts['host'] ) ) {
			return $url;
		}

		$parts['scheme'] = isset( $parts['scheme'] ) ? $parts['scheme'] : 'https';
		$parts['path']   = isset( $parts['path'] ) ? $parts['path'] : '/';
		$parts['port']   = isset( $parts['port'] ) ? ':' . $parts['port'] : '';

		return $parts['scheme'] . '://' . $parts['host'] . $parts['port'] . $parts['path'] . $query;
	}

	/**
	 * Check is valid URL
	 *
	 * @param string $url  The site URL.
	 *
	 * @since 2.1.5
	 * @return boolean
	 */
	public function is_valid_url( $url = '' ) {
		if ( empty( $url ) && null !== $url ) {
			return false;
		}

		$parse_url = wp_parse_url( $url );
		if ( empty( $parse_url ) || ! is_array( $parse_url ) || ! array_key_exists( 'host', $parse_url ) ) {
			return false;
		}

		$valid_hosts = array();

		$api_domain_parse_url = wp_parse_url( AST_BLOCK_TEMPLATES_LIBRARY_URL );
		$valid_hosts[] = $api_domain_parse_url['host'];

		// Validate host.
		if ( in_array( $parse_url['host'], $valid_hosts, true ) ) {
			return true;
		}

		return false;
	}
}
