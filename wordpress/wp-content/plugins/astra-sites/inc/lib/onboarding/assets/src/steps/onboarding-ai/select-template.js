import {
	useState,
	useCallback,
	useEffect,
	useMemo,
	useRef,
	useReducer,
} from 'react';
import toast from 'react-hot-toast';
import { twMerge } from 'tailwind-merge';
import { withDispatch, useSelect, useDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import NavigationButtons from './navigation-buttons';
import { classNames, toastBody } from './helpers';
import { STORE_KEY } from './store';
import { ColumnItem } from './components/column-item';
import Input from './components/input';
import {
	ChevronUpIcon,
	MagnifyingGlassIcon,
	XMarkIcon,
} from '@heroicons/react/24/outline';
import { useForm } from 'react-hook-form';
import { useDebounce } from './hooks/use-debounce';
import ColumnSkeleton from './components/column-skeleton';
import {
	clearSessionStorage,
	getFromSessionStorage,
	setToSessionStorage,
} from './utils/helpers';
import Button from './components/button';
import LoadingSpinner from './components/loading-spinner';

export const USER_KEYWORD = 'st-template-search';

const SelectTemplate = ( { onClickPrevious } ) => {
	const {
		setWebsiteTemplatesAIStep,
		setWebsiteSelectedTemplateAIStep,
		setWebsiteTemplateSearchResultsAIStep,
	} = useDispatch( STORE_KEY );

	const {
		stepsData: {
			businessName,
			businessType,
			templateSearchResults,
			templateList: allTemplates,
			templateKeywords: keywords = [],
		},
	} = useSelect( ( select ) => {
		const { getAIStepData, getAllPatternsCategories, getOnboardingAI } =
			select( STORE_KEY );

		const onboardingAI = getOnboardingAI();

		return {
			stepsData: getAIStepData(),
			allPatternsCategories: getAllPatternsCategories(),
			isNewUser: onboardingAI?.isNewUser,
		};
	} );

	const {
		register,
		handleSubmit,
		formState: { errors },
		reset,
		setFocus,
		watch,
		getValues,
	} = useForm( {
		defaultValues: {
			keyword:
				getFromSessionStorage( USER_KEYWORD ) ??
				keywords?.join( ', ' ) ??
				'',
		},
	} );
	const watchedKeyword = watch( 'keyword' );
	const debouncedKeyword = useDebounce( watchedKeyword, 300 );

	const [ isFetching, setIsFetching ] = useState( false );
	const [ backToTop, setBackToTop ] = useState( false );

	const parentContainer = useRef( null );
	const templatesContainer = useRef( null );
	const abortRequest = useRef( [] );
	const [ loadMoreTemplates, setLoadMoreTemplates ] = useReducer(
		( state, updatedState ) => {
			return {
				...state,
				...updatedState,
			};
		},
		{
			page: 1,
			loading: false,
			showLoadMore: false,
		}
	);

	const TEMPLATE_TYPE = {
		RECOMMENDED: 'recommended',
		PARTIAL: 'partial',
		GENERIC: 'generic',
	};

	const refinedSearchResults = useMemo( () => {
		if ( ! templateSearchResults?.length ) {
			return [];
		}

		return templateSearchResults.reduceRight( ( acc, item, index ) => {
			if ( ! item.designs?.length ) {
				return acc;
			}
			const otherDesigns = acc
				.filter( ( designItem ) => item.match !== designItem.match )
				.flatMap( ( otherItem ) => otherItem.designs );

			const updatedDesigns = item.designs.filter(
				( designItem ) =>
					! otherDesigns.find(
						( otherDesign ) => otherDesign.uuid === designItem.uuid
					)
			);

			acc[ index ] = { ...item, designs: updatedDesigns };
			return acc;
		}, templateSearchResults );
	}, [ templateSearchResults ] );

	const getTemplates = useCallback(
		( type ) => {
			const { RECOMMENDED, GENERIC, PARTIAL } = TEMPLATE_TYPE;
			switch ( type ) {
				case RECOMMENDED:
					return refinedSearchResults?.[ 0 ]?.designs || [];
				case PARTIAL:
					return refinedSearchResults?.[ 1 ]?.designs || [];
				case GENERIC:
					return refinedSearchResults?.[ 2 ]?.designs || [];
			}
		},
		[ refinedSearchResults ]
	);

	const getInitialUserKeyword = () => {
		const type = businessType.toLowerCase();
		if ( type !== 'others' ) {
			return type;
		} else if ( keywords?.length > 0 ) {
			return keywords[ 0 ];
		}
		return businessName;
	};

	const fetchTemplates = async ( keyword = getInitialUserKeyword() ) => {
		if ( ! keyword ) {
			return;
		}

		try {
			setIsFetching( true );
			if ( abortRequest.current.length ) {
				abortRequest.current.forEach( ( controller ) => {
					controller.abort();
				} );
				abortRequest.current = [];
			}
			setWebsiteTemplatesAIStep( [] );

			const finalKeywords = [
				...new Set(
					keyword
						.split( ',' )
						.map( ( item ) => item.trim()?.toLowerCase() )
				),
			];

			let results = [];
			const allTemplatesList = [];

			const promises = finalKeywords.map( async ( keywordItem ) => {
				const abortController = new AbortController();
				abortRequest.current.push( abortController );
				const response = await apiFetch( {
					path: 'zipwp/v1/templates',
					method: 'POST',
					data: {
						keyword: keywordItem,
						business_name: businessName,
					},
					signal: abortController.signal,
				} );

				const result = response?.data?.data || [];

				if ( results.length === 0 ) {
					results = result;
				} else {
					result.forEach( ( item, indx ) => {
						if ( item?.designs?.length > 0 ) {
							results[ indx ].designs = [
								...results[ indx ].designs,
								...item.designs.filter(
									( template ) =>
										! results[ indx ].designs.find(
											( existingTemplate ) =>
												existingTemplate.uuid ===
												template.uuid
										)
								),
							];
						}
					} );
				}

				// Get the the designs in sequence
				result.forEach( ( item ) => {
					if ( Array.isArray( item.designs ) ) {
						allTemplatesList.push(
							...item.designs.filter(
								( template ) =>
									! allTemplatesList.find(
										( existingTemplate ) =>
											existingTemplate.uuid ===
											template.uuid
									)
							)
						);
					}
				} );

				setWebsiteTemplatesAIStep( [ ...allTemplatesList ] );
				setWebsiteTemplateSearchResultsAIStep( [ ...results ] );
				setIsFetching( false );
				setLoadMoreTemplates( { showLoadMore: true } );

				return true;
			} );

			await Promise.all( promises );
		} catch ( error ) {
			if ( error?.name === 'AbortError' ) {
				return;
			}
			setIsFetching( false );
			toast.error(
				toastBody( {
					message:
						error?.response?.data?.message ||
						'Error while fetching templates',
				} )
			);
		}
	};

	const fetchAllTemplatesByPage = async ( page = 1 ) => {
		try {
			if ( loadMoreTemplates.loading ) {
				return;
			}
			setLoadMoreTemplates( { loading: true } );

			const response = await apiFetch( {
				path: 'zipwp/v1/all-templates',
				method: 'POST',
				data: {
					business_name: businessName,
					per_page: 9,
					page,
				},
			} );

			if ( ! response.success ) {
				throw new Error( response.data.data || 'Error' );
			}

			const result = response?.data?.data?.result || [];
			const lastPage = response?.data?.data?.lastPage || 1;

			const updatedAllTemplates = [
				...allTemplates,
				...result.map( ( item ) => item.designs ).flat(),
			];
			const updatedSearchResults = [ ...templateSearchResults ];
			result.forEach( ( item ) => {
				if ( ! item?.match ) {
					return;
				}
				const indx = updatedSearchResults.findIndex(
					( searchResult ) => searchResult?.match === item?.match
				);
				if ( indx !== -1 ) {
					const existingDesigns = updatedSearchResults[
						indx
					].designs.map( ( design ) => design.uuid );
					const newDesigns = item.designs.filter(
						( designItem ) =>
							! existingDesigns.includes( designItem.uuid )
					);
					updatedSearchResults[ indx ].designs = [
						...updatedSearchResults[ indx ].designs,
						...newDesigns,
					];
				}
			} );

			setWebsiteTemplatesAIStep( updatedAllTemplates );
			setWebsiteTemplateSearchResultsAIStep( updatedSearchResults );

			if ( page === lastPage ) {
				setLoadMoreTemplates( { showLoadMore: false } );
			}
		} catch ( error ) {
			toast.error(
				toastBody( {
					message:
						error?.message?.toString() ||
						'Error while fetching templates',
				} )
			);
		} finally {
			setLoadMoreTemplates( { loading: false } );
		}
	};

	useEffect( () => {
		setFocus( 'keyword' );

		// Save the manually entered keyword to session storage.
		return () => {
			const keyword = getValues( 'keyword' );
			if (
				! keyword ||
				keywords.some(
					( item ) => item?.toLowerCase() === keyword?.toLowerCase()
				)
			) {
				return clearSessionStorage( USER_KEYWORD );
			}
			setToSessionStorage( USER_KEYWORD, keyword );
		};
	}, [] );

	useEffect( () => {
		fetchTemplates(
			debouncedKeyword ? debouncedKeyword : getInitialUserKeyword()
		);
	}, [ debouncedKeyword ] );

	const handleSubmitKeyword = ( { keyword } ) => {
		onChangeKeyword( keyword );
	};

	const handleClearSearch = () => {
		if ( ! watchedKeyword ) {
			return;
		}
		reset( { keyword: '' } );
		onChangeKeyword( getInitialUserKeyword() );
	};

	const onChangeKeyword = ( value = '' ) => {
		fetchTemplates( value );
		setWebsiteSelectedTemplateAIStep( '' );
	};

	const renderTemplates = useMemo( () => {
		if (
			! getTemplates( TEMPLATE_TYPE.RECOMMENDED )?.length &&
			! getTemplates( TEMPLATE_TYPE.PARTIAL )?.length &&
			! getTemplates( TEMPLATE_TYPE.GENERIC )?.length
		) {
			return null;
		}

		return (
			<>
				{ getTemplates( TEMPLATE_TYPE.RECOMMENDED )?.map(
					( template, index ) => (
						<ColumnItem
							key={ template.uuid }
							template={ template }
							isRecommended
							position={ index + 1 }
						/>
					)
				) }
				{ getTemplates( TEMPLATE_TYPE.PARTIAL )?.map(
					( template, index ) => (
						<ColumnItem
							key={ template.uuid }
							template={ template }
							position={
								index +
								1 +
								( getTemplates( TEMPLATE_TYPE.RECOMMENDED )
									?.length || 0 )
							}
						/>
					)
				) }
				{ getTemplates( TEMPLATE_TYPE.GENERIC )?.map(
					( template, index ) => (
						<ColumnItem
							key={ template.uuid }
							template={ template }
							position={
								index +
								1 +
								( ( getTemplates( TEMPLATE_TYPE.RECOMMENDED )
									?.length || 0 ) +
									( getTemplates( TEMPLATE_TYPE.PARTIAL )
										?.length || 0 ) )
							}
						/>
					)
				) }
			</>
		);
	}, [ getTemplates ] );

	const handleShowBackToTop = ( event ) => {
		const SCROLL_THRESHOLD = 100;
		const { scrollTop } = event.target;

		if ( scrollTop > SCROLL_THRESHOLD && ! backToTop ) {
			setBackToTop( true );
		}
		if ( scrollTop <= SCROLL_THRESHOLD && backToTop ) {
			setBackToTop( false );
		}
	};

	const handleClickBackToTop = () => {
		parentContainer.current.scrollTo( {
			top: 0,
			behavior: 'smooth',
		} );
	};

	return (
		<div
			ref={ parentContainer }
			className={ twMerge(
				`mx-auto flex flex-col overflow-x-hidden`,
				'w-full'
			) }
			onScroll={ handleShowBackToTop }
		>
			<div className="space-y-5 px-5 md:px-10 lg:px-14 xl:px-15 pt-12">
				<h1>
					{ __(
						'Choose the structure for your website',
						'astra-sites'
					) }
				</h1>
				<p className="text-base font-normal leading-6 text-app-text">
					{ __(
						'Select your preferred structure for your website from the options below.',
						'astra-sites'
					) }
				</p>
			</div>

			<form
				className="sticky -top-1.5 z-10 pt-4 pb-4 bg-zip-app-light-bg px-5 md:px-10 lg:px-14 xl:px-15"
				onSubmit={ handleSubmit( handleSubmitKeyword ) }
			>
				<Input
					name="keyword"
					inputClassName="pl-11"
					register={ register }
					placeholder="Add a keyword"
					height="12"
					error={ errors?.keyword }
					prefixIcon={
						<div className="absolute left-4 flex items-center">
							<button
								type="button"
								className="w-auto h-auto p-0 flex items-center justify-center cursor-pointer bg-transparent border-0 focus:outline-none"
								onClick={ handleClearSearch }
							>
								{ watchedKeyword ? (
									<XMarkIcon className="w-5 h-5 text-zip-app-inactive-icon" />
								) : (
									<MagnifyingGlassIcon className="w-5 h-5 text-zip-app-inactive-icon" />
								) }
							</button>
						</div>
					}
				/>
			</form>

			<div
				ref={ templatesContainer }
				className={ classNames(
					'custom-confirmation-modal-scrollbar', // class for thin scrollbar
					'relative',
					'px-5 md:px-10 lg:px-14 xl:px-15',
					'xl:max-w-full'
				) }
			>
				<div
					ref={ templatesContainer }
					className={ classNames(
						'grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 auto-rows-auto items-start justify-center gap-6 mb-10'
					) }
				>
					{ ! isFetching
						? renderTemplates
						: Array.from( { length: 6 } ).map( ( _, index ) => (
								<ColumnSkeleton key={ `skeleton-${ index }` } />
						  ) ) }
				</div>
			</div>

			{ loadMoreTemplates.showLoadMore && (
				<div className="align-center flex justify-center">
					<Button
						className="min-w-[188px]"
						variant="primary"
						onClick={ () => {
							if ( loadMoreTemplates.loading ) {
								return;
							}
							fetchAllTemplatesByPage( loadMoreTemplates.page );
							setLoadMoreTemplates( {
								page: loadMoreTemplates.page + 1,
							} );
						} }
						disabled={ loadMoreTemplates.loading }
					>
						{ loadMoreTemplates.loading ? (
							<LoadingSpinner />
						) : (
							__( 'Load More Designs', 'astra-sites' )
						) }
					</Button>
				</div>
			) }

			{ backToTop && (
				<div className="absolute right-20 bottom-28 ml-auto">
					<button
						type="button"
						className="absolute bottom-0 right-0 z-10 w-8 h-8 rounded-full bg-accent-st border-0 border-solid text-white flex items-center justify-center shadow-sm cursor-pointer"
						onClick={ handleClickBackToTop }
					>
						<ChevronUpIcon className="w-5 h-5" />
					</button>
				</div>
			) }

			<div className="sticky bottom-0 pb-6 bg-zip-app-light-bg pt-6 px-5 md:px-10 lg:px-14 xl:px-15">
				<NavigationButtons
					onClickPrevious={ onClickPrevious }
					hideContinue
				/>
			</div>
		</div>
	);
};

export default compose(
	withDispatch( ( dispatch ) => {
		const { setNextAIStep, setPreviousAIStep } = dispatch( STORE_KEY );

		return {
			onClickNext: setNextAIStep,
			onClickPrevious: setPreviousAIStep,
		};
	} )
)( SelectTemplate );
