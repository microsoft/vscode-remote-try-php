/**
 * External dependencies
 */
import '@wordpress/notices';
import { Spinner } from '@woocommerce/components';

/**
 * Internal dependencies
 */
import SyncSettings from '../components/SyncSettings';
import SetupProductSync from '../steps/SetupProductSync';
import SetupPins from '../steps/SetupPins';
import AdvancedSettings from '../steps/AdvancedSettings';
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

	useBodyClasses();
	useCreateNotice()( wcSettings.pinterest_for_woocommerce.error );

	return (
		<>
			<HealthCheck />
			<NavigationClassic />

			{ appSettings ? (
				<div className="woocommerce-setup-guide__container">
					<>
						<SyncSettings view={ SETTINGS_VIEW } />
						<SetupProductSync view={ SETTINGS_VIEW } />
						<SetupPins view={ SETTINGS_VIEW } />
						<AdvancedSettings view={ SETTINGS_VIEW } />
						<SaveSettingsButton />
					</>
				</div>
			) : (
				<Spinner />
			) }
		</>
	);
};

export default SettingsApp;
