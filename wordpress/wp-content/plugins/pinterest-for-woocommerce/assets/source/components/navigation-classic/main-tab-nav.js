/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { getNewPath, getPath } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import AppTabNav from '../app-tab-nav';

const tabs = [
	{
		key: 'catalog',
		title: __( 'Catalog', 'pinterest-for-woocommerce' ),
		href: getNewPath( {}, '/pinterest/catalog', {} ),
	},
	{
		key: 'settings',
		title: __( 'Settings', 'pinterest-for-woocommerce' ),
		href: getNewPath( {}, '/pinterest/settings', {} ),
	},
	{
		key: 'connection',
		title: __( 'Connection', 'pinterest-for-woocommerce' ),
		href: getNewPath( {}, '/pinterest/connection', {} ),
	},
];

const getSelectedTabKey = () => {
	const path = getPath();

	return tabs.find( ( el ) => path.includes( el.key ) )?.key;
};

const MainTabNav = () => {
	useEffect( () => {
		// Highlight the wp-admin dashboard menu
		const marketingMenu = document.querySelector(
			'#toplevel_page_woocommerce-marketing'
		);

		if ( ! marketingMenu ) {
			return;
		}

		const dashboardLink = marketingMenu.querySelector(
			"a[href^='admin.php?page=wc-admin&path=%2Fpinterest%2Fcatalog']"
		);

		marketingMenu.classList.add( 'current', 'wp-has-current-submenu' );
		if ( dashboardLink ) {
			dashboardLink.parentElement.classList.add( 'current' );
		}
	}, [] );

	const selectedKey = getSelectedTabKey();

	return <AppTabNav tabs={ tabs } selectedKey={ selectedKey } />;
};

export default MainTabNav;
