/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';
import { Notice, ExternalLink } from '@wordpress/components';
import { Link } from '@woocommerce/components';
import { getSetting } from '@woocommerce/settings'; // eslint-disable-line import/no-unresolved
// The above is an unpublished package, delivered with WC, we use Dependency Extraction Webpack Plugin to import it.
// See https://github.com/woocommerce/woocommerce-admin/issues/7781,
// https://github.com/woocommerce/woocommerce-admin/issues/7810
// Please note, that this is NOT https://www.npmjs.com/package/@woocommerce/settings,
// or https://github.com/woocommerce/woocommerce-admin/tree/main/packages/wc-admin-settings
// but https://github.com/woocommerce/woocommerce-gutenberg-products-block/blob/trunk/assets/js/settings/shared/index.ts
// (at an unknown version).

/**
 * Internal dependencies
 */
import './style.scss';
import documentationLinkProps from '../../helpers/documentation-link-props';

/**
 * Renders an unsupported country <Notice> with warning appearance.
 *
 * @fires wcadmin_pfw_get_started_notice_link_click with `{ context: 'pinterest-landing', linkId: 'ads-availability' | 'unsupported-country-link' }`
 *
 * @param {Object} props React props.
 * @param {string} props.countryCode The alpha-2 country code to map the country name.
 */
function UnsupportedCountryNotice( { countryCode } ) {
	const countryName = getSetting( 'countries', {} )[ countryCode ];

	if ( ! countryName ) {
		return null;
	}

	return (
		<Notice
			className="pins-for-woo-unsupported-country-notice"
			status="warning"
			isDismissible={ false }
		>
			{ createInterpolateElement(
				__(
					'Your store’s country is <country />. This country is currently not supported by Pinterest for WooCommerce. However, you can still choose to list your products in supported countries, if you are able to sell your products to customers there. <settingsLink>Change your store’s country here</settingsLink>. <supportedCountriesLink>Read more about supported countries</supportedCountriesLink>',
					'pinterest-for-woocommerce'
				),
				{
					country: <strong>{ countryName }</strong>,
					settingsLink: (
						<Link
							{ ...documentationLinkProps( {
								href: '/wp-admin/admin.php?page=wc-settings',
								eventName: 'pfw_get_started_notice_link_click',
								linkId: 'unsupported-country-link',
								context: 'pinterest-landing',
							} ) }
							className="pins-for-woo-unsupported-country-notice__link"
							type="wp-admin"
						/>
					),
					supportedCountriesLink: (
						<ExternalLink
							{ ...documentationLinkProps( {
								href:
									wcSettings.pinterest_for_woocommerce
										.pinterestLinks.adsAvailability,
								eventName: 'pfw_get_started_notice_link_click',
								linkId: 'ads-availability',
								context: 'pinterest-landing',
							} ) }
							className="pins-for-woo-unsupported-country-notice__link"
						/>
					),
				}
			) }
		</Notice>
	);
}

export default UnsupportedCountryNotice;
