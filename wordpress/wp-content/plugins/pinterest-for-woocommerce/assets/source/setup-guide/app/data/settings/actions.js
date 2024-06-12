/**
 * External dependencies
 */
import { select, dispatch } from '@wordpress/data';
import { apiFetch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import TYPES from './action-types';
import { STORE_NAME, API_ENDPOINT, OPTIONS_NAME } from './constants';
import { REPORTS_STORE_NAME } from '../../../../catalog-sync/data';

export function receiveSettings( settings ) {
	return {
		type: TYPES.RECEIVE_SETTINGS,
		settings,
	};
}

export function setRequestingError( error, name ) {
	return {
		type: TYPES.SET_REQUESTING_ERROR,
		error,
		name,
	};
}

export function setUpdatingError( error ) {
	return {
		type: TYPES.SET_UPDATING_ERROR,
		error,
	};
}

export function setIsUpdating( isUpdating ) {
	return {
		type: TYPES.SET_IS_UPDATING,
		isUpdating,
	};
}

export function setIsSyncing( isSyncing ) {
	return {
		type: TYPES.SET_IS_SYNCING,
		isSyncing,
	};
}

/**
 * Update the settings in the store or in DB
 *
 * @param {Object} data The data to be updated
 * @param {boolean} [saveToDb=false] If true it persists the changes in DB
 * @return {Generator<{success}, void, ?>} A generator for the data update
 */
export function* updateSettings( data, saveToDb = false ) {
	yield receiveSettings( data );

	if ( ! saveToDb ) {
		return { success: true };
	}

	yield setIsUpdating( true );
	const settings = yield select( STORE_NAME ).getSettings();

	try {
		const results = yield apiFetch( {
			path: API_ENDPOINT,
			method: 'POST',
			data: {
				[ OPTIONS_NAME ]: settings,
			},
		} );

		dispatch( REPORTS_STORE_NAME ).resetFeed();
		dispatch( REPORTS_STORE_NAME ).invalidateResolutionForStore();

		yield setIsUpdating( false );
		return { success: results[ OPTIONS_NAME ] };
	} catch ( error ) {
		yield setUpdatingError( error );
		throw error;
	}
}

export function* syncSettings() {
	yield setIsSyncing( true );

	try {
		const results = yield apiFetch( {
			path:
				wcSettings.pinterest_for_woocommerce.apiRoute +
				'/sync_settings/',
			method: 'GET',
		} );

		if ( results.success ) {
			yield receiveSettings( results.synced_settings );
		}

		yield setIsSyncing( false );
		return { success: results.success };
	} catch ( error ) {
		yield setIsSyncing( false );
		throw error;
	}
}
