import { useEffect, useReducer, useState } from '@wordpress/element';
import {
	FunnelIcon,
	HeartIcon,
	PlayCircleIcon,
	SquaresPlusIcon,
	CheckIcon,
	ChatBubbleLeftEllipsisIcon,
	WrenchIcon,
} from '@heroicons/react/24/outline';
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { STORE_KEY } from '../store';
import { classNames } from '../helpers';
import NavigationButtons from '../components/navigation-buttons';
import { useNavigateSteps } from '../router';
import PreBuildConfirmModal from '../components/pre-build-confirm-modal';
import PremiumConfirmModal from '../components/premium-confirm-modal';
import InformPrevErrorModal from '../components/inform-prev-error-modal';

const fetchStatus = {
	fetching: 'fetching',
	fetched: 'fetched',
	error: 'error',
};

const ICON_SET = {
	heart: HeartIcon,
	'squares-plus': SquaresPlusIcon,
	funnel: FunnelIcon,
	'play-circle': PlayCircleIcon,
	'live-chat': ChatBubbleLeftEllipsisIcon,
};

const Features = () => {
	const { nextStep, previousStep } = useNavigateSteps();

	const {
		setSiteFeatures,
		storeSiteFeatures,
		setWebsiteInfoAIStep,
		setLimitExceedModal,
		updateImportAiSiteData,
	} = useDispatch( STORE_KEY );
	const {
		siteFeatures,
		stepsData: {
			businessName,
			selectedImages = [],
			keywords = [],
			businessType,
			businessDetails,
			businessContact,
			selectedTemplate,
			siteLanguage,
			selectedTemplateIsPremium,
			templateList,
		},
		loadingNextStep,
	} = useSelect( ( select ) => {
		const { getSiteFeatures, getAIStepData, getLoadingNextStep } =
			select( STORE_KEY );

		return {
			siteFeatures: getSiteFeatures(),
			stepsData: getAIStepData(),
			loadingNextStep: getLoadingNextStep(),
		};
	}, [] );
	const [ isFetchingStatus, setIsFetchingStatus ] = useState(
		fetchStatus.fetching
	);
	const [ isInProgress, setIsInProgress ] = useState( false );
	const [ preBuildModal, setPreBuildModal ] = useState( {
		open: false,
		skipFeature: false,
	} );
	const [ premiumModal, setPremiumModal ] = useState( false );
	const [ prevErrorAlert, setPrevErrorAlert ] = useReducer(
			( state, action ) => ( {
				...state,
				...action,
			} ),
			{ open: false, error: {}, requestData: {} }
		),
		setPrevErrorAlertOpen = ( value ) =>
			setPrevErrorAlert( { open: value } );
	const selectedTemplateData = templateList.find(
			( item ) => item.uuid === selectedTemplate
		),
		isEcommarceSite = selectedTemplateData?.features?.ecommerce === 'yes';

	const handleClosePreBuildModal = ( value = false ) => {
		setPreBuildModal( ( prev ) => {
			return {
				...prev,
				open: value,
			};
		} );
	};

	const handleClickStartBuilding =
		( skipFeature = false ) =>
		() => {
			if ( isInProgress ) {
				return;
			}

			if (
				aiBuilderVars?.zip_plans?.active_plan?.slug === 'free' &&
				selectedTemplateIsPremium
			) {
				setPremiumModal( true );
				return;
			}

			if ( 'yes' !== aiBuilderVars.firstImportStatus ) {
				handleGenerateContent( skipFeature )();
				return;
			}

			setPreBuildModal( {
				open: true,
				skipFeature,
			} );
		};

	const fetchSiteFeatures = async () => {
		const response = await apiFetch( {
			path: 'zipwp/v1/site-features',
			method: 'GET',
			headers: {
				'X-WP-Nonce': aiBuilderVars.rest_api_nonce,
			},
		} );

		if ( response?.success ) {
			// Store to state.
			storeSiteFeatures( response.data.data );

			// Set status to fetched.
			return setIsFetchingStatus( fetchStatus.fetched );
		}

		setIsFetchingStatus( fetchStatus.error );
	};

	const handleToggleFeature = ( featureId ) => () => {
		setSiteFeatures( featureId );
	};

	const limitExceeded = () => {
		const zipPlans = aiBuilderVars?.zip_plans;
		const sitesRemaining = zipPlans?.plan_data?.remaining;
		const aiSitesRemainingCount = sitesRemaining?.ai_sites_count;
		const allSitesRemainingCount = sitesRemaining?.all_sites_count;

		if (
			( typeof aiSitesRemainingCount === 'number' &&
				aiSitesRemainingCount <= 0 ) ||
			( typeof allSitesRemainingCount === 'number' &&
				allSitesRemainingCount <= 0 )
		) {
			return true;
		}

		return false;
	};

	const createSite = async ( {
		template,
		email,
		description,
		name,
		phone,
		address,
		category,
		imageKeyword,
		socialProfiles,
		language,
		images,
		features,
	} ) =>
		await apiFetch( {
			path: 'zipwp/v1/site',
			method: 'POST',
			data: {
				template,
				business_email: email,
				business_description: description,
				business_name: name,
				business_phone: phone,
				business_address: address,
				business_category: category,
				image_keyword: imageKeyword,
				social_profiles: socialProfiles,
				language,
				images,
				site_features: features,
			},
		} );

	const previousErrors = async () => {
		try {
			const response = await apiFetch( {
				path: 'zipwp/v1/import-error-log',
				method: 'GET',
			} );
			if ( response.success ) {
				const errorData = response.data.data;
				if ( errorData && Object.values( errorData ).length > 0 ) {
					return errorData;
				}
			}

			return {};
		} catch ( error ) {
			return {};
		}
	};

	const handleCreateSiteResponse = async ( requestData ) => {
		if ( isInProgress ) {
			return;
		}
		// Start the process.
		setIsInProgress( true );

		const response = await createSite( requestData );

		if ( response.success ) {
			const websiteData = response.data.data.site;
			// Close the onboarding screen on success.
			setWebsiteInfoAIStep( websiteData );
			updateImportAiSiteData( {
				templateId: websiteData.uuid,
				importErrorMessages: {},
				importErrorResponse: [],
				importError: false,
			} );
			nextStep();
		} else {
			// Handle error.
			const message = response?.data?.data;
			if (
				typeof message === 'string' &&
				message.includes( 'Usage limit' )
			) {
				setLimitExceedModal( {
					open: true,
				} );
			}
			setIsInProgress( false );
		}
	};

	const handleGenerateContent =
		( skip = false ) =>
		async () => {
			if ( isInProgress ) {
				return;
			}

			if ( limitExceeded() ) {
				setLimitExceedModal( {
					open: true,
				} );
				return;
			}

			const enabledFeatures = skip
				? []
				: siteFeatures
						.filter( ( feature ) => feature.enabled )
						.map( ( feature ) => feature.id );

			// Add ecommerce feature if selected template is ecommerce.
			if ( isEcommarceSite ) {
				enabledFeatures.push( 'ecommerce' );
			}

			const requestData = {
				template: selectedTemplate,
				email: businessContact?.email,
				description: businessDetails,
				name: businessName,
				phone: businessContact?.phone,
				address: businessContact?.address,
				category: businessType,
				imageKeyword: keywords,
				socialProfiles: businessContact?.socialMedia,
				language: siteLanguage,
				images: selectedImages,
				features: enabledFeatures,
			};

			const previousError = await previousErrors();
			if ( previousError && Object.values( previousError ).length > 0 ) {
				setPrevErrorAlert( {
					open: true,
					error: previousError,
					requestData,
				} );
				return;
			}

			await handleCreateSiteResponse( requestData );
		};

	const onConfirmErrorAlert = async () => {
		setPrevErrorAlert( { open: false, error: {}, requestData: {} } );
		await handleCreateSiteResponse( prevErrorAlert.requestData );
	};

	useEffect( () => {
		if ( isFetchingStatus === fetchStatus.fetching ) {
			fetchSiteFeatures();
		}
	}, [] );

	return (
		<div className="grid grid-cols-1 gap-8 auto-rows-auto px-10 pb-10 pt-12 max-w-[880px] w-full mx-auto">
			<div className="space-y-4">
				<h1 className="text-3xl font-bold text-zip-app-heading">
					{ __( 'Select features', 'ai-builder' ) }
				</h1>
				<p className="m-0 p-0 text-base font-normal text-zip-body-text">
					{ __(
						'Select the features you want on this website',
						'ai-builder'
					) }
				</p>
			</div>

			{ /* Feature Cards */ }
			<div className="grid grid-cols-1 lg:grid-cols-2 auto-rows-auto gap-x-8 gap-y-5 w-full">
				{ isFetchingStatus === fetchStatus.fetched &&
					siteFeatures.map( ( feature ) => {
						const FeatureIcon = ICON_SET?.[ feature.icon ];
						return (
							<div
								key={ feature.id }
								className={ classNames(
									'relative py-4 pl-4 pr-5 rounded-md shadow-sm border border-solid bg-white border-transparent transition-colors duration-150 ease-in-out',
									feature.enabled && 'border-accent-st'
								) }
								data-disabled={ loadingNextStep }
							>
								<div className="flex items-start justify-start gap-3">
									<div className="p-0.5 shrink-0">
										{ FeatureIcon && (
											<FeatureIcon className="text-zip-body-text w-7 h-7" />
										) }
										{ ! FeatureIcon && (
											<WrenchIcon className="text-zip-body-text w-7 h-7" />
										) }
									</div>
									<div className="space-y-1 mr-5">
										<p className="p-0 m-0 !text-base !font-semibold !text-zip-app-heading">
											{ feature.title }
										</p>
										<p className="p-0 m-0 !text-sm !font-normal !text-zip-body-text">
											{ feature.description }
										</p>
									</div>
								</div>
								{ /* Check mark */ }
								<span
									className={ classNames(
										'inline-flex absolute top-4 right-4 p-[0.1875rem] border border-solid border-zip-app-inactive-icon rounded',
										feature.enabled &&
											'border-accent-st bg-accent-st'
									) }
								>
									<CheckIcon
										className="w-2.5 h-2.5 text-white"
										strokeWidth={ 4 }
									/>
								</span>
								{ /* Click handler overlay */ }
								<div
									className="absolute inset-0 cursor-pointer"
									onClick={ handleToggleFeature(
										feature.id
									) }
								/>
							</div>
						);
					} ) }
				{ /* Skeleton */ }
				{ isFetchingStatus === fetchStatus.fetching &&
					Array.from( {
						length: Object.keys( ICON_SET ).length,
					} ).map( ( _, index ) => (
						<div
							key={ index }
							className="relative py-4 pl-4 pr-5 rounded-md shadow-sm border border-solid bg-white border-transparent"
						>
							<div className="flex items-start justify-start gap-3">
								<div className="p-0.5 shrink-0">
									<div className="w-7 h-7 bg-gray-200 rounded animate-pulse" />
								</div>
								<div className="space-y-1 w-full">
									<div className="w-3/4 h-6 bg-gray-200 rounded animate-pulse" />
									<div className="w-1/2 h-5 bg-gray-200 rounded animate-pulse" />
								</div>
							</div>
							<span className="inline-flex absolute top-4 right-4 w-4 h-4 bg-gray-200 animate-pulse rounded" />
							<div className="absolute inset-0 cursor-pointer" />
						</div>
					) ) }
			</div>
			{ /* Error Message */ }
			{ isFetchingStatus === fetchStatus.error && (
				<div className="flex items-center justify-center w-full px-5 py-5">
					<p className="text-secondary-text text-center px-10 py-5 border-2 border-dashed border-border-primary rounded-md">
						{ __(
							'Something went wrong. Please try again later.',
							'ai-builder'
						) }
					</p>
				</div>
			) }

			<hr className="!border-border-tertiary border-b-0 w-full" />

			{ /* Navigation buttons */ }
			<NavigationButtons
				continueButtonText={ __( 'Start Building', 'ai-builder' ) }
				onClickPrevious={ previousStep }
				onClickContinue={ handleClickStartBuilding() }
				onClickSkip={ handleClickStartBuilding( true ) }
				loading={ isInProgress }
				skipButtonText={ __( 'Skip & Start Building', 'ai-builder' ) }
			/>
			<PreBuildConfirmModal
				open={ preBuildModal.open }
				setOpen={ handleClosePreBuildModal }
				startBuilding={ handleGenerateContent(
					preBuildModal.skipFeature
				) }
			/>
			<PremiumConfirmModal
				open={ premiumModal }
				setOpen={ setPremiumModal }
			/>
			<InformPrevErrorModal
				open={ prevErrorAlert.open }
				setOpen={ setPrevErrorAlertOpen }
				onConfirm={ onConfirmErrorAlert }
				errorString={ JSON.stringify( prevErrorAlert.error ) }
			/>
		</div>
	);
};

export default Features;
