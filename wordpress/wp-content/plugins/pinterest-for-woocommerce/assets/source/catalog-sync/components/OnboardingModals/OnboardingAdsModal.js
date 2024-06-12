/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';
import { Icon, external as externalIcon } from '@wordpress/icons';
import {
	Button,
	Flex,
	Modal,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettingsSelect } from '../../../setup-guide/app/helpers/effects';
import { useBillingSetupFlowEntered } from '../../helpers/effects';

const OnboardingModalText = ( { isBillingSetup } ) => {
	const appSettings = useSettingsSelect();
	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;

	if ( ! isBillingSetup ) {
		return (
			<Text variant="body">
				{ createInterpolateElement(
					sprintf(
						// translators: %1$s: Amount of ad credit given with currency. %2$s: Amount of money required to spend to claim ad credits with currency.
						__(
							'You are eligible for %1$s of Pinterest ad credits. To claim the credits, <strong>you would need to add your billing details and spend %2$s on Pinterest ads.</strong>',
							'pinterest-for-woocommerce'
						),
						currencyCreditInfo.creditsGiven,
						currencyCreditInfo.spendRequire
					),
					{
						strong: <strong />,
					}
				) }
			</Text>
		);
	}

	return (
		<Text variant="body">
			{ sprintf(
				// translators: %s: Amount of ad credit given with currency.
				__(
					'You are eligible for %s of Pinterest ad credits. To claim the credits, head over to the Pinterest ads manager and ',
					'pinterest-for-woocommerce'
				),
				currencyCreditInfo.creditsGiven
			) }
			<strong>
				{ sprintf(
					// translators: %s: Amount of money required to spend to claim ad credits with currency.
					__(
						'spend %s on Pinterest ads.',
						'pinterest-for-woocommerce'
					),
					currencyCreditInfo.spendRequire
				) }
			</strong>
		</Text>
	);
};

/**
 * Ads Onboarding Modal.
 *
 * @param {Object} options
 * @param {Function} options.onCloseModal Action to call when the modal gets closed.
 *
 * @return {JSX.Element} rendered component
 */
const OnboardingAdsModal = ( { onCloseModal } ) => {
	const appSettings = useSettingsSelect();
	const isBillingSetup = appSettings?.account_data?.is_billing_setup;
	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;
	const billingSetupFlowEntered = useBillingSetupFlowEntered();

	const onClickBilling = () => {
		onCloseModal();
		billingSetupFlowEntered();
	};

	return (
		<Modal
			icon={
				<img
					src={
						wcSettings.pinterest_for_woocommerce.pluginUrl +
						'/assets/images/gift_banner.svg'
					}
					alt="Gift banner"
				/>
			}
			onRequestClose={ onCloseModal }
			className="pinterest-for-woocommerce-catalog-sync__onboarding-modal"
		>
			<Text variant="title.small">
				{ sprintf(
					// translators: %s: Amount of ad credit given with currency.
					__(
						'You are one step away from claiming %s of Pinterest ad credits.',
						'pinterest-for-woocommerce'
					),
					currencyCreditInfo.creditsGiven
				) }
			</Text>
			<Text variant="body">
				{ __(
					'You have successfully set up your Pinterest integration! Your product catalog is being synced and reviewed. This could take up to 2 days.',
					'pinterest-for-woocommerce'
				) }
			</Text>
			<OnboardingModalText isBillingSetup={ isBillingSetup } />
			<Text variant="caption">
				{ __(
					'*Ad credits may take up to 24 hours to be credited to account.',
					'pinterest-for-woocommerce'
				) }
			</Text>
			<Flex direction="row" justify="flex-end">
				{ isBillingSetup ? (
					<Button isPrimary onClick={ onCloseModal }>
						{ __( 'Got it', 'pinterest-for-woocommerce' ) }
					</Button>
				) : (
					<>
						<Button onClick={ onCloseModal }>
							{ __(
								'Do this later',
								'pinterest-for-woocommerce'
							) }
						</Button>
						{
							// Empty tracking_advertiser should not happen.
							appSettings.tracking_advertiser ? (
								<Button
									isPrimary
									href={ `https://ads.pinterest.com/advertiser/${ appSettings.tracking_advertiser }/billing/` }
									target="_blank"
									onClick={ onClickBilling }
								>
									{ __(
										'Add billing details',
										'pinterest-for-woocommerce'
									) }
									<Icon icon={ externalIcon } />
								</Button>
							) : (
								''
							)
						}
					</>
				) }
			</Flex>
		</Modal>
	);
};

export default OnboardingAdsModal;
