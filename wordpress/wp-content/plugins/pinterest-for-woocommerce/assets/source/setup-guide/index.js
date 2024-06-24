/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
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
import LandingPageApp from './app/views/LandingPageApp';
import WizardApp from './app/views/WizardApp';
import ConnectionApp from './app/views/ConnectionApp';
import SettingsApp from './app/views/SettingsApp';
import CatalogSyncApp from '../catalog-sync/App';

import './app/style.scss';

const woocommerceTranslation =
	// Pre WC 5.8
	getSetting( 'woocommerceTranslation' ) ||
	// WC 5.8+
	getSetting( 'admin' )?.woocommerceTranslation;

addFilter(
	'woocommerce_admin_pages_list',
	'woocommerce-marketing',
	( pages ) => {
		const navigationEnabled = !! window.wcAdminFeatures?.navigation;
		const initialBreadcrumbs = [ [ '', woocommerceTranslation ] ];

		/**
		 * If the WooCommerce Navigation feature is not enabled,
		 * we want to display the plugin under WC Marketing;
		 * otherwise, display it under WC Navigation - Extensions.
		 */
		if ( ! navigationEnabled ) {
			initialBreadcrumbs.push( [
				'/marketing',
				__( 'Marketing', 'pinterest-for-woocommerce' ),
			] );
		}

		initialBreadcrumbs.push(
			__( 'Pinterest', 'pinterest-for-woocommerce' )
		);

		pages.push( {
			container: LandingPageApp,
			path: '/pinterest/landing',
			breadcrumbs: [ 'Pinterest' ],
			wpOpenMenu: 'toplevel_page_woocommerce-marketing',
			navArgs: {
				id: 'pinterest-for-woocommerce-landing-page',
			},
		} );

		pages.push( {
			container: WizardApp,
			path: '/pinterest/onboarding',
			breadcrumbs: [
				...initialBreadcrumbs,
				__( 'Onboarding Guide', 'pinterest-for-woocommerce' ),
			],
			navArgs: {
				id: 'pinterest-for-woocommerce-setup-guide',
			},
		} );

		pages.push( {
			container: ConnectionApp,
			path: '/pinterest/connection',
			breadcrumbs: [
				...initialBreadcrumbs,
				__( 'Connection', 'pinterest-for-woocommerce' ),
			],
			wpOpenMenu: 'toplevel_page_woocommerce-marketing',
			navArgs: {
				id: 'pinterest-for-woocommerce-connection',
			},
		} );

		pages.push( {
			container: SettingsApp,
			path: '/pinterest/settings',
			breadcrumbs: [
				...initialBreadcrumbs,
				__( 'Settings', 'pinterest-for-woocommerce' ),
			],
			wpOpenMenu: 'toplevel_page_woocommerce-marketing',
			navArgs: {
				id: 'pinterest-for-woocommerce-settings',
			},
		} );

		pages.push( {
			container: CatalogSyncApp,
			path: '/pinterest/catalog',
			breadcrumbs: [
				...initialBreadcrumbs,
				__( 'Products Catalog', 'pinterest-for-woocommerce' ),
			],
			wpOpenMenu: 'toplevel_page_woocommerce-marketing',
			navArgs: {
				id: 'pinterest-for-woocommerce-catalog',
			},
		} );

		return pages;
	}
);
