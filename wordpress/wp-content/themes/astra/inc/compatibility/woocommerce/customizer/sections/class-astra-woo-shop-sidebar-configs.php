<?php
/**
 * Content Spacing Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Woo_Shop_Sidebar_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Woo_Shop_Sidebar_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-WooCommerce Shop Sidebar Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				/**
				 * Option: Sidebar Layout.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[woocommerce-sidebar-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => 'section-woo-general',
					'default'           => astra_get_option( 'woocommerce-sidebar-layout' ),
					'priority'          => 5,
					'title'             => __( 'Sidebar Layout', 'astra' ),
					'choices'           => array(
						'default'       => array(
							'label' => __( 'Default', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'layout-default', false ) : '',
						),
						'no-sidebar'    => array(
							'label' => __( 'No Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'no-sidebar', false ) : '',
						),
						'left-sidebar'  => array(
							'label' => __( 'Left Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'left-sidebar', false ) : '',
						),
						'right-sidebar' => array(
							'label' => __( 'Right Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'right-sidebar', false ) : '',
						),
					),
					'description'       => __( 'Sidebar will only apply when container layout is set to normal.', 'astra' ),
					'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Woocommerce Sidebar Style.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[woocommerce-sidebar-style]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-woo-general',
					'default'    => astra_get_option( 'woocommerce-sidebar-style', 'default' ),
					'priority'   => 5,
					'title'      => __( 'Sidebar Style', 'astra' ),
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-divider' ),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[shop-display-options-divider]',
					'section'  => 'woocommerce_product_catalog',
					'title'    => __( 'Shop Display Options', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 9.5,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing' ),
				),

			);

			return array_merge( $configurations, $_configs );
		}
	}
}

new Astra_Woo_Shop_Sidebar_Configs();



