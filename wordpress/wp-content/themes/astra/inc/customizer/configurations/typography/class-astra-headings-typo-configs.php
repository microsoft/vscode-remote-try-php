<?php
/**
 * Styling Options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2021, Astra
 * @link        https://wpastra.com/
 * @since       Astra 3.7.0
 */

/** @psalm-suppress ParadoxicalCondition **/ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer Sanitizes Initial setup
 */
class Astra_Headings_Typo_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Register headings Typography Customizer Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 3.7.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$section = 'section-typography';

		$_configs = array(

			/**
			 * Heading Typography starts here - h1 - h3
			 */

			/**
			 * Option: Heading <H1> Font Family
			 */
			array(
				'name'      => 'font-family-h1',
				'type'      => 'sub-control',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h1-typo]',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'font-family-h1' ),
				'title'     => __( 'Font Family', 'astra' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h1]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),


			/**
			 * Option: Heading <H1> Font Weight
			 */
			array(
				'name'              => 'font-weight-h1',
				'type'              => 'sub-control',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h1-typo]',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'title'             => __( 'Font Weight', 'astra' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'font-weight-h1' ),
				'section'           => $section,
				'priority'          => 28,
				'connect'           => 'font-family-h1',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading 1 (H1) Font Size
			 */

			array(
				'name'              => 'font-size-h1',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h1-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h1' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
			* Option: Heading H1 Font Extras
			*/
			array(
				'name'     => 'font-extras-h1',
				'type'     => 'sub-control',
				'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h1-typo]',
				'control'  => 'ast-font-extras',
				'section'  => $section,
				'priority' => 28,
				'default'  => astra_get_option( 'font-extras-h1' ),
			),


			/**
			 * Option: Heading <H2> Font Family
			 */
			array(
				'name'      => 'font-family-h2',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h2-typo]',
				'font_type' => 'ast-font-family',
				'title'     => __( 'Font Family', 'astra' ),
				'default'   => astra_get_option( 'font-family-h2' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h2]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading <H2> Font Weight
			 */
			array(
				'name'              => 'font-weight-h2',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h2-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'title'             => __( 'Font Weight', 'astra' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-weight-h2' ),
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'priority'          => 28,
				'connect'           => 'font-family-h2',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading 2 (H2) Font Size
			 */

			array(
				'name'              => 'font-size-h2',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h2-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h2' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
				 * Option: Heading H2 Font Extras
				 */
				array(
					'name'     => 'font-extras-h2',
					'type'     => 'sub-control',
					'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h2-typo]',
					'control'  => 'ast-font-extras',
					'section'  => $section,
					'priority' => 28,
					'default'  => astra_get_option( 'font-extras-h2' ),
				),

			/**
			 * Option: Heading <H3> Font Family
			 */
			array(
				'name'      => 'font-family-h3',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h3-typo]',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'font-family-h3' ),
				'title'     => __( 'Font Family', 'astra' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h3]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading <H3> Font Weight
			 */
			array(
				'name'              => 'font-weight-h3',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h3-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'font-weight-h3' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'section'           => $section,
				'priority'          => 28,
				'connect'           => 'font-family-h3',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading 3 (H3) Font Size
			 */

			array(
				'name'              => 'font-size-h3',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h3-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h3' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
				 * Option: Heading H3 Font Extras
				 */
				array(
					'name'     => 'font-extras-h3',
					'type'     => 'sub-control',
					'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h3-typo]',
					'control'  => 'ast-font-extras',
					'section'  => $section,
					'priority' => 28,
					'default'  => astra_get_option( 'font-extras-h3' ),
				),

			/**
			 * Option: Heading <H4> Font Family
			 */
			array(
				'name'      => 'font-family-h4',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h4-typo]',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'title'     => __( 'Font Family', 'astra' ),
				'default'   => astra_get_option( 'font-family-h4' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h4]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading <H4> Font Weight
			 */
			array(
				'name'              => 'font-weight-h4',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h4-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'default'           => astra_get_option( 'font-weight-h4' ),
				'section'           => $section,
				'priority'          => 28,
				'connect'           => 'font-family-h4',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading 4 (H4) Font Size
			 */

			array(
				'name'              => 'font-size-h4',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h4-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h4' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
			* Option: Heading H4 Font Extras
			*/
			array(
				'name'     => 'font-extras-h4',
				'type'     => 'sub-control',
				'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h4-typo]',
				'control'  => 'ast-font-extras',
				'section'  => $section,
				'priority' => 28,
				'default'  => astra_get_option( 'font-extras-h4' ),
			),

			/**
			 * Option: Heading <H5> Font Family
			 */
			array(
				'name'      => 'font-family-h5',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h5-typo]',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'font-family-h5' ),
				'title'     => __( 'Font Family', 'astra' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h5]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading <H5> Font Weight
			 */
			array(
				'name'              => 'font-weight-h5',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h5-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-weight-h5' ),
				'priority'          => 28,
				'connect'           => 'font-family-h5',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),


			/**
			 * Option: Heading 5 (H5) Font Size
			 */
			array(
				'name'              => 'font-size-h5',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h5-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h5' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
			* Option: Heading H5 Font Extras
			*/
			array(
				'name'     => 'font-extras-h5',
				'type'     => 'sub-control',
				'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h5-typo]',
				'control'  => 'ast-font-extras',
				'section'  => $section,
				'priority' => 28,
				'default'  => astra_get_option( 'font-extras-h5' ),
			),

			/**
			 * Option: Heading <H6> Font Family
			 */
			array(
				'name'      => 'font-family-h6',
				'parent'    => ASTRA_THEME_SETTINGS . '[ast-heading-h6-typo]',
				'type'      => 'sub-control',
				'control'   => 'ast-font',
				'font_type' => 'ast-font-family',
				'default'   => astra_get_option( 'font-family-h6' ),
				'title'     => __( 'Font Family', 'astra' ),
				'section'   => $section,
				'priority'  => 28,
				'connect'   => ASTRA_THEME_SETTINGS . '[font-weight-h6]',
				'transport' => 'postMessage',
				'divider'   => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading <H6> Font Weight
			 */
			array(
				'name'              => 'font-weight-h6',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h6-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-font',
				'font_type'         => 'ast-font-weight',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_font_weight' ),
				'default'           => astra_get_option( 'font-weight-h6' ),
				'title'             => __( 'Font Weight', 'astra' ),
				'section'           => $section,
				'priority'          => 28,
				'connect'           => 'font-family-h6',
				'transport'         => 'postMessage',
				'divider'           => array( 'ast_class' => 'ast-sub-bottom-dotted-divider' ),
			),

			/**
			 * Option: Heading 6 (H6) Font Size
			 */
			array(
				'name'              => 'font-size-h6',
				'parent'            => ASTRA_THEME_SETTINGS . '[ast-heading-h6-typo]',
				'type'              => 'sub-control',
				'control'           => 'ast-responsive-slider',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
				'section'           => $section,
				'default'           => astra_get_option( 'font-size-h6' ),
				'transport'         => 'postMessage',
				'priority'          => 28,
				'title'             => __( 'Font Size', 'astra' ),
				'suffix'            => array( 'px', 'em', 'vw', 'rem' ),
				'input_attrs'       => array(
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
			* Option: Heading H6 Font Extras
			*/
			array(
				'name'     => 'font-extras-h6',
				'type'     => 'sub-control',
				'parent'   => ASTRA_THEME_SETTINGS . '[ast-heading-h6-typo]',
				'control'  => 'ast-font-extras',
				'section'  => $section,
				'priority' => 28,
				'default'  => astra_get_option( 'font-extras-h6' ),
			),
		);
		return array_merge( $configurations, $_configs );
	}
}

new Astra_Headings_Typo_Configs();
