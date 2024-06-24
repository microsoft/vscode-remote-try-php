<?php //phpcs:disable WordPress.WP.AlternativeFunctions --- Uses FS read/write in order to reliable append to an existing file.
/**
 * Pinterest for WooCommerce Catalog Syncing
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use \Automattic\WooCommerce\ActionSchedulerJobFramework\Proxies\ActionScheduler as ActionSchedulerProxy;
use Automattic\WooCommerce\Pinterest\FeedRegistration;
use Automattic\WooCommerce\Pinterest\API\FeedIssues;
use Automattic\WooCommerce\Pinterest\Utilities\ProductFeedLogger;

/**
 * Class Handling registration & generation of the XML product feed.
 */
class ProductSync {

	use ProductFeedLogger;

	/**
	 * Feed File Generator Instance
	 *
	 * @var $feed_generator FeedGenerator
	 */
	private static $feed_generator = null;


	/**
	 * Feed File Generator Instance
	 *
	 * @var $feed_registration FeedRegistration
	 */
	private static $feed_registration = null;


	/**
	 * Local Feed Configurations class.
	 *
	 * @var $configurations LocalFeedConfigs
	 */
	private static $configurations = null;

	/**
	 * Initiate class.
	 */
	public static function maybe_init() {

		add_action( 'update_option_' . PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME, array( __class__, 'maybe_deregister' ), 10, 2 );
		if ( ! self::is_product_sync_enabled() ) {
			return;
		}

		self::initialize_feed_components();
		/**
		 * Mark feed as needing re-generation whenever a product is edited or changed.
		 */
		add_action( 'edit_post', array( __CLASS__, 'mark_feed_dirty' ), 10, 1 );

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			add_action( 'woocommerce_variation_set_stock_status', array( __CLASS__, 'mark_feed_dirty' ), 10, 1 );
			add_action( 'woocommerce_product_set_stock_status', array( __CLASS__, 'mark_feed_dirty' ), 10, 1 );
		}

		/**
		 * Mark feed as needing re-generation on changes to the woocommerce_hide_out_of_stock_items setting
		 */
		add_action(
			'update_option_woocommerce_hide_out_of_stock_items',
			function () {
				self::$feed_generator->mark_feed_dirty();
			}
		);

		/**
		 * Mark feed as needing re-generation on changes to the woocommerce_tax_display_shop or woocommerce_tax_display_cart settings
		 */
		add_action(
			'update_option_woocommerce_tax_display_shop',
			function () {
				self::$feed_generator->mark_feed_dirty();
			}
		);
		add_action(
			'update_option_woocommerce_tax_display_cart',
			function () {
				self::$feed_generator->mark_feed_dirty();
			}
		);
	}

	/**
	 * Observe Pinterest option change and decide if we need to deregister.
	 *
	 * @since 1.0.10
	 *
	 * @param array $old_value Option old value.
	 * @param array $value     Option new value.
	 */
	public static function maybe_deregister( $old_value, $value ) {
		if ( ! is_array( $value ) ) {
			return;
		}

		$product_sync_enabled = $value['product_sync_enabled'] ?? false;

		if ( ! $product_sync_enabled ) {
			self::deregister();
		}
	}

	/**
	 * Initialize components of the synchronization process.
	 *
	 * @since 1.0.10
	 */
	private static function initialize_feed_components() {
		self::$configurations    = LocalFeedConfigs::get_instance();
		$action_scheduler        = new ActionSchedulerProxy();
		$feed_file_operations    = new FeedFileOperations( self::$configurations );
		self::$feed_generator    = new FeedGenerator( $action_scheduler, $feed_file_operations, self::$configurations );
		self::$feed_registration = new FeedRegistration( self::$configurations, $feed_file_operations );

		self::$feed_registration->init();
		self::$feed_generator->init();
	}

	/**
	 * Checks if the feature is enabled, and all requirements are met.
	 *
	 * @return boolean
	 */
	public static function is_product_sync_enabled() {

		$domain_verified  = Pinterest_For_Woocommerce()::is_domain_verified();
		$tracking_enabled = $domain_verified && Pinterest_For_Woocommerce()::is_tracking_configured();

		return (bool) $domain_verified && $tracking_enabled && Pinterest_For_Woocommerce()::get_setting( 'product_sync_enabled' );
	}

	/**
	 * Get the proper extra_info for the feed status.
	 *
	 * @return string
	 */
	public static function get_feed_status_extra_info() {

		if ( self::is_product_sync_enabled() ) {
			return '';
		}

		if ( ! Pinterest_For_Woocommerce()::is_domain_verified() ) {
			return sprintf(
				/* translators: 1: The URL of the connection page */
				__( 'The domain is not verified, visit the <a href="%1$s">connection</a> page to verify it.', 'pinterest-for-woocommerce' ),
				esc_url(
					add_query_arg(
						array(
							'page' => 'wc-admin',
							'path' => '/pinterest/connection',
						),
						admin_url( 'admin.php' )
					)
				)
			);
		}

		if ( ! Pinterest_For_Woocommerce()::is_tracking_configured() ) {
			return sprintf(
				/* translators: 1: The URL of the connection page */
				__( 'The tracking tag is not configured, visit the <a href="%1$s">connection</a> page to configure it.', 'pinterest-for-woocommerce' ),
				esc_url(
					add_query_arg(
						array(
							'page' => 'wc-admin',
							'path' => '/pinterest/connection',
						),
						admin_url( 'admin.php' )
					)
				)
			);
		}

		return sprintf(
			/* translators: 1: The URL of the settings page */
			__( 'Visit the <a href="%1$s">settings</a> page to enable it.', 'pinterest-for-woocommerce' ),
			esc_url(
				add_query_arg(
					array(
						'page' => 'wc-admin',
						'path' => '/pinterest/settings',
					),
					admin_url( 'admin.php' )
				)
			)
		);
	}

	/**
	 * Handles de-registration of the feed.
	 *
	 * @return void
	 */
	private static function deregister() {
		FeedGenerator::deregister();
		LocalFeedConfigs::deregister();
		FeedRegistration::deregister();
		ProductFeedStatus::deregister();
		FeedIssues::deregister();

		self::log( 'Product feed reset and files deleted.' );
	}

	/**
	 * Stop jobs on deactivation.
	 */
	public static function cancel_jobs() {
		if ( ! function_exists( 'as_unschedule_all_actions' ) ) {
			return;
		}

		FeedGenerator::cancel_jobs();
		FeedRegistration::cancel_jobs();
	}

	/**
	 * Check if Given ID is of a product and if yes, mark feed as dirty.
	 *
	 * @param integer $product_id The product ID.
	 *
	 * @return void
	 */
	public static function mark_feed_dirty( $product_id ) {
		if ( ! wc_get_product( $product_id ) ) {
			return;
		}

		self::$feed_generator->mark_feed_dirty();
	}
}
