<?php
/**
 * Easy Digital Downloads Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Edd_Archive_Layout_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Edd_Archive_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-Easy Digital Downloads Shop Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.5.5
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$grid_ast_divider = ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'edd' ) ) ? array() : array( 'ast_class' => 'ast-top-section-divider' );
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$_configs = array(

				/**
				 * Option: Shop Columns
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[edd-archive-grids]',
					'type'              => 'control',
					'control'           => 'ast-responsive-slider',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
					'section'           => 'section-edd-archive',
					'default'           => astra_get_option(
						'edd-archive-grids',
						array(
							'desktop' => 4,
							'tablet'  => 3,
							'mobile'  => 2,
						)
					),
					'priority'          => 10,
					'title'             => __( 'Archive Columns', 'astra' ),
					'input_attrs'       => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 6,
					),
					'divider'           => $grid_ast_divider,
					'transport'         => 'postMessage',
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[edd-archive-product-structure-divider]',
					'section'  => 'section-edd-archive',
					'title'    => __( 'Product Structure', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 30,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: EDD Archive Post Meta
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[edd-archive-product-structure]',
					'type'              => 'control',
					'control'           => 'ast-sortable',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_multi_choices' ),
					'section'           => 'section-edd-archive',
					'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
					'default'           => astra_get_option( 'edd-archive-product-structure' ),
					'priority'          => 30,
					'title'             => __( 'Product Structure', 'astra' ),
					'description'       => __( 'The Image option cannot be sortable if the Product Style is selected to the List Style ', 'astra' ),
					'choices'           => array(
						'image'      => __( 'Image', 'astra' ),
						'category'   => __( 'Category', 'astra' ),
						'title'      => __( 'Title', 'astra' ),
						'price'      => __( 'Price', 'astra' ),
						'short_desc' => __( 'Short Description', 'astra' ),
						'add_cart'   => __( 'Add To Cart', 'astra' ),
					),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[edd-archive-button-divider]',
					'section'  => 'section-edd-archive',
					'title'    => __( 'Buttons', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 31,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing' ),
				),

				/**
				 * Option: Add to Cart button text
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[edd-archive-add-to-cart-button-text]',
					'type'     => 'control',
					'control'  => 'text',
					'section'  => 'section-edd-archive',
					'default'  => astra_get_option( 'edd-archive-add-to-cart-button-text' ),
					'priority' => 31,
					'title'    => __( 'Cart Button Text', 'astra' ),
					'context'  => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[edd-archive-product-structure]',
							'operator' => 'contains',
							'value'    => 'add_cart',
						),
					),
					'divider'  => array( 'ast_class' => 'ast-top-spacing ast-bottom-section-divider' ),
				),

				/**
				 * Option: Variable product button
				 */

				array(
					'name'       => ASTRA_THEME_SETTINGS . '[edd-archive-variable-button]',
					'default'    => astra_get_option( 'edd-archive-variable-button' ),
					'section'    => 'section-edd-archive',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'title'      => __( 'Variable Product Button', 'astra' ),
					'priority'   => 31,
					'choices'    => array(
						'button'  => __( 'Button', 'astra' ),
						'options' => __( 'Options', 'astra' ),
					),
					'transport'  => 'refresh',
					'renderAs'   => 'text',
					'responsive' => false,
					'context'    => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[edd-archive-product-structure]',
							'operator' => 'contains',
							'value'    => 'add_cart',
						),
					),
					'divider'    => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Variable product button text
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[edd-archive-variable-button-text]',
					'type'     => 'control',
					'control'  => 'text',
					'divider'  => array( 'ast_class' => 'ast-bottom-divider' ),
					'section'  => 'section-edd-archive',
					'default'  => astra_get_option( 'edd-archive-variable-button-text' ),
					'context'  => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[edd-archive-variable-button]',
							'operator' => '==',
							'value'    => 'button',
						),
					),
					'priority' => 31,
					'title'    => __( 'Variable Product Button Text', 'astra' ),
				),

				/**
				 * Option: Archive Content Width
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[edd-archive-width]',
					'default'    => astra_get_option( 'edd-archive-width' ),
					'section'    => 'section-edd-archive',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'title'      => __( 'Archive Content Width', 'astra' ),
					'divider'    => array( 'ast_class' => 'ast-top-section-divider' ),
					'priority'   => 220,
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'custom'  => __( 'Custom', 'astra' ),
					),
					'transport'  => 'postMessage',
					'renderAs'   => 'text',
					'responsive' => false,
				),

				/**
				 * Option: Enter Width
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[edd-archive-max-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'section'     => 'section-edd-archive',
					'default'     => astra_get_option( 'edd-archive-max-width' ),
					'priority'    => 225,
					'context'     => array(
						Astra_Builder_Helper::$general_tab_config,
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[edd-archive-width]',
							'operator' => '===',
							'value'    => 'custom',
						),
					),

					'title'       => __( 'Custom Width', 'astra' ),
					'transport'   => 'postMessage',
					'suffix'      => 'px',
					'input_attrs' => array(
						'min'  => 768,
						'step' => 1,
						'max'  => 1920,
					),
					'divider'     => array( 'ast_class' => 'ast-top-dotted-divider' ),
				),
			);

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {

				$_configs[] =

					/**
					 * Option: Learn More about Contant Typography
					 */
					array(
						'name'     => ASTRA_THEME_SETTINGS . '[edd-product-archive-button-link]',
						'type'     => 'control',
						'control'  => 'ast-button-link',
						'section'  => 'section-edd-archive',
						'priority' => 999,
						'title'    => __( 'View Astra Pro Features', 'astra' ),
						'url'      => ASTRA_PRO_CUSTOMIZER_UPGRADE_URL,
						'settings' => array(),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					);

			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;

		}
	}
}

new Astra_Edd_Archive_Layout_Configs();

