<?php
/**
 * Styling Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Archive_Typo_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Archive_Typo_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Archive Typography Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array();

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {

				$_configs = array(

					/**
					 * Option: Astra Pro items for blog pro.
					 */
					array(
						'name'     => ASTRA_THEME_SETTINGS . '[ast-blog-pro-items]',
						'type'     => 'control',
						'control'  => 'ast-upgrade',
						'renderAs' => 'list',
						'choices'  => array(
							'one'    => array(
								'title' => __( 'Posts Filter', 'astra' ),
							),
							'eleven' => array(
								'title' => __( 'Posts Reveal Effect', 'astra' ),
							),
							'two'    => array(
								'title' => __( 'Grid, Masonry layout', 'astra' ),
							),
							'twelve' => array(
								'title' => __( 'Extended Meta Style Options', 'astra' ),
							),
							'three'  => array(
								'title' => __( 'Custom featured images size', 'astra' ),
							),
							'four'   => array(
								'title' => __( 'Archive pagination options', 'astra' ),
							),
							'six'    => array(
								'title' => __( 'Extended typography options', 'astra' ),
							),
							'seven'  => array(
								'title' => __( 'Extended spacing options', 'astra' ),
							),
							'eight'  => array(
								'title' => __( 'Archive read time', 'astra' ),
							),
							'nine'   => array(
								'title' => __( 'Archive excerpt options', 'astra' ),
							),
							'ten'    => array(
								'title' => __( 'Extended spacing options', 'astra' ),
							),
						),
						'section'  => 'section-blog',
						'default'  => '',
						'priority' => 999,
						'context'  => array(),
						'title'    => __( 'Take your blog to the next level with powerful design features.', 'astra' ),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					),
				);
			}

			if ( ! defined( 'ASTRA_EXT_VER' ) || ( defined( 'ASTRA_EXT_VER' ) && ! Astra_Ext_Extension::is_active( 'typography' ) ) ) {
				$new_configs = array(
					/**
					 * Option: Blog - Post Title Font Size
					 */
					array(
						'name'              => ASTRA_THEME_SETTINGS . '[font-size-page-title]',
						'control'           => 'ast-responsive-slider',
						'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
						'section'           => 'section-blog',
						'type'              => 'control',
						'transport'         => 'postMessage',
						'title'             => __( 'Post Title Size', 'astra' ),
						'priority'          => 140,
						'default'           => astra_get_option( 'font-size-page-title' ),
						'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
						'input_attrs'       => array(
							'px'  => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 200,
							),
							'em'  => array(
								'min'  => 0,
								'step' => 0.01,
								'max'  => 20,
							),
							'vw'  => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 25,
							),
							'rem' => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 20,
							),
						),
						'context'           => Astra_Builder_Helper::$design_tab,
						'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
					),
					array(
						'name'              => ASTRA_THEME_SETTINGS . '[font-size-post-meta]',
						'control'           => 'ast-responsive-slider',
						'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
						'section'           => 'section-blog',
						'type'              => 'control',
						'transport'         => 'postMessage',
						'title'             => __( 'Meta Font Size', 'astra' ),
						'priority'          => 140,
						'default'           => astra_get_option( 'font-size-post-meta' ),
						'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
						'input_attrs'       => array(
							'px'  => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 200,
							),
							'em'  => array(
								'min'  => 0,
								'step' => 0.01,
								'max'  => 20,
							),
							'vw'  => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 25,
							),
							'rem' => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 20,
							),
						),
						'context'           => Astra_Builder_Helper::$design_tab,
						'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
					),
					array(
						'name'              => ASTRA_THEME_SETTINGS . '[font-size-post-tax]',
						'control'           => 'ast-responsive-slider',
						'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
						'section'           => 'section-blog',
						'type'              => 'control',
						'transport'         => 'postMessage',
						'title'             => __( 'Taxonomy Font', 'astra' ),
						'priority'          => 140,
						'default'           => astra_get_option( 'font-size-post-tax' ),
						'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
						'input_attrs'       => array(
							'px'  => array(
								'min'  => 0,
								'step' => 1,
								'max'  => 200,
							),
							'em'  => array(
								'min'  => 0,
								'step' => 0.01,
								'max'  => 20,
							),
							'vw'  => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 25,
							),
							'rem' => array(
								'min'  => 0,
								'step' => 0.1,
								'max'  => 20,
							),
						),
						'context'           => array(
							Astra_Builder_Helper::$design_tab_config,
							array(
								'relation' => 'OR',
								array(
									'setting'  => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
									'operator' => 'contains',
									'value'    => 'category',
								),
								array(
									'setting'  => ASTRA_THEME_SETTINGS . '[blog-post-structure]',
									'operator' => 'contains',
									'value'    => 'tag',
								),
								array(
									'setting'  => ASTRA_THEME_SETTINGS . '[blog-meta]',
									'operator' => 'contains',
									'value'    => 'category',
								),
								array(
									'setting'  => ASTRA_THEME_SETTINGS . '[blog-meta]',
									'operator' => 'contains',
									'value'    => 'tag',
								),
							),
						),
						'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
					),
				);
				$_configs    = array_merge( $_configs, $new_configs );
			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Archive_Typo_Configs();
