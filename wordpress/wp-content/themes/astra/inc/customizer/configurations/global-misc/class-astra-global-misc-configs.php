<?php
/**
 * Global Misc Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2022, Astra
 * @link        https://wpastra.com/
 * @since       Astra  4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Astra Global Misc Configurations.
 */
class Astra_Global_Misc_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register Astra Global Misc  Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 4.0.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$_configs = array(

			/**
			 * Option: Scroll to id.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[enable-scroll-to-id]',
				'default'  => astra_get_option( 'enable-scroll-to-id' ),
				'type'     => 'control',
				'control'  => 'ast-toggle-control',
				'title'    => __( 'Enable Smooth Scroll to ID', 'astra' ),
				'section'  => 'section-global-misc',
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				'priority' => 10,
			),
		);

		return array_merge( $configurations, $_configs );
	}
}

new Astra_Global_Misc_Configs();
