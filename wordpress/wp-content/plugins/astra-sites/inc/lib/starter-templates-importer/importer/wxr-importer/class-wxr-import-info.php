<?php
/**
 * WordPress Importer
 * https://github.com/humanmade/WordPress-Importer
 *
 * Released under the GNU General Public License v2.0
 * https://github.com/humanmade/WordPress-Importer/blob/master/LICENSE
 *
 * @since 2.0.0
 *
 * @package WordPress Importer
 */

if ( ! class_exists( 'WXR_Import_Info' ) ) {

	/**
	 * Import Info
	 *
	 * @since 2.0.0
	 */
	class WXR_Import_Info {

		/**
		 * Home
		 *
		 * @var Home
		 */
		public $home;

		/**
		 * Siteurl
		 *
		 * @var Site URL
		 */
		public $siteurl;

		/**
		 * Title
		 *
		 * @var Title
		 */
		public $title;

		/**
		 * Users
		 *
		 * @var Users
		 */
		public $users = array();

		/**
		 * Post_count
		 *
		 * @var Post Count
		 */
		public $post_count = 0;

		/**
		 * Media Count
		 *
		 * @var Media Count
		 */
		public $media_count = 0;

		/**
		 * Comment Count
		 *
		 * @var Comment Count
		 */
		public $comment_count = 0;

		/**
		 * Term Count
		 *
		 * @var Term Count
		 */
		public $term_count = 0;

		/**
		 * Generator
		 *
		 * @var Generator
		 */
		public $generator = '';

		/**
		 * Version
		 *
		 * @var Version
		 */
		public $version;
	}

}
