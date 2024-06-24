<?php
/**
 * Content Spacing Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Lifter_Sidebar_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Lifter_Sidebar_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-LifterLMS Sidebar Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			$common_title                    = __( 'Sidebar Layout', 'astra' );
			$common_section                  = 'section-lifterlms';
			$common_lifter_lms_sidebar_style = __( 'Sidebar Style', 'astra' );
			$lifter_lms_section_divider      = true;

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'lifterlms' ) ) {
				$section_general                        = 'section-lifterlms-general';
				$section_courses                        = 'section-lifterlms-course-lesson';
				$title_lifter_lms                       = $common_title;
				$title_lifter_lms_courses               = $common_title;
				$title_lifter_lms_sidebar_style         = $common_lifter_lms_sidebar_style;
				$title_lifter_lms_courses_sidebar_style = $common_lifter_lms_sidebar_style;
				$lifter_lms_section_divider             = false;
			} else {
				$section_general                        = $common_section;
				$section_courses                        = $common_section;
				$title_lifter_lms                       = __( 'Global Sidebar Layout', 'astra' );
				$title_lifter_lms_courses               = __( 'Course/Lesson Sidebar Layout', 'astra' );
				$title_lifter_lms_sidebar_style         = __( 'Global Sidebar Style', 'astra' );
				$title_lifter_lms_courses_sidebar_style = __( 'Course/Lesson Sidebar Style', 'astra' );
			}


			$_configs = array(

				/**
				 * Option: Global Sidebar Layout.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[lifterlms-sidebar-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => $section_general,
					'default'           => astra_get_option( 'lifterlms-sidebar-layout' ),
					'priority'          => 1,
					'title'             => $title_lifter_lms,
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
				 * Option: LifterLMS Sidebar Style.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[lifterlms-sidebar-style]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => $section_general,
					'default'    => astra_get_option( 'lifterlms-sidebar-style', 'default' ),
					'priority'   => 1,
					'title'      => $title_lifter_lms_sidebar_style,
					'choices'    => array(
						'default' => __( 'Default', 'astra' ),
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-top-divider ast-top-spacing' ),
				),

				/**
				 * Option: Course/Lesson Sidebar Layout.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[lifterlms-course-lesson-sidebar-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => $section_courses,
					'default'           => astra_get_option( 'lifterlms-course-lesson-sidebar-layout' ),
					'priority'          => 1,
					'title'             => $title_lifter_lms_courses,
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
					'divider'           => $lifter_lms_section_divider ? array( 'ast_class' => 'ast-section-spacing ast-top-section-divider' ) : array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Course/Lesson Sidebar Style.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[lifterlms-course-lesson-sidebar-style]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => $section_courses,
					'default'    => astra_get_option( 'lifterlms-course-lesson-sidebar-style', 'default' ),
					'priority'   => 1,
					'title'      => $title_lifter_lms_courses_sidebar_style,
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

new Astra_Lifter_Sidebar_Configs();
