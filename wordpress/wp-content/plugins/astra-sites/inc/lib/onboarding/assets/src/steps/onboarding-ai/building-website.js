import { CircularProgressBar } from '@tomickigrzegorz/react-circular-progress-bar';
import { useEffect, useRef, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { XMarkIcon } from '@heroicons/react/24/outline';
import { compose } from '@wordpress/compose';
import { withDispatch, useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { useStateValue } from '../../store/store';
import {
	checkFileSystemPermissions,
	checkRequiredPlugins,
	getAiDemo,
} from '../import-site/import-utils';
import { SITE_CREATION_STATUS_CODES } from './helpers/index';
import { STORE_KEY } from './store';
import { FrameUI } from '../ui/icons';
import ErrorModel from './error-model';
import { getFromSessionStorage, limitExceeded } from './utils/helpers';
import { TOTAL_STEPS } from './onboarding-ai';

const { imageDir } = starterTemplates;

const WebsiteBuilding = ( { onClickNext } ) => {
	const [ progressPercentage, setProgressPercentage ] = useState( 0 );
	const intervalHandle = useRef( null );
	const [ , dispatch ] = useStateValue();
	const storedState = useStateValue();
	const retryCount = useRef( 0 );

	const { stepsData } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );

		return {
			stepsData: getAIStepData(),
		};
	}, [] );
	const { setWebsiteInfoAIStep, setLimitExceedModal } =
		useDispatch( STORE_KEY );

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
		if ( retryCount.current > 1 ) {
			setHideCloseIcon( false );
			setShowProgressBar( false );
		}
		setStatusText( msg || __( 'Failed to create website', 'astra-sites' ) );
		setStatus( 'error' );
		clearInterval( intervalHandle.current );
	};

	const resetErrorState = ( msg ) => {
		setStatusText( msg ?? '' );
		setStatus( '' );
		setHideCloseIcon( true );
		setShowProgressBar( true );
	};

	const getDemoWithRetry = async ( state ) => {
		try {
			return getAiDemo( stepsData, state, websiteInfo );
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

				setStatusText( __( 'Please wait a moment…', 'astra-sites' ) );
				setStatus( 'in-progress' );

				const templateResponse = await getDemoWithRetry( storedState );

				if (
					! templateResponse.success ||
					( templateResponse.success &&
						Object.keys?.( templateResponse )?.length === 0 )
				) {
					onCreationError();
					return;
				}

				await checkRequiredPlugins( storedState );
				checkFileSystemPermissions( storedState );

				setStatusText(
					__(
						'Congratulations! Your website is ready!',
						'astra-sites'
					)
				);
				setStatus( 'done' );
				onClickNext();
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
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
					_ajax_nonce: astraSitesVars._ajax_nonce,
				},
			} );

			// explicit check
			if ( response?.success === true ) {
				handleStatusResponse( response );
			} else if ( response?.success === false ) {
				onCreationError();
			}
		} catch ( error ) {
			onCreationError();
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
	}, [ websiteInfo ] );

	const createSite = async () => {
		try {
			if ( limitExceeded() ) {
				setLimitExceedModal( {
					open: true,
				} );
				return;
			}

			const createSitePayload = getFromSessionStorage(
				'create-site-payload'
			);

			const response = await apiFetch( {
				path: 'zipwp/v1/site',
				method: 'POST',
				data: createSitePayload,
			} );

			if ( response?.success ) {
				// Store website info to state.
				const websiteData = response.data.data.site;
				setWebsiteInfoAIStep( websiteData );
				return response;
			}
			throw new Error( response );
		} catch ( error ) {
			onCreationError();
			const message = error?.data?.data;
			if (
				typeof message === 'string' &&
				message.includes( 'Usage limit' )
			) {
				setLimitExceedModal( {
					open: true,
				} );
			}
		} finally {
			retryCount.current++;
		}
	};

	const restartProcess = () => {
		resetErrorState(
			__( 'Retrying creating the site again.', 'astra-sites' )
		);
		createSite();
	};

	// If failed, retry automatically for one time.
	useEffect( () => {
		if ( retryCount.current === 0 && status === 'error' ) {
			// Reset error status and create site again.
			restartProcess();
		}
	}, [ status ] );

	const handleClose = () => {
		dispatch( {
			type: 'set',
			currentIndex: 0,
		} );
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
					{ showProgressBar && status !== 'error' && (
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
					{ status === 'error' && retryCount.current >= 1 ? (
						<ErrorModel
							renderHeader={
								<div className="space-y-4">
									<h2>
										{ __(
											'Oops.. Something went wrong',
											'astra-sites'
										) }{ ' ' }
										😕
									</h2>
									<div className="text-base !font-semibold leading-6 !mt-5">
										{ __(
											'What happened?',
											'astra-sites'
										) }
									</div>
									<div className="text-app-text text-base font-normal leading-6">
										{ __(
											'Something went wrong during site creation. Please try again later.',
											'astra-sites'
										) }
									</div>
								</div>
							}
							tryAgainCallback={ restartProcess }
							websiteInfo={ websiteInfo }
						/>
					) : (
						<div className="flex flex-col">
							<h4>
								{ __(
									'We are building your website…',
									'astra-sites'
								) }
							</h4>
							<p className="zw-sm-normal text-app-text w-[350px]">
								{ statusText }
							</p>
						</div>
					) }
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

export default compose(
	withDispatch( ( dispatch ) => {
		const {
			setPreviousAIStep,
			setCurrentCategory,

			setNextAIStep,
		} = dispatch( 'ast-block-templates' );

		return {
			onClickPrevious: setPreviousAIStep,
			setCurrentCategory,
			onClickNext: setNextAIStep,
		};
	} )
)( WebsiteBuilding );
