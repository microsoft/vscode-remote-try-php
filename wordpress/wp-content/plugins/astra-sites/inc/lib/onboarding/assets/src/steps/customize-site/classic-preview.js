import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../store/store';
import ChangeTemplate from '../../components/change-template';
import LicenseValidation from './license-validation';
import SiteLogo from './site-logo';
import ColorPalettes from './color-palettes';
import FontSelector from './font-selector';
import Button from '../../components/button/button';
import PreviousStepLink from '../../components/util/previous-step-link/index';
import { ChevronRightIcon } from '@heroicons/react/24/outline';
import { Toaster } from '@brainstormforce/starter-templates-components';

const ClassicPreview = () => {
	const [
		{
			currentCustomizeIndex,
			templateResponse,
			licenseStatus,
			selectedTemplateType,
			templateId,
			currentIndex,
			builder,
		},
		dispatch,
	] = useStateValue();

	const [ error, setError ] = useState( '' );
	const setErrorCallback = ( message ) => {
		setError( message );
	};
	useEffect( () => {
		const premiumTemplate =
			'free' !== templateResponse?.[ 'astra-site-type' ];

		if ( premiumTemplate && ! licenseStatus ) {
			if ( astraSitesVars.isPro ) {
				dispatch( {
					type: 'set',
					validateLicenseStatus: true,
				} );
			}
		}
	}, [ templateResponse ] );

	const lastStep = () => {
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex - 1,
			currentIndex: currentIndex - 1,
		} );
	};

	const setNextStep = () => {
		dispatch( {
			type: 'set',
			currentIndex: currentIndex + 1,
		} );
	};

	const preventRefresh = ( event ) => {
		event.returnValue = __(
			'Are you sure you want to cancel the site import process?',
			'astra-sites'
		);
		return event;
	};

	useEffect( () => {
		window.addEventListener( 'beforeunload', preventRefresh ); // eslint-disable-line
		return () =>
			window.removeEventListener( 'beforeunload', preventRefresh ); // eslint-disable-line
	} );
	return (
		<>
			<ChangeTemplate />
			<div className="flex flex-col gap-2 text-sm px-6 font-normal leading-5 mt-3">
				<h5>{ __( 'Customize', 'astra-sites' ) }</h5>
				<p className="!text-zip-app-inactive-icon">
					{ __(
						'Add your own logo, change color and font.',
						'astra-sites'
					) }
				</p>
			</div>
			<div className="st-preview-section px-6 mb-5 w-full">
				<SiteLogo />
				{ templateResponse ? (
					<>
						<FontSelector />
						{ builder !== 'beaver-builder' && <ColorPalettes /> }
					</>
				) : (
					<div className="space-y-5 mt-5">
						<div
							data-placeholder
							className="relative animate-pulse overflow-hidden bg-gray-300 h-[50px] w-full rounded-md"
						/>
						{ builder !== 'beaver-builder' && (
							<div
								data-placeholder
								className="relative animate-pulse overflow-hidden bg-gray-300 h-[50px] w-full rounded-md"
							/>
						) }
					</div>
				) }
			</div>
			<div className="w-full flex flex-col gap-4 mt-auto px-6">
				{ ! licenseStatus && 'free' !== selectedTemplateType && (
					<LicenseValidation setErrorCB={ setErrorCallback } />
				) }
				<div className="flex flex-col gap-2 mt-2">
					{ ( ( licenseStatus && selectedTemplateType !== 'free' ) ||
						selectedTemplateType === 'free' ) && (
						<Button
							className={ `w-full flex gap-2 items-center ${
								( ! licenseStatus &&
									selectedTemplateType !== 'free' ) ||
								( templateId === 0 &&
									selectedTemplateType === 'free' )
									? '!bg-border-tertiary !text-zip-app-inactive-icon'
									: ''
							}` }
							onClick={ setNextStep }
							disabled={
								( ! licenseStatus &&
									selectedTemplateType !== 'free' ) ||
								( templateId === 0 &&
									selectedTemplateType === 'free' )
							}
						>
							<span>{ __( 'Continue', 'astra-sites' ) }</span>
							<ChevronRightIcon className="w-4 h-4 !fill-none" />
						</Button>
					) }
					<PreviousStepLink
						className="w-full"
						onClick={ lastStep }
						before
						customizeStep={ true }
					>
						{ __( 'Back', 'astra-sites' ) }
					</PreviousStepLink>
				</div>
			</div>
			{ error !== '' && (
				<Toaster
					type="error"
					message={ error }
					autoHideDuration={ 5 }
					className="flex relative"
				/>
			) }
		</>
	);
};

export default ClassicPreview;
