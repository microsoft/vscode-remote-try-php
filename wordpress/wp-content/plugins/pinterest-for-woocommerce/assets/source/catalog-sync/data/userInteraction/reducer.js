/**
 * Internal dependencies
 */
import TYPES from './action-types';

const userInteractionsReducer = (
	state = {
		userInteractions: {
			ads_modal_dismissed: false,
			ads_notice_dismissed: false,
			billingSetupFlowEntered: false,
		},
		isRequesting: false,
		interactionsLoaded: false,
		requestingErrors: {},
	},
	action
) => {
	switch ( action.type ) {
		case TYPES.RECEIVE_INTERACTIONS:
			state = {
				...state,
				userInteractions: action.userInteractions,
				interactionsLoaded: true,
			};
			break;
		case TYPES.SET_IS_REQUESTING:
			state = {
				...state,
				isRequesting: action.isRequesting,
			};
			break;
		case TYPES.SET_ADS_MODAL_DISMISSED:
			state = {
				...state,
				userInteractions: {
					...state.userInteractions,
					ads_modal_dismissed: action.modalDismissed,
				},
			};
			break;
		case TYPES.SET_ADS_NOTICE_DISMISSED:
			state = {
				...state,
				userInteractions: {
					...state.userInteractions,
					ads_notice_dismissed: action.noticeDismissed,
				},
			};
			break;
		case TYPES.SET_BILLING_SETUP_FLOW_ENTERED:
			state = {
				...state,
				userInteractions: {
					...state.userInteractions,
					billingSetupFlowEntered: true,
				},
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

export default userInteractionsReducer;
