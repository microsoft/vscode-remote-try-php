const selectors = {
	getOnboardingAI( state ) {
		return state;
	},

	getAllPatternsCategories( { allPatternsCategories } ) {
		return allPatternsCategories;
	},

	getDynamicContent( { dynamicContent } ) {
		return dynamicContent;
	},

	getCurrentAIStep( { currentStep } ) {
		return currentStep;
	},

	getAIStepData( { stepData } ) {
		return stepData;
	},
	getWebsiteInfo( { websiteInfo } ) {
		return websiteInfo;
	},
	getWebsiteVersionList( { websiteVersionList } ) {
		return websiteVersionList;
	},
	getSelectedWebsiteVersion( { selectedWebsiteVersion } ) {
		return selectedWebsiteVersion;
	},
	getLimitExceedModalInfo( { limitExceedModal } ) {
		return limitExceedModal;
	},
	getContinueProgressModalInfo( { continueProgressModal } ) {
		return continueProgressModal;
	},
	getDisableAi( { disableAi } ) {
		return disableAi;
	},

	getDisablePreview( { disablePreview } ) {
		return disablePreview;
	},
	getRegeneratingContentCategory( { regeneratingContentCategory } ) {
		return regeneratingContentCategory;
	},

	getImportInProgress( { importInProgress } ) {
		return importInProgress;
	},

	getSpecAITogglePopup( { specAITogglePopup } ) {
		return specAITogglePopup;
	},

	getShowPagesOnboarding( { showPagesOnboarding } ) {
		return showPagesOnboarding;
	},

	getCreditsDetails( { credits } ) {
		return credits;
	},

	getSiteFeatures( { stepData: { siteFeatures } } ) {
		return siteFeatures;
	},

	getSiteLogo( { stepData: { siteLogo } } ) {
		return siteLogo;
	},

	getActiveColorPalette( { stepData: { activeColorPalette } } ) {
		return activeColorPalette;
	},

	getActiveTypography( { stepData: { activeTypography } } ) {
		return activeTypography;
	},

	getImportSiteProgressData( { importSiteProgressData } ) {
		return importSiteProgressData;
	},

	getDefaultColorPalette( { stepData: { defaultColorPalette } } ) {
		return defaultColorPalette;
	},

	getLoadingNextStep( { loadingNextStep } ) {
		return loadingNextStep;
	},
};

export default selectors;
