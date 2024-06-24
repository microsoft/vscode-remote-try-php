<?php
/**
 * Styling Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Body_Typo_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Body_Typo_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Body Typography Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$typo_section = astra_has_gcp_typo_preset_compatibility() ? 'section-typography' : 'section-body-typo';

			$_configs = array(

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-body-font-settings-divider]',
					'section'  => $typo_section,
					'title'    => __( 'Base Font', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 6,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Body font family.
				 */
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'default'   => astra_get_option( 'ast-body-font-settings' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'Body Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 6,
					'divider'   => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Font Family
				 */
				array(
					'name'        => 'body-font-family',
					'parent'      => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'type'        => 'sub-control',
					'control'     => 'ast-font',
					'font_type'   => 'ast-font-family',
					'ast_inherit' => __( 'Default System Font', 'astra' ),
					'default'     => astra_get_option( 'body-font-family' ),
					'section'     => $typo_section,
					'priority'    => 6,
					'title'       => __( 'Font Family', 'astra' ),
					'connect'     => ASTRA_THEME_SETTINGS . '[body-font-weight]',
					'variant'     => ASTRA_THEME_SETTINGS . '[body-font-variant]',
					'divider'     => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				),

				/**
				 * Option: Font Variant
				 */
				array(
					'name'              => 'body-font-variant',
					'type'              => 'sub-control',
					'parent'            => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'control'           => 'ast-font-variant',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_variant' ),
					'default'           => astra_get_option( 'body-font-variant' ),
					'ast_inherit'       => __( 'Default', 'astra' ),
					'section'           => $typo_section,
					'priority'          => 15,
					'title'             => '',
					'variant'           => ASTRA_THEME_SETTINGS . '[body-font-family]',
					'context'           => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[body-font-family]',
							'operator' => '!=',
							'value'    => 'inherit',
						),
					),
					'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),

				),

				/**
				 * Option: Font Weight
				 */
				array(
					'name'              => 'body-font-weight',
					'type'              => 'sub-control',
					'parent'            => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'control'           => 'ast-font',
					'font_type'         => 'ast-font-weight',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
					'default'           => astra_get_option( 'body-font-weight' ),
					'ast_inherit'       => __( 'Default', 'astra' ),
					'section'           => $typo_section,
					'priority'          => 14,
					'title'             => __( 'Font Weight', 'astra' ),
					'connect'           => 'body-font-family',
				),

				/**
				 * Option: Body Font Size
				 */
				array(
					'name'        => 'font-size-body',
					'type'        => 'sub-control',
					'parent'      => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'control'     => 'ast-responsive-slider',
					'section'     => $typo_section,
					'default'     => astra_get_option( 'font-size-body' ),
					'priority'    => 15,
					'lazy'        => true,
					'title'       => __( 'Font Size', 'astra' ),
					'suffix'      => array( 'px', 'em', 'vw', 'rem' ),
					'input_attrs' => array(
						'px'  => array(
							'min'  => 0,
							'step' => 1,
							'max'  => 200,
						),
						'em'  => array(
							'min'  => 0,
							'step' => 0.01,
							'max'  => 20,
						),
						'vw'  => array(
							'min'  => 0,
							'step' => 0.1,
							'max'  => 25,
						),
						'rem' => array(
							'min'  => 0,
							'step' => 0.1,
							'max'  => 20,
						),
					),
				),

				/**
				 * Option: Body Font Height
				 */
				array(
					'name'     => 'body-font-extras',
					'type'     => 'sub-control',
					'parent'   => ASTRA_THEME_SETTINGS . '[ast-body-font-settings]',
					'control'  => 'ast-font-extras',
					'section'  => $typo_section,
					'priority' => 25,
					'default'  => astra_get_option( 'body-font-extras' ),
					'title'    => __( 'Font Extras', 'astra' ),
				),

				/**
				 * Option: Headings font family.
				 */
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings]',
					'default'   => astra_get_option( 'ast-headings-font-settings' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'Headings Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 10,
					'divider'   => array( 'ast_class' => 'ast-top-dotted-divider ast-bottom-spacing' ),
				),

				/**
				 * Option: Divider.
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings-divider]',
					'section'  => $typo_section,
					'title'    => __( 'Heading Font', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 10,
					'settings' => array(),
					'divider'  => array( 'ast_class' => 'ast-bottom-spacing' ),
				),

				/**
				 * Option: Headings Font Family
				 */
				array(
					'name'      => 'headings-font-family',
					'type'      => 'sub-control',
					'parent'    => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings]',
					'control'   => 'ast-font',
					'font_type' => 'ast-font-family',
					'default'   => astra_get_option( 'headings-font-family' ),
					'title'     => __( 'Font Family', 'astra' ),
					'section'   => $typo_section,
					'priority'  => 26,
					'connect'   => ASTRA_THEME_SETTINGS . '[headings-font-weight]',
					'variant'   => ASTRA_THEME_SETTINGS . '[headings-font-variant]',
					'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
				),

				/**
				 * Option: Headings Font Weight
				 */
				array(
					'name'              => 'headings-font-weight',
					'type'              => 'sub-control',
					'parent'            => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings]',
					'control'           => 'ast-font',
					'font_type'         => 'ast-font-weight',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
					'default'           => astra_get_option( 'headings-font-weight' ),
					'title'             => __( 'Font Weight', 'astra' ),
					'section'           => $typo_section,
					'priority'          => 26,
					'connect'           => 'headings-font-family',
				),

				/**
				 * Option: Font Variant
				 */
				array(
					'name'              => 'headings-font-variant',
					'type'              => 'sub-control',
					'parent'            => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings]',
					'control'           => 'ast-font-variant',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_variant' ),
					'default'           => astra_get_option( 'headings-font-variant' ),
					'ast_inherit'       => __( 'Default', 'astra' ),
					'section'           => $typo_section,
					'priority'          => 26,
					'variant'           => ASTRA_THEME_SETTINGS . '[headings-font-family]',
					'context'           => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[headings-font-family]',
							'operator' => '!=',
							'value'    => 'inherit',
						),
					),
				),

				/**
				 * Option: Heading Font Height
				 */
				array(
					'name'      => 'headings-font-extras',
					'type'      => 'sub-control',
					'parent'    => ASTRA_THEME_SETTINGS . '[ast-headings-font-settings]',
					'control'   => 'ast-font-extras',
					'transport' => 'postMessage',
					'section'   => $typo_section,
					'priority'  => 26,
					'default'   => astra_get_option( 'headings-font-height-settings' ),
					'title'     => __( 'Font Extras', 'astra' ),
					'divider'   => array( 'ast_class' => 'ast-sub-top-dotted-divider' ),
				),

				/**
				 * Option: Paragraph Margin Bottom
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[para-margin-bottom]',
					'type'              => 'control',
					'control'           => 'ast-slider',
					'default'           => astra_get_option( 'para-margin-bottom' ),
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_number_n_blank' ),
					'transport'         => 'postMessage',
					'section'           => $typo_section,
					'priority'          => 31,
					'title'             => __( 'Paragraph Margin Bottom', 'astra' ),
					'suffix'            => 'em',
					'lazy'              => true,
					'input_attrs'       => array(
						'min'  => 0.5,
						'step' => 0.01,
						'max'  => 5,
					),
					'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
				),

				/**
				 * Option: Underline links in entry-content.
				 */
				array(
					'name'      => ASTRA_THEME_SETTINGS . '[underline-content-links]',
					'default'   => astra_get_option( 'underline-content-links' ),
					'type'      => 'control',
					'control'   => 'ast-toggle-control',
					'section'   => $typo_section,
					'priority'  => 32,
					'divider'   => array( 'ast_class' => 'ast-top-dotted-divider' ),
					'title'     => __( 'Underline Content Links', 'astra' ),
					'transport' => 'postMessage',
				),
			);

			if ( astra_has_gcp_typo_preset_compatibility() ) {

				/**
				 * Option: H1 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h1-typo]',
					'default'   => astra_get_option( 'ast-heading-h1-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H1 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);

				/**
				 * Option: H2 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h2-typo]',
					'default'   => astra_get_option( 'ast-heading-h2-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H2 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);

				/**
				 * Option: H3 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h3-typo]',
					'default'   => astra_get_option( 'ast-heading-h3-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H3 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);

				/**
				 * Option: H4 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h4-typo]',
					'default'   => astra_get_option( 'ast-heading-h4-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H4 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);

				/**
				 * Option: H5 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h5-typo]',
					'default'   => astra_get_option( 'ast-heading-h5-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H5 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);

				/**
				 * Option: H6 Typography Section.
				 */
				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[ast-heading-h6-typo]',
					'default'   => astra_get_option( 'ast-heading-h6-typo' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'H6 Font', 'astra' ),
					'section'   => $typo_section,
					'transport' => 'postMessage',
					'priority'  => 30,
				);
			}

			return array_merge( $configurations, $_configs );
		}
	}
}

new Astra_Body_Typo_Configs();
