/**
 * Internal dependencies
 */
import {
	receiveFeedIssues,
	receiveFeedState,
	setRequestingError,
	setIsRequesting,
} from './actions';
import { fetch } from './controls';

/**
 * Request current feed issues.
 *
 * @param {Object} query
 */
export function* getFeedIssues( query = {} ) {
	try {
		const data = {
			paged: query.paged || 1,
			per_page: query.per_page || 25,
		};
		yield setIsRequesting( true );

		const result = yield fetch( 'feed_issues', data );
		yield receiveFeedIssues( result );

		yield setIsRequesting( false );
	} catch ( error ) {
		yield setRequestingError( error, 'feed_issues' );
	}
}

/**
 * Request current feed state.
 */
export function* getFeedState() {
	try {
		const result = yield fetch( 'feed_state' );
		yield receiveFeedState( result );
	} catch ( error ) {
		yield setRequestingError( error, 'feed_state' );
	}
}
