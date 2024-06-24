/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { __, sprintf } from '@wordpress/i18n';
import {
	useEffect,
	useState,
	createInterpolateElement,
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@woocommerce/components';
import {
	Button,
	Card,
	CardBody,
	Flex,
	Notice,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import AdsCreditsPromo from './components/AdsCreditsPromo';
import { PROCESS_STATUS as STATUS, LABEL_STATUS } from '../constants';
import StepHeader from '../components/StepHeader';
import StepOverview from '../components/StepOverview';
import UrlInputControl from '../components/UrlInputControl';
import StatusLabel from '../components/StatusLabel';
import {
	useSettingsSelect,
	useSettingsDispatch,
	useCreateNotice,
} from '../helpers/effects';
import documentationLinkProps from '../helpers/documentation-link-props';

const StaticError = ( { reqError } ) => {
	if ( reqError?.data?.pinterest_code === undefined ) {
		return null;
	}

	const staticErrors = [ 71, 75 ]; // See https://developers.pinterest.com/docs/redoc/#tag/API-Response-Codes

	if ( ! staticErrors.includes( reqError.data.pinterest_code ) ) {
		return null;
	}

	const message = createInterpolateElement(
		sprintf(
			// translators: %s: error reason returned by Pinterest when verifying website claim fail.
			__(
				'<strong>We were unable to verify this domain.</strong> %s',
				'pinterest-for-woocommerce'
			),
			reqError.message
		),
		{
			strong: <strong />,
		}
	);

	return (
		<Notice status="error" isDismissible={ false }>
			{ message }
		</Notice>
	);
};

/**
 * Triggered when domain verification fails.
 *
 * @event wcadmin_pfw_domain_verify_failure
 *
 * @property {string} step Identifier of the step when verification failed.
 */

/**
 * Triggered when a site is successfully verified.
 *
 * @event wcadmin_pfw_domain_verify_success
 */

/**
 * Claim Website step component.
 * Renders a UI with section block and <Card> to claim website (if not yet completed) and display its status.
 *
 * To be used in onboarding setup stepper.
 *
 * @fires wcadmin_pfw_domain_verify_failure
 * @fires wcadmin_pfw_domain_verify_success
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'claim-website', context: props.view }`
 * @param {Object} props React props.
 * @param {'wizard'|'settings'} props.view Indicate which view this component is rendered on.
 * @param {Function} [props.goToNextStep]
 *   When the website claim is complete, called when clicking the "Continue" button.
 *   The "Continue" button is only displayed when `props.view` is 'wizard'.
 * @return {JSX.Element} Rendered component.
 */
const ClaimWebsite = ( { goToNextStep, view } ) => {
	const [ status, setStatus ] = useState( STATUS.IDLE );
	const [ reqError, setReqError ] = useState();
	const isDomainVerified = useSettingsSelect( 'isDomainVerified' );
	const setAppSettings = useSettingsDispatch( view === 'wizard' );
	const createNotice = useCreateNotice();
	const pfwSettings = wcSettings.pinterest_for_woocommerce;

	useEffect( () => {
		if ( status !== STATUS.PENDING && isDomainVerified ) {
			setStatus( STATUS.SUCCESS );
		}
	}, [ status, isDomainVerified ] );

	const handleClaimWebsite = async () => {
		setStatus( STATUS.PENDING );
		setReqError();

		try {
			const results = await apiFetch( {
				path: pfwSettings.apiRoute + '/domain_verification',
				method: 'POST',
			} );
			await setAppSettings( { account_data: results.account_data } );

			recordEvent( 'pfw_domain_verify_success' );

			setStatus( STATUS.SUCCESS );
		} catch ( error ) {
			setStatus( STATUS.ERROR );
			setReqError( error );

			createNotice(
				'error',
				error.message ||
					__(
						'Couldn’t verify your domain.',
						'pinterest-for-woocommerce'
					)
			);

			recordEvent( 'pfw_domain_verify_failure', {
				step:
					wcSettings.pinterest_for_woocommerce
						.claimWebsiteErrorStatus[ error?.data?.status ] ||
					'unknown',
			} );
		}
	};

	const VerifyButton = () => {
		const buttonLabels = {
			[ STATUS.IDLE ]: __(
				'Start verification',
				'pinterest-for-woocommerce'
			),
			[ STATUS.PENDING ]: __( 'Verifying…', 'pinterest-for-woocommerce' ),
			[ STATUS.ERROR ]: __( 'Try again', 'pinterest-for-woocommerce' ),
			[ STATUS.SUCCESS ]: __( 'Verified', 'pinterest-for-woocommerce' ),
		};

		const text = buttonLabels[ status ];

		if ( Object.values( LABEL_STATUS ).includes( status ) ) {
			return <StatusLabel status={ status } text={ text } />;
		}

		return (
			<Button isSecondary text={ text } onClick={ handleClaimWebsite } />
		);
	};

	return (
		<div className="woocommerce-setup-guide__claim-website">
			{ view === 'wizard' && (
				<StepHeader
					title={ __(
						'Claim your website',
						'pinterest-for-woocommerce'
					) }
					subtitle={ __( 'Step Two', 'pinterest-for-woocommerce' ) }
				/>
			) }

			<div className="woocommerce-setup-guide__step-columns">
				<div className="woocommerce-setup-guide__step-column">
					<StepOverview
						title={
							view === 'wizard'
								? __(
										'Claim your website',
										'pinterest-for-woocommerce'
								  )
								: __(
										'Verified domain',
										'pinterest-for-woocommerce'
								  )
						}
						description={ __(
							'Claim your website to get access to analytics for the Pins you publish from your site, the analytics on Pins that other people create from your site and let people know where they can find more of your content.'
						) }
						readMore={ documentationLinkProps( {
							href: pfwSettings.pinterestLinks.claimWebsite,
							linkId: 'claim-website',
							context: view,
						} ) }
					/>
					{ view === 'wizard' && <AdsCreditsPromo /> }
				</div>
				<div className="woocommerce-setup-guide__step-column">
					<Card>
						{ undefined !== isDomainVerified ? (
							<CardBody size="large">
								<Text variant="subtitle">
									{ __(
										'Verify your domain to claim your website',
										'pinterest-for-woocommerce'
									) }
								</Text>
								<Text variant="body">
									{ __(
										'This will allow access to analytics for the Pins you publish from your site, the analytics on Pins that other people create from your site, and let people know where they can find more of your content.',
										'pinterest-for-woocommerce'
									) }
								</Text>

								<Flex gap={ 6 }>
									<UrlInputControl
										disabled
										value={ pfwSettings.homeUrlToVerify }
									/>
									<VerifyButton />
								</Flex>

								<StaticError reqError={ reqError } />
							</CardBody>
						) : (
							<CardBody size="large">
								<Spinner />
							</CardBody>
						) }
					</Card>

					{ view === 'wizard' && (
						<div className="woocommerce-setup-guide__footer-button">
							<Button
								isPrimary
								disabled={ status !== STATUS.SUCCESS }
								onClick={ goToNextStep }
								text={ __(
									'Continue',
									'pinterest-for-woocommerce'
								) }
							/>
						</div>
					) }
				</div>
			</div>
		</div>
	);
};

export default ClaimWebsite;
