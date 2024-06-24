/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Flex,
	Dashicon,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import OnboardingModal from './OnboardingModal';
import { useSettingsSelect } from '../../../setup-guide/app/helpers/effects';

/**
 * Ads Onboarding Modal.
 *
 * @param {Object} options
 * @param {Function} options.onCloseModal Action to call when the modal gets closed.
 *
 * @return {JSX.Element} rendered component
 */
const OnboardingErrorModal = ( { onCloseModal } ) => {
	const ALREADY_REDEEMED_ERROR = 2322;
	const DIFFERENT_ADVERTISER_ALREADY_REDEEMED_ERROR = 2318;
	const OFFER_EXPIRED_ERROR = 2319;
	const NOT_AVAILABLE_IN_COUNTRY_OR_CURRENCY_ERROR = 2327;
	const WRONG_BILLING_PROFILE_ERROR = 2006;

	const couponRedeemInfo = useSettingsSelect()?.account_data
		?.coupon_redeem_info;

	let errorMessageText = '';
	switch ( couponRedeemInfo?.error_id ) {
		case ALREADY_REDEEMED_ERROR:
			errorMessageText = __(
				'Advertiser already has a redeemed offer.',
				'pinterest-for-woocommerce'
			);

			break;

		case DIFFERENT_ADVERTISER_ALREADY_REDEEMED_ERROR:
			errorMessageText = __(
				'The merchant already redeemed the offer on another advertiser.',
				'pinterest-for-woocommerce'
			);

			break;

		case OFFER_EXPIRED_ERROR:
			errorMessageText = __(
				'Unable to claim Pinterest ads credits as the offer has expired.',
				'pinterest-for-woocommerce'
			);

			break;

		case NOT_AVAILABLE_IN_COUNTRY_OR_CURRENCY_ERROR:
			errorMessageText = __(
				'Unable to claim Pinterest ads credits as the offer code is not available for your country.',
				'pinterest-for-woocommerce'
			);

			break;

		case WRONG_BILLING_PROFILE_ERROR:
			errorMessageText = __(
				'Offer code can only be redeemed by an advertiser with a credit card billing profile.',
				'pinterest-for-woocommerce'
			);

			break;

		default:
			errorMessageText = couponRedeemInfo?.error_message;
			break;
	}
	return (
		<OnboardingModal onCloseModal={ onCloseModal }>
			<Flex
				direction="row"
				className="pinterest-for-woocommerce-catalog-sync__onboarding-generic-modal__error"
			>
				<Dashicon icon="info" />
				<Text variant="body.large">{ errorMessageText }</Text>
			</Flex>
		</OnboardingModal>
	);
};

export default OnboardingErrorModal;
