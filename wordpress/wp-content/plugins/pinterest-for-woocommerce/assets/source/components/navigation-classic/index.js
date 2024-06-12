/**
 * Internal dependencies
 */
import MainTabNav from './main-tab-nav';

/**
 * Display tab-based navigation when the new WC Navigation is not enabled.
 *
 * @return {import("./main-tab-nav").default} Retuns MainTabNav if WC Navigation is not enabled.
 */
const NavigationClassic = () => {
	const navigationEnabled = !! window.wcAdminFeatures?.navigation;

	return navigationEnabled ? null : <MainTabNav />;
};

export default NavigationClassic;
