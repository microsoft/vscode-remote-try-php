<?php
/**
 * Elementor Compatibility File.
 *
 * @package Astra
 */

namespace Elementor;// phpcs:ignore PHPCompatibility.Keywords.NewKeywords.t_namespaceFound, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

// @codingStandardsIgnoreStart PHPCompatibility.Keywords.NewKeywords.t_useFound
use Astra_Global_Palette;
use Astra_Dynamic_CSS;
use Elementor\Core\Settings\Manager as SettingsManager;
// @codingStandardsIgnoreEnd PHPCompatibility.Keywords.NewKeywords.t_useFound

// If plugin - 'Elementor' not exist then return.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

/**
 * Astra Elementor Compatibility
 */
if ( ! class_exists( 'Astra_Elementor' ) ) :

	/**
	 * Astra Elementor Compatibility
	 *
	 * @since 1.0.0
	 */
	class Astra_Elementor {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'elementor_default_setting' ), 20 );
			add_action( 'elementor/preview/init', array( $this, 'elementor_default_setting' ) );
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'elementor_overlay_zindex' ) );
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'elementor_add_scripts' ) );

			/**
			 * Compatibility for Elementor Headings after Elementor-v2.9.9.
			 *
			 * @since  2.4.5
			 */
			add_filter( 'astra_dynamic_theme_css', array( $this, 'enqueue_elementor_compatibility_styles' ) );

			add_action( 'rest_request_after_callbacks', array( $this, 'elementor_add_theme_colors' ), 999, 3 );
			add_filter( 'rest_request_after_callbacks', array( $this, 'display_global_colors_front_end' ), 999, 3 );
			add_filter( 'astra_dynamic_theme_css', array( $this, 'generate_global_elementor_style' ), 11 );

			/**
			 * Compatibility for Elementor title disable from editor and elementor builder.
			 *
			 * @since  4.1.0
			 */
			add_filter( 'astra_entry_header_class', array( $this, 'astra_entry_header_class_custom' ), 1, 99 );
		}


		/**
		 * Astra post layout 2 disable compatibility.
		 *
		 * @param array $classes Array of elementor edit mode check.
		 *
		 * @since 4.1.0
		 */
		function astra_entry_header_class_custom( $classes ) {
			$edit_mode         = get_post_meta( astra_get_post_id(), '_elementor_edit_mode', true );
			$astra_layout_type = astra_get_option( 'ast-dynamic-single-' . get_post_type() . '-layout', 'layout-1' );

			if ( ( $edit_mode && $edit_mode === 'builder' ) || ( $edit_mode === 'builder' && $astra_layout_type === 'layout-2' ) ) {
				$classes[] = 'ast-header-without-markup';
				/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				if ( $astra_layout_type === 'layout-2' && in_array( 'ast-header-without-markup', $classes ) ) {
					unset( $classes[ array_search( 'ast-header-without-markup', $classes ) ] );
				}
			}

			return $classes;
		}

		/**
		 * Compatibility CSS for Elementor Headings after Elementor-v2.9.9
		 *
		 * In v2.9.9 Elementor has removed [ .elementor-widget-heading .elementor-heading-title { margin: 0 } ] this CSS.
		 * Again in v2.9.10 Elementor added this as .elementor-heading-title { margin: 0 } but still our [ .entry-content heading { margin-bottom: 20px } ] CSS overrding their fix.
		 *
		 * That's why adding this CSS fix to headings by setting bottom-margin to 0.
		 *
		 * @param  string $dynamic_css Astra Dynamic CSS.
		 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
		 * @return string $dynamic_css Generated CSS.
		 *
		 * @since  2.4.5
		 */
		public function enqueue_elementor_compatibility_styles( $dynamic_css, $dynamic_css_filtered = '' ) {

			global $post;
			$id = astra_get_post_id();

			if ( $this->is_elementor_activated( $id ) ) {

				$elementor_heading_margin_comp = array(
					'.elementor-widget-heading .elementor-heading-title' => array(
						'margin' => '0',
					),
					'.elementor-page .ast-menu-toggle' => array(
						'color'      => 'unset !important',
						'background' => 'unset !important',
					),
				);

				/* Parse CSS from array() */
				$parse_css = astra_parse_css( $elementor_heading_margin_comp );

				$elementor_base_css = array(
					'.elementor-post.elementor-grid-item.hentry' => array(
						'margin-bottom' => '0',
					),
					'.woocommerce div.product .elementor-element.elementor-products-grid .related.products ul.products li.product, .elementor-element .elementor-wc-products .woocommerce[class*=\'columns-\'] ul.products li.product' => array(
						'width'  => 'auto',
						'margin' => '0',
						'float'  => 'none',
					),
				);

				if ( astra_can_remove_elementor_toc_margin_space() ) {
					$elementor_base_css['.elementor-toc__list-wrapper'] = array(
						'margin' => 0,
					);
				}

				if ( astra_can_add_styling_for_hr() ) {
					$elementor_base_css['body .elementor hr'] = array(
						'background-color' => '#ccc',
						'margin'           => '0',
					);
				}

				// Load base static CSS when Elmentor is activated.
				$parse_css .= astra_parse_css( $elementor_base_css );

				if ( is_rtl() ) {
					$elementor_rtl_support_css = array(
						'.ast-left-sidebar .elementor-section.elementor-section-stretched,.ast-right-sidebar .elementor-section.elementor-section-stretched' => array(
							'max-width' => '100%',
							'right'     => '0 !important',
						),
					);
				} else {
					$elementor_rtl_support_css = array(
						'.ast-left-sidebar .elementor-section.elementor-section-stretched,.ast-right-sidebar .elementor-section.elementor-section-stretched' => array(
							'max-width' => '100%',
							'left'      => '0 !important',
						),
					);
				}
				$parse_css .= astra_parse_css( $elementor_rtl_support_css );


				$dynamic_css .= $parse_css;
			}

			// To visible proper column structure with elementor flexbox model.
			$elementor_posts_container_css = array(
				'.elementor-posts-container [CLASS*="ast-width-"]' => array(
					'width' => '100%',
				),
			);
			
			$dynamic_css .= astra_parse_css( $elementor_posts_container_css );

			$elementor_archive_page_css = array(
				'.elementor-template-full-width .ast-container' => array(
					'display' => 'block',
				),
				'.elementor-screen-only, .screen-reader-text, .screen-reader-text span, .ui-helper-hidden-accessible' => array(
					'top' => '0 !important',
				),
			);
			$dynamic_css               .= astra_parse_css( $elementor_archive_page_css );

			$dynamic_css .= astra_parse_css(
				array(
					'.elementor-element .elementor-wc-products .woocommerce[class*="columns-"] ul.products li.product' => array(
						'width'  => 'auto',
						'margin' => '0',
					),
					'.elementor-element .woocommerce .woocommerce-result-count' => array(
						'float' => 'none',
					),
				),
				'',
				astra_get_mobile_breakpoint()
			);

			return $dynamic_css;
		}

		/**
		 * Elementor Content layout set as Page Builder
		 *
		 * @return void
		 * @since  1.0.2
		 */
		public function elementor_default_setting() {

			if ( false === astra_enable_page_builder_compatibility() || 'post' == get_post_type() ) {
				return;
			}

			// don't modify post meta settings if we are not on Elementor's edit page.
			if ( ! $this->is_elementor_editor() ) {
				return;
			}

			global $post;
			$id = astra_get_post_id();

			$page_builder_flag = get_post_meta( $id, '_astra_content_layout_flag', true );
			if ( isset( $post ) && empty( $page_builder_flag ) && ( is_admin() || is_singular() ) ) {

				if ( empty( $post->post_content ) && $this->is_elementor_activated( $id ) ) {

					update_post_meta( $id, '_astra_content_layout_flag', 'disabled' );
					update_post_meta( $id, 'site-post-title', 'disabled' );
					update_post_meta( $id, 'ast-title-bar-display', 'disabled' );
					update_post_meta( $id, 'ast-featured-img', 'disabled' );

					// Compatibility with revamped layouts to update default layout to page builder.
					$migrated_user = ( ! Astra_Dynamic_CSS::astra_fullwidth_sidebar_support() );
					if ( $migrated_user ) {
						$content_layout = get_post_meta( $id, 'site-content-layout', true );
					} else {
						$content_layout = get_post_meta( $id, 'ast-site-content-layout', true );
					}

					if ( empty( $content_layout ) || 'default' == $content_layout ) {
						if ( $migrated_user ) {
							update_post_meta( $id, 'site-content-layout', 'page-builder' );
						}
						update_post_meta( $id, 'ast-site-content-layout', 'full-width-container' );
					}

					$sidebar_layout = get_post_meta( $id, 'site-sidebar-layout', true );
					if ( empty( $sidebar_layout ) || 'default' == $sidebar_layout ) {
						update_post_meta( $id, 'site-sidebar-layout', 'no-sidebar' );
					}

					// In the preview mode, Apply the layouts using filters for Elementor Template Library.
					add_filter(
						'astra_page_layout',
						function() { // phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.Found
							return 'no-sidebar';
						}
					);

					add_filter(
						'astra_get_content_layout',
						function () { // phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.Found
							return 'page-builder';
						}
					);

					add_filter( 'astra_the_post_title_enabled', '__return_false' );
					add_filter( 'astra_featured_image_enabled', '__return_false' );
				}
			}
		}

		/**
		 * Add z-index CSS for elementor's drag drop
		 *
		 * @return void
		 * @since  1.4.0
		 */
		public function elementor_overlay_zindex() {

			// return if we are not on Elementor's edit page.
			if ( ! $this->is_elementor_editor() ) {
				return;
			}

			?>
			<style type="text/css" id="ast-elementor-overlay-css">
				.elementor-editor-active .elementor-element > .elementor-element-overlay {
					z-index: 9999;
				}
				.elementor-element .elementor-widget-woocommerce-checkout-page #customer_details {
					background: var(--checkout-sections-background-color, #ffffff);
				}
			</style>

			<?php
		}

		/**
		 * Check is elementor activated.
		 *
		 * @param int $id Post/Page Id.
		 * @return boolean
		 */
		public function is_elementor_activated( $id ) {
			if ( version_compare( ELEMENTOR_VERSION, '1.5.0', '<' ) ) {
				return ( 'builder' === Plugin::$instance->db->get_edit_mode( $id ) );
			} else {
				$document = Plugin::$instance->documents->get( $id );
				if ( $document ) {
					return $document->is_built_with_elementor();
				} else {
					return false;
				}
			}
		}

		/**
		 * Check if Elementor Editor is open.
		 *
		 * @since  1.2.7
		 *
		 * @return boolean True IF Elementor Editor is loaded, False If Elementor Editor is not loaded.
		 */
		private function is_elementor_editor() {
			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}

			return false;
		}

		/**
		 * Display theme global colors to Elementor Global colors
		 *
		 * @since 3.7.0
		 * @param object          $response rest request response.
		 * @param array           $handler Route handler used for the request.
		 * @param WP_REST_Request $request Request used to generate the response.
		 * @return object
		 */
		public function elementor_add_theme_colors( $response, $handler, $request ) {

			$route = $request->get_route();

			if ( astra_maybe_disable_global_color_in_elementor() ) {
				return $response;
			}

			if ( '/elementor/v1/globals' != $route ) {
				return $response;
			}

			$global_palette = astra_get_option( 'global-color-palette' );
			$data           = $response->get_data();
			$slugs          = Astra_Global_Palette::get_palette_slugs();
			$labels         = Astra_Global_Palette::get_palette_labels();

			foreach ( $global_palette['palette'] as $key => $color ) {

				$slug = $slugs[ $key ];
				// Remove hyphens from slug.
				$no_hyphens = str_replace( '-', '', $slug );

				$data['colors'][ $no_hyphens ] = array(
					'id'    => esc_attr( $no_hyphens ),
					'title' => 'Theme ' . $labels[ $key ],
					'value' => $color,
				);
			}

			$response->set_data( $data );
			return $response;
		}

		/**
		 * Display global paltte colors on Elementor front end Page.
		 *
		 * @since 3.7.0
		 * @param object          $response rest request response.
		 * @param array           $handler Route handler used for the request.
		 * @param WP_REST_Request $request Request used to generate the response.
		 * @return object
		 */
		public function display_global_colors_front_end( $response, $handler, $request ) {
			if ( astra_maybe_disable_global_color_in_elementor() ) {
				return $response;
			}

			$route = $request->get_route();

			if ( 0 !== strpos( $route, '/elementor/v1/globals' ) ) {
				return $response;
			}

			$slug_map      = array();
			$palette_slugs = Astra_Global_Palette::get_palette_slugs();

			foreach ( $palette_slugs as $key => $slug ) {
				// Remove hyphens as hyphens do not work with Elementor global styles.
				$no_hyphens              = str_replace( '-', '', $slug );
				$slug_map[ $no_hyphens ] = $key;
			}

			$rest_id = substr( $route, strrpos( $route, '/' ) + 1 );

			if ( ! in_array( $rest_id, array_keys( $slug_map ), true ) ) {
				return $response;
			}

			$colors   = astra_get_option( 'global-color-palette' );
			$response = rest_ensure_response(
				array(
					'id'    => esc_attr( $rest_id ),
					'title' => Astra_Global_Palette::get_css_variable_prefix() . esc_html( $slug_map[ $rest_id ] ),
					'value' => $colors['palette'][ $slug_map[ $rest_id ] ],
				)
			);
			return $response;
		}

		/**
		 * Generate CSS variable style for Elementor.
		 *
		 * @since 3.7.0
		 * @param string $dynamic_css Dynamic CSS.
		 * @return object
		 */
		public function generate_global_elementor_style( $dynamic_css ) {
			if ( astra_maybe_disable_global_color_in_elementor() ) {
				return $dynamic_css;
			}

			$global_palette = astra_get_option( 'global-color-palette' );
			$palette_style  = array();
			$slugs          = Astra_Global_Palette::get_palette_slugs();
			$style          = array();

			if ( isset( $global_palette['palette'] ) ) {
				foreach ( $global_palette['palette'] as $color_index => $color ) {
					$variable_key           = '--e-global-color-' . str_replace( '-', '', $slugs[ $color_index ] );
					$style[ $variable_key ] = $color;
				}

				$palette_style[':root'] = $style;
				$dynamic_css           .= astra_parse_css( $palette_style );
			}

			// Apply Astra Mini Cart CSS if Elementor Mini Cart Template is disabled.
			$is_site_rtl = is_rtl();
			$ltr_left    = $is_site_rtl ? 'right' : 'left';
			$ltr_right   = $is_site_rtl ? 'left' : 'right';
			if ( defined( 'ELEMENTOR_PRO_VERSION' ) && 'no' === get_option( 'elementor_' . 'use_mini_cart_template' ) ) {
				$mini_cart_template_css = array(
					'.woocommerce-js .woocommerce-mini-cart' => array(
						'margin-inline-start' => '0',
						'list-style'          => 'none',
						'padding'             => '1.3em',
						'flex'                => '1',
						'overflow'            => 'auto',
						'position'            => 'relative',
					),
					'.woocommerce-js .widget_shopping_cart_content ul li.mini_cart_item' => array(
						'min-height'            => '60px',
						'padding-top'           => '1.2em',
						'padding-bottom'        => '1.2em',
						'padding-' . $ltr_left  => '5em',
						'padding-' . $ltr_right => '0',
					),
					'.woocommerce-js .woocommerce-mini-cart-item .ast-mini-cart-price-wrap' => array(
						'float'      => 'right',
						'margin-top' => '0.5em',
						'position'   => 'absolute',
						$ltr_left    => 'auto',
						$ltr_right   => '0',
						'top'        => '3.5em',
					),
					'.woocommerce-js .widget_shopping_cart_content a.remove' => array(
						'position' => 'absolute',
						$ltr_left  => 'auto',
						$ltr_right => '0',
					),
					'.woocommerce-js .woocommerce-mini-cart__total' => array(
						'display'         => 'flex',
						'justify-content' => 'space-between',
						'padding'         => '0.7em 0',
						'margin-bottom'   => '0',
						'font-size'       => '16px',
						'border-top'      => '1px solid var(--ast-border-color)',
						'border-bottom'   => '1px solid var(--ast-border-color)',
					),
					'.woocommerce-mini-cart__buttons' => array(
						'display'        => 'flex',
						'flex-direction' => 'column',
						'gap'            => '20px',
						'padding-top'    => '1.34em',
					),
					'.woocommerce-mini-cart__buttons .button' => array(
						'text-align'  => 'center',
						'font-weight' => '500',
						'font-size'   => '16px',
					),
					'.woocommerce-js ul.product_list_widget li a img' => array(
						'top' => '52%',
					),
					'.ast-mini-cart-empty .ast-mini-cart-message' => array(
						'display' => 'none',
					),
				);
				$dynamic_css           .= astra_parse_css( $mini_cart_template_css );
			}
			return $dynamic_css;
		}

		/**
		 * Load style inside Elementor editor.
		 *
		 * @since 3.7.0
		 * @return void
		 */
		public function elementor_add_scripts() {

			$editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' );
			$theme              = $editor_preferences->get_model()->get_settings( 'ui_theme' );
			$style              = 'dark' == $theme ? '-dark' : '';

			wp_enqueue_style( 'astra-elementor-editor-style', ASTRA_THEME_URI . 'inc/assets/css/ast-elementor-editor' . $style . '.css', array(), ASTRA_THEME_VERSION );
		}
	}

endif;

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Elementor::get_instance();
