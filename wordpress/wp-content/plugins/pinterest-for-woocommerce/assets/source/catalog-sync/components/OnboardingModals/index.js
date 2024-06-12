/**
 * Internal dependencies
 */
import { useSettingsSelect } from '../../../setup-guide/app/helpers/effects';
import OnboardingAdsModal from './OnboardingAdsModal';
import OnboardingModal from './OnboardingModal';
import OnboardingErrorModal from './OnboardingErrorModal';

/**
 * Ads Onboarding Modal.
 *
 * @param {Object} options
 * @param {Function} options.onCloseModal Action to call when the modal gets closed.
 *
 * @return {JSX.Element} rendered component
 */
const OnboardingModals = ( { onCloseModal } ) => {
	const adsCampaignIsActive = useSettingsSelect()?.ads_campaign_is_active;
	const couponRedeemInfo = useSettingsSelect()?.account_data
		?.coupon_redeem_info;

	// Generic modal when there is no campaign.
	if ( ! adsCampaignIsActive ) {
		return <OnboardingModal onCloseModal={ onCloseModal } />;
	}

	// Ads campaign modal no error.
	if ( ! couponRedeemInfo?.error_id ) {
		return <OnboardingAdsModal onCloseModal={ onCloseModal } />;
	}

	// Ads campaign redeem error modal.
	return <OnboardingErrorModal onCloseModal={ onCloseModal } />;
};

export default OnboardingModals;
