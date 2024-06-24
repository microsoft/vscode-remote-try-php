<?php
/**
 * Pinterest for WooCommerce CompleteOnboardingAfterThreeDays class.
 *
 * @package Pinterest_For_WooCommerce/Classes/
 * @version 1.1.0
 */

namespace Automattic\WooCommerce\Pinterest\Notes\Collection;

defined( 'ABSPATH' ) || exit;

/**
 * Class CompleteOnboardingAfterThreeDays.
 *
 * Class responsible for admin Inbox notification after three days from setup.
 *
 * @since 1.1.0
 */
class CompleteOnboardingAfterThreeDays extends AbstractCompleteOnboarding {

	const NOTE_NAME = 'pinterest-complete-onboarding-note-after-3-days';

	/**
	 * Get note title.
	 *
	 * @since 1.1.0
	 * @return string Note title.
	 */
	protected function get_note_title(): string {
		return __( 'Reach more shoppers by connecting with Pinterest', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get note content.
	 *
	 * @since 1.1.0
	 * @return string Note content.
	 */
	protected function get_note_content(): string {
		return __( 'Complete setting up Pinterest for WooCommerce to get your catalog in front of a large, engaged audience who are ready to buy! Create or connect your Pinterest business account to sync your product catalog and turn your products into shoppable Pins.', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get the number of days that a notification should be delayed.
	 *
	 * This method should be overridden by child classes if needed.
	 *
	 * @return int
	 */
	protected static function get_days_delay(): int {
		return 3;
	}
}
