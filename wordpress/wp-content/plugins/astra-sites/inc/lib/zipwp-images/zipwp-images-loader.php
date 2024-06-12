<?php
/**
 * Plugin Loader.
 *
 * @package {{package}}
 * @since 1.0.0
 */

namespace ZipWP_Images;

/**
 * Zipwp_Images_Loader
 *
 * @since 1.0.0
 */
class Zipwp_Images_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 * @return void
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			(string) preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);

		$file = ZIPWP_IMAGES_DIR . $filename . '.php';

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

		add_action( 'wp_loaded', [ $this, 'load_textdomain' ] );
		add_action( 'wp_loaded', [ $this, 'load_files' ] );
	}

	/**
	 * Load Files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_files() {
		require_once ZIPWP_IMAGES_DIR . 'classes/zipwp-images-script.php';
		require_once ZIPWP_IMAGES_DIR . 'classes/zipwp-images-api.php';
	}

	/**
	 * Load Plugin Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/zipwp-images/ folder
	 *      2. Local dorectory /wp-content/plugins/zipwp-images/languages/ folder
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		// Default languages directory.
		$lang_dir = ZIPWP_IMAGES_DIR . 'languages/';

		/**
		 * Filters the languages directory path to use for plugin.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'zipwp_images_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		$locale = apply_filters( 'plugin_locale', $get_locale, 'zipwp-images' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'zipwp-images', $locale );

		// Setup paths to current locale file.
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
		$mofile_local  = $lang_dir . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/zipwp-images/ folder.
			load_textdomain( 'zipwp-images', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/zipwp-images/languages/ folder.
			load_textdomain( 'zipwp-images', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'zipwp-images', false, $lang_dir );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Zipwp_Images_Loader::get_instance();
