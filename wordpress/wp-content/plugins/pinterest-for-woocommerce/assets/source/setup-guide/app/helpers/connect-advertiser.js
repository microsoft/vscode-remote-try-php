/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';

const connectAdvertiser = async (
	trackingAdvertiser,
	trackingTag,
	enableAem = false
) => {
	try {
		const results = await apiFetch( {
			path: `${ wcSettings.pinterest_for_woocommerce.apiRoute }/tagowner`,
			data: {
				advrtsr_id: trackingAdvertiser,
				tag_id: trackingTag,
				enable_aem: enableAem,
			},
			method: 'POST',
		} );

		return results.reconnected;
	} catch ( error ) {
		throw error;
	}
};

export default connectAdvertiser;
