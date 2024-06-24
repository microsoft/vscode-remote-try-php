<?php
/**
 * Entry Content options for Astra Theme.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2022, Astra
 * @link        https://wpastra.com/
 * @since       Astra 3.8.0
 */

if ( ! class_exists( 'Astra_Block_Editor_Configs' ) ) {

	/**
	 * Register Site Layout Customizer Configurations.
	 */
	class Astra_Block_Editor_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Site Layout Customizer Configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 3.8.0
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$is_legacy_setup = ( 'legacy' === astra_get_option( 'wp-blocks-ui', 'comfort' ) || true === astra_get_option( 'blocks-legacy-setup', false ) ) ? true : false;

			$preset_options = array(
				'compact' => __( 'Compact', 'astra' ),
				'comfort' => __( 'Comfort', 'astra' ),
				'custom'  => __( 'Custom', 'astra' ),
			);
			if ( $is_legacy_setup ) {
				$preset_options = array(
					'legacy'  => __( 'Legacy', 'astra' ),
					'compact' => __( 'Compact', 'astra' ),
					'comfort' => __( 'Comfort', 'astra' ),
					'custom'  => __( 'Custom', 'astra' ),
				);
			}

			$_configs = array(
				/**
				 * Option: Presets for block editor padding.
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[wp-blocks-ui]',
					'type'       => 'control',
					'control'    => 'ast-selector',
					'section'    => 'section-block-editor',
					'default'    => astra_get_option( 'wp-blocks-ui' ),
					'priority'   => 9,
					'title'      => __( 'Core Blocks Spacing', 'astra' ),
					'choices'    => $preset_options,
					'responsive' => false,
					'renderAs'   => 'text',
				),

				/**
				 * Option: Global Padding Option.
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[wp-blocks-global-padding]',
					'section'           => 'section-block-editor',
					'title'             => __( 'Size', 'astra' ),
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
					'default'           => astra_get_option( 'wp-blocks-global-padding' ),
					'type'              => 'control',
					'control'           => 'ast-responsive-spacing',
					'choices'           => array(
						'top'    => __( 'Top', 'astra' ),
						'right'  => __( 'Right', 'astra' ),
						'bottom' => __( 'Bottom', 'astra' ),
						'left'   => __( 'Left', 'astra' ),
					),
					'linked_choices'    => true,
					'priority'          => 10,
					'unit_choices'      => array( 'px', 'em', '%' ),
					'context'           => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[wp-blocks-ui]',
							'operator' => '===',
							'value'    => 'custom',
						),
					),
					'divider'           => array( 'ast_class' => 'ast-section-spacing' ),
				),
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[wp-blocks-ui-description]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => 'section-block-editor',
					'priority' => 10,
					'help'     => '<p style="margin-top: -5px;">' . __( 'Global padding setting for WordPress Group, Column, Cover blocks, it can be overridden by respective block\'s Dimension setting.', 'astra' ) . '</p>',
					'settings' => array(),
				),
			);

			return array_merge( $configurations, $_configs );
		}
	}
}

/**
 * Kicking this off by creating new instance.
 */
new Astra_Block_Editor_Configs();
