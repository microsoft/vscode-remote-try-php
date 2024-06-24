<?php
/**
 * Batch Processing
 *
 * @package Astra Sites
 * @since 1.0.14
 */

use STImporter\Importer\Helpers\ST_Image_Importer;

if ( ! class_exists( 'Astra_Sites_Batch_Processing_Widgets' ) ) :

	/**
	 * Astra_Sites_Batch_Processing_Widgets
	 *
	 * @since 1.0.14
	 */
	class Astra_Sites_Batch_Processing_Widgets {

		/**
		 * WP Forms.
		 *
		 * @since 2.6.22
		 * @var object Class object.
		 */
		public $wpforms_ids_mapping;

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
				WP_CLI::line( 'Importing Widgets Data' );
			}

			// Catch mapping data.
			$this->wpforms_ids_mapping = get_option( 'astra_sites_wpforms_ids_mapping', array() );

			// Process image widget data.
			$this->widget_media_image();

			// Process text widget data.
			$this->widget_text();

			// Process WP Forms widget data.
			$this->widget_wpform();
		}

		/**
		 * Widget WP Forms
		 *
		 * @since 3.1.3
		 * @return void
		 */
		public function widget_wpform() {

			$data = get_option( 'widget_wpforms-widget', null );

			if ( empty( $data ) ) {
				return;
			}

			Astra_Sites_Importer_Log::add( '---- Processing Contact Form Mapping from WP Forms Widgets -----' );

			foreach ( $data as $key => $value ) {

				if ( isset( $value['form_id'] ) && ! empty( $value['form_id'] ) ) {

					$content = $value['form_id'];

					// Empty mapping? Then return.
					if ( ! empty( $this->wpforms_ids_mapping ) ) {
						// Replace ID's.
						foreach ( $this->wpforms_ids_mapping as $old_id => $new_id ) {
							if ( $old_id === $content ) {
								$content = $new_id;
							}
						}
					}

					$data[ $key ]['form_id'] = $content;

					if ( defined( 'WP_CLI' ) ) {
						WP_CLI::line( 'Updating Contact Form Mapping from WP Forms Widgets' );
					}
				}
			}

			update_option( 'widget_wpforms-widget', $data );
		}

		/**
		 * Widget Text
		 *
		 * @since 2.6.22
		 * @return void
		 */
		public function widget_text() {

			$data = get_option( 'widget_text', null );

			if ( empty( $data ) ) {
				return;
			}

			Astra_Sites_Importer_Log::add( '---- Processing Contact Form Mapping from Text Widgets -----' );

			foreach ( $data as $key => $value ) {

				if ( isset( $value['text'] ) && ! empty( $value['text'] ) ) {

					$content = $value['text'];

					// Empty mapping? Then return.
					if ( ! empty( $this->wpforms_ids_mapping ) ) {
						// Replace ID's.
						foreach ( $this->wpforms_ids_mapping as $old_id => $new_id ) {
							$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
							$content = str_replace( '{"formId":"' . $old_id . '"}', '{"formId":"' . $new_id . '"}', $content );
						}
					}

					$data[ $key ]['text'] = $content;

					if ( defined( 'WP_CLI' ) ) {
						WP_CLI::line( 'Updating Contact Form Mapping from Text Widgets' );
					}
				}
			}

			update_option( 'widget_text', $data );
		}

		/**
		 * Widget Media Image
		 *
		 * @since 1.0.14
		 * @return void
		 */
		public function widget_media_image() {

			$data = get_option( 'widget_media_image', null );

			if ( empty( $data ) ) {
				return;
			}

			Astra_Sites_Importer_Log::add( '---- Processing Images from Widgets -----' );

			foreach ( $data as $key => $value ) {

				if (
					isset( $value['url'] ) &&
					isset( $value['attachment_id'] )
				) {

					$image = array(
						'url' => $value['url'],
						'id'  => $value['attachment_id'],
					);

					$downloaded_image = ST_Image_Importer::get_instance()->import( $image );

					$data[ $key ]['url']           = $downloaded_image['url'];
					$data[ $key ]['attachment_id'] = $downloaded_image['id'];

					if ( defined( 'WP_CLI' ) ) {
						WP_CLI::line( 'Importing Widgets Image: ' . $value['url'] . ' | New Image ' . $downloaded_image['url'] );
					}
				}
			}

			update_option( 'widget_media_image', $data );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Batch_Processing_Widgets::get_instance();

endif;
