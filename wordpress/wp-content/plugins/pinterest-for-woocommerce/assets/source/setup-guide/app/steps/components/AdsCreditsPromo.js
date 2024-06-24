/**
 * External dependencies
 */
import { useState, createInterpolateElement } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { recordEvent } from '@woocommerce/tracks';
import {
	CardDivider,
	Flex,
	FlexBlock,
	Icon,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useSettingsSelect } from '../../helpers/effects';
import AdsCreditsTermsAndConditionsModal from '../../components/TermsAndConditionsModal';
import GiftIcon from '../../components/GiftIcon';

const AdsCreditsPromo = () => {
	const appSettings = useSettingsSelect();
	const [
		isTermsAndConditionsModalOpen,
		setIsTermsAndConditionsModalOpen,
	] = useState( false );

	const openTermsAndConditionsModal = () => {
		setIsTermsAndConditionsModalOpen( true );
		recordEvent( 'pfw_modal_open', {
			context: 'wizard',
			name: 'ads-credits-terms-and-conditions',
		} );
	};

	const closeTermsAndConditionsModal = () => {
		setIsTermsAndConditionsModalOpen( false );
		recordEvent( 'pfw_modal_closed', {
			context: 'wizard',
			name: 'ads-credits-terms-and-conditions',
		} );
	};

	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;

	return appSettings?.ads_campaign_is_active ? (
		<>
			<CardDivider
				className={ 'woocommerce-setup-guide__ad-credits__divider' }
			/>
			<Flex className={ 'woocommerce-setup-guide__ad-credits' }>
				<FlexBlock className="image-block">
					<Icon icon={ GiftIcon } />
				</FlexBlock>
				<FlexBlock className="content-block">
					<Text variant="body">
						{ createInterpolateElement(
							sprintf(
								//  translators: %1$s: Amount of ad credits given with currency. %2$s: Amount of money required to spend to claim ad credits with currency.
								__(
									'As a new Pinterest customer, you can get %1$s in free ad credits when you successfully set up Pinterest for WooCommerce and spend %2$s on Pinterest Ads. <a>Pinterest Terms and conditions</a> apply.',
									'pinterest-for-woocommerce'
								),
								currencyCreditInfo.creditsGiven,
								currencyCreditInfo.spendRequire
							),
							{
								a: (
									// Disabling no-content rule - content is interpolated from above string
									// eslint-disable-next-line jsx-a11y/anchor-is-valid, jsx-a11y/anchor-has-content
									<a
										href={ '#' }
										onClick={ openTermsAndConditionsModal }
									/>
								),
							}
						) }
					</Text>
				</FlexBlock>
			</Flex>
			{ isTermsAndConditionsModalOpen && (
				<AdsCreditsTermsAndConditionsModal
					onModalClose={ closeTermsAndConditionsModal }
				/>
			) }
		</>
	) : null;
};

export default AdsCreditsPromo;
