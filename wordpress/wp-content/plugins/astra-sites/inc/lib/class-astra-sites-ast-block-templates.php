<?php
/**
 * Init
 *
 * @since 1.0.0
 * @package Ast Block Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites_Ast_Block_Templates' ) ) :

	/**
	 * Admin
	 */
	class Astra_Sites_Ast_Block_Templates {

		/**
		 * Instance
		 *
		 * @since 1.0.0
		 * @var (Object) Astra_Sites_Ast_Block_Templates
		 */
		private static $instance = null;

		/**
		 * Get Instance
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 */
		private function __construct() {
			$this->version_check();
			add_action( 'init', array( $this, 'load' ), 999 );
		}

		/**
		 * Version Check
		 *
		 * @return void
		 */
		public function version_check() {

			$file = realpath( dirname( __FILE__ ) . '/gutenberg-templates/version.json' );

			// Is file exist?
			if ( is_file( $file ) ) {
				// @codingStandardsIgnoreStart
				$file_data = json_decode( file_get_contents( $file ), true );
				// @codingStandardsIgnoreEnd
				global $ast_block_templates_version, $ast_block_templates_init;
				$path    = realpath( dirname( __FILE__ ) . '/gutenberg-templates/ast-block-templates.php' );
				$version = isset( $file_data['ast-block-templates'] ) ? $file_data['ast-block-templates'] : 0;

				if ( null === $ast_block_templates_version ) {
					$ast_block_templates_version = '1.0.0';
				}

				// Compare versions.
				if ( version_compare( $version, $ast_block_templates_version, '>' ) ) {
					$ast_block_templates_version = $version;
					$ast_block_templates_init    = $path;
				}
			}
		}

		/**
		 * Load latest plugin
		 *
		 * @return void
		 */
		public function load() {
			global $ast_block_templates_version, $ast_block_templates_init;
			if ( is_file( realpath( $ast_block_templates_init ) ) ) {
				include_once realpath( $ast_block_templates_init );
			}
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Ast_Block_Templates::get_instance();

endif;
