jest.mock( '../../helpers/effects' );
jest.mock( '@woocommerce/tracks' );

/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { fireEvent, render } from '@testing-library/react';
import userEvent from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import AccountConnection from './Connection';

recordEvent.mockName( 'recordEvent' );

afterEach( () => {
	jest.clearAllMocks();
} );

describe( 'AccountConnection component', () => {
	describe( 'when rendered with account data', () => {
		let getByRole;
		beforeEach( () => {
			// Render connected component.
			getByRole = render(
				<AccountConnection
					isConnected={ true }
					accountData={ { id: 123 } }
					context="foo"
				/>
			).getByRole;
		} );
		describe( 'once "Disconnect" button is clicked', () => {
			beforeEach( () => {
				// Find & click the Disconnect button.
				const disconnectButton = getByRole( 'button', {
					name: 'Disconnect',
				} );
				fireEvent.click( disconnectButton );
			} );

			it( 'Should call `pfw_account_disconnect_button_click { context }` track event', () => {
				// Assert fired event.
				expect( recordEvent ).toHaveBeenCalledWith(
					'pfw_account_disconnect_button_click',
					{
						context: 'foo',
					}
				);
			} );

			it( "Should call `pfw_modal_open { name: 'account-disconnection', context}` track event", () => {
				// Assert fired event.
				expect( recordEvent ).toHaveBeenCalledWith( 'pfw_modal_open', {
					context: 'foo',
					name: 'account-disconnection',
				} );
			} );

			it( "then \"Cancel\" button is clicked, should call `pfw_modal_closed { name: 'account-disconnection', action: 'dismiss', context}` track event", () => {
				// Find & click the Cancel button.
				const cancelButton = getByRole( 'button', {
					name: 'Cancel',
				} );
				fireEvent.click( cancelButton );

				// Assert fired event.
				expect( recordEvent ).toHaveBeenCalledWith(
					'pfw_modal_closed',
					{
						action: 'dismiss',
						context: 'foo',
						name: 'account-disconnection',
					}
				);
			} );

			it( "then \"X\" button is clicked, should call `pfw_modal_closed { name: 'account-disconnection', action: 'dismiss', context}` track event", () => {
				// Find & click the Cancel button.
				const cancelButton = getByRole( 'button', {
					name: 'Close dialog',
				} );
				fireEvent.click( cancelButton );

				// Assert fired event.
				expect( recordEvent ).toHaveBeenCalledWith(
					'pfw_modal_closed',
					{
						action: 'dismiss',
						context: 'foo',
						name: 'account-disconnection',
					}
				);
			} );

			it( "then \"Esc\" key is pressed, should call `pfw_modal_closed { name: 'account-disconnection', action: 'dismiss', context}` track event", () => {
				// Press Esc.
				userEvent.keyboard( '{esc}' );

				// Assert fired event.
				expect( recordEvent ).toHaveBeenCalledWith(
					'pfw_modal_closed',
					{
						action: 'dismiss',
						context: 'foo',
						name: 'account-disconnection',
					}
				);
			} );
		} );
	} );
} );
