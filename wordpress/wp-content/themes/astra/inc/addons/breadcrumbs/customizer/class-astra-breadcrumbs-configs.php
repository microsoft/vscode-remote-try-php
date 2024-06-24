<?php
/**
 * Breadcrumbs Options for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Breadcrumbs_Configs' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Breadcrumbs_Configs extends Astra_Customizer_Config_Base {

		/**
		 * Register Astra-Breadcrumbs Settings.
		 *
		 * @param Array                $configurations Astra Customizer Configurations.
		 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
		 * @since 1.7.0
		 * @return Array Astra Customizer Configurations with updated configurations.
		 */
		public function register_configuration( $configurations, $wp_customize ) {

			$breadcrumb_source_list = apply_filters(
				'astra_breadcrumb_source_list',
				array(
					'default' => __( 'Default', 'astra' ),
				),
				'breadcrumb-list'
			);

			$_section = 'section-breadcrumb';

			$positions = array(
				'none'                      => __( 'None', 'astra' ),
				'astra_masthead_content'    => __( 'Inside', 'astra' ),
				'astra_header_markup_after' => __( 'After Header', 'astra' ),
				'astra_entry_top'           => __( 'Before Title', 'astra' ),
			);

			if ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) {
				$positions = array(
					'none'                                 => __( 'None', 'astra' ),
					'astra_header_primary_container_after' => __( 'Inside', 'astra' ),
					'astra_header_after'                   => __( 'After', 'astra' ),
					'astra_entry_top'                      => __( 'Before Title', 'astra' ),
				);
			}

			$_configs = array(

				/*
				 * Breadcrumb
				 */
				array(
					'name'               => $_section,
					'type'               => 'section',
					'priority'           => 20,
					'title'              => __( 'Breadcrumb', 'astra' ),
					'description_hidden' => true,
					'description'        => $this->section_get_description(
						array(
							'description' => '<p><b>' . __( 'Helpful Information', 'astra' ) . '</b></p>',
							'links'       => array(
								array(
									'text'  => __( 'Breadcrumb Overview', 'astra' ) . ' &#187;',
									'attrs' => array(
										'href' => astra_get_pro_url( 'https://wpastra.com/docs/add-breadcrumbs-with-astra/', 'customizer', 'helpful_information', 'astra_theme' ),
									),
								),
							),
						)
					),
				),

				/**
				 * Option: Breadcrumb Position
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
					'default'    => astra_get_option( 'breadcrumb-position', 'none' ),
					'section'    => $_section,
					'title'      => __( 'Header Position', 'astra' ),
					'type'       => 'control',
					'control'    => 'ast-select',
					'priority'   => 5,
					'choices'    => $positions,
					'partial'    => array(
						'selector'            => '.ast-breadcrumbs-wrapper .ast-breadcrumbs .trail-items',
						'container_inclusive' => false,
					),
					'context'    => Astra_Builder_Helper::$general_tab,
					'responsive' => false,
					'renderAs'   => 'text',
					'divider'    => array( 'ast_class' => 'ast-section-spacing' ),
				),

				// Breadcrumb if set to None - Show the notice under the Design tab.
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-position-none-notice]',
					'type'     => 'control',
					'control'  => 'ast-description',
					'section'  => $_section,
					'priority' => 5,
					'label'    => '',
					'help'     => __( 'Note: To get design settings in action make sure to select Header Position other than None.', 'astra' ),
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '==',
							'value'    => 'none',
						),
						( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ?
							Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Divider
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-layout-divider]',
					'section'  => $_section,
					'title'    => __( 'Display Settings', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-heading',
					'priority' => 25,
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),

				/**
				 * Option: Disable Breadcrumb on Categories
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-home-page]',
					'default'  => astra_get_option( 'breadcrumb-disable-home-page', '1' ),
					'type'     => 'control',
					'section'  => $_section,
					'title'    => __( 'Enable on Home Page?', 'astra' ),
					'priority' => 25,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'divider'  => array( 'ast_class' => 'ast-section-spacing' ),
				),


				/**
				 * Option: Disable Breadcrumb on Categories
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-blog-posts-page]',
					'default'     => astra_get_option( 'breadcrumb-disable-blog-posts-page', '1' ),
					'type'        => 'control',
					'section'     => $_section,
					'description' => __( 'Latest posts page or when any page is selected as blog page', 'astra' ),
					'title'       => __( 'Enable on Blog / Posts Page?', 'astra' ),
					'priority'    => 25,
					'control'     => 'ast-toggle-control',
					'context'     => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on Search
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-search]',
					'default'  => astra_get_option( 'breadcrumb-disable-search', '1' ),
					'type'     => 'control',
					'section'  => $_section,
					'title'    => __( 'Enable on Search?', 'astra' ),
					'priority' => 30,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on Archive
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-archive]',
					'default'  => astra_get_option( 'breadcrumb-disable-archive', '1' ),
					'type'     => 'control',
					'section'  => $_section,
					'title'    => __( 'Enable on Archive?', 'astra' ),
					'priority' => 35,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on Single Page
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-single-page]',
					'default'  => astra_get_option( 'breadcrumb-disable-single-page', '1' ),
					'type'     => 'control',
					'section'  => $_section,
					'title'    => __( 'Enable on Single Page?', 'astra' ),
					'priority' => 40,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on Single Post
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-single-post]',
					'default'  => astra_get_option( 'breadcrumb-disable-single-post', '1' ),
					'type'     => 'control',
					'section'  => $_section,
					'title'    => __( 'Enable on Single Post?', 'astra' ),
					'priority' => 45,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on Singular
				 */
				array(
					'name'        => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-singular]',
					'default'     => astra_get_option( 'breadcrumb-disable-singular', '1' ),
					'type'        => 'control',
					'section'     => $_section,
					'description' => __( 'All Pages, All Posts, All Attachments', 'astra' ),
					'title'       => __( 'Enable on Singular?', 'astra' ),
					'priority'    => 50,
					'control'     => 'ast-toggle-control',
					'context'     => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Disable Breadcrumb on 404 Page
				 */
				array(
					'name'     => ASTRA_THEME_SETTINGS . '[breadcrumb-disable-404-page]',
					'default'  => astra_get_option( 'breadcrumb-disable-404-page', '1' ),
					'type'     => 'control',
					'section'  => $_section,

					'title'    => __( 'Enable on 404 Page?', 'astra' ),
					'priority' => 55,
					'control'  => 'ast-toggle-control',
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
				),

				/**
				 * Option: Breadcrumb Alignment
				 */
				array(
					'name'       => ASTRA_THEME_SETTINGS . '[breadcrumb-alignment]',
					'default'    => astra_get_option( 'breadcrumb-alignment', 'left' ),
					'section'    => $_section,
					'transport'  => 'postMessage',
					'title'      => __( 'Alignment', 'astra' ),
					'type'       => 'control',
					'control'    => 'ast-selector',
					'priority'   => 24,
					'context'    => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'choices'    => array(
						'left'   => 'align-left',
						'center' => 'align-center',
						'right'  => 'align-right',
					),
					'responsive' => false,
					'divider'    => array( 'ast_class' => 'ast-top-section-divider ast-bottom-spacing' ),
				),

				/**
				 * Option: Breadcrumb Spacing
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[breadcrumb-spacing]',
					'default'           => astra_get_option( 'breadcrumb-spacing' ),
					'type'              => 'control',
					'transport'         => 'postMessage',
					'control'           => 'ast-responsive-spacing',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
					'priority'          => 83,
					'title'             => __( 'Spacing', 'astra' ),
					'linked_choices'    => true,
					'unit_choices'      => array( 'px', 'em', '%' ),
					'choices'           => array(
						'top'    => __( 'Top', 'astra' ),
						'right'  => __( 'Right', 'astra' ),
						'bottom' => __( 'Bottom', 'astra' ),
						'left'   => __( 'Left', 'astra' ),
					),

					'section'           => $_section,
					'context'           => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						( true === Astra_Builder_Helper::$is_header_footer_builder_active ) ?
							Astra_Builder_Helper::$design_tab_config : Astra_Builder_Helper::$general_tab_config,
					),
				),
			);

			if ( $this->is_third_party_breadcrumb_active() ) {

				$_configs[] = array(
					'name'     => ASTRA_THEME_SETTINGS . '[select-breadcrumb-source]',
					'default'  => astra_get_option( 'select-breadcrumb-source', 'default' ),
					'section'  => $_section,
					'title'    => __( 'Breadcrumb Source', 'astra' ),
					'type'     => 'control',
					'control'  => 'ast-select',
					'priority' => 10,
					'choices'  => $breadcrumb_source_list,
					'context'  => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'divider'  => array( 'ast_class' => 'ast-top-divider' ),
				);
			}

			if ( $this->is_selected_breadcrumb_active() ) {

				/**
				 * Option: Breadcrumb separator
				 */

				$_configs[] = array(
					'name'              => ASTRA_THEME_SETTINGS . '[breadcrumb-separator-selector]',
					'type'              => 'control',
					'control'           => 'ast-radio-image',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_choices' ),
					'default'           => astra_get_option( 'breadcrumb-separator-selector' ),
					'priority'          => 15,
					'title'             => __( 'Separator', 'astra' ),
					'section'           => $_section,
					'choices'           => array(
						'\003E'   => array(
							'label' => __( 'Type 1', 'astra' ),
							'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'breadcrumb-separator-1' ),
						),
						'\00BB'   => array(
							'label' => __( 'Type 2', 'astra' ),
							'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'breadcrumb-separator-2' ),
						),
						'\002F'   => array(
							'label' => __( 'Type 3', 'astra' ),
							'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'breadcrumb-separator-3' ),
						),
						'unicode' => array(
							'label' => __( 'Custom separator', 'astra' ),
							'path'  => Astra_Builder_UI_Controller::fetch_svg_icon( 'breadcrumb-separator-unicode' ),
						),
					),
					'alt_layout'        => true,
					'transport'         => 'postMessage',
					'context'           => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'divider'           => array( 'ast_class' => 'ast-top-section-divider' ),
				);

				/**
				 * Option: Breadcrumb Unicode input
				 */

				$_configs[] = array(
					'name'      => ASTRA_THEME_SETTINGS . '[breadcrumb-separator]',
					'type'      => 'control',
					'control'   => 'text',
					'section'   => $_section,
					'default'   => astra_get_option( 'breadcrumb-separator' ),
					'priority'  => 15,
					'title'     => __( 'Unicode', 'astra' ),
					'context'   => array(
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-position]',
							'operator' => '!=',
							'value'    => 'none',
						),
						array(
							'setting'  => ASTRA_THEME_SETTINGS . '[breadcrumb-separator-selector]',
							'operator' => '=',
							'value'    => 'unicode',
						),
						Astra_Builder_Helper::$general_tab_config,
					),
					'transport' => 'postMessage',
				);
			}

			if ( true === Astra_Builder_Helper::$is_header_footer_builder_active ) {

				$_configs[] = array(
					'name'        => $_section . '-ast-context-tabs',
					'section'     => $_section,
					'type'        => 'control',
					'control'     => 'ast-builder-header-control',
					'priority'    => 0,
					'description' => '',
				);
			}

			return array_merge( $configurations, $_configs );
		}

		/**
		 * Is third-party breadcrumb active.
		 * Decide if the Source option should be visible depending on third party plugins.
		 *
		 * @return boolean  True - If the option should be displayed, False - If the option should be hidden.
		 */
		public function is_third_party_breadcrumb_active() {

			// Check if breadcrumb is turned on from WPSEO option.
			$breadcrumb_enable = is_callable( 'WPSEO_Options::get' ) ? WPSEO_Options::get( 'breadcrumbs-enable' ) : false;
			$wpseo_option      = get_option( 'wpseo_internallinks' ) ? get_option( 'wpseo_internallinks' ) : $breadcrumb_enable;
			if ( ! is_array( $wpseo_option ) ) {
				unset( $wpseo_option );
				$wpseo_option = array(
					'breadcrumbs-enable' => $breadcrumb_enable,
				);
			}

			if ( function_exists( 'yoast_breadcrumb' ) && true === $wpseo_option['breadcrumbs-enable'] ) {
				// Check if breadcrumb is turned on from SEO Yoast plugin.
				return true;
			} elseif ( function_exists( 'bcn_display' ) ) {
				// Check if breadcrumb is turned on from Breadcrumb NavXT plugin.
				return true;
			} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
				// Check if breadcrumb is turned on from Rank Math plugin.
				return true;
			} elseif ( function_exists( 'seopress_display_breadcrumbs' ) ) {
				// Check if breadcrumb is turned on from SEOPress plugin.
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Is selected third-party breadcrumb active.
		 * Decide if the Separator option should be visible depending on third party plugins.
		 *
		 * @return boolean  True - If the option should be displayed, False - If the option should be hidden.
		 */
		public function is_selected_breadcrumb_active() {

			// Check if breadcrumb is turned on from WPSEO option.
			$selected_breadcrumb_source = astra_get_option( 'select-breadcrumb-source' );
			$breadcrumb_enable          = is_callable( 'WPSEO_Options::get' ) ? WPSEO_Options::get( 'breadcrumbs-enable' ) : false;
			$wpseo_option               = get_option( 'wpseo_internallinks' ) ? get_option( 'wpseo_internallinks' ) : $breadcrumb_enable;
			if ( ! is_array( $wpseo_option ) ) {

				unset( $wpseo_option );
				$wpseo_option = array(
					'breadcrumbs-enable' => $breadcrumb_enable,
				);
			}

			if ( function_exists( 'yoast_breadcrumb' ) && true === $wpseo_option['breadcrumbs-enable'] && 'yoast-seo-breadcrumbs' === $selected_breadcrumb_source ) {
				// Check if breadcrumb is turned on from SEO Yoast plugin.
				return false;
			} elseif ( function_exists( 'bcn_display' ) && 'breadcrumb-navxt' === $selected_breadcrumb_source ) {
				// Check if breadcrumb is turned on from Breadcrumb NavXT plugin.
				return false;
			} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) && 'rank-math' === $selected_breadcrumb_source ) {
				// Check if breadcrumb is turned on from Rank Math plugin.
				return false;
			} elseif ( function_exists( 'seopress_display_breadcrumbs' ) ) {
				// Check if breadcrumb is turned on from SEOPress plugin.
				return false;
			} else {
				return true;
			}
		}
	}
}

new Astra_Breadcrumbs_Configs();
