/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';

/**
 * Internal dependencies
 */
import documentationLinkProps from '../../setup-guide/app/helpers/documentation-link-props';

/**
 * Renders a notice for Beta versions
 *
 * @fires wcadmin_pfw_get_started_notice_link_click `{ context: 'pinterest-landing', link_id: 'prelaunch-notice' }`
 * @return {JSX.Element} The rendered component
 */
const PrelaunchNotice = () => {
	return (
		<Notice
			status="warning"
			isDismissible={ false }
			className="pinterest-for-woocommerce-prelaunch-notice"
		>
			<h3>
				{ __(
					'Pinterest for WooCommerce is a limited beta.',
					'pinterest-for-woocommerce'
				) }
			</h3>
			<p>
				{ __(
					'The integration is only available to approved stores participating in the beta program.',
					'pinterest-for-woocommerce'
				) }
			</p>
			<p>
				<a
					{ ...documentationLinkProps( {
						href:
							wcSettings.pinterest_for_woocommerce.pinterestLinks
								.preLaunchNotice,
						eventName: 'pfw_get_started_notice_link_click',
						linkId: 'prelaunch-notice',
						context: 'pinterest-landing',
						rel: 'noreferrer',
					} ) }
				>
					{ __(
						'Click here for more information.',
						'pinterest-for-woocommerce'
					) }
				</a>
			</p>
		</Notice>
	);
};

export default PrelaunchNotice;
