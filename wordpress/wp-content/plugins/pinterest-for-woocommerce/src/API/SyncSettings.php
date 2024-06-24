<?php
/**
 * API Sync Settings
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest\PinterestSyncSettings;
use \WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint handling SyncSettings.
 */
class SyncSettings extends VendorAPI {

	/**
	 * Initialize class
	 */
	public function __construct() {

		$this->base              = 'sync_settings';
		$this->endpoint_callback = 'sync_settings';
		$this->methods           = WP_REST_Server::READABLE;

		$this->register_routes();
	}


	/**
	 * Handle sync settings.
	 *
	 * @return array
	 */
	public function sync_settings() {
		return PinterestSyncSettings::sync_settings();
	}
}
