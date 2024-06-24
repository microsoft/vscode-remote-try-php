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

if ( ! class_exists( 'Astra_Woo_Shop_Single_Layout_Configs' ) ) {


	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Woo_Shop_Single_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-WooCommerce Shop Single Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$product_divider_title = astra_has_pro_woocommerce_addon() ? __( 'Product Structure Options', 'astra' ) : __( 'Product Options', 'astra' );


			$clonning_attr    = array();
			$add_to_cart_attr = array();

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( astra_has_pro_woocommerce_addon() ) {

				/**
				 * Single product extras control.
				 */
				$clonning_attr['summary-extras'] = array(
					'clone'       => false,
					'is_parent'   => true,
					'main_index'  => 'summary-extras',
					'clone_limit' => 2,
					'title'       => __( 'Extras', 'astra' ),
				);

			}

			/**
			 * Single product add to cart control.
			 */
			$add_to_cart_attr['add_cart'] = array(
				'clone'       => false,
				'is_parent'   => true,
				'main_index'  => 'add_cart',
				'clone_limit' => 2,
				'title'       => __( 'Add To Cart', 'astra' ),
			);

			/**
			 * Single product payment control.
			 */

			$clonning_attr['single-product-payments'] = array(
				'clone'       => false,
				'is_parent'   => true,
				'main_index'  => 'single-product-payments',
				'clone_limit' => 2,
				'title'       => __( 'Payments', 'astra' ),
			);

			$_configs = array(

				array(
					'name'        => 'section-woo-shop-single-ast-context-tabs',
					'section'     => 'section-woo-shop-single',
					'type'        => 'control',
					'control'     => 'ast-builder-header-control',
					'priority'    => 0,
					'description' => '',
				),

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-single-product-structure-divider]',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'Single Product Structure', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 15,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),


				/**
				 * Option: Single Post Meta
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-structure]',
					'default'           => astra_get_option( 'single-product-structure' ),
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-woo-shop-single',
					'priority'          => 15,
					'choices'           => array_merge(
						array(
							'title'   => __( 'Title', 'astra' ),
							'price'   => __( 'Price', 'astra' ),
							'ratings' => __( 'Ratings', 'astra' ),
						),
						$add_to_cart_attr,
						array(
							'short_desc' => __( 'Short Description', 'astra' ),
							'meta'       => __( 'Meta', 'astra' ),
							'category'   => __( 'Category', 'astra' ),
						),
						$clonning_attr
					),
					'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-single-product-structure-fields-divider]',
					'section'  => 'section-woo-shop-single',
					'title'    => $product_divider_title,
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 16,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				* Option: Disable Breadcrumb
				*/
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[single-product-breadcrumb-disable]',
					'section'  => 'section-woo-shop-single',
					'type'     => 'control',
					'control'  => 'ast-toggle-control',
					'default'  => astra_get_option( 'single-product-breadcrumb-disable' ),
					'title'    => __( 'Enable Breadcrumb', 'astra' ),
					'priority' => 16,
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Enable free shipping
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[single-product-enable-shipping]',
					'default'     => astra_get_option( 'single-product-enable-shipping' ),
					'type'        => 'control',
					'section'     => 'section-woo-shop-single',
					'title'       => __( 'Enable Shipping Text', 'astra' ),
					'description' => __( 'Adds shipping text next to the product price.', 'astra' ),
					'control'     => 'ast-toggle-control',
					'priority'    => 16,
				),

				/**
				* Option: Single page variation tab layout.
				*/
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[single-product-variation-tabs-layout]',
					'default'     => astra_get_option( 'single-product-variation-tabs-layout' ),
					'type'        => 'control',
					'section'     => 'section-woo-shop-single',
					'title'       => __( 'Product Variation Layout', 'astra' ),
					'description' => __( 'Changes single product variation layout to be displayed inline or stacked.', 'astra' ),
					'context'     => array(
						Astra_Builder_Helper::$general_tab_config,
					),
					'control'     => 'ast-selector',
					'priority'    => 17,
					'choices'     => array(
						'horizontal' => __( 'Inline', 'astra' ),
						'vertical'   => __( 'Stack', 'astra' ),
					),
					'renderAs'    => 'text',
					'responsive'  => false,
				),


				/**
				 * Option: Disable Transparent Header on WooCommerce Product pages
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[transparent-header-disable-woo-products]',
					'default'  => astra_get_option( 'transparent-header-disable-woo-products' ),
					'type'     => 'control',
					'section'  => 'section-transparent-header',
					'title'    => __( 'Disable on WooCommerce Product Pages?', 'astra' ),
					'context'  => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[transparent-header-enable]',
							'operator' => '==',
							'value'    => '1',
						),
					),
					'priority' => 26,
					'control'  => 'ast-toggle-control',
				),

				/**
				 * Option: Free shipping text
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[single-product-shipping-text]',
					'default'  => astra_get_option( 'single-product-shipping-text' ),
					'type'     => 'control',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'Shipping Text', 'astra' ),
					'context'  => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-enable-shipping]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'control'  => 'text',
					'priority' => 16,
					'divider'  => array( 'ast_class' => 'ast-bottom-spacing' ),
				),

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-divider]',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'Sticky Add To Cart', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 76,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				* Option: Sticky add to cart.
				*/
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
					'default'  => astra_get_option( 'single-product-sticky-add-to-cart' ),
					'type'     => 'control',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'Enable Sticky Add to Cart', 'astra' ),
					'control'  => 'ast-toggle-control',
					'priority' => 76,
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Sticky add to cart position.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-position]',
					'default'    => astra_get_option( 'single-product-sticky-add-to-cart-position' ),
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-woo-shop-single',
					'priority'   => 76,
					'title'      => __( 'Sticky Placement ', 'astra' ),
					'choices'    => array(
						'top'    => __( 'Top', 'astra' ),
						'bottom' => __( 'Bottom', 'astra' ),
					),
					'transport'  => 'postMessage',
					'renderAs'   => 'text',
					'responsive' => false,
					'context'    => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'divider'    => array( 'ast_class' => 'ast-top-dotted-divider' ),
				),


				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[woo-single-product-sticky-color-divider]',
					'section'  => 'section-woo-shop-single',
					'title'    => __( 'Sticky Add To Cart Colors', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 82,
					'settings' => array(),
					'context'  => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Sticky add to cart text color.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-text-color]',
					'default'           => astra_get_option( 'single-product-sticky-add-to-cart-text-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Text Color', 'astra' ),
					'context'           => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'priority'          => 82,
					'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Sticky add to cart background color.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-bg-color]',
					'default'           => astra_get_option( 'single-product-sticky-add-to-cart-bg-color' ),
					'type'              => 'control',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'transport'         => 'postMessage',
					'title'             => __( 'Background Color', 'astra' ),
					'context'           => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
					'priority'          => 82,
				),

				/**
				* Option: Sticky add to cart button text color.
				*/
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-color]',
					'default'   => astra_get_option( 'single-product-sticky-add-to-cart-btn-color' ),
					'type'      => 'control',
					'control'   => 'ast-color-group',
					'title'     => __( 'Button Text', 'astra' ),
					'section'   => 'section-woo-shop-single',
					'transport' => 'postMessage',
					'priority'  => 82,
					'context'   => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
				),

				/**
				 * Option: Link Color.
				 */
				array(
					'type'     => 'sub-control',
					'priority' => 76,
					'parent'   => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-color]',
					'section'  => 'section-woo-shop-single',
					'control'  => 'ast-color',
					'default'  => astra_get_option( 'single-product-sticky-add-to-cart-btn-n-color' ),
					'name'     => 'single-product-sticky-add-to-cart-btn-n-color',
					'title'    => __( 'Normal', 'astra' ),
					'tab'      => __( 'Normal', 'astra' ),
				),

				/**
				 * Option: Link Hover Color.
				 */
				array(
					'type'              => 'sub-control',
					'priority'          => 82,
					'parent'            => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-color]',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'default'           => astra_get_option( 'single-product-sticky-add-to-cart-btn-h-color' ),
					'transport'         => 'postMessage',
					'name'              => 'single-product-sticky-add-to-cart-btn-h-color',
					'title'             => __( 'Hover', 'astra' ),
					'tab'               => __( 'Hover', 'astra' ),
				),

				/**
				 * Option: Sticky add to cart button background color.
				 */
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-bg-color]',
					'default'   => astra_get_option( 'single-product-sticky-add-to-cart-btn-bg-color' ),
					'type'      => 'control',
					'control'   => 'ast-color-group',
					'title'     => __( 'Button Background', 'astra' ),
					'section'   => 'section-woo-shop-single',
					'transport' => 'postMessage',
					'priority'  => 82,
					'context'   => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => true,
						),
					),
				),

				/**
				 * Option: Link Color.
				 */
				array(
					'type'     => 'sub-control',
					'priority' => 82,
					'parent'   => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-bg-color]',
					'section'  => 'section-woo-shop-single',
					'control'  => 'ast-color',
					'default'  => astra_get_option( 'single-product-sticky-add-to-cart-btn-bg-n-color' ),
					'name'     => 'single-product-sticky-add-to-cart-btn-bg-n-color',
					'title'    => __( 'Normal', 'astra' ),
					'tab'      => __( 'Normal', 'astra' ),
				),

				/**
				 * Option: Link Hover Color.
				 */
				array(
					'type'              => 'sub-control',
					'priority'          => 82,
					'parent'            => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart-btn-bg-color]',
					'section'           => 'section-woo-shop-single',
					'control'           => 'ast-color',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
					'default'           => astra_get_option( 'single-product-sticky-add-to-cart-btn-bg-h-color' ),
					'transport'         => 'postMessage',
					'name'              => 'single-product-sticky-add-to-cart-btn-bg-h-color',
					'title'             => __( 'Hover', 'astra' ),
					'tab'               => __( 'Hover', 'astra' ),
				),

				/**
				 * Single product payment icon color style.
				 */
				array(
					'name'       => 'single-product-payment-icon-color',
					'parent'     => ASTRA_THEME_SETTINGS . '[single-product-structure]',
					'default'    => astra_get_option( 'single-product-payment-icon-color' ),
					'linked'     => 'single-product-payments',
					'type'       => 'sub-control',
					'control'    => 'ast-selector',
					'section'    => 'section-woo-shop-single',
					'priority'   => 5,
					'title'      => __( 'Choose Icon Colors', 'astra' ),
					'choices'    => array(
						'inherit'            => __( 'Default', 'astra' ),
						'inherit_text_color' => __( 'Grayscale', 'astra' ),
					),
					'transport'  => 'postMessage',
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Single product payment heading text.
				 */
				array(
					'name'      => 'single-product-payment-text',
					'parent'    => ASTRA_THEME_SETTINGS . '[single-product-structure]',
					'default'   => astra_get_option( 'single-product-payment-text' ),
					'linked'    => 'single-product-payments',
					'type'      => 'sub-control',
					'control'   => 'ast-text-input',
					'section'   => 'section-woo-shop-single',
					'priority'  => 5,
					'transport' => 'postMessage',
					'title'     => __( 'Payment Title', 'astra' ),
					'settings'  => array(),
				),



			);

			/**
			 * Single product extras list.
			 */
			$_configs[] = array(
				'name'        => 'single-product-payment-list',
				'parent'      => ASTRA_THEME_SETTINGS . '[single-product-structure]',
				'default'     => astra_get_option( 'single-product-payment-list' ),
				'linked'      => 'single-product-payments',
				'type'        => 'sub-control',
				'control'     => 'ast-list-icons',
				'section'     => 'section-woo-shop-single',
				'priority'    => 10,
				'divider'     => array( 'ast_class' => 'ast-bottom-divider' ),
				'disable'     => false,
				'input_attrs' => array(
					'text_control_label'       => __( 'Payment Title', 'astra' ),
					'text_control_placeholder' => __( 'Add payment title', 'astra' ),
				),
			);

			/**
			* Option: Button width option
			*/
			$_configs[] = array(
				'name'        => 'single-product-cart-button-width',
				'parent'      => ASTRA_THEME_SETTINGS . '[single-product-structure]',
				'default'     => astra_get_option( 'single-product-cart-button-width' ),
				'linked'      => 'add_cart',
				'type'        => 'sub-control',
				'control'     => 'ast-responsive-slider',
				'responsive'  => true,
				'section'     => 'section-woo-shop-single',
				'priority'    => 11,
				'title'       => __( 'Button Width', 'astra' ),
				'transport'   => 'postMessage',
				'suffix'      => '%',
				'input_attrs' => array(
					'min'  => 1,
					'step' => 1,
					'max'  => 100,
				),
			);

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( astra_has_pro_woocommerce_addon() ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$_configs[] = array(
					'name'        => 'single-product-cart-button-width',
					'parent'      => ASTRA_THEME_SETTINGS . '[single-product-structure]',
					'default'     => astra_get_option( 'single-product-cart-button-width' ),
					'linked'      => 'add_cart',
					'type'        => 'sub-control',
					'control'     => 'ast-responsive-slider',
					'responsive'  => true,
					'section'     => 'section-woo-shop-single',
					'priority'    => 11,
					'title'       => __( 'Button Width', 'astra' ),
					'transport'   => 'postMessage',
					'suffix'      => '%',
					'input_attrs' => array(
						'min'  => 1,
						'step' => 1,
						'max'  => 100,
					),
				);

			} else {
				$_configs[] = array(
					'name'        => ASTRA_THEME_SETTINGS . '[single-product-cart-button-width]',
					'default'     => astra_get_option( 'single-product-cart-button-width' ),
					'type'        => 'control',
					'transport'   => 'postMessage',
					'responsive'  => true,
					'control'     => 'ast-responsive-slider',
					'section'     => 'section-woo-shop-single',
					'title'       => __( 'Button Width', 'astra' ),
					'suffix'      => '%',
					'priority'    => 16,
					'input_attrs' => array(
						'min'  => 1,
						'step' => 1,
						'max'  => 100,
					),
					'divider'     => array( 'ast_class' => 'ast-top-section-divider ast-bottom-section-divider' ),
				);
			}

			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[sticky-add-to-cart-notice]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => 'section-woo-shop-single',
					'priority' => 5,
					'label'    => '',
					'help'     => __( 'Note: To get design settings make sure to enable sticky add to cart.', 'astra' ),
					'context'  => array(
						Astra_Builder_Helper::$design_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[single-product-sticky-add-to-cart]',
							'operator' => '==',
							'value'    => false,
						),
					),
				);

				if ( astra_showcase_upgrade_notices() ) {
					// Learn More link if Astra Pro is not activated.
					$_configs[] = array(
						'name'     => ASTRA_THEME_SETTINGS . '[ast-woo-single-product-pro-items]',
						'type'     => 'control',
						'control'  => 'ast-upgrade',
						'renderAs' => 'list',
						'choices'  => array(
							'two'   => array(
								'title' => __( 'More product galleries', 'astra' ),
							),
							'three' => array(
								'title' => __( 'Sticky product summary', 'astra' ),
							),
							'five'  => array(
								'title' => __( 'Product description layouts', 'astra' ),
							),
							'six'   => array(
								'title' => __( 'Related, Upsell product controls', 'astra' ),
							),
							'seven' => array(
								'title' => __( 'Extras option for product structure', 'astra' ),
							),
							'eight' => array(
								'title' => __( 'More typography options', 'astra' ),
							),
							'nine'  => array(
								'title' => __( 'More color options', 'astra' ),
							),
							'one'   => array(
								'title' => __( 'More design controls', 'astra' ),
							),
						),
						'section'  => 'section-woo-shop-single',
						'default'  => '',
						'priority' => 999,
						'title'    => __( 'Extra conversion options for store product pages means extra profit!', 'astra' ),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
						'context'  => array(),
					);
				}
			}

			return array_merge( $configurations, $_configs );

		}
	}
}

new Astra_Woo_Shop_Single_Layout_Configs();


