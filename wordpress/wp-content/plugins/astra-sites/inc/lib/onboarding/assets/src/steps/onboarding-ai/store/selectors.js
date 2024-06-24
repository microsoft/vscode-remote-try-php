const selectors = {
	getTogglePopup( { togglePopup } ) {
		return togglePopup;
	},

	getAllWireframes( { allWireframes } ) {
		return allWireframes;
	},

	getAllPatterns( { allPatterns } ) {
		return allPatterns;
	},

	getAllPatternsCategories( { allPatternsCategories } ) {
		return allPatternsCategories;
	},

	getAllPagesCategories( { allPagesCategories } ) {
		return allPagesCategories;
	},

	getDynamicContentSyncFlags( { dynamicContentSyncFlags } ) {
		return dynamicContentSyncFlags;
	},

	getDynamicContentSyncStatus( { dynamicContentSyncStatus } ) {
		return dynamicContentSyncStatus;
	},

	getDynamicContentReSyncStatus( { dynamicContentReSyncStatus } ) {
		return dynamicContentReSyncStatus;
	},

	getAllBlocksPages( { allBlocksPages } ) {
		return allBlocksPages;
	},

	getAllCategories( { allCategories } ) {
		return allCategories;
	},

	getDynamicContent( { dynamicContent } ) {
		return dynamicContent;
	},

	getCurrentCategory( { currentCategory } ) {
		return currentCategory;
	},

	getFavorites( { favorites } ) {
		return favorites;
	},

	getAllBlocks( { allBlocks } ) {
		return allBlocks;
	},

	getAllSites( { allSites } ) {
		return allSites;
	},

	getCount( { count } ) {
		return count;
	},

	getCurrentScreen( { currentScreen } ) {
		return currentScreen;
	},

	getPreviousScreen( { previousScreen } ) {
		return previousScreen;
	},

	getSearchTerm( { searchTerm } ) {
		return searchTerm;
	},

	getSitePreview( { sitePreview } ) {
		return sitePreview;
	},

	getNotice( { notice } ) {
		return notice;
	},

	getImportItemInfo( { importItemInfo } ) {
		return importItemInfo;
	},

	getPagePreview( { pagePreview } ) {
		return pagePreview;
	},

	getFullWidthPreview( { fullWidthPreview } ) {
		return fullWidthPreview;
	},

	getFilterBlocksByCategory( { filterBlocksByCategory } ) {
		return filterBlocksByCategory;
	},
	getFilterBlocksByColor( { filterBlocksByColor } ) {
		return filterBlocksByColor;
	},
	getDefaultBlockColorPalette( { defaultBlockPalette } ) {
		return defaultBlockPalette;
	},
	getActiveBlockPalette( { activeBlockPalette } ) {
		return activeBlockPalette;
	},
	getActiveBlockPaletteSlug( { activeBlockPaletteSlug } ) {
		return activeBlockPaletteSlug;
	},
	getDefaultPageColorPalette( { defaultPagePalette } ) {
		return defaultPagePalette;
	},
	getActivePagePalette( { activePagePalette } ) {
		return activePagePalette;
	},
	getActivePagePaletteSlug( { activePagePaletteSlug } ) {
		return activePagePaletteSlug;
	},
	getFilterBlocksBySearchTerm( { filterBlocksBySearchTerm } ) {
		return filterBlocksBySearchTerm;
	},
	getFilterPagesByPageType( { filterPagesByPageType } ) {
		return filterPagesByPageType;
	},
	getFilterPagesBySearchTerm( { filterPagesBySearchTerm } ) {
		return filterPagesBySearchTerm;
	},

	getFilterBlocksPagesByCategory( { filterBlocksPagesByCategory } ) {
		return filterBlocksPagesByCategory;
	},
	getFilterBlocksPagesByColor( { filterBlocksPagesByColor } ) {
		return filterBlocksPagesByColor;
	},
	getFilterBlocksPagesBySearchTerm( { filterBlocksPagesBySearchTerm } ) {
		return filterBlocksPagesBySearchTerm;
	},
	getFullWidthPagePreview( { fullWidthPagePreview } ) {
		return fullWidthPagePreview;
	},
	getFullWidthBlockPreview( { fullWidthBlockPreview } ) {
		return fullWidthBlockPreview;
	},

	getAllPages( { allPages } ) {
		return allPages;
	},

	getBlockSearchInput( { blockSearchInput } ) {
		return blockSearchInput;
	},

	getDisplayDynamicPopup( { displayDynamicPopup } ) {
		return displayDynamicPopup;
	},

	getOnboardingAI( { onboardingAI } ) {
		return onboardingAI;
	},

	getCurrentAIStep( { onboardingAI: { currentStep } } ) {
		return currentStep;
	},

	getAIStepData( { onboardingAI: { stepData } } ) {
		return stepData;
	},
	getWebsiteInfo( { onboardingAI: { websiteInfo } } ) {
		return websiteInfo;
	},
	getWebsiteVersionList( { onboardingAI: { websiteVersionList } } ) {
		return websiteVersionList;
	},
	getSelectedWebsiteVersion( { onboardingAI: { selectedWebsiteVersion } } ) {
		return selectedWebsiteVersion;
	},
	getLimitExceedModalInfo( { onboardingAI: { limitExceedModal } } ) {
		return limitExceedModal;
	},
	getAuthenticationErrorModalInfo( {
		onboardingAI: { authenticationErrorModal },
	} ) {
		return authenticationErrorModal;
	},
	getContinueProgressModalInfo( {
		onboardingAI: { continueProgressModal },
	} ) {
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

	getSiteFeatures( {
		onboardingAI: {
			stepData: { siteFeatures },
		},
	} ) {
		return siteFeatures;
	},

	getLoadingNextStep( { onboardingAI: { loadingNextStep } } ) {
		return loadingNextStep;
	},
};

export default selectors;
