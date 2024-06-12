import { Outlet } from '@tanstack/react-router';
import { CheckIcon } from '@heroicons/react/24/outline';
import { twMerge } from 'tailwind-merge';
import { memo, useEffect, useLayoutEffect, Fragment } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { removeQueryArgs } from '@wordpress/url';
import {
	classNames,
	getLocalStorageItem,
	setLocalStorageItem,
} from '../../helpers/index';
import PreviewWebsite from '../../pages/preview';
import { STORE_KEY } from '../../store';
import LimitExceedModal from '../limit-exceeded-modal';
// import GetStarted from './authorize-account';
import ContinueProgressModal from '../continue-progress-modal';
import AiBuilderExitButton from '../ai-builder-exit-button';
import { AnimatePresence } from 'framer-motion';
import { useNavigateSteps, steps, useValidateStep } from '../../router';
import { Toaster } from 'react-hot-toast';

const { logoUrlDark } = aiBuilderVars;

const OnboardingAI = () => {
	const {
		currentStepURL,
		currentStepIndex: currentStep,
		navigateTo,
	} = useNavigateSteps();
	const redirectToStepURL = useValidateStep( currentStepURL );

	const authenticated = aiBuilderVars?.zip_token_exists;

	const { setContinueProgressModal } = useDispatch( STORE_KEY );

	const aiOnboardingDetails = useSelect( ( select ) => {
		const { getOnboardingAI } = select( STORE_KEY );
		return getOnboardingAI();
	} );
	const selectedTemplate = aiOnboardingDetails?.stepData?.selectedTemplate,
		{ loadingNextStep } = aiOnboardingDetails;

	// Redirect to the required step.
	useEffect( () => {
		if ( ! aiBuilderVars.zip_token_exists ) {
			navigateTo( {
				to: '/',
				replace: true,
			} );
			return;
		}
		navigateTo( {
			to: redirectToStepURL,
			replace: true,
		} );
	}, [ currentStep, aiOnboardingDetails ] );

	useEffect( () => {
		if (
			! aiOnboardingDetails?.stepData?.businessType ||
			'' === aiOnboardingDetails?.stepData?.businessType
		) {
			return;
		}
		setLocalStorageItem(
			'ai-builder-onboarding-details',
			aiOnboardingDetails
		);
	}, [ aiOnboardingDetails ] );

	useEffect( () => {
		const savedAiOnboardingDetails = getLocalStorageItem(
			'ai-builder-onboarding-details'
		);
		if (
			savedAiOnboardingDetails?.stepData?.businessType &&
			authenticated
		) {
			setContinueProgressModal( {
				open: true,
			} );
		}
	}, [] );

	const dynamicStepClass = function ( step, stepIndex ) {
		if ( step === stepIndex ) {
			return 'border-zip-dark-theme-heading text-zip-dark-theme-heading border-solid';
		}
		if ( step > stepIndex ) {
			return 'bg-zip-dark-theme-content-background text-zip-app-inactive-icon border-zip-dark-theme-content-background border-solid';
		}
		return 'border-solid border-zip-app-inactive-icon text-zip-app-inactive-icon';
	};

	const dynamicClass = function ( cStep, sIndex ) {
		if ( steps?.[ sIndex ].layoutConfig?.screen === 'done' ) {
			return '';
		}
		if ( cStep === sIndex ) {
			return 'bg-gradient-to-b from-white to-transparent';
		}
		if ( cStep > sIndex ) {
			return 'bg-zip-dark-theme-border';
		}
		return 'bg-gradient-to-b from-gray-700 to-transparent';
	};

	/* useEffect( () => {
		if (
			( typeof aiSitesRemainingCount === 'number' &&
				aiSitesRemainingCount <= 0 ) ||
			( typeof allSitesRemainingCount === 'number' &&
				allSitesRemainingCount <= 0 )
		) {
			// If the user has no remaining sites, show the limit exceeded modal
		}
	}, [] ); */
	const urlParams = new URLSearchParams( window.location.search );
	useLayoutEffect( () => {
		const token = urlParams.get( 'token' );
		if ( token ) {
			const url = removeQueryArgs(
				window.location.href,
				'token',
				'email',
				'action',
				'credit_token'
			);

			window.onbeforeunload = null;
			window.history.replaceState( {}, '', url + '#/' );
		}
	}, [ currentStep, currentStepURL, aiOnboardingDetails ] );

	const getStepIndex = ( value, by = 'path' ) => {
		return steps.findIndex( ( item ) => item[ by ] === value );
	};

	const moveToStep = ( stepURL, stepIndex ) => () => {
		if (
			currentStep === stepIndex ||
			currentStep > getStepIndex( '/features' ) ||
			currentStep < stepIndex ||
			loadingNextStep
		) {
			return;
		}

		navigateTo( {
			to: stepURL,
		} );
	};

	return (
		<>
			<div
				id="spectra-onboarding-ai"
				className={ `font-figtree ${
					steps[ currentStep ]?.layoutConfig?.hideSidebar
						? ''
						: 'grid grid-cols-1 lg:grid-cols-[360px_1fr]'
				} h-screen` }
			>
				{ ! steps[ currentStep ]?.layoutConfig?.hideSidebar && (
					<div className="hidden lg:flex lg:w-full lg:flex-col z-[1] overflow-y-auto">
						<div className="flex flex-col gap-y-5 overflow-y-hidden border-r border-gray-200 bg-zip-dark-theme-bg px-6 relative h-screen">
							<div className="mt-3 flex h-16 shrink-0 items-center relative">
								<img
									className="h-10"
									src={ logoUrlDark }
									alt={ __( 'Build with AI', 'ai-builder' ) }
								/>
								{ /* Close button */ }
								{ /* Do not show on Migration step */ }
								{ getStepIndex( '/done' ) !== currentStep &&
									getStepIndex( '/building-website' ) !==
										currentStep && (
										<div className="absolute top-3 right-0">
											<AiBuilderExitButton />
										</div>
									) }
							</div>
							<nav className="flex flex-col gap-y-1 overflow-y-auto">
								{ steps.map(
									(
										{
											path,
											layoutConfig: {
												name,
												description,
												hideStep,
												stepNumber,
											},
										},
										stepIdx
									) =>
										hideStep ? (
											<Fragment key={ stepIdx } />
										) : (
											<div
												className={ classNames(
													'flex gap-3',
													{
														'cursor-pointer':
															currentStep >
																stepIdx &&
															currentStep <=
																getStepIndex(
																	'/features'
																) &&
															! loadingNextStep,
													}
												) }
												key={ stepIdx }
												onClick={ moveToStep(
													path,
													stepIdx
												) }
											>
												<div
													className={ classNames(
														'flex flex-col gap-y-1 items-center',
														stepIdx ===
															steps.length - 1
															? 'justify-start'
															: 'justify-center'
													) }
												>
													<div
														className={ classNames(
															'rounded-full border text-xs font-semibold flex items-center justify-center w-6 h-6',
															dynamicStepClass(
																currentStep,
																stepIdx
															)
														) }
													>
														{ currentStep >
														stepIdx ? (
															<CheckIcon className="text-white h-3 w-3" />
														) : (
															<span>
																{ stepNumber }
															</span>
														) }
													</div>
													{ steps.length - 1 >
														stepIdx && (
														<div
															className={ classNames(
																'h-8 w-[1px]',
																dynamicClass(
																	currentStep,
																	stepIdx
																)
															) }
														/>
													) }
												</div>
												<div className="flex flex-col gap-y-1 items-start justify-start ">
													<div
														className={ classNames(
															'text-sm font-semibold',
															currentStep >=
																stepIdx
																? 'text-zip-app-inactive-icon'
																: 'text-zip-dark-theme-body',
															currentStep ===
																stepIdx &&
																'text-zip-dark-theme-heading'
														) }
													>
														{ name }
													</div>
													<div
														className={ classNames(
															'text-sm font-normal',
															currentStep >=
																stepIdx
																? 'text-zip-app-inactive-icon'
																: 'text-zip-app-inactive-icon',
															currentStep ===
																stepIdx &&
																'text-zip-dark-theme-body'
														) }
													>
														{ description }
													</div>
												</div>
											</div>
										)
								) }
							</nav>
						</div>
					</div>
				) }
				<main
					id="sp-onboarding-content-wrapper"
					className="flex-1 overflow-x-hidden h-screen bg-zip-app-light-bg"
				>
					<div className="h-full w-full relative flex">
						<div
							className={ twMerge(
								`w-full max-h-full flex flex-col flex-auto items-center`,
								steps[ currentStep ]?.layoutConfig?.hideSidebar
									? ''
									: 'px-5 pt-5 md:px-10 md:pt-10 lg:px-14 lg:pt-12 xl:px-20 xl:pt-12',
								'',
								steps[ currentStep ]?.layoutConfig
									?.contentClassName
							) }
						>
							{ /* Renders page content */ }
							<Outlet />
						</div>
					</div>
				</main>
				<LimitExceedModal />
				<ContinueProgressModal />
			</div>
			<div className="absolute top-0 left-0 z-20">
				<AnimatePresence>
					{ !! selectedTemplate && currentStepURL === '/design' && (
						<PreviewWebsite />
					) }
				</AnimatePresence>
			</div>
			{ /* Toaster container */ }
			<Toaster position="top-right" reverseOrder={ false } gutter={ 8 } />
		</>
	);
};

export default memo( OnboardingAI );
