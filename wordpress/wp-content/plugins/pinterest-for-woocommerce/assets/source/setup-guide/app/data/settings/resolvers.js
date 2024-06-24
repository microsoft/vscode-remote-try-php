/**
 * Internal dependencies
 */
import { receiveSettings, setRequestingError } from './actions';
import { fetch } from './controls';

/**
 * Request all settings values.
 */
export function* getSettings() {
	try {
		const result = yield fetch();
		yield receiveSettings( result );
	} catch ( error ) {
		yield setRequestingError( error, 'all' );
	}
}
