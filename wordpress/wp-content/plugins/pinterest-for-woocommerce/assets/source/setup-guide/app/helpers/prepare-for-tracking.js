const TRACKABLE_SETTINGS = [
	'enable_debug_logging',
	'enhanced_match_support',
	'automatic_enhanced_match_support',
	'erase_plugin_data',
	'product_sync_enabled',
	'rich_pins_on_posts',
	'rich_pins_on_products',
	'save_to_pinterest',
	'track_conversions',
];

/**
 * Prepares the data for tracking. Only allows the props inside TRACKABLE_SETTINGS to be tracked.
 *
 * @param {Object} data The raw data in which we want to extract the properties to track
 * @return {Object} The data prepared for Tracking
 */
function prepareForTracking( data = {} ) {
	const preparedData = {};

	// Only allows the properties we want to track, this is for preventing sensitive information, like account info, to be tracked.
	TRACKABLE_SETTINGS.forEach( ( setting ) => {
		if ( typeof data[ setting ] !== 'undefined' ) {
			preparedData[ setting ] = data[ setting ];
		}
	} );

	return preparedData;
}

export default prepareForTracking;
