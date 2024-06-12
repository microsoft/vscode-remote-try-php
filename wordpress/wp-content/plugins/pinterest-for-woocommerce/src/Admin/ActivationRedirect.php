<?php
/**
 * Helper class for handling the redirection to the onboarding page.
 *
 * @package Automattic\WooCommerce\Pinterest\Admin
 * @since   1.1.0
 */

namespace Automattic\WooCommerce\Pinterest\Admin;

use Automattic\WooCommerce\Pinterest\PluginHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ActivationRedirect
 */
class ActivationRedirect {

	use PluginHelper;

	/**
	 * The setting for redirect to onboarding.
	 *
	 * @since  1.1.0
	 */
	protected const DID_REDIRECT_SETTING = 'did_redirect_to_onboarding';

	/**
	 * Initiate class.
	 *
	 * @since  1.1.0
	 */
	public function register() {
		add_action(
			'admin_init',
			function () {
				$this->maybe_redirect_to_onboarding();
			}
		);
	}

	/**
	 * Checks if merchant should be redirected to the onboarding page.
	 *
	 * @since  1.1.0
	 */
	protected function maybe_redirect_to_onboarding() {
		if ( wp_doing_ajax() ) {
			return;
		}

		// If we have redirected before do not attempt to redirect again.
		if ( ! $this->did_redirect_setting() ) {
			return;
		}

		// Do not redirect if setup is already complete.
		if ( Pinterest_For_Woocommerce()::is_setup_complete() ) {
			$this->update_did_redirect_setting( false );
			return;
		}

		// Do not redirect if we already are in the Get Started page.
		if ( $this->is_onboarding_page() && $this->did_redirect_setting() ) {
			$this->update_did_redirect_setting( false );
			return;
		}

		// Redirect if setup is not complete.
		$this->redirect_to_onboarding_page();

	}

	/**
	 * Utility function to immediately redirect to the main "Get Started" onboarding page.
	 * Note that this function immediately ends the execution.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function redirect_to_onboarding_page(): void {
		// If we are already on the onboarding page, do nothing.
		if ( $this->is_onboarding_page() ) {
			return;
		}

		$this->update_did_redirect_setting( true );

		wp_safe_redirect( admin_url( add_query_arg( $this->onboarding_page_parameters(), 'admin.php' ) ) );
		exit();
	}

	/**
	 * Maybe update the redirect option.
	 *
	 * @since  1.1.0
	 */
	public function maybe_update_redirect_option(): void {

		if (
			// Only redirect to onboarding when activated on its own.
			isset( $_GET['action'] ) && 'activate' === $_GET['action'] // phpcs:ignore WordPress.Security.NonceVerification
			// ...or with a bulk action.
			|| isset( $_POST['checked'] ) && is_array( $_POST['checked'] ) && 1 === count( $_POST['checked'] ) // phpcs:ignore WordPress.Security.NonceVerification
		) {
			$this->update_did_redirect_setting( true );
		}
	}

	/**
	 * Update the redirect setting.
	 *
	 * @param bool $did_redirect The new value.
	 * @since 1.1.0
	 */
	protected function update_did_redirect_setting( $did_redirect ): void {
		Pinterest_For_Woocommerce()->save_setting( self::DID_REDIRECT_SETTING, $did_redirect );
	}

	/**
	 * Get the redirect setting.
	 *
	 * @since  1.1.0
	 * @return bool
	 */
	protected function did_redirect_setting(): bool {
		return (bool) Pinterest_For_Woocommerce()->get_setting( self::DID_REDIRECT_SETTING );
	}
}
