<?php
/**
 * Related Posts Loader for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2021, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Customizer Initialization
 *
 * @since 3.5.0
 */
class Astra_Related_Posts_Loader {

	/**
	 *  Constructor
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		add_filter( 'astra_theme_defaults', array( $this, 'theme_defaults' ) );
		add_action( 'customize_register', array( $this, 'related_posts_customize_register' ), 2 );
		// Load Google fonts.
		add_action( 'astra_get_fonts', array( $this, 'add_fonts' ), 1 );
	}

	/**
	 * Enqueue google fonts.
	 *
	 * @return void
	 */
	public function add_fonts() {
		if ( astra_target_rules_for_related_posts() ) {
			// Related Posts Section title.
			$section_title_font_family = astra_get_option( 'related-posts-section-title-font-family' );
			$section_title_font_weight = astra_get_option( 'related-posts-section-title-font-weight' );
			Astra_Fonts::add_font( $section_title_font_family, $section_title_font_weight );

			// Related Posts - Posts title.
			$post_title_font_family = astra_get_option( 'related-posts-title-font-family' );
			$post_title_font_weight = astra_get_option( 'related-posts-title-font-weight' );
			Astra_Fonts::add_font( $post_title_font_family, $post_title_font_weight );

			// Related Posts - Meta Font.
			$meta_font_family = astra_get_option( 'related-posts-meta-font-family' );
			$meta_font_weight = astra_get_option( 'related-posts-meta-font-weight' );
			Astra_Fonts::add_font( $meta_font_family, $meta_font_weight );

			// Related Posts - Content Font.
			$content_font_family = astra_get_option( 'related-posts-content-font-family' );
			$content_font_weight = astra_get_option( 'related-posts-content-font-weight' );
			Astra_Fonts::add_font( $content_font_family, $content_font_weight );
		}
	}

	/**
	 * Set Options Default Values
	 *
	 * @param  array $defaults  Astra options default value array.
	 * @return array
	 */
	public function theme_defaults( $defaults ) {

		/**
		 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
		 *
		 * @since 4.0.0
		 */
		$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

		$astra_options     = Astra_Theme_Options::get_astra_options();
		$astra_blog_update = Astra_Dynamic_CSS::astra_4_6_0_compatibility();

		// Related Posts.
		$defaults['enable-related-posts']                    = false;
		$defaults['related-posts-title']                     = __( 'Related Posts', 'astra' );
		$defaults['releted-posts-title-alignment']           = 'left';
		$defaults['related-posts-total-count']               = 2;
		$defaults['enable-related-posts-excerpt']            = false;
		$defaults['related-posts-box-placement']             = 'default';
		$defaults['related-posts-outside-location']          = 'above';
		$defaults['related-posts-container-width']           = $astra_blog_update ? '' : 'fallback';
		$defaults['related-posts-excerpt-count']             = 25;
		$defaults['related-posts-based-on']                  = 'categories';
		$defaults['related-posts-order-by']                  = 'date';
		$defaults['related-posts-order']                     = 'asc';
		$defaults['related-posts-grid-responsive']           = array(
			'desktop' => '2-equal',
			'tablet'  => '2-equal',
			'mobile'  => 'full',
		);
		$defaults['related-posts-structure']                 = array(
			'featured-image',
			'title-meta',
		);
		$defaults['related-posts-tag-style']                 = 'none';
		$defaults['related-posts-category-style']            = 'none';
		$defaults['related-posts-date-format']               = '';
		$defaults['related-posts-meta-date-type']            = 'published';
		$defaults['related-posts-author-avatar-size']        = '';
		$defaults['related-posts-author-avatar']             = false;
		$defaults['related-posts-author-prefix-label']       = astra_default_strings( 'string-blog-meta-author-by', false );
		$defaults['related-posts-image-size']                = '';
		$defaults['related-posts-image-custom-scale-width']  = 16;
		$defaults['related-posts-image-custom-scale-height'] = 9;
		$defaults['related-posts-image-ratio-pre-scale']     = '16/9';
		$defaults['related-posts-image-ratio-type']          = '';
		$defaults['related-posts-meta-structure']            = array(
			'comments',
			'category',
			'author',
		);
		// Related Posts - Color styles.
		$defaults['related-posts-text-color']            = $apply_new_default_color_typo_values ? 'var(--ast-global-color-2)' : '';
		$defaults['related-posts-link-color']            = '';
		$defaults['related-posts-title-color']           = $apply_new_default_color_typo_values ? 'var(--ast-global-color-2)' : '';
		$defaults['related-posts-background-color']      = '';
		$defaults['related-posts-meta-color']            = '';
		$defaults['related-posts-link-hover-color']      = '';
		$defaults['related-posts-meta-link-hover-color'] = '';
		// Related Posts - Title typo.
		$defaults['related-posts-section-title-font-family']    = 'inherit';
		$defaults['related-posts-section-title-font-weight']    = 'inherit';
		$defaults['related-posts-section-title-text-transform'] = '';
		$defaults['related-posts-section-title-line-height']    = $apply_new_default_color_typo_values ? '1.25' : '';
		$defaults['related-posts-section-title-font-extras']    = array(
			'line-height'         => ! isset( $astra_options['related-posts-section-title-font-extras'] ) && isset( $astra_options['related-posts-section-title-line-height'] ) ? $astra_options['related-posts-section-title-line-height'] : '1.6',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['related-posts-section-title-font-extras'] ) && isset( $astra_options['related-posts-section-title-text-transform'] ) ? $astra_options['related-posts-section-title-text-transform'] : '',
			'text-decoration'     => '',
		);
		$defaults['related-posts-section-title-font-size']      = array(
			'desktop'      => $apply_new_default_color_typo_values ? '26' : '30',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);

		// Related Posts - Title typo.
		$defaults['related-posts-title-font-family']    = 'inherit';
		$defaults['related-posts-title-font-weight']    = $apply_new_default_color_typo_values ? '500' : 'inherit';
		$defaults['related-posts-title-text-transform'] = '';
		$defaults['related-posts-title-line-height']    = '1';
		$defaults['related-posts-title-font-size']      = array(
			'desktop'      => '20',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);
		$defaults['related-posts-title-font-extras']    = array(
			'line-height'         => ! isset( $astra_options['related-posts-title-font-extras'] ) && isset( $astra_options['related-posts-title-line-height'] ) ? $astra_options['related-posts-title-line-height'] : ( $astra_blog_update ? '1.5' : '1' ),
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['related-posts-title-font-extras'] ) && isset( $astra_options['related-posts-title-text-transform'] ) ? $astra_options['related-posts-title-text-transform'] : '',
			'text-decoration'     => '',
		);

		// Related Posts - Meta typo.
		$defaults['related-posts-meta-font-family']    = 'inherit';
		$defaults['related-posts-meta-font-weight']    = 'inherit';
		$defaults['related-posts-meta-text-transform'] = '';
		$defaults['related-posts-meta-line-height']    = '';
		$defaults['related-posts-meta-font-size']      = array(
			'desktop'      => '14',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);
		$defaults['related-posts-meta-font-extras']    = array(
			'line-height'         => ! isset( $astra_options['related-posts-meta-font-extras'] ) && isset( $astra_options['related-posts-meta-line-height'] ) ? $astra_options['related-posts-meta-line-height'] : '1.6',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['related-posts-meta-font-extras'] ) && isset( $astra_options['related-posts-meta-text-transform'] ) ? $astra_options['related-posts-meta-text-transform'] : '',
			'text-decoration'     => '',
		);

		// Related Posts - Content typo.
		$defaults['related-posts-content-font-family']     = 'inherit';
		$defaults['related-posts-content-font-weight']     = 'inherit';
		$defaults['related-posts-content-font-extras']     = array(
			'line-height'         => ! isset( $astra_options['related-posts-content-font-extras'] ) && isset( $astra_options['related-posts-content-line-height'] ) ? $astra_options['related-posts-content-line-height'] : '',
			'line-height-unit'    => 'em',
			'letter-spacing'      => '',
			'letter-spacing-unit' => 'px',
			'text-transform'      => ! isset( $astra_options['related-posts-content-font-extras'] ) && isset( $astra_options['related-posts-content-text-transform'] ) ? $astra_options['related-posts-content-text-transform'] : '',
			'text-decoration'     => '',
		);
		$defaults['related-posts-content-font-size']       = array(
			'desktop'      => '',
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);
		$defaults['ast-sub-section-related-posts-padding'] = array(
			'desktop'      => array(
				'top'    => 2.5,
				'right'  => 2.5,
				'bottom' => 2.5,
				'left'   => 2.5,
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
		);
		$defaults['ast-sub-section-related-posts-margin']  = array(
			'desktop'      => array(
				'top'    => 2,
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
		);

		return $defaults;
	}

	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @since 3.5.0
	 */
	public function related_posts_customize_register( $wp_customize ) {

		/**
		 * Register Config control in Related Posts.
		 */
		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_RELATED_POSTS_DIR . 'customizer/class-astra-related-posts-configs.php';
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Render the Related Posts title for the selective refresh partial.
	 *
	 * @since 3.5.0
	 */
	public function render_related_posts_title() {
		return astra_get_option( 'related-posts-title' );
	}
}

/**
*  Kicking this off by creating NEW instace.
*/
new Astra_Related_Posts_Loader();
