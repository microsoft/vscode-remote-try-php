<?php
/**
 * Plugin Loader.
 *
 * @package {{package}}
 * @since 2.0.0
 */

namespace Gutenberg_Templates;

use Gutenberg_Templates\Inc\Api\Api_Init;
use Gutenberg_Templates\Inc\Importer\Sync_Library;
use Gutenberg_Templates\Inc\Importer\Sync_Library_WP_CLI;
use Gutenberg_Templates\Inc\Importer\Plugin;
use Gutenberg_Templates\Inc\Importer\Image_Importer;
use Gutenberg_Templates\Inc\Importer\Updater;
use Gutenberg_Templates\Inc\Content\Ai_Content;
use Gutenberg_Templates\Inc\Traits\Upgrade;
use Gutenberg_Templates\Inc\Importer\Template_Kit_Importer;
use Gutenberg_Templates\Inc\Block\Spectra_AI_Block;
use Gutenberg_Templates\Inc\Classes\Ast_Block_Templates_Zipwp_Api;

/**
 * Ast_Block_Plugin_Loader
 *
 * @since 2.0.0
 */
class Ast_Block_Plugin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 2.0.0
	 */
	private static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 2.0.0
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
	 *
	 * @return void
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			(string) preg_replace(
				array( '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
				array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
				$class_to_load
			)
		);

		$file = AST_BLOCK_TEMPLATES_DIR . $filename . '.php';

		// if the file redable, include it.
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		spl_autoload_register( array( $this, 'autoload' ) );

		add_action( 'wp_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wp_loaded', array( $this, 'load_classes' ), 999 );
	}

	/**
	 * Loads plugin classes as per requirement.
	 *
	 * @return void
	 * @since  2.0.0
	 */
	public function load_classes() {
		Ast_Block_Templates_Zipwp_Api::instance();
		Api_Init::instance();
		Template_Kit_Importer::instance();
		Plugin::instance();
		Image_Importer::instance();
		Sync_Library::instance();
		Sync_Library_WP_CLI::instance();
		Ai_Content::instance();
		Upgrade::instance();
		Updater::instance();
		//phpcs:disable Squiz
		// Spectra_AI_Block::get_instance();
		//phpcs:enable Squiz
	}

	/**
	 * Load Plugin Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/gutenberg-templates/ folder
	 *      2. Local dorectory /wp-content/plugins/gutenberg-templates/languages/ folder
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function load_textdomain() {
		// Default languages directory.
		$lang_dir = AST_BLOCK_TEMPLATES_DIR . 'languages/';

		/**
		 * Filters the languages directory path to use for plugin.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'wpb_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Language Locale for plugin
		 *
		 * @var string $get_locale The locale to use.
		 * Uses get_user_locale()` in WordPress 4.7 or greater,
		 * otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'ast-block-templates' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'ast-block-templates', $locale );

		// Setup paths to current locale file.
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
		$mofile_local  = $lang_dir . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/gutenberg-templates/ folder.
			load_textdomain( 'ast-block-templates', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/gutenberg-templates/languages/ folder.
			load_textdomain( 'ast-block-templates', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'ast-block-templates', false, $lang_dir );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Ast_Block_Plugin_Loader::get_instance();
