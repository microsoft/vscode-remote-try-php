import {
	useEffect,
	useState,
	useLayoutEffect,
	useRef,
} from '@wordpress/element';
import { ArrowRightIcon, ChevronLeftIcon } from '@heroicons/react/24/outline';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import SiteSkeleton from '../components/site-skeleton';
import Button from '../components/button';
import { STORE_KEY } from '../store';
import {
	addHttps,
	sendPostMessage as dispatchPostMessage,
} from '../utils/helpers';
import { classNames } from '../helpers';
import ColorPalettes from '../components/color-palettes';
import FontSelector from '../components/font-selector';
import SiteLogo from '../components/site-logo';
import AiBuilderExitButton from '../components/ai-builder-exit-button';
import ResponsiveButtons, {
	RESPONSIVE_MODES,
} from '../components/responsive-buttons';
import { getDataUri } from '../utils/functions';
import { motion, useAnimation } from 'framer-motion';
import { useNavigateSteps } from '../router';
import CustomColorPalette from '../components/custom-color-palette';

const { logoUrlDark } = aiBuilderVars;

const getAnimationVariants = () => {
	if ( window.innerWidth < 1024 ) {
		return {
			collapsed: { width: '100%', marginLeft: 0 },
			expanded: { width: '100%', marginLeft: 0 },
		};
	}
	return {
		collapsed: { width: '100%', marginLeft: 0 },
		expanded: { width: 'calc(100% - 360px)', marginLeft: '360px' },
	};
};

const sidebarVariants = {
	collapsed: { x: '-100%' },
	expanded: { x: 0 },
};

const SitePreview = () => {
	const { nextStep } = useNavigateSteps();

	const [ loadingIframe, setLoadingIframe ] = useState( true );
	const [ responsiveMode, setResponsiveMode ] = useState(
		RESPONSIVE_MODES.desktop
	);
	const [ collapsed, setCollapsed ] = useState( window.innerWidth < 1024 );
	const { setWebsiteSelectedTemplateAIStep, setSelectedTemplateIsPremium } =
		useDispatch( STORE_KEY );
	const previewAnimationControl = useAnimation();

	const previewContainer = useRef( null );

	const {
		stepData: {
			selectedTemplate,
			templateList,
			businessName,
			selectedImages = [],
			businessContact,
		},
		aiSiteLogo,
		aiActiveTypography,
		aiActivePallette,
	} = useSelect( ( select ) => {
		const {
			getWebsiteInfo,
			getAIStepData,
			getSiteLogo,
			getActiveTypography,
			getActiveColorPalette,
		} = select( STORE_KEY );

		return {
			websiteInfo: getWebsiteInfo(),
			stepData: getAIStepData(),
			aiSiteLogo: getSiteLogo(),
			aiActiveTypography: getActiveTypography(),
			aiActivePallette: getActiveColorPalette(),
		};
	}, [] );
	const selectedTemplateItem = templateList?.find(
		( item ) => item?.uuid === selectedTemplate
	);

	const sendPostMessage = ( data ) => {
		dispatchPostMessage( data, 'astra-starter-templates-preview' );
	};

	const updateScaling = () => {
		const container = previewContainer.current;
		if ( ! container ) {
			return;
		}

		const iframe = container.children[ 1 ];
		const containerWidth = container.clientWidth;
		const containerHeight = container.clientHeight - 44;
		const iframeWidth = iframe.clientWidth;
		const scaleX = containerWidth / iframeWidth;
		const scaleValue = scaleX;

		if ( responsiveMode.name !== 'desktop' ) {
			// Check if overflowing.
			iframe.removeAttribute( 'style' );
			if ( containerWidth > iframeWidth ) {
				return;
			}
		}

		// Set the scale for both width and height
		iframe.style.transform = `scale(${ scaleValue })`;
		iframe.style.transformOrigin = 'top left';
		iframe.style.height = `${ containerHeight / scaleValue }px`;
	};

	const handleIframeLoading = () => {
		if ( ! selectedImages?.length ) {
			selectedImages.push( aiBuilderVars?.placeholder_images[ 0 ] );
			selectedImages.push( aiBuilderVars?.placeholder_images[ 1 ] );
		}

		if ( aiSiteLogo?.url ) {
			const mediaData = { ...aiSiteLogo };
			if ( window.location.protocol === 'http:' ) {
				getDataUri( mediaData.url, function ( data ) {
					mediaData.dataUri = data;
				} );
			}
			setTimeout( () => {
				sendPostMessage( {
					param: 'siteLogo',
					data: mediaData,
				} );
			}, 100 );
		}

		if ( ! aiActivePallette?.slug?.includes( 'default' ) ) {
			sendPostMessage( {
				param: 'colorPalette',
				data: aiActivePallette,
			} );
		}

		if ( ! aiActiveTypography?.default ) {
			sendPostMessage( {
				param: 'siteTypography',
				data: aiActiveTypography,
			} );
		}

		if ( Object.values( businessContact ).some( Boolean ) ) {
			const updatedData = [
				{
					type: 'phone',
					value: businessContact.phone,
					fallback: '202-555-0188',
				},
				{
					type: 'email',
					value: businessContact.email,
					fallback: 'contact@example.com',
				},
				{
					type: 'address',
					value: businessContact.address,
					fallback: '2360 Hood Avenue, San Diego, CA, 92123',
				},
			];
			sendPostMessage( {
				param: 'contactInfo',
				data: updatedData,
			} );
		}

		sendPostMessage( {
			param: 'images',
			data: {
				...selectedImages,
			},
			preview_type: 'full',
		} );

		if ( selectedTemplateItem?.content ) {
			sendPostMessage( {
				param: 'content',
				data: selectedTemplateItem.content,
				businessName,
			} );
		}

		setLoadingIframe( false );
		updateScaling();
	};

	const updateZipPlanData = async () => {
		await apiFetch( {
			path: 'zipwp/v1/zip-plan',
			method: 'POST',
			headers: {
				'X-WP-Nonce': aiBuilderVars.rest_api_nonce,
			},
		} ).then( ( response ) => {
			if ( response.success ) {
				// setZipPlans( response.data );
			} else {
				//  Handle error.
			}
		} );
	};

	useEffect( () => {
		updateZipPlanData();
	}, [] );

	const handleResize = () => {
		if ( window.innerWidth < 1024 ) {
			setCollapsed( true );
		} else {
			setCollapsed( false );
		}
	};

	// Check for window resize.
	useLayoutEffect( () => {
		const resizeObserver = new ResizeObserver( handleResize );
		resizeObserver.observe( window.document.body );
		return () => {
			resizeObserver.unobserve( window.document.body );
		};
	}, [] );

	useLayoutEffect( () => {
		requestAnimationFrame( updateScaling );
	}, [ responsiveMode, collapsed ] );

	useLayoutEffect( () => {
		const resizeObserver = new ResizeObserver( updateScaling );
		resizeObserver.observe( window.document.body );
		return () => {
			resizeObserver.unobserve( window.document.body );
		};
	}, [ responsiveMode ] );

	const toggleCollapse = async () => {
		setCollapsed( ( prev ) => ! prev );
		await previewAnimationControl.start( {
			opacity: [ 0, 0, 1 ],
			transition: {
				type: 'spring',
				mass: 0.4,
				damping: 600,
				stiffness: 600,
				duration: 0.6,
				ease: 'easeOut',
			},
		} );
	};

	const renderBrowserFrame = () => (
		<div
			className={ classNames(
				'flex items-center justify-start py-3 px-4 bg-white shadow-sm rounded-t-lg mx-auto h-[44px] z-[1] relative',
				responsiveMode?.name === 'desktop' && 'w-full mx-0',
				responsiveMode?.name === 'tablet' && 'w-[800px]',
				responsiveMode?.name === 'mobile' && 'w-[400px]'
			) }
		>
			<div className="flex gap-2 py-[3px] w-20">
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
			</div>
			<p className="!m-0 w-full truncate !text-sm !text-zip-body-text text-center">
				{ __(
					'This is just a sneak peek. The actual website and its content will be created in the next step.',
					'ai-builder'
				) }
			</p>
		</div>
	);

	return (
		<motion.div
			id="spectra-onboarding-ai"
			key="spectra-onboarding-ai"
			className="relative font-sans flex flex-wrap h-screen w-screen"
			initial={ { opacity: 0 } }
			animate={ { opacity: 1 } }
			exit={ { opacity: 0 } }
			transition={ { type: 'spring' } }
		>
			<motion.div
				className={ classNames(
					'absolute top-0 left-0 flex w-[360px] lg:flex-col z-10 h-screen'
				) }
				onTransitionEnd={ updateScaling }
				initial={ false }
				animate={ collapsed ? 'collapsed' : 'expanded' }
				transition={ {
					duration: 0.25,
					ease: 'easeInOut',
					delay: 0.05,
				} }
				variants={ sidebarVariants }
			>
				<div className="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-zip-dark-theme-bg px-6">
					<div className="mt-3 flex h-16 shrink-0 items-center relative">
						<img
							className="h-10"
							src={ logoUrlDark }
							alt={ __( 'Build with AI', 'ai-builder' ) }
						/>
						{ /* Exit button */ }
						<div className="absolute top-3 right-0">
							<AiBuilderExitButton />
						</div>
					</div>
					<nav className="flex flex-1 flex-col gap-y-1">
						<div className="w-full mt-2">
							<div className="space-y-5">
								<div>
									<SiteLogo />
								</div>
								<div>
									<FontSelector />
								</div>
								<div>
									<ColorPalettes />
								</div>
								{ aiActivePallette?.slug === 'custom' && (
									<div>
										<CustomColorPalette />
									</div>
								) }
							</div>
						</div>
						<div className="mt-8 mb-5 space-y-5">
							<Button
								className="h-10 w-full font-semibold text-sm leading-5"
								onClick={ nextStep }
								variant="primary"
								hasSuffixIcon
							>
								<span>{ __( 'Continue', 'ai-builder' ) }</span>
								<ArrowRightIcon className="w-5 h-5" />
							</Button>
							<Button
								className="mx-auto text-white h-10 w-full font-semibold text-sm leading-5 bg-zip-dark-theme-content-background"
								variant="blank"
								onClick={ () => {
									setWebsiteSelectedTemplateAIStep( '' );
									setSelectedTemplateIsPremium( '' );
								} }
							>
								<span>
									{ __(
										'Back to Other Designs',
										'ai-builder'
									) }
								</span>
							</Button>
						</div>

						{ /* Responsive preview buttons */ }
						<div className="mt-auto mb-6 flex items-center justify-between gap-3">
							<span className="text-zip-dark-theme-body text-sm font-semibold">
								{ __( 'Responsive Preview', 'ai-builder' ) }
							</span>
							<ResponsiveButtons
								onChange={ setResponsiveMode }
								value={ responsiveMode }
							/>
						</div>
					</nav>
				</div>
				<button
					className="absolute top-[45%] left-full flex items-center justify-center w-4 h-14 bg-zip-dark-theme-bg shadow-sm rounded-tr rounded-br border border-solid border-zip-dark-theme-border cursor-pointer focus:outline-none"
					onClick={ toggleCollapse }
				>
					<ChevronLeftIcon
						className={ classNames(
							'w-4 h-4 text-white scale-110 stroke-2 !shrink-0 transform transition-transform duration-200 ease-in-out',
							collapsed ? 'rotate-180' : 'rotate-0'
						) }
					/>
				</button>
			</motion.div>

			<motion.main
				id="sp-onboarding-content-wrapper"
				className="flex-1 overflow-hidden h-screen max-w-full bg-white transition-all duration-200 ease-in-out"
				initial={ false }
				animate={ collapsed ? 'collapsed' : 'expanded' }
				transition={ { duration: 0.1, ease: 'easeInOut' } }
				variants={ getAnimationVariants() }
				onUpdate={ () => requestAnimationFrame( updateScaling ) }
				onTransitionEnd={ () => requestAnimationFrame( updateScaling ) }
			>
				<div className="h-full w-full relative flex">
					<div
						className={ `w-full max-h-full flex flex-col flex-auto items-center bg-preview-background overflow-hidden` }
					>
						<div className="w-full h-full flex-1">
							{ loadingIframe && (
								<div className="w-full h-full p-8 overflow-y-hidden bg-zip-app-light-bg text-center">
									{ renderBrowserFrame() }
									<SiteSkeleton className="shadow-template-preview !h-[calc(100%_-_44px)]" />
								</div>
							) }

							{ selectedTemplateItem?.domain && (
								<motion.div
									className="w-full h-full p-8"
									animate={ previewAnimationControl }
								>
									<div
										ref={ previewContainer }
										className={ classNames(
											'h-full mx-auto relative overflow-hidden shadow-template-preview',
											responsiveMode?.name ===
												'desktop' && 'w-full mx-0',
											responsiveMode?.name === 'tablet' &&
												'w-[800px]',
											responsiveMode?.name === 'mobile' &&
												'w-[400px]'
										) }
									>
										{ renderBrowserFrame() }
										<div
											className={ classNames(
												'h-full bg-zip-app-light-bg mx-auto',
												responsiveMode?.name ===
													'desktop' &&
													'w-[1700px] mx-0',
												responsiveMode?.name ===
													'tablet' && 'w-[800px]',
												responsiveMode?.name ===
													'mobile' && 'w-[400px]'
											) }
										>
											<motion.iframe
												className={ classNames(
													'h-full z-[1]',
													responsiveMode?.name ===
														'desktop' &&
														'w-[1700px]',
													responsiveMode?.name ===
														'tablet' && 'w-[800px]',
													responsiveMode?.name ===
														'mobile' && 'w-[400px]'
												) }
												id="astra-starter-templates-preview"
												title="Website Preview"
												height="100%"
												width={
													responsiveMode?.width ??
													'100%'
												}
												src={
													addHttps(
														selectedTemplateItem.domain
													) + '?preview_demo=yes'
												}
												onLoad={ handleIframeLoading }
											/>
										</div>
									</div>
								</motion.div>
							) }
						</div>
					</div>
				</div>
			</motion.main>
		</motion.div>
	);
};

export default SitePreview;
