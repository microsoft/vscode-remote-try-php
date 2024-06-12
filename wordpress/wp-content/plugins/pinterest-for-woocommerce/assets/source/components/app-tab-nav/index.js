/**
 * External dependencies
 */
import { NavigableMenu } from '@wordpress/components';
import { Link } from '@woocommerce/components';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

const TabLink = ( { tabId, href, children, selected, ...rest } ) => {
	return (
		<Link
			role="tab"
			tabIndex={ selected ? null : -1 }
			aria-selected={ selected }
			id={ tabId }
			href={ href }
			{ ...rest }
		>
			{ children }
		</Link>
	);
};

const AppTabNav = ( props ) => {
	const { selectedKey, tabs } = props;

	return (
		<div className="app-tab-nav">
			<NavigableMenu
				role="tablist"
				orientation="horizontal"
				className="app-tab-nav__tabs"
			>
				{ tabs.map( ( tab ) => (
					<TabLink
						className={ classnames(
							'components-button',
							'app-tab-nav__tabs-item',
							{
								'is-active': tab.key === selectedKey,
							}
						) }
						tabId={ `${ tab.key }` }
						aria-controls={ `${ tab.key }-view` }
						selected={ tab.key === selectedKey }
						key={ tab.key }
						href={ tab.href }
					>
						{ tab.title }
					</TabLink>
				) ) }
			</NavigableMenu>
		</div>
	);
};

export default AppTabNav;
