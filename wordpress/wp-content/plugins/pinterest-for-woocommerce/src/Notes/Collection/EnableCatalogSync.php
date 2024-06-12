<?php
/**
 * Pinterest for WooCommerce EnableCatalogSync class.
 *
 * @package Pinterest_For_WooCommerce/Classes/
 * @version 1.1.0
 */

namespace Automattic\WooCommerce\Pinterest\Notes\Collection;

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Pinterest\ProductSync;
use Automattic\WooCommerce\Pinterest\Utilities\Utilities;

/**
 * Class EnableCatalogSync.
 *
 * Class responsible for admin Inbox notification after successful connection but
 * the sync feature disabled.
 *
 * @since 1.1.0
 */
class EnableCatalogSync extends AbstractNote {

	const NOTE_NAME = 'pinterest-enable-catalog-sync';

	/**
	 * Should the note be added to the inbox.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public static function should_be_added(): bool {
		if ( ! Pinterest_For_Woocommerce()::is_setup_complete() ) {
			return false;
		}

		if ( ProductSync::is_product_sync_enabled() ) {
			return false;
		}

		if ( self::note_exists() ) {
			return false;
		}

		// Are we there yet? We want to try three days after the account was connected.
		if ( time() < ( DAY_IN_SECONDS * 3 + Utilities::get_account_connection_timestamp() ) ) {
			return false;
		}

		// All preconditions are met, we can send the note.
		return true;
	}

	/**
	 * Get note title.
	 *
	 * @since 1.1.0
	 * @return string Note title.
	 */
	protected function get_note_title(): string {
		return __( 'Notice: Your products aren’t synced on Pinterest', 'pinterest-for-woocommerce' );
	}

	/**
	 * Get note content.
	 *
	 * @since 1.1.0
	 * @return string Note content.
	 */
	protected function get_note_content(): string {
		return __( 'Your Catalog sync with Pinterest has been disabled. Select “Enable Product Sync” to sync your products and reach shoppers on Pinterest.', 'pinterest-for-woocommerce' );
	}

	/**
	 * Add button to Pinterest For WooCommerce landing page
	 *
	 * @since 1.1.0
	 * @param Note $note Note to which we add an action.
	 */
	protected function add_action( $note ): void {
		$note->add_action(
			'goto-pinterest-settings',
			__( 'Enable Sync', 'pinterest-for-woocommerce' ),
			wc_admin_url( '&path=/pinterest/settings' )
		);
	}

}
