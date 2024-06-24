<?php
/**
 * Copyright footer Configuration.
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
 * Register copyright footer builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_copyright_footer_configuration() {
	$_section = 'section-footer-copyright';
	$_configs = array(

		/*
		* Footer Builder section
		*/
		array(
			'name'     => $_section,
			'type'     => 'section',
			'priority' => 5,
			'title'    => __( 'Copyright', 'astra' ),
			'panel'    => 'panel-footer-builder-group',
		),

		/**
		 * Option: Footer Builder Tabs
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
		 * Option: Footer Copyright Html Editor.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[footer-copyright-editor]',
			'type'        => 'control',
			'control'     => 'ast-html-editor',
			'section'     => $_section,
			'transport'   => 'postMessage',
			'priority'    => 4,
			'default'     => astra_get_option( 'footer-copyright-editor', 'Copyright [copyright] [current_year] [site_title] | Powered by [theme_author]' ),
			'input_attrs' => array(
				'id' => 'ast-footer-copyright',
			),
			'partial'     => array(
				'selector'            => '.ast-footer-copyright',
				'container_inclusive' => true,
				'render_callback'     => array( Astra_Builder_Footer::get_instance(), 'footer_copyright' ),
			),
			'context'     => Astra_Builder_Helper::$general_tab,
			'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Option: Column Alignment
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[footer-copyright-alignment]',
			'default'   => astra_get_option( 'footer-copyright-alignment' ),
			'type'      => 'control',
			'control'   => 'ast-selector',
			'section'   => $_section,
			'priority'  => 6,
			'title'     => __( 'Alignment', 'astra' ),
			'context'   => Astra_Builder_Helper::$general_tab,
			'transport' => 'postMessage',
			'choices'   => array(
				'left'   => 'align-left',
				'center' => 'align-center',
				'right'  => 'align-right',
			),
			'divider'   => array( 'ast_class' => 'ast-top-section-divider' ),
		),

		/**
		 * Option: Text Color.
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[footer-copyright-color]',
			'default'           => astra_get_option( 'footer-copyright-color' ),
			'type'              => 'control',
			'section'           => $_section,
			'priority'          => 8,
			'transport'         => 'postMessage',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'title'             => __( 'Text Color', 'astra' ),
			'context'           => Astra_Builder_Helper::$design_tab,
			'divider'           => array( 'ast_class' => 'ast-bottom-section-divider' ),

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
			'priority' => 99,
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
			'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
		),
	);

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_typography_options( $_section ) );

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section, 'footer' ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_footer_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_copyright_footer_configuration();
}
