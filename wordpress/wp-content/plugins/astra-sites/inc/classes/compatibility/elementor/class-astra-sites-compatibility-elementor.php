<?php
/**
 * Astra Sites Compatibility for 'Elementor'
 *
 * @package Astra Sites
 * @since 2.0.0
 */

namespace AstraSites\Elementor;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

if ( ! class_exists( 'Astra_Sites_Compatibility_Elementor' ) ) :

	/**
	 * Elementor Compatibility
	 *
	 * @since 2.0.0
	 */
	class Astra_Sites_Compatibility_Elementor {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class object.
		 * @since 2.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		public function __construct() {

			/**
			 * Add Slashes
			 *
			 * @todo    Elementor already have below code which works on defining the constant `WP_LOAD_IMPORTERS`.
			 *          After defining the constant `WP_LOAD_IMPORTERS` in WP CLI it was not works.
			 *          Try to remove below duplicate code in future.
			 */
			if ( ! wp_doing_ajax() || ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) ) {
				remove_filter( 'wp_import_post_meta', array( 'Elementor\Compatibility', 'on_wp_import_post_meta' ) );
				remove_filter( 'wxr_importer.pre_process.post_meta', array( 'Elementor\Compatibility', 'on_wxr_importer_pre_process_post_meta' ) );

				add_filter( 'wp_import_post_meta', array( $this, 'on_wp_import_post_meta' ) );
				add_filter( 'wxr_importer.pre_process.post_meta', array( $this, 'on_wxr_importer_pre_process_post_meta' ) );
			}

			add_action( 'astra_sites_before_delete_imported_posts', array( $this, 'force_delete_kit' ), 10, 2 );
			add_action( 'astra_sites_before_sse_import', array( $this, 'disable_attachment_metadata' ) );

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'astra_sites_after_plugin_activation', array( $this, 'disable_elementor_redirect' ) );
		}

		/**
		 * Disable Elementor redirect.
		 *
		 * @return void.
		 */
		public function disable_elementor_redirect() {
			$elementor_redirect = get_transient( 'elementor_activation_redirect' );

			if ( ! empty( $elementor_redirect ) && '' !== $elementor_redirect ) {
				delete_transient( 'elementor_activation_redirect' );
			}
		}

		/**
		 * Remove the transient update check for plugins callback from Elementor.
		 * This reduces the extra code execution for Elementor.
		 */
		public function init() {
			if ( astra_sites_has_import_started() && null !== \Elementor\Plugin::$instance->admin ) {
				remove_filter( 'pre_set_site_transient_update_plugins', array( \Elementor\Plugin::$instance->admin->get_component( 'canary-deployment' ), 'check_version' ) );
			}
		}

		/**
		 * Disable the attachment metadata
		 */
		public function disable_attachment_metadata() {
			remove_filter(
				'wp_update_attachment_metadata', array(
					\Elementor\Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' ),
					'set_svg_meta_data',
				), 10, 2
			);
		}


		/**
		 * Force Delete Elementor Kit
		 *
		 * Delete the previously imported Elementor kit.
		 *
		 * @param int    $post_id     Post name.
		 * @param string $post_type   Post type.
		 */
		public function force_delete_kit( $post_id = 0, $post_type = '' ) {

			if ( ! $post_id ) {
				return;
			}

			if ( 'elementor_library' === $post_type ) {
				$_GET['force_delete_kit'] = true;
			}
		}

		/**
		 * Process post meta before WP importer.
		 *
		 * Normalize Elementor post meta on import, We need the `wp_slash` in order
		 * to avoid the unslashing during the `add_post_meta`.
		 *
		 * Fired by `wp_import_post_meta` filter.
		 *
		 * @since 1.4.3
		 * @access public
		 *
		 * @param array $post_meta Post meta.
		 *
		 * @return array Updated post meta.
		 */
		public function on_wp_import_post_meta( $post_meta ) {
			foreach ( $post_meta as &$meta ) {
				if ( '_elementor_data' === $meta['key'] ) {
					$meta['value'] = wp_slash( $meta['value'] );
					break;
				}
			}

			return $post_meta;
		}

		/**
		 * Process post meta before WXR importer.
		 *
		 * Normalize Elementor post meta on import with the new WP_importer, We need
		 * the `wp_slash` in order to avoid the unslashing during the `add_post_meta`.
		 *
		 * Fired by `wxr_importer.pre_process.post_meta` filter.
		 *
		 * @since 1.4.3
		 * @access public
		 *
		 * @param array $post_meta Post meta.
		 *
		 * @return array Updated post meta.
		 */
		public function on_wxr_importer_pre_process_post_meta( $post_meta ) {
			if ( '_elementor_data' === $post_meta['key'] ) {
				$post_meta['value'] = wp_slash( $post_meta['value'] );
			}

			return $post_meta;
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Compatibility_Elementor::get_instance();

endif;
