<?php
/**
 * LifterLMS General Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       1.4.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Lifter_General_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Lifter_General_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-LifterLMS General Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'lifterlms' ) ) {
				$divider_array = array( 'ast_class' => 'ast-bottom-divider' );
				$section       = 'section-lifterlms-general';
			} else {
				$divider_array = array();
				$section       = 'section-lifterlms';
			}

			$_configs = array(

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[llms-course-grid-divider]',
					'section'  => $section,
					'title'    => __( 'Columns', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 1,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Course Columns
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[llms-course-grid]',
					'type'              => 'control',
					'control'           => 'ast-responsive-slider',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
					'section'           => $section,
					'default'           => astra_get_option(
						'llms-course-grid',
						array(
							'desktop' => 3,
							'tablet'  => 2,
							'mobile'  => 1,
						)
					),
					'title'             => __( 'Course Columns', 'astra' ),
					'priority'          => 1,
					'input_attrs'       => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 6,
					),
					'divider'           => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
				),

				/**
				 * Option: Membership Columns
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[llms-membership-grid]',
					'type'              => 'control',
					'control'           => 'ast-responsive-slider',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
					'section'           => $section,
					'default'           => astra_get_option(
						'llms-membership-grid',
						array(
							'desktop' => 3,
							'tablet'  => 2,
							'mobile'  => 1,
						)
					),
					'title'             => __( 'Membership Columns', 'astra' ),
					'priority'          => 1,
					'input_attrs'       => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 6,
					),
				),
			);

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {

				$_configs[] =

					/**
					 * Option: Learn More about Contant Typography
					 */
					array(
						'name'     => ASTRA_THEME_SETTINGS . '[llms-button-link]',
						'type'     => 'control',
						'control'  => 'ast-button-link',
						'section'  => $section,
						'priority' => 999,
						'title'    => __( 'View Astra Pro Features', 'astra' ),
						'url'      => ASTRA_PRO_CUSTOMIZER_UPGRADE_URL,
						'settings' => array(),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					);

			}

			return array_merge( $configurations, $_configs );

		}
	}
}

new Astra_Lifter_General_Configs();
