/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { getNewPath } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import TopBar from '../components/top-bar';

const OnboardingTopBar = () => {
	return (
		<TopBar
			title={ __(
				'Get started with Pinterest for WooCommerce',
				'pinterest-for-woocommerce'
			) }
			backHref={ getNewPath( {}, '/pinterest/landing' ) }
		/>
	);
};

export default OnboardingTopBar;
