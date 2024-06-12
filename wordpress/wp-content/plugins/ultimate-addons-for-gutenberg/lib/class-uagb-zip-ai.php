<?php
/**
 * Zip AI initialization
 *
 * @since 2.10.2
 * @package zip-ai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'UAGB_Zip_AI' ) ) :

	/**
	 * This class connect zip ai library with spectra
	 *
	 * @since 2.10.2
	 */
	class UAGB_Zip_AI {

		/**
		 * Instance
		 *
		 * @since 2.10.2
		 * @var (Object) UAGB_Zip_AI
		 */
		private static $instance = null;

		/**
		 * Get Instance
		 *
		 * @since 2.10.2
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
		 * @since 2.10.2
		 *
		 * @return void
		 */
		private function __construct() {
			$this->version_check();
			add_action( 'plugins_loaded', array( $this, 'load' ), 15 );
		}

		/**
		 * Checks for latest version of zip-ai library available in environment.
		 *
		 * @since 2.10.2
		 *
		 * @return void
		 */
		public function version_check() {

			$file = realpath( dirname( __FILE__ ) . '/zip-ai/version.json' );

			// Is file exist?
			if ( is_file( $file ) ) {
				// @codingStandardsIgnoreStart
				$file_data = json_decode( file_get_contents( $file ), true );
				// @codingStandardsIgnoreEnd
				global $zip_ai_version, $zip_ai_path;
				$path    = realpath( dirname( __FILE__ ) . '/zip-ai/zip-ai.php' );
				$version = isset( $file_data['zip-ai'] ) ? $file_data['zip-ai'] : 0;

				if ( null === $zip_ai_version ) {
					$zip_ai_version = '1.0.0';
				}

				// Compare versions.
				if ( version_compare( $version, $zip_ai_version, '>' ) ) {
					$zip_ai_version = $version;
					$zip_ai_path    = $path;
				}
			}
		}

		/**
		 * Load latest zip-ai library
		 *
		 * @since 2.10.2
		 *
		 * @return void
		 */
		public function load() {
			global $zip_ai_path;
			if ( ! is_null( $zip_ai_path ) && is_file( realpath( $zip_ai_path ) ) ) {
				include_once realpath( $zip_ai_path );
			}
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Zip_AI::get_instance();

endif;
