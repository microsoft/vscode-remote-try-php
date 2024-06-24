/**
 * External dependencies
 */
import '@wordpress/notices';
import { Spinner } from '@woocommerce/components';
import { useState, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import SetupAccount from '../steps/SetupAccount';
import ClaimWebsite from '../steps/ClaimWebsite';
import SetupTracking from '../steps/SetupTracking';
import SaveSettingsButton from '../components/SaveSettingsButton';
import HealthCheck from '../components/HealthCheck';
import {
	useSettingsSelect,
	useBodyClasses,
	useCreateNotice,
} from '../helpers/effects';
import { SETTINGS_VIEW } from '../helpers/views';
import NavigationClassic from '../../../components/navigation-classic';

const SettingsApp = () => {
	const appSettings = useSettingsSelect();
	const isDomainVerified = useSettingsSelect( 'isDomainVerified' );

	const [ isConnected, setIsConnected ] = useState(
		wcSettings.pinterest_for_woocommerce.isConnected
	);

	const [ isBusinessConnected, setIsBusinessConnected ] = useState(
		wcSettings.pinterest_for_woocommerce.isBusinessConnected
	);

	useEffect( () => {
		if ( ! isConnected ) {
			setIsBusinessConnected( false );
		}
	}, [ isConnected, setIsBusinessConnected ] );

	const isGroup1Visible = isBusinessConnected;
	const isGroup2Visible = isGroup1Visible && isDomainVerified;

	useBodyClasses();
	useCreateNotice()( wcSettings.pinterest_for_woocommerce.error );

	return (
		<>
			<HealthCheck />
			<NavigationClassic />

			{ appSettings ? (
				<div className="woocommerce-setup-guide__container">
					<SetupAccount
						view={ SETTINGS_VIEW }
						setIsConnected={ setIsConnected }
						isConnected={ isConnected }
						isBusinessConnected={ isBusinessConnected }
					/>

					{ isGroup1Visible && (
						<ClaimWebsite view={ SETTINGS_VIEW } />
					) }
					{ isGroup2Visible && (
						<SetupTracking view={ SETTINGS_VIEW } />
					) }
					{ isGroup2Visible && (
						<SaveSettingsButton view={ SETTINGS_VIEW } />
					) }
				</div>
			) : (
				<Spinner />
			) }
		</>
	);
};

export default SettingsApp;
