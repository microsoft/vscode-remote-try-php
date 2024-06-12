<?php
/**
 * Container Options for Astra theme.
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

if ( ! class_exists( 'Astra_Lifter_Container_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 *
	 * @since 1.4.3
	 */
	class Astra_Lifter_Container_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register LifterLMS Container Settings.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'lifterlms' ) ) {
				$section = 'section-lifterlms-general';
			} else {
				$section = 'section-lifterlms';
			}

			$_configs = array(

				/**
				 * Option: Revamped Container Layout.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[lifterlms-ast-content-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => $section,
					'default'           => astra_get_option( 'lifterlms-ast-content-layout' ),
					'priority'          => 1,
					'title'             => __( 'Container Layout', 'astra' ),
					'choices'           => array(
						'default'                => array(
							'label' => __( 'Default', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'layout-default', false ) : '',
						),
						'normal-width-container' => array(
							'label' => __( 'Normal', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'normal-width-container', false ) : '',
						),
						'full-width-container'   => array(
							'label' => __( 'Full Width', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'full-width-container', false ) : '',
						),
					),
					'divider'           => array( 'ast_class' => 'ast-section-spacing ast-bottom-divider' ),
				),

				/**
				 * Option: Content Style Option.
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[lifterlms-content-style]',
					'type'        => 'control',
					'control'     => 'ast-selector',
					'section'     => $section,
					'default'     => astra_get_option( 'lifterlms-content-style', 'default' ),
					'priority'    => 1,
					'title'       => __( 'Container Style', 'astra' ),
					'description' => __( 'Container style will apply only when layout is set to either normal or narrow.', 'astra' ),
					'choices'     => array(
						'default' => __( 'Default', 'astra' ),
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'renderAs'    => 'text',
					'responsive'  => false,
				),
			);

			return array_merge( $configurations, $_configs );

		}
	}
}

new Astra_Lifter_Container_Configs();
