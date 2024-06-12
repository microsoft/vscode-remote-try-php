<?php
/**
 * Content Spacing Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Learndash_Sidebar_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Learndash_Sidebar_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register LearnDash Sidebar settings.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array(

				/**
				 * Option: Sidebar Layout.
				 */

				array(
					'name'              => ASTRA_THEME_SETTINGS . '[learndash-sidebar-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => 'section-leandash-general',
					'default'           => astra_get_option( 'learndash-sidebar-layout' ),
					'priority'          => 5,
					'title'             => __( 'Sidebar Layout', 'astra' ),
					'choices'           => array(
						'default'       => array(
							'label' => __( 'Default', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'layout-default', false ) : '',
						),
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
					'description'       => __( 'Sidebar will only apply when container layout is set to normal.', 'astra' ),
					'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Learndash Sidebar Style.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[learndash-sidebar-style]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-leandash-general',
					'default'    => astra_get_option( 'learndash-sidebar-style', 'default' ),
					'priority'   => 5,
					'title'      => __( 'Sidebar Style', 'astra' ),
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-divider ast-top-spacing' ),
				),
			);

			return array_merge( $configurations, $_configs );
		}
	}
}

new Astra_Learndash_Sidebar_Configs();
