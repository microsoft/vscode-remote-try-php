/**
 * Internal dependencies
 */
import TYPES from './action-types';

const reportsReducer = (
	state = {
		feedIssues: {},
		feedState: {},
		isRequesting: false,
		requestingErrors: {},
	},
	action
) => {
	switch ( action.type ) {
		case TYPES.RECEIVE_FEEDISSUES:
			state = {
				...state,
				feedIssues: action.feedIssues,
			};
			break;
		case TYPES.RECEIVE_FEEDSTATE:
			state = {
				...state,
				feedState: action.feedState,
			};
			break;
		case TYPES.RESET_FEED:
			state = {
				...state,
				feedIssues: {},
				feedState: {},
			};
			break;
		case TYPES.SET_IS_REQUESTING:
			state = {
				...state,
				isRequesting: action.isRequesting,
			};
			break;
		case TYPES.SET_REQUESTING_ERROR:
			state = {
				...state,
				requestingErrors: {
					[ action.name ]: action.error,
				},
			};
			break;
	}

	return state;
};

export default reportsReducer;
