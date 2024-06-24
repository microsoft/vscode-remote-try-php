<?php
/**
 * The Setup for Pinterest Onboarding task.
 *
 * @package Automattic\WooCommerce\Pinterest\Admin
 * @since 1.2.11
 */

namespace Automattic\WooCommerce\Pinterest\Admin\Tasks;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

/**
 * Onboarding Task class.
 */
class Onboarding extends Task {

	/**
	 * Get the ID of the task.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'setup-pinterest';
	}

	/**
	 * Get the title for the task.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Get your products in front of engaged shoppers with Pinterest for WooCommerce', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get the content for the task.
	 *
	 * @return string
	 */
	public function get_content() {
		return '';
	}

	/**
	 * Get the time required to perform the task.
	 *
	 * @return string
	 */
	public function get_time() {
		return esc_html__( '20 minutes', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get the action URL for the task.
	 *
	 * @return string
	 */
	public function get_action_url() {
		if ( $this->is_complete() ) {
			$action_url = admin_url( 'admin.php?page=wc-admin&path=/pinterest/catalog' );
		} else {
			$action_url = admin_url( 'admin.php?page=wc-admin&path=/pinterest/landing' );
		}
		return $action_url;
	}

	/**
	 * Check if the task is complete.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return Pinterest_For_Woocommerce()::is_setup_complete();
	}

	/**
	 * Parent ID. This method is abstract in WooCommerce 6.1.x, 6.2.x and 6.3.x. This implementation is for backward compatibility for these versions.
	 * compatibility-code "WC 6.1.x, WC 6.2.x, WC 6.3.x"
	 *
	 * @return string
	 */
	public function get_parent_id() {
		if ( is_callable( parent::class . '::get_parent_id' ) ) {
			return parent::get_parent_id();
		}

		return 'extended'; // The parent task list id.
	}
}
