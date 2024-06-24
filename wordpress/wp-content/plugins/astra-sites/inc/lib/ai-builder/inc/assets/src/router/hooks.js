import { useNavigate, useRouterState } from '@tanstack/react-router';
import steps from './routes';
import { useSelect } from '@wordpress/data';
import { STORE_KEY } from '../store';

export const useNavigateSteps = () => {
	const routerState = useRouterState(),
		currentLocation = routerState.location.pathname;
	const navigate = useNavigate();

	const currentStepIndex = steps.findIndex(
		( step ) => step?.path === currentLocation
	);
	let previousStepURL = '',
		nextStepURL = '',
		currentStepURL = '';
	if ( currentStepIndex !== -1 ) {
		currentStepURL = steps[ currentStepIndex ]?.path ?? '';
		previousStepURL = steps[ currentStepIndex - 1 ]?.path ?? '';
		nextStepURL = steps[ currentStepIndex + 1 ]?.path ?? '';
	}

	const nextStep = ( from = '' ) => {
		const firstStep = steps[ 0 ]?.path;

		navigate( {
			...( !! from && { from } ),
			to: `/${ nextStepURL || firstStep }`,
			state: { from: currentStepURL },
		} );
	};

	const previousStep = ( from = '' ) => {
		navigate( {
			...( !! from && { from } ),
			to: `/${ previousStepURL }`,
			state: { from: currentStepURL },
		} );
	};

	return Object.seal( {
		currentStepURL,
		previousStepURL,
		nextStepURL,
		nextStep,
		previousStep,
		navigateTo: navigate,
		currentStepIndex,
	} );
};

export const useValidateStep = ( currentStepURL ) => {
	const stepData = useSelect( ( select ) => {
		const { getAIStepData, getWebsiteInfo } = select( STORE_KEY );
		return {
			...getAIStepData(),
			websiteInfo: getWebsiteInfo(),
		};
	}, [] );
	const stepIndex = steps.findIndex(
		( step ) => step?.path === currentStepURL
	);

	if ( stepIndex === -1 ) {
		return '';
	}

	return steps.reduce( ( acc, step, indx ) => {
		if ( indx > stepIndex || acc ) {
			return acc;
		}

		if (
			! step?.requiredStates?.every( ( state ) => {
				const stateValue = stepData?.[ state ],
					valueType = typeof stateValue;

				if ( valueType === 'string' && !! stateValue.trim() ) {
					return true;
				} else if (
					valueType === 'object' &&
					Object.values( stateValue ).length
				) {
					return true;
				}
				return false;
			} )
		) {
			return steps[ indx ].path;
		}

		return acc;
	}, '' );
};
