<?php
/**
 * Setup the TikTok Onboarding Task.
 *
 * @package TikTok
 */


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
		return 'onboarding-tiktok';
	}

	/**
	 * Get the title for the task.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Reach 1 billion users worldwide by advertising your products on TikTok', 'tiktok-for-business' );
	}

	/**
	 * Content.
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
		return '';
	}

	/**
	 * Get the action URL for the task.
	 *
	 * @return string
	 */
	public function get_action_url() {
		return admin_url( 'admin.php?page=tiktok' );
	}

	/**
	 * Check if the task is complete. The task is completed if we are able to connect to TikTok.
	 *
	 * @return bool
	 */
	public function is_complete() {
		$is_connected = false;
		$access_token = get_option( 'tt4b_access_token' );

		if ( false !== $access_token ) {
			$logger               = new Logger( wc_get_logger() );
			$mapi                 = new Tt4b_Mapi_Class( $logger );
			$external_business_id = get_option( 'tt4b_external_business_id' );
			// Get business profile information to pass into external data.
			$business_profile_rsp = $mapi->get_business_profile( $access_token, $external_business_id );
			$business_profile     = json_decode( $business_profile_rsp, true );
			if ( empty( $business_profile['data'] ) ) {
				$is_connected = false;
			} elseif ( ! is_null( $business_profile['data']['status'] ) && 2 !== $business_profile['data']['status'] ) {
				$is_connected = false;
			} else {
				$is_connected = true;
			}
		}

		return $is_connected;
	}

	/**
	 * Parent ID. This method is abstract in WooCommerce 6.1.x, 6.2.x and 6.3.x. This implementation is for backward compatibility for these versions.
	 * compatibility-code "WC 6.1.x, WC 6.2.x, WC 6.3.x"
	 *
	 * @return string
	 */
	public function get_parent_id() {
		if ( is_callable( 'parent::get_parent_id' ) ) {
			return parent::get_parent_id();
		}

		return 'extended'; // The parent task list id.
	}
}
