<?php
/**
 * Bottom Footer Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sidebar_Layout_Configs' ) ) {

	/**
	 * Register Astra Sidebar Layout Configurations.
	 */
	class Astra_Sidebar_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra Sidebar Layout Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				/**
				 * Option: Default Sidebar Position
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[site-sidebar-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => 'section-sidebars',
					'default'           => astra_get_option( 'site-sidebar-layout' ),
					'priority'          => 5,
					'description'       => __( 'Sidebar will only apply when container layout is set to normal.', 'astra' ),
					'title'             => __( 'Default Layout', 'astra' ),
					'choices'           => array(
						'no-sidebar'    => array(
							'label' => __( 'No Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'no-sidebar', false ) : '',
						),
						'left-sidebar'  => array(
							'label' => __( 'Left Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'left-sidebar', false ) : '',
						),
						'right-sidebar' => array(
							'label' => __( 'Right Sidebar', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'right-sidebar', false ) : '',
						),
					),
				),

				/**
				 * Option: Site Sidebar Style.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[site-sidebar-style]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-sidebars',
					'default'    => astra_get_option( 'site-sidebar-style', 'unboxed' ),
					'priority'   => 9,
					'title'      => __( 'Sidebar Style', 'astra' ),
					'choices'    => array(
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-divider ast-bottom-section-divider' ),
				),

				/**
				 * Option: Primary Content Width
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[site-sidebar-width]',
					'type'        => 'control',
					'control'     => 'ast-slider',
					'default'     => astra_get_option( 'site-sidebar-width' ),
					'section'     => 'section-sidebars',
					'priority'    => 15,
					'title'       => __( 'Sidebar Width', 'astra' ),
					'suffix'      => '%',
					'transport'   => 'postMessage',
					'input_attrs' => array(
						'min'  => 15,
						'step' => 1,
						'max'  => 50,
					),

				),

				array(
					'name'     => ASTRA_THEME_SETTINGS . '[site-sidebar-width-description]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => 'section-sidebars',
					'priority' => 15,
					'title'    => '',
					'help'     => __( 'Sidebar width will apply only when one of the above sidebar is set.', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-bottom-section-divider' ),
					'settings' => array(),
				),

				/**
				 * Option: Sticky Sidebar
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[site-sticky-sidebar]',
					'default'  => astra_get_option( 'site-sticky-sidebar' ),
					'type'     => 'control',
					'section'  => 'section-sidebars',
					'title'    => __( 'Enable Sticky Sidebar', 'astra' ),
					'priority' => 15,
					'control'  => 'ast-toggle-control',
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),
			);

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {
				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-sidebar-pro-items]',
					'type'     => 'control',
					'control'  => 'ast-upgrade',
					'renderAs' => 'list',
					'choices'  => array(
						'one'   => array(
							'title' => __( 'Sidebar spacing', 'astra' ),
						),
						'two'   => array(
							'title' => __( 'Sidebar color options', 'astra' ),
						),
						'three' => array(
							'title' => __( 'Widget color options', 'astra' ),
						),
						'four'  => array(
							'title' => __( 'Widget title typography', 'astra' ),
						),
						'five'  => array(
							'title' => __( 'Widget content typography', 'astra' ),
						),
					),
					'section'  => 'section-sidebars',
					'default'  => '',
					'priority' => 999,
					'title'    => __( 'Make sidebars work harder to engage with Astra Pro', 'astra' ),
					'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
				);
			}

			return array_merge( $configurations, $_configs );
		}
	}
}


new Astra_Sidebar_Layout_Configs();





