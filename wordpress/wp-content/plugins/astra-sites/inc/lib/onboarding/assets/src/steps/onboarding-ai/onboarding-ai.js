import { CheckIcon } from '@heroicons/react/24/outline';
import { twMerge } from 'tailwind-merge';
import { useLocation, useNavigate } from 'react-router-dom';
import {
	memo,
	useEffect,
	useLayoutEffect,
	useRef,
	useCallback,
} from '@wordpress/element';
import { withSelect, withDispatch, useSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import { addQueryArgs, removeQueryArgs } from '@wordpress/url';
import { useStateValue } from '../../store/store';
import { saveGutenbergAsDefaultBuilder } from '../../utils/functions';
import { classNames, setLocalStorageItem } from './helpers/index';
import DescribeBusiness from './describe-business';
import BusinessContact from './business-contact';
import Images from './images';
import SelectTemplate from './select-template';
import WebsiteBuilding from './building-website';
import BuildDone from './done';
import ImportAiSIte from './import-ai-site';
import PreviewWebsite from './preview';
import { STORE_KEY } from './store';
import LimitExceedModal from './components/limit-exceeded-modal';
import GetStarted from './get-started-step-ai';
import ContinueProgressModal from './components/continue-progress-modal';
import AiBuilderExitButton from './components/ai-builder-exit-button';
import Features from './features';
import { Fragment } from 'react';
import { AnimatePresence } from 'framer-motion';
import BusinessDetails from './business-details';

const { logoUrl } = starterTemplates;

const steps = [
	{
		component: <GetStarted />,
		hideSidebar: true,
		hideCloseIcon: true,
		hideStep: true,
		hideCredits: true,
	},
	{
		stepNumber: 1,
		name: __( "Let's Start", 'astra-sites' ),
		description: __( 'Name, language & type', 'astra-sites' ),
		screen: 'type',
		component: <BusinessDetails />,
		hideCredits: false,
	},
	{
		stepNumber: 2,
		name: __( 'Describe', 'astra-sites' ),
		description: __( 'Some details please', 'astra-sites' ),
		screen: 'details',
		component: <DescribeBusiness />,
		hideCredits: false,
	},
	{
		stepNumber: 3,
		name: __( 'Contact', 'astra-sites' ),
		description: __( 'How can people get in touch', 'astra-sites' ),
		screen: 'contact-details',
		component: <BusinessContact />,
		hideCredits: false,
	},
	{
		stepNumber: 4,
		name: __( 'Select Images', 'astra-sites' ),
		description: __( 'Select relevant images as needed', 'astra-sites' ),
		screen: 'images',
		contentClassName:
			'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',
		component: <Images />,
		hideCredits: false,
	},
	{
		stepNumber: 5,
		name: __( 'Design', 'astra-sites' ),
		description: __( 'Choose a structure for your website', 'astra-sites' ),
		screen: 'template',
		contentClassName:
			'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',

		component: <SelectTemplate />,
		hideCredits: false,
	},
	{
		stepNumber: 6,
		name: __( 'Features', 'astra-sites' ),
		description: __( 'Select features as you need', 'astra-sites' ),
		screen: 'features',
		contentClassName:
			'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',
		component: <Features />,
		hideCredits: false,
	},
	{
		stepNumber: 7,
		name: __( 'Configure', 'astra-sites' ),
		description: __( 'Personalize your website', 'astra-sites' ),
		screen: 'building-website',
		hideCloseIcon: true,
		component: <WebsiteBuilding />,
		hideStep: true,
		hideCredits: true,
	},
	{
		stepNumber: 8,
		name: __( 'Done', 'astra-sites' ),
		description: __( 'Your website is ready!', 'astra-sites' ),
		screen: 'migration',
		component: <ImportAiSIte />,
		hideStep: true,
		hideCredits: true,
	},
	{
		name: __( 'Done', 'astra-sites' ),
		description: __(
			'Congratulations! Your website is ready!',
			'astra-sites'
		),
		screen: 'done',
		contentClassName: 'pt-0 md:pt-0 lg:pt-0 xl:pt-0',
		component: <BuildDone />,
		hideStep: true,
		hideCredits: true,
	},
];
export const TOTAL_STEPS = steps.length;
const zipPlans = astraSitesVars?.zip_plans;
const sitesRemaining = zipPlans?.plan_data?.remaining;
const aiSitesRemainingCount = sitesRemaining?.ai_sites_count;
const allSitesRemainingCount = sitesRemaining?.all_sites_count;

const OnboardingAI = ( {
	togglePopup,
	currentScreen,
	sitePreview,
	currentStep,
	setAIStep,
} ) => {
	const [ { currentIndex, builder }, dispatch ] = useStateValue();
	const urlParams = new URLSearchParams( window.location.search );
	const authenticated = astraSitesVars?.zip_token_exists;

	const { continueProgressModal } = useSelect( ( select ) => {
		const { getContinueProgressModalInfo } = select( STORE_KEY );
		return {
			continueProgressModal: getContinueProgressModalInfo(),
		};
	} );

	const aiOnboardingDetails = useSelect( ( select ) => {
		const { getOnboardingAI } = select( STORE_KEY );
		return getOnboardingAI();
	} );
	const selectedTemplate = aiOnboardingDetails?.stepData?.selectedTemplate,
		{ loadingNextStep } = aiOnboardingDetails;
	const routerHistory = useNavigate();
	const location = useLocation();
	const prevStepRef = useRef( currentStep );

	useEffect( () => {
		if (
			! aiOnboardingDetails?.stepData?.businessType ||
			'' === aiOnboardingDetails?.stepData?.businessType
		) {
			return;
		}
		setLocalStorageItem( 'ai-onboarding-details', aiOnboardingDetails );
	}, [ aiOnboardingDetails ] );

	const updateRoute = ( step ) => {
		if ( ! step ) {
			urlParams.delete( 'ai' );
			urlParams.delete( 'ci' );
		} else {
			urlParams.set( 'ai', step );
		}
		routerHistory(
			`${ window.location.pathname }?${ urlParams.toString() }`
		);
	};

	const createSiteStep = steps.length - 2;
	const migrateSiteStep = steps.length - 1;

	useEffect( () => {
		const aiStep = +urlParams.get( 'ai' );

		if ( continueProgressModal?.open ) {
			return;
		}

		if ( ! aiStep || isNaN( aiStep ) ) {
			updateRoute( currentStep );
			return;
		}

		if ( aiStep === 1 && authenticated ) {
			dispatch( {
				type: 'set',
				currentIndex: 0,
			} );
		}

		if ( ! authenticated && aiStep !== 1 ) {
			setAIStep( 1 );
			return;
		}

		// If currentStep is Create Site or Migrate Site, don't change the step.
		if (
			currentStep === createSiteStep ||
			currentStep === migrateSiteStep
		) {
			return;
		}

		if ( aiStep !== currentStep ) {
			setAIStep( aiStep );
		}
	}, [ location ] );

	useEffect( () => {
		const aiStep = +urlParams.get( 'ai' );

		if ( continueProgressModal?.open ) {
			return;
		}

		if ( currentStep === aiStep ) {
			return;
		}

		// If currentStep hasn't changed, don't update the URL
		if (
			aiStep &&
			! isNaN( aiStep ) &&
			currentStep === prevStepRef.current
		) {
			return;
		}
		prevStepRef.current = currentStep;
		updateRoute( currentStep );
	}, [ currentStep, continueProgressModal?.open ] );

	const handleBackToBuilderType = useCallback(
		( event ) => {
			const {
				target: { location: oldLocation },
			} = event;
			const oldUrlParams = new URLSearchParams( oldLocation.search );

			if (
				currentStep === createSiteStep ||
				currentStep === migrateSiteStep
			) {
				return;
			}

			const aiStep = +oldUrlParams.get( 'ai' );
			const ciValue = +oldUrlParams.get( 'ci' );

			if ( ! ciValue || ! aiStep ) {
				oldUrlParams.delete( 'ai' );
				oldUrlParams.delete( 'ci' );
				dispatch( {
					type: 'set',
					builder: 'gutenberg',
					currentIndex: 0,
				} );
				saveGutenbergAsDefaultBuilder();
			}
		},
		[ currentStep ]
	);

	useLayoutEffect( () => {
		window.addEventListener( 'popstate', handleBackToBuilderType );

		return () => {
			window.removeEventListener( 'popstate', handleBackToBuilderType );
		};
	}, [ handleBackToBuilderType ] );

	useEffect( () => {
		if ( togglePopup ) {
			document.body.classList.add( 'ast-block-templates-modal-open' );
			document
				.getElementById( 'ast-block-templates-modal-wrap' )
				.classList.add( 'open' );
		} else {
			document.body.classList.remove( 'ast-block-templates-modal-open' );
			document
				.getElementById( 'ast-block-templates-modal-wrap' )
				?.classList.remove( 'open' );
		}
	}, [ togglePopup, currentScreen, sitePreview ] );

	const dynamicStepClass = function ( step, stepIndex ) {
		if ( step === stepIndex + 1 ) {
			return 'border-zip-dark-theme-heading text-zip-dark-theme-heading border-solid';
		}
		if ( step > stepIndex + 1 ) {
			return 'bg-zip-dark-theme-content-background text-zip-app-inactive-icon';
		}
		return 'border-solid border-zip-app-inactive-icon text-zip-app-inactive-icon';
	};

	const dynamicClass = function ( cStep, sIndex ) {
		if ( steps?.[ sIndex ]?.screen === 'features' ) {
			return '';
		}
		if ( cStep === sIndex + 1 ) {
			return 'bg-gradient-to-b from-white to-transparent';
		}
		if ( cStep > sIndex + 1 ) {
			return 'bg-zip-dark-theme-border';
		}
		return 'bg-gradient-to-b from-gray-700 to-transparent';
	};

	useEffect( () => {
		const token = urlParams.get( 'token' );
		if ( token ) {
			let url = removeQueryArgs(
				window.location.href,
				'token',
				'email',
				'action',
				'credit_token'
			);
			url = addQueryArgs( url, { ci: currentIndex } );

			window.onbeforeunload = null;
			window.history.replaceState( {}, '', url );
		}
	}, [] );

	useEffect( () => {
		if (
			( typeof aiSitesRemainingCount === 'number' &&
				aiSitesRemainingCount <= 0 ) ||
			( typeof allSitesRemainingCount === 'number' &&
				allSitesRemainingCount <= 0 )
		) {
			if ( 'ai-builder' === builder ) {
				dispatch( {
					type: 'set',
					builder: 'gutenberg',
				} );

				const content = new FormData();
				content.append( 'action', 'astra-sites-change-page-builder' );
				content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
				content.append( 'page_builder', 'gutenberg' );

				fetch( ajaxurl, {
					method: 'post',
					body: content,
				} );
			}
			return dispatch( {
				type: 'set',
				currentIndex: 0,
			} );
		}
	}, [] );

	const getStepIndex = ( value, by = 'screen' ) => {
		return steps.findIndex( ( item ) => item[ by ] === value ) + 1;
	};
	const handleStepClick = ( stepIndex ) => {
		if ( loadingNextStep ) {
			return;
		}
		if ( stepIndex <= currentStep ) {
			// Update the current step state
			setAIStep( stepIndex );

			// Update the URL to reflect the new step
			urlParams.set( 'ai', stepIndex );
			routerHistory(
				`${ window.location.pathname }?${ urlParams.toString() }`
			);
		}
	};

	return (
		<>
			<div
				id="spectra-onboarding-ai"
				className={ `font-figtree ${
					steps[ currentStep - 1 ]?.hideSidebar
						? ''
						: 'grid grid-cols-1 lg:grid-cols-[360px_1fr]'
				} h-screen` }
			>
				{ ! steps[ currentStep - 1 ]?.hideSidebar && (
					<div className="hidden lg:flex lg:w-full lg:flex-col z-[1] overflow-y-auto">
						<div className="flex flex-col gap-y-5 overflow-y-hidden border-r border-gray-200 bg-zip-dark-theme-bg px-6 relative h-screen">
							<div className="mt-3 flex h-16 shrink-0 items-center relative">
								<img
									className="w-10 h-10"
									src={ logoUrl }
									alt={ __( 'Build with AI', 'astra-sites' ) }
								/>
								{ /* Close button */ }
								{ /* Do not show on Site Creation & Migration step */ }
								{ getStepIndex( 'migration' ) !== currentStep &&
									getStepIndex( 'building-website' ) !==
										currentStep &&
									getStepIndex( 'done' ) !== currentStep && (
										<div className="absolute top-3 right-0">
											<AiBuilderExitButton />
										</div>
									) }
							</div>
							<nav className="flex flex-col gap-y-1 overflow-y-auto">
								{ steps.map(
									(
										{
											name,
											description,
											hideStep,
											stepNumber,
										},
										stepIdx
									) =>
										hideStep ? (
											<Fragment key={ stepIdx } />
										) : (
											<div
												className={ classNames(
													'flex gap-3',
													stepIdx < currentStep &&
														! loadingNextStep
														? 'cursor-pointer'
														: 'cursor-default'
												) }
												key={ stepIdx }
												onClick={ () =>
													handleStepClick(
														stepIdx + 1
													)
												} // Set cursor based on navigability
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
														stepIdx + 1 ? (
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
																stepIdx + 1
																? 'text-zip-app-inactive-icon'
																: 'text-zip-dark-theme-body',
															currentStep ===
																stepIdx + 1 &&
																'text-zip-dark-theme-heading'
														) }
													>
														{ name }
													</div>
													<div
														className={ classNames(
															'text-sm font-normal',
															currentStep >=
																stepIdx + 1
																? 'text-zip-app-inactive-icon'
																: 'text-zip-app-inactive-icon',
															currentStep ===
																stepIdx + 1 &&
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
								steps[ currentStep - 1 ]?.hideSidebar
									? ''
									: 'px-5 pt-5 md:px-10 md:pt-10 lg:px-14 lg:pt-12 xl:px-20 xl:pt-12',
								'',
								steps[ currentStep - 1 ]?.contentClassName
							) }
						>
							{ steps[ currentStep - 1 ]?.component }
						</div>
					</div>
				</main>
				<LimitExceedModal />
				<ContinueProgressModal />
			</div>
			<div className="absolute top-0 left-0 z-20">
				<AnimatePresence>
					{ !! selectedTemplate &&
						currentStep ===
							steps.findIndex(
								( item ) => item.name === 'Design'
							) +
								1 && <PreviewWebsite /> }
				</AnimatePresence>
			</div>
		</>
	);
};

export default compose(
	withSelect( ( select ) => {
		const {
			getTogglePopup,
			getSitePreview,
			getCurrentScreen,
			setCurrentScreen,
			getCurrentAIStep,
		} = select( 'ast-block-templates' );
		return {
			togglePopup: getTogglePopup(),
			sitePreview: getSitePreview(),
			currentScreen: getCurrentScreen(),
			setCurrentScreen,
			currentStep: getCurrentAIStep(),
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { toggleOnboardingAIStep, setAIStep } = dispatch(
			'ast-block-templates'
		);
		return {
			toggleOnboardingAIStep,
			setAIStep,
		};
	} )
)( memo( OnboardingAI ) );
