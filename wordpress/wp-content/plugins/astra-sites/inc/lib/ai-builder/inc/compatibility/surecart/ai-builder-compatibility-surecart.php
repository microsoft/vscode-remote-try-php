<?php
/**
 * AI Builder Compatibility for 'SureCart'
 *
 * @see  https://wordpress.org/plugins/surecart/
 *
 * @package AI Builder
 * @since 3.3.0
 */

namespace AiBuilder\Inc\Compatibility\SureCart;

if ( ! class_exists( 'Ai_Builder_Compatibility_SureCart' ) ) :

	/**
	 * SureCart Compatibility
	 *
	 * @since 3.3.0
	 */
	class Ai_Builder_Compatibility_SureCart {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class object.
		 * @since 3.3.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 3.3.0
		 * @return object initialized object of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 3.3.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'disable_default_surecart_pages_creation' ), 2 );
			add_action( 'astra_sites_import_complete', array( $this, 'get_all_pages' ), 10 );
			add_action( 'astra_sites_after_plugin_activation', array( $this, 'activation' ), 10 );
		}

		/**
		 * Set the source to 'starter-templates' on activation.
		 *
		 * @since 1.0.15
		 * @return void
		 */
		public function activation() {
			update_option( 'surecart_source', 'starter_templates', false );
		}

		/**
		 * Retrieve all pages
		 *
		 * @since 3.3.0
		 * @return void
		 */
		public function get_all_pages() {
			if ( ! is_plugin_active( 'surecart/surecart.php' ) ) {
				return;
			}
			// Retrieve all pages.
			$pages = get_pages();
			foreach ( $pages as $page ) {
				// Get the page ID.
				$page_id      = $page->ID;
				$page_content = $page->post_content;
				$this->check_page_types_and_update_options( $page_id, $page_content );
			}
		}

		/**
		 * Check surecart page types and update options
		 *
		 * @param string $page_id    The page ID.
		 * @param string $page_content   The page content.
		 * @since 3.3.0
		 * @return void
		 */
		public function check_page_types_and_update_options( $page_id, $page_content ) {

			if ( strpos( $page_content, 'wp:surecart/checkout' ) !== false ) {
				update_option( 'surecart_checkout_page_id', $page_id );
				// Extract the sc_form id using regular expressions.
				preg_match( '/checkout-form[^"]+\\{\\\\"id\\\\":(\d+)\\}/i', $page_content, $matches );
				$number = isset( $matches[1] ) ? $matches[1] : '';
				if ( ! empty( $number ) ) {
					update_option( 'surecart_checkout_sc_form_id', $number );
				}
			} elseif ( strpos( $page_content, 'wp:surecart/dashboard' ) !== false ) {
				update_option( 'surecart_dashboard_page_id', $page_id );

			} elseif ( strpos( $page_content, 'wp:surecart/product' ) !== false ) {
				update_option( 'surecart_shop_page_id', $page_id );
			}
		}

		/**
		 * Restrict SureCart Pages Creation process
		 *
		 * Why? SureCart creates set of pages on it's activation
		 * These pages are re created via our XML import step.
		 * In order to avoid the duplicacy we restrict these page creation process.
		 *
		 * @since 3.3.0
		 * @return void
		 */
		public function disable_default_surecart_pages_creation() {
			if ( astra_sites_has_import_started() ) {
				add_filter( 'surecart/seed/all', '__return_false' );
			}
		}
	}

	/**
	 * Kicking this off by calling 'instance()' method
	 */
	Ai_Builder_Compatibility_SureCart::instance();

endif;
