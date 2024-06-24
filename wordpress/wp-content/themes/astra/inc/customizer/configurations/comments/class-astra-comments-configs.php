<?php
/**
 * Comments options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2023, Astra
 * @link        https://wpastra.com/
 * @since       Astra 4.6.0
 */

if ( ! class_exists( 'Astra_Comments_Configs' ) ) {

	/**
	 * Register Comments Customizer Configurations.
	 */
	class Astra_Comments_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Comments Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 3.8.0
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$parent_section = 'section-blog-single';
			$_configs       = array(
				array(
					'name'        => 'comments-section-ast-context-tabs',
					'section'     => 'ast-sub-section-comments',
					'type'        => 'control',
					'control'     => 'ast-builder-header-control',
					'priority'    => 0,
					'description' => '',
					'context'     => array(),
				),
				array(
					'name'     => 'ast-sub-section-comments',
					'title'    => __( 'Comments', 'astra' ),
					'type'     => 'section',
					'section'  => $parent_section,
					'panel'    => '',
					'priority' => 1,
				),
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[comments-single-section-heading]',
					'section'  => $parent_section,
					'type'     => 'control',
					'control'  => 'ast-heading',
					'title'    => __( 'Comments', 'astra' ),
					'priority' => 20,
				),
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[enable-comments-area]',
					'type'     => 'control',
					'default'  => astra_get_option( 'enable-comments-area' ),
					'control'  => 'ast-section-toggle',
					'section'  => $parent_section,
					'priority' => 20,
					'linked'   => 'ast-sub-section-comments',
					'linkText' => __( 'Comments', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-bottom-divider ast-bottom-section-divider' ),
				),
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[comments-box-placement]',
					'default'     => astra_get_option( 'comments-box-placement' ),
					'type'        => 'control',
					'section'     => 'ast-sub-section-comments',
					'priority'    => 20,
					'title'       => __( 'Section Placement', 'astra' ),
					'control'     => 'ast-selector',
					'description' => __( 'Decide whether to isolate or integrate the module with the entry content area.', 'astra' ),
					'choices'     => array(
						''        => __( 'Default', 'astra' ),
						'inside'  => __( 'Contained', 'astra' ),
						'outside' => __( 'Separated', 'astra' ),
					),
					'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
					'context'     => Astra_Builder_Helper::$general_tab,
					'responsive'  => false,
					'renderAs'    => 'text',
				),
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[comments-box-container-width]',
					'default'    => astra_get_option( 'comments-box-container-width' ),
					'type'       => 'control',
					'section'    => 'ast-sub-section-comments',
					'priority'   => 20,
					'title'      => __( 'Container Structure', 'astra' ),
					'control'    => 'ast-selector',
					'choices'    => array(
						'narrow' => __( 'Narrow', 'astra' ),
						''       => __( 'Full Width', 'astra' ),
					),
					'context'    => array(
						Astra_Builder_Helper::$general_tab_config,
						'relation' => 'AND',
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[comments-box-placement]',
							'operator' => '==',
							'value'    => 'outside',
						),
					),
					'divider'    => array( 'ast_class' => 'ast-top-section-spacing' ),
					'responsive' => false,
					'renderAs'   => 'text',
				),
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[comment-form-position]',
					'default'    => astra_get_option( 'comment-form-position' ),
					'type'       => 'control',
					'section'    => 'ast-sub-section-comments',
					'priority'   => 20,
					'title'      => __( 'Form Position', 'astra' ),
					'control'    => 'ast-selector',
					'choices'    => array(
						'below' => __( 'Below Comments', 'astra' ),
						'above' => __( 'Above Comments', 'astra' ),
					),
					'context'    => Astra_Builder_Helper::$general_tab,
					'divider'    => array( 'ast_class' => 'ast-top-section-divider' ),
					'responsive' => false,
					'renderAs'   => 'text',
				),
			);

			$_configs = array_merge( $_configs, Astra_Extended_Base_Configuration::prepare_section_spacing_border_options( 'ast-sub-section-comments', true ) );

			return array_merge( $configurations, $_configs );
		}
	}
}

/**
 * Kicking this off by creating new instance.
 */
new Astra_Comments_Configs();
