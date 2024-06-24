import { useEffect, useState } from 'react';
import {
	FunnelIcon,
	HeartIcon,
	PlayCircleIcon,
	SquaresPlusIcon,
	CheckIcon,
	ChatBubbleLeftEllipsisIcon,
	WrenchIcon,
} from '@heroicons/react/24/outline';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { STORE_KEY } from './store';
import { classNames } from './helpers';
import NavigationButtons from './navigation-buttons';
import { useStateValue } from '../../store/store';
import { limitExceeded, setToSessionStorage } from './utils/helpers';

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
	const [ , dispatch ] = useStateValue();
	const {
		setSiteFeatures,
		storeSiteFeatures,
		setNextAIStep,
		setPreviousAIStep,
		setWebsiteInfoAIStep,
		setLimitExceedModal,
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
	const selectedTemplateItem = templateList?.find(
		( item ) => item?.uuid === selectedTemplate
	);

	const fetchSiteFeatures = async () => {
		const response = await apiFetch( {
			path: 'zipwp/v1/site-features',
			method: 'GET',
			headers: {
				'X-WP-Nonce': astraSitesVars.rest_api_nonce,
			},
		} );

		if ( response?.success ) {
			// Store to state.
			storeSiteFeatures( response.data.data );

			// Chemark based on template features settings.
			const featuresMapping = {
				blog_enabled: 'blog',
				donation_enabled: 'donations',
				store_enabled: 'sales-funnels',
			};

			Object.entries( featuresMapping ).forEach(
				( [ featureKey, featureId ] ) => {
					if (
						selectedTemplateItem?.features?.[ featureKey ] === 'yes'
					) {
						const featureIndex = siteFeatures.findIndex(
							( item ) => item.id === featureId
						);
						if ( featureIndex !== -1 ) {
							setSiteFeatures( featureId );
						}
					}
				}
			);

			// Set status to fetched.
			return setIsFetchingStatus( fetchStatus.fetched );
		}

		setIsFetchingStatus( fetchStatus.error );
	};

	const handleToggleFeature = ( featureId ) => () => {
		setSiteFeatures( featureId );
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
			setIsInProgress( true );

			const formData = new window.FormData();

			formData.append( 'action', 'ast-block-templates-ai-content' );
			formData.append( 'security', astraSitesVars.ai_content_ajax_nonce );
			formData.append( 'business_name', businessName );
			formData.append( 'business_desc', businessDetails );
			formData.append( 'business_category', businessType );
			formData.append( 'images', JSON.stringify( selectedImages ) );
			formData.append( 'image_keywords', JSON.stringify( keywords ) );
			formData.append(
				'business_address',
				businessContact?.address || ''
			);
			formData.append( 'business_phone', businessContact?.phone || '' );
			formData.append( 'business_email', businessContact?.email || '' );
			formData.append(
				'social_profiles',
				JSON.stringify( businessContact?.socialMedia || [] )
			);

			const createSitePayload = {
				template: selectedTemplate,
				business_email: businessContact?.email,
				business_description: businessDetails,
				business_name: businessName,
				business_phone: businessContact?.phone,
				business_address: businessContact?.address,
				business_category: businessType,
				image_keyword: keywords,
				social_profiles: businessContact?.socialMedia,
				language: siteLanguage,
				images: selectedImages,
				site_features: skip
					? []
					: siteFeatures
							.filter( ( feature ) => feature.enabled )
							.map( ( feature ) => feature.id ),
			};
			setToSessionStorage( 'create-site-payload', createSitePayload );

			const response = await apiFetch( {
				path: 'zipwp/v1/site',
				method: 'POST',
				data: createSitePayload,
			} );

			if ( response.success ) {
				const websiteData = response.data.data.site;
				// Close the onboarding screen on success.
				setWebsiteInfoAIStep( websiteData );
				dispatch( {
					type: 'set',
					templateId: websiteData.uuid,
					importErrorMessages: {},
					importErrorResponse: [],
					importError: false,
				} );
				setNextAIStep();
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

	useEffect( () => {
		if ( isFetchingStatus === fetchStatus.fetching ) {
			fetchSiteFeatures();
		}
	}, [] );

	return (
		<div className="grid grid-cols-1 gap-8 auto-rows-auto px-10 pb-10 pt-12 max-w-[880px] w-full mx-auto">
			<div className="space-y-4">
				<h1 className="text-3xl font-bold text-zip-app-heading">
					{ __( 'Select features', 'astra-sites' ) }
				</h1>
				<p className="m-0 p-0 text-base font-normal text-zip-body-text">
					{ __(
						'Select the features you want on this website',
						'astra-sites'
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
							'astra-sites'
						) }
					</p>
				</div>
			) }

			<hr className="!border-border-tertiary border-b-0 w-full" />

			{ /* Navigation buttons */ }
			<NavigationButtons
				continueButtonText="Start Building"
				onClickPrevious={ setPreviousAIStep }
				onClickContinue={ handleGenerateContent() }
				onClickSkip={ handleGenerateContent( true ) }
				loading={ isInProgress }
				skipButtonText={ __( 'Skip & Start Building', 'astra-sites' ) }
			/>
		</div>
	);
};

export default Features;
