<?php
/**
 * Search for Astra theme.
 *
 * @package     astra-builder
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_HEADER_SEARCH_DIR', ASTRA_THEME_DIR . 'inc/builder/type/header/search' );
define( 'ASTRA_HEADER_SEARCH_URI', ASTRA_THEME_URI . 'inc/builder/type/header/search' );

/**
 * Heading Initial Setup
 *
 * @since 3.0.0
 */
class Astra_Header_Search_Component {

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_HEADER_SEARCH_DIR . '/class-astra-header-search-component-loader.php';

		// Include front end files.
		if ( ! is_admin() || Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
			require_once ASTRA_HEADER_SEARCH_DIR . '/dynamic-css/dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

		add_filter( 'rest_post_query', array( $this, 'astra_update_rest_post_query' ), 10, 2 );
	}

	/**
	 * Update REST Post Query for live search.
	 *
	 * @since 4.4.0
	 * @param array $args Query args.
	 * @param array $request Request args.
	 * @return array
	 */
	public function astra_update_rest_post_query( $args, $request ) {
		if (
			isset( $request['post_type'] )
			&&
			( strpos( $request['post_type'], 'ast_queried' ) !== false )
		) {
			$search_post_types = explode( ':', sanitize_text_field( $request['post_type'] ) );

			$args = array(
				'posts_per_page' => ! empty( $args['posts_per_page'] ) ? $args['posts_per_page'] : 10,
				'post_type'      => $search_post_types,
				'paged'          => 1,
				's'              => ! empty( $args['s'] ) ? $args['s'] : '',
			);
		}

		return $args;
	}
}

/**
 *  Kicking this off by creating an object.
 */
new Astra_Header_Search_Component();
