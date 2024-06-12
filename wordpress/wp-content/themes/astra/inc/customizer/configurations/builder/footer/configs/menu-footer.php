<?php
/**
 * Menu footer Configuration.
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
 * Register menu footer builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_menu_footer_configuration() {
	$_section = 'section-footer-menu';

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
			'title'    => __( 'Footer Menu', 'astra' ),
			'panel'    => 'panel-footer-builder-group',
			'priority' => 50,
		),

		/**
		* Option: Theme Menu create link
		*/
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[footer-create-menu-link]',
			'default'   => astra_get_option( 'footer-create-menu-link' ),
			'type'      => 'control',
			'control'   => 'ast-customizer-link',
			'section'   => $_section,
			'priority'  => 10,
			'link_type' => 'section',
			'linked'    => 'menu_locations',
			'link_text' => __( 'Configure Menu from Here.', 'astra' ),
			'context'   => Astra_Builder_Helper::$general_tab,

		),


		// Option: Footer Menu Layout.
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[footer-menu-layout]',
			'default'    => astra_get_option( 'footer-menu-layout' ),
			'section'    => $_section,
			'priority'   => 20,
			'title'      => __( 'Layout', 'astra' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'transport'  => 'postMessage',
			'partial'    => array(
				'selector'            => '.footer-widget-area[data-section="section-footer-menu"] nav',
				'container_inclusive' => true,
				'render_callback'     => array( Astra_Builder_Footer::get_instance(), 'footer_menu' ),
			),
			'choices'    => array(
				'horizontal' => __( 'Inline', 'astra' ),
				'vertical'   => __( 'Stack', 'astra' ),
			),
			'context'    => Astra_Builder_Helper::$general_tab,
			'responsive' => true,
			'renderAs'   => 'text',
			'divider'    => array( 'ast_class' => 'ast-top-section-divider ast-bottom-section-divider' ),
		),

		/**
		 * Option: Alignment
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[footer-menu-alignment]',
			'default'   => astra_get_option( 'footer-menu-alignment' ),
			'type'      => 'control',
			'control'   => 'ast-selector',
			'section'   => $_section,
			'priority'  => 21,
			'title'     => __( 'Alignment', 'astra' ),
			'context'   => Astra_Builder_Helper::$general_tab,
			'transport' => 'postMessage',
			'choices'   => array(
				'flex-start' => 'align-left',
				'center'     => 'align-center',
				'flex-end'   => 'align-right',
			),
		),

		// Option Group: Menu Color.
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[footer-menu-link-colors]',
			'type'       => 'control',
			'control'    => 'ast-color-group',
			'context'    => Astra_Builder_Helper::$design_tab,
			'title'      => __( 'Link / Text', 'astra' ),
			'section'    => $_section,
			'transport'  => 'postMessage',
			'priority'   => 90,
			'responsive' => true,
			'divider'    => array( 'ast_class' => 'ast-section-spacing' ),
		),
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[footer-menu-background-colors]',
			'type'       => 'control',
			'control'    => 'ast-color-group',
			'context'    => Astra_Builder_Helper::$design_tab,
			'title'      => __( 'Background', 'astra' ),
			'section'    => $_section,
			'transport'  => 'postMessage',
			'priority'   => 90,
			'responsive' => true,
			'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
		),
		// Option: Menu Color.
		array(
			'name'       => 'footer-menu-color-responsive',
			'default'    => astra_get_option( 'footer-menu-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-link-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Normal', 'astra' ),
			'section'    => $_section,
			'title'      => __( 'Normal', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 7,
		),

		// Option: Menu Background image, color.
		array(
			'name'       => 'footer-menu-bg-obj-responsive',
			'default'    => astra_get_option( 'footer-menu-bg-obj-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-background-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-background',
			'section'    => $_section,
			'transport'  => 'postMessage',
			'tab'        => __( 'Normal', 'astra' ),
			'data_attrs' => array( 'name' => 'footer-menu-bg-obj-responsive' ),
			'title'      => __( 'Normal', 'astra' ),
			'label'      => __( 'Normal', 'astra' ),
			'priority'   => 9,
		),

		// Option: Menu Hover Color.
		array(
			'name'       => 'footer-menu-h-color-responsive',
			'default'    => astra_get_option( 'footer-menu-h-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-link-colors]',
			'tab'        => __( 'Hover', 'astra' ),
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'title'      => __( 'Hover', 'astra' ),
			'section'    => $_section,
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 19,
		),

		// Option: Menu Hover Background Color.
		array(
			'name'       => 'footer-menu-h-bg-color-responsive',
			'default'    => astra_get_option( 'footer-menu-h-bg-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-background-colors]',
			'type'       => 'sub-control',
			'title'      => __( 'Hover', 'astra' ),
			'section'    => $_section,
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Hover', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 21,
		),

		// Option: Active Menu Color.
		array(
			'name'       => 'footer-menu-a-color-responsive',
			'default'    => astra_get_option( 'footer-menu-a-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-link-colors]',
			'type'       => 'sub-control',
			'section'    => $_section,
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'tab'        => __( 'Active', 'astra' ),
			'title'      => __( 'Active', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 31,
		),

		// Option: Active Menu Background Color.
		array(
			'name'       => 'footer-menu-a-bg-color-responsive',
			'default'    => astra_get_option( 'footer-menu-a-bg-color-responsive' ),
			'parent'     => ASTRA_THEME_SETTINGS . '[footer-menu-background-colors]',
			'type'       => 'sub-control',
			'control'    => 'ast-responsive-color',
			'transport'  => 'postMessage',
			'section'    => $_section,
			'title'      => __( 'Active', 'astra' ),
			'tab'        => __( 'Active', 'astra' ),
			'responsive' => true,
			'rgba'       => true,
			'priority'   => 33,
		),

		/**
		 * Option: Divider
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[footer-main-menu-divider]',
			'section'  => $_section,
			'title'    => __( 'Spacing', 'astra' ),
			'type'     => 'control',
			'control'  => 'ast-heading',
			'priority' => 210,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$design_tab,
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),

		// Option - Menu Space.
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[footer-main-menu-spacing]',
			'default'           => astra_get_option( 'footer-main-menu-spacing' ),
			'type'              => 'control',
			'control'           => 'ast-responsive-spacing',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
			'transport'         => 'postMessage',
			'section'           => $_section,
			'context'           => Astra_Builder_Helper::$design_tab,
			'priority'          => 210,
			'title'             => __( 'Menu Spacing', 'astra' ),
			'linked_choices'    => true,
			'unit_choices'      => array( 'px', 'em', '%' ),
			'choices'           => array(
				'top'    => __( 'Top', 'astra' ),
				'right'  => __( 'Right', 'astra' ),
				'bottom' => __( 'Bottom', 'astra' ),
				'left'   => __( 'Left', 'astra' ),
			),
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

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'typography' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$new_configs = array(

			// Option Group: Menu Typography.
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[footer-menu-typography]',
				'default'   => astra_get_option( 'footer-menu-typography' ),
				'type'      => 'control',
				'control'   => 'ast-settings-group',
				'title'     => __( 'Menu Font', 'astra' ),
				'section'   => $_section,
				'context'   => Astra_Builder_Helper::$design_tab,
				'transport' => 'postMessage',
				'priority'  => 120,
			),

			// Option: Menu Font Size.

			array(
				'name'              => 'footer-menu-font-size',
				'default'           => astra_get_option( 'footer-menu-font-size' ),
				'parent'            => ASTRA_THEME_SETTINGS . '[footer-menu-typography]',
				'section'           => $_section,
				'type'              => 'sub-control',
				'priority'          => 23,
				'title'             => __( 'Font Size', 'astra' ),
				'transport'         => 'postMessage',
				'control'           => 'ast-responsive-slider',
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
		);
	} else {

		$new_configs = array(

			// Option: Menu Font Size.

			array(
				'name'              => ASTRA_THEME_SETTINGS . '[footer-menu-font-size]',
				'default'           => astra_get_option( 'footer-menu-font-size' ),
				'section'           => $_section,
				'control'           => 'ast-responsive-slider',
				'context'           => Astra_Builder_Helper::$design_tab,
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'type'              => 'control',
				'transport'         => 'postMessage',
				'title'             => __( 'Menu Font Size', 'astra' ),
				'priority'          => 120,
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
		);
	}

	$_configs = array_merge( $_configs, $new_configs );

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section, 'footer' ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_footer_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_menu_footer_configuration();
}
