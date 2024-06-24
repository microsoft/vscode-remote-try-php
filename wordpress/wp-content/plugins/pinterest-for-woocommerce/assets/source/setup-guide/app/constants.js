/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';

/**
 * Internal dependencies
 */
import documentationLinkProps from './helpers/documentation-link-props';

/**
 * Enum of general label status.
 *
 * @readonly
 * @enum {string}
 */
export const LABEL_STATUS = Object.freeze( {
	PENDING: 'pending',
	SUCCESS: 'success',
} );

/**
 * Enum of general process status.
 *
 * @readonly
 * @enum {string}
 */
export const PROCESS_STATUS = Object.freeze( {
	...LABEL_STATUS,
	IDLE: 'idle',
	ERROR: 'error',
} );

/**
 * Enum of the disapproval reasons for merchants.
 */
export const DISAPPROVAL_COPY_STATES = Object.freeze( {
	MARKETPLACE: __(
		'Merchant is an affiliate or resale marketplace',
		'pinterest-for-woocommerce'
	),
	MARKETPLACE_SECOND_HAND: __(
		'Merchant is a resale marketplace',
		'pinterest-for-woocommerce'
	),
	MARKETPLACE_AFFILIATES: __(
		'Merchant is an affiliate marketplace or marketer',
		'pinterest-for-woocommerce'
	),
	MARKETPLACE_WHOLESALE: __(
		'Merchant is a wholesale seller',
		'pinterest-for-woocommerce'
	),
	PROHIBITED_PRODUCTS: __(
		'Merchant does not meet our policy on prohibited products',
		'pinterest-for-woocommerce'
	),
	SERVICES: __(
		'Merchant offers services rather than products',
		'pinterest-for-woocommerce'
	),
	DOMAIN_AGE: __(
		"Merchant's domain age does not meet minimum requirement",
		'pinterest-for-woocommerce'
	),
	DOMAIN_MISMATCH: __(
		'Merchant domain mismatched with merchant account',
		'pinterest-for-woocommerce'
	),
	SHIPPING: __(
		"Merchant's shipping policy is unclear or unavailable",
		'pinterest-for-woocommerce'
	),
	NO_SHIPPING_POLICY: __(
		"Merchant's shipping policy is unclear or unavailable",
		'pinterest-for-woocommerce'
	),
	RETURNS: __(
		"Merchant's returns policy is unclear or unavailable",
		'pinterest-for-woocommerce'
	),
	NO_RETURN_POLICY: __(
		"Merchant's returns policy is unclear or unavailable",
		'pinterest-for-woocommerce'
	),
	BROKEN_URL: __(
		"Merchant's URL is broken or requires registration",
		'pinterest-for-woocommerce'
	),
	BROKEN_404: __(
		"Merchant's domain URL is broken",
		'pinterest-for-woocommerce'
	),
	BROKEN_REGISTRATION: __(
		"Merchant's domain requires registration to view products",
		'pinterest-for-woocommerce'
	),
	INCOMPLETE: __(
		"Merchant's URL is incomplete",
		'pinterest-for-woocommerce'
	),
	AUTHENTICITY: __(
		"Merchant's domain does not meet brand information requirements",
		'pinterest-for-woocommerce'
	),
	AUTHENTICITY_NO_SOCIALS_OR_ABOUT: __(
		"There is no 'About Us' page or no social information in your website",
		'pinterest-for-woocommerce'
	),
	AUTHENTICITY_NO_CONTACT_INFORMATION: __(
		'There is no contact information in your website',
		'pinterest-for-woocommerce'
	),
	IN_STOCK: __(
		"Merchant's products are out of stock",
		'pinterest-for-woocommerce'
	),
	BANNER_ADS: __(
		"Merchant's website includes banner or pop-up ads",
		'pinterest-for-woocommerce'
	),
	IMAGE_QUALITY: __(
		"Merchant's products do not meet image quality requirements",
		'pinterest-for-woocommerce'
	),
	LOW_QUALITY_IMAGERY: __(
		"Merchant's products do not meet image quality requirements",
		'pinterest-for-woocommerce'
	),
	WATERMARKS: __(
		"Merchant's product images include watermarks",
		'pinterest-for-woocommerce'
	),
	SALE: __(
		"Merchant's products are always on sale",
		'pinterest-for-woocommerce'
	),
	OUT_OF_DATE: __(
		"Merchant's products refer to outdated content",
		'pinterest-for-woocommerce'
	),
	PRODUCT_DESCRIPTION: __(
		"Merchant's website uses generic product descriptions",
		'pinterest-for-woocommerce'
	),
	PRODUCTS: __(
		"Merchant's product descriptions and categories do not meet requirements",
		'pinterest-for-woocommerce'
	),
	POP_UP: __(
		"Merchant's website displays several pop-up messages",
		'pinterest-for-woocommerce'
	),
	MINIMUM_WEBSITE_QUALITY: __(
		'Merchant does not meet minimum website quality requirements',
		'pinterest-for-woocommerce'
	),
	BROKEN_SOCIAL_MEDIA: __(
		"Merchant's social media links are broken",
		'pinterest-for-woocommerce'
	),
	TERMINATED: __(
		"We're unable to include you as a merchant at this time. We determined your account doesn't comply with our guidelines. Your full catalog has been removed from Pinterest.",
		'pinterest-for-woocommerce'
	),
	INAUTHENTIC_PHOTOS: __(
		"Merchant's product images are unavailable or mismatched",
		'pinterest-for-woocommerce'
	),
	INACTIVE_FEED: createInterpolateElement(
		__(
			'We recently updated our <merchantGuidelinesLink>merchant guidelines</merchantGuidelinesLink> and have found that your account is currently not in compliance with our guidelines. Merchants who do not comply with our guidelines will not be able to distribute or promote product Pins from their catalog on Pinterest. If you’d like to appeal this decision, review our guidelines for more detailed information on how you can get your products on Pinterest.',
			'pinterest-for-woocommerce'
		),
		{
			merchantGuidelinesLink: (
				// Disabling no-content rule - content is interpolated from above string.
				// eslint-disable-next-line jsx-a11y/anchor-has-content
				<a
					{ ...documentationLinkProps( {
						href:
							wcSettings.pinterest_for_woocommerce.pinterestLinks
								.merchantGuidelines,
						linkId: 'merchant-guidelines',
						context: 'merchant-disapproval-reasons',
						rel: 'noreferrer',
					} ) }
				/>
			),
		}
	),
	RESALE_MARKETPLACE: __(
		'Resale marketplaces are not allowed',
		'pinterest-for-woocommerce'
	),
	AFFILIATE_MARKETPLACE: __(
		'Affiliate links are not allowed',
		'pinterest-for-woocommerce'
	),
	WEBSITE_REQUIREMENTS: __(
		'Account does not meet the website requirements for verification',
		'pinterest-for-woocommerce'
	),
	PRODUCT_REQUIREMENTS: __(
		'Account does not meet the product requirements for verification',
		'pinterest-for-woocommerce'
	),
	BRAND_REPUTATION: __(
		'Account does not meet the brand reputation criteria for verification',
		'pinterest-for-woocommerce'
	),
	INCOMPLETE_WEBSITE_TEMPLATE: __(
		'The template of the website is incomplete',
		'pinterest-for-woocommerce'
	),
	TS: __(
		'Merchant does not meet community guidelines',
		'pinterest-for-woocommerce'
	),
	ADS_QUALITY: __(
		'Merchant has exceeded number of reported ads',
		'pinterest-for-woocommerce'
	),
	PINNER: __(
		'Merchant has exceeded number of user reports',
		'pinterest-for-woocommerce'
	),
	SHOPPING: __(
		'Merchant does not meet minimum product requirements',
		'pinterest-for-woocommerce'
	),
	MERCHANT: __(
		'Merchant does not meet minimum website quality requirements',
		'pinterest-for-woocommerce'
	),
} );
