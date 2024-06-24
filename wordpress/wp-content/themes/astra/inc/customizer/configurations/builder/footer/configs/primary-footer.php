<?php
/**
 * Primary footer Configuration.
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
 * Register primary footer builder Customizer Configurations.
 *
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_primary_footer_configuration() {
	$_section = 'section-primary-footer-builder';

	$column_count = range( 1, Astra_Builder_Helper::$num_of_footer_columns );
	$column_count = array_combine( $column_count, $column_count );

	$_configs = array(

		// Section: Primary Footer.
		array(
			'name'     => $_section,
			'type'     => 'section',
			'title'    => __( 'Primary Footer', 'astra' ),
			'panel'    => 'panel-footer-builder-group',
			'priority' => 20,
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
		 * Option: Column count
		 */

		array(
			'name'       => ASTRA_THEME_SETTINGS . '[hb-footer-column]',
			'default'    => astra_get_option( 'hb-footer-column' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'section'    => $_section,
			'priority'   => 2,
			'title'      => __( 'Column', 'astra' ),
			'choices'    => $column_count,
			'context'    => Astra_Builder_Helper::$general_tab,
			'transport'  => 'postMessage',
			'partial'    => array(
				'selector'            => '.site-primary-footer-wrap',
				'container_inclusive' => false,
				'render_callback'     => array( Astra_Builder_Footer::get_instance(), 'primary_footer' ),
			),
			'renderAs'   => 'text',
			'responsive' => false,
			'divider'    => array( 'ast_class' => 'ast-section-spacing ast-bottom-dotted-divider' ),
		),

		/**
		 * Option: Row Layout
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[hb-footer-layout]',
			'section'     => $_section,
			'default'     => astra_get_option( 'hb-footer-layout' ),
			'priority'    => 3,
			'title'       => __( 'Layout', 'astra' ),
			'type'        => 'control',
			'control'     => 'ast-row-layout',
			'context'     => Astra_Builder_Helper::$general_tab,
			'input_attrs' => array(
				'responsive' => true,
				'footer'     => 'primary',
				'layout'     => Astra_Builder_Helper::$footer_row_layouts,
			),
			'divider'     => array( 'ast_class' => 'ast-bottom-section-divider' ),
			'transport'   => 'postMessage',
		),

		/**
		 * Option: Layout Width
		 */
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[hb-footer-layout-width]',
			'default'    => astra_get_option( 'hb-footer-layout-width' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'section'    => $_section,
			'priority'   => 25,
			'title'      => __( 'Width', 'astra' ),
			'choices'    => array(
				'full'    => __( 'Full Width', 'astra' ),
				'content' => __( 'Content Width', 'astra' ),
			),
			'context'    => Astra_Builder_Helper::$general_tab,
			'transport'  => 'postMessage',
			'renderAs'   => 'text',
			'responsive' => false,
			'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
		),

		// Section: Primary Footer Height.
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[hb-primary-footer-height]',
			'section'     => $_section,
			'transport'   => 'refresh',
			'default'     => astra_get_option( 'hb-primary-footer-height' ),
			'priority'    => 30,
			'title'       => __( 'Height', 'astra' ),
			'suffix'      => 'px',
			'type'        => 'control',
			'control'     => 'ast-slider',
			'input_attrs' => array(
				'min'  => 30,
				'step' => 1,
				'max'  => 600,
			),
			'divider'     => array( 'ast_class' => 'ast-bottom-section-divider' ),
			'context'     => Astra_Builder_Helper::$general_tab,
		),


		/**
		 * Option: Vertical Alignment
		 */
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[hb-footer-vertical-alignment]',
			'default'    => astra_get_option( 'hb-footer-vertical-alignment' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'section'    => $_section,
			'priority'   => 30,
			'title'      => __( 'Vertical Alignment', 'astra' ),
			'choices'    => array(
				'flex-start' => __( 'Top', 'astra' ),
				'center'     => __( 'Middle', 'astra' ),
				'flex-end'   => __( 'Bottom', 'astra' ),
			),
			'context'    => Astra_Builder_Helper::$general_tab,
			'transport'  => 'postMessage',
			'renderAs'   => 'text',
			'responsive' => false,
		),

		array(
			'name'     => ASTRA_THEME_SETTINGS . '[hb-stack]',
			'default'  => astra_get_option( 'hb-stack' ),
			'type'     => 'control',
			'control'  => 'ast-selector',
			'section'  => $_section,
			'priority' => 5,
			'title'    => __( 'Inner Elements Layout', 'astra' ),
			'choices'  => array(
				'stack'  => __( 'Stack', 'astra' ),
				'inline' => __( 'Inline', 'astra' ),
			),
			'context'  => Astra_Builder_Helper::$general_tab,
			'renderAs' => 'text',
			'divider'  => array( 'ast_class' => 'ast-bottom-section-divider' ),
		),

		// Option: Footer Separator.
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[hb-footer-main-sep]',
			'transport'   => 'postMessage',
			'default'     => astra_get_option( 'hb-footer-main-sep' ),
			'type'        => 'control',
			'control'     => 'ast-slider',
			'section'     => $_section,
			'priority'    => 4,
			'title'       => __( 'Top Border Size', 'astra' ),
			'suffix'      => 'px',
			'input_attrs' => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 600,
			),
			'context'     => Astra_Builder_Helper::$design_tab,
			'divider'     => array( 'ast_class' => 'ast-section-spacing ast-bottom-dotted-divider' ),
		),

		// Option: Footer Top Boder Color.
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[hb-footer-main-sep-color]',
			'transport'         => 'postMessage',
			'default'           => astra_get_option( 'hb-footer-main-sep-color' ),
			'type'              => 'control',
			'control'           => 'ast-color',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
			'section'           => $_section,
			'priority'          => 5,
			'title'             => __( 'Border Color', 'astra' ),
			'context'           => array(
				Astra_Builder_Helper::$design_tab_config,
				array(
					'setting'  => ASTRA_THEME_SETTINGS . '[hb-footer-main-sep]',
					'operator' => '>=',
					'value'    => 1,
				),
			),
			'divider'           => array( 'ast_class' => 'ast-bottom-section-divider' ),
		),

		// Sub Option: Footer Background.
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[hb-footer-bg-obj-responsive]',
			'section'    => $_section,
			'type'       => 'control',
			'control'    => 'ast-responsive-background',
			'transport'  => 'postMessage',
			'priority'   => 7,
			'data_attrs' => array(
				'name' => 'hb-footer-bg-obj-responsive',
			),
			'default'    => astra_get_option( 'hb-footer-bg-obj-responsive' ),
			'title'      => __( 'Background', 'astra' ),
			'context'    => Astra_Builder_Helper::$design_tab,
			'divider'    => array( 'ast_class' => 'ast-bottom-section-divider' ),
		),

		/**
		 * Option: Inner Spacing
		 */
		array(
			'name'              => ASTRA_THEME_SETTINGS . '[hb-inner-spacing]',
			'section'           => $_section,
			'priority'          => 205,
			'transport'         => 'postMessage',
			'default'           => astra_get_option( 'hb-inner-spacing' ),
			'title'             => __( 'Inner Column Spacing', 'astra' ),
			'suffix'            => 'px',
			'type'              => 'control',
			'control'           => 'ast-responsive-slider',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
			'input_attrs'       => array(
				'min'  => 0,
				'step' => 1,
				'max'  => 200,
			),
			'context'           => Astra_Builder_Helper::$design_tab,
		),

	);

	$_configs = array_merge( $_configs, Astra_Extended_Base_Configuration::prepare_advanced_tab( $_section ) );

	$_configs = array_merge( $_configs, Astra_Builder_Base_Configuration::prepare_visibility_tab( $_section, 'footer' ) );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_footer_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_primary_footer_configuration();
}
