/**
 * Internal dependencies
 */
import {
	receiveUserInteractions,
	setRequestingError,
	setIsRequesting,
} from './actions';
import { fetch } from './controls';

/**
 * Request current feed state.
 */
export function* getUserInteractions() {
	try {
		yield setIsRequesting( true );
		const result = yield fetch( 'user_interaction' );
		yield receiveUserInteractions( result );
		yield setIsRequesting( false );
	} catch ( error ) {
		yield setRequestingError( error, 'user_interaction' );
	}
}
