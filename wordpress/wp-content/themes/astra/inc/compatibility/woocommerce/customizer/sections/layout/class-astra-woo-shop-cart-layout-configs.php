<?php
/**
 * WooCommerce Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Woo_Shop_Cart_Layout_Configs' ) ) {


	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Woo_Shop_Cart_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-WooCommerce Shop Cart Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(
				/**
				 * Option: Enable checkout button text
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[woo-enable-cart-button-text]',
					'default'     => astra_get_option( 'woo-enable-cart-button-text' ),
					'type'        => 'control',
					'section'     => 'section-woo-shop-cart',
					'title'       => __( 'Change Cart Button Text', 'astra' ),
					'description' => __( 'Add custom text for cart button', 'astra' ),
					'control'     => 'ast-toggle-control',
					'priority'    => 2,
				),

				/**
				 * Option: Checkout
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-cart-button-text]',
					'default'  => astra_get_option( 'woo-cart-button-text' ),
					'type'     => 'control',
					'section'  => 'section-woo-shop-cart',
					'title'    => __( 'Cart Button Text', 'astra' ),
					'context'  => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[woo-enable-cart-button-text]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'control'  => 'text',
					'priority' => 2,
				),

				/**
				 * Option: Cart upsells
				 *
				 * Enable Cross-sells - in the code it is refrenced as upsells rather than cross-sells.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[enable-cart-upsells]',
					'section'  => 'section-woo-shop-cart',
					'type'     => 'control',
					'control'  => 'ast-toggle-control',
					'default'  => astra_get_option( 'enable-cart-upsells' ),
					'title'    => __( 'Enable Cross-sells', 'astra' ),
					'priority' => 2.7,
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),
			);

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '8.3', '>=' ) ) {
				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-block-incompatible-cart-notice]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => 'section-woo-shop-cart',
					'priority' => 1,
					'label'    => '',
					'help'     => '<p style="margin-bottom: 20px;>"><strong>' . __( 'Note:', 'astra' ) . '</strong>' . __( ' Certain Cart page options may not work smoothly on the block editor based Cart page. For best results with these features, prefer using a shortcode based Cart page.', 'astra' ) . '</p>',
				);

				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-block-incompatible-checkout-notice]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => 'woocommerce_checkout',
					'priority' => 1,
					'label'    => '',
					'help'     => '<p style="margin-bottom: 20px;>"><strong>' . __( 'Note:', 'astra' ) . '</strong>' . __( ' Certain Checkout page options may not work smoothly on the block editor based Checkout page. For best results with these features, prefer using a shortcode-based Checkout page.', 'astra' ) . '</p>',
				);
			}

			if ( astra_showcase_upgrade_notices() ) {
				// Learn More link if Astra Pro is not activated.
				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-woo-cart-pro-items]',
					'type'     => 'control',
					'control'  => 'ast-upgrade',
					'renderAs' => 'list',
					'choices'  => array(
						'two'   => array(
							'title' => __( 'Modern cart layout', 'astra' ),
						),
						'one'   => array(
							'title' => __( 'Sticky cart totals', 'astra' ),
						),
						'three' => array(
							'title' => __( 'Real-time quantity updater', 'astra' ),
						),
					),
					'section'  => 'section-woo-shop-cart',
					'default'  => '',
					'priority' => 999,
					'title'    => __( 'Convert more, earn more with extensive cart conversion features', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					'context'  => array(),
				);

				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-woo-checkout-pro-items]',
					'type'     => 'control',
					'control'  => 'ast-upgrade',
					'renderAs' => 'list',
					'choices'  => array(
						'two'   => array(
							'title' => __( 'Modern layout', 'astra' ),
						),
						'one'   => array(
							'title' => __( 'Multi-column layouts', 'astra' ),
						),
						'three' => array(
							'title' => __( 'Modern order received layout', 'astra' ),
						),
						'four'  => array(
							'title' => __( 'Sticky order review', 'astra' ),
						),
						'five'  => array(
							'title' => __( 'Two-step checkout', 'astra' ),
						),
						'six'   => array(
							'title' => __( 'Order note, Coupon field control', 'astra' ),
						),
						'seven' => array(
							'title' => __( 'Distraction free checkout', 'astra' ),
						),
						'eight' => array(
							'title' => __( 'Persistent checkout form data', 'astra' ),
						),
						'nine'  => array(
							'title' => __( 'Text form options', 'astra' ),
						),
						'ten'   => array(
							'title' => __( 'Summary, Payment background', 'astra' ),
						),
					),
					'section'  => 'woocommerce_checkout',
					'default'  => '',
					'priority' => 999,
					'title'    => __( 'Encourage last-minute purchases with extra conversion options at checkout', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					'context'  => array(),
				);
			}

			return array_merge( $configurations, $_configs );
		}
	}
}

new Astra_Woo_Shop_Cart_Layout_Configs();
