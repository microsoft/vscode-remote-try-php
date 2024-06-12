<?php
/**
 * General Options for Astra Theme.
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

if ( ! class_exists( 'Astra_Site_Container_Layout_Configs' ) ) {

	/**
	 * Register Astra Site Container Layout Customizer Configurations.
	 */
	class Astra_Site_Container_Layout_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra Site Container Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_section = 'section-colors-background';

			if ( class_exists( 'Astra_Ext_Extension' ) && Astra_Ext_Extension::is_active( 'colors-and-background' ) && ! astra_has_gcp_typo_preset_compatibility() ) {
				$_section = 'section-colors-body';
			}

			$_configs = array(

				/**
				 * Option: Global Revamped Container Layouts.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[ast-site-content-layout]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'section'           => 'section-container-layout',
					'default'           => astra_get_option( 'ast-site-content-layout', 'normal-width-container' ),
					'priority'          => 9,
					'title'             => __( 'Container Layout', 'astra' ),
					'transport'         => 'refresh',
					'choices'           => array(
						'normal-width-container' => array(
							'label' => __( 'Normal', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'normal-width-container', false ) : '',
						),
						'narrow-width-container' => array(
							'label' => __( 'Narrow', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'narrow-width-container', false ) : '',
						),
						'full-width-container'   => array(
							'label' => __( 'Full Width', 'astra' ),
							'path'  => ( class_exists( 'Astra_Builder_UI_Controller' ) ) ? Astra_Builder_UI_Controller::fetch_svg_icon( 'full-width-container', false ) : '',
						),
					),
					'divider'           => array( 'ast_class' => 'ast-section-spacing ast-bottom-spacing ast-bottom-divider' ),
				),

				/**
				 * Option: Global Content Style.
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[site-content-style]',
					'type'        => 'control',
					'control'     => 'ast-selector',
					'section'     => 'section-container-layout',
					'default'     => astra_get_option( 'site-content-style', 'boxed' ),
					'priority'    => 9,
					'description' => __( 'Container style will apply only when layout is set to either normal or narrow.', 'astra' ),
					'title'       => __( 'Container Style', 'astra' ),
					'choices'     => array(
						'unboxed' => __( 'Unboxed', 'astra' ),
						'boxed'   => __( 'Boxed', 'astra' ),
					),
					'responsive'  => false,
					'renderAs'    => 'text',
				),

				/**
				 * Option: Theme color heading
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[surface-colors-title]',
					'section'     => $_section,
					'title'       => __( 'Surface Color', 'astra' ),
					'type'        => 'control',
					'control'     => 'ast-group-title',
					'priority'    => 25,
					'responsive'  => true,
					'settings'    => array(),
					'input_attrs' => array(
						'reset_linked_controls' => array(
							'site-layout-outside-bg-obj-responsive',
							'content-bg-obj-responsive',
						),
					),
				),

				/**
				 * Option: Body Background
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[site-layout-outside-bg-obj-responsive]',
					'type'        => 'control',
					'control'     => 'ast-responsive-background',
					'default'     => astra_get_option( 'site-layout-outside-bg-obj-responsive' ),
					'section'     => $_section,
					'transport'   => 'postMessage',
					'priority'    => 25,
					'input_attrs' => array(
						'ignore_responsive_btns' => true,
					),
					'title'       => __( 'Site Background', 'astra' ),
				),
			);

			$section_content_bg_obj = ( class_exists( 'Astra_Ext_Extension' ) && Astra_Ext_Extension::is_active( 'colors-and-background' ) ) ? 'section-colors-body' : 'section-colors-background';

			if ( astra_has_gcp_typo_preset_compatibility() ) {

				$_configs[] = array(
					'name'        => ASTRA_THEME_SETTINGS . '[content-bg-obj-responsive]',
					'default'     => astra_get_option( 'content-bg-obj-responsive' ),
					'type'        => 'control',
					'control'     => 'ast-responsive-background',
					'section'     => $_section,
					'title'       => __( 'Content Background', 'astra' ),
					'transport'   => 'postMessage',
					'input_attrs' => array(
						'ignore_responsive_btns' => true,
					),
					'priority'    => 25,
					'divider'     => defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'colors-and-background' ) ? array( 'ast_class' => 'ast-bottom-section-divider' ) : array(),
				);
			}

			$configurations = array_merge( $configurations, $_configs );

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {
				$config = array(
					array(
						'name'     => ASTRA_THEME_SETTINGS . '[ast-site-layout-button-link]',
						'type'     => 'control',
						'control'  => 'ast-upgrade',
						'renderAs' => 'list',
						'choices'  => array(
							'one'   => array(
								'title' => __( 'Full Width layout', 'astra' ),
							),
							'two'   => array(
								'title' => __( 'Padded layout', 'astra' ),
							),
							'three' => array(
								'title' => __( 'Fluid layout', 'astra' ),
							),
							'four'  => array(
								'title' => __( 'Container spacings', 'astra' ),
							),
						),
						'section'  => 'section-container-layout',
						'default'  => '',
						'priority' => 999,
						'title'    => __( 'Use containers to their maximum potential with Astra Pro', 'astra' ),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					),
				);

				$configurations = array_merge( $configurations, $config );
			}

			return $configurations;
		}
	}
}

new Astra_Site_Container_Layout_Configs();
