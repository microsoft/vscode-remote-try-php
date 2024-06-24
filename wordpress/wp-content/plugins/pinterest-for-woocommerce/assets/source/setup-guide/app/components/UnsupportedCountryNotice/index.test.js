/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { render, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';

/**
 * Internal dependencies
 */
import UnsupportedCountryNotice from './index';

describe( 'UnsupportedCountryNotice', () => {
	it( '`pfw_get_started_notice_link_click` is tracked on click', () => {
		const { getByText } = render(
			<UnsupportedCountryNotice countryCode="es" />
		);

		fireEvent.click( getByText( 'Change your store’s country here' ) );

		expect( recordEvent ).toHaveBeenCalledWith(
			'pfw_get_started_notice_link_click',
			expect.any( Object )
		);
	} );
} );
