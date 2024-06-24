<?php
/**
 * Plugin Loader.
 *
 * @package st-import
 * @since 1.0.1
 */

namespace STImporter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STImporter\Importer\ST_Importer;
use STImporter\Resetter\ST_Resetter;
use STImporter\Importer\WXR_Importer\ST_WXR_Importer;

if ( ! class_exists( '\STImporter\ST_Importer_Loader' ) ) {
	/**
	 * Plugin_Loader
	 *
	 * @since 1.0.0
	 */
	class ST_Importer_Loader {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class Instance.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Autoload classes.
		 *
		 * @param string $class class name.
		 */
		public function autoload( $class ) {
			if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
				return;
			}

			$class_to_load = $class;

			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_to_load
				)
			);

			$file = ST_IMPORTER_DIR . $filename . '.php';

			// if the file redable, include it.
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			spl_autoload_register( [ $this, 'autoload' ] );
			add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
			$this->define_constants();
			$this->setup_classes();
		}

		/**
		 * Load Plugin Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/st-import/ folder
		 *      2. Local directory /wp-content/plugins/st-import/languages/ folder
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			// Default languages directory.
			$lang_dir = ST_IMPORTER_DIR . 'languages/';

			/**
			 * Filters the languages directory path to use for plugin.˜
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'starter_templates_importer_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Language Locale for plugin
			 *
			 * @var $get_locale The locale to use.
			 * Uses get_user_locale()` in WordPress 4.7 or greater,
			 * otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'st-import' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'st-import', $locale );

			// Setup paths to current locale file.
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
			$mofile_local  = $lang_dir . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/st-import/ folder.
				load_textdomain( 'st-import', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/st-import/languages/ folder.
				load_textdomain( 'st-import', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'st-import', false, $lang_dir );
			}
		}

		/**
		 * Define the required constants.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function define_constants() {
			define( 'ST_IMPORTER_FILE', __FILE__ );
			define( 'ST_IMPORTER_DIR', plugin_dir_path( ST_IMPORTER_FILE ) );
			define( 'ST_IMPORTER_URL', plugins_url( '/', ST_IMPORTER_FILE ) );
			define( 'ST_IMPORTER_VER', '1.0.17' );
		}

		/**
		 * Setup the required classes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function setup_classes() {

			require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';

			require_once ST_IMPORTER_DIR . 'importer/wxr-importer.php';

			// Enable the Zip AI Admin Configurations if required.
			if ( is_admin() ) {
				ST_Importer::get_instance();
				ST_Resetter::get_instance();

				ST_WXR_Importer::get_instance();
			}

			require_once ST_IMPORTER_DIR . 'importer/wxr-importer/class-wp-importer-logger.php';
			require_once ST_IMPORTER_DIR . 'importer/wxr-importer/class-wp-importer-logger-serversentevents.php';
			require_once ST_IMPORTER_DIR . 'importer/wxr-importer/class-wxr-import-info.php';

			// Core Helpers - Batch Processing.
			require_once ST_IMPORTER_DIR . 'importer/helpers/wp-async-request.php';
			require_once ST_IMPORTER_DIR . 'importer/helpers/wp-background-process.php';
			require_once ST_IMPORTER_DIR . 'importer/helpers/wp-background-process-astra.php';
			require_once ST_IMPORTER_DIR . 'importer/helpers/wp-background-process-astra-single.php';
			require_once ST_IMPORTER_DIR . 'importer/helpers/wp-background-process-astra-site-importer.php';

			require_once ST_IMPORTER_DIR . 'importer/st-importer-file-system.php';
			require_once ST_IMPORTER_DIR . 'importer/batch/st-batch-processing.php';
			require_once ST_IMPORTER_DIR . 'importer/batch/st-batch-processing-gutenberg.php';
			require_once ST_IMPORTER_DIR . 'importer/batch/st-replace-images.php';
			require_once ST_IMPORTER_DIR . 'importer/batch/st-batch-processing-misc.php';
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	ST_Importer_Loader::get_instance();
}
