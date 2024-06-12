jest.mock( '../helpers/effects', () => {
	return {
		useCreateNotice: () => () => {},
		useSettingsSelect: () => {},
		useSettingsDispatch: () => () => {},
		useResetSettings: () => () => {},
		useResetUserInteractions: () => () => {},
	};
} );
jest.mock( '@woocommerce/tracks', () => {
	return {
		recordEvent: jest.fn().mockName( 'recordEvent' ),
	};
} );

/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import SaveSettingsButton from './SaveSettingsButton';

afterEach( () => {
	jest.clearAllMocks();
} );

describe( 'Save Settings function', () => {
	const eventName = 'pfw_save_changes_button_click';

	const views = [ 'pinterest_settings', 'pinterest_connection' ];

	it.each( views )(
		`${ eventName } is called on Save Settings in "%s" view.`,
		( view ) => {
			const { getByRole } = render(
				<SaveSettingsButton view={ view } />
			);
			const saveSettingsBtn = getByRole( 'button' );

			fireEvent.click( saveSettingsBtn );
			expect( recordEvent ).toHaveBeenCalledWith( eventName, {
				context: view,
			} );
		}
	);
} );
