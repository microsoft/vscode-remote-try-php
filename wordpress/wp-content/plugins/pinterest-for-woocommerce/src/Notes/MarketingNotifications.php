<?php
/**
 * Marketing notifications for merchants (admin).
 *
 * @package Pinterest_For_WooCommerce/Classes/
 * @since   1.1.0
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Notes;

use Automattic\WooCommerce\Pinterest\Notes\Collection\AbstractNote;
use Automattic\WooCommerce\Pinterest\Notes\Collection\EnableCatalogSync;
use Automattic\WooCommerce\Pinterest\Notes\Collection\CatalogSyncErrors;
use Automattic\WooCommerce\Pinterest\Notes\Collection\CompleteOnboardingAfterThreeDays as OnboardingThreeDays;
use Automattic\WooCommerce\Pinterest\Notes\Collection\CompleteOnboardingReminderAfterSevenDays as OnboardingSevenDays;
use Automattic\WooCommerce\Pinterest\Notes\Collection\CompleteOnboardingReminderAfterFourteenDays as OnboardingFourteenDays;
use Automattic\WooCommerce\Pinterest\Notes\Collection\CompleteOnboardingReminderAfterThirtyDays as OnboardingThirtyDays;
use Automattic\WooCommerce\Pinterest\Utilities\Utilities;


defined( 'ABSPATH' ) || exit;

/**
 * Class responsible for displaying inbox notifications for the merchant.
 *
 * In the specification for the notes we have that the notification should
 * be sent some time after the plugin installation. There is no retroactive
 * way of figuring out when the plugin was first installed. So we count
 *
 * @since 1.1.0
 */
class MarketingNotifications {

	// All options common prefix.
	const OPTIONS_PREFIX = PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_marketing_notifications';

	// Timestamp option marking the moment we start to count time.
	const INIT_TIMESTAMP = self::OPTIONS_PREFIX . '_init_timestamp';

	// List of marketing notifications that we want to send.
	const NOTES = array(
		EnableCatalogSync::class,
		CatalogSyncErrors::class,
		OnboardingThreeDays::class,
		OnboardingSevenDays::class,
		OnboardingFourteenDays::class,
		OnboardingThirtyDays::class,
	);

	/**
	 * Trigger inbox messages.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function init_notifications(): void {
		/*
		 * Check if we have connection timestamp set. If not and the connection
		 * has been made we set the timestamp as now bc we can't know when the
		 * actual connection has been made.
		 */
		if ( 0 === Utilities::get_account_connection_timestamp() && Pinterest_For_Woocommerce()::is_setup_complete() ) {
			Utilities::set_account_connection_timestamp();
		}

		/*
		 * Check to see whether we have initialized marketing notifications. If
		 * not we set the timestamp now.
		 */
		if ( 0 === $this->get_init_timestamp() ) {
			$this->set_init_timestamp();
		}

		/** @var AbstractNote $note */
		foreach ( self::NOTES as $note ) {
			if ( ! $note::should_be_added( $this->get_init_timestamp() ) ) {
				continue;
			}

			/** @var AbstractNote $notification */
			$notification = new $note();
			$notification->prepare_note()->save();
		}
	}

	/**
	 * Get the notification init timestamp.
	 *
	 * @since 1.1.0
	 * @return int Initialization timestamp.
	 */
	protected function get_init_timestamp(): int {
		return (int) get_option( self::INIT_TIMESTAMP, 0 );
	}

	/**
	 * Set the notification init timestamp to the current time.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_init_timestamp(): void {
		update_option( self::INIT_TIMESTAMP, time() );
	}
}
