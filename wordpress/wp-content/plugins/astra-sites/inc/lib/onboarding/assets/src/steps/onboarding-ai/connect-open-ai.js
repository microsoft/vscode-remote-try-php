import { ChevronRightIcon } from '@heroicons/react/24/outline';
import { useForm } from 'react-hook-form';
import { useEffect } from '@wordpress/element';
import { withDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';
import Heading from './heading';
import { STORE_KEY } from './store';
import Button from './components/button';

const { site_url: siteUrl, spec_ai_auth_url: specAiUrl } = astraSitesVars;

const ConnectOpenAI = ( { onClickContinue, onLater } ) => {
	const { token } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );
	const { handleSubmit, setFocus } = useForm( { defaultValues: { token } } );

	const handleSubmitForm = () => {
		window.location.href = specAiUrl;
	};

	useEffect( () => {
		setFocus( 'apiKey' );
	}, [ setFocus ] );

	useEffect( () => {
		if ( token ) {
			onClickContinue();
		}
	}, [] );

	const doItLater = async () => {
		try {
			await apiFetch( {
				path: `${ siteUrl }/wp-json/gutenberg-templates/v1/do-it-later`,
				method: 'GET',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
				},
			} );
			onLater();
		} catch ( error ) {
			// Do nothing
		}

		return 0;
	};

	return (
		<form
			className="max-w-container mx-auto flex flex-col gap-10"
			onSubmit={ handleSubmit( handleSubmitForm ) }
		>
			<Heading
				heading="Connect Your Account"
				subHeading="We use Zip AI to generate unique and optimized content for your website. Connect your Zip AI account by authorizing the account to continue."
			/>
			<div className="flex">
				<Button type="submit" variant="primary">
					<span>{ __( 'Authorize', 'astra-sites' ) }</span>
					<ChevronRightIcon className="w-6 h-6" />
				</Button>
				<Button
					type="button"
					className="text-secondary-text"
					variant="link"
					onClick={ () => doItLater() }
				>
					{ __( "I'll do it later", 'astra-sites' ) }
				</Button>
			</div>
		</form>
	);
};

export default compose(
	withDispatch( ( dispatch ) => {
		const { setNextAIStep, setPreviousAIStep, toggleOnboardingAIStep } =
			dispatch( 'ast-block-templates' );
		return {
			onClickContinue: setNextAIStep,
			onClickPrevious: setPreviousAIStep,
			onClickSkip: setNextAIStep,
			onLater: toggleOnboardingAIStep,
		};
	} )
)( ConnectOpenAI );
