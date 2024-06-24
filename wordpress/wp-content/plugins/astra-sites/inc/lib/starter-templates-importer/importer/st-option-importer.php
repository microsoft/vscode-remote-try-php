<?php
/**
 * Starter Templates Options Importer - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package Starter Templates Importer
 */

namespace STImporter\Importer;

use STImporter\Importer\ST_Importer_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Module Class.
 */
class ST_Option_Importer {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Images IDs
	 *
	 * @var array   The Array of already image IDs.
	 * @since 1.0.0
	 */
	private static $already_imported_ids = array();

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
	 * Site Options
	 *
	 * @since 1.0.0
	 *
	 * @return array    List of defined array.
	 */
	public static function site_options() {
		return apply_filters(
			'st_importer_site_options',
			array(
				'custom_logo',
				'nav_menu_locations',
				'show_on_front',
				'page_on_front',
				'page_for_posts',
				'site_title',

				// Astra Theme Global Color Palette and Typography Preset options.
				'astra-color-palettes',
				'astra-typography-presets',
			)
		);
	}


	/**
	 * Get post from post title and post type.
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed  $post_title  post title.
	 * @param  string $post_type post type.
	 * @return mixed
	 */
	public static function get_page_by_title( $post_title, $post_type ) {
		$page  = array();
		$query = new \WP_Query(
			array(
				'post_type'              => $post_type,
				'title'                  => $post_title,
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'ID',
				'order'                  => 'DESC',
			)
		);
		if ( $query->have_posts() ) {
			$page = $query->posts[0];
		}
		return $page;
	}

	/**
	 * Update post option
	 *
	 * @since 1.0.0
	 *
	 * @param  string $option_name  Option name.
	 * @param  mixed  $option_value Option value.
	 * @return void
	 */
	public static function update_page_id_by_option_value( $option_name, $option_value ) {
		if ( empty( $option_value ) ) {
			return;
		}

		$page = self::get_page_by_title( $option_value, 'page' );

		if ( is_object( $page ) ) {
			update_option( $option_name, $page->ID );
		}
	}

	/**
	 * In WP nav menu is stored as ( 'menu_location' => 'menu_id' );
	 * In export we send 'menu_slug' like ( 'menu_location' => 'menu_slug' );
	 * In import we set 'menu_id' from menu slug like ( 'menu_location' => 'menu_id' );
	 *
	 * @since 1.0.0
	 * @param array $nav_menu_locations Array of nav menu locations.
	 */
	public static function set_nav_menu_locations( $nav_menu_locations = array() ) {

		$menu_locations = array();

		// Update menu locations.
		if ( isset( $nav_menu_locations ) ) {

			foreach ( $nav_menu_locations as $menu => $value ) {

				$term = get_term_by( 'slug', $value, 'nav_menu' );

				if ( is_object( $term ) ) {
					$menu_locations[ $menu ] = $term->term_id;
				}
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}
	}

	/**
	 * Insert Logo By URL
	 *
	 * @since 1.0.0
	 * @param  string $image_url Logo URL.
	 * @return void
	 */
	public static function insert_logo( $image_url = '' ) {

		$downloaded_image = self::import(
			array(
				'url' => $image_url,
				'id'  => 0,
			)
		);

		if ( $downloaded_image['id'] ) {
			ST_Importer_Helper::track_post( $downloaded_image['id'] );
			set_theme_mod( 'custom_logo', $downloaded_image['id'] );
		}

	}

	/**
	 * Import Image
	 *
	 * @since 1.0.0
	 * @param  array $attachment Attachment array.
	 * @return array              Attachment array.
	 *
	 * @throws \Exception Exception that is catched.
	 */
	public static function import( $attachment ) {

		if ( isset( $attachment['url'] ) && ! self::is_valid_image_url( $attachment['url'] ) ) {
			return $attachment;
		}

		$saved_image = self::get_saved_image( $attachment );

		if ( $saved_image['status'] ) {
			return $saved_image['attachment'];
		}

		$file_content = wp_remote_retrieve_body(
			wp_safe_remote_get(
				$attachment['url'],
				array(
					'timeout'   => '60',
					'sslverify' => false,
				)
			)
		);

		// Empty file content?
		if ( empty( $file_content ) ) {
			return $attachment;
		}

		// Extract the file name and extension from the URL.
		$filename = basename( $attachment['url'] );

		$upload = wp_upload_bits( $filename, null, $file_content );

		$post = array(
			'post_title' => $filename,
			'guid'       => $upload['url'],
		);

		$info = wp_check_filetype( $upload['file'] );
		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			// For now just return the origin attachment.
			return $attachment;
		}

		$post_id = wp_insert_attachment( $post, $upload['file'] );
		try {

			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				include ABSPATH . 'wp-admin/includes/image.php';
			}

			wp_update_attachment_metadata(
				$post_id,
				wp_generate_attachment_metadata( $post_id, $upload['file'] )
			);
		} catch ( \Exception $e ) {
			throw $e;
		}

		update_post_meta( $post_id, '_astra_sites_image_hash', ST_Importer_Helper::get_hash_image( $attachment['url'] ) );
		ST_Importer_Helper::track_post( $post_id );

		$new_attachment = array(
			'id'  => $post_id,
			'url' => $upload['url'],
		);

		self::$already_imported_ids[] = $post_id;

		return $new_attachment;
	}


	/**
	 * Get Saved Image.
	 *
	 * @since 1.0.0
	 * @param  string $attachment   Attachment Data.
	 * @return string                 Hash string.
	 */
	public static function get_saved_image( $attachment ) {

		if ( apply_filters( 'astra_sites_image_importer_skip_image', false, $attachment ) ) {
			return array(
				'status'     => true,
				'attachment' => $attachment,
			);
		}

		global $wpdb;

		// 1. Is already imported in Batch Import Process?
		$post_id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We are checking if this image is already processed. WO_Query would have been overkill.
			$wpdb->prepare(
				'SELECT `post_id` FROM `' . $wpdb->postmeta . '`
                    WHERE `meta_key` = \'_astra_sites_image_hash\'
                        AND `meta_value` = %s
                ;',
				ST_Importer_Helper::get_hash_image( $attachment['url'] )
			)
		);

		// 2. Is image already imported though XML?
		if ( empty( $post_id ) ) {

			// Get file name without extension.
			// To check it exist in attachment.
			$filename = basename( $attachment['url'] );

			// Find the attachment by meta value.
			// Code reused from Elementor plugin.
			$post_id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We are checking if this attachment is already processed. WO_Query would have been overkill.
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta}
                    WHERE meta_key = '_wp_attached_file'
                    AND meta_value LIKE %s",
					'%/' . $filename . '%'
				)
			);
		}

		if ( $post_id ) {
			$new_attachment               = array(
				'id'  => $post_id,
				'url' => wp_get_attachment_url( $post_id ),
			);
			self::$already_imported_ids[] = $post_id;

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
	 * Check if valid image url.
	 *
	 * @since 1.0.0
	 * @param  string $link image URL.
	 * @return bool
	 */
	public static function is_valid_image_url( $link = '' ) {
		return preg_match( '/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-\@]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $link );
	}

}
