<?php
/**
 * Astra Theme Customizer Configuration Builder.
 *
 * @package     astra-builder
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       3.0.0
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Astra_Customizer_Config_Base' ) ) {

	/**
	 * Register Builder Customizer Configurations.
	 *
	 * @since 3.0.0
	 */
	class Astra_Customizer_Footer_Menu_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Builder Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 3.0.0
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$_configs = astra_menu_footer_configuration();
			return array_merge( $configurations, $_configs );
		}
	}

	/**
	 * Kicking this off by creating 'new' object of this class.
	 */
	new Astra_Customizer_Footer_Menu_Configs();
}
