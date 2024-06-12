<?php
/**
 * Astra Sites Importer Log
 *
 * @since 1.1.0
 * @package Astra Sites
 */

namespace AiBuilder\Inc\Classes;

use AiBuilder\Inc\Traits\Instance;

/**
 * Astra Sites Importer
 */
class Ai_Builder_Importer_Log {

	use Instance;

	/**
	 * Log File
	 *
	 * @since 1.1.0
	 * @var (Object) Class object
	 */
	private static $log_file = null;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 */
	private function __construct() {

		// Check file read/write permissions.
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'has_file_read_write' ) );
		}

	}

	/**
	 * Check file read/write permissions and process.
	 *
	 * @since 1.1.0
	 * @return null
	 */
	public function has_file_read_write() {

		$upload_dir = self::log_dir();

		$file_created = self::get_filesystem()->put_contents( $upload_dir['path'] . 'index.html', '' );
		if ( ! $file_created ) {
			add_action( 'admin_notices', array( $this, 'file_permission_notice' ) );
			return;
		}

		// Set log file.
		self::set_log_file();

		// Initial AJAX Import Hooks.
		add_action( 'astra_sites_import_start', array( $this, 'start' ), 10, 2 );
	}

	/**
	 * File Permission Notice
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function file_permission_notice() {
		$upload_dir  = self::log_dir();
		$plugin_name = ASTRA_SITES_NAME;

		/* translators: %1$s refers to the plugin name */
		$notice = sprintf( __( 'Required File Permissions to import the templates from %s are missing.', 'ai-builder', 'astra-sites' ), $plugin_name );
		?>
		<div class="notice notice-error ai-builder-must-notices ai-builder-file-permission-issue">
			<p><?php echo esc_html( $notice ); ?></p>
			<?php if ( defined( 'FS_METHOD' ) ) { ?>
				<p><?php esc_html_e( 'This is usually due to inconsistent file permissions.', 'ai-builder', 'astra-sites' ); ?></p>
				<p><code><?php echo esc_html( $upload_dir['path'] ); ?></code></p>
			<?php } else { ?>
				<p><?php esc_html_e( 'You can easily update permissions by adding the following code into the wp-config.php file.', 'ai-builder', 'astra-sites' ); ?></p>
				<p><code>define( 'FS_METHOD', 'direct' );</code></p>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Add log file URL in UI response.
	 *
	 * @since 1.1.0
	 */
	public static function add_log_file_url() {

		$upload_dir   = self::log_dir();
		$upload_path  = trailingslashit( $upload_dir['url'] );
		$file_abs_url = get_option( 'ai_builder_recent_import_log_file', self::$log_file );
		$file_url     = $upload_path . basename( $file_abs_url );

		return array(
			'abs_url' => $file_abs_url,
			'url'     => $file_url,
		);
	}

	/**
	 * Current Time for log.
	 *
	 * @since 1.1.0
	 * @return string Current time with time zone.
	 */
	public static function current_time() {
		return gmdate( 'H:i:s' ) . ' ' . date_default_timezone_get();
	}

	/**
	 * Import Start
	 *
	 * @since 1.1.0
	 * @param  array  $data         Import Data.
	 * @param  string $demo_api_uri Import site API URL.
	 * @return void
	 */
	public function start( $data = array(), $demo_api_uri = '' ) {

		self::add( 'Started Import Process' );

		self::add( '# System Details: ' );
		self::add( "Debug Mode \t\t: " . self::get_debug_mode() );
		self::add( "Operating System \t: " . self::get_os() );
		self::add( "Software \t\t: " . self::get_software() );
		self::add( "MySQL version \t\t: " . self::get_mysql_version() );
		self::add( "XML Reader \t\t: " . self::get_xmlreader_status() );
		self::add( "PHP Version \t\t: " . self::get_php_version() );
		self::add( "PHP Max Input Vars \t: " . self::get_php_max_input_vars() );
		self::add( "PHP Max Post Size \t: " . self::get_php_max_post_size() );
		self::add( "PHP Extension GD \t: " . self::get_php_extension_gd() );
		self::add( "PHP Max Execution Time \t: " . self::get_max_execution_time() );
		self::add( "Max Upload Size \t: " . size_format( wp_max_upload_size() ) );
		self::add( "Memory Limit \t\t: " . self::get_memory_limit() );
		self::add( "Timezone \t\t: " . self::get_timezone() );
		self::add( PHP_EOL . '-----' . PHP_EOL );
		self::add( 'Importing Started! - ' . self::current_time() );

		self::add( '---' . PHP_EOL );
		self::add( 'WHY IMPORT PROCESS CAN FAIL? READ THIS - ' );
		self::add( 'https://wpastra.com/docs/?p=1314&utm_source=demo-import-panel&utm_campaign=import-error&utm_medium=wp-dashboard' . PHP_EOL );
		self::add( '---' . PHP_EOL );

	}

	/**
	 * Get Log File
	 *
	 * @since 1.1.0
	 * @return string log file URL.
	 */
	public static function get_log_file() {
		return self::$log_file;
	}

	/**
	 * Log file directory
	 *
	 * @since 1.1.0
	 * @param  string $dir_name Directory Name.
	 * @return array    Uploads directory array.
	 */
	public static function log_dir( $dir_name = 'ai-builder' ) {

		$upload_dir = wp_upload_dir();

		// Build the paths.
		$dir_info = array(
			'path' => $upload_dir['basedir'] . '/' . $dir_name . '/',
			'url'  => $upload_dir['baseurl'] . '/' . $dir_name . '/',
		);

		// Create the upload dir if it doesn't exist.
		if ( ! file_exists( $dir_info['path'] ) ) {

			// Create the directory.
			wp_mkdir_p( $dir_info['path'] );

			// Add an index file for security.
			self::get_filesystem()->put_contents( $dir_info['path'] . 'index.html', '' );

			// Add an .htaccess for security.
			self::get_filesystem()->put_contents( $dir_info['path'] . '.htaccess', 'deny from all' );
		}

		return $dir_info;
	}

	/**
	 * Get an instance of WP_Filesystem_Direct.
	 *
	 * @since 2.0.0
	 * @return object A WP_Filesystem_Direct instance.
	 */
	public static function get_filesystem() {
		global $wp_filesystem;

		require_once ABSPATH . '/wp-admin/includes/file.php';

		WP_Filesystem();

		return $wp_filesystem;
	}

	/**
	 * Set log file
	 *
	 * @since 1.1.0
	 */
	public static function set_log_file() {

		$upload_dir = self::log_dir();

		$upload_path = trailingslashit( $upload_dir['path'] );

		// File format e.g. 'import-31-Oct-2017-06-39-12-hashcode.log'.
		self::$log_file = $upload_path . 'import-' . gmdate( 'd-M-Y-h-i-s' ) . '-' . wp_hash( 'starter-templates-log' ) . '.log';

		if ( ! get_option( 'ai_builder_recent_import_log_file', false ) ) {
			update_option( 'ai_builder_recent_import_log_file', self::$log_file, 'no' );
		}
	}

	/**
	 * Write content to a file.
	 *
	 * @since 1.1.0
	 * @param string $content content to be saved to the file.
	 */
	public static function add( $content ) {

		if ( get_option( 'ai_builder_recent_import_log_file', false ) ) {
			$log_file = get_option( 'ai_builder_recent_import_log_file', self::$log_file );
		} else {
			$log_file = self::$log_file;
		}

		if ( apply_filters( 'astra_sites_debug_logs', false ) ) {
			error_log( $content ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- This is for the debug logs while importing. This is conditional and will not be logged in the debug.log file for normal users.
		}

		$existing_data = '';
		if ( file_exists( $log_file ) ) {
			$existing_data = self::get_filesystem()->get_contents( $log_file );
		}

		// Style separator.
		$separator = PHP_EOL;

		self::get_filesystem()->put_contents( $log_file, $existing_data . $separator . $content, FS_CHMOD_FILE );
	}

	/**
	 * Debug Mode
	 *
	 * @since 1.1.0
	 * @return string Enabled for Debug mode ON and Disabled for Debug mode Off.
	 */
	public static function get_debug_mode() {
		if ( WP_DEBUG ) {
			return __( 'Enabled', 'ai-builder', 'astra-sites' );
		}

		return __( 'Disabled', 'ai-builder', 'astra-sites' );
	}

	/**
	 * Memory Limit
	 *
	 * @since 1.1.0
	 * @return string Memory limit.
	 */
	public static function get_memory_limit() {

		$required_memory                = '64M';
		$memory_limit_in_bytes_current  = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		$memory_limit_in_bytes_required = wp_convert_hr_to_bytes( $required_memory );

		if ( $memory_limit_in_bytes_current < $memory_limit_in_bytes_required ) {
			return sprintf(
				/* translators: %1$s Memory Limit, %2$s Recommended memory limit. */
				_x( 'Current memory limit %1$s. We recommend setting memory to at least %2$s.', 'Recommended Memory Limit', 'ai-builder', 'astra-sites' ),
				WP_MEMORY_LIMIT,
				$required_memory
			);
		}

		return WP_MEMORY_LIMIT;
	}

	/**
	 * Timezone
	 *
	 * @since 1.1.0
	 * @see https://codex.wordpress.org/Option_Reference/
	 *
	 * @return string Current timezone.
	 */
	public static function get_timezone() {
		$timezone = get_option( 'timezone_string' );

		if ( ! $timezone ) {
			return get_option( 'gmt_offset' );
		}

		return $timezone;
	}

	/**
	 * Operating System
	 *
	 * @since 1.1.0
	 * @return string Current Operating System.
	 */
	public static function get_os() {
		return PHP_OS;
	}

	/**
	 * Server Software
	 *
	 * @since 1.1.0
	 * @return string Current Server Software.
	 */
	public static function get_software() {
		return isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) : '';
	}

	/**
	 * MySql Version
	 *
	 * @since 1.1.0
	 * @return string Current MySql Version.
	 */
	public static function get_mysql_version() {
		global $wpdb;
		return $wpdb->db_version();
	}

	/**
	 * XML Reader
	 *
	 * @since 1.2.8
	 * @return string Current XML Reader status.
	 */
	public static function get_xmlreader_status() {

		if ( class_exists( 'XMLReader' ) ) {
			return __( 'Yes', 'ai-builder', 'astra-sites' );
		}

		return __( 'No', 'ai-builder', 'astra-sites' );
	}

	/**
	 * PHP Version
	 *
	 * @since 1.1.0
	 * @return string Current PHP Version.
	 */
	public static function get_php_version() {
		if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
			return _x( 'We recommend to use php 5.4 or higher', 'PHP Version', 'ai-builder', 'astra-sites' );
		}
		return PHP_VERSION;
	}

	/**
	 * PHP Max Input Vars
	 *
	 * @since 1.1.0
	 * @return string Current PHP Max Input Vars
	 */
	public static function get_php_max_input_vars() {
		return ini_get( 'max_input_vars' ); // phpcs:disable PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
	}

	/**
	 * PHP Max Post Size
	 *
	 * @since 1.1.0
	 * @return string Current PHP Max Post Size
	 */
	public static function get_php_max_post_size() {
		return ini_get( 'post_max_size' );
	}

	/**
	 * PHP Max Execution Time
	 *
	 * @since 1.1.0
	 * @return string Current Max Execution Time
	 */
	public static function get_max_execution_time() {
		return ini_get( 'max_execution_time' );
	}

	/**
	 * PHP GD Extension
	 *
	 * @since 1.1.0
	 * @return string Current PHP GD Extension
	 */
	public static function get_php_extension_gd() {
		if ( extension_loaded( 'gd' ) ) {
			return __( 'Yes', 'ai-builder', 'astra-sites' );
		}

		return __( 'No', 'ai-builder', 'astra-sites' );
	}

	/**
	 * Display Data
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function display_data() {

		$crons  = _get_cron_array();
		$events = array();

		if ( empty( $crons ) ) {
			esc_html_e( 'You currently have no scheduled cron events.', 'ai-builder', 'astra-sites' );
		}

		foreach ( $crons as $time => $cron ) {
			$keys           = array_keys( $cron );
			$key            = $keys[0];
			$events[ $key ] = $time;
		}

		$expired = get_site_transient( 'ai-builder-import-check' );
		if ( $expired ) {
			global $wpdb;
			$transient = 'ai-builder-import-check';

			$transient_timeout = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_value
				FROM $wpdb->options
				WHERE option_name
				LIKE %s",
					'%_transient_timeout_' . $transient . '%'
				)
			); // WPCS: cache ok. // WPCS: db call ok.

			$older_date       = $transient_timeout[0];
			$transient_status = 'Transient: Not Expired! Recheck in ' . human_time_diff( time(), $older_date );
		} else {
			$transient_status = 'Transient: Starting.. Process for each 5 minutes.';
		}
		$temp  = get_site_option( 'ai-builder-batch-status-string', '' );
		$temp .= isset( $events['wp_astra_site_importer_cron'] ) ? '<br/>Batch: Recheck batch in ' . human_time_diff( time(), $events['wp_astra_site_importer_cron'] ) : '<br/>Batch: Not Started! Until the Transient expire.';

		$upload_dir   = self::log_dir();
		$list_files   = list_files( $upload_dir['path'] );
		$backup_files = array();
		$log_files    = array();
		foreach ( $list_files as $key => $file ) {
			if ( strpos( $file, '.json' ) ) {
				$backup_files[] = $file;
			}
			if ( strpos( $file, '.txt' ) ) {
				$log_files[] = $file;
			}
		}
		?>
		<table>
			<tr>
				<td>
					<h2>Log Files</h2>
					<ul>
						<?php
						foreach ( $log_files as $key => $file ) {
							$file_name = basename( $file );
							$file      = str_replace( $upload_dir['path'], $upload_dir['url'], $file );
							?>
							<li>
								<a target="_blank" href="<?php echo esc_url( $file ); ?>"><?php echo esc_html( $file_name ); ?></a>
							</li>
						<?php } ?>
					</ul>
				</td>
				<td>
					<h2>Backup Files</h2>
					<ul>
						<?php
						foreach ( $backup_files as $key => $file ) {
							$file_name = basename( $file );
							$file      = str_replace( $upload_dir['path'], $upload_dir['url'], $file );
							?>
							<li>
								<a target="_blank" href="<?php echo esc_url( $file ); ?>"><?php echo esc_html( $file_name ); ?></a>
							</li>
						<?php } ?>
					</ul>
				</td>
				<td>
					<div class="batch-log">
						<p><?php echo wp_kses_post( $temp ); ?></p>
						<p><?php echo wp_kses_post( $transient_status ); ?></p>
					</div>
				</td>
			</tr>
		</table>
		<?php
	}

}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Ai_Builder_Importer_Log::Instance();

