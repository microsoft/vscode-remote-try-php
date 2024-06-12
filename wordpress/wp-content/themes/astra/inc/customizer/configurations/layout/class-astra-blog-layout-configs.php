<?php
/**
 * Bottom Footer Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Blog_Layout_Configs' ) ) {

	/**
	 * Register Blog Layout Customizer Configurations.
	 */
	class Astra_Blog_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Blog Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$astra_backwards = Astra_Dynamic_CSS::astra_4_6_0_compatibility();

			$old_blog_layouts      = array();
			$old_blog_layouts_free = array();
			$new_blog_layouts      = array();
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$if_astra_addon = defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' );
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			if ( false === $astra_backwards ) {
				$old_blog_layouts = array(
					'blog-layout-1' => array(
						'label' => __( 'Layout 1', 'astra' ),
						'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-1', false ) : '',
					),
					'blog-layout-2' => array(
						'label' => __( 'Layout 2', 'astra' ),
						'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-2', false ) : '',
					),
					'blog-layout-3' => array(
						'label' => __( 'Layout 3', 'astra' ),
						'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-3', false ) : '',
					),
				);

				$old_blog_layouts_free = array(
					'blog-layout-classic' => array(
						'label' => __( 'Classic Layout', 'astra' ),
						'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-classic', false ) : '',
					),
				);
			}

			$new_blog_layouts = array(
				'blog-layout-4' => array(
					'label' => __( 'Grid', 'astra' ),
					'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-4', false ) : '',
				),
				'blog-layout-5' => array(
					'label' => __( 'List', 'astra' ),
					'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-5', false ) : '',
				),
				'blog-layout-6' => array(
					'label' => __( 'Cover', 'astra' ),
					'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'blog-layout-6', false ) : '',
				),
			);


			if ( $if_astra_addon ) {
				$blog_layout = array_merge(
					$old_blog_layouts,
					$new_blog_layouts
				);
			} else {
				$blog_layout = array_merge(
					$old_blog_layouts_free,
					$new_blog_layouts
				);
			}

			$_configs = array(

				/**
				 * Option: Blog Content Width
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[blog-width]',
					'default'    => astra_get_option( 'blog-width' ),
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-blog',
					'priority'   => 50,
					'transport'  => 'postMessage',
					'title'      => __( 'Content Width', 'astra' ),
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'custom'  => __( 'Custom', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Enter Width
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[blog-max-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'section'     => 'section-blog',
					'transport'   => 'postMessage',
					'default'     => astra_get_option( 'blog-max-width' ),
					'priority'    => 50,
					'context'     => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-width]',
							'operator' => '===',
							'value'    => 'custom',
						),
					),
					'title'       => __( 'Custom Width', 'astra' ),
					'suffix'      => 'px',
					'input_attrs' => array(
						'min'  => 768,
						'step' => 1,
						'max'  => 1920,
					),
					'divider'     => array( 'ast_class' => 'ast-top-dotted-divider' ),
				),

				/**
				 * Option: Blog Post Content
				 */
				array(
					'name'        => 'blog-post-content',
					'parent'      => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'     => 'section-blog',
					'title'       => __( 'Post Content', 'astra' ),
					'default'     => astra_get_option( 'blog-post-content' ),
					'type'        => 'sub-control',
					'control'     => 'ast-selector',
					'linked'      => 'excerpt',
					'priority'    => 75,
					'choices'     => array(
						'full-content' => __( 'Full Content', 'astra' ),
						'excerpt'      => __( 'Excerpt', 'astra' ),
					),
					'responsive'  => false,
					'renderAs'    => 'text',
					'input_attrs' => array(
						'dependents' => array(
							'excerpt' => array( 'blog-excerpt-count' ),
						),
					),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[blog-divider]',
					'section'  => 'section-blog',
					'title'    => __( 'Blog Layout', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 14,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing' ),
				),

				/**
				 * Option: Blog Layout
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[blog-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => 'section-blog',
					'default'           => astra_get_option( 'blog-layout' ),
					'priority'          => 14,
					'title'             => __( 'Layout', 'astra' ),
					'choices'           => $blog_layout,
				),

				/**
				 * Option: Post Per Page
				 */
				array(
					'name'         => ASTRA_THEME_SETTINGS . '[blog-post-per-page]',
					'default'      => astra_get_option( 'blog-post-per-page' ),
					'type'         => 'control',
					'control'      => 'ast-number',
					'qty_selector' => true,
					'section'      => 'section-blog',
					'title'        => __( 'Post Per Page', 'astra' ),
					'priority'     => 14,
					'responsive'   => false,
					'input_attrs'  => array(
						'min'  => 1,
						'step' => 1,
						'max'  => 500,
					),
					'divider'      => array( 'ast_class' => ( $if_astra_addon ) ? 'ast-top-dotted-divider' : 'ast-top-section-divider' ),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[archive-post-content-structure-divider]',
					'section'  => 'section-blog',
					'title'    => __( 'Posts Structure', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 51,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing' ),
				),

				array(
					'name'              => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'default'           => astra_get_option( 'blog-post-structure' ),
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-blog',
					'priority'          => 52,
					'title'             => __( 'Post Elements', 'astra' ),
					'divider'           => array( 'ast_class' => 'ast-top-spacing' ),
					'choices'           => array(
						'image'      => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'image',
							'clone_limit' => 1,
							'title'       => __( 'Featured Image', 'astra' ),
						),
						'category'   => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'category',
							'clone_limit' => 1,
							'title'       => __( 'Categories', 'astra' ),
						),
						'tag'        => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'tag',
							'clone_limit' => 1,
							'title'       => __( 'Tags', 'astra' ),
						),
						'title'      => __( 'Title', 'astra' ),
						'title-meta' => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'title-meta',
							'clone_limit' => 1,
							'title'       => __( 'Post Meta', 'astra' ),
						),
						'excerpt'    => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'excerpt',
							'clone_limit' => 1,
							'title'       => __( 'Excerpt', 'astra' ),
						),
						'read-more'  => array(
							'clone'       => false,
							'is_parent'   => defined( 'ASTRA_EXT_VER' ) ? true : false,
							'main_index'  => 'read-more',
							'clone_limit' => 1,
							'title'       => __( 'Read More', 'astra' ),
						),
					),
				),

				/**
				 * Option: Date Meta Type.
				 */
				array(
					'name'       => 'blog-meta-date-type',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'section'    => 'section-blog',
					'default'    => astra_get_option( 'blog-meta-date-type' ),
					'priority'   => 1,
					'linked'     => 'date',
					'transport'  => 'postMessage',
					'title'      => __( 'Type', 'astra' ),
					'choices'    => array(
						'published' => __( 'Published', 'astra' ),
						'updated'   => __( 'Last Updated', 'astra' ),
					),
					'divider'    => array( 'ast_class' => 'ast-bottom-spacing' ),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Date format support for meta field.
				 */
				array(
					'name'       => 'blog-meta-date-format',
					'default'    => astra_get_option( 'blog-meta-date-format' ),
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'linked'     => 'date',
					'type'       => 'sub-control',
					'control'    => 'ast-select',
					'transport'  => 'postMessage',
					'section'    => 'section-blog',
					'priority'   => 2,
					'responsive' => false,
					'renderAs'   => 'text',
					'title'      => __( 'Format', 'astra' ),
					'choices'    => array(
						''       => __( 'Default', 'astra' ),
						'F j, Y' => 'November 6, 2010',
						'Y-m-d'  => '2010-11-06',
						'm/d/Y'  => '11/06/2010',
						'd/m/Y'  => '06/11/2010',
					),
				),

				/**
				 * Option: Image Ratio Type.
				 */
				array(
					'name'                   => 'blog-image-ratio-type',
					'default'                => astra_get_option( 'blog-image-ratio-type' ),
					'type'                   => 'sub-control',
					'transport'              => 'postMessage',
					'parent'                 => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'                => 'section-blog',
					'linked'                 => 'image',
					'priority'               => 5,
					'control'                => 'ast-selector',
					'title'                  => __( 'Image Ratio', 'astra' ),
					'choices'                => array(
						''           => __( 'Original', 'astra' ),
						'predefined' => __( 'Predefined', 'astra' ),
						'custom'     => __( 'Custom', 'astra' ),
					),
					'responsive'             => false,
					'renderAs'               => 'text',
					'contextual_sub_control' => true,
					'input_attrs'            => array(
						'dependents' => array(
							''           => array( 'blog-original-image-scale-description' ),
							'predefined' => array( 'blog-image-ratio-pre-scale' ),
							'custom'     => array( 'blog-image-custom-scale-width', 'blog-image-custom-scale-height', 'blog-custom-image-scale-description' ),
						),
					),
				),

				/**
				 * Option: Image Ratio Scale.
				 */
				array(
					'name'       => 'blog-image-ratio-pre-scale',
					'default'    => astra_get_option( 'blog-image-ratio-pre-scale', '16/9' ),
					'type'       => 'sub-control',
					'transport'  => 'postMessage',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'linked'     => 'image',
					'section'    => 'section-blog',
					'priority'   => 10,
					'control'    => 'ast-selector',
					'choices'    => array(
						'1/1'  => __( '1:1', 'astra' ),
						'4/3'  => __( '4:3', 'astra' ),
						'16/9' => __( '16:9', 'astra' ),
						'2/1'  => __( '2:1', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Image Scale width.
				 */
				array(
					'name'              => 'blog-image-custom-scale-width',
					'default'           => astra_get_option( 'blog-image-custom-scale-width', 16 ),
					'type'              => 'sub-control',
					'control'           => 'ast-number',
					'transport'         => 'postMessage',
					'title'             => __( 'Width', 'astra' ),
					'qty_selector'      => true,
					'parent'            => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'           => 'section-blog',
					'linked'            => 'image',
					'priority'          => 11,
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_number_n_blank' ),
				),

				/**
				 * Option: Image Scale height.
				 */
				array(
					'name'         => 'blog-image-custom-scale-height',
					'default'      => astra_get_option( 'blog-image-custom-scale-height', 9 ),
					'type'         => 'sub-control',
					'control'      => 'ast-number',
					'qty_selector' => true,
					'transport'    => 'postMessage',
					'title'        => __( 'Height', 'astra' ),
					'parent'       => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'      => 'section-blog',
					'linked'       => 'image',
					'priority'     => 12,
				),

				array(
					'name'     => 'blog-custom-image-scale-description',
					'parent'   => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'linked'   => 'image',
					'type'     => 'sub-control',
					'control'  => 'ast-description',
					'section'  => 'section-blog',
					'priority' => 14,
					'label'    => '',
					'help'     => sprintf( /* translators: 1: link open markup, 2: link close markup */ __( 'Calculate a personalized image ratio using this %1$s online tool %2$s for your image dimensions.', 'astra' ), '<a href="https://www.digitalrebellion.com/webapps/aspectcalc" target="_blank">', '</a>' ),
				),

				/**
				 * Option: Blog Hover Effect.
				 */
				array(
					'name'       => 'blog-hover-effect',
					'default'    => astra_get_option( 'blog-hover-effect' ),
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'    => 'section-blog',
					'linked'     => 'image',
					'type'       => 'sub-control',
					'priority'   => 17,
					'transport'  => 'postMessage',
					'title'      => __( 'Hover Effect', 'astra' ),
					'divider'    => array( 'ast_class' => 'ast-top-dotted-divider' ),
					'control'    => 'ast-selector',
					'responsive' => false,
					'renderAs'   => 'text',
					'choices'    => array(
						'none'     => __( 'None', 'astra' ),
						'zoom-in'  => __( 'Zoom In', 'astra' ),
						'zoom-out' => __( 'Zoom Out', 'astra' ),
					),
				),

				/**
				 * Option: Image Size.
				 */
				array(
					'name'      => 'blog-image-size',
					'default'   => astra_get_option( 'blog-image-size', 'large' ),
					'parent'    => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'   => 'section-blog',
					'linked'    => 'image',
					'type'      => 'sub-control',
					'priority'  => 17,
					'transport' => 'postMessage',
					'title'     => __( 'Image Size', 'astra' ),
					'divider'   => array( 'ast_class' => 'ast-top-dotted-divider' ),
					'control'   => 'ast-select',
					'choices'   => astra_get_site_image_sizes(),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[blog-post-color-divider]',
					'section'  => 'section-blog',
					'title'    => __( 'Post Cards', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 1,
					'context'  => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-layout]',
							'operator' => '===',
							'value'    => 'blog-layout-6',
						),
					),
				),

				array(
					'name'      => ASTRA_THEME_SETTINGS . '[post-card-featured-overlay]',
					'default'   => astra_get_option( 'post-card-featured-overlay' ),
					'type'      => 'control',
					'control'   => 'ast-color',
					'section'   => 'section-blog',
					'priority'  => 2.5,
					'title'     => __( 'Background Overlay', 'astra' ),
					'transport' => 'postMessage',
					'context'   => array(
						Astra_Builder_Helper::$design_tab_config,
						'relation' => 'AND',
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-layout]',
							'operator' => '===',
							'value'    => 'blog-layout-6',
						),
					),
				),

				/**
				 * Option: Card Radius
				 */
				array(
					'name'           => ASTRA_THEME_SETTINGS . '[post-card-border-radius]',
					'default'        => astra_get_option( 'post-card-border-radius' ),
					'type'           => 'control',
					'control'        => 'ast-responsive-spacing',
					'transport'      => 'postMessage',
					'section'        => 'section-blog',
					'title'          => __( 'Border Radius', 'astra' ),
					'suffix'         => 'px',
					'priority'       => $if_astra_addon ? 144 : 2.5,
					'divider'        => array( 'ast_class' => 'ast-section-spacing' ),
					'context'        => Astra_Builder_Helper::$design_tab,
					'linked_choices' => true,
					'unit_choices'   => array( 'px', 'em', '%' ),
					'choices'        => array(
						'top'    => __( 'Top', 'astra' ),
						'right'  => __( 'Right', 'astra' ),
						'bottom' => __( 'Bottom', 'astra' ),
						'left'   => __( 'Left', 'astra' ),
					),
					'connected'      => false,
				),

				/**
				 * Option: Blog Category Style
				 */
				array(
					'name'       => 'blog-category-style',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'    => 'section-blog',
					'title'      => __( 'Style', 'astra' ),
					'default'    => astra_get_option( 'blog-category-style' ),
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'linked'     => 'category',
					'priority'   => 75,
					'transport'  => 'refresh',
					'choices'    => array(
						'default'   => __( 'Default', 'astra' ),
						'badge'     => __( 'Badge', 'astra' ),
						'underline' => __( 'Underline', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Blog Tag Style
				 */
				array(
					'name'       => 'blog-tag-style',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'    => 'section-blog',
					'title'      => __( 'Style', 'astra' ),
					'default'    => astra_get_option( 'blog-tag-style' ),
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'transport'  => 'postMessage',
					'linked'     => 'tag',
					'priority'   => 75,
					'choices'    => array(
						'default'   => __( 'Default', 'astra' ),
						'badge'     => __( 'Badge', 'astra' ),
						'underline' => __( 'Underline', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),


				/**
				 * Option: Blog Meta Category Divider Type
				 */
				array(
					'name'       => 'blog-post-meta-divider-type',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
					'section'    => 'section-blog',
					'title'      => __( 'Divider Type', 'astra' ),
					'default'    => astra_get_option( 'blog-post-meta-divider-type' ),
					'type'       => 'sub-control',
					'transport'  => 'postMessage',
					'control'    => 'ast-selector',
					'linked'     => 'title-meta',
					'priority'   => 75,
					'choices'    => array(
						'/'    => '/',
						'-'    => '-',
						'|'    => '|',
						'•'    => '•',
						'none' => __( 'None', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Blog Meta Category Style
				 */
				array(
					'name'       => 'blog-meta-category-style',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'section'    => 'section-blog',
					'title'      => __( 'Style', 'astra' ),
					'default'    => astra_get_option( 'blog-meta-category-style' ),
					'type'       => 'sub-control',
					'transport'  => 'postMessage',
					'control'    => 'ast-selector',
					'linked'     => 'category',
					'priority'   => 75,
					'choices'    => array(
						'default'   => __( 'Default', 'astra' ),
						'underline' => __( 'Underline', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Blog Meta Tag Style
				 */
				array(
					'name'       => 'blog-meta-tag-style',
					'parent'     => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'section'    => 'section-blog',
					'title'      => __( 'Style', 'astra' ),
					'default'    => astra_get_option( 'blog-meta-tag-style' ),
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'transport'  => 'postMessage',
					'linked'     => 'tag',
					'priority'   => 75,
					'choices'    => array(
						'default'   => __( 'Default', 'astra' ),
						'underline' => __( 'Underline', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
				),
			);

			$_configs[] = array(
				'name'        => 'section-blog-ast-context-tabs',
				'section'     => 'section-blog',
				'type'        => 'control',
				'control'     => 'ast-builder-header-control',
				'priority'    => 0,
				'description' => '',
			);

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( ! defined( 'ASTRA_EXT_VER' ) || ( defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'blog-pro' ) ) ) {

				$_configs[] = array(
					'name'              => ASTRA_THEME_SETTINGS . '[blog-meta]',
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-blog',
					'default'           => astra_get_option( 'blog-meta' ),
					'priority'          => 52,
					'context'           => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
							'operator' => 'contains',
							'value'    => 'title-meta',
						),
					),
					'title'             => __( 'Meta', 'astra' ),
					'choices'           => array(
						'comments' => __( 'Comments', 'astra' ),
						'category' => __( 'Categories', 'astra' ),
						'author'   => __( 'Author', 'astra' ),
						'date'     => array(
							'clone'       => false,
							'is_parent'   => true,
							'main_index'  => 'date',
							'clone_limit' => 1,
							'title'       => __( 'Date', 'astra' ),
						),
						'tag'      => __( 'Tags', 'astra' ),
					),
					'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
				);
			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Blog_Layout_Configs();
