/**
 * External dependencies
 */
import { sprintf, __ } from '@wordpress/i18n';
import { Spinner } from '@woocommerce/components';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import {
	Button,
	Flex,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import {
	useSettingsSelect,
	useSyncSettingsDispatch,
	useCreateNotice,
} from '../../helpers/effects';
import './style.scss';

/**
 * Sync settings button.
 */
const SyncSettings = () => {
	const appSettings = useSettingsSelect();
	const isSyncing = useSettingsSelect( 'isSettingsSyncing' );
	const syncAppSettings = useSyncSettingsDispatch();
	const createNotice = useCreateNotice();
	const { removeNotice } = useDispatch( 'core/notices' );
	const [ triggeredSyncSettings, setTriggeredSyncSettings ] = useState(
		false
	);

	const syncSettings = async () => {
		try {
			await syncAppSettings();

			createNotice(
				'success',
				__(
					'Settings successfully synced with Pinterest Ads Manager.',
					'pinterest-for-woocommerce'
				),
				{
					id: 'pinterest-for-woocommerce-settings-synced',
				}
			);
		} catch ( error ) {
			createNotice(
				'error',
				__(
					'Failed to sync settings with Pinterest Ads Manager.',
					'pinterest-for-woocommerce'
				),
				{
					actions: [
						{
							label: 'Retry.',
							onClick: syncSettings,
						},
					],
				},
				{
					id: 'pinterest-for-woocommerce-settings-synced',
				}
			);
		}
	};

	if ( ! triggeredSyncSettings ) {
		syncSettings();
		setTriggeredSyncSettings( true );
	}

	removeNotice( 'pinterest-for-woocommerce-settings-synced' );

	const lastSyncedTime = appSettings.last_synced_settings
		? appSettings.last_synced_settings
		: '-';

	const syncInfo = sprintf(
		'%1$s: %2$s •',
		__( 'Settings last updated' ),
		lastSyncedTime
	);

	const syncButton = isSyncing ? (
		<>
			{ __( 'Syncing settings', 'pinterest-for-woocommerce' ) }
			<Spinner />
		</>
	) : (
		<Button
			isLink
			target="_blank"
			onClick={ syncSettings }
			label={ __(
				'Sync to get latest settings from Pinterest Ads Manager',
				'pinterest-for-woocommerce'
			) }
			showTooltip={ true }
			tooltipPosition="top center"
		>
			{ __( 'Sync', 'pinterest-for-woocommerce' ) }
		</Button>
	);

	return (
		<Flex justify="end" className="pinterest-for-woocommerce-sync-settings">
			<Text className="pinterest-for-woocommerce-sync-settings__info">
				{ syncInfo }
				{ syncButton }
			</Text>
		</Flex>
	);
};

export default SyncSettings;
