<?php
/**
 * Post Strctures Extension
 *
 * @package Astra
 * @since 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_THEME_POST_STRUCTURE_DIR', ASTRA_THEME_DIR . 'inc/modules/posts-structures/' );
define( 'ASTRA_THEME_POST_STRUCTURE_URI', ASTRA_THEME_URI . 'inc/modules/posts-structures/' );

/**
 * Post Strctures Initial Setup
 *
 * @since 4.0.0
 */
class Astra_Post_Structures {

	/**
	 * Constructor function that loads require files.
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_THEME_POST_STRUCTURE_DIR . 'class-astra-posts-structure-loader.php';
		require_once ASTRA_THEME_POST_STRUCTURE_DIR . 'class-astra-posts-structure-markup.php';

		// Include front end files.
		if ( ! is_admin() ) {
			require_once ASTRA_THEME_POST_STRUCTURE_DIR . 'css/single-dynamic.css.php';
			require_once ASTRA_THEME_POST_STRUCTURE_DIR . 'css/archive-dynamic.css.php';
			require_once ASTRA_THEME_POST_STRUCTURE_DIR . 'css/special-dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}
}

/**
 *  Kicking this off by creating new object.
 */
new Astra_Post_Structures();
