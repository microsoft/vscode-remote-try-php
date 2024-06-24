<?php
/**
 * Pinterest for WooCommerce Marketing Channel
 *
 * @package     Automattic\WooCommerce\Pinterest\MultichannelMarketing
 * @version     1.3.0
 */

namespace Automattic\WooCommerce\Pinterest\MultichannelMarketing;

use Automattic\WooCommerce\Admin\Marketing\MarketingCampaign;
use Automattic\WooCommerce\Admin\Marketing\MarketingCampaignType;
use Automattic\WooCommerce\Admin\Marketing\MarketingChannelInterface;
use Automattic\WooCommerce\Pinterest\FeedRegistration;
use Automattic\WooCommerce\Pinterest\Feeds;
use Automattic\WooCommerce\Pinterest\FeedStatusService;
use Automattic\WooCommerce\Pinterest\ProductFeedStatus;
use Automattic\WooCommerce\Pinterest\ProductSync;

defined( 'ABSPATH' ) || exit;

/**
 * Class PinterestChannel
 */
class PinterestChannel implements MarketingChannelInterface {

	/**
	 * The Singleton's instance.
	 *
	 * @var PinterestChannel|null Instance object.
	 */
	private static $instance = null;

	/**
	 * Singleton initialization and instance fetching method.
	 *
	 * @return PinterestChannel Singleton instance.
	 */
	public static function get_instance(): PinterestChannel {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Returns the unique identifier string for the marketing channel extension, also known as the plugin slug.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return 'pinterest-for-woocommerce';
	}

	/**
	 * Returns the name of the marketing channel.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return __( 'Pinterest for WooCommerce', 'pinterest-for-woocommerce' );
	}

	/**
	 * Returns the description of the marketing channel.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return __( 'Grow your business on Pinterest! Use this official plugin to allow shoppers to Pin products while browsing your store, track conversions, and advertise on Pinterest.', 'pinterest-for-woocommerce' );
	}

	/**
	 * Returns the path to the channel icon.
	 *
	 * @return string
	 */
	public function get_icon_url(): string {
		return 'https://woo.com/wp-content/plugins/wccom-plugins/marketing-tab-rest-api/icons/pinterest.svg';
	}

	/**
	 * Returns the setup status of the marketing channel.
	 *
	 * @return bool
	 */
	public function is_setup_completed(): bool {
		return Pinterest_For_Woocommerce()::is_setup_complete();
	}

	/**
	 * Returns the URL to the settings page, or the link to complete the setup/onboarding if the channel has not been set up yet.
	 *
	 * @return string
	 */
	public function get_setup_url(): string {
		if ( ! $this->is_setup_completed() ) {
			return wc_admin_url( '&path=/pinterest/landing' );
		}

		return wc_admin_url( '&path=/pinterest/settings' );
	}

	/**
	 * Returns the status of the marketing channel's product listings.
	 *
	 * @return string
	 */
	public function get_product_listings_status(): string {
		if ( ! $this->is_setup_completed() || ! ProductSync::is_product_sync_enabled() ) {
			return self::PRODUCT_LISTINGS_NOT_APPLICABLE;
		}

		$local_feed_status        = $this->get_local_feed_status();
		$feed_registration_status = $this->get_feed_registration_status();

		if ( self::PRODUCT_LISTINGS_SYNCED === $local_feed_status ) {
			if ( self::PRODUCT_LISTINGS_SYNCED === $feed_registration_status ) {
				$status = $this->get_feed_sync_status();
			} else {
				$status = $feed_registration_status;
			}
		} else {
			$status = $local_feed_status;
		}

		return $status;
	}

	/**
	 * Returns the number of channel issues/errors (e.g. account-related errors, product synchronization issues, etc.).
	 *
	 * @return int The number of issues to resolve, or 0 if there are no issues with the channel.
	 */
	public function get_errors_count(): int {
		$count = 0;

		try {
			$feed_id     = FeedRegistration::get_locally_stored_registered_feed_id();
			$merchant_id = Pinterest_For_Woocommerce()::get_data( 'merchant_id' );
			if ( $feed_id && $merchant_id ) {
				$workflow = Feeds::get_feed_latest_workflow( (string) $merchant_id, (string) $feed_id );
				if ( $workflow ) {
					$count = FeedStatusService::get_workflow_overview_stats( $workflow )['errors'];
				}
			}
		} catch ( \Exception $e ) {
			return 0;
		}

		return $count;
	}

	/**
	 * Returns an array of marketing campaign types that the channel supports.
	 *
	 * @return MarketingCampaignType[] Array of marketing campaign type objects.
	 */
	public function get_supported_campaign_types(): array {
		// TODO: Implement get_supported_campaign_types() method.
		return array();
	}

	/**
	 * Returns an array of the channel's marketing campaigns.
	 *
	 * @return MarketingCampaign[]
	 */
	public function get_campaigns(): array {
		// TODO: Implement get_campaigns() method.
		return array();
	}

	/**
	 * Returns the status of local feed generation.
	 *
	 * @return string
	 */
	private function get_local_feed_status(): string {
		$local_feed_status = ProductFeedStatus::get()['status'];
		if ( in_array(
			$local_feed_status,
			array(
				'in_progress',
				'scheduled_for_generation',
				'pending_config',
			),
			true
		) ) {
			$status = self::PRODUCT_LISTINGS_SYNC_IN_PROGRESS;
		} elseif ( 'generated' === $local_feed_status ) {
			$status = self::PRODUCT_LISTINGS_SYNCED;
		} else {
			$status = self::PRODUCT_LISTINGS_SYNC_FAILED;
		}

		return $status;
	}

	/**
	 * Returns the status of feed registration.
	 *
	 * @return string
	 */
	private function get_feed_registration_status(): string {
		try {
			$feed_registration_status = FeedStatusService::get_feed_registration_status();
		} catch ( \Exception $e ) {
			return self::PRODUCT_LISTINGS_SYNC_FAILED;
		}

		if ( in_array(
			$feed_registration_status,
			array(
				'not_registered',
				'pending',
				'appeal_pending',
			),
			true
		) ) {
			$status = self::PRODUCT_LISTINGS_SYNC_IN_PROGRESS;
		} elseif ( 'approved' === $feed_registration_status ) {
			$status = self::PRODUCT_LISTINGS_SYNCED;
		} else {
			$status = self::PRODUCT_LISTINGS_SYNC_FAILED;
		}

		return $status;
	}

	/**
	 * Returns the status of feed sync.
	 *
	 * @return string
	 */
	private function get_feed_sync_status(): string {
		try {
			$feed_sync_status = FeedStatusService::get_feed_sync_status();
		} catch ( \Exception $e ) {
			return self::PRODUCT_LISTINGS_SYNC_FAILED;
		}

		if ( in_array(
			$feed_sync_status,
			array(
				'processing',
				'under_review',
				'queued_for_processing',
			),
			true
		) ) {
			$status = self::PRODUCT_LISTINGS_SYNC_IN_PROGRESS;
		} elseif ( in_array( $feed_sync_status, array( 'completed', 'completed_early' ), true ) ) {
			$status = self::PRODUCT_LISTINGS_SYNCED;
		} else {
			$status = self::PRODUCT_LISTINGS_SYNC_FAILED;
		}

		return $status;
	}
}
