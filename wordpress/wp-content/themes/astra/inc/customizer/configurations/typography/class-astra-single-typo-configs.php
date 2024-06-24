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

if ( ! class_exists( 'Astra_Single_Typo_Configs' ) ) {

	/**
	 * Customizer Single Typography Configurations.
	 *
	 * @since 1.4.3
	 */
	class Astra_Single_Typo_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Single Typography configurations.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$_configs = array();

			// Learn More link if Astra Pro is not activated.
			if ( astra_showcase_upgrade_notices() ) {

				$_configs = array(

					/**
					 * Option: Astra Pro blog single post's options.
					 */
					array(
						'name'     => ASTRA_THEME_SETTINGS . '[ast-single-post-items]',
						'type'     => 'control',
						'control'  => 'ast-upgrade',
						'renderAs' => 'list',
						'choices'  => array(
							'one'   => array(
								'title' => __( 'Author Box with Social Share', 'astra' ),
							),
							'two'   => array(
								'title' => __( 'Auto load previous posts', 'astra' ),
							),
							'three' => array(
								'title' => __( 'Single post navigation control', 'astra' ),
							),
							'four'  => array(
								'title' => __( 'Custom featured images size', 'astra' ),
							),
							'seven' => array(
								'title' => __( 'Single post read time', 'astra' ),
							),
							'five'  => array(
								'title' => __( 'Extended typography options', 'astra' ),
							),
							'six'   => array(
								'title' => __( 'Extended spacing options', 'astra' ),
							),
							'eight' => array(
								'title' => __( 'Social sharing options', 'astra' ),
							),
						),
						'section'  => 'section-blog-single',
						'default'  => '',
						'priority' => 999,
						'context'  => array(),
						'title'    => __( 'Extensive range of tools to help blog pages stand out.', 'astra' ),
						'divider'  => array( 'ast_class' => 'ast-top-section-divider' ),
					),
				);
			}

			$configurations = array_merge( $configurations, $_configs );

			return $configurations;
		}
	}
}

new Astra_Single_Typo_Configs();
