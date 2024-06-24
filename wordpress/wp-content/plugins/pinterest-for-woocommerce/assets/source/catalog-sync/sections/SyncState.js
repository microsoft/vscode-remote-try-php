/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { createInterpolateElement } from '@wordpress/element';
import { Icon, trendingUp as trendingUpIcon } from '@wordpress/icons';
import {
	Card,
	CardHeader,
	CardFooter,
	ExternalLink,
	Flex,
	FlexItem,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { REPORTS_STORE_NAME } from '../data';
import SyncStateSummary from './SyncStateSummary';
import SyncStateTable from './SyncStateTable';
import { useSettingsSelect } from '../../setup-guide/app/helpers/effects';

/**
 * Clicking on the "Pinterest ads manager" link.
 *
 * @event wcadmin_pfw_ads_manager_link_click
 */

/**
 * Catalog sync state overview component.
 *
 * @fires wcadmin_pfw_ads_manager_link_click
 * @return {JSX.Element} Rendered component.
 */
const SyncState = () => {
	const feedState = useSelect( ( select ) =>
		select( REPORTS_STORE_NAME ).getFeedState()
	);

	const hasAvailableCredits = useSettingsSelect()?.account_data
		?.available_discounts?.marketing_offer?.remaining_discount;

	const availableCredits = sprintf(
		/* translators: %s credits value with currency formatted using wc_price */
		__(
			'You have %s of free ad credits left to use',
			'pinterest-for-woocommerce'
		),
		hasAvailableCredits
	);

	return (
		<Card className="woocommerce-table pinterest-for-woocommerce-catalog-sync__state">
			<CardHeader>
				<Text variant="title.small" as="h2">
					{ __( 'Overview', 'pinterest-for-woocommerce' ) }
				</Text>
			</CardHeader>
			<SyncStateSummary overview={ feedState?.overview } />
			<SyncStateTable workflow={ feedState?.workflow } />
			<CardFooter justify="flex-start">
				<Icon icon={ trendingUpIcon } />
				<Flex
					direction={ 'column' }
					className="pinterest-for-woocommerce-catalog-sync__state-footer"
				>
					<FlexItem>
						<Text>
							{ createInterpolateElement(
								__(
									'Create ads to increase your reach with the <adsManagerLink>Pinterest ads manager</adsManagerLink>',
									'pinterest-for-woocommerce'
								),
								{
									adsManagerLink: (
										<ExternalLink
											href={
												wcSettings
													.pinterest_for_woocommerce
													.pinterestLinks.adsManager
											}
											onClick={ () => {
												recordEvent(
													'pfw_ads_manager_link_click'
												);
											} }
										/>
									),
								}
							) }
						</Text>
					</FlexItem>
					{ hasAvailableCredits && (
						<FlexItem>
							<Text
								className="pinterest-for-woocommerce-catalog-sync__state-footer-credits"
								dangerouslySetInnerHTML={ {
									__html: availableCredits,
								} }
							/>
						</FlexItem>
					) }
				</Flex>
			</CardFooter>
		</Card>
	);
};

export default SyncState;
