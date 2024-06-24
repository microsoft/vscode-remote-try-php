<?php
/**
 * Posts Structures Options for our theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2022, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 4.0.0
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail if Customizer config base class does not exist.
if ( ! class_exists( 'Astra_Customizer_Config_Base' ) ) {
	return;
}

/**
 * Register Posts Structures Customizer Configurations.
 *
 * @since 4.0.0
 */
class Astra_Posts_Structures_Configs extends Astra_Customizer_Config_Base {

	/**
	 * Excluding some post types with avoiding narrow-width container layout.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	public static function get_narrow_width_exculde_cpts() {
		$exclude_cpts = array();
		if ( ( ! Astra_Dynamic_CSS::astra_fullwidth_sidebar_support() ) ) {
			return apply_filters( 'astra_exculde_narrow_width_support_posttypes', array( 'product', 'download', 'course', 'lesson', 'tutor_quiz', 'tutor_assignments', 'sfwd-assignment', 'sfwd-essays', 'sfwd-transactions', 'sfwd-certificates', 'sfwd-quiz' ) );
		}

		// Excluded some more cpts for narrow.
		return apply_filters( 'astra_exculde_narrow_width_support_posttypes', array( 'product', 'download', 'course', 'lesson', 'tutor_quiz', 'tutor_assignments', 'sfwd-assignment', 'sfwd-essays', 'sfwd-transactions', 'sfwd-certificates', 'sfwd-quiz', 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'groups' ) );
	}

	/**
	 * Function to get formatted dynamic cpt section title.
	 *
	 * @since 4.0.2
	 * @param object|null $post_type_obj WP_Post_Type object.
	 * @param string      $label fallback label.
	 * @return string formatted label.
	 */
	public static function astra_get_dynamic_section_title( $post_type_obj, $label ) {
		return is_object( $post_type_obj ) && isset( $post_type_obj->labels->name ) ? $post_type_obj->labels->name : ucfirst( $label );
	}

	/**
	 * Register Posts Structures Customizer Configurations.
	 *
	 * @param Array                $configurations Astra Customizer Configurations.
	 * @param WP_Customize_Manager $wp_customize instance of WP_Customize_Manager.
	 * @since 4.0.0
	 * @return Array Astra Customizer Configurations with updated configurations.
	 */
	public function register_configuration( $configurations, $wp_customize ) {

		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();

		if ( ! empty( $post_types ) ) {

			$_configs = array(
				array(
					'name'     => 'section-posts-structure',
					'type'     => 'section',
					'priority' => 69,
					'title'    => __( 'Custom Post Types', 'astra' ),
				),
			);

			$ignore_single_for_posttypes  = array( 'post', 'product' );
			$ignore_archive_for_posttypes = array( 'post', 'product' );

			/**
			 * Individual post types main section.
			 */
			foreach ( $post_types as $index => $label ) {
				$post_type_object = get_post_type_object( $label );
				$parent_section   = 'section-posts-structure';

				if ( 'download' === $label ) {
					$parent_section = 'section-edd-group';
				}

				if ( 'llms_membership' === $label ) {
					$parent_section = 'section-lifterlms';
				}

				if ( 'groups' === $label || 'sfwd-topic' === $label || 'sfwd-lessons' === $label || 'sfwd-courses' === $label ) {
					$parent_section = 'section-learndash';
				}

				$section_title = self::astra_get_dynamic_section_title( $post_type_object, $label );

				$_configs[] = array(
					'name'     => 'section-posttype-' . $label,
					'type'     => 'section',
					'section'  => $parent_section,
					'title'    => $section_title,
					'priority' => 69,
				);
				
				if ( ! in_array( $label, $ignore_archive_for_posttypes ) ) {
					$_configs[] = array(
						'name'     => 'archive-posttype-' . $label,
						'type'     => 'section',
						'title'    => __( 'Archive', 'astra' ) . ' ' . $section_title,
						'section'  => 'section-posttype-' . $label,
						'priority' => 5,
					);
				}
				if ( ! in_array( $label, $ignore_single_for_posttypes ) ) {
					$_configs[] = array(
						'name'     => 'single-posttype-' . $label,
						'type'     => 'section',
						'title'    => __( 'Single', 'astra' ) . ' ' . $section_title,
						'section'  => 'section-posttype-' . $label,
						'priority' => 10,
					);
				}
			}

			$configurations = array_merge( $configurations, $_configs );
		}

		return $configurations;
	}
}

/**
 * Kicking this off by creating new object.
 */
new Astra_Posts_Structures_Configs();
