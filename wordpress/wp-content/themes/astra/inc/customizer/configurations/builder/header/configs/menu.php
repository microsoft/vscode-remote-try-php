<?php
/**
 * Menu Header Configuration.
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
 * Register menu header builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_menu_configuration() {
	$menu_configs = array();

	$component_limit = defined( 'ASTRA_EXT_VER' ) ? Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_header_menu;

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$custom_req_divider = array( 'ast_class' => ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'colors-and-background' ) ) ? 'ast-bottom-dotted-divider' : '' );
	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	for ( $index = 1; $index <= $component_limit; $index++ ) {

		$_section = 'section-hb-menu-' . $index;
		$_prefix  = 'menu' . $index;

		switch ( $index ) {
			case 1:
				$edit_menu_title = __( 'Primary Menu', 'astra' );
				break;
			case 2:
				$edit_menu_title = __( 'Secondary Menu', 'astra' );
				break;
			default:
				$edit_menu_title = __( 'Menu ', 'astra' ) . $index;
				break;
		}

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
				'name'        => $_section,
				'type'        => 'section',
				'title'       => $edit_menu_title,
				'panel'       => 'panel-header-builder-group',
				'priority'    => 40,
				'clone_index' => $index,
				'clone_type'  => 'header-menu',
			),

			/**
			 * Option: Theme Menu create link.
			 */
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-create-menu-link]',
				'default'   => astra_get_option( 'header-' . $_prefix . '-create-menu-link' ),
				'type'      => 'control',
				'control'   => 'ast-customizer-link',
				'section'   => $_section,
				'priority'  => 30,
				'link_type' => 'section',
				'linked'    => 'menu_locations',
				'link_text' => __( 'Configure Menu from Here.', 'astra' ),
				'context'   => Astra_Builder_Helper::$general_tab,
			),

			/**
			 * Option: Menu hover style
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-menu-hover-animation]',
				'default'    => astra_get_option( 'header-' . $_prefix . '-menu-hover-animation' ),
				'type'       => 'control',
				'control'    => 'ast-select',
				'section'    => $_section,
				'priority'   => 10,
				'title'      => __( 'Menu Hover Style', 'astra' ),
				'choices'    => array(
					''          => __( 'None', 'astra' ),
					'zoom'      => __( 'Zoom In', 'astra' ),
					'underline' => __( 'Underline', 'astra' ),
					'overline'  => __( 'Overline', 'astra' ),
				),
				'context'    => Astra_Builder_Helper::$design_tab,
				'transport'  => 'postMessage',
				'responsive' => false,
				'renderAs'   => 'text',
				'divider'    => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Submenu heading.
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-heading]',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'section'  => $_section,
				'title'    => __( 'Submenu', 'astra' ),
				'settings' => array(),
				'priority' => 30,
				'context'  => Astra_Builder_Helper::$general_tab,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			/**
			 * Option: Submenu width
			 */
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-width]',
				'default'     => astra_get_option( 'header-' . $_prefix . '-submenu-width' ),
				'type'        => 'control',
				'context'     => Astra_Builder_Helper::$general_tab,
				'section'     => $_section,
				'control'     => 'ast-slider',
				'priority'    => 30.5,
				'title'       => __( 'Width', 'astra' ),
				'suffix'      => 'px',
				'input_attrs' => array(
					'min'  => 0,
					'step' => 1,
					'max'  => 1920,
				),
				'transport'   => 'postMessage',
				'divider'     => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
			),

			/**
			 * Option: Submenu Animation
			 */
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-container-animation]',
				'default'    => astra_get_option( 'header-' . $_prefix . '-submenu-container-animation' ),
				'type'       => 'control',
				'control'    => 'ast-select',
				'section'    => $_section,
				'priority'   => 23,
				'title'      => __( 'Submenu Animation', 'astra' ),
				'choices'    => array(
					''           => __( 'None', 'astra' ),
					'slide-down' => __( 'Slide Down', 'astra' ),
					'slide-up'   => __( 'Slide Up', 'astra' ),
					'fade'       => __( 'Fade', 'astra' ),
				),
				'context'    => Astra_Builder_Helper::$design_tab,
				'transport'  => 'postMessage',
				'responsive' => false,
				'renderAs'   => 'text',
				'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
			),

			// Option: Submenu Container Divider.
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-container-divider]',
				'section'  => $_section,
				'type'     => 'control',
				'control'  => 'ast-heading',
				'title'    => __( 'Submenu Container', 'astra' ),
				'priority' => 20,
				'settings' => array(),
				'context'  => Astra_Builder_Helper::$design_tab,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			// Option: Submenu Divider Size.
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-item-b-size]',
				'type'        => 'control',
				'control'     => 'ast-slider',
				'default'     => astra_get_option( 'header-' . $_prefix . '-submenu-item-b-size' ),
				'section'     => $_section,
				'priority'    => 20.5,
				'transport'   => 'postMessage',
				'title'       => __( 'Divider Size', 'astra' ),
				'context'     => array(
					Astra_Builder_Helper::$design_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-item-border]',
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
				'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
			),

			// Option: Submenu item Border Color.
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-item-b-color]',
				'default'           => astra_get_option( 'header-' . $_prefix . '-submenu-item-b-color' ),
				'type'              => 'control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Divider Color', 'astra' ),
				'section'           => $_section,
				'priority'          => 21,
				'context'           => array(
					Astra_Builder_Helper::$design_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-item-border]',
						'operator' => '==',
						'value'    => true,
					),
				),
				'divider'           => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
			),

			/**
			 * Option: Submenu Top Offset
			 */
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-top-offset]',
				'default'     => astra_get_option( 'header-' . $_prefix . '-submenu-top-offset' ),
				'type'        => 'control',
				'context'     => Astra_Builder_Helper::$design_tab,
				'section'     => $_section,
				'control'     => 'ast-slider',
				'priority'    => 22,
				'title'       => __( 'Top Offset', 'astra' ),
				'suffix'      => 'px',
				'transport'   => 'postMessage',
				'input_attrs' => array(
					'min'  => 0,
					'step' => 1,
					'max'  => 200,
				),
				'divider'     => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
			),

			// Option: Sub-Menu Border.
			array(
				'name'           => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-border]',
				'default'        => astra_get_option( 'header-' . $_prefix . '-submenu-border' ),
				'type'           => 'control',
				'control'        => 'ast-border',
				'transport'      => 'postMessage',
				'section'        => $_section,
				'linked_choices' => true,
				'context'        => Astra_Builder_Helper::$design_tab,
				'priority'       => 23,
				'title'          => __( 'Border Width', 'astra' ),
				'choices'        => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
				'divider'        => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
			),

			// Option: Submenu Container Border Color.
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-b-color]',
				'default'           => astra_get_option( 'header-' . $_prefix . '-submenu-b-color' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-border-group]',
				'type'              => 'control',
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Border Color', 'astra' ),
				'section'           => $_section,
				'priority'          => 23,
				'context'           => Astra_Builder_Helper::$design_tab,
				'divider'           => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
			),

			/**
			* Option: Button Radius Fields
			*/
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-border-radius-fields]',
				'default'           => astra_get_option( 'header-' . $_prefix . '-submenu-border-radius-fields' ),
				'type'              => 'control',
				'control'           => 'ast-responsive-spacing',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
				'section'           => $_section,
				'title'             => __( 'Border Radius', 'astra' ),
				'linked_choices'    => true,
				'transport'         => 'postMessage',
				'unit_choices'      => array( 'px', 'em', '%' ),
				'choices'           => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
				'priority'          => 23,
				'connected'         => false,
				'divider'           => array( 'ast_class' => 'ast-bottom-section-divider' ),
				'context'           => Astra_Builder_Helper::$design_tab,
			),

			// Option: Submenu Divider Checkbox.
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-submenu-item-border]',
				'default'   => astra_get_option( 'header-' . $_prefix . '-submenu-item-border' ),
				'type'      => 'control',
				'control'   => 'ast-toggle-control',
				'section'   => $_section,
				'priority'  => 35,
				'title'     => __( 'Item Divider', 'astra' ),
				'context'   => Astra_Builder_Helper::$general_tab,
				'transport' => 'postMessage',
			),

			// Option: Menu Stack on Mobile Checkbox.
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-menu-stack-on-mobile]',
				'default'   => astra_get_option( 'header-' . $_prefix . '-menu-stack-on-mobile' ),
				'type'      => 'control',
				'control'   => 'ast-toggle-control',
				'section'   => $_section,
				'priority'  => 41,
				'divider'   => array( 'ast_class' => 'ast-top-section-divider' ),
				'title'     => __( 'Stack on Responsive', 'astra' ),
				'context'   => Astra_Builder_Helper::$responsive_general_tab,
				'transport' => 'postMessage',
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
				'priority'          => 151,
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
				'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
			),

			// Option Group: Menu Color.
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-text-colors]',
				'type'       => 'control',
				'control'    => 'ast-color-group',
				'title'      => __( 'Text / Link', 'astra' ),
				'section'    => $_section,
				'transport'  => 'postMessage',
				'priority'   => 90,
				'context'    => Astra_Builder_Helper::$design_tab,
				'responsive' => true,
				'divider'    => array(
					'ast_title' => __( 'Menu Color', 'astra' ),
				),
			),
			array(
				'name'       => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-background-colors]',
				'type'       => 'control',
				'control'    => 'ast-color-group',
				'title'      => __( 'Background', 'astra' ),
				'section'    => $_section,
				'transport'  => 'postMessage',
				'priority'   => 90,
				'context'    => Astra_Builder_Helper::$design_tab,
				'responsive' => true,
				'divider'    => $custom_req_divider,
			),

			// Option: Menu Color.
			array(
				'name'       => 'header-' . $_prefix . '-color-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-color-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-text-colors]',
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
				'name'       => 'header-' . $_prefix . '-bg-obj-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-bg-obj-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-background-colors]',
				'type'       => 'sub-control',
				'control'    => 'ast-responsive-background',
				'section'    => $_section,
				'transport'  => 'postMessage',
				'tab'        => __( 'Normal', 'astra' ),
				'data_attrs' => array( 'name' => 'header-' . $_prefix . '-bg-obj-responsive' ),
				'title'      => __( 'Normal', 'astra' ),
				'priority'   => 9,
				'context'    => Astra_Builder_Helper::$general_tab,
			),

			// Option: Menu Hover Color.
			array(
				'name'       => 'header-' . $_prefix . '-h-color-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-h-color-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-text-colors]',
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
				'name'       => 'header-' . $_prefix . '-h-bg-color-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-h-bg-color-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-background-colors]',
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
				'name'       => 'header-' . $_prefix . '-a-color-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-a-color-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-text-colors]',
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
				'name'       => 'header-' . $_prefix . '-a-bg-color-responsive',
				'default'    => astra_get_option( 'header-' . $_prefix . '-a-bg-color-responsive' ),
				'parent'     => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-background-colors]',
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

			// Font Divider.
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[header-' . $index . '-font-divider]',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'section'  => $_section,
				'title'    => __( 'Font', 'astra' ),
				'settings' => array(),
				'priority' => 120,
				'context'  => Astra_Builder_Helper::$design_tab,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),

			// Option Group: Menu Typography.
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-header-menu-typography]',
				'default'   => astra_get_option( 'header-' . $_prefix . '-header-menu-typography' ),
				'type'      => 'control',
				'control'   => 'ast-settings-group',
				'title'     => __( 'Menu Font', 'astra' ),
				'section'   => $_section,
				'transport' => 'postMessage',
				'priority'  => 120,
				'context'   => Astra_Builder_Helper::$design_tab,
				'divider'   => array( 'ast_class' => 'ast-section-spacing' ),
			),

			// Option: Menu Font Family.
			array(
				'name'      => 'header-' . $_prefix . '-font-family',
				'default'   => astra_get_option( 'header-' . $_prefix . '-font-family' ),
				'parent'    => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-header-menu-typography]',
				'type'      => 'sub-control',
				'section'   => $_section,
				'transport' => 'postMessage',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'title'     => __( 'Font Family', 'astra' ),
				'priority'  => 22,
				'connect'   => 'header-' . $_prefix . '-font-weight',
				'context'   => Astra_Builder_Helper::$general_tab,
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			// Option: Menu Font Weight.
			array(
				'name'              => 'header-' . $_prefix . '-font-weight',
				'default'           => astra_get_option( 'header-' . $_prefix . '-font-weight' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-header-menu-typography]',
				'section'           => $_section,
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'transport'         => 'postMessage',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'priority'          => 23,
				'connect'           => 'header-' . $_prefix . '-font-family',
				'context'           => Astra_Builder_Helper::$general_tab,
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),


			// Option: Menu Font Size.
			array(
				'name'              => 'header-' . $_prefix . '-font-size',
				'default'           => astra_get_option( 'header-' . $_prefix . '-font-size' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-header-menu-typography]',
				'section'           => $_section,
				'type'              => 'sub-control',
				'priority'          => 23,
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
			 * Option: Primary Menu Font Extras
			 */
			array(
				'name'     => 'header-' . $_prefix . '-font-extras',
				'parent'   => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-header-menu-typography]',
				'section'  => $_section,
				'type'     => 'sub-control',
				'control'  => 'ast-font-extras',
				'priority' => 26,
				'default'  => astra_get_option( 'header-' . $_prefix . '-font-extras' ),
				'title'    => __( 'Font Extras', 'astra' ),
			),


			/**
			* Option: Spacing Divider
			*/
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[header-' . $index . '-spacing-divider]',
				'type'     => 'control',
				'control'  => 'ast-heading',
				'section'  => $_section,
				'title'    => __( 'Spacing', 'astra' ),
				'settings' => array(),
				'priority' => 150,
				'context'  => Astra_Builder_Helper::$design_tab,
				'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
			),


			// Option - Menu Space.
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[header-' . $_prefix . '-menu-spacing]',
				'default'           => astra_get_option( 'header-' . $_prefix . '-menu-spacing' ),
				'type'              => 'control',
				'control'           => 'ast-responsive-spacing',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
				'transport'         => 'postMessage',
				'section'           => $_section,
				'priority'          => 150,
				'title'             => __( 'Menu', 'astra' ),
				'linked_choices'    => true,
				'unit_choices'      => array( 'px', 'em', '%' ),
				'choices'           => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
				'context'           => Astra_Builder_Helper::$design_tab,
				'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
			),
		);

		$menu_configs[] = Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section );
		$menu_configs[] = $_configs;
	}

	$menu_configs = call_user_func_array( 'array_merge', $menu_configs + array( array() ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $menu_configs );
	}

	return $menu_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_menu_configuration();
}
