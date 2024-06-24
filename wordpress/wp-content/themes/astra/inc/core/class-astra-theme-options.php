<?php
/**
 * Astra Theme Options
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Theme Options
 */
if ( ! class_exists( 'Astra_Theme_Options' ) ) {
	/**
	 * Theme Options
	 */
	class Astra_Theme_Options {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Customizer defaults.
		 *
		 * @access Private
		 * @since 1.4.3
		 * @var Array
		 */
		private static $defaults;

		/**
		 * Post id.
		 *
		 * @var $instance Post id.
		 */
		public static $post_id = null;

		/**
		 * A static option variable.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var mixed $db_options
		 */
		private static $db_options;

		/**
		 * A static option variable.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var mixed $db_options
		 */
		private static $db_options_no_defaults;

		/**
		 * A static theme astra-options variable.
		 *
		 * @since 4.0.2
		 * @access public
		 * @var mixed $astra_options
		 */
		public static $astra_options = null;

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

			// Refresh options variables after customizer save.
			add_action( 'after_setup_theme', array( $this, 'refresh' ) );

		}

		/**
		 * Set default theme option values
		 *
		 * @since 1.0.0
		 * @return default values of the theme.
		 */
		public static function defaults() {

			if ( ! is_null( self::$defaults ) ) {
				return self::$defaults;
			}

			$palette_css_var_prefix = Astra_Global_Palette::get_css_variable_prefix();
			/**
			 * Update Astra customizer default values. To not update directly on existing users site, added backwards.
			 *
			 * @since 3.6.3
			 */
			$apply_new_default_values = astra_button_default_padding_updated();

			/**
			 * Update Astra customizer default values. To not update directly on existing users site, added backwards.
			 *
			 * @since 4.5.2
			 */
			$apply_scndry_default_padding_values = astra_scndry_btn_default_padding();
			$update_secondary_paddings           = Astra_Dynamic_CSS::astra_4_6_4_compatibility();

			$desk_sec_vertical_padding = $apply_scndry_default_padding_values ? 15 : '';
			$desk_sec_vertical_padding = $update_secondary_paddings ? 13 : $desk_sec_vertical_padding;

			$tab_sec_vertical_padding = $apply_scndry_default_padding_values ? 14 : '';
			$tab_sec_vertical_padding = $update_secondary_paddings ? 12 : $tab_sec_vertical_padding;

			$mob_sec_vertical_padding = $apply_scndry_default_padding_values ? 12 : '';
			$mob_sec_vertical_padding = $update_secondary_paddings ? 10 : $mob_sec_vertical_padding;

			/**
			 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
			 *
			 * @since 4.0.0
			 */
			$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

			$astra_options        = self::get_astra_options();
			$post_per_page        = intval( get_option( 'posts_per_page' ) );
			$blog_defaults_update = Astra_Dynamic_CSS::astra_4_6_0_compatibility();

			// Update Astra heading 5 font size & handled backward case
			$update_heading_five_font_size  = Astra_Dynamic_CSS::astra_4_6_14_compatibility();
			$updated_heading_font_five_size = ( $blog_defaults_update && $update_heading_five_font_size ) ? 18 : 16;

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$selected_layout = ( false === $blog_defaults_update ) ? 'blog-layout-1' : 'blog-layout-4';
			} else {
				$selected_layout = ( false === $blog_defaults_update ) ? 'blog-layout-classic' : 'blog-layout-4';
			}

			// Defaults list of options.
			self::$defaults = apply_filters(
				'astra_theme_defaults',
				array(
					// Blog Single.
					'blog-single-width'                    => 'default',
					'blog-single-max-width'                => 1200,
					'single-content-images-shadow'         => true,
					'single-post-ast-content-layout'       => $blog_defaults_update ? 'narrow-width-container' : 'default',
					'single-post-sidebar-style'            => $blog_defaults_update ? 'boxed' : 'default',
					'ast-dynamic-single-post-elements-gap' => $blog_defaults_update ? 15 : 10,
					'ast-dynamic-single-post-meta-font-size' => array(
						'desktop'      => $blog_defaults_update ? 13 : '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'ast-dynamic-single-post-meta-font-weight' => $blog_defaults_update ? '600' : '',
					'ast-dynamic-single-post-metadata'     => $blog_defaults_update ? array(
						'author',
						'date',
					) : array(
						'comments',
						'author',
						'date',
					),

					// Search.
					'section-search-page-title-structure'  => $blog_defaults_update ? array(
						'section-search-page-title-title',
						'section-search-page-title-breadcrumb',
					) : array(
						'section-search-page-title-title',
					),
					'section-search-page-title-custom-title' => __( 'Search Results for:', 'astra' ),
					'section-search-page-title-found-custom-description' => __( 'Here are the search results for your search.', 'astra' ),
					'section-search-page-title-not-found-custom-description' => __( 'Sorry, but we could not find anything related to your search terms. Please try again.', 'astra' ),
					'section-search-page-title-title-font-weight' => $blog_defaults_update ? '600' : '',
					'section-search-page-title-title-font-size' => array(
						'desktop'      => $blog_defaults_update ? 32 : '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'ast-search-results-per-page'          => 10,
					'section-search-page-title-horizontal-alignment' => $blog_defaults_update ? array(
						'desktop' => 'center',
						'tablet'  => 'center',
						'mobile'  => 'center',
					) : array(
						'desktop' => '',
						'tablet'  => '',
						'mobile'  => '',
					),
					'ast-search-live-search'               => false,
					'ast-search-live-search-post-types'    => array(
						'post' => 1,
						'page' => 1,
					),

					// Blog.
					'blog-post-structure'                  => $blog_defaults_update ? array(
						'image',
						'category',
						'title',
						'title-meta',
						'excerpt',
					) : array(
						'image',
						'title-meta',
					),
					'blog-post-per-page'                   => $post_per_page ? $post_per_page : 10,
					'blog-hover-effect'                    => $blog_defaults_update ? 'zoom-in' : 'none',
					'blog-layout'                          => $selected_layout,
					'blog-width'                           => 'default',
					'blog-meta-date-type'                  => 'published',
					'blog-meta-date-format'                => '',
					'blog-max-width'                       => 1200,
					'blog-post-content'                    => 'excerpt',
					'blog-meta'                            => $blog_defaults_update ? array(
						'author',
						'date',
					) : array(
						'comments',
						'category',
						'author',
					),
					'post-card-border-radius'              => array(
						'desktop'      => array(
							'top'    => $blog_defaults_update ? 6 : '',
							'right'  => $blog_defaults_update ? 6 : '',
							'bottom' => $blog_defaults_update ? 6 : '',
							'left'   => $blog_defaults_update ? 6 : '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'post-card-featured-overlay'           => '',
					'blog-category-style'                  => 'default',
					'blog-tag-style'                       => 'default',
					'blog-post-meta-divider-type'          => '/',
					'blog-meta-category-style'             => 'default',
					'blog-meta-tag-style'                  => 'default',
					'blog-image-ratio-type'                => $blog_defaults_update ? 'predefined' : '',
					'blog-image-ratio-pre-scale'           => '16/9',
					'blog-image-custom-scale-width'        => 16,
					'blog-image-custom-scale-height'       => 9,
					// Colors.
					'text-color'                           => 'var(' . $palette_css_var_prefix . '3)',
					'link-color'                           => 'var(' . $palette_css_var_prefix . '0)',
					'theme-color'                          => 'var(' . $palette_css_var_prefix . '0)',
					'link-h-color'                         => 'var(' . $palette_css_var_prefix . '1)',
					'heading-base-color'                   => 'var(' . $palette_css_var_prefix . '2)',
					'border-color'                         => 'var(' . $palette_css_var_prefix . '6)',

					// Footer Bar Background.
					'footer-bg-obj'                        => array(
						'background-color'      => '',
						'background-image'      => '',
						'background-repeat'     => 'repeat',
						'background-position'   => 'center center',
						'background-size'       => 'auto',
						'background-attachment' => 'scroll',
						'background-type'       => '',
						'background-media'      => '',
						'overlay-type'          => '',
						'overlay-opacity'       => '',
						'overlay-color'         => '',
						'overlay-gradient'      => '',
					),
					'footer-color'                         => '',
					'footer-link-color'                    => '',
					'footer-link-h-color'                  => '',

					// Footer Widgets Background.
					'footer-adv-bg-obj'                    => array(
						'background-color'      => '',
						'background-image'      => '',
						'background-repeat'     => 'repeat',
						'background-position'   => 'center center',
						'background-size'       => 'auto',
						'background-attachment' => 'scroll',
						'background-type'       => '',
						'background-media'      => '',
						'overlay-type'          => '',
						'overlay-color'         => '',
						'overlay-opacity'       => '',
						'overlay-gradient'      => '',
					),
					'footer-adv-text-color'                => '',
					'footer-adv-link-color'                => '',
					'footer-adv-link-h-color'              => '',
					'footer-adv-wgt-title-color'           => '',

					// Buttons.
					'button-color'                         => '',
					'button-h-color'                       => '',
					'button-bg-color'                      => '',
					'button-bg-h-color'                    => '',
					'secondary-button-bg-h-color'          => '',
					'secondary-button-bg-color'            => '',
					'secondary-button-color'               => '',
					'secondary-button-h-color'             => '',
					'theme-button-padding'                 => array(
						'desktop'      => array(
							'top'    => $apply_new_default_values ? 15 : 10,
							'right'  => $apply_new_default_values ? 30 : 40,
							'bottom' => $apply_new_default_values ? 15 : 10,
							'left'   => $apply_new_default_values ? 30 : 40,
						),
						'tablet'       => array(
							'top'    => $apply_new_default_values ? 14 : '',
							'right'  => $apply_new_default_values ? 28 : '',
							'bottom' => $apply_new_default_values ? 14 : '',
							'left'   => $apply_new_default_values ? 28 : '',
						),
						'mobile'       => array(
							'top'    => $apply_new_default_values ? 12 : '',
							'right'  => $apply_new_default_values ? 24 : '',
							'bottom' => $apply_new_default_values ? 12 : '',
							'left'   => $apply_new_default_values ? 24 : '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'secondary-theme-button-padding'       => array(
						'desktop'      => array(
							'top'    => $apply_scndry_default_padding_values ? $desk_sec_vertical_padding : '',
							'right'  => $apply_scndry_default_padding_values ? 30 : '',
							'bottom' => $apply_scndry_default_padding_values ? $desk_sec_vertical_padding : '',
							'left'   => $apply_scndry_default_padding_values ? 30 : '',
						),
						'tablet'       => array(
							'top'    => $apply_scndry_default_padding_values ? $tab_sec_vertical_padding : '',
							'right'  => $apply_scndry_default_padding_values ? 28 : '',
							'bottom' => $apply_scndry_default_padding_values ? $tab_sec_vertical_padding : '',
							'left'   => $apply_scndry_default_padding_values ? 28 : '',
						),
						'mobile'       => array(
							'top'    => $apply_scndry_default_padding_values ? $mob_sec_vertical_padding : '',
							'right'  => $apply_scndry_default_padding_values ? 24 : '',
							'bottom' => $apply_scndry_default_padding_values ? $mob_sec_vertical_padding : '',
							'left'   => $apply_scndry_default_padding_values ? 24 : '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'button-radius-fields'                 => array(
						'desktop'      => array(
							'top'    => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'right'  => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'bottom' => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'left'   => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'secondary-button-radius-fields'       => array(
						'desktop'      => array(
							'top'    => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'right'  => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'bottom' => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'left'   => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'theme-button-border-group-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'secondary-theme-button-border-group-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),

					// Footer - Small.
					'footer-sml-layout'                    => 'footer-sml-layout-1',
					'footer-sml-section-1'                 => 'custom',
					'footer-sml-section-1-credit'          => __( 'Copyright &copy; [current_year] [site_title] | Powered by [theme_author]', 'astra' ),
					'footer-sml-section-2'                 => '',
					'footer-sml-section-2-credit'          => __( 'Copyright &copy; [current_year] [site_title] | Powered by [theme_author]', 'astra' ),
					'footer-sml-dist-equal-align'          => true,
					'footer-sml-divider'                   => 1,
					'footer-sml-divider-color'             => '#7a7a7a',
					'footer-layout-width'                  => 'content',
					// General.
					'ast-header-retina-logo'               => '',
					'use-logo-svg-icon'                    => false,
					'logo-svg-icon'                        => array(
						'type'  => '',
						'value' => '',
					),
					'logo-svg-site-title-gap'              => array(
						'desktop' => '5',
						'tablet'  => '5',
						'mobile'  => '5',
					),
					'ast-header-logo-width'                => '',
					'ast-header-responsive-logo-width'     => array(
						'desktop' => '',
						'tablet'  => '',
						'mobile'  => '',
					),
					'header-color-site-title'              => '',
					'header-color-h-site-title'            => '',
					'header-color-site-tagline'            => '',
					'display-site-title-responsive'        => array(
						'desktop' => 1,
						'tablet'  => 1,
						'mobile'  => 1,
					),
					'display-site-tagline-responsive'      => array(
						'desktop' => 0,
						'tablet'  => 0,
						'mobile'  => 0,
					),
					'logo-title-inline'                    => 1,
					// Header - Primary.
					'disable-primary-nav'                  => false,
					'header-layouts'                       => 'header-main-layout-1',
					'header-main-rt-section'               => 'none',
					'header-display-outside-menu'          => false,
					'header-main-rt-section-html'          => '<button>' . __( 'Contact Us', 'astra' ) . '</button>',
					'header-main-rt-section-button-text'   => __( 'Button', 'astra' ),
					'header-main-rt-section-button-link'   => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
					'header-main-rt-section-button-link-option' => array(
						'url'      => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
						'new_tab'  => false,
						'link_rel' => '',
					),
					'header-main-rt-section-button-style'  => 'theme-button',
					'header-main-rt-section-button-text-color' => '',
					'header-main-rt-section-button-back-color' => '',
					'header-main-rt-section-button-text-h-color' => '',
					'header-main-rt-section-button-back-h-color' => '',
					'header-main-rt-section-button-padding' => array(
						'desktop' => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'  => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'  => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
					),
					'header-main-rt-section-button-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'header-main-sep'                      => 1,
					'header-main-sep-color'                => '',
					'header-main-layout-width'             => 'content',
					// Header - Sub menu Border.
					'primary-submenu-border'               => array(
						'top'    => '2',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
					),
					'primary-submenu-item-border'          => false,
					'primary-submenu-b-color'              => '',
					'primary-submenu-item-b-color'         => '',

					// Primary header button typo options.
					'primary-header-button-font-family'    => 'inherit',
					'primary-header-button-font-weight'    => 'inherit',
					'primary-header-button-font-size'      => array(
						'desktop'      => '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'primary-header-button-text-transform' => '',
					'primary-header-button-line-height'    => 1,
					'primary-header-button-letter-spacing' => '',

					'header-main-menu-label'               => '',
					'header-main-menu-align'               => 'inline',
					'header-main-submenu-container-animation' => '',
					'mobile-header-breakpoint'             => '',
					'mobile-header-logo'                   => '',
					'mobile-header-logo-width'             => '',
					// Site Layout.
					'site-layout'                          => 'ast-full-width-layout',
					'site-content-width'                   => 1200,
					'narrow-container-max-width'           => 750,
					'site-layout-outside-bg-obj-responsive' => array(
						'desktop' => array(
							'background-color'      => $apply_new_default_values ? 'var(--ast-global-color-4)' : '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
					'content-bg-obj-responsive'            => array(
						'desktop' => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
					// Entry Content.
					'wp-blocks-ui'                         => false === astra_check_is_structural_setup() ? 'custom' : 'comfort',
					'wp-blocks-global-padding'             => array(
						'desktop'      => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'em',
						'tablet-unit'  => 'em',
						'mobile-unit'  => 'em',
					),
					// Single Comments.
					'enable-comments-area'                 => true,
					'comments-box-placement'               => '',
					'comment-form-position'                => 'below',
					'comments-box-container-width'         => '',
					'ast-sub-section-comments-margin'      => array(
						'desktop'      => array(
							'top'    => $blog_defaults_update ? 2 : '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'em',
						'tablet-unit'  => 'em',
						'mobile-unit'  => 'em',
					),
					'ast-sub-section-comments-padding'     => array(
						'desktop'      => array(
							'top'    => '',
							'right'  => '',
							'bottom' => $blog_defaults_update ? 2 : 3,
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'em',
						'tablet-unit'  => 'em',
						'mobile-unit'  => 'em',
					),

					// Container.
					'single-page-ast-content-layout'       => false === astra_check_is_structural_setup() ? 'default' : 'normal-width-container',
					'single-page-content-style'            => false === astra_check_is_structural_setup() ? 'default' : 'unboxed',
					'single-post-content-style'            => 'default',
					'archive-post-ast-content-layout'      => 'default',
					'ast-site-content-layout'              => 'normal-width-container',
					'site-content-style'                   => 'boxed',

					// Typography.
					'body-font-family'                     => 'inherit',
					'body-font-variant'                    => '',
					'body-font-weight'                     => $apply_new_default_color_typo_values ? '400' : 'inherit',
					'font-size-body'                       => array(
						'desktop'      => $apply_new_default_color_typo_values ? 16 : 15,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'body-font-extras'                     => array(
						'line-height'         => ! isset( $astra_options['body-font-extras'] ) && isset( $astra_options['body-line-height'] ) ? $astra_options['body-line-height'] : '1.65',
						'line-height-unit'    => Astra_Dynamic_CSS::astra_4_6_14_compatibility() ? '' : 'em',
						'letter-spacing'      => '',
						'letter-spacing-unit' => 'px',
						'text-transform'      => ! isset( $astra_options['body-font-extras'] ) && isset( $astra_options['body-text-transform'] ) ? $astra_options['body-text-transform'] : '',
						'text-decoration'     => '',
					),
					'headings-font-height-settings'        => array(
						'line-height'         => ! isset( $astra_options['headings-font-extras'] ) && isset( $astra_options['headings-line-height'] ) ? $astra_options['headings-line-height'] : '',
						'line-height-unit'    => 'em',
						'letter-spacing'      => '',
						'letter-spacing-unit' => 'px',
						'text-transform'      => ! isset( $astra_options['headings-font-extras'] ) && isset( $astra_options['headings-text-transform'] ) ? $astra_options['headings-text-transform'] : '',
						'text-decoration'     => '',
					),
					'para-margin-bottom'                   => '',
					'underline-content-links'              => true,
					'site-accessibility-toggle'            => true,
					'site-accessibility-highlight-type'    => 'dotted',
					'site-accessibility-highlight-input-type' => 'disable',
					'body-text-transform'                  => '',
					'headings-font-family'                 => 'inherit',
					'headings-font-weight'                 => $apply_new_default_values ? '600' : 'inherit',
					'font-size-site-title'                 => array(
						'desktop'      => $apply_new_default_color_typo_values ? 26 : 35,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-site-tagline'               => array(
						'desktop'      => 15,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'single-post-outside-spacing'          => array(
						'desktop'      => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-page-title'                 => array(
						'desktop'      => $blog_defaults_update ? 20 : 26,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-post-tax'                   => array(
						'desktop'      => $blog_defaults_update ? 14 : '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-post-meta'                  => array(
						'desktop'      => $blog_defaults_update ? 13 : '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h1'                         => array(
						'desktop'      => $blog_defaults_update ? 36 : 40,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h2'                         => array(
						'desktop'      => $blog_defaults_update ? 30 : 32,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h3'                         => array(
						'desktop'      => $blog_defaults_update ? 24 : 26,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h4'                         => array(
						'desktop'      => $blog_defaults_update ? 20 : 24,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h5'                         => array(
						'desktop'      => $blog_defaults_update ? $updated_heading_font_five_size : 20,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h6'                         => array(
						'desktop'      => 16,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),

					// Sidebar.
					'site-sidebar-layout'                  => false === astra_check_old_sidebar_user() ? 'right-sidebar' : 'no-sidebar',
					'site-sidebar-width'                   => 30,
					'single-page-sidebar-layout'           => false === astra_check_is_structural_setup() ? 'default' : 'no-sidebar',
					'single-post-sidebar-layout'           => 'default',
					'archive-post-sidebar-layout'          => 'default',
					'site-sticky-sidebar'                  => false,
					'site-sidebar-style'                   => 'unboxed',
					'single-page-sidebar-style'            => 'unboxed',
					'archive-post-sidebar-style'           => 'default',

					// Sidebar.
					'footer-adv'                           => 'disabled',
					'footer-adv-border-width'              => '',
					'footer-adv-border-color'              => '#7a7a7a',

					// toogle menu style.
					'mobile-header-toggle-btn-style'       => 'minimal',
					'hide-custom-menu-mobile'              => 1,

					// toogle menu target.
					'mobile-header-toggle-target'          => 'icon',

					// Misc.
					'enable-scroll-to-id'                  => true,
					'ast-dynamic-single-download-structure' => ( true === astra_enable_edd_featured_image_defaults() ) ? array(
						'ast-dynamic-single-download-title',
						'ast-dynamic-single-download-meta',
						'ast-dynamic-single-download-image',
					) : array(
						'ast-dynamic-single-download-title',
						'ast-dynamic-single-download-meta',
					),
				)
			);

			return self::$defaults;
		}

		/**
		 * Get astra-options DB values.
		 *
		 * @return array Return array of theme options from database.
		 *
		 * @since 4.0.0
		 */
		public static function get_astra_options() {
			if ( is_null( self::$astra_options ) || is_customize_preview() ) {
				self::$astra_options = get_option( ASTRA_THEME_SETTINGS );
			}
			return self::$astra_options;
		}

		/**
		 * Get theme options from static array()
		 *
		 * @return array    Return array of theme options.
		 */
		public static function get_options() {
			return self::$db_options;
		}

		/**
		 * Update theme static option array.
		 */
		public static function refresh() {
			self::$db_options = wp_parse_args(
				self::get_db_options(),
				self::defaults()
			);
		}

		/**
		 * Get theme options from static array() from database
		 *
		 * @return array    Return array of theme options from database.
		 */
		public static function get_db_options() {
			self::$db_options_no_defaults = self::get_astra_options();
			return self::$db_options_no_defaults;
		}
	}
}
/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Theme_Options::get_instance();
