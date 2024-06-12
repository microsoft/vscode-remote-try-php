<?php
/**
 * EDD Cart Header Configuration.
 *
 * @author      Astra
 * @package     Astra
 * @copyright   Copyright (c) 2023, Astra
 * @link        https://wpastra.com/
 * @since       4.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register EDD Cart header builder Customizer Configurations.
 *
 * @param array $configurations Astra Customizer Configurations.
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_edd_cart_header_configuration( $configurations = array() ) {
	$_section = ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ? 'section-header-edd-cart' : 'section-edd-general';

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$_cart_total_divider = array( 'ast_class' => ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'edd' ) ) ? 'ast-top-section-divider' : 'ast-section-spacing' );

	$_configs = array(

		/**
		* EDD Cart section
		*/
		array(
			'name'     => $_section,
			'type'     => 'section',
			'priority' => 5,
			'title'    => __( 'EDD Cart', 'astra' ),
			'panel'    => 'panel-header-builder-group',
		),

		/**
		 * Option: Header cart total
		 */


		array(
			'name'      => ASTRA_THEME_SETTINGS . '[edd-header-cart-total-display]',
			'default'   => astra_get_option( 'edd-header-cart-total-display' ),
			'type'      => 'control',
			'section'   => $_section,
			'title'     => __( 'Display Cart Total', 'astra' ),
			'priority'  => 50,
			'transport' => 'postMessage',
			'partial'   => array(
				'selector'            => '.ast-header-edd-cart',
				'container_inclusive' => false,
				'render_callback'     => array( 'Astra_Builder_Header', 'header_edd_cart' ),
			),
			'divider'   => $_cart_total_divider,
			'control'   => 'ast-toggle-control',
			'context'   => Astra_Builder_Helper::$general_tab,
		),

		/**
		 * Option: Cart Title
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[edd-header-cart-title-display]',
			'default'   => astra_get_option( 'edd-header-cart-title-display' ),
			'type'      => 'control',
			'section'   => $_section,
			'title'     => __( 'Display Cart Title', 'astra' ),
			'priority'  => 55,
			'transport' => 'postMessage',
			'partial'   => array(
				'selector'            => '.ast-header-edd-cart',
				'container_inclusive' => false,
				'render_callback'     => array( 'Astra_Builder_Header', 'header_edd_cart' ),
			),
			'control'   => 'ast-toggle-control',
			'context'   => Astra_Builder_Helper::$general_tab,
		),
		/**
		 * Option: Icon Style
		 */
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-style]',
			'default'    => astra_get_option( 'edd-header-cart-icon-style' ),
			'type'       => 'control',
			'transport'  => 'postMessage',
			'section'    => $_section,
			'title'      => __( 'Style', 'astra' ),
			'control'    => 'ast-selector',
			'priority'   => 40,
			'choices'    => array(
				'outline' => __( 'Outline', 'astra' ),
				'fill'    => __( 'Fill', 'astra' ),
			),
			'divider'    => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
			'responsive' => false,
			'renderAs'   => 'text',
			'context'    => Astra_Builder_Helper::$design_tab,
		),

		/**
		 * Option: Background color
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-color]',
			'default'           => astra_get_option( 'edd-header-cart-icon-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Color', 'astra' ),
			'transport'         => 'postMessage',
			'section'           => $_section,
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-style]',
					'operator' => '!=',
					'value'    => 'none',
				),
			),
			'priority'          => 45,
		),

		/**
		 * Option: Border Radius
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-radius]',
			'default'     => astra_get_option( 'edd-header-cart-icon-radius' ),
			'type'        => 'control',
			'transport'   => 'postMessage',
			'section'     => $_section,
			'context'     => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-style]',
					'operator' => '!=',
					'value'    => 'none',
				),
			),
			'title'       => __( 'Border Radius', 'astra' ),
			'suffix'      => 'px',
			'control'     => 'ast-slider',
			'priority'    => 47,
			'divider'     => array( 'ast_class' => 'ast-top-section-divider' ),
			'input_attrs' => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 200,
			),
		),

		/**
		* Option: Icon color
		*/
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[transparent-header-edd-cart-icon-color]',
			'default'           => astra_get_option( 'transparent-header-edd-cart-icon-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'transport'         => 'postMessage',
			'title'             => __( 'EDD Cart Icon Color', 'astra' ),
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[edd-header-cart-icon-style]',
					'operator' => '!=',
					'value'    => 'none',
				),
			),
			'section'           => 'section-transparent-header',
			'priority'          => 95,
		),
	);

	if ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) {
		$_edd_configs = array(
			array(
				'name'        => $_section . '-ast-context-tabs',
				'section'     => $_section,
				'type'        => 'control',
				'control'     => 'ast-builder-header-control',
				'priority'    => 0,
				'description' => '',
			),

			/**
			 * Option: EDD cart tray Section divider
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[section-edd-cart-tray-divider]',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'section'  => $_section,
				'title'    => __( 'Cart Tray', 'astra' ),
				'priority' => 60,
				'settings' => array(),
				'context'  => Astra_Builder_Helper::$design_tab,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			// Option: Cart Link / Text Color.
			array(
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-color',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-cart-colors]',
				'section'    => $_section,
				'transport'  => 'postMessage',
				'name'       => 'header-edd-cart-text-color',
				'default'    => astra_get_option( 'header-edd-cart-text-color' ),
				'title'      => __( 'Text Color', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 65,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Cart Link / Text Color.
			array(
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-color',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-cart-colors]',
				'section'    => $_section,
				'transport'  => 'postMessage',
				'name'       => 'header-edd-cart-link-color',
				'default'    => astra_get_option( 'header-edd-cart-link-color' ),
				'title'      => __( 'Link Color', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 65,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Cart Background Color.
			array(
				'type'       => 'control',
				'section'    => $_section,
				'control'    => 'ast-responsive-color',
				'transport'  => 'postMessage',
				'name'       => ASTRA_THEME_SETTINGS . '[header-edd-cart-background-color]',
				'default'    => astra_get_option( 'header-edd-cart-background-color' ),
				'title'      => __( 'Background Color', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 65,
				'divider'    => array( 'ast_class' => 'ast-section-spacing' ),
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Cart Separator Color.
			array(
				'type'       => 'control',
				'section'    => $_section,
				'control'    => 'ast-responsive-color',
				'transport'  => 'postMessage',
				'name'       => ASTRA_THEME_SETTINGS . '[header-edd-cart-separator-color]',
				'default'    => astra_get_option( 'header-edd-cart-separator-color' ),
				'title'      => __( 'Separator Color', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 65,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Checkout Button colors.
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-text-colors]',
				'default'    => astra_get_option( 'header-edd-checkout-button-text-colors' ),
				'type'       => 'control',
				'control'    => 'ast-color-group',
				'title'      => __( 'Button Text', 'astra' ),
				'section'    => $_section,
				'transport'  => 'postMessage',
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
				'responsive' => true,
				'divider'    => array(
					'ast_class' => 'ast-top-dotted-divider',
					'ast_title' => __( 'Checkout', 'astra' ),
				),
			),
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-background-colors]',
				'default'    => astra_get_option( 'header-edd-checkout-button-background-colors' ),
				'type'       => 'control',
				'control'    => 'ast-color-group',
				'title'      => __( 'Button Background', 'astra' ),
				'section'    => $_section,
				'transport'  => 'postMessage',
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
				'responsive' => true,
			),
			// Option: Checkout Button Text Color.
			array(
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-color',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-text-colors]',
				'section'    => $_section,
				'transport'  => 'postMessage',
				'name'       => 'header-edd-checkout-btn-text-color',
				'default'    => astra_get_option( 'header-edd-checkout-btn-text-color' ),
				'title'      => __( 'Normal', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Checkout Button Background Color.
			array(
				'type'       => 'sub-control',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-background-colors]',
				'section'    => $_section,
				'control'    => 'ast-responsive-color',
				'transport'  => 'postMessage',
				'name'       => 'header-edd-checkout-btn-background-color',
				'default'    => astra_get_option( 'header-edd-checkout-btn-background-color' ),
				'title'      => __( 'Normal', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Checkout Button Hover Text Color.
			array(
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-color',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-text-colors]',
				'section'    => $_section,
				'transport'  => 'postMessage',
				'name'       => 'header-edd-checkout-btn-text-hover-color',
				'default'    => astra_get_option( 'header-edd-checkout-btn-text-hover-color' ),
				'title'      => __( 'Hover', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
			),

			// Option: Checkout Button Hover Background Color.
			array(
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-color',
				'transport'  => 'postMessage',
				'parent'     => ASTRA_THEME_SETTINGS . '[header-edd-checkout-button-background-colors]',
				'section'    => $_section,
				'name'       => 'header-edd-checkout-btn-bg-hover-color',
				'default'    => astra_get_option( 'header-edd-checkout-btn-bg-hover-color' ),
				'title'      => __( 'Hover', 'astra' ),
				'responsive' => true,
				'rgba'       => true,
				'priority'   => 75,
				'context'    => Astra_Builder_Helper::$design_tab,
			),
		);

		$configurations = array_merge( $configurations, $_edd_configs );

		$configurations = array_merge( $configurations, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section ) );

		$_configs = array_merge( $_configs, $configurations );
	}

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_edd_cart_header_configuration();
}
