<?php
/**
 * Astra functions and definitions.
 * Text Domain: astra
 * When using a child theme (see https://codex.wordpress.org/Theme_Development
 * and https://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * For more information on hooks, actions, and filters,
 * see https://codex.wordpress.org/Plugin_API
 *
 * Astra is a very powerful theme and virtually anything can be customized
 * via a child theme.
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
 * Astra_After_Setup_Theme initial setup
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Astra_After_Setup_Theme' ) ) {

	/**
	 * Astra_After_Setup_Theme initial setup
	 */
	class Astra_After_Setup_Theme {

		/**
		 * Instance
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object
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
			add_action( 'after_setup_theme', array( $this, 'setup_theme' ), 2 );
			add_action( 'wp', array( $this, 'setup_content_width' ) );
		}

		/**
		 * Setup theme
		 *
		 * @since 1.0.0
		 */
		public function setup_theme() {

			do_action( 'astra_class_loaded' );

			/**
			 * Make theme available for translation.
			 * Translations can be filed in the /languages/ directory.
			 * If you're building a theme based on Next, use a find and replace
			 * to change 'astra' to the name of your theme in all the template files.
			 */
			load_theme_textdomain( 'astra', ASTRA_THEME_DIR . '/languages' );

			/**
			 * Theme Support
			 */

			// Gutenberg wide images.
			add_theme_support( 'align-wide' );

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			// Let WordPress manage the document title.
			add_theme_support( 'title-tag' );

			// Enable support for Post Thumbnails on posts and pages.
			add_theme_support( 'post-thumbnails' );

			// Add support for starter content ( wp preview ).
			if ( class_exists( 'Astra_Starter_Content', false ) ) {
				$astra_starter_content = new Astra_Starter_Content();
				add_theme_support( 'starter-content', $astra_starter_content->get() );
			}

			// Switch default core markup for search form, comment form, and comments.
			// to output valid HTML5.
			// Added a new value in HTML5 array 'navigation-widgets' as this was introduced in WP5.5 for better accessibility.
			add_theme_support(
				'html5',
				array(
					'navigation-widgets',
					'search-form',
					'gallery',
					'caption',
					'style',
					'script',
				)
			);

			// Post formats.
			add_theme_support(
				'post-formats',
				array(
					'gallery',
					'image',
					'link',
					'quote',
					'video',
					'audio',
					'status',
					'aside',
				)
			);

			// Add theme support for Custom Logo.
			add_theme_support(
				'custom-logo',
				array(
					'width'       => 180,
					'height'      => 60,
					'flex-width'  => true,
					'flex-height' => true,
				)
			);

			// Customize Selective Refresh Widgets.
			add_theme_support( 'customize-selective-refresh-widgets' );

			/**
			 * This theme styles the visual editor to resemble the theme style,
			 * specifically font, colors, icons, and column width.
			 */
			/* Directory and Extension */
			$dir_name    = 'minified';
			$file_prefix = '.min';
			if ( apply_filters( 'astra_theme_editor_style', true ) ) {
				add_editor_style( 'assets/css/' . $dir_name . '/editor-style' . $file_prefix . '.css' );
			}

			if ( apply_filters( 'astra_fullwidth_oembed', true ) ) {
				// Filters the oEmbed process to run the responsive_oembed_wrapper() function.
				add_filter( 'embed_oembed_html', array( $this, 'responsive_oembed_wrapper' ), 10, 3 );
			}

			// WooCommerce.
			add_theme_support( 'woocommerce' );

			// Rank Math Breadcrumb.
			if ( true === apply_filters( 'astra_rank_math_theme_support', true ) ) {
				add_theme_support( 'rank-math-breadcrumbs' );
			}

			// Native AMP Support.
			if ( true === apply_filters( 'astra_amp_support', true ) ) {
				add_theme_support(
					'amp',
					apply_filters(
						'astra_amp_theme_features',
						array(
							'paired' => true,
						)
					)
				);
			}

			// Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced.
			remove_theme_support( 'block-templates' );

			// Let WooCommerce know, Astra is not compatible with New Product Editor.
			add_filter( 'option_woocommerce_feature_product_block_editor_enabled', '__return_false' );

			add_filter( 'woocommerce_create_pages', array( $this, 'astra_enforce_woo_shortcode_pages' ), 99 );
		}

		/**
		 * Set the $content_width global variable used by WordPress to set image dimennsions.
		 *
		 * @since 1.5.5
		 * @return void
		 */
		public function setup_content_width() {
			global $content_width;

			/**
			 * Content Width
			 */
			if ( ! isset( $content_width ) ) {

				if ( is_home() || is_post_type_archive( 'post' ) ) {
					$blog_width = astra_get_option( 'blog-width' );

					if ( 'custom' === $blog_width ) {
						$content_width = apply_filters( 'astra_content_width', astra_get_option( 'blog-max-width', 1200 ) );
					} else {
						$content_width = apply_filters( 'astra_content_width', astra_get_option( 'site-content-width', 1200 ) );
					}
				} elseif ( is_single() ) {

					if ( 'post' === get_post_type() ) {
						$single_post_max = astra_get_option( 'blog-single-width' );

						if ( 'custom' === $single_post_max ) {
							$content_width = apply_filters( 'astra_content_width', astra_get_option( 'blog-single-max-width', 1200 ) );
						} else {
							$content_width = apply_filters( 'astra_content_width', astra_get_option( 'site-content-width', 1200 ) );
						}
					}

					// For custom post types set the global content width.
					$content_width = apply_filters( 'astra_content_width', astra_get_option( 'site-content-width', 1200 ) );
				} else {
					$content_width = apply_filters( 'astra_content_width', astra_get_option( 'site-content-width', 1200 ) );
				}
			}

		}

		/**
		 * Adds a responsive embed wrapper around oEmbed content
		 *
		 * @param  string $html The oEmbed markup.
		 * @param  string $url The URL being embedded.
		 * @param  array  $attr An array of attributes.
		 * @param  bool   $core_yt_block Whether the oEmbed is being rendered by the core YouTube block.
		 *
		 * @return string       Updated embed markup.
		 */
		public function responsive_oembed_wrapper( $html, $url, $attr, $core_yt_block = false ) {
			$add_astra_oembed_wrapper = apply_filters( 'astra_responsive_oembed_wrapper_enable', true );
			$ast_embed_wrapper_class  = apply_filters( 'astra_embed_wrapper_class', '' );

			$allowed_providers = apply_filters(
				'astra_allowed_fullwidth_oembed_providers',
				array(
					'vimeo.com',
					'youtube.com',
					'youtu.be',
					'wistia.com',
					'wistia.net',
					'spotify.com',
					'soundcloud.com',
					'animoto.com',
					'cloudup.com',
					'poll.fm',
					'dai.ly',
					'mixcloud.com',
					'pca.st',
					'reddit.com',
					'scribd.com',
					'slideshare.net',
					'speakerdeck.com',
					'tumblr.com',
					'videopress.com',
					'wordpress.org',
					'wordpress.tv',
					'imgur.com',
					'ted.com',
				)
			);

			if ( astra_strposa( $url, $allowed_providers ) && $add_astra_oembed_wrapper ) {
				if ( $core_yt_block ) {
					$embed_html = wp_oembed_get( $url );
					$html       = false !== $embed_html ? '<div class="wp-block-embed__wrapper"> <div class="ast-oembed-container ' . esc_attr( $ast_embed_wrapper_class ) . '" style="height: 100%;">' . $embed_html . '</div> </div>' : '';
				} else {
					$html = ( '' !== $html ) ? '<div class="ast-oembed-container ' . esc_attr( $ast_embed_wrapper_class ) . '" style="height: 100%;">' . $html . '</div>' : '';
				}
			} elseif ( '' === $html || $url === trim( $html ) ) {
				$embed_html = wp_oembed_get( $url, array( 'width' => 600 ) );
				$html       = $embed_html ? $embed_html : $url;
				wp_maybe_enqueue_oembed_host_js( $html );
			}

			return $html;
		}

		/**
		 * Enforce WooCommerce shortcode pages due to following reasons.
		 *
		 * 1. In WooCommerce 8.3 version cart & checkout pages are directly added with blocks and not with shortcodes.
		 * 2. Due to which most of Astra extended features are not working on cart & checkout pages.
		 *
		 * This is temporary workaround, once Astra ready with WooCommerce 8.3 version, this will be removed.
		 *
		 * @since 4.5.1
		 * @param array $pages_data Array of WooCommerce pages.
		 *
		 * @return array
		 */
		public function astra_enforce_woo_shortcode_pages( $pages_data ) {
			$pages_data['cart']['content']     = '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->';
			$pages_data['checkout']['content'] = '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->';

			return $pages_data;
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_After_Setup_Theme::get_instance();
