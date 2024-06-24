<?php
/**
 * Pinterest for WooCommerce CompleteOnboardingAfterThirtyDays class.
 *
 * @package Pinterest_For_WooCommerce/Classes/
 * @version 1.1.0
 */

namespace Automattic\WooCommerce\Pinterest\Notes\Collection;

defined( 'ABSPATH' ) || exit;

/**
 * Class CompleteOnboardingAfterThirtyDays.
 *
 * Class responsible for admin Inbox notification after thirty days from setup.
 *
 * @since 1.1.0
 */
class CompleteOnboardingReminderAfterThirtyDays extends CompleteOnboardingReminderAfterSevenDays {

	const NOTE_NAME = 'pinterest-complete-onboarding-note-after-30-days';

	/**
	 * Get the number of days that a notification should be delayed.
	 *
	 * This method should be overridden by child classes if needed.
	 *
	 * @return int
	 */
	protected static function get_days_delay(): int {
		return 30;
	}
}
