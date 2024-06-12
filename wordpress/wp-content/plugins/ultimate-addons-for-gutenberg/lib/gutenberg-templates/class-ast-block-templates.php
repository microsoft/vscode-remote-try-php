<?php
/**
 * Init
 *
 * @since 2.0.0
 * @package Ast Block Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ast_Block_Templates' ) ) :

	/**
	 * Admin
	 */
	class Ast_Block_Templates {

		/**
		 * Instance
		 *
		 * @since 2.0.0
		 * @var (Object) Ast_Block_Templates
		 */
		private static $instance = null;

		/**
		 * Get Instance
		 *
		 * @since 2.0.0
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.0.0
		 */
		private function __construct() {
			require_once AST_BLOCK_TEMPLATES_DIR . 'ast-block-plugin-loader.php';
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Ast_Block_Templates::get_instance();

endif;
