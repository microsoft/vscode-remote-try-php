import React, { useEffect, useState } from 'react';
import { Toaster } from '@brainstormforce/starter-templates-components';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import Button from '../../../../components/button/button';
import { useStateValue } from '../../../../store/store';
import PreviousStepLink from '../../../../components/util/previous-step-link/index';
import ICONS from '../../../../../icons';
import { whiteLabelEnabled } from '../../../../utils/functions';
import {
	checkRequiredPlugins,
	getDemo,
} from '../../../import-site/import-utils';
const { restNonce } = starterTemplates;

const LicenseValidationControls = () => {
	const storedState = useStateValue();
	const [
		{
			templateId,
			templateResponse,
			currentCustomizeIndex,
			importError,
			currentIndex,
			validateLicenseStatus,
		},
		dispatch,
	] = storedState;
	const [ error, setError ] = useState( '' );
	const [ processing, setProcessing ] = useState( false );
	const [ licenseKey, setLicenseKey ] = useState( '' );
	const premiumTemplate = false;

	// Start the pre import process.
	useEffect( () => {
		if ( importError ) {
			dispatch( {
				type: 'set',
				currentIndex: currentIndex + 2, // Skip 2 steps.
			} );
		}
	}, [ importError ] );

	const validateKey = () => {
		if ( licenseKey === '' ) {
			setError( __( 'Please Enter License Key', 'astra-sites' ) );
			return;
		}

		setProcessing( true );

		apiFetch.use( apiFetch.createNonceMiddleware( restNonce ) );
		apiFetch( {
			path: '/bsf-core/v1/license/activate',
			method: 'POST',
			data: {
				'license-key': licenseKey,
				'product-id': 'astra-pro-sites',
			},
		} ).then( async ( response ) => {
			if ( response.success ) {
				await getDemo( templateId, storedState );
				await checkRequiredPlugins( storedState );
				dispatch( {
					type: 'set',
					licenseStatus: true,
					currentIndex: currentIndex + 1,
				} );
			} else {
				setError( response.message );
			}
			setProcessing( false );
		} );
	};

	if (
		premiumTemplate &&
		'' !== astraSitesVars.license_page_builder &&
		templateResponse[ 'astra-site-page-builder' ] !==
			astraSitesVars.license_page_builder &&
		'brizy' !== templateResponse[ 'astra-site-page-builder' ] &&
		'gutenberg' !== templateResponse[ 'astra-site-page-builder' ]
	) {
		return <p>{ __( 'Not Valid License', 'astra-sites' ) }</p>;
	}

	const processingClass = processing ? 'processing' : '';

	const supportLink = sprintf(
		//translators: %1$s Support page URL.
		__(
			`<b> Questions? </b> Get in touch with our %1$ssupport team%2$s.`,
			'astra-sites'
		),
		`<a href="https://wpastra.com/support/free-support/" target="_blank">`,
		'</a>'
	);

	const lastStep = () => {
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex - 1,
		} );
	};

	const downloadLink = sprintf(
		//translators: %1$s Store page URL.
		__(
			`If you have purchased our Essential or Business Toolkits, please install the premium version of the plugin that you can %1$sdownload%2$s from our store.`,
			'astra-sites'
		),
		'<a href="https://store.brainstormforce.com/login/" target="_blank">',
		'</a>'
	);

	return (
		<>
			{ ! whiteLabelEnabled() && (
				<>
					<h4>{ __( 'Already a customer?', 'astra-sites' ) }</h4>

					{ validateLicenseStatus && (
						<p className="customer-notices">
							{ __(
								'If you have purchased our Essential or Business Toolkits, just enter your license key below to import this template.',
								'astra-sites'
							) }
						</p>
					) }

					{ ! validateLicenseStatus && (
						<>
							<p
								className="customer-notices"
								dangerouslySetInnerHTML={ {
									__html: downloadLink,
								} }
							/>
							<p className="customer-notices">
								{ __(
									'Currently the free version is installed.',
									'astra-sites'
								) }
							</p>
						</>
					) }
					<p
						className="support-link"
						dangerouslySetInnerHTML={ { __html: supportLink } }
					/>
					{ validateLicenseStatus && (
						<div className="license-wrap">
							<input
								type="text"
								className="license-key-input"
								name="license-key"
								placeholder={ __(
									'License key',
									'astra-sites'
								) }
								required
								onChange={ ( e ) => {
									setLicenseKey( e.target.value );
									setError( '' );
								} }
							/>
							<Button
								className={ `validate-btn ${ processingClass }` }
								onClick={ validateKey }
							>
								{ ! processing
									? ICONS.arrowRightBold
									: ICONS.spinner }
							</Button>
						</div>
					) }
				</>
			) }
			<PreviousStepLink onClick={ lastStep } customizeStep={ true }>
				{ __( 'Back', 'astra-sites' ) }
			</PreviousStepLink>

			{ error && ! processing && (
				<Toaster
					type="error"
					message={ error }
					autoHideDuration={ 5 }
				/>
			) }
		</>
	);
};

export default LicenseValidationControls;
