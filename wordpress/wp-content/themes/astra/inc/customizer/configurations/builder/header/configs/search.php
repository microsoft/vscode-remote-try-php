<?php
/**
 * Search Header Configuration.
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
 * Register search header builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_search_configuration() {
	$_section = 'section-header-search';

	$_configs = array(

		/*
		* Header Builder section
		*/
		array(
			'name'     => $_section,
			'type'     => 'section',
			'priority' => 80,
			'title'    => __( 'Search', 'astra' ),
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
		 * Option: Search Color.
		 */
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[header-search-icon-color]',
			'default'    => astra_get_option( 'header-search-icon-color' ),
			'type'       => 'control',
			'section'    => $_section,
			'priority'   => 8,
			'transport'  => 'postMessage',
			'control'    => 'ast-responsive-color',
			'responsive' => true,
			'rgba'       => true,
			'title'      => __( 'Icon Color', 'astra' ),
			'context'    => Astra_Builder_Helper::$design_tab,
			'divider'    => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Option: Search Size
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[header-search-icon-space]',
			'section'           => $_section,
			'priority'          => 3,
			'transport'         => 'postMessage',
			'default'           => astra_get_option( 'header-search-icon-space' ),
			'title'             => __( 'Icon Size', 'astra' ),
			'suffix'            => 'px',
			'type'              => 'control',
			'control'           => 'ast-responsive-slider',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
			'divider'           => array( 'ast_class' => ( defined( 'ASTRA_EXT_VER' ) ) ? 'ast-top-section-divider ast-bottom-section-divider' : 'ast-section-spacing' ),
			'input_attrs'       => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 50,
			),
			'context'           => Astra_Builder_Helper::$general_tab,
		),

		/**
		 * Option: Search bar width
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-search-width]',
			'section'     => $_section,
			'priority'    => 2,
			'transport'   => 'postMessage',
			'default'     => astra_get_option( 'header-search-width' ),
			'title'       => __( 'Search Width', 'astra' ),
			'suffix'      => 'px',
			'type'        => 'control',
			'control'     => 'ast-responsive-slider',
			'input_attrs' => array(
				'min'  => 1,
				'step' => 1,
				'max'  => 1000,
			),
			'divider'     => defined( 'ASTRA_EXT_VER' ) ? array( 'ast_class' => 'ast-top-dotted-divider' ) : array( 'ast_class' => 'ast-section-spacing ast-bottom-dotted-divider' ),
			'context'     => defined( 'ASTRA_EXT_VER' ) ? array(
				Astra_Builder_Helper::$general_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[header-search-box-type]',
					'operator' => 'in',
					'value'    => array( 'slide-search', 'search-box' ),
				),
			) : Astra_Builder_Helper::$general_tab,
		),

		/**
		 * Option: Live Search.
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[live-search]',
			'default'  => astra_get_option( 'live-search' ),
			'type'     => 'control',
			'control'  => 'ast-toggle-control',
			'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
			'section'  => $_section,
			'title'    => __( 'Live Search', 'astra' ),
			'priority' => 5,
			'context'  => Astra_Builder_Helper::$general_tab,
		),

		/**
		 * Option: Live Search based on Post Types.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[live-search-post-types]',
			'default'     => astra_get_option( 'live-search-post-types' ),
			'type'        => 'control',
			'control'     => 'ast-multi-selector',
			'section'     => $_section,
			'priority'    => 5,
			'title'       => __( 'Search Within Post Types', 'astra' ),
			'context'     => array(
				Astra_Builder_Helper::$general_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[live-search]',
					'operator' => '==',
					'value'    => true,
				),
			),
			'transport'   => 'refresh',
			'choices'     => astra_customizer_search_post_types_choices(),
			'divider'     => array( 'ast_class' => 'ast-top-dotted-divider' ),
			'renderAs'    => 'text',
			'input_attrs' => array(
				'stack_after' => 2, // Currently stack options supports after 2 & 3.
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
			'priority' => 220,
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

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_search_configuration();
}
