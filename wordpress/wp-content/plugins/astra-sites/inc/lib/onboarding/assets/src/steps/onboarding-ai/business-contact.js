import { useForm } from 'react-hook-form';
import { useEffect, useState, useRef } from '@wordpress/element';
import { withDispatch, useDispatch, useSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import SocialMediaAdd from './components/social-media';
import Textarea from './components/textarea';
import Input from './components/input';
import { STORE_KEY } from './store';
import Divider from './components/divider';
import NavigationButtons from './navigation-buttons';
import StyledText from './components/StyledText';
import { z as zod } from 'zod';

const PHONE_VALIDATION_REGEX = /^\+?[0-9()\s-]{6,20}$/,
	EMAIL_VALIDATION_REGEX =
		/^[a-z0-9!'#$%&*+\/=?^_`{|}~-]+(?:\.[a-z0-9!'#$%&*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-zA-Z]{2,}$/i;

const mapSocialUrl = ( list ) => {
	return list.map( ( item ) => {
		return {
			type: item.id,
			id: item.id,
			url: item.url,
		};
	} );
};

const BusinessContact = ( {
	onClickContinue,
	onClickPrevious,
	onClickSkip,
} ) => {
	const { businessContact } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );
	const { setWebsiteContactAIStep } = useDispatch( STORE_KEY );
	const [ socialMediaList, setSocialMediaList ] = useState(
		mapSocialUrl( businessContact.socialMedia ?? [] )?.map( ( item ) => ( {
			...item,
			valid: true,
		} ) )
	);
	const previousValues = useRef( {
		...businessContact,
		socialMedia: mapSocialUrl( businessContact?.socialMedia ?? [] )?.map(
			( item ) => ( { ...item, valid: true } )
		),
	} );

	const handleOnChangeSocialMedia = ( list ) => {
		setSocialMediaList( list );
	};

	const { businessName } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	const getValidationSchema = () =>
		zod.object( {
			email: zod
				.string()
				.refine(
					( value ) =>
						value === '' || EMAIL_VALIDATION_REGEX.test( value ),
					{
						message: __(
							'Please enter a valid email',
							'astra-sites'
						),
					}
				),
			phone: zod
				.string()
				.refine(
					( value ) =>
						value === '' || PHONE_VALIDATION_REGEX.test( value ),
					{
						message: __(
							'Please enter a valid phone number',
							'astra-sites'
						),
					}
				),
			address: zod.string().optional(),
		} );

	const {
		register,
		handleSubmit,
		formState: { errors },
		setFocus,
		watch,
	} = useForm( { defaultValues: { ...businessContact } } );

	const handleSubmitForm = ( data ) => {
		setWebsiteContactAIStep( {
			...data,
			socialMedia: mapSocialUrl( socialMediaList ),
		} );
		onClickContinue();
	};

	const getFilteredSocialMediaList = ( list ) => {
		return list.filter( ( item ) => item.valid );
	};

	useEffect( () => {
		setFocus( 'email' );
	}, [ setFocus ] );

	const getValidFormValues = ( formValue ) => {
		const schema = getValidationSchema();

		const validationResult = schema.safeParse( formValue );

		return validationResult?.success
			? validationResult.data
			: {
					...formValue,
					...validationResult.error.issues.reduce( ( acc, error ) => {
						acc[ error.path[ 0 ] ] = '';
						return acc;
					}, {} ),
			  };
	};

	const handleClickSkip = async () => {
		const { socialMedia = [], ...formValue } = previousValues.current;
		const validValues = getValidFormValues( formValue );

		setWebsiteContactAIStep( {
			...validValues,
			socialMedia: mapSocialUrl(
				getFilteredSocialMediaList( socialMedia )
			),
		} );
		onClickSkip();
	};

	// Save inputs before moving to the previous step.
	const handleClickPrevious = async () => {
		const formValue = watch();
		const validValues = getValidFormValues( formValue );

		setWebsiteContactAIStep( {
			...validValues,
			socialMedia: mapSocialUrl(
				getFilteredSocialMediaList( socialMediaList )
			),
		} );
		onClickPrevious();
	};

	const hasInvalidSocialMediaUrl = socialMediaList.some(
		( item ) => ! item.valid
	);

	return (
		<form
			className="w-full max-w-container flex flex-col gap-4 pb-10"
			action="#"
			onSubmit={ handleSubmit( handleSubmitForm ) }
		>
			{ /* Heading */ }
			<div className="text-[2rem] font-semibold leading-[140%]">
				{ __( 'How can people get in touch with ', 'astra-sites' ) }
				<StyledText text={ businessName } />?
			</div>
			{ /* Subheading */ }
			<div className="text-zip-body-text text-[16px] font-normal leading-6">
				{ __(
					'Please provide the contact information details below. These will be used on the website.',
					'astra-sites'
				) }
			</div>

			<div className="space-y-5">
				<div className="flex justify-between gap-x-8 items-start w-full h-[76px]">
					<Input
						className="w-full h-[48px]"
						type="text"
						name="email"
						id="email"
						label={ __( 'Email', 'astra-sites' ) }
						placeholder={ __( 'Your email', 'astra-sites' ) }
						register={ register }
						error={ errors.email }
						validations={ {
							pattern: {
								value: EMAIL_VALIDATION_REGEX,
								message: __(
									'Please enter a valid email',
									'astra-sites'
								),
							},
						} }
						height="[48px]"
					/>
					<Input
						className="w-full h-[48px]"
						type="text"
						name="phone"
						id="phone"
						label={ __( 'Phone Number', 'astra-sites' ) }
						placeholder={ __( 'Your phone number', 'astra-sites' ) }
						register={ register }
						error={ errors.phone }
						validations={ {
							pattern: {
								value: PHONE_VALIDATION_REGEX,
								message: __(
									'Please enter a valid phone number',
									'astra-sites'
								),
							},
						} }
						height="[48px]"
					/>
				</div>
				<Textarea
					rows={ 4 }
					name="address"
					id="address"
					label={ __( 'Address', 'astra-sites' ) }
					placeholder=""
					register={ register }
					error={ errors.address }
				/>

				<SocialMediaAdd
					list={ socialMediaList }
					onChange={ handleOnChangeSocialMedia }
				/>
			</div>
			<Divider />
			<NavigationButtons
				onClickPrevious={ handleClickPrevious }
				onClickSkip={ handleClickSkip }
				disableContinue={ hasInvalidSocialMediaUrl }
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
			onClickSkip: setNextAIStep,
		};
	} )
)( BusinessContact );
