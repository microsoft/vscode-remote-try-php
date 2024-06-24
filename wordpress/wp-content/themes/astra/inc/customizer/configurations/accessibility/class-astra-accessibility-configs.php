<?php
/**
 * Accessibility Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2023, Astra
 * @link        https://wpastra.com/
 * @since       Astra 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Astra Accessibility Configurations.
 */
class Astra_Accessibility_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register Astra Accessibility Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 4.1.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$_configs = array(

			/**
			 * Option: Toggle for accessibility.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[site-accessibility-toggle]',
				'default'  => astra_get_option( 'site-accessibility-toggle' ),
				'type'     => 'control',
				'control'  => 'ast-toggle-control',
				'title'    => __( 'Site Accessibility', 'astra' ),
				'section'  => 'section-accessibility',
				'priority' => 1,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Highlight type.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[site-accessibility-highlight-type]',
				'default'  => astra_get_option( 'site-accessibility-highlight-type' ),
				'type'     => 'control',
				'control'  => 'ast-radio-icon',
				'priority' => 1,
				'title'    => __( 'Global Highlight', 'astra' ),
				'section'  => 'section-accessibility',
				'choices'  => array(
					'dotted' => array(
						'label' => __( 'Dotted', 'astra' ),
						'path'  => 'ellipsis',
					),
					'solid'  => array(
						'label' => __( 'Solid', 'astra' ),
						'path'  => 'minus',
					),
				),
				'divider'  => array( 'ast_class' => 'ast-top-dotted-divider' ),
				'context'  => array(
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[site-accessibility-toggle]',
						'operator' => '===',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Highlight color.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[site-accessibility-highlight-color]',
				'default'  => astra_get_option( 'site-accessibility-highlight-color' ),
				'type'     => 'control',
				'control'  => 'ast-color',
				'priority' => 1,
				'title'    => __( 'Color', 'astra' ),
				'section'  => 'section-accessibility',
				'divider'  => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
				'context'  => array(
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[site-accessibility-toggle]',
						'operator' => '===',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Highlight type.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[site-accessibility-highlight-input-type]',
				'default'  => astra_get_option( 'site-accessibility-highlight-input-type' ),
				'type'     => 'control',
				'control'  => 'ast-radio-icon',
				'priority' => 1,
				'title'    => __( 'Input Highlight', 'astra' ),
				'section'  => 'section-accessibility',
				'choices'  => array(
					'disable' => array(
						'label' => __( 'Disable', 'astra' ),
						'path'  => 'remove',
					),
					'dotted'  => array(
						'label' => __( 'Dotted', 'astra' ),
						'path'  => 'ellipsis',
					),
					'solid'   => array(
						'label' => __( 'Solid', 'astra' ),
						'path'  => 'minus',
					),
				),
				'context'  => array(
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[site-accessibility-toggle]',
						'operator' => '===',
						'value'    => true,
					),
				),
			),

			/**
			 * Option: Highlight color.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[site-accessibility-highlight-input-color]',
				'default'  => astra_get_option( 'site-accessibility-highlight-input-color' ),
				'type'     => 'control',
				'control'  => 'ast-color',
				'priority' => 1,
				'title'    => __( 'Color', 'astra' ),
				'section'  => 'section-accessibility',
				'context'  => array(
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[site-accessibility-toggle]',
						'operator' => '===',
						'value'    => true,
					),
				),
			),
		);

		return array_merge( $configurations, $_configs );
	}
}

new Astra_Accessibility_Configs();
