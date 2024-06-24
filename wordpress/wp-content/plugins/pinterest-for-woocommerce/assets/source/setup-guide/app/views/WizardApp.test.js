jest.mock( '@woocommerce/tracks' );
jest.mock( '../data/settings/selectors', () => ( {
	...jest.requireActual( '../data/settings/selectors' ), // import and retain the original functionalities
	isTrackingConfigured: jest.fn().mockReturnValue( false ),
	isDomainVerified: jest.fn().mockReturnValue( false ),
} ) );
/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import '@testing-library/jest-dom';
import { fireEvent, render } from '@testing-library/react';
import { getQuery } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import WizardApp from './WizardApp';
import '../../../tests/custom-matchers';
import {
	isDomainVerified,
	isTrackingConfigured,
} from '../data/settings/selectors';

recordEvent.mockName( 'recordEvent' );

//Needed to be able to render the Stepper component
jest.mock( '../steps/SetupAccount', () => () => null );
jest.mock( '../steps/ClaimWebsite', () => () => null );
jest.mock( '../steps/SetupTracking', () => () => null );

const stepOne = /Set up your business account/;
const stepTwo = /Claim your website/;
const stepThree = /Track conversions/;

describe( 'WizardApp component', () => {
	describe( 'First rendering', () => {
		let rendered;
		beforeEach( () => {
			wcSettings.pinterest_for_woocommerce.isBusinessConnected = false;
			wcSettings.pinterest_for_woocommerce.isConnected = false;
			rendered = render( <WizardApp query={ getQuery() } /> );
		} );
		test( 'should show all options and first step should be clickable', () => {
			expect( rendered.getByText( stepOne ) ).toBeInTheDocument();
			expect( rendered.getByText( stepTwo ) ).toBeInTheDocument();
			expect( rendered.getByText( stepThree ) ).toBeInTheDocument();

			expect( rendered.queryAllByRole( 'button' ).length ).toBe( 1 );
		} );
	} );
	describe( 'First step is completed', () => {
		let rendered;

		beforeEach( () => {
			wcSettings.pinterest_for_woocommerce.isBusinessConnected = true;
			wcSettings.pinterest_for_woocommerce.isConnected = true;
			rendered = render( <WizardApp query={ getQuery() } /> );
		} );

		test( 'should step 1 & 2 be clickable in the stepper', () => {
			expect( rendered.queryAllByRole( 'button' ).length ).toBe( 2 );
			expect(
				rendered.getByRole( 'button', {
					name: stepOne,
				} )
			).toBeInTheDocument();
			expect(
				rendered.getByRole( 'button', {
					name: stepTwo,
				} )
			).toBeInTheDocument();
		} );
	} );
	describe( 'Second step is completed', () => {
		let rendered;

		beforeEach( () => {
			wcSettings.pinterest_for_woocommerce.isBusinessConnected = true;
			wcSettings.pinterest_for_woocommerce.isConnected = true;
			isDomainVerified.mockImplementation( () => true );
			rendered = render( <WizardApp query={ getQuery() } /> );
		} );

		test( 'should 3 steps button be clickable in the stepper', () => {
			expect( rendered.queryAllByRole( 'button' ).length ).toBe( 3 );

			const setUpButton = rendered.getByRole( 'button', {
				name: stepOne,
			} );
			const claimButton = rendered.getByRole( 'button', {
				name: stepTwo,
			} );

			expect( setUpButton ).toBeInTheDocument();
			expect( claimButton ).toBeInTheDocument();
		} );
		test( 'should event tracking be = setup-account after click', () => {
			const setUpButton = rendered.getByRole( 'button', {
				name: stepOne,
			} );

			fireEvent.click( setUpButton );

			expect( recordEvent ).toHaveBeenCalledWith( 'pfw_setup', {
				target: 'setup-account',
				trigger: 'wizard-stepper',
			} );
		} );
		test( 'should navigate to `step=setup-account`', () => {
			expect( window.location ).toContainURLSearchParam(
				'step',
				'setup-account'
			);
		} );
	} );

	describe( 'Third step is completed', () => {
		let rendered;

		beforeEach( () => {
			wcSettings.pinterest_for_woocommerce.isBusinessConnected = true;
			wcSettings.pinterest_for_woocommerce.isConnected = true;
			isDomainVerified.mockImplementation( () => true );
			isTrackingConfigured.mockImplementation( () => true );
			rendered = render( <WizardApp query={ getQuery() } /> );
		} );

		test( 'should all three steps be clickable buttons', () => {
			expect( rendered.queryAllByRole( 'button' ).length ).toBe( 3 );

			expect(
				rendered.getByRole( 'button', {
					name: stepOne,
					exact: false,
				} )
			).toBeInTheDocument();
			expect(
				rendered.getByRole( 'button', {
					name: stepTwo,
					exact: false,
				} )
			).toBeInTheDocument();
			expect(
				rendered.getByRole( 'button', {
					name: stepThree,
					exact: false,
				} )
			).toBeInTheDocument();
		} );

		test( 'should event tracking be = claim-website after click', () => {
			fireEvent.click(
				rendered.getByRole( 'button', {
					name: stepTwo,
				} )
			);

			expect( recordEvent ).toHaveBeenCalledWith( 'pfw_setup', {
				target: 'claim-website',
				trigger: 'wizard-stepper',
			} );
		} );

		test( 'should navigate to `step=claim-website', () => {
			expect( window.location ).toContainURLSearchParam(
				'step',
				'claim-website'
			);
		} );

		test( 'should event tracking be = setup-tracking after click', () => {
			fireEvent.click(
				rendered.getByRole( 'button', {
					name: stepThree,
				} )
			);

			expect( recordEvent ).toHaveBeenCalledWith( 'pfw_setup', {
				target: 'setup-tracking',
				trigger: 'wizard-stepper',
			} );
		} );
	} );
} );
