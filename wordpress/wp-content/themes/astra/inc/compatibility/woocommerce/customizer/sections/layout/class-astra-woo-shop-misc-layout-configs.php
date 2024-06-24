<?php
/**
 * WooCommerce Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 3.9.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Woo_Shop_Misc_Layout_Configs' ) ) {


	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Woo_Shop_Misc_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-WooCommerce Misc Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 3.9.2
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {


			$_configs = array(

				/**
				 * Option: Enable Quantity Plus and Minus.
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[single-product-plus-minus-button]',
					'default'     => astra_get_option( 'single-product-plus-minus-button' ),
					'type'        => 'control',
					'section'     => 'section-woo-misc',
					'title'       => __( 'Enable Quantity Plus and Minus', 'astra' ),
					'description' => __( 'Adds plus and minus buttons besides product quantity', 'astra' ),
					'priority'    => 59,
					'control'     => 'ast-toggle-control',
					'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
				),

			);

			/**
			 * Option: Adds tabs only if astra addons is enabled.
			 */
			if ( astra_has_pro_woocommerce_addon() ) {
				$_configs[] = array(
					'name'        => 'section-woo-general-tabs',
					'section'     => 'section-woo-misc',
					'type'        => 'control',
					'control'     => 'ast-builder-header-control',
					'priority'    => 0,
					'description' => '',
				);
			}

			if ( astra_showcase_upgrade_notices() ) {
				// Learn More link if Astra Pro is not activated.
				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-woo-misc-pro-items]',
					'type'     => 'control',
					'control'  => 'ast-upgrade',
					'renderAs' => 'list',
					'choices'  => array(
						'two'   => array(
							'title' => __( 'Modern input style', 'astra' ),
						),
						'one'   => array(
							'title' => __( 'Sale badge modifications', 'astra' ),
						),
						'three' => array(
							'title' => __( 'Ecommerce steps navigation', 'astra' ),
						),
						'four'  => array(
							'title' => __( 'Quantity updater designs', 'astra' ),
						),
						'five'  => array(
							'title' => __( 'Modern my-account page', 'astra' ),
						),
						'six'   => array(
							'title' => __( 'Downloads, Orders grid view', 'astra' ),
						),
						'seven' => array(
							'title' => __( 'Modern thank-you page design', 'astra' ),
						),
					),
					'section'  => 'section-woo-misc',
					'default'  => '',
					'priority' => 999,
					'title'    => __( 'Access extra conversion tools to make more profit from your eCommerce store', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					'context'  => array(),
				);
			}

			return array_merge( $configurations, $_configs );
		}
	}
}

new Astra_Woo_Shop_Misc_Layout_Configs();
