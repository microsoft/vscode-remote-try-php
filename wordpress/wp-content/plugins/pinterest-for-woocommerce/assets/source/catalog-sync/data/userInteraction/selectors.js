/**
 * Has user dismissed ads modal.
 *
 * @param {Object} state - Reducer state
 */
export const getUserInteractions = ( state ) => {
	return state.userInteractions;
};

/**
 * Determine if interactions options have been loaded.
 *
 * @param {Object} state - Reducer state
 */
export const areInteractionsLoaded = ( state ) => {
	return state.interactionsLoaded;
};
