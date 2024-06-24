<?php //phpcs:disable WordPress.WP.AlternativeFunctions --- Uses FS read/write in order to reliable append to an existing file.
/**
 * Class responsible for managing local feed configurations.
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.10
 */
namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Handling feed files generation.
 *
 * Singleton pattern used.
 * Prevent application from having multiple instances of configuration.
 * At the same time allow distributed use of configurations.
 */
class LocalFeedConfigs {

	/**
	 * Array of local feed configurations.
	 *
	 * @var array $feeds_configurations
	 */
	private $feeds_configurations = array();

	/**
	 * The Singleton's instance.
	 *
	 * @since 1.0.10
	 * @var null|LocalFeedConfigs Instance object.
	 */
	private static $instance = null;

	/**
	 * Singleton initialization and instance fetching method.
	 *
	 * @since 1.0.10
	 * @return LocalFeedConfigs Singleton instance.
	 */
	public static function get_instance(): LocalFeedConfigs {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Class responsible for local feed configurations and handling.
	 *
	 * @since 1.0.10
	 */
	protected function __construct() {
		$locations = array( Pinterest_For_Woocommerce()::get_base_country() ?? 'US' ); // Replace with multiple countries array for multiple feed config.
		$this->initialize_local_feeds_config( $locations );
	}

	/**
	 * Prepare feed configurations.
	 *
	 * @since 1.0.10
	 * @param array $locations Array of location to generate the feed files for.
	 */
	private function initialize_local_feeds_config( $locations ) {

		$feed_ids = (array) Pinterest_For_Woocommerce()::get_data( 'local_feed_ids' ) ?: array();

		foreach ( $locations as $location ) {
			if ( array_key_exists( $location, $feed_ids ) ) {
				continue;
			}
			$feed_ids[ $location ] = wp_generate_password( 6, false, false );
		}

		// Store generated ids for each location.
		Pinterest_For_Woocommerce()::save_data( 'local_feed_ids', $feed_ids );

		$file_name_base = trailingslashit( wp_get_upload_dir()['basedir'] ) . PINTEREST_FOR_WOOCOMMERCE_LOG_PREFIX . '-';
		$url_base       = trailingslashit( wp_get_upload_dir()['baseurl'] ) . PINTEREST_FOR_WOOCOMMERCE_LOG_PREFIX . '-';
		array_walk(
			$feed_ids,
			function ( &$id ) use ( $file_name_base, $url_base ) {
				$id = array(
					'feed_id'   => $id,
					'feed_file' => "{$file_name_base}{$id}.xml",
					'tmp_file'  => "{$file_name_base}{$id}-tmp.xml",
					'feed_url'  => "{$url_base}{$id}.xml",
				);
			}
		);
		$this->feeds_configurations = $feed_ids;
	}

	/**
	 * Cleanup local feed configs.
	 */
	public static function deregister() {
		Pinterest_For_Woocommerce()::save_data( 'local_feed_ids', false );
		self::$instance = null;
	}

	/**
	 * Fetch local feed configurations;
	 */
	public function get_configurations() {
		return $this->feeds_configurations;
	}
}
