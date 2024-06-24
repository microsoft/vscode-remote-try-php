/**
 * Internal dependencies
 */
import TYPES from './action-types';

const settingsReducer = (
	state = {
		settings: {},
		isUpdating: false,
		isSyncing: false,
		requestingErrors: {},
	},
	action
) => {
	switch ( action.type ) {
		case TYPES.RECEIVE_SETTINGS:
			state = {
				...state,
				settings: {
					...state.settings,
					...action.settings,
				},
			};
			break;
		case TYPES.SET_IS_UPDATING:
			state = {
				...state,
				isUpdating: action.isUpdating,
			};
			break;
		case TYPES.SET_IS_SYNCING:
			state = {
				...state,
				isSyncing: action.isSyncing,
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
		case TYPES.SET_UPDATING_ERROR:
			state = {
				...state,
				updatingError: action.error,
				isUpdating: false,
			};
			break;
	}
	return state;
};

export default settingsReducer;
