/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { render, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import PrelaunchNotice from './index';

describe( 'PrelaunchNotice', () => {
	it( '`pfw_get_started_notice_link_click` is tracked on click', () => {
		const { getByText } = render( <PrelaunchNotice /> );

		fireEvent.click( getByText( 'Click here for more information.' ) );

		expect( recordEvent ).toHaveBeenCalledWith(
			'pfw_get_started_notice_link_click',
			expect.any( Object )
		);
	} );
} );
