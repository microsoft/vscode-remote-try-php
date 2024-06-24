<?php
/**
 * Astra Extended Configuration.
 *
 * @package Astra
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Extended_Base_Configuration.
 */
final class Astra_Extended_Base_Configuration {

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
	 * Prepare Advance header configuration.
	 *
	 * @param string $section_id section id.
	 * @return array
	 */
	public static function prepare_advanced_tab( $section_id ) {

		return array(

			/**
			 * Option: Divider
			 */
			array(
				'name'     => ASTRA_THEME_SETTINGS . '[' . $section_id . '-divider]',
				'section'  => $section_id,
				'title'    => __( 'Spacing', 'astra' ),
				'type'     => 'control',
				'control'  => 'ast-heading',
				'priority' => 210,
				'settings' => array(),
				'context'  => Astra_Builder_Helper::$design_tab,
				'divider'  => array( 'ast_class' => 'ast-top-section-spacing' ),
			),

			/**
			 * Option: Padded Layout Custom Width
			 */
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[' . $section_id . '-padding]',
				'default'           => astra_get_option( $section_id . '-padding' ),
				'type'              => 'control',
				'transport'         => 'postMessage',
				'control'           => 'ast-responsive-spacing',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
				'section'           => $section_id,
				'priority'          => 210,
				'title'             => __( 'Padding', 'astra' ),
				'linked_choices'    => true,
				'unit_choices'      => array( 'px', 'em', '%' ),
				'choices'           => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
				'context'           => Astra_Builder_Helper::$design_tab,
				'divider'           => array( 'ast_class' => 'ast-section-spacing ast-bottom-section-divider' ),
			),

			/**
			 * Option: Padded Layout Custom Width
			 */
			array(
				'name'              => ASTRA_THEME_SETTINGS . '[' . $section_id . '-margin]',
				'default'           => astra_get_option( $section_id . '-margin' ),
				'type'              => 'control',
				'transport'         => 'postMessage',
				'control'           => 'ast-responsive-spacing',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_responsive_spacing' ),
				'section'           => $section_id,
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
				'context'           => Astra_Builder_Helper::$design_tab,
			),
		);
	}

	/**
	 * Prepare Spacing & Border options.
	 *
	 * @param string $section_id section id.
	 * @param bool   $skip_border_divider Skip border control divider or not.
	 *
	 * @since 4.6.0
	 * @return array
	 */
	public static function prepare_section_spacing_border_options( $section_id, $skip_border_divider = false ) {
		$_configs        = array(
			array(
				'name'      => ASTRA_THEME_SETTINGS . '[' . $section_id . '-border-group]',
				'default'   => astra_get_option( $section_id . '-border-group' ),
				'type'      => 'control',
				'control'   => 'ast-settings-group',
				'title'     => __( 'Border', 'astra' ),
				'section'   => $section_id,
				'transport' => 'postMessage',
				'priority'  => 150,
				'divider'   => true === $skip_border_divider ? array( 'ast_class' => 'ast-top-section-spacing' ) : array( 'ast_class' => 'ast-top-dotted-divider' ),
				'context'   => Astra_Builder_Helper::$design_tab,
			),
			array(
				'name'           => $section_id . '-border-width',
				'default'        => astra_get_option( $section_id . '-border-width' ),
				'parent'         => ASTRA_THEME_SETTINGS . '[' . $section_id . '-border-group]',
				'type'           => 'sub-control',
				'transport'      => 'postMessage',
				'control'        => 'ast-border',
				'title'          => __( 'Border Width', 'astra' ),
				'divider'        => array( 'ast_class' => 'ast-bottom-dotted-divider' ),
				'section'        => $section_id,
				'linked_choices' => true,
				'priority'       => 1,
				'choices'        => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
			),
			array(
				'name'              => $section_id . '-border-color',
				'default'           => astra_get_option( $section_id . '-border-color' ),
				'type'              => 'sub-control',
				'priority'          => 1,
				'parent'            => ASTRA_THEME_SETTINGS . '[' . $section_id . '-border-group]',
				'section'           => $section_id,
				'control'           => 'ast-color',
				'sanitize_callback' => array( 'Astra_Customizer_Sanitizes', 'sanitize_alpha_color' ),
				'transport'         => 'postMessage',
				'title'             => __( 'Color', 'astra' ),
				'divider'           => array( 'ast_class' => 'ast-top-spacing ast-bottom-spacing' ),
			),
			array(
				'name'           => $section_id . '-border-radius',
				'default'        => astra_get_option( $section_id . '-border-radius' ),
				'parent'         => ASTRA_THEME_SETTINGS . '[' . $section_id . '-border-group]',
				'type'           => 'sub-control',
				'transport'      => 'postMessage',
				'control'        => 'ast-border',
				'title'          => __( 'Border Radius', 'astra' ),
				'divider'        => array( 'ast_class' => 'ast-top-dotted-divider' ),
				'section'        => $section_id,
				'linked_choices' => true,
				'priority'       => 1,
				'choices'        => array(
					'top'    => __( 'Top', 'astra' ),
					'right'  => __( 'Right', 'astra' ),
					'bottom' => __( 'Bottom', 'astra' ),
					'left'   => __( 'Left', 'astra' ),
				),
			),
		);
		$spacing_configs = self::prepare_advanced_tab( $section_id );
		return array_merge( $_configs, $spacing_configs );
	}
}

/**
 *  Prepare if class 'Astra_Extended_Base_Configuration' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Astra_Extended_Base_Configuration::get_instance();
