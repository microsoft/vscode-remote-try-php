/**
 * Get settings from state tree.
 *
 * @param {Object} state - Reducer state
 */
export const getFeedIssues = ( state ) => {
	return state.feedIssues;
};

/**
 * Get setting from state tree.
 *
 * @param {Object} state - Reducer state
 */
export const getFeedState = ( state ) => {
	return state.feedState;
};

/**
 * Determine if an options request resulted in an error.
 *
 * @param {Object} state - Reducer state
 * @param {string} name - Report name
 */
export const getReportsRequestingError = ( state, name ) => {
	return state.requestingErrors[ name ] || false;
};

/**
 * Determine if options are being updated.
 *
 * @param {Object} state - Reducer state
 */
export const isRequesting = ( state ) => {
	return state.isRequesting || false;
};
