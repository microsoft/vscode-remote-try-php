<?php
/**
 * Elementor Compatibility File.
 *
 * @package Astra
 */

namespace Elementor; // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

// If plugin - 'Elementor' not exist then return.
if ( ! class_exists( '\Elementor\Plugin' ) || ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) {
	return;
}

namespace ElementorPro\Modules\ThemeBuilder\ThemeSupport; // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

// @codingStandardsIgnoreStart PHPCompatibility.Keywords.NewKeywords.t_useFound
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;
// @codingStandardsIgnoreEnd PHPCompatibility.Keywords.NewKeywords.t_useFound

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Astra Elementor Compatibility
 */
if ( ! class_exists( 'Astra_Elementor_Pro' ) ) :

	/**
	 * Astra Elementor Compatibility
	 *
	 * @since 1.2.7
	 */
	class Astra_Elementor_Pro {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.2.7
		 * @return object Class object.
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
		 * @since 1.2.7
		 */
		public function __construct() {
			// Add locations.
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// Override theme templates.
			add_action( 'astra_header', array( $this, 'do_header' ), 0 );
			add_action( 'astra_footer', array( $this, 'do_footer' ), 0 );
			add_action( 'astra_template_parts_content_top', array( $this, 'do_template_parts' ), 0 );

			add_action( 'astra_entry_content_404_page', array( $this, 'do_template_part_404' ), 0 );

			add_filter( 'post_class', array( $this, 'render_post_class' ), 99 );
			// Override post meta.
			add_action( 'wp', array( $this, 'override_meta' ), 0 );

			/**
			 * Compatibility for Elementor Pro's upcoming WooCommerce widget.
			 *
			 * @since  3.7.5
			 */
			add_filter( 'astra_theme_woocommerce_dynamic_css', array( $this, 'elementor_wc_widgets_compatibility_styles' ) );

			if ( $this->is_elementor_editor() && class_exists( 'WooCommerce' ) && astra_check_elementor_pro_3_5_version() ) {
				add_action( 'init', array( $this, 'update_woocommerce_checkout' ) );
			}

			add_filter( 'astra_shop_add_to_cart_js_localize', array( $this, 'astra_shop_add_to_cart_js_localize' ), 10, 1 );
		}

		/**
		 * Append Elementor preview status.
		 *
		 * @param Array $localize_data
		 *
		 * @since 4.1.6
		 * @return Array
		 */
		public function astra_shop_add_to_cart_js_localize( $localize_data ) {
			$elementor_preview_active = false;

			if ( class_exists( 'Elementor\Plugin' ) ) {
				$elementor_preview_active = \Elementor\Plugin::$instance->preview->is_preview_mode();

			}

			$localize_data['elementor_preview_active'] = $elementor_preview_active;

			return $localize_data;
		}

		/**
		 * Check if Elementor Editor is open.
		 *
		 * @since  3.8.0
		 *
		 * @return boolean true iF Elementor Editor is loaded, false If Elementor Editor is not loaded.
		 */
		public function is_elementor_editor() {
			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}

			return false;
		}

		/**
		 * Remove actions of WooCommerce for shipping form fields, as it needs only in 'col-1'.
		 *
		 * Case: Theme's 'woocommerce_checkout' action conflicting with Elementor Pro's checkout widget. On frontend billing + shipping details wrapper comes under col-1 div because of theme's above action. But in Elementor editor, billing + shipping wrappers comes in two different cols, i.e. col-1 & col-2. Due to this, styling looks inappropriate in editor only.
		 *
		 * @since 3.8.0
		 * @return void
		 */
		public function update_woocommerce_checkout() {
			if ( ! apply_filters( 'astra_woo_shop_product_structure_override', false ) ) {
				add_action( 'woocommerce_checkout_billing', array( WC()->checkout(), 'checkout_form_shipping' ) );
			}
			remove_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ) );
		}

		/**
		 * Compatibility CSS for Elementor Pro's WooCommerce widgets releasing in their v3.6.0
		 *
		 * @param  string $css_output CSS stylesheet.
		 * @return string $css_output CSS stylesheet.
		 *
		 * @since  3.7.5
		 */
		public function elementor_wc_widgets_compatibility_styles( $css_output ) {

			if ( ! astra_check_elementor_pro_3_5_version() ) {
				return $css_output;
			}

			$woo_widgets_desktop_css = array(
				'.woocommerce.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #customer_details.col2-set, .woocommerce-page.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #customer_details.col2-set' => array(
					'width' => '100%',
				),
				'.woocommerce.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #order_review, .woocommerce.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #order_review_heading, .woocommerce-page.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #order_review, .woocommerce-page.woocommerce-checkout .elementor-widget-woocommerce-checkout-page #order_review_heading' => array(
					'width' => '100%',
					'float' => 'inherit',
				),
				'.elementor-widget-woocommerce-checkout-page .select2-container .select2-selection--single, .elementor-widget-woocommerce-cart .select2-container .select2-selection--single' => array(
					'padding' => '0',
				),
				'.elementor-widget-woocommerce-checkout-page .woocommerce form .woocommerce-additional-fields, .elementor-widget-woocommerce-checkout-page .woocommerce form .shipping_address, .elementor-widget-woocommerce-my-account .woocommerce-MyAccount-navigation-link, .elementor-widget-woocommerce-cart .woocommerce a.remove' => array(
					'border' => 'none',
				),
				'.elementor-widget-woocommerce-cart .cart-collaterals .cart_totals > h2' => array(
					'background-color' => 'inherit',
					'border-bottom'    => '0px',
					'margin'           => '0px',
				),
				'.elementor-widget-woocommerce-cart .cart-collaterals .cart_totals' => array(
					'padding'       => '0',
					'border-color'  => 'inherit',
					'border-radius' => '0',
					'margin-bottom' => '0px',
					'border-width'  => '0px',
				),
				'.elementor-widget-woocommerce-cart .woocommerce-cart-form .e-apply-coupon' => array(
					'line-height' => 'initial',
				),
				'.elementor-widget-woocommerce-my-account .woocommerce-MyAccount-content .woocommerce-Address-title h3' => array(
					'margin-bottom' => 'var(--myaccount-section-title-spacing, 0px)',
				),
				'.elementor-widget-woocommerce-my-account .woocommerce-Addresses .woocommerce-Address-title, .elementor-widget-woocommerce-my-account table.shop_table thead, .elementor-widget-woocommerce-my-account .woocommerce-page table.shop_table thead, .elementor-widget-woocommerce-cart table.shop_table thead' => array(
					'background' => 'inherit',
				),
				'.elementor-widget-woocommerce-cart .e-apply-coupon, .elementor-widget-woocommerce-cart #coupon_code, .elementor-widget-woocommerce-checkout-page .e-apply-coupon, .elementor-widget-woocommerce-checkout-page #coupon_code' => array(
					'height' => '100%',
				),
				'.elementor-widget-woocommerce-cart td.product-name dl.variation dt' => array(
					'font-weight' => 'inherit',
				),
				'.elementor-element.elementor-widget-woocommerce-checkout-page .e-checkout__container #customer_details .col-1'  => array(
					'margin-bottom' => '0',
				),
			);

			$css_output .= astra_parse_css( $woo_widgets_desktop_css );

			return $css_output;
		}

		/**
		 * Register Locations
		 *
		 * @since 1.2.7
		 * @param object $manager Location manager.
		 * @return void
		 */
		public function register_locations( $manager ) {
			$manager->register_all_core_location();
		}

		/**
		 * Template Parts Support
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_template_parts() {
			// Is Archive?
			$did_location = Module::instance()->get_locations_manager()->do_location( 'archive' );
			if ( $did_location ) {
				// Search and default.
				remove_action( 'astra_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_search' ) );// phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
				remove_action( 'astra_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_default' ) );// phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound

				// Remove pagination.
				remove_action( 'astra_pagination', 'astra_number_pagination' );
				remove_action( 'astra_entry_after', 'astra_single_post_navigation_markup' );

				// Content.
				remove_action( 'astra_entry_content_single', 'astra_entry_content_single_template' );
			}

			// IS Single?
			$did_location = Module::instance()->get_locations_manager()->do_location( 'single' );
			if ( $did_location ) {

				// @codingStandardsIgnoreStart PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
				remove_action( 'astra_page_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_page' ) );
				remove_action( 'astra_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_post' ) );
				remove_action( 'astra_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_comments' ), 15 );
				remove_action( 'astra_page_template_parts_content', array( \Astra_Loop::get_instance(), 'template_parts_comments' ), 15 );
				// @codingStandardsIgnoreEnd PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
			}
		}

		/**
		 * Override 404 page
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_template_part_404() {
			if ( is_404() ) {

				// Is Single?
				$did_location = Module::instance()->get_locations_manager()->do_location( 'single' );
				if ( $did_location ) {
					remove_action( 'astra_entry_content_404_page', 'astra_entry_content_404_page_template' );
				}
			}
		}

		/**
		 * Override sidebar, title etc with post meta
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function override_meta() {

			// don't override meta for `elementor_library` post type.
			if ( 'elementor_library' == get_post_type() ) {
				return;
			}

			// Override post meta for single pages.
			$documents_single = Module::instance()->get_conditions_manager()->get_documents_for_location( 'single' );
			if ( $documents_single ) {
				foreach ( $documents_single as $document ) {
					$this->override_with_post_meta( $document->get_post()->ID );
				}
			}

			// Override post meta for archive pages.
			$documents_archive = Module::instance()->get_conditions_manager()->get_documents_for_location( 'archive' );
			if ( $documents_archive ) {
				foreach ( $documents_archive as $document ) {
					$this->override_with_post_meta( $document->get_post()->ID );
				}
			}
		}

		/**
		 * Override sidebar, title etc with post meta
		 *
		 * @since 1.2.7
		 * @param  integer $post_id  Post ID.
		 * @return void
		 */
		public function override_with_post_meta( $post_id = 0 ) {
			// Override! Page Title.
			$title = get_post_meta( $post_id, 'site-post-title', true );
			if ( 'disabled' === $title ) {

				// Archive page.
				add_filter( 'astra_the_title_enabled', '__return_false', 99 );

				// Single page.
				add_filter( 'astra_the_title_enabled', '__return_false' );
				remove_action( 'astra_archive_header', 'astra_archive_page_info' );
			}

			// Override! Sidebar.
			$sidebar = get_post_meta( $post_id, 'site-sidebar-layout', true );
			if ( '' === $sidebar ) {
				$sidebar = 'default';
			}

			// @codingStandardsIgnoreStart PHPCompatibility.FunctionDeclarations.NewClosure.Found

			if ( 'default' !== $sidebar ) {
				add_filter(
					'astra_page_layout',
					function( $page_layout ) use ( $sidebar ) {
						return $sidebar;
					}
				);
			}

			// Override! Content Layout.
			$content_layout = get_post_meta( $post_id, 'site-content-layout', true );
			if ( '' === $content_layout ) {
				$content_layout = 'default';
			}

			if ( 'default' !== $content_layout ) {
				add_filter(
					'astra_get_content_layout',
					function( $layout ) use ( $content_layout ) {
						return $content_layout;
					}
				);
			}

			// Override! Footer Bar.
			$footer_layout = get_post_meta( $post_id, 'footer-sml-layout', true );
			if ( '' === $footer_layout ) {
				$footer_layout = 'default';
			}

			if ( 'disabled' === $footer_layout ) {
				add_filter(
					'astra_footer_sml_layout',
					function( $is_footer ) {
						return 'disabled';
					}
				);
			}

			// Override! Footer Widgets.
			$footer_widgets = get_post_meta( $post_id, 'footer-adv-display', true );
			if ( '' === $footer_widgets ) {
				$footer_widgets = 'default';
			}

			if ( 'disabled' === $footer_widgets ) {
				add_filter(
					'astra_advanced_footer_disable',
					function() {
						return true;
					}
				);
			}

			// Override! Header.
			$main_header_display = get_post_meta( $post_id, 'ast-main-header-display', true );
			if ( '' === $main_header_display ) {
				$main_header_display = 'default';
			}

			if ( 'disabled' === $main_header_display ) {
				remove_action( 'astra_masthead', 'astra_masthead_primary_template' );
				add_filter(
					'astra_main_header_display',
					function( $display_header ) {
						return 'disabled';
					}
				);
			}
			// @codingStandardsIgnoreEnd PHPCompatibility.FunctionDeclarations.NewClosure.Found
		}

		/**
		 * Header Support
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_header() {
			$did_location = Module::instance()->get_locations_manager()->do_location( 'header' );
			if ( $did_location ) {
				remove_action( 'astra_header', 'astra_header_markup' );
				if ( true === \Astra_Builder_Helper::$is_header_footer_builder_active ) { // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
					remove_action( 'astra_header', array( \Astra_Builder_Header::get_instance(), 'header_builder_markup' ) ); // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
				}
			}
		}

		/**
		 * Footer Support
		 *
		 * @since 1.2.7
		 * @return void
		 */
		public function do_footer() {
			$did_location = Module::instance()->get_locations_manager()->do_location( 'footer' );
			if ( $did_location ) {
				remove_action( 'astra_footer', 'astra_footer_markup' );
				if ( true === \Astra_Builder_Helper::$is_header_footer_builder_active ) { // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
					remove_action( 'astra_footer', array( \Astra_Builder_Footer::get_instance(), 'footer_markup' ) ); // phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
				}
			}
		}

		/**
		 * Remove theme post's default classes when Elementor's template builder is activated.
		 *
		 * @param  array $classes Post Classes.
		 * @return array
		 * @since  1.4.9
		 */
		public function render_post_class( $classes ) {
			$post_class = array( 'elementor-post elementor-grid-item', 'elementor-portfolio-item' );
			$result     = array_intersect( $classes, $post_class );

			if ( count( $result ) > 0 ) {
				$classes = array_diff(
					$classes,
					array(
						// Astra common grid.
						'ast-col-xs-1',
						'ast-col-xs-2',
						'ast-col-xs-3',
						'ast-col-xs-4',
						'ast-col-xs-5',
						'ast-col-xs-6',
						'ast-col-xs-7',
						'ast-col-xs-8',
						'ast-col-xs-9',
						'ast-col-xs-10',
						'ast-col-xs-11',
						'ast-col-xs-12',
						'ast-col-sm-1',
						'ast-col-sm-2',
						'ast-col-sm-3',
						'ast-col-sm-4',
						'ast-col-sm-5',
						'ast-col-sm-6',
						'ast-col-sm-7',
						'ast-col-sm-8',
						'ast-col-sm-9',
						'ast-col-sm-10',
						'ast-col-sm-11',
						'ast-col-sm-12',
						'ast-col-md-1',
						'ast-col-md-2',
						'ast-col-md-3',
						'ast-col-md-4',
						'ast-col-md-5',
						'ast-col-md-6',
						'ast-col-md-7',
						'ast-col-md-8',
						'ast-col-md-9',
						'ast-col-md-10',
						'ast-col-md-11',
						'ast-col-md-12',
						'ast-col-lg-1',
						'ast-col-lg-2',
						'ast-col-lg-3',
						'ast-col-lg-4',
						'ast-col-lg-5',
						'ast-col-lg-6',
						'ast-col-lg-7',
						'ast-col-lg-8',
						'ast-col-lg-9',
						'ast-col-lg-10',
						'ast-col-lg-11',
						'ast-col-lg-12',
						'ast-col-xl-1',
						'ast-col-xl-2',
						'ast-col-xl-3',
						'ast-col-xl-4',
						'ast-col-xl-5',
						'ast-col-xl-6',
						'ast-col-xl-7',
						'ast-col-xl-8',
						'ast-col-xl-9',
						'ast-col-xl-10',
						'ast-col-xl-11',
						'ast-col-xl-12',

						// Astra Blog / Single Post.
						'ast-article-post',
						'ast-article-single',
						'ast-separate-posts',
						'remove-featured-img-padding',
						'ast-featured-post',

						// Astra Woocommerce.
						'ast-product-gallery-layout-vertical',
						'ast-product-gallery-layout-horizontal',
						'ast-product-gallery-with-no-image',

						'ast-product-tabs-layout-vertical',
						'ast-product-tabs-layout-horizontal',

						'ast-qv-disabled',
						'ast-qv-on-image',
						'ast-qv-on-image-click',
						'ast-qv-after-summary',

						'astra-woo-hover-swap',

						'box-shadow-0',
						'box-shadow-0-hover',
						'box-shadow-1',
						'box-shadow-1-hover',
						'box-shadow-2',
						'box-shadow-2-hover',
						'box-shadow-3',
						'box-shadow-3-hover',
						'box-shadow-4',
						'box-shadow-4-hover',
						'box-shadow-5',
						'box-shadow-5-hover',
					)
				);
			}

			return $classes;
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Elementor_Pro::get_instance();

endif;
