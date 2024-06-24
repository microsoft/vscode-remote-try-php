jest.mock( '@woocommerce/tracks' );

/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import LandingPageApp from './LandingPageApp';
import '../../../tests/custom-matchers';

recordEvent.mockName( 'recordEvent' );
jest.mock( '@woocommerce/settings', () => ( {
	getSetting: jest
		.fn()
		.mockName( 'getSetting' )
		.mockReturnValue( { es: 'Spain' } ),
} ) );

afterEach( () => {
	jest.clearAllMocks();
} );

describe( 'LandingPageApp component', () => {
	describe( 'when rendered', () => {
		let getByRole;
		beforeEach( () => {
			// Render connected component.
			getByRole = render( <LandingPageApp /> ).getByRole;
		} );
		describe( 'while `wcSettings.pinterest_for_woocommerce.isSetupComplete = false`', () => {
			beforeEach( () => {
				wcSettings.pinterest_for_woocommerce.isSetupComplete = false;
			} );
			describe( 'once "Get started" button is clicked', () => {
				beforeEach( () => {
					// Find & click the button.
					const getStartedButton = getByRole( 'button', {
						name: 'Get started',
					} );
					fireEvent.click( getStartedButton );
				} );

				it( "should call `wcadmin_pfw_setup { target: 'onboarding', trigger: 'get-started' }` track event", () => {
					expect( recordEvent ).toHaveBeenCalledWith( 'pfw_setup', {
						target: 'onboarding',
						trigger: 'get-started',
					} );
				} );

				it( 'should navigate to `/pinterest/onboarding`', () => {
					expect( window.location ).toContainURLSearchParam(
						'path',
						'/pinterest/onboarding'
					);
				} );
			} );
		} );
		describe( 'while `wcSettings.pinterest_for_woocommerce.isSetupComplete = true`', () => {
			beforeEach( () => {
				wcSettings.pinterest_for_woocommerce.isSetupComplete = true;
			} );
			describe( 'once "Get started" button is clicked', () => {
				beforeEach( () => {
					// Find & click the button.
					const getStartedButton = getByRole( 'button', {
						name: 'Get started',
					} );
					fireEvent.click( getStartedButton );
				} );

				it( 'should not call `wcadmin_pfw_setup` track event', () => {
					expect( recordEvent ).not.toHaveBeenCalledWith(
						'pfw_setup'
					);
				} );

				it( 'should navigate to `/pinterest/catalog`', () => {
					expect( window.location ).toContainURLSearchParam(
						'path',
						'/pinterest/catalog'
					);
				} );
			} );
		} );
	} );
} );
