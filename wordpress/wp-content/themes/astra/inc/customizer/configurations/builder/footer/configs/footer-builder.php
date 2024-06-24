<?php
/**
 * Footer Builder Configuration.
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
 * Register builder footer builder Customizer Configurations.
 *
 * @param array $configurations Astra Customizer Configurations.
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_builder_footer_configuration( $configurations = array() ) {
	$cloned_component_track         = Astra_Builder_Helper::$component_count_array;
	$widget_config                  = array();
	$astra_has_widgets_block_editor = astra_has_widgets_block_editor();

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_html; $index++ ) {

		$footer_html_section = 'section-fb-html-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $footer_html_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		Astra_Builder_Helper::$footer_desktop_items[ 'html-' . $index ] = array(
			'name'    => 'HTML ' . $index,
			'icon'    => 'text',
			'section' => $footer_html_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'html',
			'builder' => 'footer',
		);
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_widgets; $index++ ) {

		$footer_widget_section = 'sidebar-widgets-footer-widget-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $footer_widget_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		Astra_Builder_Helper::$footer_desktop_items[ 'widget-' . $index ] = array(
			'name'    => 'Widget ' . $index,
			'icon'    => 'wordpress',
			'section' => $footer_widget_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'widget',
			'builder' => 'footer',
		);

		if ( $astra_has_widgets_block_editor ) {
			$widget_config[] = array(
				'name'     => $footer_widget_section,
				'type'     => 'section',
				'priority' => 5,
				'panel'    => 'panel-footer-builder-group',
			);
		}
	}

	if ( $astra_has_widgets_block_editor ) {
		$configurations = array_merge( $configurations, $widget_config );
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_button; $index++ ) {

		$footer_button_section = 'section-fb-button-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $footer_button_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		Astra_Builder_Helper::$footer_desktop_items[ 'button-' . $index ] = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_footer_button ) ? 'Button' : 'Button ' . $index,
			'icon'    => 'admin-links',
			'section' => $footer_button_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'button',
			'builder' => 'footer',
		);
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_social_icons; $index++ ) {

		$footer_social_section = 'section-fb-social-icons-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $footer_social_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		Astra_Builder_Helper::$footer_desktop_items[ 'social-icons-' . $index ] = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_footer_social_icons ) ? 'Social' : 'Social ' . $index,
			'icon'    => 'share',
			'section' => $footer_social_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'social-icons',
			'builder' => 'footer',
		);
	}

	$zone_base = array( 'above', 'primary', 'below' );
	$zones     = array(
		'above'   => array(),
		'primary' => array(),
		'below'   => array(),
	);

	foreach ( $zone_base as $key => $base ) {
		for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_footer_columns; $index++ ) {
			$zones[ $base ][ $base . '_' . $index ] = ucfirst( $base ) . ' Section ' . $index;
		}
	}

	$_configs = array(

		array(
			'name'     => 'panel-footer-builder-group',
			'type'     => 'panel',
			'priority' => 60,
			'title'    => __( 'Footer Builder', 'astra' ),
		),

		/**
		 * Option: Footer Layout
		 */
		array(
			'name'     => 'section-footer-builder-layout',
			'type'     => 'section',
			'priority' => 5,
			'title'    => __( 'Footer Layout', 'astra' ),
			'panel'    => 'panel-footer-builder-group',
		),

		/**
		 * Option: Header Builder Tabs
		 */
		array(
			'name'        => 'section-footer-builder-layout-ast-context-tabs',
			'section'     => 'section-footer-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 0,
			'description' => '',
		),

		/*
		* Header Builder section
		*/
		array(
			'name'     => 'section-footer-builder',
			'type'     => 'section',
			'priority' => 5,
			'title'    => __( 'Footer Builder', 'astra' ),
			'panel'    => 'panel-footer-builder-group',
			'context'  => array(
				array(
					'setting'  => 'ast_selected_tab',
					'operator' => 'in',
					'value'    => array( 'general', 'design' ),
				),
			),
		),

		/**
		 * Option: Footer Builder
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[builder-footer]',
			'section'     => 'section-footer-builder',
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 20,
			'description' => '',
			'context'     => array(),
			'divider'     => ( astra_showcase_upgrade_notices() ) ? array() : array( 'ast_class' => 'ast-pro-available' ),
		),

		// Group Option: Global Footer Background styling.
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[footer-bg-obj-responsive]',
			'type'      => 'control',
			'control'   => 'ast-responsive-background',
			'default'   => astra_get_option( 'footer-bg-obj-responsive' ),
			'section'   => 'section-footer-builder-layout',
			'transport' => 'postMessage',
			'priority'  => 70,
			'title'     => __( 'Background Color-Image', 'astra' ),
			'context'   => Astra_Builder_Helper::$design_tab,
			'divider'   => array( 'ast_class' => 'ast-section-spacing' ),
		),

		// Footer Background Color notice.
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[footer-bg-obj-responsive-description]',
			'type'     => 'control',
			'control'  => 'ast-description',
			'section'  => 'section-footer-builder-layout',
			'priority' => 71,
			'label'    => '',
			'help'     => __( 'If this color setting is not reflecting, check if colors are set from dedicated above, below or primary footer settings.', 'astra' ),
			'context'  => Astra_Builder_Helper::$design_tab,
		),

		/**
		 * Option: Footer Desktop Items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[footer-desktop-items]',
			'section'     => 'section-footer-builder',
			'type'        => 'control',
			'control'     => 'ast-builder',
			'title'       => __( 'Footer Builder', 'astra' ),
			'priority'    => 10,
			'default'     => astra_get_option( 'footer-desktop-items' ),
			'choices'     => Astra_Builder_Helper::$footer_desktop_items,
			'transport'   => 'postMessage',
			'partial'     => array(
				'selector'            => '.site-footer',
				'container_inclusive' => true,
				'render_callback'     => array( Astra_Builder_Footer::get_instance(), 'footer_markup' ),
			),
			'input_attrs' => array(
				'group'   => ASTRA_THEME_SETTINGS . '[footer-desktop-items]',
				'rows'    => array( 'above', 'primary', 'below' ),
				'zones'   => $zones,
				'layouts' => array(
					'above'   => array(
						'column' => astra_get_option( 'hba-footer-column' ),
						'layout' => astra_get_option( 'hba-footer-layout' ),
					),
					'primary' => array(
						'column' => astra_get_option( 'hb-footer-column' ),
						'layout' => astra_get_option( 'hb-footer-layout' ),
					),
					'below'   => array(
						'column' => astra_get_option( 'hbb-footer-column' ),
						'layout' => astra_get_option( 'hbb-footer-layout' ),
					),
				),
				'status'  => array(
					'above'   => true,
					'primary' => true,
					'below'   => true,
				),
			),
			'context'     => array(
				array(
					'setting'  => 'ast_selected_tab',
					'operator' => 'in',
					'value'    => array( 'general', 'design' ),
				),
			),
		),

		/**
		 * Footer Available draggable items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[footer-draggable-items]',
			'section'     => 'section-footer-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-draggable-items',
			'priority'    => 10,
			'input_attrs' => array(
				'group' => ASTRA_THEME_SETTINGS . '[footer-desktop-items]',
				'zones' => array( 'above', 'primary', 'below' ),
			),
			'context'     => Astra_Builder_Helper::$general_tab,
			'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
		),
	);

	if ( astra_showcase_upgrade_notices() ) {
		$_configs[] = array(
			'name'     => ASTRA_THEME_SETTINGS . '[footer-builder-pro-items]',
			'type'     => 'control',
			'control'  => 'ast-upgrade',
			'renderAs' => 'list',
			'choices'  => array(
				'two'   => array(
					'title' => __( 'Divider element', 'astra' ),
				),
				'three' => array(
					'title' => __( 'Language Switcher element', 'astra' ),
				),
				'five'  => array(
					'title' => __( 'Clone, Delete element options', 'astra' ),
				),
				'six'   => array(
					'title' => __( 'Increased element count', 'astra' ),
				),
				'seven' => array(
					'title' => __( 'More design options', 'astra' ),
				),
			),
			'section'  => 'section-footer-builder-layout',
			'default'  => '',
			'context'  => array(),
			'priority' => 999,
			'title'    => __( 'Finish your page on a high with amazing website footers', 'astra' ),
			'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
		);
	}

	$_configs = array_merge( $_configs, Astra_Extended_Base_Configuration::prepare_advanced_tab( 'section-footer-builder-layout' ) );

	$_configs = array_merge( $_configs, $configurations );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_footer_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_builder_footer_configuration();
}
