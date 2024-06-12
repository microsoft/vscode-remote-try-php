<?php
/**
 * Header Builder Configuration.
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
 * Register header_builder header builder Customizer Configurations.
 *
 * @param array $configurations Astra Customizer Configurations.
 * @since 4.5.2
 * @return array Astra Customizer Configurations with updated configurations.
 */
function astra_header_header_builder_configuration( $configurations = array() ) {
	$cloned_component_track         = Astra_Builder_Helper::$component_count_array;
	$widget_config                  = array();
	$astra_has_widgets_block_editor = astra_has_widgets_block_editor();

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_button; $index++ ) {

		$header_button_section = 'section-hb-button-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $header_button_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		$item = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_header_button ) ? 'Button' : 'Button ' . $index,
			'icon'    => 'admin-links',
			'section' => $header_button_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'button',
			'builder' => 'header',
		);

		Astra_Builder_Helper::$header_desktop_items[ 'button-' . $index ] = $item;
		Astra_Builder_Helper::$header_mobile_items[ 'button-' . $index ]  = $item;
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_html; $index++ ) {

		$header_html_section = 'section-hb-html-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $header_html_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		$item = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_header_html ) ? 'HTML' : 'HTML ' . $index,
			'icon'    => 'text',
			'section' => $header_html_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'html',
			'builder' => 'header',
		);

		Astra_Builder_Helper::$header_desktop_items[ 'html-' . $index ] = $item;
		Astra_Builder_Helper::$header_mobile_items[ 'html-' . $index ]  = $item;
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_widgets; $index++ ) {

		$header_widget_section = 'sidebar-widgets-header-widget-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $header_widget_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		$item = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_header_widgets ) ? 'Widget' : 'Widget ' . $index,
			'icon'    => 'wordpress',
			'section' => $header_widget_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'widget',
			'builder' => 'header',
		);

		if ( $astra_has_widgets_block_editor ) {
			$widget_config[] = array(
				'name'     => $header_widget_section,
				'type'     => 'section',
				'priority' => 5,
				'panel'    => 'panel-header-builder-group',
			);
		}

		Astra_Builder_Helper::$header_desktop_items[ 'widget-' . $index ] = $item;
		Astra_Builder_Helper::$header_mobile_items[ 'widget-' . $index ]  = $item;
	}

	if ( $astra_has_widgets_block_editor ) {
		$configurations = array_merge( $configurations, $widget_config );
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_menu; $index++ ) {

		switch ( $index ) {
			case 1:
				$name = __( 'Primary Menu', 'astra' );
				break;
			case 2:
				$name = __( 'Secondary Menu', 'astra' );
				break;
			default:
				$name = __( 'Menu ', 'astra' ) . $index;
				break;
		}

		$item = array(
			'name'    => $name,
			'icon'    => 'menu',
			'section' => 'section-hb-menu-' . $index,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'menu',
			'builder' => 'header',
		);

		Astra_Builder_Helper::$header_desktop_items[ 'menu-' . $index ] = $item;

		Astra_Builder_Helper::$header_mobile_items[ 'menu-' . $index ] = $item;
	}

	for ( $index = 1; $index <= Astra_Builder_Helper::$num_of_header_social_icons; $index++ ) {

		$header_social_section = 'section-hb-social-icons-' . $index;

		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( in_array( $header_social_section, $cloned_component_track['removed-items'], true ) ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			continue;
		}

		$item = array(
			'name'    => ( 1 === Astra_Builder_Helper::$num_of_header_social_icons ) ? 'Social' : 'Social ' . $index,
			'icon'    => 'share',
			'section' => $header_social_section,
			'clone'   => defined( 'ASTRA_EXT_VER' ),
			'type'    => 'social-icons',
			'builder' => 'header',
		);

		Astra_Builder_Helper::$header_desktop_items[ 'social-icons-' . $index ] = $item;
		Astra_Builder_Helper::$header_mobile_items[ 'social-icons-' . $index ]  = $item;
	}

	$_configs = array(

		/*
		* Header Builder section
		*/
		array(
			'name'     => 'section-header-builder',
			'type'     => 'section',
			'priority' => 5,
			'title'    => __( 'Header Builder', 'astra' ),
			'panel'    => 'panel-header-builder-group',
		),

		/**
		 * Option: Header Layout
		 */
		array(
			'name'     => 'section-header-builder-layout',
			'type'     => 'section',
			'priority' => 0,
			'title'    => __( 'Header Layout', 'astra' ),
			'panel'    => 'panel-header-builder-group',
		),

		/**
		 * Option: Header Builder Tabs
		 */
		array(
			'name'        => 'section-header-builder-layout-ast-context-tabs',
			'section'     => 'section-header-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 0,
			'description' => '',
		),

		/**
		 * Header Clone Component Track.
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[cloned-component-track]',
			'section'   => 'section-header-builder-layout',
			'type'      => 'control',
			'control'   => 'ast-hidden',
			'priority'  => 43,
			'transport' => 'postMessage',
			'partial'   => false,
			'default'   => astra_get_option( 'cloned-component-track' ),
		),

		/**
		 * Option: Header Builder
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[builder-header]',
			'section'     => 'section-header-builder',
			'type'        => 'control',
			'control'     => 'ast-builder-header-control',
			'priority'    => 40,
			'description' => '',
			'context'     => array(),
			'divider'     => ( astra_showcase_upgrade_notices() ) ? array() : array( 'ast_class' => 'ast-pro-available' ),
		),

		/**
		 * Option: Header Desktop Items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-desktop-items]',
			'section'     => 'section-header-builder',
			'type'        => 'control',
			'control'     => 'ast-builder',
			'title'       => __( 'Header Builder', 'astra' ),
			'priority'    => 25,
			'default'     => astra_get_option( 'header-desktop-items' ),
			'choices'     => Astra_Builder_Helper::$header_desktop_items,
			'transport'   => 'refresh',
			'input_attrs' => array(
				'group'  => ASTRA_THEME_SETTINGS . '[header-desktop-items]',
				'rows'   => array( 'popup', 'above', 'primary', 'below' ),
				'zones'  => array(
					'popup'   => array(
						'popup_content' => 'Popup Content',
					),
					'above'   => array(
						'above_left'         => 'Top - Left',
						'above_left_center'  => 'Top - Left Center',
						'above_center'       => 'Top - Center',
						'above_right_center' => 'Top - Right Center',
						'above_right'        => 'Top - Right',
					),
					'primary' => array(
						'primary_left'         => 'Main - Left',
						'primary_left_center'  => 'Main - Left Center',
						'primary_center'       => 'Main - Center',
						'primary_right_center' => 'Main - Right Center',
						'primary_right'        => 'Main - Right',
					),
					'below'   => array(
						'below_left'         => 'Bottom - Left',
						'below_left_center'  => 'Bottom - Left Center',
						'below_center'       => 'Bottom - Center',
						'below_right_center' => 'Bottom - Right Center',
						'below_right'        => 'Bottom - Right',
					),
				),
				'status' => array(
					'above'   => true,
					'primary' => true,
					'below'   => true,
				),
			),
			'context'     => array(
				array(
					'setting' => 'ast_selected_device',
					'value'   => 'desktop',
				),
			),
		),

		array(
			'name'      => ASTRA_THEME_SETTINGS . '[header-preset-style]',
			'default'   => astra_get_option( 'header-preset-style' ),
			'type'      => 'control',
			'control'   => 'ast-header-presets',
			'section'   => 'section-header-builder-layout',
			'priority'  => 10,
			'title'     => __( 'Header Presets', 'astra' ),
			'options'   => array(
				'preset_1' => array(
					'src'     => 'header-preset-1',
					'options' => array(
						'header-desktop-items' => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
							'above'   => array(
								'above_left'         => array(),
								'above_left_center'  => array(),
								'above_center'       => array(),
								'above_right_center' => array(),
								'above_right'        => array(),
							),
							'primary' => array(
								'primary_left'         => array( 'logo' ),
								'primary_left_center'  => array(),
								'primary_center'       => array(),
								'primary_right_center' => array(),
								'primary_right'        => array( 'menu-1', 'social-icons-1' ),
							),
							'below'   => array(
								'below_left'         => array(),
								'below_left_center'  => array(),
								'below_center'       => array(),
								'below_right_center' => array(),
								'below_right'        => array(),
							),
						),
						'header-mobile-items'  => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu', 'social-icons-1' ) ),
							'above'   => array(
								'above_left'   => array(),
								'above_center' => array(),
								'above_right'  => array(),
							),
							'primary' => array(
								'primary_left'   => array( 'logo' ),
								'primary_center' => array(),
								'primary_right'  => array( 'mobile-trigger' ),
							),
							'below'   => array(
								'below_left'   => array(),
								'below_center' => array(),
								'below_right'  => array(),
							),
						),
					),
				),
				'preset_2' => array(
					'src'     => 'header-preset-2',
					'options' => array(
						'header-desktop-items' => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
							'above'   => array(
								'above_left'         => array(),
								'above_left_center'  => array(),
								'above_center'       => array(),
								'above_right_center' => array(),
								'above_right'        => array(),
							),
							'primary' => array(
								'primary_left'         => array( 'logo' ),
								'primary_left_center'  => array(),
								'primary_center'       => array(),
								'primary_right_center' => array(),
								'primary_right'        => array( 'menu-1', 'button-1' ),
							),
							'below'   => array(
								'below_left'         => array(),
								'below_left_center'  => array(),
								'below_center'       => array(),
								'below_right_center' => array(),
								'below_right'        => array(),
							),
						),
						'header-mobile-items'  => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu', 'button-1' ) ),
							'above'   => array(
								'above_left'   => array(),
								'above_center' => array(),
								'above_right'  => array(),
							),
							'primary' => array(
								'primary_left'   => array( 'logo' ),
								'primary_center' => array(),
								'primary_right'  => array( 'mobile-trigger' ),
							),
							'below'   => array(
								'below_left'   => array(),
								'below_center' => array(),
								'below_right'  => array(),
							),
						),
					),
				),
				'preset_3' => array(
					'src'     => 'header-preset-3',
					'options' => array(
						'header-desktop-items' => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
							'above'   => array(
								'above_left'         => array(),
								'above_left_center'  => array(),
								'above_center'       => array(),
								'above_right_center' => array(),
								'above_right'        => array(),
							),
							'primary' => array(
								'primary_left'         => array( 'logo', 'menu-1' ),
								'primary_left_center'  => array(),
								'primary_center'       => array(),
								'primary_right_center' => array(),
								'primary_right'        => array( 'html-1', 'button-1' ),
							),
							'below'   => array(
								'below_left'         => array(),
								'below_left_center'  => array(),
								'below_center'       => array(),
								'below_right_center' => array(),
								'below_right'        => array(),
							),
						),
						'header-mobile-items'  => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu', 'html-1', 'button-1' ) ),
							'above'   => array(
								'above_left'   => array(),
								'above_center' => array(),
								'above_right'  => array(),
							),
							'primary' => array(
								'primary_left'   => array( 'logo' ),
								'primary_center' => array(),
								'primary_right'  => array( 'mobile-trigger' ),
							),
							'below'   => array(
								'below_left'   => array(),
								'below_center' => array(),
								'below_right'  => array(),
							),
						),
						'header-html-1'        => '<a href="#">' . esc_html__( 'Log in', 'astra' ) . '</a>',
						'header-button1-text'  => esc_html__( 'Sign up', 'astra' ),
					),
				),
				'preset_4' => array(
					'src'     => 'header-preset-4',
					'options' => array(
						'hba-header-separator' => '0',
						'hb-header-height'     => array(
							'desktop' => 80,
							'tablet'  => '',
							'mobile'  => '',
						),
						'header-desktop-items' => array(
							'popup'   => array( 'popup_content' => array( 'mobile-menu' ) ),
							'above'   => array(
								'above_left'         => array(),
								'above_left_center'  => array(),
								'above_center'       => array( 'logo' ),
								'above_right_center' => array(),
								'above_right'        => array(),
							),
							'primary' => array(
								'primary_left'         => array(),
								'primary_left_center'  => array(),
								'primary_center'       => array( 'menu-1' ),
								'primary_right_center' => array(),
								'primary_right'        => array(),
							),
							'below'   => array(
								'below_left'         => array(),
								'below_left_center'  => array(),
								'below_center'       => array(),
								'below_right_center' => array(),
								'below_right'        => array(),
							),
						),
						'header-mobile-items'  => array(
							'popup'   => array( 'popup_content' => array( 'search', 'mobile-menu' ) ),
							'above'   => array(
								'above_left'   => array(),
								'above_center' => array(),
								'above_right'  => array(),
							),
							'primary' => array(
								'primary_left'   => array( 'logo' ),
								'primary_center' => array(),
								'primary_right'  => array( 'mobile-trigger' ),
							),
							'below'   => array(
								'below_left'   => array(),
								'below_center' => array(),
								'below_right'  => array(),
							),
						),
					),
				),
			),
			'transport' => 'postMessage',
			'divider'   => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Header Desktop Available draggable items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-desktop-draggable-items]',
			'section'     => 'section-header-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-draggable-items',
			'priority'    => 30,
			'input_attrs' => array(
				'group' => ASTRA_THEME_SETTINGS . '[header-desktop-items]',
				'zones' => array( 'popup', 'above', 'primary', 'below' ),
			),
			'context'     => array(
				array(
					'setting' => 'ast_selected_device',
					'value'   => 'desktop',
				),
				array(
					'setting' => 'ast_selected_tab',
					'value'   => 'general',
				),
			),
			'divider'     => array( 'ast_class' => 'ast-top-section-divider' ),
		),

		/**
		 * Option: Header Mobile Items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-mobile-items]',
			'section'     => 'section-header-builder',
			'type'        => 'control',
			'control'     => 'ast-builder',
			'title'       => __( 'Header Builder', 'astra' ),
			'priority'    => 35,
			'default'     => astra_get_option( 'header-mobile-items' ),
			'choices'     => Astra_Builder_Helper::$header_mobile_items,
			'transport'   => 'refresh',
			'input_attrs' => array(
				'group'  => ASTRA_THEME_SETTINGS . '[header-mobile-items]',
				'rows'   =>
					array( 'popup', 'above', 'primary', 'below' ),
				'zones'  =>
					array(
						'popup'   =>
							array(
								'popup_content' => 'Popup Content',
							),
						'above'   =>
							array(
								'above_left'   => 'Top - Left',
								'above_center' => 'Top - Center',
								'above_right'  => 'Top - Right',
							),
						'primary' =>
							array(
								'primary_left'   => 'Main - Left',
								'primary_center' => 'Main - Center',
								'primary_right'  => 'Main - Right',
							),
						'below'   =>
							array(
								'below_left'   => 'Bottom - Left',
								'below_center' => 'Bottom - Center',
								'below_right'  => 'Bottom - Right',
							),
					),
				'status' => array(
					'above'   => true,
					'primary' => true,
					'below'   => true,
				),
			),
			'context'     => Astra_Builder_Helper::$responsive_devices,
		),

		/**
		 * Header Mobile Available draggable items.
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-mobile-draggable-items]',
			'section'     => 'section-header-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-draggable-items',
			'input_attrs' => array(
				'group' => ASTRA_THEME_SETTINGS . '[header-mobile-items]',
				'zones' => array( 'popup', 'above', 'primary', 'below' ),
			),
			'priority'    => 43,
			'context'     => array(
				array(
					'setting'  => 'ast_selected_device',
					'operator' => 'in',
					'value'    => array( 'tablet', 'mobile' ),
				),
				array(
					'setting' => 'ast_selected_tab',
					'value'   => 'general',
				),
			),
			'divider'     => array( 'ast_class' => 'ast-top-section-divider' ),
		),

		/**
		 * Header Mobile popup items.
		 */
		array(
			'name'      => ASTRA_THEME_SETTINGS . '[header-mobile-popup-items]',
			'section'   => 'section-header-builder-layout',
			'type'      => 'control',
			'control'   => 'ast-hidden',
			'priority'  => 43,
			'transport' => 'postMessage',
			'partial'   => array(
				'selector'            => '#ast-mobile-popup-wrapper',
				'container_inclusive' => true,
				'render_callback'     => array( Astra_Builder_Header::get_instance(), 'mobile_popup' ),
			),
			'default'   => false,
		),

		/**
		 * Option: Blog Color Section heading
		 */
		array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-transparent-link-heading]',
			'type'     => 'control',
			'control'  => 'ast-heading',
			'section'  => 'section-header-builder-layout',
			'title'    => __( 'Header Types', 'astra' ),
			'priority' => 44,
			'settings' => array(),
			'context'  => Astra_Builder_Helper::$general_tab,
			'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
		),

		/**
		 * Option: Header Transparant
		 */
		array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-transparant-link]',
			'section'     => 'section-header-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-header-type-button',
			'input_attrs' => array(
				'section' => 'section-transparent-header',
				'label'   => esc_html__( 'Transparent Header', 'astra' ),
			),
			'priority'    => 45,
			'context'     => Astra_Builder_Helper::$general_tab,
			'settings'    => false,
			'divider'     => array( 'ast_class' => 'ast-section-spacing' ),
		),

		// Option: Header Width.
		array(
			'name'       => ASTRA_THEME_SETTINGS . '[hb-header-main-layout-width]',
			'default'    => astra_get_option( 'hb-header-main-layout-width' ),
			'type'       => 'control',
			'control'    => 'ast-selector',
			'section'    => 'section-header-builder-layout',
			'priority'   => 4,
			'title'      => __( 'Width', 'astra' ),
			'choices'    => array(
				'full'    => __( 'Full Width', 'astra' ),
				'content' => __( 'Content Width', 'astra' ),
			),
			'context'    => array(
				array(
					'setting' => 'ast_selected_tab',
					'value'   => 'design',
				),
				array(
					'setting' => 'ast_selected_device',
					'value'   => 'desktop',
				),
			),
			'transport'  => 'postMessage',
			'renderAs'   => 'text',
			'responsive' => false,
			'divider'    => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
		),

		array(
			'name'              => ASTRA_THEME_SETTINGS . '[section-header-builder-layout-margin]',
			'default'           => astra_get_option( 'section-header-builder-layout-margin' ),
			'type'              => 'control',
			'transport'         => 'postMessage',
			'control'           => 'ast-responsive-spacing',
			'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
			'section'           => 'section-header-builder-layout',
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

	// Learn More link if Astra Pro is not activated.
	if ( astra_showcase_upgrade_notices() ) {
		/**
		 * Option: Pro options
		 */
		$_configs[] = array(
			'name'     => ASTRA_THEME_SETTINGS . '[header-builder-pro-items]',
			'type'     => 'control',
			'control'  => 'ast-upgrade',
			'renderAs' => 'list',
			'choices'  => array(
				'one'   => array(
					'title' => __( 'Sticky header', 'astra' ),
				),
				'two'   => array(
					'title' => __( 'Divider element', 'astra' ),
				),
				'three' => array(
					'title' => __( 'Language Switcher element', 'astra' ),
				),
				'four'  => array(
					'title' => __( 'Toggle Button element', 'astra' ),
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
			'section'  => 'section-header-builder-layout',
			'default'  => '',
			'priority' => 999,
			'context'  => array(),
			'title'    => __( 'Make an instant connection with amazing site headers', 'astra' ),
			'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
		);
	}

	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'sticky-header' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		/**
		 * Option: Header Transparant
		 */
		$_configs[] = array(
			'name'        => ASTRA_THEME_SETTINGS . '[header-sticky-link]',
			'section'     => 'section-header-builder-layout',
			'type'        => 'control',
			'control'     => 'ast-header-type-button',
			'input_attrs' => array(
				'section' => 'section-sticky-header',
				'label'   => esc_html__( 'Sticky Header', 'astra' ),
			),
			'priority'    => 45,
			'context'     => Astra_Builder_Helper::$general_tab,
			'settings'    => false,
		);
	}

	$_configs = array_merge( $_configs, $configurations );

	if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
		array_map( 'astra_save_header_customizer_configs', $_configs );
	}

	return $_configs;
}

if ( Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
	astra_header_header_builder_configuration();
}
