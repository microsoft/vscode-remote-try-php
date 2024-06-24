<?php
/**
 * Site Layout Option for Astra Theme.
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

if ( ! class_exists( 'Astra_Site_Layout_Configs' ) ) {

	/**
	 * Register Site Layout Customizer Configurations.
	 */
	class Astra_Site_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Site Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				array(
					'name'        => ASTRA_THEME_SETTINGS . '[site-content-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'default'     => astra_get_option( 'site-content-width' ),
					'section'     => 'section-container-layout',
					'priority'    => 10,
					'title'       => __( 'Container Width', 'astra' ),
					'divider'     => array( 'ast_class' => 'ast-top-section-divider' ),
					'context'     => ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'site-layouts' ) ) ? array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[site-layout]',
							'operator' => '==',
							'value'    => 'ast-full-width-layout',
						),
					) : array(),
					'suffix'      => 'px',
					'input_attrs' => array(
						'min'  => 768,
						'step' => 1,
						'max'  => 1920,
					),
				),
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[narrow-container-max-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'default'     => astra_get_option( 'narrow-container-max-width' ),
					'section'     => 'section-container-layout',
					'priority'    => 10,
					'title'       => __( 'Narrow Container Width', 'astra' ),
					'suffix'      => 'px',
					'divider'     => array( 'ast_class' => 'ast-top-section-spacing' ),
					'input_attrs' => array(
						'min'  => 400,
						'step' => 1,
						'max'  => 1000,
					),
				),
			);

			return array_merge( $configurations, $_configs );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
new Astra_Site_Layout_Configs();
