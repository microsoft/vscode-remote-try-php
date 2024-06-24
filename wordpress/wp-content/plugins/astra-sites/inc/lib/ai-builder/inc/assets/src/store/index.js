import { createReduxStore, register } from '@wordpress/data';
import reducer from './reducer';
import selectors from './selectors';
import actions from './actions';

export const STORE_KEY = 'st-ai-builder';

const store = createReduxStore( STORE_KEY, {
	reducer,
	actions,
	selectors,
} );

register( store );
