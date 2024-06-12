<?php
/**
 * Scroll To Top Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2022, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       4.0.0
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if Customizer config base class does not exist.
if ( ! class_exists( 'Astra_Customizer_Config_Base' ) ) {
	return;
}

/**
 * Register Scroll To Top Customizer Configurations.
 */
class Astra_Scroll_To_Top_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register Scroll To Top Customizer Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 4.0.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$_configs = array(

			/**
			 * Option: Enable Scroll To Top
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
				'default'  => astra_get_option( 'scroll-to-top-enable' ),
				'type'     => 'control',
				'section'  => 'section-scroll-to-top',
				'title'    => __( 'Enable Scroll to Top', 'astra' ),
				'priority' => 1,
				'control'  => 'ast-toggle-control',
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Scroll to Top Display On
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[scroll-to-top-on-devices]',
				'default'    => astra_get_option( 'scroll-to-top-on-devices' ),
				'type'       => 'control',
				'control'    => 'ast-selector',
				'section'    => 'section-scroll-to-top',
				'priority'   => 10,
				'title'      => __( 'Display On', 'astra' ),
				'choices'    => array(
					'desktop' => __( 'Desktop', 'astra' ),
					'mobile'  => __( 'Mobile', 'astra' ),
					'both'    => __( 'Desktop + Mobile', 'astra' ),
				),
				'renderAs'   => 'text',
				'responsive' => false,
				'divider'    => array( 'ast_class' => 'ast-top-section-divider ast-bottom-section-divider' ),
				'context'    => array(
					'relation' => 'AND',
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Scroll to Top Position
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[scroll-to-top-icon-position]',
				'default'    => astra_get_option( 'scroll-to-top-icon-position' ),
				'type'       => 'control',
				'control'    => 'ast-selector',
				'transport'  => 'postMessage',
				'section'    => 'section-scroll-to-top',
				'title'      => __( 'Position', 'astra' ),
				'choices'    => array(
					'left'  => __( 'Left', 'astra' ),
					'right' => __( 'Right', 'astra' ),
				),
				'priority'   => 11,
				'responsive' => false,
				'renderAs'   => 'text',
				'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
				'context'    => array(
					'relation' => 'AND',
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Scroll To Top Icon Size
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[scroll-to-top-icon-size]',
				'default'   => astra_get_option( 'scroll-to-top-icon-size' ),
				'type'      => 'control',
				'control'   => 'ast-slider',
				'transport' => 'postMessage',
				'section'   => 'section-scroll-to-top',
				'title'     => __( 'Icon Size', 'astra' ),
				'suffix'    => 'px',
				'priority'  => 12,
				'context'   => array(
					'relation' => 'AND',
					Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
			),

			array(
				'name'     => ASTRA_THEME_SETTINGS . '[scroll-on-top-color-group]',
				'default'  => astra_get_option( 'scroll-on-top-color-group' ),
				'type'     => 'control',
				'control'  => 'ast-color-group',
				'title'    => __( 'Icon Color', 'astra' ),
				'section'  => 'section-scroll-to-top',
				'context'  => array(
					'relation' => 'AND',
					( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 1,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			array(
				'name'      => ASTRA_THEME_SETTINGS . '[scroll-on-top-bg-color-group]',
				'default'   => astra_get_option( 'scroll-on-top-bg-color-group' ),
				'type'      => 'control',
				'control'   => 'ast-color-group',
				'title'     => __( 'Background Color', 'astra' ),
				'section'   => 'section-scroll-to-top',
				'transport' => 'postMessage',
				'context'   => array(
					'relation' => 'AND',
					( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority'  => 1,
			),

			/**
			 * Option: Scroll To Top Radius
			 */
			array(
				'name'           => ASTRA_THEME_SETTINGS . '[scroll-to-top-icon-radius-fields]',
				'default'        => astra_get_option( 'scroll-to-top-icon-radius-fields' ),
				'type'           => 'control',
				'control'        => 'ast-responsive-spacing',
				'transport'      => 'postMessage',
				'section'        => 'section-scroll-to-top',
				'title'          => __( 'Border Radius', 'astra' ),
				'suffix'         => 'px',
				'priority'       => 1,
				'divider'        => array( 'ast_class' => 'ast-top-section-divider' ),
				'context'        => array(
					'relation' => 'AND',
					( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '==',
						'value'    => true,
					),
				),
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
			 * Option: Icon Color
			 */
			array(
				'name'              => 'scroll-to-top-icon-color',
				'default'           => astra_get_option( 'scroll-to-top-icon-color' ),
				'type'              => 'sub-control',
				'priority'          => 1,
				'parent'            => ASTRA_THEME_SETTINGS . '[scroll-on-top-color-group]',
				'section'           => 'section-scroll-to-top',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Color', 'astra' ),
			),

			/**
			 * Option: Icon Background Color
			 */
			array(
				'name'              => 'scroll-to-top-icon-bg-color',
				'default'           => astra_get_option( 'scroll-to-top-icon-bg-color' ),
				'type'              => 'sub-control',
				'priority'          => 1,
				'parent'            => ASTRA_THEME_SETTINGS . '[scroll-on-top-bg-color-group]',
				'section'           => 'section-scroll-to-top',
				'transport'         => 'postMessage',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'title'             => __( 'Color', 'astra' ),
			),

			/**
			 * Option: Icon Hover Color
			 */
			array(
				'name'              => 'scroll-to-top-icon-h-color',
				'default'           => astra_get_option( 'scroll-to-top-icon-h-color' ),
				'type'              => 'sub-control',
				'priority'          => 1,
				'parent'            => ASTRA_THEME_SETTINGS . '[scroll-on-top-color-group]',
				'section'           => 'section-scroll-to-top',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Hover Color', 'astra' ),
			),

			/**
			 * Option: Link Hover Background Color
			 */
			array(
				'name'              => 'scroll-to-top-icon-h-bg-color',
				'default'           => astra_get_option( 'scroll-to-top-icon-h-bg-color' ),
				'type'              => 'sub-control',
				'priority'          => 1,
				'parent'            => ASTRA_THEME_SETTINGS . '[scroll-on-top-bg-color-group]',
				'section'           => 'section-scroll-to-top',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Hover Color', 'astra' ),
			),
		);

		if ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) {
			$_configs[] = array(
				'name'        => 'section-scroll-to-top-ast-context-tabs',
				'section'     => 'section-scroll-to-top',
				'type'        => 'control',
				'control'     => 'ast-builder-header-control',
				'priority'    => 0,
				'description' => '',
			);
			$_configs[] = array(
				'name'     => ASTRA_THEME_SETTINGS . '[enable-scroll-to-top-notice]',
				'type'     => 'control',
				'control'  => 'ast-description',
				'section'  => 'section-scroll-to-top',
				'priority' => 1,
				'label'    => '',
				'help'     => __( 'Note: To get design settings in action make sure to enable Scroll to Top.', 'astra' ),
				'context'  => array(
					'relation' => 'AND',
					Astra_Builder_Helper::$design_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[scroll-to-top-enable]',
						'operator' => '!=',
						'value'    => true,
					),
				),
			);
		}

		$configurations = array_merge( $configurations, $_configs );

		return $configurations;
	}
}

/** Creating instance for getting customizer configs. */
new Astra_Scroll_To_Top_Configs();
