<?php
/**
 * Register customizer panels & sections.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Customizer_Register_Sections_Panels' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Customizer_Register_Sections_Panels extends Astra_Customizer_Config_Base {

		/**
		 * Register Panels and Sections for Customizer.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.4.3
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$configs = array(

				/**
				 * Layout Panel
				 */

				array(
					'name'     => 'panel-global',
					'type'     => 'panel',
					'priority' => 10,
					'title'    => __( 'Global', 'astra' ),
				),

				array(
					'name'               => 'section-container-layout',
					'type'               => 'section',
					'priority'           => 17,
					'title'              => __( 'Container', 'astra' ),
					'panel'              => 'panel-global',
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Site Layout Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/site-layout-overview/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
								array(
									'text'  => __( 'Container Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/container-overview/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				/*
				 * Header section
				 *
				 * @since 1.4.0
				 */
				array(
					'name'     => 'panel-header-group',
					'type'     => 'panel',
					'priority' => 20,
					'title'    => __( 'Header', 'astra' ),
				),

				/*
				 * Update the Site Identity section inside Layout -> Header
				 *
				 * @since 1.4.0
				 */
				array(
					'name'               => 'title_tagline',
					'type'               => 'section',
					'priority'           => 5,
					'title'              => __( 'Site Identity', 'astra' ),
					'panel'              => 'panel-header-group',
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Site Identity Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/site-identity-free/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				/*
				 * Update the Primary Header section
				 *
				 * @since 1.4.0
				 */
				array(
					'name'               => 'section-header',
					'type'               => 'section',
					'priority'           => 15,
					'title'              => __( 'Primary Header', 'astra' ),
					'panel'              => 'panel-header-group',
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Primary Header Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/header-overview/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				array(
					'name'     => 'section-primary-menu',
					'type'     => 'section',
					'priority' => 15,
					'title'    => __( 'Primary Menu', 'astra' ),
					'panel'    => 'panel-header-group',
				),
				array(
					'name'     => 'section-footer-group',
					'type'     => 'section',
					'title'    => __( 'Footer', 'astra' ),
					'priority' => 55,
				),

				array(
					'name'             => 'section-separator',
					'type'             => 'section',
					'ast_type'         => 'ast-section-separator',
					'priority'         => 70,
					'section_callback' => 'Astra_WP_Customize_Separator',
				),

				/**
				 * Footer Widgets Section
				 */

				array(
					'name'     => 'section-footer-adv',
					'type'     => 'section',
					'title'    => __( 'Footer Widgets', 'astra' ),
					'section'  => 'section-footer-group',
					'priority' => 5,
				),

				array(
					'name'               => 'section-footer-small',
					'type'               => 'section',
					'title'              => __( 'Footer Bar', 'astra' ),
					'section'            => 'section-footer-group',
					'priority'           => 10,
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Footer Bar Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/footer-bar/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				array(
					'name'     => 'section-blog-group',
					'type'     => 'section',
					'priority' => 40,
					'title'    => __( 'Blog', 'astra' ),
				),
				array(
					'name'     => 'section-blog',
					'type'     => 'section',
					'priority' => 5,
					'title'    => __( 'Blog / Archive', 'astra' ),
					'section'  => 'section-blog-group',
				),
				array(
					'name'     => 'section-blog-single',
					'type'     => 'section',
					'priority' => 10,
					'title'    => __( 'Single Post', 'astra' ),
					'section'  => 'section-blog-group',
				),

				array(
					'name'     => 'section-page-dynamic-group',
					'type'     => 'section',
					'priority' => 40,
					'title'    => __( 'Page', 'astra' ),
				),
				array(
					'name'     => 'section-single-page',
					'type'     => 'section',
					'priority' => 10,
					'title'    => __( 'Single Page', 'astra' ),
					'section'  => 'section-page-dynamic-group',
				),

				array(
					'name'               => 'section-sidebars',
					'type'               => 'section',
					'priority'           => 50,
					'title'              => __( 'Sidebar', 'astra' ),
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Sidebar Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/sidebar-free/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				/**
				* Accessibility Panel
				*
				* @since 4.1.0
				*/
				array(
					'name'     => 'section-accessibility',
					'type'     => 'section',
					'priority' => 65,
					'title'    => __( 'Accessibility', 'astra' ),
					'panel'    => 'panel-global',
				),

				/**
				 * Colors Panel
				 */
				array(
					'name'               => 'section-colors-background',
					'type'               => 'section',
					'priority'           => 16,
					'title'              => __( 'Colors', 'astra' ),
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Colors & Background Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/colors-background/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
					'panel'              => 'panel-global',
				),

				array(
					'name'     => 'section-colors-body',
					'type'     => 'section',
					'title'    => __( 'Base Colors', 'astra' ),
					'panel'    => 'panel-global',
					'priority' => 1,
					'section'  => 'section-colors-background',
				),

				array(
					'name'     => 'section-footer-adv-color-bg',
					'type'     => 'section',
					'title'    => __( 'Footer Widgets', 'astra' ),
					'panel'    => 'panel-colors-background',
					'priority' => 55,
				),

				/**
				 * Typography Panel
				 */
				array(
					'name'               => 'section-typography',
					'type'               => 'section',
					'title'              => __( 'Typography', 'astra' ),
					'priority'           => 15,
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Typography Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/typography-free/?utm_source=wp&utm_medium=dashboard', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
					'panel'              => 'panel-global',
				),

				array(
					'name'     => 'section-body-typo',
					'type'     => 'section',
					'title'    => __( 'Base Typography', 'astra' ),
					'section'  => 'section-typography',
					'priority' => 1,
					'panel'    => 'panel-global',
				),

				array(
					'name'     => 'section-content-typo',
					'type'     => 'section',
					'title'    => __( 'Headings', 'astra' ),
					'section'  => 'section-typography',
					'priority' => 35,
					'panel'    => 'panel-global',
				),

				/**
				 * Buttons Section
				 */
				array(
					'name'     => 'section-buttons',
					'type'     => 'section',
					'priority' => 50,
					'title'    => __( 'Buttons', 'astra' ),
					'panel'    => 'panel-global',
				),

				/**
				 * Header Buttons
				 */
				array(
					'name'     => 'section-header-button',
					'type'     => 'section',
					'priority' => 10,
					'title'    => __( 'Header Button', 'astra' ),
					'section'  => 'section-buttons',
				),

				/**
				 * Header Button - Default
				 */
				array(
					'name'     => 'section-header-button-default',
					'type'     => 'section',
					'priority' => 10,
					'title'    => __( 'Primary Header Button', 'astra' ),
					'section'  => 'section-header-button',
				),

				/**
				 * Header Button - Transparent
				 */
				array(
					'name'     => 'section-header-button-transparent',
					'type'     => 'section',
					'priority' => 10,
					'title'    => __( 'Transparent Header Button', 'astra' ),
					'section'  => 'section-header-button',
				),

				/**
				 * Block Editor specific configs.
				 */
				array(
					'name'     => 'section-block-editor',
					'type'     => 'section',
					'priority' => 80,
					'title'    => __( 'Block Editor', 'astra' ),
					'panel'    => 'panel-global',
				),

				/**
				 * Global Misc specific configs.
				 */
				array(
					'name'     => 'section-global-misc',
					'type'     => 'section',
					'priority' => 80,
					'title'    => __( 'Misc', 'astra' ),
					'panel'    => 'panel-global',
				),

				/**
				 * Option: Scroll To Top
				 */
				array(
					'name'     => 'section-scroll-to-top',
					'title'    => __( 'Scroll To Top', 'astra' ),
					'type'     => 'section',
					'panel'    => 'panel-global',
					'priority' => 60,
				),
			);

			// Add spacial page section under page group.
			foreach ( Astra_Posts_Structure_Loader::get_special_page_types() as $index => $special_type ) {
				$configs[] = array(
					'name'     => 'ast-section-' . $special_type . '-page',
					'type'     => 'section',
					'priority' => 10 + absint( $index ),
					'title'    => sprintf(
						/* translators: %s: Name of special page type */
						esc_html__( '%s Page', 'astra' ),
						ucfirst( $special_type )
					),
					'section'  => 'section-page-dynamic-group',
				);
			}

			return array_merge( $configurations, $configs );
		}
	}
}


/**
 * Kicking this off by calling 'get_instance()' method
 */
new Astra_Customizer_Register_Sections_Panels();
