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
 * Class CompleteOnboardingAfterSevenDays.
 *
 * Class responsible for admin Inbox notification after seven days from setup.
 *
 * @since 1.1.0
 */
class CompleteOnboardingReminderAfterSevenDays extends AbstractCompleteOnboarding {

	const NOTE_NAME = 'pinterest-complete-onboarding-note-after-7-days';

	/**
	 * Get note title.
	 *
	 * @since 1.1.0
	 * @return string Note title.
	 */
	protected function get_note_title(): string {
		return __( 'Reminder: Connect Pinterest for WooCommerce', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get note content.
	 *
	 * @since 1.1.0
	 * @return string Note content.
	 */
	protected function get_note_content(): string {
		return __( 'Finish setting up Pinterest for WooCommerce to reach over 400 million shoppers and inspire their next purchase.', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get the number of days that a notification should be delayed.
	 *
	 * This method should be overridden by child classes if needed.
	 *
	 * @return int
	 */
	protected static function get_days_delay(): int {
		return 7;
	}
}
