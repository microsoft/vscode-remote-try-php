<?php
/**
 * Batch Processing
 *
 * @package ST Importer
 * @since 1.2.14
 */

namespace STImporter\Importer\Batch;

use STImporter\Importer\Batch\ST_Batch_Processing;
use STImporter\Importer\ST_Importer_File_System;
use STImporter\Importer\Helpers\ST_Image_Importer;

if ( ! class_exists( 'ST_Batch_Processing_Gutenberg' ) ) :

	/**
	 * Astra Sites Batch Processing Brizy
	 *
	 * @since 1.2.14
	 */
	class ST_Batch_Processing_Gutenberg {

		/**
		 * Instance
		 *
		 * @since 1.2.14
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.2.14
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
		 * @since 1.2.14
		 */
		public function __construct() {}

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
		 * Import
		 *
		 * @since 1.2.14
		 * @return array<string, mixed>
		 */
		public function import() {

			// Allow the SVG tags in batch update process.
			add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_tags_and_attributes' ), 10, 2 );

			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'Processing "Gutenberg" Batch Import' );
			}

			$post_types = apply_filters( 'astra_sites_gutenberg_batch_process_post_types', array( 'page', 'post', 'wp_block', 'wp_template', 'wp_navigation', 'wp_template_part', 'wp_global_styles', 'sc_form' ) );
			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'For post types: ' . implode( ', ', $post_types ) );
			}

			$post_ids = St_Batch_Processing::get_pages( $post_types );

			if ( empty( $post_ids ) && ! is_array( $post_ids ) ) {
				return array(
					'success' => false,
					'msg'     => __( 'Post ids are empty', 'st-importer', 'astra-sites' ),
				);
			}

			foreach ( $post_ids as $post_id ) {
				$this->import_single_post( $post_id );
			}

			return array(
				'success' => true,
				'msg'     => __( 'Gutenberg batch completed.', 'st-importer', 'astra-sites' ),
			);
		}

		/**
		 * Update post meta.
		 *
		 * @param  integer $post_id Post ID.
		 * @return void
		 */
		public function import_single_post( $post_id = 0 ) {

			if ( defined( 'WP_CLI' ) ) {
				\WP_CLI::line( 'Gutenberg - Processing page: ' . $post_id );
			}

			// Is page imported with Starter Sites?
			// If not then skip batch process.
			$imported_from_demo_site = get_post_meta( $post_id, '_astra_sites_enable_for_batch', true );
			if ( ! $imported_from_demo_site ) {
				return;
			}

			$is_elementor_page      = get_post_meta( $post_id, '_elementor_version', true );
			$is_beaver_builder_page = get_post_meta( $post_id, '_fl_builder_enabled', true );
			$is_brizy_page          = get_post_meta( $post_id, 'brizy_post_uid', true );

			// If page contain Elementor, Brizy or Beaver Builder meta then skip this page.
			if ( $is_elementor_page || $is_beaver_builder_page || $is_brizy_page ) {
				return;
			}

			$ids_mapping = get_option( 'astra_sites_wpforms_ids_mapping', array() );

			// Post content.
			$content = get_post_field( 'post_content', $post_id );
			// Empty mapping? Then return.
			if ( ! empty( $ids_mapping ) ) {
				// Replace ID's.
				foreach ( $ids_mapping as $old_id => $new_id ) {
					$content = str_replace( '[wpforms id=\"' . $old_id, '[wpforms id=\"' . $new_id, $content );
					$content = str_replace( '{\"formId\":\"' . $old_id . '\"}', '{\"formId\":\"' . $new_id . '\"}', $content );
				}
			}

			// This replaces the category ID in UAG Post blocks.
			$site_options = ST_Importer_File_System::get_instance()->get_demo_content();

			if ( isset( $site_options['astra-site-taxonomy-mapping'] ) ) {

				$tax_mapping = $site_options['astra-site-taxonomy-mapping'];

				if ( isset( $tax_mapping['post'] ) ) {

					$catogory_mapping = ( isset( $tax_mapping['post']['category'] ) ) ? $tax_mapping['post']['category'] : array();

					if ( is_array( $catogory_mapping ) && ! empty( $catogory_mapping ) ) {

						foreach ( $catogory_mapping as $key => $value ) {

							$this_site_term = get_term_by( 'slug', $value['slug'], 'category' );
							if ( ! is_wp_error( $this_site_term ) && $this_site_term ) {
								$content = str_replace( '"categories":"' . $value['id'], '"categories":"' . $this_site_term->term_id, $content );
								$content = str_replace( '\"categories\":\"' . $value['id'], '"categories":"' . $this_site_term->term_id, $content );
								$content = str_replace( '{"categories":[{"id":' . $value['id'], '{"categories":[{"id":' . $this_site_term->term_id, $content );
								$content = str_replace( 'categories/' . $value['id'], 'categories/' . $this_site_term->term_id, $content );
								$content = str_replace( 'categories=' . $value['id'], 'categories=' . $this_site_term->term_id, $content );
							}
						}
					}
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
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $content,
					'post_excerpt' => '',
				)
			);
		}

		/**
		 * Download and Replace hotlink images
		 *
		 * @since 2.0.0
		 *
		 * @param  string $content Mixed post content.
		 * @return array           Hotlink image array.
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
				if ( astra_sites_is_valid_image( $link ) ) {

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
					$downloaded_image = ST_Image_Importer::get_instance()->import( $image );

					// Old and New image mapping links.
					$link_mapping[ $image_url ] = $downloaded_image['url'];
				}
			}

			// Step 2: Replace the demo site URL with live site URL.
			if ( ! empty( $other_links ) ) {
				$demo_data = ST_Importer_File_System::get_instance()->get_demo_content();
				if ( isset( $demo_data['astra-site-url'] ) ) {
					$site_url = get_site_url();
					foreach ( $other_links as $key => $link ) {
						$link_mapping[ $link ] = str_replace( 'https:' . $demo_data['astra-site-url'], $site_url, $link );
					}
				}
			}

			// Step 3: Replace mapping links.
			foreach ( $link_mapping as $old_url => $new_url ) {
				if ( ! is_string( $old_url ) ) {
					continue;
				}
				$content = str_replace( $old_url, $new_url, $content );

				// Replace the slashed URLs if any exist.
				$old_url = str_replace( '/', '/\\', (string) $old_url );
				$new_url = str_replace( '/', '/\\', $new_url );
				$content = str_replace( $old_url, $new_url, $content );
			}

			return $content;
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	ST_Batch_Processing_Gutenberg::get_instance();

endif;
