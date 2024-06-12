<?php
/**
 * Download Docs locally.
 *
 * @package Astra
 * @since 4.6.0
 */

/**
 * Process Docs from locally.
 */
class Astra_Docs_Loader {

	/**
	 * The remote URL.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $remote_url;

	/**
	 * Base path.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $base_path;

	/**
	 * Base URL.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $base_url;

	/**
	 * Subfolder name.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $subfolder_name;

	/**
	 * The docs folder.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $docs_folder;

	/**
	 * The local stylesheet's path.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $local_stylesheet_path;

	/**
	 * The local stylesheet's URL.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $local_docs_json_url;

	/**
	 * The remote CSS.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $remote_styles;

	/**
	 * The final docs data.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @var string
	 */
	protected $docs_data;

	/**
	 * Cleanup routine frequency.
	 */
	const CLEANUP_FREQUENCY = 'weekly';

	/**
	 * Constructor.
	 *
	 * Get a new instance of the object for a new URL.
	 *
	 * @access public
	 * @since 4.6.0
	 * @param string $url The remote URL.
	 * @param string $subfolder_name The subfolder name.
	 */
	public function __construct( $url = '', $subfolder_name = 'bsf-docs' ) {
		$this->remote_url = $url;
		$this->subfolder_name = $subfolder_name;

		// Add a cleanup routine.
		$this->schedule_cleanup();
		add_action( 'astra_delete_docs_folder', array( $this, 'astra_delete_docs_folder' ) );
	}

	/**
	 * Get the local URL which contains the styles.
	 *
	 * Fallback to the remote URL if we were unable to write the file locally.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_url() {

		// Check if the local stylesheet exists.
		if ( $this->local_file_exists() ) {

			// Attempt to update the stylesheet. Return the local URL on success.
			if ( $this->write_json() ) {
				return $this->get_local_docs_json_url();
			}
		}

		$astra_docs_url = file_exists( $this->get_local_docs_file_path() ) ? $this->get_local_docs_json_url() : $this->remote_url;

		return $astra_docs_url;
	}

	/**
	 * Get the local stylesheet URL.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_local_docs_json_url() {
		if ( ! $this->local_docs_json_url ) {
			$this->local_docs_json_url = str_replace(
				$this->get_base_path(),
				$this->get_base_url(),
				$this->get_local_docs_file_path()
			);
		}
		return $this->local_docs_json_url;
	}

	/**
	 * Get remote data locally.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_remote_data() {

		// If we already have the local file, return its contents.
		$local_docs_contents = $this->get_local_docs_contents();
		if ( $local_docs_contents ) {
			return $local_docs_contents;
		}

		// Get the remote URL contents.
		$this->remote_styles = $this->get_remote_url_contents();
		$this->docs_data = $this->remote_styles;

		$this->write_json();

		return $this->docs_data;
	}

	/**
	 * Get local stylesheet contents.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string|false Returns the remote URL contents.
	 */
	public function get_local_docs_contents() {
		$local_path = $this->get_local_docs_file_path();

		// Check if the local file exists.
		if ( $this->local_file_exists() ) {

			// Attempt to update the file. Return false on fail.
			if ( ! $this->write_json() ) {
				return false;
			}
		}

		ob_start();
		include $local_path;
		return ob_get_clean();
	}

	/**
	 * Get remote file contents.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string Returns the remote URL contents.
	 */
	public function get_remote_url_contents() {

		/**
		 * The user-agent we want to use.
		 *
		 * The default user-agent is the only one compatible with woff (not woff2)
		 * which also supports unicode ranges.
		 */
		$user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8';

		// Get the response.
		$response = wp_remote_get( $this->remote_url, array( 'user-agent' => $user_agent ) );

		// Early exit if there was an error.
		if ( is_wp_error( $response ) ) {
			return '';
		}

		// Get the CSS from our response.
		$contents = wp_remote_retrieve_body( $response );

		return $contents;
	}

	/**
	 * Write the CSS to the filesystem.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @return string|false Returns the absolute path of the file on success, or false on fail.
	 */
	protected function write_json() {
		$file_path  = $this->get_local_docs_file_path();
		$filesystem = $this->get_filesystem();

		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}

		// If the folder doesn't exist, create it.
		if ( ! file_exists( $this->get_docs_folder() ) ) {
			$this->get_filesystem()->mkdir( $this->get_docs_folder(), FS_CHMOD_DIR );
		}

		// If the file doesn't exist, create it. Return false if it can not be created.
		if ( ! $filesystem->exists( $file_path ) && ! $filesystem->touch( $file_path ) ) {
			return false;
		}

		// If we got this far, we need to write the file.
		// Get the CSS.
		if ( ! $this->docs_data ) {
			$this->get_remote_data();
		}

		// Put the contents in the file. Return false if that fails.
		if ( ! $filesystem->put_contents( $file_path, $this->docs_data ) ) {
			return false;
		}

		return $file_path;
	}

	/**
	 * Get the stylesheet path.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_local_docs_file_path() {
		if ( ! $this->local_stylesheet_path ) {
			$this->local_stylesheet_path = $this->get_docs_folder() . '/' . $this->get_local_docs_filename() . '.json';
		}
		return $this->local_stylesheet_path;
	}

	/**
	 * Get the local stylesheet filename.
	 *
	 * This is a hash, generated from the site-URL, the wp-content path and the URL.
	 * This way we can avoid issues with sites changing their URL, or the wp-content path etc.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_local_docs_filename() {
		return apply_filters( 'astra_local_docs_file_name', 'docs' );
	}

	/**
	 * Check if the local stylesheet exists.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return bool
	 */
	public function local_file_exists() {
		return ( ! file_exists( $this->get_local_docs_file_path() ) );
	}

	/**
	 * Get the base path.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_base_path() {
		if ( ! $this->base_path ) {
			$this->base_path = apply_filters( 'astra_local_docs_base_path', $this->get_filesystem()->wp_content_dir() . 'uploads' );
		}
		return $this->base_path;
	}

	/**
	 * Get the base URL.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return string
	 */
	public function get_base_url() {
		if ( ! $this->base_url ) {
			$this->base_url = apply_filters( 'astra_local_docs_base_url', content_url() . '/uploads' );
		}
		return $this->base_url;
	}

	/**
	 * Get the folder for docs.
	 *
	 * @access public
	 * @return string
	 */
	public function get_docs_folder() {
		if ( ! $this->docs_folder ) {
			$this->docs_folder = $this->get_base_path();
			$this->docs_folder .= '/' . $this->subfolder_name;
		}

		return $this->docs_folder;
	}

	/**
	 * Schedule a cleanup.
	 *
	 * Deletes the docs file on a regular basis.
	 * This way docs file will get updated regularly,
	 * and we avoid edge cases where unused files remain in the server.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return void
	 */
	public function schedule_cleanup() {
		if ( ! wp_next_scheduled( 'astra_delete_docs_folder' ) && ! wp_installing() ) {
			wp_schedule_event( time(), self::CLEANUP_FREQUENCY, 'astra_delete_docs_folder' );  // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.cron_functionality_wp_schedule_event
		}
	}

	/**
	 * Delete the documentation folder.
	 *
	 * This runs as part of a cleanup routine.
	 *
	 * @access public
	 * @since 4.6.0
	 * @return bool
	 */
	public function astra_delete_docs_folder() {
		// Delete previously created supportive options.
		return $this->get_filesystem()->delete( $this->get_docs_folder(), true );
	}

	/**
	 * Get the filesystem.
	 *
	 * @access protected
	 * @since 4.6.0
	 * @return \WP_Filesystem_Base
	 */
	protected function get_filesystem() {
		global $wp_filesystem;

		// If the filesystem has not been instantiated yet, do it here.
		if ( ! $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );  // PHPCS:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}
			WP_Filesystem();
		}
		return $wp_filesystem;
	}
}

/**
 * Create instance of Astra_Docs_Loader class.
 *
 * @param string $docs_rest_url Knowledge Base URL to set data.
 * @param string $subfolder_name Subfolder name.
 *
 * @return object
 * @since 4.6.0
 */
function astra_docs_loader_instance( $docs_rest_url = '', $subfolder_name = 'bsf-docs' ) {
	return new Astra_Docs_Loader( $docs_rest_url, $subfolder_name );
}
