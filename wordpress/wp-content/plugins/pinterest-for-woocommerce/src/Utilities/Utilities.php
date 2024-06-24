<?php
/**
 * Pinterest for WooCommerce Utilities.
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @since       1.1.0
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Utilities;

/**
 * Utilities class.
 *
 * @since 1.1.0
 */
class Utilities {

	const ACCOUNT_CONNECTION_TIMESTAMP = PINTEREST_FOR_WOOCOMMERCE_OPTION_NAME . '_account_connection_timestamp';

	/**
	 * Set the account connection timestamp.
	 *
	 * @since 1.1.0
	 */
	public static function set_account_connection_timestamp() {
		update_option( self::ACCOUNT_CONNECTION_TIMESTAMP, time() );
	}

	/**
	 * Gets the account connection timestamp.
	 *
	 * @since 1.1.0
	 * @return int Account connection timestamp. Zero if not set.
	 */
	public static function get_account_connection_timestamp(): int {
		return (int) get_option( self::ACCOUNT_CONNECTION_TIMESTAMP, 0 );
	}
}
