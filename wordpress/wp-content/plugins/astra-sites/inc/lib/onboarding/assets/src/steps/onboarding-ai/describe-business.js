import { useForm } from 'react-hook-form';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/20/solid';
import apiFetch from '@wordpress/api-fetch';
import {
	useEffect,
	useState,
	useRef,
	useLayoutEffect,
} from '@wordpress/element';
import { withDispatch, useSelect, useDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { WandIcon } from '../ui/icons';
import Textarea from './components/textarea';
import LoadingSpinner from './components/loading-spinner';
import NavigationButtons from './navigation-buttons';
import Heading from './heading';
import Divider from './components/divider';
import { STORE_KEY } from './store';
import { adjustTextAreaHeight } from './utils/helpers';
import { classNames } from './helpers';
import StyledText from './components/StyledText';
import { __ } from '@wordpress/i18n';

const DescribeBusiness = ( { onClickContinue, onClickPrevious } ) => {
	const {
		businessDetails,
		businessType,
		businessName,
		descriptionListStore,
	} = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	const aiOnboardingDetails = useSelect( ( select ) => {
			const { getOnboardingAI } = select( STORE_KEY );
			return getOnboardingAI();
		} ),
		{ loadingNextStep } = aiOnboardingDetails;

	const {
		setWebsiteDetailsAIStep,
		setWebsiteKeywordsAIStep,
		resetKeywordsImagesAIStep,
		setOnboardingAIDetails,
	} = useDispatch( STORE_KEY );

	const [ isLoading, setIsLoading ] = useState( false );
	const [ isFetchingKeywords, setIsFetchingKeywords ] = useState( false );
	const prevBusinessDetails = useRef( businessDetails );
	const textareaRef = useRef( null );

	const {
		register,
		handleSubmit,
		formState: { errors },
		watch,
		setValue,
		setFocus,
	} = useForm( { defaultValues: { businessDetails } } );
	const formBusinessDetails = watch( 'businessDetails' );

	const handleFormSubmit = async ( data ) => {
		setWebsiteDetailsAIStep( data.businessDetails );
		if ( prevBusinessDetails.current !== data.businessDetails ) {
			// Reset images and keywords if description changes.
			resetKeywordsImagesAIStep();
		}
		await fetchImageKeywords( data.businessDetails );
		onClickContinue();
	};

	const handleGenerateContent = async () => {
		if ( isLoading ) {
			return;
		}
		setIsLoading( true );

		const newDescList = [ formBusinessDetails ];

		try {
			const response = await apiFetch( {
				path: `zipwp/v1/description`,
				method: 'POST',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
				},
				data: {
					business_name: businessName,
					business_description: formBusinessDetails,
					category: businessType,
				},
			} );
			if ( response.success ) {
				const description = response.data?.data || [];
				if ( description !== undefined ) {
					newDescList.push( description );

					addDescriptionToList( newDescList );

					setValue( 'businessDetails', description, {
						shouldValidate: true,
					} );
				}
			}
		} catch ( error ) {
			// Do nothing
		} finally {
			setIsLoading( false );
		}
	};

	const fetchImageKeywords = async ( details ) => {
		if ( isFetchingKeywords ) {
			return;
		}
		// If description is same as previous, do not fetch keywords.
		if ( prevBusinessDetails.current === details ) {
			return;
		}
		setIsFetchingKeywords( true );
		try {
			const response = await apiFetch( {
				path: `zipwp/v1/keywords`,
				method: 'POST',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
				},
				data: {
					business_name: businessName,
					business_description: details,
					category: businessType,
				},
			} );
			if ( response.success ) {
				const keywordsData = response.data?.data;
				setWebsiteKeywordsAIStep(
					Array.isArray( keywordsData )
						? keywordsData
						: Object.values( keywordsData )
				);
			}
		} catch ( error ) {
			// DO Nothing.
		} finally {
			setIsFetchingKeywords( false );
		}
	};

	const getTitle = ( strings, name ) => {
		if ( name === 'name' ) {
			name = businessName;
		}

		return (
			<div className="text-[2rem] font-semibold leading-[45px]">
				{ strings[ 0 ] }
				<StyledText text={ businessName } />
				{ strings[ 1 ] }
			</div>
		);
	};

	const CATEGORY_DATA = {
		business: {
			question: getTitle`What is ${ 'name' }? Please describe the business.`,
			description:
				'Please be as descriptive as you can. Share details such as services, products, goals, etc.',
		},
		'personal-website': {
			question: getTitle`Who is ${ 'name' }? Tell us more about the person.`,
			description:
				'Please be as descriptive as you can. Share details such as what they do, their expertise, offerings, etc.',
		},
		organisation: {
			question: getTitle`What is ${ 'name' }? Please describe the organisation.`,
			description:
				'Please be as descriptive as you can. Share details such as services, programs, mission, vision, etc.',
		},
		restaurant: {
			question: getTitle`What is ${ 'name' }? Tell us more about the restaurant.`,
			description:
				'Please be as descriptive as you can. Share details such as a brief about the restaurant, specialty, menu, etc.',
		},
		product: {
			question: getTitle`What is ${ 'name' }? Share more details about the product.`,
			description:
				'Please be as descriptive as you can. Share details such as a brief about the product, features, some USPs, etc.',
		},
		event: {
			question: getTitle`Tell us more about ${ 'name' }.`,
			description:
				'Please be as descriptive as you can. Share details such as Event information date, venue, some highlights, etc.',
		},
		'landing-page': {
			question: getTitle`Share more details about ${ 'name' }.`,
			description:
				'Please be as descriptive as you can. Share details such as a brief about the product, features, some USPs, etc.',
		},
		medical: {
			question: getTitle`Tell us more about the  ${ 'name' }.`,
			description:
				'Please be as descriptive as you can. Share details such as treatments, procedures, facilities, etc.',
		},
		unknown: {
			question: getTitle`Please describe ${ 'name' } in a few words.`,
		},
	};

	const getDescription = ( type ) => {
		return (
			CATEGORY_DATA[ type ]?.description ??
			__(
				'The best way to describe anything is by answering a few WH questions. Who, What, Where, Why, When, etc.',
				'astra-sites'
			)
		);
	};

	useEffect( () => {
		setFocus( 'businessDetails' );
	}, [ setFocus ] );

	useLayoutEffect( () => {
		const textarea = textareaRef.current;
		if ( textarea ) {
			adjustTextAreaHeight( textarea );
		}
	}, [ formBusinessDetails ] );

	const { list: descriptionList, currentPage: descriptionPage } =
		descriptionListStore || {};

	const navigateDescription = ( showNext ) => {
		const newPageNumber = showNext
			? descriptionPage + 1
			: descriptionPage - 1;

		const currentPageIndex = descriptionPage - 1;

		const newList = [ ...descriptionList ];

		// Check if user has made changes to current description and save that change in new slot.
		if ( descriptionList[ currentPageIndex ] !== formBusinessDetails ) {
			newList[ currentPageIndex ] = formBusinessDetails;
		}

		setValue( 'businessDetails', newList[ newPageNumber - 1 ] );
		setOnboardingAIDetails( {
			...aiOnboardingDetails,
			stepData: {
				...aiOnboardingDetails.stepData,
				descriptionListStore: {
					...descriptionListStore,
					list: newList,
					currentPage: newPageNumber,
				},
			},
		} );
	};

	const addDescriptionToList = ( descList ) => {
		if ( ! Array.isArray( descList ) ) {
			return;
		}

		const filteredList = descList.filter(
			( desc ) =>
				desc?.trim()?.length !== 0 &&
				! descriptionList?.includes( desc )
		);

		const newDescList = [ ...descriptionList, ...filteredList ];

		setOnboardingAIDetails( {
			...aiOnboardingDetails,
			stepData: {
				...aiOnboardingDetails.stepData,
				descriptionListStore: {
					list: newDescList,
					currentPage: newDescList.length,
				},
				businessDetails: formBusinessDetails,
				templateList: [],
			},
		} );
	};

	const setBusinessDesc = ( descriptionValue, isOnSubmit ) => {
		if ( descriptionValue?.trim() === businessDetails?.trim() ) {
			return;
		}

		setOnboardingAIDetails( {
			...aiOnboardingDetails,
			stepData: {
				...aiOnboardingDetails.stepData,
				businessDetails: formBusinessDetails,
				...( ! isOnSubmit && {
					keywords: [],
					selectedImages: [],
					imagesPreSelected: false,
				} ),
				templateList: [],
			},
		} );
	};

	useEffect( () => {
		setBusinessDesc( formBusinessDetails );
		adjustTextAreaHeight( textareaRef.current );
	}, [ formBusinessDetails ] );

	return (
		<form
			className="w-full max-w-container flex flex-col gap-8 pb-10"
			action="#"
			onSubmit={ handleSubmit( handleFormSubmit ) }
		>
			<Heading
				heading={
					CATEGORY_DATA[ businessType ]?.question ??
					CATEGORY_DATA.unknown.question
				}
				subHeading={ getDescription(
					businessType?.replaceAll( ' ', '-' )?.toLowerCase()
				) }
			/>
			<div>
				<Textarea
					ref={ textareaRef }
					rows={ 8 }
					className="w-full"
					placeholder={ __(
						'E.g. Mantra Minds is a yoga studio located in Chino Hills, California. The studio offers a variety of classes such as Hatha yoga, Vinyasa flow, and Restorative yoga. The studio is led by Jane, an experienced and certified yoga instructor with over 10 years of teaching expertise. The welcoming atmosphere and personalized Jane make it a favorite among yoga enthusiasts in the area.',
						'astra-sites'
					) }
					name="businessDetails"
					register={ register }
					maxLength={ 1000 }
					validations={ {
						required: __( 'Details are required', 'astra-sites' ),
						maxLength: 1000,
					} }
					error={ errors.businessDetails }
					disabled={ isLoading || loadingNextStep }
				/>

				{ /* Wand Button */ }
				<div
					className={ classNames(
						'h-7 mt-3 flex items-center gap-2 text-app-secondary hover:text-app-accent-hover'
					) }
				>
					{ isLoading && (
						<LoadingSpinner className="text-accent-st cursor-progress" />
					) }
					{ ! isLoading && (
						<div className="flex justify-between w-full">
							<div
								className="flex gap-2 cursor-pointer"
								onClick={ handleGenerateContent }
								data-disabled={ loadingNextStep }
							>
								<WandIcon className="w-5 h-5 transition duration-150 ease-in-out text-accent-st" />
								<span className="font-semibold text-sm transition duration-150 ease-in-out text-accent-st">
									{ formBusinessDetails?.trim() === ''
										? __( 'Write Using AI', 'astra-sites' )
										: __(
												'Improve Using AI',
												'astra-sites'
										  ) }
								</span>
							</div>

							{ descriptionPage > 0 &&
								descriptionList?.length > 1 && (
									<div className="flex gap-2 items-center justify-start w-[100px] cursor-default text-zip-body-text">
										<div
											className="w-5"
											data-disabled={ loadingNextStep }
										>
											{ descriptionPage !== 1 && (
												<ChevronLeftIcon
													className="w-5 cursor-pointer text-zip-body-text"
													onClick={ () =>
														navigateDescription(
															false
														)
													}
												/>
											) }
										</div>
										<div className="zw-sm-semibold cursor-default mt-[-4px]">
											{ descriptionPage } /{ ' ' }
											{ descriptionList?.length }
										</div>
										<div
											className="w-5"
											data-disabled={ loadingNextStep }
										>
											{ descriptionPage !==
												descriptionList?.length && (
												<ChevronRightIcon
													className="w-5 cursor-pointer text-zip-body-text"
													onClick={ () =>
														navigateDescription(
															true
														)
													}
												/>
											) }
										</div>
									</div>
								) }
						</div>
					) }
				</div>
			</div>
			<Divider />
			<NavigationButtons
				onClickPrevious={ onClickPrevious }
				loading={ isFetchingKeywords }
			/>
		</form>
	);
};

export default compose(
	withDispatch( ( dispatch ) => {
		const { setNextAIStep, setPreviousAIStep } = dispatch(
			'ast-block-templates'
		);
		return {
			onClickContinue: setNextAIStep,
			onClickPrevious: setPreviousAIStep,
		};
	} )
)( DescribeBusiness );
