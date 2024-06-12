/**
 * External dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { getNewPath, getHistory } from '@woocommerce/navigation';
import {
	createInterpolateElement,
	useCallback,
	useState,
} from '@wordpress/element';
import { recordEvent } from '@woocommerce/tracks';
import {
	Button,
	Card,
	Flex,
	FlexBlock,
	Panel,
	PanelBody,
	PanelRow,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import AdsCreditsTermsAndConditionsModal from '../components/TermsAndConditionsModal';
import PrelaunchNotice from '../../../components/prelaunch-notice';
import documentationLinkProps from '../helpers/documentation-link-props';
import UnsupportedCountryNotice from '../components/UnsupportedCountryNotice';
import { useSettingsSelect } from '../helpers/effects';

const tosHref = 'https://business.pinterest.com/business-terms-of-service/';

/**
 * Triggered on events during setup,
 * like starting, ending, or navigating between steps.
 *
 * @event wcadmin_pfw_setup
 *
 * @property {string} target Setup phase that the user navigates to.
 * @property {string} trigger UI element that triggered the action, e.g. `wizard-stepper` or `get-started` button.
 */

/**
 * Welcome Section Card.
 * To be used in getting started page.
 *
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'terms-of-service', context: 'welcome-section' }`
 * @fires wcadmin_pfw_setup with `{ target: 'onboarding', trigger: 'get-started' }` when "Get started" button is clicked for incomplete setup.
 *
 * @return {JSX.Element} Rendered element.
 */
const WelcomeSection = () => {
	const handleGetStarted = () => {
		if ( ! wcSettings.pinterest_for_woocommerce.isSetupComplete ) {
			recordEvent( 'pfw_setup', {
				target: 'onboarding',
				trigger: 'get-started',
			} );
		}
		getHistory().push(
			getNewPath(
				{},
				wcSettings.pinterest_for_woocommerce.isSetupComplete
					? '/pinterest/catalog'
					: '/pinterest/onboarding'
			)
		);
	};
	return (
		<Card className="woocommerce-table pinterest-for-woocommerce-landing-page__welcome-section">
			<Flex>
				<FlexBlock className="content-block">
					<Text variant="title.medium">
						{ __(
							'Get your products in front of more than 400M people on Pinterest',
							'pinterest-for-woocommerce'
						) }
					</Text>

					<Text variant="body">
						{ __(
							'Pinterest is a visual discovery engine people use to find inspiration for their lives! More than 400 million people have saved more than 300 billion Pins, making it easier to turn inspiration into their next purchase.',
							'pinterest-for-woocommerce'
						) }
					</Text>

					<Text variant="body">
						<Button isPrimary onClick={ handleGetStarted }>
							{ __( 'Get started', 'pinterest-for-woocommerce' ) }
						</Button>
					</Text>

					<Text variant="body">
						{ createInterpolateElement(
							__(
								'By clicking ‘Get started’, you agree to our <a>Terms of Service</a>.',
								'pinterest-for-woocommerce'
							),
							{
								a: (
									// Disabling no-content rule - content is interpolated from above string.
									// eslint-disable-next-line jsx-a11y/anchor-has-content
									<a
										{ ...documentationLinkProps( {
											href: tosHref,
											linkId: 'terms-of-service',
											context: 'welcome-section',
											rel: 'noreferrer',
										} ) }
									/>
								),
							}
						) }
					</Text>
				</FlexBlock>
				<FlexBlock className="image-block">
					<img
						src={
							wcSettings.pinterest_for_woocommerce.pluginUrl +
							'/assets/images/landing_welcome.png'
						}
						alt=""
					/>
				</FlexBlock>
			</Flex>
		</Card>
	);
};

/**
 * Ads Credits Section Card.
 * To be used in getting started page.
 *
 * @fires wcadmin_pfw_modal_open with `{ name: 'ads-credits-terms-and-conditions', … }`
 * @fires wcadmin_pfw_modal_closed with `{ name: 'ads-credits-terms-and-conditions'', … }`
 *
 * @return {JSX.Element} Rendered element.
 */
const AdsCreditSection = () => {
	const [
		isTermsAndConditionsModalOpen,
		setIsTermsAndConditionsModalOpen,
	] = useState( false );

	const openTermsAndConditionsModal = () => {
		setIsTermsAndConditionsModalOpen( true );
		recordEvent( 'pfw_modal_open', {
			context: 'landing-page',
			name: 'ads-credits-terms-and-conditions',
		} );
	};

	const closeTermsAndConditionsModal = () => {
		setIsTermsAndConditionsModalOpen( false );
		recordEvent( 'pfw_modal_closed', {
			context: 'landing-page',
			name: 'ads-credits-terms-and-conditions',
		} );
	};

	const appSettings = useSettingsSelect();
	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;

	return (
		<Card className="woocommerce-table pinterest-for-woocommerce-landing-page__credits-section">
			<Flex>
				<FlexBlock className="image-block">
					<img
						src={
							wcSettings.pinterest_for_woocommerce.pluginUrl +
							'/assets/images/landing_credit.svg'
						}
						alt=""
					/>
				</FlexBlock>
				<FlexBlock className="content-block">
					<Text variant="subtitle">
						{ sprintf(
							// translators: %s: Amount of ad credits given with currency.
							__(
								'Try Pinterest for WooCommerce and get %s in ad credits!',
								'pinterest-for-woocommerce'
							),
							currencyCreditInfo.creditsGiven
						) }
					</Text>
					<Text variant="body">
						{ createInterpolateElement(
							sprintf(
								// translators: %1$s: Amount of ad credits given with currency. %2$s: Amount of money required to spend to claim ad credits with currency.
								__(
									'To help you get started with Pinterest Ads, new Pinterest customers can get %1$s in ad credits when they have successfully set up Pinterest for WooCommerce and spend %2$s on Pinterest Ads. <a>Pinterest Terms and conditions</a> apply.',
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
		</Card>
	);
};

const FeaturesSection = () => {
	return (
		<Card className="woocommerce-table pinterest-for-woocommerce-landing-page__features-section">
			<Flex justify="center" align="top">
				<Feature
					imageUrl={
						wcSettings.pinterest_for_woocommerce.pluginUrl +
						'/assets/images/landing_connect.svg'
					}
					title={ __(
						'Sync your catalog',
						'pinterest-for-woocommerce'
					) }
					text={ __(
						'Connect your store to seamlessly sync your product catalog with Pinterest and create rich pins for each item. Your pins are kept up to date with daily automatic updates.',
						'pinterest-for-woocommerce'
					) }
				/>
				<Feature
					imageUrl={
						wcSettings.pinterest_for_woocommerce.pluginUrl +
						'/assets/images/landing_organic.svg'
					}
					title={ __(
						'Increase organic reach',
						'pinterest-for-woocommerce'
					) }
					text={ __(
						'Pinterest users can easily discover, save and buy products from your website without any advertising spend from you. Track your performance with the Pinterest tag.',
						'pinterest-for-woocommerce'
					) }
				/>
				<Feature
					imageUrl={
						wcSettings.pinterest_for_woocommerce.pluginUrl +
						'/assets/images/landing_catalog.svg'
					}
					title={ __(
						'Create a storefront on Pinterest',
						'pinterest-for-woocommerce'
					) }
					text={ __(
						'Syncing your catalog creates a Shop tab on your Pinterest profile which allows Pinterest users to easily discover your products.',
						'pinterest-for-woocommerce'
					) }
				/>
			</Flex>
		</Card>
	);
};

const Feature = ( { title, text, imageUrl } ) => {
	return (
		<FlexBlock>
			<img src={ imageUrl } alt="" />
			<Text variant="subtitle">{ title }</Text>
			<Text variant="body">{ text }</Text>
		</FlexBlock>
	);
};

const FaqSection = () => {
	const appSettings = useSettingsSelect();
	const currencyCreditInfo = appSettings?.account_data?.currency_credit_info;

	return (
		<Card className="woocommerce-table pinterest-for-woocommerce-landing-page__faq-section">
			<Panel
				header={ __(
					'Frequently asked questions',
					'pinterest-for-woocommerce'
				) }
			>
				<FaqQuestion
					questionId={ 'why-account-not-connected-error' }
					question={ __(
						'Why am I getting an “Account not connected” error message?',
						'pinterest-for-woocommerce'
					) }
					answer={ __(
						'Your password might have changed recently. Click Reconnect Pinterest Account and follow the instructions on screen to restore the connection.',
						'pinterest-for-woocommerce'
					) }
				/>
				<FaqQuestion
					questionId={ 'can-i-connect-to-multiple-accounts' }
					question={ __(
						'I have more than one Pinterest Advertiser account. Can I connect my WooCommerce store to multiple Pinterest Advertiser accounts?',
						'pinterest-for-woocommerce'
					) }
					answer={ __(
						'Only one Pinterest advertiser account can be linked to each WooCommerce store. If you want to connect a different Pinterest advertiser account you will need to either Disconnect the existing Pinterest Advertiser account from your current WooCommerce store and connect a different Pinterest Advertiser account, or Create another WooCommerce store and connect the additional Pinterest Advertiser account.',
						'pinterest-for-woocommerce'
					) }
				/>
				{ currencyCreditInfo && (
					<FaqQuestion
						questionId={ 'how-to-redeem-ad-credits' }
						question={ sprintf(
							// translators: %s: Amount of ad credits given with currency.
							__(
								'How do I redeem the %s ad credit from Pinterest?',
								'pinterest-for-woocommerce'
							),
							currencyCreditInfo.creditsGiven
						) }
						answer={ sprintf(
							// translators: %1$s: Amount of ad credits given with currency. %2$s: Amount of money required to spend to claim ad credits with currency.
							__(
								'To be eligible and redeem the %1$s ad credit from Pinterest, you must complete the setup of Pinterest for WooCommerce, set up your billing with Pinterest Ads manager, and spend %2$s with Pinterest ads. Ad credits may vary by country and is subject to availability. Credits may take up to 24 hours to be credited to the user. Each user is only eligible to receive the ad credits once.',
								'pinterest-for-woocommerce'
							),
							currencyCreditInfo.creditsGiven,
							currencyCreditInfo.spendRequire
						) }
					/>
				) }
			</Panel>
		</Card>
	);
};

/**
 * Clicking on getting started page faq item to collapse or expand it.
 *
 * @event wcadmin_pfw_get_started_faq
 *
 * @property {string} action `'expand' | 'collapse'` What action was initiated.
 * @property {string} question_id Identifier of the clicked question.
 */

/**
 * FAQ component.
 *
 * @fires wcadmin_pfw_get_started_faq whenever the FAQ is toggled.
 * @param {Object} props React props
 * @param {string} props.questionId Question identifier, to be forwarded to the trackign event.
 * @param {string} props.question Text of the question.
 * @param {string} props.answer Text of the answer.
 * @return {JSX.Element} FAQ component.
 */
const FaqQuestion = ( { questionId, question, answer } ) => {
	const panelToggled = useCallback(
		( isOpened ) => {
			recordEvent( 'pfw_get_started_faq', {
				question_id: questionId,
				action: isOpened ? 'expand' : 'collapse',
			} );
		},
		[ questionId ]
	);

	return (
		<PanelBody
			title={ question }
			initialOpen={ false }
			onToggle={ panelToggled }
		>
			<PanelRow>{ answer }</PanelRow>
		</PanelBody>
	);
};

const LandingPageApp = () => {
	const {
		pluginVersion,
		isAdsSupportedCountry,
		storeCountry,
	} = wcSettings.pinterest_for_woocommerce;

	const adsCampaignIsActive = useSettingsSelect()?.ads_campaign_is_active;

	// Only show the pre-launch beta notice if the plugin version is a beta.
	const prelaunchNotice = pluginVersion.includes( 'beta' ) ? (
		<PrelaunchNotice />
	) : null;

	return (
		<>
			{ prelaunchNotice }
			<div className="pinterest-for-woocommerce-landing-page">
				{ ! isAdsSupportedCountry && (
					<UnsupportedCountryNotice countryCode={ storeCountry } />
				) }
				<WelcomeSection />
				{ adsCampaignIsActive && <AdsCreditSection /> }
				<FeaturesSection />
				<FaqSection />
			</div>
		</>
	);
};

export default LandingPageApp;
