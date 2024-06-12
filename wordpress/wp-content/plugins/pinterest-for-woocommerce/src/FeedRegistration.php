<?php
/**
 * Pinterest for WooCommerce Feed Registration.
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.10
 */

namespace Automattic\WooCommerce\Pinterest;

use Exception;
use Throwable;
use Automattic\WooCommerce\Pinterest\Utilities\ProductFeedLogger;
use Automattic\WooCommerce\Pinterest\Exception\PinterestApiLocaleException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Handling feed files registration.
 */
class FeedRegistration {

	use ProductFeedLogger;

	const ACTION_HANDLE_FEED_REGISTRATION = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '-handle-feed-registration';

	/**
	 * Local Feed Configurations class.
	 *
	 * @var LocalFeedConfigs of local feed configurations;
	 */
	private $configurations;

	/**
	 * Feed File Operations Instance
	 *
	 * @var $feed_file_operations FeedFileOperations
	 */
	private $feed_file_operations;

	/**
	 * Feed Registration.
	 *
	 * @since 1.0.10
	 * @param LocalFeedConfigs   $local_feeds_configurations Locations configuration class.
	 * @param FeedFileOperations $feed_file_operations Feed file operations class.
	 */
	public function __construct( $local_feeds_configurations, $feed_file_operations ) {
		$this->configurations       = $local_feeds_configurations;
		$this->feed_file_operations = $feed_file_operations;
	}

	/**
	 * Initialize FeedRegistration actions and Action Scheduler hooks.
	 *
	 * @since 1.0.10
	 */
	public function init() {
		add_action( self::ACTION_HANDLE_FEED_REGISTRATION, array( $this, 'handle_feed_registration' ) );
		if ( false === as_has_scheduled_action( self::ACTION_HANDLE_FEED_REGISTRATION, array(), PINTEREST_FOR_WOOCOMMERCE_PREFIX ) ) {
			as_schedule_recurring_action( time() + 10, 10 * MINUTE_IN_SECONDS, self::ACTION_HANDLE_FEED_REGISTRATION, array(), PINTEREST_FOR_WOOCOMMERCE_PREFIX );
		}
	}

	/**
	 * Check if the feed is registered based on the plugin's settings.
	 * If not, try to register it,
	 * Log issues.
	 * Potentially report issues.
	 *
	 * Should be run on demand when settings change,
	 * and on a scheduled basis.
	 *
	 * @return mixed
	 *
	 * @throws Exception PHP Exception.
	 */
	public function handle_feed_registration() {

		// Clean merchants error code.
		$this->clear_merchant_error_code();

		if ( ! self::feed_file_exists() ) {
			self::log( 'Feed didn\'t fully generate yet. Retrying later.', 'debug' );
			// Feed is not generated yet, lets wait a bit longer.
			return true;
		}

		try {
			if ( self::register_feed() ) {
				return true;
			}

			throw new Exception( esc_html__( 'Could not register feed.', 'pinterest-for-woocommerce' ) );

		} catch ( PinterestApiLocaleException $e ) {
			Pinterest_For_Woocommerce()::save_data( 'merchant_locale_not_valid', true );

			// translators: %s: Error message.
			$error_message = "Could not register feed. Error: {$e->getMessage()}";
			self::log( $error_message, 'error' );

		} catch ( Throwable $th ) {
			if ( method_exists( $th, 'get_pinterest_code' ) && 4163 === $th->get_pinterest_code() ) {
				Pinterest_For_Woocommerce()::save_data( 'merchant_connected_diff_platform', true );
			}

			self::log( $th->getMessage(), 'error' );
			return false;
		}

	}

	/**
	 * Clear merchant error code.
	 *
	 * @since 1.2.13
	 * @return void
	 */
	private function clear_merchant_error_code() {
		Pinterest_For_Woocommerce()::save_data( 'merchant_connected_diff_platform', false );
		Pinterest_For_Woocommerce()::save_data( 'merchant_locale_not_valid', false );
	}

	/**
	 * Handles feed registration using the given arguments.
	 * Will try to create a merchant if none exists.
	 * Also if a different feed is registered, it will update using the URL in the
	 * $feed_args.
	 *
	 * @return boolean
	 *
	 * @throws Exception PHP Exception.
	 */
	private static function register_feed() {

		$merchant_id = self::check_merchant_approval_status();

		if ( ! $merchant_id ) {
			return false;
		}

		$feed_id = Feeds::match_local_feed_configuration_to_registered_feeds( $merchant_id );

		// If no matching registered feed found try to create it.
		if ( ! $feed_id ) {
			$response = Merchants::update_or_create_merchant();
			$feed_id  = $response['feed_id'] ?? '';
		}

		Pinterest_For_Woocommerce()::save_data( 'feed_registered', $feed_id );

		if ( ! $feed_id ) {
			return false;
		}

		self::feed_enable_status_maintenance( $merchant_id, $feed_id );
		return true;
	}

	/**
	 * Maintenance function for feed enable status.
	 * Enable the registered feed if it is not enabled.
	 * Disable all other feed configurations for the merchant.
	 *
	 * @since 1.2.13
	 * @param string $merchant_id Merchant ID.
	 * @param string $feed_id Feed ID.
	 * @return void
	 */
	private static function feed_enable_status_maintenance( $merchant_id, $feed_id ) {
		// Check if the feed is enabled. If not, enable it.
		if ( ! Feeds::is_local_feed_enabled( $merchant_id, $feed_id ) ) {
			Feeds::enabled_feed( $merchant_id, $feed_id );
		}

		// Cleanup feeds that are registered but not in the local feed configurations.
		self::maybe_disable_stale_feeds_for_merchant( $merchant_id, $feed_id );
	}

	/**
	 * Check if the merchant is approved.
	 * This is a helper function for the register_feed method.
	 *
	 * @return mixed False if the merchant is not approved, merchant id otherwise.
	 */
	private static function check_merchant_approval_status() {

		$merchant = Merchants::get_merchant();

		if ( ! empty( $merchant['data']->id ) && 'declined' === $merchant['data']->product_pin_approval_status ) {

			self::log( 'Pinterest returned a Declined status for product_pin_approval_status' );
			return false;
		}

		return $merchant['data']->id;
	}

	/**
	 * Check if there are stale feeds that are registered but not in the local feed configurations.
	 * Deregister them if they are registered as WooCommerce integration.
	 *
	 * @since 1.2.13
	 *
	 * @param string $merchant_id Merchant ID.
	 * @param string $feed_id Feed ID.
	 *
	 * @return void
	 */
	public static function maybe_disable_stale_feeds_for_merchant( $merchant_id, $feed_id ) {

		$feed_profiles = Feeds::get_merchant_feeds( $merchant_id );

		if ( empty( $feed_profiles ) ) {
			return;
		}

		$configs    = LocalFeedConfigs::get_instance()->get_configurations();
		$config     = reset( $configs );
		$local_path = dirname( $config['feed_url'] );

		$invalidate_cache = false;

		foreach ( $feed_profiles as $feed ) {
			// Local feed should not be disabled.
			if ( $feed_id === $feed->id ) {
				continue;
			}

			// Only disable feeds that are registered as WooCommerce integration.
			if ( 'WOOCOMMERCE' !== $feed->integration_platform_type ) {
				continue;
			}

			/**
			 * Disable feeds only if their file URL matches, using the directory path for accurate identification. This
			 * method prevents the disabling of non-WooCommerce feeds that share the same merchant registration.
			 * Simultaneously, disable feeds registered for WooCommerce from the same host with different file names,
			 * ending with a suffix generated by the wp_generate_password function, as outlined in the LocalFeedConfigs
			 * class. Utilizing dirname eliminates the file name and suffix, leaving only the directory path for
			 * comparison.
			 */
			if ( dirname( $feed->location_config->full_feed_fetch_location ) !== $local_path ) {
				continue;
			}

			// Disable the feed if it is active.
			if ( 'ACTIVE' === $feed->feed_status ) {
				Feeds::disable_feed( $merchant_id, $feed->id );
				$invalidate_cache = true;
			}
		}

		if ( $invalidate_cache ) {
			Feeds::invalidate_get_merchant_feeds_cache( $merchant_id );
		}
	}

	/**
	 * Checks if the feed file for the configured (In $state var) feed exists.
	 * This could be true as the feed is being generated, if its not the 1st time
	 * its been generated.
	 *
	 * @return bool
	 */
	public function feed_file_exists() {
		return $this->feed_file_operations->check_if_feed_file_exists();
	}

	/**
	 * Returns the feed profile ID stored locally if it's registered.
	 * Returns `false` otherwise.
	 * If everything is configured correctly, this feed profile id will match
	 * the setup that the merchant has in Pinterest.
	 *
	 * @return string|boolean
	 */
	public static function get_locally_stored_registered_feed_id() {
		return Pinterest_For_Woocommerce()::get_data( 'feed_registered' ) ?? false;
	}

	/**
	 * Stop feed generator jobs.
	 */
	public static function cancel_jobs() {
		as_unschedule_all_actions( self::ACTION_HANDLE_FEED_REGISTRATION, array(), PINTEREST_FOR_WOOCOMMERCE_PREFIX );
	}

	/**
	 * Cleanup registration data.
	 */
	public static function deregister() {
		Pinterest_For_Woocommerce()::save_data( 'feed_registered', false );
		self::cancel_jobs();
	}
}
