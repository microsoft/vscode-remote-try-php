<?php
/**
 * Plugin Loader.
 *
 * @package zip-ai
 * @since 1.0.0
 */

namespace ZipAI;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Classes to be used, in alphabetical order.
use ZipAI\Classes\Admin_Configurations;
use ZipAI\Classes\Module;
use ZipAI\Classes\Sidebar_Configurations;

if ( ! class_exists( '\ZipAI\Loader' ) ) {
	/**
	 * Plugin_Loader
	 *
	 * @since 1.0.0
	 */
	class Loader {

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

			$file = ZIP_AI_DIR . $filename . '.php';

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
			add_action( 'plugins_loaded', [ $this, 'setup_classes' ], 20 );
			$this->define_constants();
		}

		/**
		 * Load Plugin Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/zip-ai/ folder
		 *      2. Local directory /wp-content/plugins/zip-ai/languages/ folder
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			// Default languages directory.
			$lang_dir = ZIP_AI_DIR . 'languages/';

			/**
			 * Filters the languages directory path to use for plugin.˜
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'zip_ai_languages_directory', $lang_dir );

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
			$locale = apply_filters( 'plugin_locale', $get_locale, 'zip-ai' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'zip-ai', $locale );

			// Setup paths to current locale file.
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
			$mofile_local  = $lang_dir . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/zip-ai/ folder.
				load_textdomain( 'zip-ai', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/zip-ai/languages/ folder.
				load_textdomain( 'zip-ai', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'zip-ai', false, $lang_dir );
			}
		}

		/**
		 * Define the required constants.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function define_constants() {
			define( 'ZIP_AI_FILE', __FILE__ );
			define( 'ZIP_AI_DIR', plugin_dir_path( ZIP_AI_FILE ) );
			define( 'ZIP_AI_URL', plugins_url( '/', ZIP_AI_FILE ) );
			define( 'ZIP_AI_VERSION', '1.1.6' );
			define( 'ZIP_AI_MENU_SLUG', 'zip-ai' );
			define( 'ZIP_AI_MIDDLEWARE', 'https://app.zipwp.com/auth/' );
			define( 'ZIP_AI_ZIPWP_API', 'https://api.zipwp.com/api/' );
			define( 'ZIP_AI_CREDIT_SERVER_API', 'https://credits.startertemplates.com/api/' );
			define( 'ZIP_AI_CREDIT_TOPUP_URL', 'https://app.zipwp.com/credits-pricing' );
			define( 'ZIP_AI_CREDIT_THRESHOLD_MEDIUM', 65 );
			define( 'ZIP_AI_CREDIT_THRESHOLD_HIGH', 85 );
		}

		/**
		 * Setup the required classes.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function setup_classes() {
			// Migrate any older modules to the new format.
			Module::migrate_options();

			// Enable the Zip AI Chat Sidebar if required - filter is for old users.
			if ( apply_filters( 'zip_ai_enable_chat_sidebar', true ) && Module::is_enabled( 'ai_assistant' ) ) {
				Sidebar_Configurations::get_instance();
			}

			// Enable the Zip AI Admin Configurations if required.
			if ( is_admin() ) {
				Admin_Configurations::get_instance();
			}
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Loader::get_instance();
}
