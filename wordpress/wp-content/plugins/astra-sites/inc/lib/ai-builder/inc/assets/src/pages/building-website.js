import { CircularProgressBar } from '@tomickigrzegorz/react-circular-progress-bar';
import { useEffect, useRef, useState } from '@wordpress/element';
import {
	ExclamationTriangleIcon,
	XMarkIcon,
} from '@heroicons/react/24/outline';
import { useDispatch, useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import {
	checkFileSystemPermissions,
	checkRequiredPlugins,
	getAiDemo,
} from '../utils/import-site/import-utils';
import { SITE_CREATION_STATUS_CODES } from '../helpers/index';
import { STORE_KEY } from '../store';
import { FrameUI } from '../ui/icons';
import { TOTAL_STEPS, useNavigateSteps } from '../router';

const { imageDir } = aiBuilderVars;

const WebsiteBuilding = () => {
	const { nextStep } = useNavigateSteps();

	const [ progressPercentage, setProgressPercentage ] = useState( 0 );
	const intervalHandle = useRef( null );

	const { stepsData } = useSelect( ( select ) => {
		const {
			getAIStepData,
			getSiteLogo,
			getActiveTypography,
			getActiveColorPalette,
		} = select( STORE_KEY );

		return {
			stepsData: getAIStepData(),
			siteLogo: getSiteLogo(),
			siteTypography: getActiveTypography(),
			siteColorPalette: getActiveColorPalette(),
		};
	}, [] );
	const { updateImportAiSiteData } = useDispatch( STORE_KEY );

	const [ hideCloseIcon, setHideCloseIcon ] = useState( true );

	const { websiteInfo } = useSelect( ( select ) => {
		const { getWebsiteInfo } = select( STORE_KEY );

		return {
			websiteInfo: getWebsiteInfo(),
		};
	} );

	let currentStep = 0;

	const [ status, setStatus ] = useState( 'second' );
	const [ statusText, setStatusText ] = useState( false );
	const [ showProgressBar, setShowProgressBar ] = useState( true );
	const [ isFetchingStatus, setIsFetchingStatus ] = useState( false );

	const updateProgressBar = ( step, totalSteps ) => {
		if ( step >= totalSteps ) {
			setProgressPercentage( 100 );
			return;
		}

		const percentage = Math.floor( ( step / totalSteps ) * 100 );
		setProgressPercentage( percentage );
	};

	const onCreationError = ( msg ) => {
		setHideCloseIcon( false );
		setStatusText( msg || 'Failed to create website' );
		setStatus( 'error' );
		setShowProgressBar( false );
		clearInterval( intervalHandle.current );
	};

	const getDemoWithRetry = async ( dispatch ) => {
		try {
			return getAiDemo( stepsData, dispatch, websiteInfo );
		} catch ( error ) {
			onCreationError();
		}
	};

	const handleStatusResponse = async ( response ) => {
		const responseCode = response?.data?.data?.code;
		const responseCodeType = responseCode?.slice( 0, 1 );

		if ( ! ( responseCode in SITE_CREATION_STATUS_CODES ) ) {
			return;
		}

		const msg = SITE_CREATION_STATUS_CODES[ responseCode ]?.trim();

		if ( response?.success ) {
			const step = +responseCode?.slice( 1 );

			// Avoid progress bar going back
			if ( step > currentStep ) {
				currentStep = step;
				updateProgressBar( currentStep, TOTAL_STEPS );
			}

			// Make sure msg is not empty
			if ( msg && msg !== 'Done' ) {
				setStatusText( msg );
				setStatus(
					responseCodeType !== 'R' ? 'in-progress' : 'retrying'
				);
			}

			if ( msg === 'Done' ) {
				clearInterval( intervalHandle.current );

				setStatusText( 'Please wait a moment...' );
				setStatus( 'in-progress' );

				const templateResponse = await getDemoWithRetry(
					updateImportAiSiteData
				);

				if (
					! templateResponse.success ||
					( templateResponse.success &&
						Object.keys?.( templateResponse )?.length === 0 )
				) {
					onCreationError();
					return;
				}

				await checkRequiredPlugins( updateImportAiSiteData );
				checkFileSystemPermissions( updateImportAiSiteData );

				setStatusText( 'Congratulations! Your website is ready!' );
				setStatus( 'done' );
				nextStep();
			}
		} else {
			onCreationError( msg );
		}
	};

	const fetchImportStatus = async () => {
		if ( isFetchingStatus ) {
			return;
		}
		setIsFetchingStatus( true );

		try {
			const randomToken = ( Math.random() * 200 )?.toString(); // to avoid response caching
			const response = await apiFetch( {
				path: `zipwp/v1/import-status?uuid=${ websiteInfo.uuid }&token=${ randomToken }`,
				method: 'GET',
				headers: {
					'X-WP-Nonce': aiBuilderVars.rest_api_nonce,
					_ajax_nonce: aiBuilderVars._ajax_nonce,
				},
			} );

			// explicit check
			if ( response?.success === true ) {
				handleStatusResponse( response );
			} else if ( response?.success === false ) {
				onCreationError();
			}
		} catch ( error ) {
			// Do nothing
		} finally {
			setIsFetchingStatus( false );
		}
	};

	const handleRefreshStatus = () => {
		intervalHandle.current = setInterval( () => {
			fetchImportStatus();
		}, 7000 );
	};

	useEffect( () => {
		fetchImportStatus();
		handleRefreshStatus();
	}, [] );

	const handleClose = () => {
		window.location.href = `${ aiBuilderVars.adminUrl }themes.php?page=starter-templates`;
	};

	// Confirmation before leaving the page.
	useEffect( () => {
		const handleBeforeUnload = () => isFetchingStatus;

		window.onbeforeunload = handleBeforeUnload;

		return () => {
			window.onbeforeunload = null;
		};
	}, [ isFetchingStatus ] );

	return (
		<>
			<div className="flex flex-1 flex-col items-center justify-center gap-y-4 w-full pb-10">
				<div className="flex items-center justify-center gap-x-6">
					{ showProgressBar && (
						<CircularProgressBar
							colorCircle="#3d45921a"
							colorSlice={
								status === 'error' ? '#EF4444' : '#3D4592'
							}
							percent={ progressPercentage }
							round
							speed={
								status === 'error' || status === 'retrying'
									? 0
									: 15
							}
							fontColor="#0F172A"
							fontSize="18px"
							fontWeight={ 700 }
							size={ 72 }
						/>
					) }
					{ status === 'error' && (
						<ExclamationTriangleIcon className="w-16 h-16 mt-2 cursor-pointer text-alert-error" />
					) }
					<div className="flex flex-col">
						<h4 className="text-xl">
							{ status === 'error'
								? 'Something went wrong'
								: 'We are building your website...' }
						</h4>
						<p className="zw-sm-normal text-app-text w-[350px]">
							{ statusText }
						</p>
					</div>
				</div>
				{ status !== 'error' && (
					<div className="relative flex items-center justify-center px-10 py-6 h-120 w-120 bg-loading-website-grid-texture">
						<div
							className="absolute flex items-center justify-center w-full h-full"
							style={ {
								backgroundImage: `url(${ imageDir }/build-with-ai/grid.svg)`,
							} }
						>
							<div className="relative flex items-center justify-center w-32 h-32 bg-white rounded-full shadow-loader z-[2]">
								<div className="absolute flex items-center justify-center w-full h-full">
									<img
										width={ 82 }
										height={ 82 }
										className="animate-rotate"
										src={ `${ imageDir }/build-with-ai/loader-circle-dots.svg` }
										alt=""
									/>
								</div>
								<div className="absolute flex items-center justify-center w-full h-full">
									<img
										width={ 40 }
										height={ 40 }
										src={ `${ imageDir }/build-with-ai/loader-wand.svg` }
										alt=""
									/>
								</div>
							</div>
						</div>
						<div className="z-[1] after:content-[''] after:absolute after:inset-0 after:z-[-1] after:bg-gradient-to-r after:from-[#0A21F8] after:via-[#9933FF] after:to-[#FC65D2] after:blur-xl after:-translate-y-1 after:opacity-50 after:scale-75">
							<FrameUI className="w-[400px] h-[288px]" />
						</div>
					</div>
				) }
			</div>
			{ ! hideCloseIcon && (
				<div
					className="fixed top-0 right-0 z-50"
					onClick={ handleClose }
					aria-hidden="true"
				>
					<div className="absolute top-5 right-5 cursor-pointer">
						<XMarkIcon className="w-8 text-zip-app-inactive-icon hover:text-icon-secondary transition duration-150 ease-in-out" />
					</div>
				</div>
			) }
		</>
	);
};

export default WebsiteBuilding;
