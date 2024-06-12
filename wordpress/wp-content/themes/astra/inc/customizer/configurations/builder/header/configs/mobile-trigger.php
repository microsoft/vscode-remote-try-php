<?php
/**
 * Mobile Trigger Header Configuration.
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
 * Register Header Trigger header builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_mobile_trigger_configuration() {
	$_section = 'section-header-mobile-trigger';

	$_configs = array(

		/*
		* Header Builder section
		*/
		array(
			'name'     => 'section-header-mobile-trigger',
			'type'     => 'section',
			'priority' => 70,
			'title'    => __( 'Toggle Button', 'astra' ),
			'panel'    => 'panel-header-builder-group',
		),

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

		/**
		 * Option: Header Html Editor.
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[header-trigger-icon]',
			'type'              => 'control',
			'control'           => 'ast-radio-image',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
			'default'           => astra_get_option( 'header-trigger-icon' ),
			'title'             => __( 'Icons', 'astra' ),
			'section'           => $_section,
			'choices'           => array(
				'menu'  => array(
					'label' => __( 'Menu', 'astra' ),
					'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'mobile_menu' ),
				),
				'menu2' => array(
					'label' => __( 'Menu 2', 'astra' ),
					'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'mobile_menu2' ),
				),
				'menu3' => array(
					'label' => __( 'Menu 3', 'astra' ),
					'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'mobile_menu3' ),
				),
			),
			'transport'         => 'postMessage',
			'partial'           => array(
				'selector'        => '.ast-button-wrap',
				'render_callback' => array( 'Astra_Builder_UI_Controller', 'render_mobile_trigger' ),
			),
			'priority'          => 10,
			'context'           => Astra_Builder_Helper::$general_tab,
			'divider'           => array( 'ast_class' => 'ast-bottom-section-divider' ),
			'alt_layout'        => true,
		),

		/**
		 * Option: Toggle Button Style
		 */
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-style]',
			'default'    => astra_get_option( 'mobile-header-toggle-btn-style' ),
			'section'    => $_section,
			'title'      => __( 'Toggle Button Style', 'astra' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'priority'   => 11,
			'choices'    => array(
				'fill'    => __( 'Fill', 'astra' ),
				'outline' => __( 'Outline', 'astra' ),
				'minimal' => __( 'Minimal', 'astra' ),
			),
			'context'    => Astra_Builder_Helper::$general_tab,
			'transport'  => 'postMessage',
			'partial'    => array(
				'selector'        => '.ast-button-wrap',
				'render_callback' => array( 'Astra_Builder_UI_Controller', 'render_mobile_trigger' ),
			),
			'responsive' => false,
			'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
			'renderAs'   => 'text',
		),

		/**
		 * Option: Mobile Menu Label
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[mobile-header-menu-label]',
			'transport' => 'postMessage',
			'partial'   => array(
				'selector'        => '.ast-button-wrap',
				'render_callback' => array( 'Astra_Builder_UI_Controller', 'render_mobile_trigger' ),
			),
			'default'   => astra_get_option( 'mobile-header-menu-label' ),
			'section'   => $_section,
			'priority'  => 20,
			'title'     => __( 'Menu Label', 'astra' ),
			'type'      => 'control',
			'control'   => 'text',
			'context'   => Astra_Builder_Helper::$general_tab,
			'divider'   => array( 'ast_class' => 'ast-bottom-divider ast-top-divider' ),
		),

		/**
		 * Option: Toggle Button Color
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-color]',
			'default'           => astra_get_option( 'mobile-header-toggle-btn-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Icon Color', 'astra' ),
			'section'           => $_section,
			'transport'         => 'postMessage',
			'priority'          => 40,
			'context'           => Astra_Builder_Helper::$design_tab,
			'divider'           => array( 'ast_class' => 'ast-section-spacing' ),

		),

		/**
		 * Option: Icon Size
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-icon-size]',
			'default'     => astra_get_option( 'mobile-header-toggle-icon-size' ),
			'type'        => 'control',
			'control'     => 'ast-slider',
			'section'     => $_section,
			'title'       => __( 'Icon Size', 'astra' ),
			'priority'    => 50,
			'suffix'      => 'px',
			'transport'   => 'postMessage',
			'input_attrs' => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 100,
			),
			'context'     => Astra_Builder_Helper::$design_tab,
			'divider'     => array( 'ast_class' => 'ast-top-section-divider' ),
		),



		/**
		 * Option: Toggle Button Bg Color
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-bg-color]',
			'default'           => astra_get_option( 'mobile-header-toggle-btn-bg-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Background Color', 'astra' ),
			'section'           => $_section,
			'transport'         => 'postMessage',
			'priority'          => 40,
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-style]',
					'operator' => '==',
					'value'    => 'fill',
				),
			),
		),

		/**
		 * Option: Toggle Button Border Size
		 */
		array(
			'name'           => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-border-size]',
			'default'        => astra_get_option( 'mobile-header-toggle-btn-border-size' ),
			'type'           => 'control',
			'section'        => $_section,
			'control'        => 'ast-border',
			'transport'      => 'postMessage',
			'linked_choices' => true,
			'priority'       => 60,
			'title'          => __( 'Border Width', 'astra' ),
			'choices'        => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
			'context'        => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-style]',
					'operator' => '==',
					'value'    => 'outline',
				),
			),
			'divider'        => array( 'ast_class' => 'ast-top-section-divider' ),
		),

		/**
		 * Option: Toggle Button Border Color
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-border-color]',
			'default'           => astra_get_option( 'mobile-header-toggle-border-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Border Color', 'astra' ),
			'section'           => $_section,
			'transport'         => 'postMessage',
			'priority'          => 40,
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-style]',
					'operator' => '==',
					'value'    => 'outline',
				),
			),
		),

		/**
		* Option: Button Radius Fields
		*/
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-border-radius-fields]',
			'default'           => astra_get_option( 'mobile-header-toggle-border-radius-fields' ),
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
			'priority'          => 50,
			'connected'         => false,
			'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[mobile-header-toggle-btn-style]',
					'operator' => '!=',
					'value'    => 'minimal',
				),
			),
		),


		/**
		 * Option: Divider
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[' . $_section . '-margin-divider]',
			'section'  => $_section,
			'title'    => __( 'Spacing', 'astra' ),
			'type'     => 'control',
			'control'  => 'ast-heading',
			'priority' => 130,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$design_tab,
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
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
			'priority'          => 130,
			'title'             => __( 'Margin', 'astra' ),
			'linked_choices'    => true,
			'unit_choices'      => array( 'px', 'em', '%' ),
			'choices'           => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
			'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
			'context'           => Astra_Builder_Helper::$design_tab,
		),
	);

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'typography' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$typo_configs = array(

			// Option Group: Trigger Typography.
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[mobile-header-label-typography]',
				'default'   => astra_get_option( 'mobile-header-label-typography' ),
				'type'      => 'control',
				'control'   => 'ast-settings-group',
				'title'     => __( 'Typography', 'astra' ),
				'section'   => $_section,
				'transport' => 'postMessage',
				'priority'  => 70,
				'context'   => array(
					Astra_Builder_Helper::$design_tab_config,
					array(
						'setting'  => ASTRA_THEME_SETTINGS . '[mobile-header-menu-label]',
						'operator' => '!=',
						'value'    => '',
					),
				),
			),

			// Option: Trigger Font Size.
			array(
				'name'        => 'mobile-header-label-font-size',
				'default'     => astra_get_option( 'mobile-header-label-font-size' ),
				'parent'      => ASTRA_THEME_SETTINGS . '[mobile-header-label-typography]',
				'section'     => $_section,
				'type'        => 'sub-control',
				'priority'    => 23,
				'suffix'      => 'px',
				'title'       => __( 'Font Size', 'astra' ),
				'control'     => 'ast-slider',
				'transport'   => 'postMessage',
				'input_attrs' => array(
					'min' => 0,
					'max' => 200,
				),
				'units'       => array(
					'px'  => 'px',
					'em'  => 'em',
					'vw'  => 'vw',
					'rem' => 'rem',
				),
				'context'     => Astra_Builder_Helper::$design_tab,
			),
		);

	} else {

		$typo_configs = array(

			// Option: Trigger Font Size.
			array(
				'name'        => ASTRA_THEME_SETTINGS . '[mobile-header-label-font-size]',
				'default'     => astra_get_option( 'mobile-header-label-font-size' ),
				'section'     => $_section,
				'type'        => 'control',
				'priority'    => 70,
				'suffix'      => 'px',
				'title'       => __( 'Font Size', 'astra' ),
				'control'     => 'ast-slider',
				'transport'   => 'postMessage',
				'input_attrs' => array(
					'min' => 0,
					'max' => 200,
				),
				'units'       => array(
					'px'  => 'px',
					'em'  => 'em',
					'vw'  => 'vw',
					'rem' => 'rem',
				),
				'context'     => Astra_Builder_Helper::$design_tab,
			),
		);
	}

	$_configs = array_merge( $_configs, $typo_configs );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_mobile_trigger_configuration();
}
