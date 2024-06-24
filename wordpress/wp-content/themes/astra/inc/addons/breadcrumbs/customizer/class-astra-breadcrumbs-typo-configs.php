<?php
/**
 * Typography - Breadcrumbs Options for theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.7.0
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
 * Customizer Sanitizes
 *
 * @since 1.7.0
 */
if ( ! class_exists( 'Astra_Breadcrumbs_Typo_Configs' ) ) {

	/**
	 * Register Colors and Background - Breadcrumbs Options Customizer Configurations.
	 */
	class Astra_Breadcrumbs_Typo_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Colors and Background - Breadcrumbs Options Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.7.0
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				/*
				 * Breadcrumb Typography
				 */
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[section-breadcrumb-typo]',
					'default'   => astra_get_option( 'section-breadcrumb-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => esc_html__( 'Content Font', 'astra' ),
					'section'   => 'section-breadcrumb',
					'transport' => 'postMessage',
					'priority'  => 71,
					'context'   => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ?
							Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					),
					'divider'   => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
				),

				/**
				 * Option: Font Family
				 */
				array(
					'name'      => 'breadcrumb-font-family',
					'default'   => astra_get_option( 'breadcrumb-font-family' ),
					'type'      => 'sub-control',
					'parent'    => ASTRA_THEME_SETTINGS . '[section-breadcrumb-typo]',
					'section'   => 'section-breadcrumb',
					'control'   => 'ast-font',
					'font_type' => 'ast-font-family',
					'title'     => esc_html__( 'Font Family', 'astra' ),
					'connect'   => 'breadcrumb-font-weight',
					'priority'  => 5,
					'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				),

				/**
				 * Option: Font Weight
				 */
				array(
					'name'              => 'breadcrumb-font-weight',
					'control'           => 'ast-font',
					'type'              => 'sub-control',
					'parent'            => ASTRA_THEME_SETTINGS . '[section-breadcrumb-typo]',
					'section'           => 'section-breadcrumb',
					'font_type'         => 'ast-font-weight',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
					'default'           => astra_get_option( 'breadcrumb-font-weight' ),
					'title'             => esc_html__( 'Font Weight', 'astra' ),
					'connect'           => 'breadcrumb-font-family',
					'priority'          => 10,
					'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				),

				/**
				 * Option: Font Size
				 */

				array(
					'name'              => 'breadcrumb-font-size',
					'parent'            => ASTRA_THEME_SETTINGS . '[section-breadcrumb-typo]',
					'type'              => 'sub-control',
					'control'           => 'ast-responsive-slider',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
					'section'           => 'section-breadcrumb',
					'transport'         => 'postMessage',
					'title'             => esc_html__( 'Font Size', 'astra' ),
					'priority'          => 10,
					'default'           => astra_get_option( 'breadcrumb-font-size' ),
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
				),

				/**
				 * Option: Breadcrumb Content Font Extras
				 */
				array(
					'name'     => 'breadcrumb-font-extras',
					'type'     => 'sub-control',
					'parent'   => ASTRA_THEME_SETTINGS . '[section-breadcrumb-typo]',
					'control'  => 'ast-font-extras',
					'section'  => 'section-breadcrumb',
					'priority' => 25,
					'default'  => astra_get_option( 'breadcrumb-font-extras' ),
					'title'    => esc_html__( 'Line Height', 'astra' ),
				),

			);

			return array_merge( $configurations, $_configs );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
new Astra_Breadcrumbs_Typo_Configs();
