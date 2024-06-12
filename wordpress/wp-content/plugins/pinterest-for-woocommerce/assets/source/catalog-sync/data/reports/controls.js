/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { API_ROUTE } from './constants';

export const fetch = ( endpoint, data = {} ) => {
	return {
		type: 'FETCH',
		endpoint,
		data,
	};
};

export const controls = {
	FETCH( { endpoint, data = {} } ) {
		return new Promise( ( resolve ) => {
			const url = addQueryArgs( `${ API_ROUTE }/${ endpoint }`, data );

			apiFetch( { path: url } ).then( ( result ) => resolve( result ) );
		} );
	},
};
