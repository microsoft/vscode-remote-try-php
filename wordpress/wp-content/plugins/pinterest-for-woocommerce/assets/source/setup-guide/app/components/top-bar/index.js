/**
 * External dependencies
 */
import { Link } from '@woocommerce/components';
import { Icon, chevronLeft } from '@wordpress/icons';

/**
 * Simple top bar with back button and title,
 * to be used when configuring a campaign during oboarding and later.
 *
 * @param {Object} props
 * @param {string} props.title Title to indicate where the user is at.
 * @param {import(".~/components/app-button").default} props.helpButton Help button
 * @param {string} props.backHref Href for the back button.
 * @param {Function} props.onBackButtonClick
 */
const TopBar = ( { title, backHref, helpButton, onBackButtonClick } ) => {
	return (
		<div className="woocommerce-layout__header">
			<div className="woocommerce-layout__header-wrapper">
				<Link
					className="woocommerce-layout__header-back-button"
					href={ backHref }
					type="wc-admin"
					onClick={ onBackButtonClick }
				>
					<Icon icon={ chevronLeft } onClick={ onBackButtonClick } />
				</Link>
				<div className="woocommerce-layout__header-heading with-back-button">
					<span>{ title }</span>
					<div>{ helpButton }</div>
				</div>
			</div>
		</div>
	);
};

export default TopBar;
