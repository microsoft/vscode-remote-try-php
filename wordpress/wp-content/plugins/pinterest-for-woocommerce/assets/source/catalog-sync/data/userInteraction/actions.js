/**
 * External dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import TYPES from './action-types';
import { API_ROUTE } from './constants';

export function receiveUserInteractions( userInteractions ) {
	return {
		type: TYPES.RECEIVE_INTERACTIONS,
		userInteractions,
	};
}

export function setAdsModalDismissed( modalDismissed ) {
	return {
		type: TYPES.SET_ADS_MODAL_DISMISSED,
		modalDismissed,
	};
}

export function setIsRequesting( isRequesting ) {
	return {
		type: TYPES.SET_IS_REQUESTING,
		isRequesting,
	};
}

export function setRequestingError( error, name ) {
	return {
		type: TYPES.SET_REQUESTING_ERROR,
		error,
		name,
	};
}

export function* adsModalDismissed() {
	yield setAdsModalDismissed( true );

	try {
		const results = yield apiFetch( {
			path: API_ROUTE,
			method: 'POST',
			data: {
				ads_modal_dismissed: true,
			},
		} );

		return { success: results.ads_modal_dismissed };
	} catch ( error ) {}
}

export function setAdsNoticeDismissed( noticeDismissed ) {
	return {
		type: TYPES.SET_ADS_NOTICE_DISMISSED,
		noticeDismissed,
	};
}

export function* adsNoticeDismissed() {
	yield setAdsNoticeDismissed( true );

	try {
		const results = yield apiFetch( {
			path: API_ROUTE,
			method: 'POST',
			data: {
				ads_notice_dismissed: true,
			},
		} );

		return { success: results.ads_notice_dismissed };
	} catch ( error ) {}
}

export function setBillingSetupFlowEntered() {
	return {
		type: TYPES.SET_BILLING_SETUP_FLOW_ENTERED,
	};
}

export function* billingSetupFlowEntered() {
	yield setBillingSetupFlowEntered();

	try {
		const results = yield apiFetch( {
			path: API_ROUTE,
			method: 'POST',
			data: {
				billing_setup_flow_entered: true,
			},
		} );

		return { success: results.billing_setup_flow_entered };
	} catch ( error ) {}
}
