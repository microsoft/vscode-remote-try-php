<?php
/**
 * Astra Builder Base Configuration.
 *
 * @package astra-builder
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Builder_Base_Configuration.
 */
final class Astra_Builder_Base_Configuration {

	/**
	 * Member Variable
	 *
	 * @var mixed instance
	 */
	private static $instance = null;


	/**
	 *  Initiator
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() { }

	/**
	 * Prepare Advance Typography configuration.
	 *
	 * @param string $section_id section id.
	 * @param array  $required_condition Required Condition.
	 * @param array  $divider_setup Required divider setup.
	 * @return array
	 */
	public static function prepare_typography_options( $section_id, $required_condition = array(), $divider_setup = array() ) {

		$parent = ASTRA_THEME_SETTINGS . '[' . $section_id . '-typography]';

		if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'typography' ) ) {

			$_configs = array(

				array(
					'name'      => $parent,
					'default'   => astra_get_option( $section_id . '-typography' ),
					'type'      => 'control',
					'control'   => 'ast-settings-group',
					'title'     => __( 'Text Font', 'astra' ),
					'section'   => $section_id,
					'divider'   => $divider_setup,
					'transport' => 'postMessage',
					'priority'  => 16,
					'context'   => empty( $required_condition ) ? Astra_Builder_Helper::$design_tab : $required_condition,
				),

				/**
				 * Option: Font Size
				 */

				array(
					'name'              => 'font-size-' . $section_id,
					'type'              => 'sub-control',
					'parent'            => $parent,
					'section'           => $section_id,
					'control'           => 'ast-responsive-slider',
					'default'           => astra_get_option( 'font-size-' . $section_id ),
					'transport'         => 'postMessage',
					'priority'          => 15,
					'title'             => __( 'Font Size', 'astra' ),
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
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

			);
		} else {

			$_configs = array(

				/**
				 * Option: Font Size
				 */

				array(
					'name'              => ASTRA_THEME_SETTINGS . '[font-size-' . $section_id . ']',
					'section'           => $section_id,
					'default'           => astra_get_option( 'font-size-' . $section_id ),
					'type'              => 'control',
					'transport'         => 'postMessage',
					'control'           => 'ast-responsive-slider',
					'priority'          => 16,
					'title'             => __( 'Font Size', 'astra' ),
					'context'           => empty( $required_condition ) ? Astra_Builder_Helper::$design_tab : $required_condition,
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_slider' ),
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
			);
		}

		return $_configs;
	}

	/**
	 * Prepare Visibility options.
	 *
	 * @param string $_section section id.
	 * @param string $builder_type Builder Type.
	 * @return array
	 */
	public static function prepare_visibility_tab( $_section, $builder_type = 'header' ) {
		$astra_options = Astra_Theme_Options::get_astra_options();
		/**
		 * Option: Visibility
		 */
		return array(
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[' . $_section . '-visibility-responsive]',
				'default'   => astra_get_option(
					'' . $_section . '-visibility-responsive',
					array(
						'desktop' => ! isset( $astra_options[ '' . $_section . '-visibility-responsive' ] ) && isset( $astra_options[ '' . $_section . '-hide-desktop' ] ) ? ( $astra_options[ '' . $_section . '-hide-desktop' ] ? 0 : 1 ) : 1,
						'tablet'  => ! isset( $astra_options[ '' . $_section . '-visibility-responsive' ] ) && isset( $astra_options[ '' . $_section . '-hide-tablet' ] ) ? ( $astra_options[ '' . $_section . '-hide-tablet' ] ? 0 : 1 ) : 1,
						'mobile'  => ! isset( $astra_options[ '' . $_section . '-visibility-responsive' ] ) && isset( $astra_options[ '' . $_section . '-hide-mobile' ] ) ? ( $astra_options[ '' . $_section . '-hide-mobile' ] ? 0 : 1 ) : 1,
					)
				),
				'type'      => 'control',
				'control'   => 'ast-multi-selector',
				'section'   => $_section,
				'priority'  => 320,
				'title'     => __( 'Visibility', 'astra' ),
				'context'   => Astra_Builder_Helper::$general_tab,
				'transport' => 'refresh',
				'choices'   => array(
					'desktop' => 'customizer-desktop',
					'tablet'  => 'customizer-tablet',
					'mobile'  => 'customizer-mobile',
				),
				'divider'   => array( 'ast_class' => 'ast-top-section-divider' ),
			),
		);
	}

	/**
	 * Prepare common options for the widgets by type.
	 *
	 * @param string $type type.
	 * @return array
	 */
	public static function prepare_widget_options( $type = 'header' ) {
		$html_config = array();

		if ( 'footer' === $type ) {
			$component_limit = defined( 'ASTRA_EXT_VER' ) ?
				Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_footer_widgets;
		} else {
			$component_limit = defined( 'ASTRA_EXT_VER' ) ?
				Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_header_widgets;
		}
		$astra_has_widgets_block_editor = astra_has_widgets_block_editor();
		for ( $index = 1; $index <= $component_limit; $index++ ) {

			$_section = ( ! $astra_has_widgets_block_editor ) ? 'sidebar-widgets-' . $type . '-widget-' . $index : 'astra-sidebar-widgets-' . $type . '-widget-' . $index;

			$html_config[] = array(

				array(
					'name'        => $_section,
					'type'        => 'section',
					'priority'    => 5,
					'title'       => __( 'Widget ', 'astra' ) . $index,
					'panel'       => 'panel-' . $type . '-builder-group',
					'clone_index' => $index,
					'clone_type'  => $type . '-widget',
					'divider'     => array( 'ast_class' => 'ast-bottom-divider' ),
				),

				/**
				 * Option: Margin
				 */
				array(
					'name'              => ASTRA_THEME_SETTINGS . '[' . $_section . '-margin]',
					'default'           => astra_get_option( $_section . '-margin' ),
					'type'              => 'control',
					'transport'         => 'postMessage',
					'control'           => 'ast-responsive-spacing',
					'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
					'section'           => $_section,
					'priority'          => 220,
					'title'             => __( 'Margin', 'astra' ),
					'linked_choices'    => true,
					'unit_choices'      => array( 'px', 'em', '%' ),
					'choices'           => array(
						'top'    => __( 'Top', 'astra' ),
						'right'  => __( 'Right', 'astra' ),
						'bottom' => __( 'Bottom', 'astra' ),
						'left'   => __( 'Left', 'astra' ),
					),
					'divider'           => array( 'ast_class' => ' ast-section-spacing ' ),
				),
			);

			if ( 'footer' === $type ) {
				$html_config [] = array(
					array(
						'name'      => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-alignment-' . $index . ']',
						'default'   => astra_get_option( $type . '-widget-alignment-' . $index ),
						'type'      => 'control',
						'control'   => 'ast-selector',
						'section'   => $_section,
						'priority'  => 5,
						'title'     => __( 'Alignment', 'astra' ),
						'transport' => 'postMessage',
						'choices'   => array(
							'left'   => 'align-left',
							'center' => 'align-center',
							'right'  => 'align-right',
						),
						'divider'   => ( ! $astra_has_widgets_block_editor ) ? array( 'ast_class' => 'ast-top-divider' ) : array( 'ast_class' => 'ast-bottom-section-divider ast-section-spacing' ),
					),
				);
			}

				$html_config[] = array(

					/**
					 * Option: Widget title color.
					 */
					array(
						'name'       => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-title-color]',
						'default'    => astra_get_option( $type . '-widget-' . $index . '-title-color' ),
						'title'      => __( 'Heading Color', 'astra' ),
						'type'       => 'control',
						'section'    => $_section,
						'priority'   => 7,
						'transport'  => 'postMessage',
						'control'    => 'ast-responsive-color',
						'responsive' => true,
						'divider'    => ( ! $astra_has_widgets_block_editor ) ? array( 'ast_class' => 'ast-top-divider' ) : array( 'ast_class' => 'ast-section-spacing' ),
						'rgba'       => true,
					),

					/**
					 * Option: Widget Color.
					 */
					array(
						'name'       => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-color]',
						'default'    => astra_get_option( $type . '-widget-' . $index . '-color' ),
						'title'      => __( 'Content Color', 'astra' ),
						'type'       => 'control',
						'section'    => $_section,
						'priority'   => 7,
						'transport'  => 'postMessage',
						'control'    => 'ast-responsive-color',
						'responsive' => true,
						'rgba'       => true,
					),
					array(
						'name'       => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-link-color-group]',
						'default'    => astra_get_option( $type . '-widget-' . $index . '-color-group' ),
						'type'       => 'control',
						'control'    => 'ast-color-group',
						'title'      => __( 'Link Color', 'astra' ),
						'section'    => $_section,
						'transport'  => 'postMessage',
						'priority'   => 7,
						'responsive' => true,
						'divider'    => array( 'ast_class' => 'ast-bottom-divider' ),
					),

					/**
					 * Option: Widget link color.
					 */
					array(
						'name'       => $type . '-widget-' . $index . '-link-color',
						'default'    => astra_get_option( $type . '-widget-' . $index . '-link-color' ),
						'parent'     => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-link-color-group]',
						'type'       => 'sub-control',
						'section'    => $_section,
						'priority'   => 3,
						'transport'  => 'postMessage',
						'control'    => 'ast-responsive-color',
						'responsive' => true,
						'rgba'       => true,
						'title'      => __( 'Normal', 'astra' ),
					),

					/**
					 * Option: Widget link color.
					 */
					array(
						'name'       => $type . '-widget-' . $index . '-link-h-color',
						'default'    => astra_get_option( $type . '-widget-' . $index . '-link-h-color' ),
						'parent'     => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-link-color-group]',
						'type'       => 'sub-control',
						'section'    => $_section,
						'priority'   => 1,
						'transport'  => 'postMessage',
						'control'    => 'ast-responsive-color',
						'responsive' => true,
						'rgba'       => true,
						'title'      => __( 'Hover', 'astra' ),
					),
				);

				if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'typography' ) ) {
					$html_config[] = array(

						/**
						 * Option: Widget Title Typography
						 */
						array(
							'name'      => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-text-typography]',
							'default'   => astra_get_option( $type . '-widget-' . $index . '-text-typography' ),
							'type'      => 'control',
							'control'   => 'ast-settings-group',
							'title'     => __( 'Heading Font', 'astra' ),
							'section'   => $_section,
							'transport' => 'postMessage',
							'priority'  => 90,
							'divider'   => array( 'ast_class' => 'ast-bottom-divider' ),
						),


						/**
						 * Option: Widget Title Font Size
						 */
						array(
							'name'        => $type . '-widget-' . $index . '-font-size',
							'default'     => astra_get_option( $type . '-widget-' . $index . '-font-size' ),
							'parent'      => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-text-typography]',
							'transport'   => 'postMessage',
							'title'       => __( 'Font Size', 'astra' ),
							'type'        => 'sub-control',
							'section'     => $_section,
							'control'     => 'ast-responsive-slider',
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
							'priority'    => 2,
						),

						/**
						 * Option: Widget Content Typography
						 */
						array(
							'name'      => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-content-typography]',
							'default'   => astra_get_option( $type . '-widget-' . $index . '-content-typography' ),
							'type'      => 'control',
							'control'   => 'ast-settings-group',
							'title'     => __( 'Content Font', 'astra' ),
							'section'   => $_section,
							'transport' => 'postMessage',
							'priority'  => 91,
						),

						/**
						 * Option: Widget Content Font Size
						 */
						array(
							'name'        => $type . '-widget-' . $index . '-content-font-size',
							'default'     => astra_get_option( $type . '-widget-' . $index . '-content-font-size' ),
							'parent'      => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-content-typography]',
							'transport'   => 'postMessage',
							'title'       => __( 'Font Size', 'astra' ),
							'type'        => 'sub-control',
							'section'     => $_section,
							'control'     => 'ast-responsive-slider',
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
							'priority'    => 2,
						),
					);
				} else {
					$html_config[] = array(

						/**
						 * Option: Widget Title Font Size
						 */
						array(
							'name'        => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-font-size]',
							'default'     => astra_get_option( $type . '-widget-' . $index . '-font-size' ),
							'transport'   => 'postMessage',
							'title'       => __( 'Title Font Size', 'astra' ),
							'type'        => 'control',
							'section'     => $_section,
							'control'     => 'ast-responsive-slider',
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
							'priority'    => 90,
						),

						/**
						 * Option: Widget Content Font Size
						 */
						array(
							'name'        => ASTRA_THEME_SETTINGS . '[' . $type . '-widget-' . $index . '-content-font-size]',
							'default'     => astra_get_option( $type . '-widget-' . $index . '-content-font-size' ),
							'transport'   => 'postMessage',
							'title'       => __( 'Content Font Size', 'astra' ),
							'type'        => 'control',
							'section'     => $_section,
							'control'     => 'ast-responsive-slider',
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
							'priority'    => 91,
						),
					);
				}

				$html_config[] = self::prepare_visibility_tab( $_section, $type );

		}

		return call_user_func_array( 'array_merge', $html_config + array( array() ) );

	}

}

/**
 *  Prepare if class 'Astra_Builder_Base_Configuration' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Astra_Builder_Base_Configuration::get_instance();
