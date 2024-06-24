/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { __, sprintf } from '@wordpress/i18n';
import {
	createInterpolateElement,
	useCallback,
	useState,
} from '@wordpress/element';
import {
	ExternalLink,
	Icon,
	Notice,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettingsSelect } from '../../setup-guide/app/helpers/effects';
import GiftIcon from '../../setup-guide/app/components/GiftIcon';
import {
	useBillingSetupFlowEntered,
	useDismissAdsNoticeDispatch,
} from '../helpers/effects';

/**
 * Closing the Ad Credits notice.
 *
 * @event wcadmin_pfw_ads_credits_success_notice
 */
/**
 * Clicking the "Add billing details" link.
 *
 * @event wcadmin_pfw_ads_billing_details_link_click
 */

/**
 * Catalog ad credits notice.
 *
 * @fires wcadmin_pfw_ads_credits_success_notice
 * @fires wcadmin_pfw_ads_billing_details_link_click
 * @return {JSX.Element} Rendered component.
 */
const AdCreditsNotice = () => {
	const [ isNoticeDisplayed, setIsNoticeDisplayed ] = useState( true );

	const appSettings = useSettingsSelect();
	const isBillingSetup = appSettings?.account_data?.is_billing_setup;
	const trackingAdvertiser = appSettings?.tracking_advertiser;
	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;

	const closeAdCreditsNotice = () => {
		setIsNoticeDisplayed( false );
		handleSetDismissAdsNotice();
		recordEvent( 'pfw_ads_credits_success_notice' );
	};

	const setDismissAdsNotice = useDismissAdsNoticeDispatch();
	const handleSetDismissAdsNotice = useCallback( async () => {
		try {
			await setDismissAdsNotice();
		} catch ( error ) {}
	}, [ setDismissAdsNotice ] );

	const billingSetupFlowEntered = useBillingSetupFlowEntered();

	return (
		isNoticeDisplayed && (
			<Notice
				isDismissible={ true }
				onRemove={ closeAdCreditsNotice }
				className="pinterest-for-woocommerce-catalog-sync__ad-credits"
				status="success"
			>
				<Icon
					icon={ GiftIcon }
					className="pinterest-for-woocommerce-catalog-sync__ad-credits__icon"
				/>
				{ isBillingSetup ? (
					<Text>
						{ sprintf(
							// translators: %1$s: Amount of money required to spend to claim ad credits with currency. %2$s: Amount of ad credits given with currency.
							__(
								'Spend %1$s to claim %2$s in Pinterest ad credits. (Ad credits may take up to 24 hours to be credited to account).',
								'pinterest-for-woocommerce'
							),
							currencyCreditInfo.spendRequire,
							currencyCreditInfo.creditsGiven
						) }
					</Text>
				) : (
					<Text>
						{ createInterpolateElement(
							sprintf(
								// translators: %1$s: Amount of money required to spend to claim ad credits with currency. %2$s: Amount of ad credits given with currency.
								__(
									'Spend %1$s to claim %2$s in Pinterest ad credits. To claim the credits, <adsBillingDetails>add your billing details.</adsBillingDetails>',
									'pinterest-for-woocommerce'
								),
								currencyCreditInfo.spendRequire,
								currencyCreditInfo.creditsGiven
							),
							{
								adsBillingDetails: trackingAdvertiser ? (
									<ExternalLink
										href={ `https://ads.pinterest.com/advertiser/${ trackingAdvertiser }/billing/` }
										onClick={ () => {
											recordEvent(
												'wcadmin_pfw_ads_credits_success_notice'
											);
											billingSetupFlowEntered();
										} }
									/>
								) : (
									<strong />
								),
							}
						) }
					</Text>
				) }
			</Notice>
		)
	);
};

export default AdCreditsNotice;
