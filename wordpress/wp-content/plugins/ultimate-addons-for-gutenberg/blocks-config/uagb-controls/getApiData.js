// Import the apiFetch function from the '@wordpress/api-fetch' package
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { isObjectNotEmpty } from '../utils/Helpers'

/**
 * A function to send form data via API fetch.
 *
 * @async
 * @function
 *
 * @param {Object} params - The parameters object.
 * @param {string} params.url - The URL to send the data.
 * @param {string} params.action - The action to take with the data.
 * @param {Object} params.data - The data to send.
 *
 * @return {Promise} Returns a Promise that resolves to an object containing the API response.
 */
const getApiData = async ( { url, action, data } ) => {

	if ( !isObjectNotEmpty( data ) ) { 
		return Promise.reject( new Error( __( 'data object is empty', 'ultimate-addons-for-gutenberg' ) ) );
	}
	// Create a new instance of the FormData class
	const formData = new window.FormData();
	// Append an 'action' property to the formData object
	formData.append( 'action', action );

	// If the 'data' object is not empty, iterate over its key-value pairs and append them to the formData object
	for( const dataKey in data ){
		const dataValue = data[dataKey];
		formData.append( dataKey, dataValue );
	}
	
	// Make a POST request using the apiFetch function, passing in the url, method, and body properties
	return await apiFetch( {
		url,
		method: 'POST',
		body: formData,
	} );
};


// Export the getApiData function as the default export of the module
export default getApiData;