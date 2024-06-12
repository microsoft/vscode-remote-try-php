/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Spinner } from '@woocommerce/components';
import {
	Card,
	CardBody,
	CheckboxControl,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import StepOverview from '../components/StepOverview';
import {
	useSettingsSelect,
	useSettingsDispatch,
	useCreateNotice,
} from '../helpers/effects';

const AdvancedSettings = ( { view } ) => {
	const appSettings = useSettingsSelect();
	const setAppSettings = useSettingsDispatch( view === 'wizard' );
	const createNotice = useCreateNotice();

	const handleOptionChange = async ( name, value ) => {
		try {
			await setAppSettings( {
				[ name ]: value ?? ! appSettings[ name ],
			} );
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
		<div className="woocommerce-setup-guide__setup-product-sync">
			<div className="woocommerce-setup-guide__step-columns">
				<div className="woocommerce-setup-guide__step-column">
					<StepOverview
						title={ __(
							'Advanced Settings',
							'pinterest-for-woocommerce'
						) }
					/>
				</div>
				<div className="woocommerce-setup-guide__step-column">
					<Card>
						<CardBody size="large">
							{ undefined !== appSettings &&
							Object.keys( appSettings ).length > 0 ? (
								<>
									<Text
										className="woocommerce-setup-guide__checkbox-heading"
										variant="subtitle"
									>
										{ __(
											'Debug Logging',
											'pinterest-for-woocommerce'
										) }
									</Text>
									<CheckboxControl
										label={ __(
											'Enable Debug Logging',
											'pinterest-for-woocommerce'
										) }
										checked={
											appSettings.enable_debug_logging
										}
										className="woocommerce-setup-guide__checkbox-group"
										onChange={ () =>
											handleOptionChange(
												'enable_debug_logging'
											)
										}
									/>

									<Text
										className="woocommerce-setup-guide__checkbox-heading"
										variant="subtitle"
									>
										{ __(
											'Plugin Data',
											'pinterest-for-woocommerce'
										) }
									</Text>
									<CheckboxControl
										label={ __(
											'Erase Plugin Data',
											'pinterest-for-woocommerce'
										) }
										checked={
											appSettings.erase_plugin_data
										}
										className="woocommerce-setup-guide__checkbox-group"
										onChange={ () =>
											handleOptionChange(
												'erase_plugin_data'
											)
										}
									/>
								</>
							) : (
								<Spinner />
							) }
						</CardBody>
					</Card>
				</div>
			</div>
		</div>
	);
};

export default AdvancedSettings;
