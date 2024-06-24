/**
 * Internal dependencies
 */
import TYPES from './action-types';

export function receiveFeedIssues( feedIssues ) {
	return {
		type: TYPES.RECEIVE_FEEDISSUES,
		feedIssues,
	};
}

export function receiveFeedState( feedState ) {
	return {
		type: TYPES.RECEIVE_FEEDSTATE,
		feedState,
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

export function resetFeed() {
	return {
		type: TYPES.RESET_FEED,
	};
}
