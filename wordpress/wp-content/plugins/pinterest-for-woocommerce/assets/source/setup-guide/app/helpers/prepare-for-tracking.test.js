/**
 * Internal dependencies
 */
import prepareForTracking from './prepare-for-tracking';

describe( 'Prepare for Tracking function', () => {
	const trackableData = {
		enable_debug_logging: true,
		enhanced_match_support: true,
		automatic_enhanced_match_support: true,
		erase_plugin_data: true,
		product_sync_enabled: true,
		rich_pins_on_posts: true,
		rich_pins_on_products: true,
		save_to_pinterest: true,
		track_conversions: true,
	};

	const rawData = {
		account_data: 'sensitive',
		...trackableData,
	};

	it( 'Only tracks props explicitly added in TRACKABLE_DATA constant', () => {
		expect( prepareForTracking( rawData ) ).toStrictEqual( trackableData );
	} );
} );
