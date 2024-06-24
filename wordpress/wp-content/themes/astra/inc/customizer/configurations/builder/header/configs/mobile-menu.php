<?php
/**
 * Mobile Menu Header Configuration.
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
 * Register mobile-menu header builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_mobile_menu_configuration() {
	$_section = 'section-header-mobile-menu';

	$_configs = array(

		/**
		 * Option: Header Builder Tabs
		 */
		array(
			'name'        => $_section . '-ast-context-tabs',
			'section'     => $_section,
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 0,
			'description' => '',
		),

		// Section: Primary Header.
		array(
			'name'     => $_section,
			'type'     => 'section',
			'title'    => __( 'Off-Canvas Menu', 'astra' ),
			'panel'    => 'panel-header-builder-group',
			'priority' => 40,
		),

		/**
		* Option: Theme Menu create link
		*/
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[header-mobile-menu-create-menu-link]',
			'default'   => astra_get_option( 'header-mobile-menu-create-menu-link' ),
			'type'      => 'control',
			'control'   => 'ast-customizer-link',
			'section'   => $_section,
			'priority'  => 30,
			'link_type' => 'section',
			'linked'    => 'menu_locations',
			'link_text' => __( 'Configure Menu from Here.', 'astra' ),
			'context'   => Astra_Builder_Helper::$general_tab,
			'divider'   => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
		),


		// Option: Submenu Divider Checkbox.
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-border]',
			'default'   => astra_get_option( 'header-mobile-menu-submenu-item-border' ),
			'type'      => 'control',
			'control'   => 'ast-toggle-control',
			'section'   => $_section,
			'priority'  => 150,
			'title'     => __( 'Item Divider', 'astra' ),
			'context'   => Astra_Builder_Helper::$general_tab,
			'transport' => 'postMessage',
			'divider'   => array( 'ast_class' => 'ast-top-section-divider' ),
		),

		// Option: Menu Color Divider.
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-divider-colors-divider]',
			'section'  => $_section,
			'type'     => 'control',
			'control'  => 'ast-heading',
			'title'    => __( 'Item Divider', 'astra' ),
			'priority' => 150,
			'settings' => array(),
			'context'  => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-border]',
					'operator' => '==',
					'value'    => true,
				),
			),
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),

		// Option: Submenu item Border Size.
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-b-size]',
			'type'        => 'control',
			'control'     => 'ast-slider',
			'default'     => astra_get_option( 'header-mobile-menu-submenu-item-b-size' ),
			'section'     => $_section,
			'priority'    => 150,
			'transport'   => 'postMessage',
			'title'       => __( 'Divider Size', 'astra' ),
			'context'     => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-border]',
					'operator' => '==',
					'value'    => true,
				),
			),
			'suffix'      => 'px',
			'input_attrs' => array(
				'min'  => 1,
				'step' => 1,
				'max'  => 10,
			),
			'divider'     => array( 'ast_class' => 'ast-bottom-dotted-divider ast-section-spacing' ),
		),

		// Option: Submenu item Border Color.
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-b-color]',
			'default'           => astra_get_option( 'header-mobile-menu-submenu-item-b-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'transport'         => 'postMessage',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Divider Color', 'astra' ),
			'section'           => $_section,
			'priority'          => 150,
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[header-mobile-menu-submenu-item-border]',
					'operator' => '==',
					'value'    => true,
				),
			),
		),


		// Option Group: Menu Color.
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[header-mobile-menu-link-colors]',
			'type'       => 'control',
			'control'    => 'ast-color-group',
			'title'      => __( 'Link', 'astra' ),
			'section'    => $_section,
			'transport'  => 'postMessage',
			'priority'   => 90,
			'context'    => Astra_Builder_Helper::$design_tab,
			'responsive' => true,
			'divider'    => array(
				'ast_title' => __( 'Menu Color', 'astra' ),
				'ast_class' => 'ast-section-spacing',
			),
		),
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[header-mobile-menu-background-colors]',
			'type'       => 'control',
			'control'    => 'ast-color-group',
			'title'      => __( 'Background', 'astra' ),
			'section'    => $_section,
			'transport'  => 'postMessage',
			'priority'   => 90,
			'context'    => Astra_Builder_Helper::$design_tab,
			'responsive' => true,
			'divider'    => array(
				'ast_title' => '',
				'ast_class' => class_exists( 'Astra_Ext_Extension' ) && Astra_Ext_Extension::is_active( 'colors-and-background' ) ? 'ast-bottom-dotted-divider' : '',
			),
		),
		// Option: Menu Color.
		array(
			'name'       => 'header-mobile-menu-color-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-link-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Normal', 'astra' ),
			'section'    => $_section,
			'title'      => __( 'Normal', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 7,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		// Option: Menu Background image, color.
		array(
			'name'       => 'header-mobile-menu-bg-obj-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-bg-obj-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-background-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-background',
			'section'    => $_section,
			'transport'  => 'postMessage',
			'tab'        => __( 'Normal', 'astra' ),
			'data_attrs' => array( 'name' => 'header-mobile-menu-bg-obj-responsive' ),
			'title'      => __( 'Normal', 'astra' ),
			'priority'   => 9,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		// Option: Menu Hover Color.
		array(
			'name'       => 'header-mobile-menu-h-color-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-h-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-link-colors]',
			'tab'        => __( 'Hover', 'astra' ),
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'title'      => __( 'Hover', 'astra' ),
			'section'    => $_section,
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 19,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		// Option: Menu Hover Background Color.
		array(
			'name'       => 'header-mobile-menu-h-bg-color-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-h-bg-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-background-colors]',
			'type'       => 'sub-control',
			'title'      => __( 'Hover', 'astra' ),
			'section'    => $_section,
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Hover', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 21,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		// Option: Active Menu Color.
		array(
			'name'       => 'header-mobile-menu-a-color-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-a-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-link-colors]',
			'type'       => 'sub-control',
			'section'    => $_section,
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Active', 'astra' ),
			'title'      => __( 'Active', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 31,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		// Option: Active Menu Background Color.
		array(
			'name'       => 'header-mobile-menu-a-bg-color-responsive',
			'default'    => astra_get_option( 'header-mobile-menu-a-bg-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-background-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'section'    => $_section,
			'title'      => __( 'Active', 'astra' ),
			'tab'        => __( 'Active', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 33,
			'context'    => Astra_Builder_Helper::$general_tab,
		),

		/**
		 * Option: WOO Off Canvas Menu Submenu Color Section divider
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-typo-divider]',
			'type'     => 'control',
			'control'  => 'ast-heading',
			'section'  => $_section,
			'title'    => __( 'Font', 'astra' ),
			'priority' => 120,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$design_tab,
			'divider'  => array(
				'ast_class' => 'ast-section-spacing',
			),
		),

		// Option Group: Menu Typography.
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-menu-typography]',
			'default'   => astra_get_option( 'header-mobile-menu-header-menu-typography' ),
			'type'      => 'control',
			'control'   => 'ast-settings-group',
			'title'     => __( 'Menu Font', 'astra' ),
			'section'   => $_section,
			'transport' => 'postMessage',
			'priority'  => 120,
			'context'   => Astra_Builder_Helper::$design_tab,
			'divider'   => array(
				'ast_class' => 'ast-section-spacing',
			),
		),

		// Option: Menu Font Family.
		array(
			'name'      => 'header-mobile-menu-font-family',
			'default'   => astra_get_option( 'header-mobile-menu-font-family', 'inherit' ),
			'parent'    => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-menu-typography]',
			'type'      => 'sub-control',
			'section'   => $_section,
			'transport' => 'postMessage',
			'control'   => 'ast-font',
			'font_type' => 'ast-font-family',
			'title'     => __( 'Font Family', 'astra' ),
			'priority'  => 22,
			'connect'   => 'header-mobile-menu-font-weight',
			'context'   => Astra_Builder_Helper::$general_tab,
			'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
		),

		// Option: Menu Font Weight.
		array(
			'name'              => 'header-mobile-menu-font-weight',
			'default'           => astra_get_option( 'header-mobile-menu-font-weight', 'inherit' ),
			'parent'            => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-menu-typography]',
			'section'           => $_section,
			'type'              => 'sub-control',
			'control'           => 'ast-font',
			'transport'         => 'postMessage',
			'font_type'         => 'ast-font-weight',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
			'title'             => __( 'Font Weight', 'astra' ),
			'priority'          => 23,
			'connect'           => 'header-mobile-menu-font-family',
			'context'           => Astra_Builder_Helper::$general_tab,
			'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
		),

		// Option: Menu Font Size.
		array(
			'name'              => 'header-mobile-menu-font-size',
			'default'           => astra_get_option( 'header-mobile-menu-font-size' ),
			'parent'            => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-menu-typography]',
			'section'           => $_section,
			'type'              => 'sub-control',
			'priority'          => 24,
			'title'             => __( 'Font Size', 'astra' ),
			'control'           => 'ast-responsive-slider',
			'transport'         => 'postMessage',
			'context'           => Astra_Builder_Helper::$general_tab,
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
			'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
			'input_attrs'       => array(
				'px'  => array(
					'min'  => 0,
					'step' => 1,
					'max'  => 200,
				),
				'em'  => array(
					'min'  => 0,
					'step' => 0.01,
					'max'  => 20,
				),
				'vw'  => array(
					'min'  => 0,
					'step' => 0.1,
					'max'  => 25,
				),
				'rem' => array(
					'min'  => 0,
					'step' => 0.1,
					'max'  => 20,
				),
			),
		),


		/**
		 * Option: Font Extras
		 */
		array(
			'name'     => 'font-extras-header-mobile-menu',
			'parent'   => ASTRA_THEME_SETTINGS . '[header-mobile-menu-header-menu-typography]',
			'section'  => $_section,
			'type'     => 'sub-control',
			'control'  => 'ast-font-extras',
			'priority' => 24,
			'default'  => astra_get_option( 'font-extras-header-mobile-menu' ),
			'title'    => __( 'Font Extras', 'astra' ),
		),

		/**
		 * Option: Divider
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-mobile-menu-menu-spacing-divider]',
			'section'  => $_section,
			'title'    => __( 'Spacing', 'astra' ),
			'type'     => 'control',
			'control'  => 'ast-heading',
			'priority' => 150,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$design_tab,
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),


		// Option - Menu Space.
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[header-mobile-menu-menu-spacing]',
			'default'           => astra_get_option( 'header-mobile-menu-menu-spacing' ),
			'type'              => 'control',
			'control'           => 'ast-responsive-spacing',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
			'transport'         => 'postMessage',
			'section'           => $_section,
			'priority'          => 150,
			'title'             => __( 'Menu Spacing', 'astra' ),
			'linked_choices'    => true,
			'unit_choices'      => array( 'px', 'em', '%' ),
			'choices'           => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
			'context'           => Astra_Builder_Helper::$design_tab,
			'divider'           => array( 'ast_class' => 'ast-bottom-section-divider ast-section-spacing' ),
		),

		/**
		 * Option: Margin Space
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[' . $_section . '-margin]',
			'default'           => astra_get_option( $_section . '-margin' ),
			'type'              => 'control',
			'transport'         => 'postMessage',
			'control'           => 'ast-responsive-spacing',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
			'section'           => $_section,
			'priority'          => 220,
			'title'             => __( 'Margin', 'astra' ),
			'linked_choices'    => true,
			'unit_choices'      => array( 'px', 'em', '%' ),
			'choices'           => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
			'context'           => Astra_Builder_Helper::$design_tab,
		),
	);

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_mobile_menu_configuration();
}
