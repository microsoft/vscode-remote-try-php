<?php
/**
 * Pinterest for WooCommerce Track Events.
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @since       1.0.10
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Utilities;

/**
 * Trait Tracks
 *
 * @since 1.2.5
 */
trait Tracks {

	/**
	 * Record a tracks event.
	 *
	 * @param string $name       The event name to record.
	 * @param array  $properties Array of properties to include with the event.
	 */
	private static function record_event( string $name, array $properties = array() ): void {
		if ( class_exists( WC_Tracks::class ) ) {
			WC_Tracks::record_event( $name, $properties );
		} elseif ( function_exists( 'wc_admin_record_tracks_event' ) ) {
			wc_admin_record_tracks_event( $name, $properties );
		}
	}

}
