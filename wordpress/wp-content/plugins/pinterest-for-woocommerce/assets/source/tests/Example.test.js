/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */

import Example from './Example';

describe( 'Example test', () => {
	it( 'should show Example', () => {
		const { getByText } = render( <Example /> );
		expect( getByText( 'Example' ) ).toBeTruthy();
	} );
} );
