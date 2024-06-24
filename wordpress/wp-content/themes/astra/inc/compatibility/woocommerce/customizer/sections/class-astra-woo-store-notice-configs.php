<?php
/**
 * Store Notice options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2021, Astra
 * @link        https://wpastra.com/
 * @since       Astra 3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer WooCommerece store notice - customizer config initial setup.
 */
class Astra_Woo_Store_Notice_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register Astra-WooCommerce Shop Cart Layout Customizer Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 3.9.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$_configs = array(

			/**
			 * Option: Transparent Header Builder - HTML Elements configs.
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[woo-store-notice-colors-group]',
				'default'   => astra_get_option( 'woo-store-notice-colors-group' ),
				'type'      => 'control',
				'control'   => 'ast-color-group',
				'title'     => __( 'Color', 'astra' ),
				'section'   => 'woocommerce_store_notice',
				'transport' => 'postMessage',
				'priority'  => 50,
				'context'   => array(
					array(
						'setting'  => 'woocommerce_demo_store',
						'operator' => '==',
						'value'    => true,
					),
				),
				'divider'   => array( 'ast_class' => 'ast-top-divider ast-bottom-divider' ),
			),

			// Option: Text Color.
			array(
				'name'              => 'store-notice-text-color',
				'default'           => astra_get_option( 'store-notice-text-color' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[woo-store-notice-colors-group]',
				'type'              => 'sub-control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'section'           => 'woocommerce_store_notice',
				'transport'         => 'postMessage',
				'priority'          => 1,
				'title'             => __( 'Text', 'astra' ),
			),

			// Option: Background Color.
			array(
				'name'              => 'store-notice-background-color',
				'default'           => astra_get_option( 'store-notice-background-color' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[woo-store-notice-colors-group]',
				'type'              => 'sub-control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'section'           => 'woocommerce_store_notice',
				'transport'         => 'postMessage',
				'priority'          => 2,
				'title'             => __( 'Background', 'astra' ),
			),

			/**
			 * Option: Notice Position
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[store-notice-position]',
				'default'    => astra_get_option( 'store-notice-position' ),
				'type'       => 'control',
				'control'    => 'ast-selector',
				'section'    => 'woocommerce_store_notice',
				'transport'  => 'postMessage',
				'priority'   => 60,
				'title'      => __( 'Notice Position', 'astra' ),
				'choices'    => array(
					'hang-over-top' => __( 'Hang Over Top', 'astra' ),
					'top'           => __( 'Top', 'astra' ),
					'bottom'        => __( 'Bottom', 'astra' ),
				),
				'context'    => array(
					array(
						'setting'  => 'woocommerce_demo_store',
						'operator' => '==',
						'value'    => true,
					),
				),
				'renderAs'   => 'text',
				'responsive' => false,
			),
		);

		return array_merge( $configurations, $_configs );
	}
}

new Astra_Woo_Store_Notice_Configs();
