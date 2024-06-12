<?php
/**
 * Batch Processing
 *
 * @package Astra Sites
 * @since 1.0.14
 */

use STImporter\Importer\Helpers\ST_Image_Importer;

if ( ! class_exists( 'Astra_Sites_Batch_Processing_Beaver_Builder' ) ) :

	/**
	 * Astra_Sites_Batch_Processing_Beaver_Builder
	 *
	 * @since 1.0.14
	 */
	class Astra_Sites_Batch_Processing_Beaver_Builder {

		/**
		 * Instance
		 *
		 * @since 1.0.14
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.14
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
		 * @since 1.0.14
		 */
		public function __construct() {
		}

		/**
		 * Import
		 *
		 * @since 1.0.14
		 * @return void
		 */
		public function import() {

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Processing "Beaver Builder" Batch Import' );
			}

			Astra_Sites_Importer_Log::add( '---- Processing WordPress Posts / Pages - for Beaver Builder ----' );

			if ( ! is_callable( 'FLBuilderModel::get_post_types' ) ) {
				return;
			}

			$post_types = FLBuilderModel::get_post_types( 'post-types' );
			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'For post types: ' . implode( ', ', $post_types ) );
			}
			if ( empty( $post_types ) && ! is_array( $post_types ) ) {
				return;
			}

			$post_ids = Astra_Sites_Batch_Processing::get_pages( $post_types );
			if ( empty( $post_ids ) && ! is_array( $post_ids ) ) {
				return;
			}

			foreach ( $post_ids as $post_id ) {
				$this->import_single_post( $post_id );
			}
		}

		/**
		 * Update post meta.
		 *
		 * @param  integer $post_id Post ID.
		 * @return void
		 */
		public function import_single_post( $post_id = 0 ) {

			$is_bb_post = get_post_meta( $post_id, '_fl_builder_enabled', true );
			if ( ! $is_bb_post ) {
				return;
			}

			// Is page imported with Starter Sites?
			// If not then skip batch process.
			$imported_from_demo_site = get_post_meta( $post_id, '_astra_sites_enable_for_batch', true );
			if ( ! $imported_from_demo_site ) {
				return;
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Beaver Builder - Processing page: ' . $post_id );
			}
			Astra_Sites_Importer_Log::add( '---- Processing WordPress Page - for Beaver Builder ---- "' . $post_id . '"' );

			if ( ! empty( $post_id ) ) {

				// Get page builder data.
				$data = get_post_meta( $post_id, '_fl_builder_data', true );

				if ( ! empty( $data ) ) {
					foreach ( $data as $key => $el ) {

						// Update 'row' images.
						if ( 'row' === $el->type ) {
							$data[ $key ]->settings = self::update_row( $el->settings );
						}

						// Update 'module' images.
						if ( 'module' === $el->type ) {
							$data[ $key ]->settings = self::update_module( $el->settings );
						}

						// Update 'column' images.
						if ( 'column' === $el->type ) {
							$data[ $key ]->settings = self::update_column( $el->settings );
						}
					}

					// Update page builder data.
					update_post_meta( $post_id, '_fl_builder_data', $data );
					update_post_meta( $post_id, '_fl_builder_draft', $data );

					// Clear all cache.
					FLBuilderModel::delete_asset_cache_for_all_posts();
				}

				// Clean the post excerpt.
				astra_sites_empty_post_excerpt( $post_id );

			}

		}

		/**
		 * Import Module Images.
		 *
		 * @param  object $settings Module settings object.
		 * @return object
		 */
		public static function update_module( $settings ) {

			// 1) Set photos.
			$settings = self::import_photo( $settings );

			/**
			 * 2) Set `$settings->data` for Only type 'image-icon'
			 *
			 * @todo Remove the condition `'image-icon' === $settings->type` if `$settings->data` is used only for the Image Icon.
			 */
			if (
				isset( $settings->data ) &&
				isset( $settings->photo ) && ! empty( $settings->photo ) &&
				'image-icon' === $settings->type
			) {
				$settings->data = FLBuilderPhoto::get_attachment_data( $settings->photo );
			}

			if ( 'uabb-wp-forms-styler' === $settings->type ) {
				astra_sites_error_log( '--------WP Form Styler ID replacement start-------' );
				$ids_mapping = get_option( 'astra_sites_wpforms_ids_mapping', array() );
				if ( $ids_mapping ) {
					// Update WP form IDs.
					foreach ( $ids_mapping as $old_id => $new_id ) {
						if ( isset( $settings->wp_form_id ) && $old_id === $settings->wp_form_id ) {
							astra_sites_error_log( '--------WP Form Styler ID ' . $old_id . ' replaced to ' . $new_id . '-------' );
							$settings->wp_form_id = $new_id;
						}
					}
				}
				astra_sites_error_log( '--------WP Form Styler ID replacement done-------' );
			}

			// 3) Set `list item` module images.
			if ( isset( $settings->add_list_item ) ) {
				foreach ( $settings->add_list_item as $key => $value ) {
					$settings->add_list_item[ $key ] = self::import_photo( $value );
				}
			}

			// 4) Set `list item` module images.
			if ( isset( $settings->text ) ) {
				$settings->text = self::get_wpforms_mapping( $settings->text );
			} elseif ( isset( $settings->html ) ) {
				$settings->html = self::get_wpforms_mapping( $settings->html );
			}

			return $settings;
		}

		/**
		 * Replace WP Forms shortcode.
		 *
		 * @since 2.0.0
		 * @param  string $content Content.
		 * @return string          Content.
		 */
		private static function get_wpforms_mapping( $content = '' ) {
			$ids_mapping = get_option( 'astra_sites_wpforms_ids_mapping', array() );
			astra_sites_error_log( wp_json_encode( $ids_mapping ) );
			if ( $ids_mapping ) {
				// Update WP form IDs.
				foreach ( $ids_mapping as $old_id => $new_id ) {
					$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
				}
			}

			return $content;
		}

		/**
		 * Import Column Images.
		 *
		 * @param  object $settings Column settings object.
		 * @return object
		 */
		public static function update_column( $settings ) {

			// 1) Set BG Images.
			$settings = self::import_bg_image( $settings );

			return $settings;
		}

		/**
		 * Import Row Images.
		 *
		 * @param  object $settings Row settings object.
		 * @return object
		 */
		public static function update_row( $settings ) {

			// 1) Set BG Images.
			$settings = self::import_bg_image( $settings );

			return $settings;
		}

		/**
		 * Helper: Import BG Images.
		 *
		 * @param  object $settings Row settings object.
		 * @return object
		 */
		public static function import_bg_image( $settings ) {

			if (
				( ! empty( $settings->bg_image ) && ! empty( $settings->bg_image_src ) )
			) {
				$image = array(
					'url' => $settings->bg_image_src,
					'id'  => $settings->bg_image,
				);

				$downloaded_image = ST_Image_Importer::get_instance()->import( $image );

				$settings->bg_image_src = $downloaded_image['url'];
				$settings->bg_image     = $downloaded_image['id'];
			}

			return $settings;
		}

		/**
		 * Helper: Import Photo.
		 *
		 * @param  object $settings Row settings object.
		 * @return object
		 */
		public static function import_photo( $settings ) {

			if ( ! empty( $settings->photo ) && ! empty( $settings->photo_src ) ) {

				$image = array(
					'url' => $settings->photo_src,
					'id'  => $settings->photo,
				);

				$downloaded_image = ST_Image_Importer::get_instance()->import( $image );

				$settings->photo_src = $downloaded_image['url'];
				$settings->photo     = $downloaded_image['id'];
			}

			return $settings;
		}


	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Batch_Processing_Beaver_Builder::get_instance();

endif;
