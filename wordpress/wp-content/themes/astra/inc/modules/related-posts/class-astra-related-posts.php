<?php
/**
 * Related Posts for Astra theme.
 *
 * @package     Astra
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2021, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_RELATED_POSTS_DIR', ASTRA_THEME_DIR . 'inc/modules/related-posts/' );

/**
 * Related Posts Initial Setup
 *
 * @since 3.5.0
 */
class Astra_Related_Posts {

	/**
	 * Constructor function that initializes required actions and hooks
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_RELATED_POSTS_DIR . 'class-astra-related-posts-loader.php';
		require_once ASTRA_RELATED_POSTS_DIR . 'class-astra-related-posts-markup.php';
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

		// Include front end files.
		if ( ! is_admin() ) {
			require_once ASTRA_RELATED_POSTS_DIR . 'css/static-css.php'; // phpcs:ignore: WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			require_once ASTRA_RELATED_POSTS_DIR . 'css/dynamic-css.php'; // phpcs:ignore: WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
	}
}

/**
 *  Kicking this off by creating NEW instance.
 */
new Astra_Related_Posts();
