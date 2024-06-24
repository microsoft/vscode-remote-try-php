import { useEffect } from 'react';
import { withDispatch, useSelect, useDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';
import { STORE_KEY } from './store';
import Divider from './components/divider';
import Heading from './heading';
import NavigationButtons from './navigation-buttons';
import LanguageSelection from './language-selection';
import BusinessTypes from './components/business-types';

const Type = ( { onClickContinue } ) => {
	const { setSiteLanguageListAIStep, setAuthenticationErrorModal } =
		useDispatch( STORE_KEY );
	const { businessType, siteLanguageList } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	const handleClickContinue = () => {
		if ( ! businessType || '' === businessType ) {
			return;
		}

		onClickContinue();
	};

	const getLanguages = async () => {
		try {
			const response = await apiFetch( {
				path: 'zipwp/v1/site-languages',
				method: 'GET',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
				},
			} );
			if ( response.success ) {
				setSiteLanguageListAIStep( response?.data?.data );
			} else {
				setAuthenticationErrorModal( {
					open: true,
				} );
			}
		} catch ( error ) {
			// Handle error.
		}
	};

	useEffect( () => {
		if ( siteLanguageList?.length ) {
			return;
		}
		getLanguages();
	}, [ siteLanguageList ] );

	return (
		<div className="w-full max-w-container flex flex-col gap-8">
			{ /* Heading */ }
			<Heading
				heading="This website is for:"
				subHeading="Let's get started by choosing the type of website you'd like to create."
			/>
			{ /* Types */ }
			<div className="min-h-[48px]">
				<BusinessTypes />
			</div>
			<LanguageSelection />
			<Divider />
			{ /* Footer */ }
			<NavigationButtons
				onClickContinue={ handleClickContinue }
				disableContinue={ ! businessType || '' === businessType }
			/>
		</div>
	);
};
export default compose(
	withDispatch( ( dispatch ) => {
		const { setNextAIStep, setPreviousAIStep } = dispatch( STORE_KEY );
		return {
			onClickContinue: setNextAIStep,
			onClickPrevious: setPreviousAIStep,
		};
	} )
)( Type );
