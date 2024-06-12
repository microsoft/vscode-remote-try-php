/**
 * External dependencies
 */
import '@wordpress/notices';
import { __ } from '@wordpress/i18n';
import { createElement, useState, useEffect } from '@wordpress/element';
import { Spinner, Stepper } from '@woocommerce/components';
import { recordEvent } from '@woocommerce/tracks';
import { updateQueryString } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import SetupAccount from '../steps/SetupAccount';
import ClaimWebsite from '../steps/ClaimWebsite';
import SetupTracking from '../steps/SetupTracking';
import OnboardingTopBar from '../components/TopBar';
import {
	useSettingsSelect,
	useBodyClasses,
	useCreateNotice,
} from '../helpers/effects';

/**
 * Onboarding Wizard component.
 *
 * @param {Object} props React props.
 * @param {Object} props.query The current query string, parsed into an object, from the page URL.
 *
 * @fires wcadmin_pfw_setup with `{ target: 'setup-account' | 'claim-website' | 'setup-tracking', trigger: 'wizard-stepper' }` when wizard's header step is clicked.
 * @fires wcadmin_pfw_setup with `{ target: 'claim-website' , trigger: 'setup-account-continue' }` when continue button is clicked.
 * @fires wcadmin_pfw_setup with `{ target: 'setup-tracking', trigger: 'claim-website-continue' }` when continue button is clicked.
 *
 * @return {JSX.Element} Rendered element.
 */
const WizardApp = ( { query } ) => {
	const [ isConnected, setIsConnected ] = useState(
		wcSettings.pinterest_for_woocommerce.isConnected
	);

	const [ isBusinessConnected, setIsBusinessConnected ] = useState(
		wcSettings.pinterest_for_woocommerce.isBusinessConnected
	);

	const appSettings = useSettingsSelect();
	const isDomainVerified = useSettingsSelect( 'isDomainVerified' );
	const createNotice = useCreateNotice();

	useEffect( () => {
		if ( ! isConnected ) {
			setIsBusinessConnected( false );
		}
		createNotice( 'error', wcSettings.pinterest_for_woocommerce.error );
	}, [ isConnected, setIsBusinessConnected, createNotice ] );

	useBodyClasses( 'wizard' );

	const steps = [
		{
			key: 'setup-account',
			container: SetupAccount,
			label: __(
				'Set up your business account',
				'pinterest-for-woocommerce'
			),
			props: {
				setIsConnected,
				isConnected,
				setIsBusinessConnected,
				isBusinessConnected,
			},
			isClickable: true,
		},
		{
			key: 'claim-website',
			container: ClaimWebsite,
			label: __( 'Claim your website', 'pinterest-for-woocommerce' ),
			isClickable: isBusinessConnected,
		},
		{
			key: 'setup-tracking',
			container: SetupTracking,
			label: __(
				'Track conversions with the Pinterest tag',
				'pinterest-for-woocommerce'
			),
			isClickable: isBusinessConnected && isDomainVerified,
		},
	];

	const getSteps = () => {
		return steps.map( ( step ) => {
			const container = createElement( step.container, {
				query,
				step,
				goToNextStep: () => goToNextStep( step ),
				view: 'wizard',
				...step.props,
			} );

			step.content = (
				<div
					className={ `woocommerce-setup-guide__container ${ step.key }` }
				>
					{ container }
				</div>
			);

			if ( step.isClickable ) {
				step.onClick = ( key ) => {
					recordEvent( 'pfw_setup', {
						target: key,
						trigger: 'wizard-stepper',
					} );
					updateQueryString( { step: key } );
				};
			}

			return step;
		} );
	};

	const getCurrentStep = () => {
		const step = steps.find( ( s ) => s.key === query.step );

		if ( ! step ) {
			return steps[ 0 ].key;
		}

		return step.key;
	};

	const goToNextStep = ( step ) => {
		const currentStepIndex = steps.findIndex( ( s ) => s.key === step.key );

		const nextStep = steps[ currentStepIndex + 1 ];
		recordEvent( 'pfw_setup', {
			target: nextStep.key,
			trigger: step.key + '-continue',
		} );

		if ( typeof nextStep === 'undefined' ) {
			return;
		}

		return updateQueryString( { step: nextStep.key } );
	};

	const currentStep = getCurrentStep();

	return (
		<>
			<OnboardingTopBar />
			<div className="woocommerce-setup-guide__main">
				{ appSettings ? (
					<Stepper currentStep={ currentStep } steps={ getSteps() } />
				) : (
					<Spinner />
				) }
			</div>
		</>
	);
};

export default WizardApp;
