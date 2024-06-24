/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { recordEvent } from '@woocommerce/tracks';

/**
 * Internal dependencies
 */
import {
	useSettingsSelect,
	useSettingsDispatch,
	useCreateNotice,
	useResetSettings,
} from '../helpers/effects';
import connectAdvertiser from '../helpers/connect-advertiser';
import prepareForTracking from '../helpers/prepare-for-tracking';
import { useResetUserInteractions } from '../../../catalog-sync/helpers/effects';

/**
 * Clicking on "… Save changes" button.
 *
 * @event wcadmin_pfw_save_changes_button_click
 *
 * @property {boolean} enable_debug_logging Indicates if Enable debug logging option is checked
 * @property {boolean} enhanced_match_support Indicates if Enhanced Match Support option is checked
 * @property {boolean} automatic_enhanced_match_support Indicates if Automatic Enhanced Match Support option is checked
 * @property {boolean} erase_plugin_data Indicates if Erase Plugin Data option is checked
 * @property {boolean} product_sync_enabled Indicates if Enable Product Sync option is checked
 * @property {boolean} rich_pins_on_posts Indicates if Add Rich Pins for Posts option is checked
 * @property {boolean} rich_pins_on_products Indicates if Add Rich Pins for Products option is checked
 * @property {boolean} save_to_pinterest Indicates if Save to Pinterest option is checked
 * @property {boolean} track_conversions Indicates if Track Conversion option is checked
 * @property {string} context The context in which the event is recorded
 */

/**
 * Save Settings button component
 *
 * @fires wcadmin_pfw_save_changes_button_click with `{ context: view, … }`
 * @param {string} view The view in which this component is being rendered
 * @return {JSX.Element} Rendered element
 */
const SaveSettingsButton = ( { view } ) => {
	const isSaving = useSettingsSelect( 'isSettingsUpdating' );
	const settings = useSettingsSelect( 'getSettings' );
	const resetUserInteractions = useResetUserInteractions();
	const resetSettings = useResetSettings();
	const setAppSettings = useSettingsDispatch( true );
	const createNotice = useCreateNotice();
	const appSettings = useSettingsSelect();

	const saveSettings = async () => {
		recordEvent( 'pfw_save_changes_button_click', {
			...prepareForTracking( settings ),
			context: view,
		} );

		try {
			await setAppSettings( {} );

			if (
				appSettings?.tracking_advertiser &&
				appSettings?.tracking_tag
			) {
				const result = await connectAdvertiser(
					appSettings.tracking_advertiser,
					appSettings.tracking_tag
				);

				if ( result ) {
					createNotice(
						'success',
						__(
							'The advertiser was connected successfully.',
							'pinterest-for-woocommerce'
						)
					);
				}
			}

			/*
			 * Make sure that the state is fresh after a reset.
			 * We want fresh options because account data could have been updated.
			 * We want fresh interactions because different advertiser has different coupons.
			 */
			await resetSettings();
			await resetUserInteractions();

			createNotice(
				'success',
				__(
					'Your settings have been saved successfully.',
					'pinterest-for-woocommerce'
				)
			);
		} catch ( error ) {
			createNotice(
				'error',
				__(
					'There was a problem saving your settings.',
					'pinterest-for-woocommerce'
				)
			);
		}
	};

	return (
		<div className="woocommerce-setup-guide__footer-button">
			<Button isPrimary onClick={ saveSettings } disabled={ isSaving }>
				{ isSaving
					? __( 'Saving settings…', 'pinterest-for-woocommerce' )
					: __( 'Save changes', 'pinterest-for-woocommerce' ) }
			</Button>
		</div>
	);
};

export default SaveSettingsButton;
