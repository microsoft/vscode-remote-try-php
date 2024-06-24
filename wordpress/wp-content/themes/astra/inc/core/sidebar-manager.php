<?php
/**
 * Sidebar Manager functions
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

/**
 * Site Sidebar
 */
if ( ! function_exists( 'astra_page_layout' ) ) {

	/**
	 * Site Sidebar
	 *
	 * Default 'right sidebar' for overall site.
	 */
	function astra_page_layout() {

		$supported_post_types = Astra_Posts_Structure_Loader::get_supported_post_types();

		if ( is_singular() ) {

			// If post meta value is empty,
			// Then get the POST_TYPE sidebar.
			$layout = astra_get_option_meta( 'site-sidebar-layout', '', true );

			// If post meta value is empty or in editor and sidebar set as default.
			if ( empty( $layout ) ) {

				$post_type = strval( get_post_type() );

				if ( in_array( $post_type, $supported_post_types ) ) {

					$layout = astra_get_option( 'single-' . $post_type . '-sidebar-layout' );
				}

				if ( 'default' == $layout || empty( $layout ) ) {

					// Get the global sidebar value.
					// NOTE: Here not used `true` in the below function call.
					$layout = astra_get_option( 'site-sidebar-layout' );
				}
			}
		} else {

			if ( is_search() ) {

				// Check only post type archive option value.
				$layout = astra_get_option( 'archive-post-sidebar-layout' );

				$search_sidebar_layout = astra_get_option( 'ast-search-sidebar-layout', 'default' );
				$layout                = 'default' !== $search_sidebar_layout ? $search_sidebar_layout : $layout;

				if ( 'default' == $layout || empty( $layout ) ) {

					// Get the global sidebar value.
					// NOTE: Here not used `true` in the below function call.
					$layout = astra_get_option( 'site-sidebar-layout' );
				}
			} else {

				$post_type = strval( get_post_type() );
				$layout    = '';

				if ( in_array( $post_type, $supported_post_types ) ) {
					$layout = astra_get_option( 'archive-' . $post_type . '-sidebar-layout' );
				}

				if ( 'default' == $layout || empty( $layout ) ) {

					// Get the global sidebar value.
					// NOTE: Here not used `true` in the below function call.
					$layout = astra_get_option( 'site-sidebar-layout' );
				}
			}
		}

		return apply_filters( 'astra_page_layout', $layout );
	}
}
