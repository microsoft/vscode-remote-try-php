<?php
/**
 * Deprecated methods compatibility layer.
 *
 * @package Pinterest/Compat
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Features\Onboarding;
use Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists;

/**
 * Helper class with functions that handle WordPress and WooCommerce deprecations.
 * Using helper class with static methods to help with autoloading.
 */
class Compat {
	/**
	 * Helper function to check if UI should show tasks.
	 *
	 * @since 1.0.1
	 * @return bool
	 */
	public static function should_show_tasks(): bool {
		if ( version_compare( WC_VERSION, '5.9', '<' ) ) {
			return Onboarding::should_show_tasks();
		}

		$setup_list    = TaskLists::get_list( 'setup' );
		$extended_list = TaskLists::get_list( 'extended' );

		return ( $setup_list && ! $setup_list->is_hidden() ) || ( $extended_list && ! $extended_list->is_hidden() );
	}
}
