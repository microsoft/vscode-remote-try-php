import React, { useEffect, useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { useStateValue } from '../../store/store';
import { useForm } from 'react-hook-form';
import Button from '../../components/button/button';
import { whiteLabelEnabled } from '../../utils/functions';
import ICONS from '../../../icons';
import Input from '../onboarding-ai/components/input';
import { ArrowRightIcon } from '@heroicons/react/24/outline';
import apiFetch from '@wordpress/api-fetch';
const { restNonce } = starterTemplates;
import {
	checkRequiredPlugins,
	getDemo,
} from '../../steps/import-site/import-utils';

const LicenseValidation = ( param ) => {
	const {} = useForm( { defaultValues: { 'license-key': '' } } );
	const storedState = useStateValue();
	const [
		{ templateId, currentIndex, validateLicenseStatus, builder },
		dispatch,
	] = storedState;
	const [ alreadyPurchasedClicked, setAlreadyPurchasedClicked ] =
		useState( false );
	const [ processing, setProcessing ] = useState( false );
	const [ licenseKey, setLicenseKey ] = useState( '' );
	useEffect( () => {
		dispatch( {
			type: 'set',
			designStep: 2,
		} );
	}, [] );

	const accessLinkOutput = __(
		`Access this template and all others with Essentials & Business Toolkit package starting at just $79.`,
		'astra-sites'
	);

	const alreadyPurchasedOutput = __(
		`Please enter your licence key.`,
		'astra-sites'
	);

	const getAccessLink = () => {
		window.open( astraSitesVars.cta_links[ builder ] );
	};

	const getwhiteLabelLink = () => {
		if ( astraSitesVars.whiteLabelUrl !== '#' ) {
			window.open( astraSitesVars.whiteLabelUrl );
		}
	};

	const handleClick = ( event ) => {
		event.preventDefault();
		setAlreadyPurchasedClicked( true );
	};

	const validateKey = () => {
		if ( licenseKey === '' ) {
			param.setErrorCB( __( 'Please Enter License Key', 'astra-sites' ) );
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
				param.setErrorCB( response.message );
			}
			setProcessing( false );
		} );
	};
	const processingClass = processing ? 'processing' : '';
	const StoreLink = sprintf(
		//translators: %1$s Opening anchor tag %2$s Closing anchor tag.
		__(
			`If you have already purchased the Essential or Business Toolkit, please install the premium version of the Starter Templates plugin from our %1$sstore%2$s.`,
			'astra-sites'
		),
		`<a href="https://wpastra.com/support/free-support/" target="_blank">`,
		`</a>`
	);

	const SupportTeam = sprintf(
		//translators: %1$s Opening anchor tag %2$s Closing anchor tag.
		__(
			'Need help? feel free to get in touch with our %1$ssupport team%2$s.',
			'astra-sites'
		),
		'<a href="https://wpastra.com/support/free-support/" target="_blank">',
		'</a>'
	);

	return (
		<>
			<div className="flex flex-col p-4 rounded-md border border-solid border-blue-500 gap-3 via-blue-500 bg-background-primary">
				<div className="flex gap-2 flex-col">
					<div className="flex gap-2 items-center">
						<span className="w-5 h-5">{ ICONS.premiumIcon }</span>
						<h4 className="text-base font-semibold text-16 leading-24 tracking-normal text-left">
							{ __( 'Premium Template', 'astra-sites' ) }
						</h4>
					</div>
					<p>
						{ ! alreadyPurchasedClicked ? accessLinkOutput : ' ' }
					</p>
				</div>
				{ alreadyPurchasedClicked && ! validateLicenseStatus && (
					<p>
						{ __(
							'You are currently using the Free version.',
							'astra-sites'
						) }
						<br />
						<span
							dangerouslySetInnerHTML={ { __html: StoreLink } }
						/>
						<br />
						<span
							dangerouslySetInnerHTML={ { __html: SupportTeam } }
						/>
					</p>
				) }

				{ alreadyPurchasedClicked && validateLicenseStatus && (
					<>
						<p>{ alreadyPurchasedOutput }</p>
						<form className="" onSubmit={ validateKey }>
							<div style={ { position: 'relative' } }>
								<Input
									className="w-full"
									inputClassName="pr-10"
									height="12"
									name="license-key"
									placeholder={ __(
										'License key',
										'astra-sites'
									) }
									onChange={ ( e ) => {
										setLicenseKey( e.target.value );
										param.setErrorCB( '' );
									} }
									value={ licenseKey }
								/>
								<button
									type="button"
									className={ `absolute right-0 top-0 h-full p-1 pl-2 flex items-center justify-center cursor-pointer bg-transparent border-0 focus:outline-none ${ processingClass }` }
									onClick={ validateKey }
								>
									{ ! processing ? (
										<ArrowRightIcon className="w-5 h-5" />
									) : (
										ICONS.spinner
									) }
								</button>
							</div>
						</form>
						<div className="text-xs flex gap-6 flex-row">
							<p>
								<a
									href="https://store.brainstormforce.com/login/"
									target="_blank"
									rel="noreferrer"
								>
									{ __( 'Get your key here', 'astra-sites' ) }
								</a>
							</p>
							<p>
								<a
									href="https://wpastra.com/support/free-support/"
									target="_blank"
									rel="noreferrer"
								>
									{ __( 'Need help?', 'astra-sites' ) }
								</a>
							</p>
						</div>
					</>
				) }
				{ ! alreadyPurchasedClicked && (
					<>
						<Button
							className="px-3 py-2 rounded-md gap-2 flex !mt-1"
							onClick={
								whiteLabelEnabled()
									? getwhiteLabelLink
									: getAccessLink
							}
						>
							{ __( 'Get Access', 'astra-sites' ) }
							<ArrowRightIcon className="w-4 h-4 text-zip-dark-theme-heading" />
						</Button>
						<div className="text-center">
							<a
								href="#"
								className="w-fill h-hug"
								onClick={ handleClick }
							>
								{ __( 'Already purchased?', 'astra-sites' ) }
							</a>
						</div>
					</>
				) }
			</div>
		</>
	);
};

export default LicenseValidation;
