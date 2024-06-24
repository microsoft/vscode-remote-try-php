<?php
/**
 * Easy Digital Downloads Compatibility File.
 *
 * @link https://easydigitaldownloads.com/
 *
 * @package Astra
 */

// If plugin - 'Easy_Digital_Downloads' not exist then return.
if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
	return;
}

/**
 * Astra Easy Digital Downloads Compatibility
 */
if ( ! class_exists( 'Astra_Edd' ) ) :

	/**
	 * Astra Easy Digital Downloads Compatibility
	 *
	 * @since 1.5.5
	 */
	class Astra_Edd {

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

			require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/edd-common-functions.php';// phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			add_filter( 'astra_theme_defaults', array( $this, 'theme_defaults' ) );
			// Register Store Sidebars.
			add_action( 'widgets_init', array( $this, 'store_widgets_init' ), 15 );
			// Replace Edd Store Sidebars.
			add_filter( 'astra_get_sidebar', array( $this, 'replace_store_sidebar' ) );
			// Edd Sidebar Layout.
			add_filter( 'astra_page_layout', array( $this, 'store_sidebar_layout' ) );
			// Edd Content Layout.
			add_filter( 'astra_get_content_layout', array( $this, 'store_content_layout' ) );

			add_filter( 'body_class', array( $this, 'edd_products_item_class' ) );
			add_filter( 'post_class', array( $this, 'edd_single_product_class' ) );
			add_filter( 'post_class', array( $this, 'render_post_class' ), 99 );

			add_action( 'customize_register', array( $this, 'customize_register' ), 2 );

			add_filter( 'astra_theme_assets', array( $this, 'add_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_scripts' ) );
			add_filter( 'astra_dynamic_theme_css', array( $this, 'add_inline_styles' ) );

			add_action( 'wp', array( $this, 'edd_initialization' ) );
			add_action( 'init', array( $this, 'edd_set_defaults_initialization' ) );

			// Add Cart option in dropdown.
			add_filter( 'astra_header_section_elements', array( $this, 'header_section_elements' ) );

			// Add Cart icon in Menu.
			add_filter( 'astra_get_dynamic_header_content', array( $this, 'astra_header_cart' ), 10, 3 );

			add_filter( 'astra_single_post_navigation', array( $this, 'edd_single_post_navigation' ) );

			// Header Cart Icon.
			add_action( 'astra_edd_header_cart_icons_before', array( $this, 'header_cart_icon_markup' ) );
			add_filter( 'astra_edd_cart_in_menu_class', array( $this, 'header_cart_icon_class' ), 99 );

		}

		/**
		 * Header Cart Extra Icons markup
		 *
		 * @return void;
		 */
		public function header_cart_icon_markup() {

			if ( false === Astra_Builder_Helper::$is_header_footer_builder_active && ! defined( 'ASTRA_EXT_VER' ) ) {
				return;
			}

			$icon               = astra_get_option( 'edd-header-cart-icon' );
			$cart_total_display = astra_get_option( 'edd-header-cart-total-display' );
			$cart_count_display = apply_filters( 'astra_edd_header_cart_count', true );
			$cart_title_display = astra_get_option( 'edd-header-cart-title-display' );
			$cart_title         = apply_filters( 'astra_header_cart_title', __( 'Cart', 'astra' ) );

			$cart_title_markup = '<span class="ast-edd-header-cart-title">' . esc_html( $cart_title ) . '</span>';

			$cart_total_markup = '<span class="ast-edd-header-cart-total">' . esc_html( edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ) ) . '</span>';

			// Cart Title & Cart Cart total markup.
			$cart_info_markup = sprintf(
				'<span class="ast-edd-header-cart-info-wrap">
						%1$s
						%2$s
						%3$s
					</span>',
				( $cart_title_display ) ? $cart_title_markup : '',
				( $cart_total_display && $cart_title_display ) ? '/' : '',
				( $cart_total_display ) ? $cart_total_markup : ''
			);

			$cart_items          = count( edd_get_cart_contents() );
			$cart_contents_count = $cart_items;

			$cart_icon = sprintf(
				'<span class="astra-icon ast-icon-shopping-%1$s %2$s"
							%3$s
						>%4$s</span>',
				( $icon ) ? $icon : '',
				( $cart_count_display ) ? '' : 'no-cart-total',
				( $cart_count_display ) ? 'data-cart-total="' . $cart_contents_count . '"' : '',
				( $icon ) ? ( ( false !== Astra_Icons::is_svg_icons() ) ? Astra_Icons::get_icons( $icon ) : '' ) : ''
			);

			// Theme's default icon with cart title and cart total.
			if ( 'default' == $icon || ! defined( 'ASTRA_EXT_VER' ) || ( defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'edd' ) ) ) {
				// Cart Total or Cart Title enable then only add markup.
				if ( $cart_title_display || $cart_total_display ) {
					echo $cart_info_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {

				// Remove Default cart icon added by theme.
				add_filter( 'astra_edd_default_header_cart_icon', '__return_false' );

				/* translators: 1: Cart Title Markup, 2: Cart Icon Markup */
				printf(
					'<div class="ast-addon-cart-wrap">
							%1$s
							%2$s
					</div>',
					( $cart_title_display || $cart_total_display ) ? $cart_info_markup : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					( $cart_icon ) ? $cart_icon : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		/**
		 * Header Cart Icon Class
		 *
		 * @param array $classes Default argument array.
		 *
		 * @return array;
		 */
		public function header_cart_icon_class( $classes ) {

			if ( false === Astra_Builder_Helper::$is_header_footer_builder_active && ! defined( 'ASTRA_EXT_VER' ) ) {
				return $classes;
			}

			$header_cart_icon_style = astra_get_option( 'edd-header-cart-icon-style' );

			$classes[]                  = 'ast-edd-menu-cart-' . $header_cart_icon_style;
			$header_cart_icon_has_color = astra_get_option( 'edd-header-cart-icon-color' );
			if ( ! empty( $header_cart_icon_has_color ) && ( 'none' !== $header_cart_icon_style ) ) {
				$classes[] = 'ast-menu-cart-has-color';
			}

			return $classes;
		}

		/**
		 * Disable EDD style only for the first time
		 *
		 * @return void
		 */
		public function edd_set_defaults_initialization() {

			$astra_theme_options = get_option( 'astra-settings' );
			$edd_settings        = get_option( 'edd_settings' );

			// Set flag to set the EDD style disable only once for the very first time.
			if ( ! isset( $astra_theme_options['ast-edd-disable-styles'] ) ) {
				$astra_theme_options['ast-edd-disable-styles'] = true;
				$edd_settings['disable_styles']                = true;
				update_option( 'astra-settings', $astra_theme_options );
				update_option( 'edd_settings', $edd_settings );
			}

		}

		/**
		 * Single Product Navigation
		 *
		 * @param array $args single products navigation arguments.
		 *
		 * @return array $args single products navigation arguments.
		 */
		public function edd_single_post_navigation( $args ) {
			$is_edd_single_product_page        = astra_is_edd_single_product_page();
			$disable_single_product_navigation = astra_get_option( 'disable-edd-single-product-nav' );
			if ( $is_edd_single_product_page && ! $disable_single_product_navigation ) {
				$next_post = get_next_post();
				$prev_post = get_previous_post();

				$next_text = false;
				if ( $next_post ) {
					$next_text = sprintf(
						'%s <span class="ast-right-arrow">&rarr;</span>',
						$next_post->post_title
					);
				}

				$prev_text = false;
				if ( $prev_post ) {
					$prev_text = sprintf(
						'<span class="ast-left-arrow">&larr;</span> %s',
						$prev_post->post_title
					);
				}

				$args['prev_text'] = $prev_text;
				$args['next_text'] = $next_text;
			} elseif ( $is_edd_single_product_page && $disable_single_product_navigation ) {
				$args['prev_text'] = false;
				$args['next_text'] = false;
			}

			return $args;
		}

		/**
		 * EDD Initialization
		 *
		 * @return void
		 */
		public function edd_initialization() {
			$is_edd_archive_page        = astra_is_edd_archive_page();
			$is_edd_single_product_page = astra_is_edd_single_product_page();

			if ( $is_edd_archive_page ) {
				add_action( 'astra_template_parts_content', array( $this, 'edd_content_loop' ) );
				remove_action( 'astra_template_parts_content', array( Astra_Loop::get_instance(), 'template_parts_default' ) );

				// Add edd wrapper.
				add_action( 'astra_template_parts_content_top', array( $this, 'astra_edd_templat_part_wrap_open' ), 25 );
				add_action( 'astra_template_parts_content_bottom', array( $this, 'astra_edd_templat_part_wrap_close' ), 5 );

				// Remove closing and ending div 'ast-row'.
				remove_action( 'astra_template_parts_content_top', array( Astra_Loop::get_instance(), 'astra_templat_part_wrap_open' ), 25 );
				remove_action( 'astra_template_parts_content_bottom', array( Astra_Loop::get_instance(), 'astra_templat_part_wrap_close' ), 5 );
			}
			if ( $is_edd_single_product_page ) {
				remove_action( 'astra_template_parts_content', array( Astra_Loop::get_instance(), 'template_parts_post' ) );

				add_action( 'astra_template_parts_content', array( $this, 'edd_single_template' ) );

			}
		}


		/**
		 * Add wrapper for edd archive pages
		 *
		 * @return void
		 */
		public function astra_edd_templat_part_wrap_open() {
			?>
				<div class="ast-edd-container">
			<?php
		}

		/**
		 * Add end of wrapper for edd archive pages
		 */
		public function astra_edd_templat_part_wrap_close() {
			?>
				</div> <!-- .ast-edd-container -->
			<?php
		}

		/**
		 * Edd Single Product template
		 */
		public function edd_single_template() {

			astra_entry_before();
			?>

			<div <?php post_class(); ?>>

				<?php astra_entry_top(); ?>

				<?php astra_entry_content_single(); ?>

				<?php astra_entry_bottom(); ?>

			</div><!-- #post-## -->

			<?php
			astra_entry_after();
		}

		/**
		 * Add Cart icon markup
		 *
		 * @param Array $options header options array.
		 *
		 * @return Array header options array.
		 * @since 1.5.5
		 */
		public function header_section_elements( $options ) {

			$options['edd'] = __( 'Easy Digital Downloads', 'astra' );

			return $options;
		}

		/**
		 * Add wrapper to the edd archive content template
		 *
		 * @return void
		 */
		public function edd_content_loop() {
			?>
			<div <?php post_class(); ?>>
				<?php
				/**
				 * Edd Archive Page Product Content Sorting
				 */
				do_action( 'astra_edd_archive_product_content' );
				?>
			</div>
			<?php
		}



		/**
		 * Remove theme post's default classes when EDD archive.
		 *
		 * @param  array $classes Post Classes.
		 * @return array
		 * @since  1.5.5
		 */
		public function render_post_class( $classes ) {
			$post_class = array( 'ast-edd-archive-article' );
			$result     = array_intersect( $classes, $post_class );

			if ( count( $result ) > 0 ) {
				$classes = array_diff(
					$classes,
					array(
						// Astra common grid.
						'ast-col-sm-12',
						'ast-col-md-8',
						'ast-col-md-6',
						'ast-col-md-12',

						// Astra Blog / Single Post.
						'ast-article-post',
						'ast-article-single',
						'ast-separate-posts',
						'remove-featured-img-padding',
						'ast-featured-post',
					)
				);
			}
			return $classes;
		}

		/**
		 * Add Cart icon markup
		 *
		 * @param String $output Markup.
		 * @param String $section Section name.
		 * @param String $section_type Section selected option.
		 * @return Markup String.
		 *
		 * @since 1.5.5
		 */
		public function astra_header_cart( $output, $section, $section_type ) {

			if ( 'edd' === $section_type && apply_filters( 'astra_edd_header_cart_icon', true ) ) {

				$output = $this->edd_mini_cart_markup();
			}

			return $output;
		}

		/**
		 * Easy Digital DOwnloads mini cart markup markup
		 *
		 * @since 1.5.5
		 * @return html
		 */
		public function edd_mini_cart_markup() {
			$class = '';
			if ( edd_is_checkout() ) {
				$class = 'current-menu-item';
			}

			$cart_menu_classes = apply_filters( 'astra_edd_cart_in_menu_class', array( 'ast-menu-cart-with-border' ) );

			ob_start();
			if ( is_customize_preview() && ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ) {
				Astra_Builder_UI_Controller::render_customizer_edit_button();
			}
			?>
			<div class="ast-edd-site-header-cart <?php echo esc_attr( implode( ' ', $cart_menu_classes ) ); ?>">
				<div class="ast-edd-site-header-cart-wrap <?php echo esc_attr( $class ); ?>">
					<?php $this->astra_get_edd_cart(); ?>
				</div>
				<?php if ( ! edd_is_checkout() ) { ?>
				<div class="ast-edd-site-header-cart-widget">
					<?php
					the_widget( 'edd_cart_widget', 'title=' );
					?>
				</div>
				<?php } ?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Cart Link
		 * Displayed a link to the cart including the number of items present and the cart total
		 *
		 * @return void
		 * @since  1.5.5
		 */
		public function astra_get_edd_cart() {

			$view_shopping_cart = apply_filters( 'astra_edd_view_shopping_cart_title', __( 'View your shopping cart', 'astra' ) );
			$edd_cart_link      = apply_filters( 'astra_edd_cart_link', edd_get_checkout_uri() );

			if ( is_customize_preview() ) {
				$edd_cart_link = '#';
			}
			?>
			<a class="ast-edd-cart-container" href="<?php echo esc_url( $edd_cart_link ); ?>" title="<?php echo esc_attr( $view_shopping_cart ); ?>">

						<?php
						do_action( 'astra_edd_header_cart_icons_before' );

						if ( apply_filters( 'astra_edd_default_header_cart_icon', true ) ) {
							?>
							<div class="ast-edd-cart-menu-wrap">
								<span class="count">
									<?php
									if ( apply_filters( 'astra_edd_header_cart_total', true ) ) {
										$cart_items = count( edd_get_cart_contents() );
										echo esc_html( $cart_items );
									}
									?>
								</span>
							</div>
							<?php
						}

						do_action( 'astra_edd_header_cart_icons_after' );

						?>
			</a>
			<?php
		}

		/**
		 * Add assets in theme
		 *
		 * @param array $assets list of theme assets (JS & CSS).
		 * @return array List of updated assets.
		 * @since 1.5.5
		 */
		public function add_styles( $assets ) {
			$assets['css']['astra-edd'] = Astra_Builder_Helper::apply_flex_based_css() ? 'compatibility/edd-grid' : 'compatibility/edd';
			return $assets;
		}

		/**
		 * Add inline style
		 *
		 * @since 1.5.5
		 */
		public function add_inline_scripts() {

			$is_site_rtl = is_rtl();

			if ( ! Astra_Builder_Helper::apply_flex_based_css() ) {
				$max_tablet_edd_css = array(
					'.tablet-columns-1 .ast-edd-archive-article' => array(
						'width' => '100%',
					),
					'.tablet-columns-2 .ast-edd-archive-article' => array(
						'width' => '47.6%',
						'width' => 'calc(50% - 10px)',
					),
					'.tablet-columns-3 .ast-edd-archive-article, .edd_downloads_list[class*="edd_download_columns_"] .edd_download' => array(
						'width' => '30.2%',
						'width' => 'calc(33.33% - 14px)',
					),
					'.tablet-columns-4 .ast-edd-archive-article' => array(
						'width' => '21.5%',
						'width' => 'calc(25% - 15px)',
					),
					'.tablet-columns-5 .ast-edd-archive-article' => array(
						'width' => '16.2%',
						'width' => 'calc(20% - 16px)',
					),
					'.tablet-columns-6 .ast-edd-archive-article' => array(
						'width' => '12.7%',
						'width' => 'calc(16.66% - 16.66px)',
					),
				);
			} else {
				$max_tablet_edd_css = array(
					'.tablet-columns-1 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(1, 1fr)',
					),
					'.tablet-columns-2 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(2, 1fr)',
					),
					'.tablet-columns-3 .ast-edd-container, .edd_downloads_list[class*="edd_download_columns_"] .edd_download' => array(
						'grid-template-columns' => 'repeat(3, 1fr)',
					),
					'.tablet-columns-4 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(4, 1fr)',
					),
					'.tablet-columns-5 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(5, 1fr)',
					),
					'.tablet-columns-6 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(6, 1fr)',
					),
				);
			}

			/* Parse CSS from array() -> max-width: (tablet-breakpoint) px & min-width: (mobile-breakpoint + 1) px */
			$edd_css_output = astra_parse_css( $max_tablet_edd_css, astra_get_mobile_breakpoint( '', 1 ), astra_get_tablet_breakpoint() );

			if ( ! Astra_Builder_Helper::apply_flex_based_css() ) {
				if ( $is_site_rtl ) {
					$max_tablet_edd_lang_direction_css = array(
						'[class*="columns-"] .ast-edd-archive-article:nth-child(n)' => array(
							'margin-left' => '20px',
							'clear'       => 'none',
						),
						'.tablet-columns-2 .ast-edd-archive-article:nth-child(2n), .tablet-columns-3 .ast-edd-archive-article:nth-child(3n), .tablet-columns-4 .ast-edd-archive-article:nth-child(4n), .tablet-columns-5 .ast-edd-archive-article:nth-child(5n), .tablet-columns-6 .ast-edd-archive-article:nth-child(6n), .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(3n)' => array(
							'margin-left' => '0',
							'clear'       => 'left',
						),
						'.tablet-columns-2 .ast-edd-archive-article:nth-child(2n+1), .tablet-columns-3 .ast-edd-archive-article:nth-child(3n+1), .tablet-columns-4 .ast-edd-archive-article:nth-child(4n+1), .tablet-columns-5 .ast-edd-archive-article:nth-child(5n+1), .tablet-columns-6 .ast-edd-archive-article:nth-child(6n+1), .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(3n+1)' => array(
							'clear' => 'right',
						),
					);
				} else {
					$max_tablet_edd_lang_direction_css = array(
						'[class*="columns-"] .ast-edd-archive-article:nth-child(n)' => array(
							'margin-right' => '20px',
							'clear'        => 'none',
						),
						'.tablet-columns-2 .ast-edd-archive-article:nth-child(2n), .tablet-columns-3 .ast-edd-archive-article:nth-child(3n), .tablet-columns-4 .ast-edd-archive-article:nth-child(4n), .tablet-columns-5 .ast-edd-archive-article:nth-child(5n), .tablet-columns-6 .ast-edd-archive-article:nth-child(6n), .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(3n)' => array(
							'margin-right' => '0',
							'clear'        => 'right',
						),
						'.tablet-columns-2 .ast-edd-archive-article:nth-child(2n+1), .tablet-columns-3 .ast-edd-archive-article:nth-child(3n+1), .tablet-columns-4 .ast-edd-archive-article:nth-child(4n+1), .tablet-columns-5 .ast-edd-archive-article:nth-child(5n+1), .tablet-columns-6 .ast-edd-archive-article:nth-child(6n+1), .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(3n+1)' => array(
							'clear' => 'left',
						),
					);
				}
				/* Parse CSS from array() -> max-width: (tablet-breakpoint) px & min-width: (mobile-breakpoint + 1) px */
				$edd_css_output .= astra_parse_css( $max_tablet_edd_lang_direction_css, astra_get_mobile_breakpoint( '', 1 ), astra_get_tablet_breakpoint() );
			}

			if ( ! Astra_Builder_Helper::apply_flex_based_css() ) {
				$mobile_edd_css = array(
					'.mobile-columns-1 .ast-edd-archive-article' => array(
						'width' => '100%',
					),
					'.mobile-columns-3 .ast-edd-archive-article' => array(
						'width' => '28.2%',
						'width' => 'calc(33.33% - 14px)',
					),
					'.mobile-columns-4 .ast-edd-archive-article' => array(
						'width' => '19%',
						'width' => 'calc(25% - 15px)',
					),
					'.mobile-columns-5 .ast-edd-archive-article' => array(
						'width' => '13%',
						'width' => 'calc(20% - 16px)',
					),
					'.mobile-columns-6 .ast-edd-archive-article' => array(
						'width' => '10.2%',
						'width' => 'calc(16.66% - 16.66px)',
					),
					'.edd_downloads_list[class*="edd_download_columns_"] .edd_download, .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(2n+1), .mobile-columns-2 .ast-edd-archive-article' => array(
						'width' => '46.1%',
						'width' => 'calc(50% - 10px)',
					),
				);
			} else {
				$mobile_edd_css = array(
					'.mobile-columns-1 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(1, 1fr)',
					),
					'.mobile-columns-3 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(3, 1fr)',
					),
					'.mobile-columns-4 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(4, 1fr)',
					),
					'.mobile-columns-5 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(5, 1fr)',
					),
					'.mobile-columns-6 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(6, 1fr)',
					),
					'.edd_downloads_list[class*="edd_download_columns_"] .edd_download, .edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(2n+1), .mobile-columns-2 .ast-edd-container' => array(
						'grid-template-columns' => 'repeat(2, 1fr)',
					),
				);
			}

			/* Parse CSS from array() -> max-width: (mobile-breakpoint) px */
			$edd_css_output .= astra_parse_css( $mobile_edd_css, '', astra_get_mobile_breakpoint() );

			if ( $is_site_rtl ) {
				$mobile_edd_lang_direction_css = array(
					'[class*="columns-"] .ast-edd-archive-article:nth-child(n)' => array(
						'margin-left' => '20px',
						'clear'       => 'none',
					),
					'.mobile-columns-1 .ast-edd-archive-article:nth-child(n)' => array(
						'margin-left' => '0',
					),
					'.edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(2n), .mobile-columns-2 .ast-edd-archive-article:nth-child(2n), .mobile-columns-3 .ast-edd-archive-article:nth-child(3n), .mobile-columns-4 .ast-edd-archive-article:nth-child(4n), .mobile-columns-5 .ast-edd-archive-article:nth-child(5n), .mobile-columns-6 .ast-edd-archive-article:nth-child(6n)' => array(
						'margin-left' => '0',
						'clear'       => 'left',
					),
					'.mobile-columns-2 .ast-edd-archive-article:nth-child(2n+1), .mobile-columns-3 .ast-edd-archive-article:nth-child(3n+1), .mobile-columns-4 .ast-edd-archive-article:nth-child(4n+1), .mobile-columns-5 .ast-edd-archive-article:nth-child(5n+1), .mobile-columns-6 .ast-edd-archive-article:nth-child(6n+1)' => array(
						'clear' => 'right',
					),
				);
			} else {
				$mobile_edd_lang_direction_css = array(
					'[class*="columns-"] .ast-edd-archive-article:nth-child(n)' => array(
						'margin-right' => '20px',
						'clear'        => 'none',
					),
					'.mobile-columns-1 .ast-edd-archive-article:nth-child(n)' => array(
						'margin-right' => '0',
					),
					'.edd_downloads_list[class*="edd_download_columns_"] .edd_download:nth-child(2n), .mobile-columns-2 .ast-edd-archive-article:nth-child(2n), .mobile-columns-3 .ast-edd-archive-article:nth-child(3n), .mobile-columns-4 .ast-edd-archive-article:nth-child(4n), .mobile-columns-5 .ast-edd-archive-article:nth-child(5n), .mobile-columns-6 .ast-edd-archive-article:nth-child(6n)' => array(
						'margin-right' => '0',
						'clear'        => 'right',
					),
					'.mobile-columns-2 .ast-edd-archive-article:nth-child(2n+1), .mobile-columns-3 .ast-edd-archive-article:nth-child(3n+1), .mobile-columns-4 .ast-edd-archive-article:nth-child(4n+1), .mobile-columns-5 .ast-edd-archive-article:nth-child(5n+1), .mobile-columns-6 .ast-edd-archive-article:nth-child(6n+1)' => array(
						'clear' => 'left',
					),
				);
			}

			/* Parse CSS from array() -> max-width: (mobile-breakpoint) px */
			$edd_css_output .= astra_parse_css( $mobile_edd_lang_direction_css, '', astra_get_mobile_breakpoint() );

			wp_add_inline_style( 'astra-edd', apply_filters( 'astra_theme_edd_dynamic_css', $edd_css_output ) );

			// Inline js for EDD Cart updates.
			wp_add_inline_script(
				'edd-ajax',
				"jQuery( document ).ready( function($) {
					/**
					 * Astra - Easy Digital Downloads Cart Quantity & Total Amount
					 */
					var cartQuantity = jQuery('.ast-edd-site-header-cart-wrap .count'),
						iconQuantity = jQuery('.ast-edd-site-header-cart-wrap .astra-icon'),
						cartTotalAmount = jQuery('.ast-edd-site-header-cart-wrap .ast-edd-header-cart-total');

					jQuery('body').on('edd_cart_item_added', function( event, response ) {
						cartQuantity.html( response.cart_quantity );
						iconQuantity.attr('data-cart-total', response.cart_quantity );
						cartTotalAmount.html( response.total );
					});

					jQuery('body').on('edd_cart_item_removed', function( event, response ) {
						cartQuantity.html( response.cart_quantity );
						iconQuantity.attr('data-cart-total', response.cart_quantity );
						cartTotalAmount.html( response.total );
					});
				});"
			);
		}

		/**
		 * Dynamic CSS
		 *
		 * @param  string $dynamic_css          Astra Dynamic CSS.
		 * @param  string $dynamic_css_filtered Astra Dynamic CSS Filters.
		 * @since 1.5.5
		 * @return string $dynamic_css
		 */
		public function add_inline_styles( $dynamic_css, $dynamic_css_filtered = '' ) {

			/**
			 * - Variable Declaration
			 */

			$site_content_width    = astra_get_option( 'site-content-width', 1200 );
			$edd_archive_width     = astra_get_option( 'edd-archive-width' );
			$edd_archive_max_width = astra_get_option( 'edd-archive-max-width' );
			$css_output            = '';

			$theme_color  = astra_get_option( 'theme-color' );
			$link_color   = astra_get_option( 'link-color', $theme_color );
			$text_color   = astra_get_option( 'text-color' );
			$link_h_color = astra_get_option( 'link-h-color' );

			$btn_color = astra_get_option( 'button-color' );
			if ( empty( $btn_color ) ) {
				$btn_color = astra_get_foreground_color( $theme_color );
			}

			$btn_h_color = astra_get_option( 'button-h-color' );
			if ( empty( $btn_h_color ) ) {
				$btn_h_color = astra_get_foreground_color( $link_h_color );
			}
			$btn_bg_h_color = astra_get_option( 'button-bg-h-color', $link_h_color );

			$btn_border_radius_fields = astra_get_option( 'button-radius-fields' );

			$cart_h_color = astra_get_foreground_color( $link_h_color );

			$css_output = array(
				// Loading effect color.
				'a.edd-add-to-cart.white .edd-loading, .edd-discount-loader.edd-loading, .edd-loading-ajax.edd-loading' => array(
					'border-left-color' => esc_attr( $cart_h_color ),
				),
			);

			if ( false === Astra_Builder_Helper::$is_header_footer_builder_active ) {

				$compat_css_desktop = array(
					/**
					 * Cart in menu
					 */
					'.ast-edd-site-header-cart a'          => array(
						'color' => esc_attr( $text_color ),
					),

					'.ast-edd-site-header-cart a:focus, .ast-edd-site-header-cart a:hover, .ast-edd-site-header-cart .current-menu-item a' => array(
						'color' => esc_attr( $text_color ),
					),

					'.ast-edd-cart-menu-wrap .count, .ast-edd-cart-menu-wrap .count:after' => array(
						'border-color' => esc_attr( $link_color ),
						'color'        => esc_attr( $link_color ),
					),

					'.ast-edd-cart-menu-wrap:hover .count' => array(
						'color'            => esc_attr( $cart_h_color ),
						'background-color' => esc_attr( $link_color ),
					),
					'.ast-edd-site-header-cart .widget_edd_cart_widget .cart-total' => array(
						'color' => esc_attr( $link_color ),
					),

					'.ast-edd-site-header-cart .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a' => array(
						'color'                      => $btn_h_color,
						'border-color'               => $btn_bg_h_color,
						'background-color'           => $btn_bg_h_color,
						'border-top-left-radius'     => astra_responsive_spacing( $btn_border_radius_fields, 'top', 'desktop' ),
						'border-top-right-radius'    => astra_responsive_spacing( $btn_border_radius_fields, 'right', 'desktop' ),
						'border-bottom-right-radius' => astra_responsive_spacing( $btn_border_radius_fields, 'bottom', 'desktop' ),
						'border-bottom-left-radius'  => astra_responsive_spacing( $btn_border_radius_fields, 'left', 'desktop' ),
					),
					'.site-header .ast-edd-site-header-cart .ast-edd-site-header-cart-widget .edd_checkout a, .site-header .ast-edd-site-header-cart .ast-edd-site-header-cart-widget .edd_checkout a:hover' => array(
						'color' => $btn_color,
					),
					'.below-header-user-select .ast-edd-site-header-cart .widget, .ast-above-header-section .ast-edd-site-header-cart .widget a, .below-header-user-select .ast-edd-site-header-cart .widget_edd_cart_widget a' => array(
						'color' => $text_color,
					),
					'.below-header-user-select .ast-edd-site-header-cart .widget_edd_cart_widget a:hover, .ast-above-header-section .ast-edd-site-header-cart .widget_edd_cart_widget a:hover, .below-header-user-select .ast-edd-site-header-cart .widget_edd_cart_widget a.remove:hover, .ast-above-header-section .ast-edd-site-header-cart .widget_edd_cart_widget a.remove:hover' => array(
						'color' => esc_attr( $link_color ),
					),
					'.widget_edd_cart_widget a.edd-remove-from-cart:hover:after' => array(
						'color'            => esc_attr( $link_color ),
						'border-color'     => esc_attr( $link_color ),
						'background-color' => esc_attr( '#ffffff' ),
					),
				);

				$css_output = array_merge( $css_output, $compat_css_desktop );
			}

			/* Parse CSS from array() */
			$css_output = astra_parse_css( $css_output );

			/* Easy Digital DOwnloads Shop Archive width */
			if ( 'custom' === $edd_archive_width ) :
				// Easy Digital DOwnloads shop archive custom width.
				$site_width  = array(
					'.ast-edd-archive-page .site-content > .ast-container' => array(
						'max-width' => astra_get_css_value( $edd_archive_max_width, 'px' ),
					),
				);
				$css_output .= astra_parse_css( $site_width, astra_get_tablet_breakpoint( '', 1 ) );

			else :
				// Easy Digital DOwnloads shop archive default width.
				$site_width = array(
					'.ast-edd-archive-page .site-content > .ast-container' => array(
						'max-width' => astra_get_css_value( $site_content_width + 40, 'px' ),
					),
				);

				/* Parse CSS from array()*/
				$css_output .= astra_parse_css( $site_width, astra_get_tablet_breakpoint( '', 1 ) );
			endif;

			$dynamic_css .= apply_filters( 'astra_theme_edd_dynamic_css', $css_output );

			return $dynamic_css;
		}

		/**
		 * Theme Defaults.
		 *
		 * @param array $defaults Array of options value.
		 * @return array
		 */
		public function theme_defaults( $defaults ) {

			// Container.
			$defaults['edd-ast-content-layout'] = 'normal-width-container';

			// Sidebar.
			$defaults['edd-general-sidebar-layout'] = 'default';

			// Edd Archive.
			$defaults['edd-archive-grids'] = array(
				'desktop' => 4,
				'tablet'  => 3,
				'mobile'  => 2,
			);

			$defaults['edd-archive-product-structure'] = array(
				'image',
				'category',
				'title',
				'price',
				'add_cart',
			);

			$defaults['edd-archive-add-to-cart-button-text'] = __( 'Add To Cart', 'astra' );
			$defaults['edd-archive-variable-button']         = 'button';
			$defaults['edd-archive-variable-button-text']    = __( 'View Details', 'astra' );

			$defaults['edd-archive-width']              = 'default';
			$defaults['edd-archive-max-width']          = 1200;
			$defaults['disable-edd-single-product-nav'] = false;

			return $defaults;
		}


		/**
		 * Add products item class to the body
		 *
		 * @param Array $classes product classes.
		 *
		 * @return array.
		 */
		public function edd_products_item_class( $classes = '' ) {

			$is_edd_archive_page = astra_is_edd_archive_page();

			if ( $is_edd_archive_page ) {
				$shop_grid = astra_get_option( 'edd-archive-grids' );
				$classes[] = 'columns-' . $shop_grid['desktop'];
				$classes[] = 'tablet-columns-' . $shop_grid['tablet'];
				$classes[] = 'mobile-columns-' . $shop_grid['mobile'];

				$classes[] = 'ast-edd-archive-page';
			}

			return $classes;
		}

		/**
		 * Add class on single product page
		 *
		 * @param Array $classes product classes.
		 *
		 * @return array.
		 */
		public function edd_single_product_class( $classes ) {

			$is_edd_archive_page = astra_is_edd_archive_page();

			if ( $is_edd_archive_page ) {
				$classes[] = 'ast-edd-archive-article';
			}

			return $classes;
		}

		/**
		 * Store widgets init.
		 */
		public function store_widgets_init() {
			register_sidebar(
				apply_filters(
					'astra_edd_sidebar_init',
					array(
						'name'          => esc_html__( 'Easy Digital Downloads Sidebar', 'astra' ),
						'id'            => 'astra-edd-sidebar',
						'description'   => __( 'This sidebar will be used on Product archive, Cart, Checkout and My Account pages.', 'astra' ),
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					)
				)
			);
			register_sidebar(
				apply_filters(
					'astra_edd_single_product_sidebar_init',
					array(
						'name'          => esc_html__( 'EDD Single Product Sidebar', 'astra' ),
						'id'            => 'astra-edd-single-product-sidebar',
						'description'   => __( 'This sidebar will be used on EDD Single Product page.', 'astra' ),
						'before_widget' => '<div id="%1$s" class="widget %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					)
				)
			);
		}

		/**
		 * Assign shop sidebar for store page.
		 *
		 * @param String $sidebar Sidebar.
		 *
		 * @return String $sidebar Sidebar.
		 */
		public function replace_store_sidebar( $sidebar ) {

			$is_edd_page                = astra_is_edd_page();
			$is_edd_single_product_page = astra_is_edd_single_product_page();

			if ( $is_edd_page && ! $is_edd_single_product_page ) {
				$sidebar = 'astra-edd-sidebar';
			} elseif ( $is_edd_single_product_page ) {
				$sidebar = 'astra-edd-single-product-sidebar';
			}

			return $sidebar;
		}

		/**
		 * Easy Digital Downloads Container
		 *
		 * @param String $sidebar_layout Layout type.
		 *
		 * @return String $sidebar_layout Layout type.
		 */
		public function store_sidebar_layout( $sidebar_layout ) {

			$is_edd_page                = astra_is_edd_page();
			$is_edd_single_product_page = astra_is_edd_single_product_page();
			$is_edd_archive_page        = astra_is_edd_archive_page();

			if ( $is_edd_page ) {

				// Global.
				$edd_sidebar = astra_get_option( 'site-sidebar-layout' );

				if ( 'default' !== $edd_sidebar ) {
					$sidebar_layout = $edd_sidebar;
				}

				// Customizer General.
				$edd_customizer_sidebar = astra_get_option( 'edd-general-sidebar-layout' );

				if ( 'default' !== $edd_customizer_sidebar ) {
					$sidebar_layout = $edd_customizer_sidebar;
				}

				if ( $is_edd_single_product_page ) {
					$edd_single_product_sidebar = astra_get_option( 'single-download-sidebar-layout' );

					if ( 'default' !== $edd_single_product_sidebar ) {
						$sidebar_layout = $edd_single_product_sidebar;
					}

					$page_id            = get_the_ID();
					$edd_sidebar_layout = get_post_meta( $page_id, 'site-sidebar-layout', true );
				} elseif ( $is_edd_archive_page ) {
					$edd_sidebar_layout = astra_get_option( 'archive-download-sidebar-layout' );
				} else {
					$edd_sidebar_layout = astra_get_option_meta( 'site-sidebar-layout', '', true );
				}

				if ( 'default' !== $edd_sidebar_layout && ! empty( $edd_sidebar_layout ) ) {
					$sidebar_layout = $edd_sidebar_layout;
				}
			}

			return $sidebar_layout;
		}
		/**
		 * Easy Digital Downloads Container
		 *
		 * @param String $layout Layout type.
		 *
		 * @return String $layout Layout type.
		 */
		public function store_content_layout( $layout ) {

			$is_edd_page         = astra_is_edd_page();
			$is_edd_single_page  = astra_is_edd_single_page();
			$is_edd_archive_page = astra_is_edd_archive_page();

			if ( $is_edd_page ) {


				// Global.
				$edd_layout = astra_toggle_layout( 'ast-site-content-layout', 'global', false );

				if ( 'default' !== $edd_layout ) {
					$layout = $edd_layout;
				}

				// Customizer General.
				$edd_customizer_layout = astra_toggle_layout( 'edd-ast-content-layout', 'global', false );


				if ( 'default' !== $edd_customizer_layout ) {
					$layout = $edd_customizer_layout;
				}

				if ( $is_edd_single_page ) {
					$edd_single_product_layout = astra_toggle_layout( 'single-download-ast-content-layout', 'single', false );

					if ( 'default' !== $edd_single_product_layout ) {
						$layout = $edd_single_product_layout;
					}

					$page_id         = get_the_ID();
					$edd_page_layout = get_post_meta( $page_id, 'site-content-layout', true );
				} elseif ( $is_edd_archive_page ) {
					$edd_page_layout = astra_toggle_layout( 'archive-download-ast-content-layout', 'archive', false );
				} else {
					$edd_page_layout = astra_get_option_meta( 'site-content-layout', '', true );
					if ( isset( $edd_page_layout ) ) {
						$edd_page_layout = astra_toggle_layout( 'ast-site-content-layout', 'meta', false, $edd_page_layout );
					} else {
						$edd_page_layout = astra_toggle_layout( 'ast-site-content-layout', 'meta', false );
					}
				}

				if ( 'default' !== $edd_page_layout && ! empty( $edd_page_layout ) ) {
					$layout = $edd_page_layout;
				}
			}

			return $layout;
		}

		/**
		 * Register Customizer sections and panel for Easy Digital Downloads.
		 *
		 * @since 1.5.5
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {

			// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			/**
			 * Register Sections & Panels
			 */
			require ASTRA_THEME_DIR . 'inc/compatibility/edd/customizer/class-astra-customizer-register-edd-section.php';

			/**
			 * Sections
			 */
			require ASTRA_THEME_DIR . 'inc/compatibility/edd/customizer/sections/class-astra-edd-container-configs.php';
			require ASTRA_THEME_DIR . 'inc/compatibility/edd/customizer/sections/class-astra-edd-sidebar-configs.php';
			require ASTRA_THEME_DIR . 'inc/compatibility/edd/customizer/sections/layout/class-astra-edd-archive-layout-configs.php';
			require ASTRA_THEME_DIR . 'inc/compatibility/edd/customizer/sections/layout/class-astra-edd-single-product-layout-configs.php';
			// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

		}

	}

endif;

if ( apply_filters( 'astra_enable_edd_integration', true ) ) {
	Astra_Edd::get_instance();
}
