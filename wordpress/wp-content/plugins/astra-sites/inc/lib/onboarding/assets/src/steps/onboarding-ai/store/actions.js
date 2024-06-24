import { objSnakeToCamelCase } from '../utils/helpers';
import * as actionsTypes from './action-types';

const actions = {
	setTogglePopup() {
		return {
			type: 'TOGGLE_POPUP',
		};
	},

	setOnboardingAiPopup() {
		return {
			type: 'SET_ONBOARDING_AI_POPUP',
		};
	},

	setFullWidthPagePreview( fullWidthPagePreview ) {
		return {
			type: 'FULL_WIDTH_PAGE_PREVIEW',
			fullWidthPagePreview,
		};
	},

	setFullWidthBlockPreview( fullWidthBlockPreview ) {
		return {
			type: 'FULL_WIDTH_BLOCK_PREVIEW',
			fullWidthBlockPreview,
		};
	},

	setCurrentScreen( currentScreen ) {
		return {
			type: 'SET_CURRENT_SCREEN',
			currentScreen,
		};
	},

	setPreviousScreen( previousScreen ) {
		return {
			type: 'SET_PREVIOUS_SCREEN',
			previousScreen,
		};
	},

	setSitePreview( sitePreview ) {
		return {
			type: 'SET_SITE_PREVIEW',
			sitePreview,
		};
	},

	setSearchPagePreview( item ) {
		return {
			type: 'SET_SEARCH_PAGE_PREVIEW',
			item,
		};
	},

	setNotice( notice ) {
		return {
			type: 'SET_NOTICE',
			notice,
		};
	},

	setPagePreview( pagePreview ) {
		return {
			type: 'SET_PAGE_PREVIEW',
			pagePreview,
		};
	},

	setFullWidthPreview( fullWidthPreview ) {
		return {
			type: 'SET_FULL_PREVIEW',
			fullWidthPreview,
		};
	},

	setSearchTerm( searchTerm ) {
		return {
			type: 'SEARCH_TERM',
			searchTerm,
		};
	},

	setFilterBlocksByCategory( filterBlocksByCategory ) {
		return {
			type: 'SET_FILTER_BLOCKS_BY_CATEGORY',
			filterBlocksByCategory,
		};
	},

	setFilterBlocksByColor( filterBlocksByColor ) {
		return {
			type: 'SET_FILTER_BLOCKS_BY_COLOR',
			filterBlocksByColor,
		};
	},

	setDefaultBlockPalette( defaultBlockPalette ) {
		return {
			type: 'SET_DEFAULT_BLOCK_PALETTE',
			defaultBlockPalette,
		};
	},

	setActiveBlockPalette( activeBlockPalette ) {
		return {
			type: 'SET_ACTIVE_BLOCK_PALETTE',
			activeBlockPalette,
		};
	},

	setActiveBlockPaletteSlug( activeBlockPaletteSlug ) {
		return {
			type: 'SET_ACTIVE_BLOCK_PALETTE_SLUG',
			activeBlockPaletteSlug,
		};
	},

	setDefaultPagePalette( defaultPagePalette ) {
		return {
			type: 'SET_DEFAULT_PAGE_PALETTE',
			defaultPagePalette,
		};
	},

	setActivePagePalette( activePagePalette ) {
		return {
			type: 'SET_ACTIVE_PAGE_PALETTE',
			activePagePalette,
		};
	},

	setActivePagePaletteSlug( activePagePaletteSlug ) {
		return {
			type: 'SET_ACTIVE_PAGE_PALETTE_SLUG',
			activePagePaletteSlug,
		};
	},

	setImportItemInfo( importItemInfo ) {
		return {
			type: 'SET_IMPORT_ITEM_INFO',
			importItemInfo,
		};
	},

	setFilterBlocksBySearchTerm( filterBlocksBySearchTerm ) {
		return {
			type: 'SET_FILTER_BLOCKS_BY_SEARCH_TERM',
			filterBlocksBySearchTerm,
		};
	},

	setFilterBlocksPagesByCategory( filterBlocksPagesByCategory ) {
		return {
			type: 'SET_FILTER_BLOCKS_PAGES_BY_CATEGORY',
			filterBlocksPagesByCategory,
		};
	},

	setFilterBlocksPagesByColor( filterBlocksPagesByColor ) {
		return {
			type: 'SET_FILTER_BLOCKS_PAGES_BY_COLOR',
			filterBlocksPagesByColor,
		};
	},

	setFilterBlocksPagesBySearchTerm( filterBlocksPagesBySearchTerm ) {
		return {
			type: 'SET_FILTER_BLOCKS_PAGES_BY_SEARCH_TERM',
			filterBlocksPagesBySearchTerm,
		};
	},

	setFilterPagesByPageType( filterPagesByPageType ) {
		return {
			type: 'SET_FILTER_PAGES_BY_PAGE_TYPE',
			filterPagesByPageType,
		};
	},

	setFilterPagesBySearchTerm( filterPagesBySearchTerm ) {
		return {
			type: 'SET_FILTER_PAGES_BY_SEARCH_TERM',
			filterPagesBySearchTerm,
		};
	},

	setAllPages( allPages ) {
		return {
			type: 'SET_ALL_PAGES',
			allPages,
		};
	},

	setAllPatterns( allPatterns ) {
		return {
			type: 'SET_ALL_PATTERNS',
			allPatterns,
		};
	},

	setCurrentCategory( type, category ) {
		return {
			type: 'SET_CURRENT_CATEGORY',
			payload: { type, category },
		};
	},

	setAllCategories( allCategories ) {
		return {
			type: 'SET_ALL_CATEGORIES',
			allCategories,
		};
	},

	setDynamicContent( dynamicContent ) {
		return {
			type: 'SET_DYNAMIC_CONTENT',
			dynamicContent,
		};
	},

	setFavorites( favorites ) {
		return {
			type: 'SET_FAVORITES',
			favorites,
		};
	},

	setState( state ) {
		return {
			type: 'SET_STATE',
			state,
		};
	},

	setDisplayDynamicPopup( displayDynamicPopup ) {
		return {
			type: 'SET_DISPLAY_DYNAMIC_POPUP',
			displayDynamicPopup,
		};
	},

	toggleOnboardingAIStep( value ) {
		return {
			type: actionsTypes.TOGGLE_ONBOARDING_AI_STEP,
			...( !! value && { payload: value } ),
		};
	},

	setNextAIStep() {
		return {
			type: actionsTypes.SET_NEXT_AI_STEP,
		};
	},

	setAIStep( step ) {
		return {
			type: actionsTypes.SET_AI_STEP,
			step,
		};
	},

	setPreviousAIStep() {
		return {
			type: actionsTypes.SET_PREVIOUS_AI_STEP,
		};
	},

	setTokenStep( token ) {
		return {
			type: actionsTypes.SET_OPEN_AI_API_KEY_AI_STEP,
			payload: token,
		};
	},

	setWebsiteOnboardingAIDetails( onboardingAI ) {
		return {
			type: actionsTypes.SET_WEBSITE_ONBOARDING_AI_DETAILS,
			payload: onboardingAI,
		};
	},

	setBusinessTypeListAIStep( businessTypeList ) {
		return {
			type: actionsTypes.SET_WEBSITE_TYPE_LIST_AI_STEP,
			payload: businessTypeList,
		};
	},

	setSiteLanguageListAIStep( siteLanguageList ) {
		return {
			type: actionsTypes.SET_WEBSITE_LANGUAGE_LIST_AI_STEP,
			payload: siteLanguageList,
		};
	},

	setWebsiteVersionList( websiteVersionList ) {
		return {
			type: actionsTypes.SET_WEBSITE_VERSION_LIST,
			payload: websiteVersionList,
		};
	},
	setSelectedWebsiteVersion( selectedWebsiteVersion ) {
		return {
			type: actionsTypes.SET_SELECTED_WEBSITE_VERSION,
			payload: selectedWebsiteVersion,
		};
	},

	setLimitExceedModal( limitExceedModal ) {
		return {
			type: actionsTypes.SET_LIMIT_EXCEED_MODAL,
			payload: limitExceedModal,
		};
	},
	setAuthenticationErrorModal( authenticationErrorModal ) {
		return {
			type: actionsTypes.SET_AUTHENTICATION_ERROR_MODAL,
			payload: authenticationErrorModal,
		};
	},
	setContinueProgressModal( continueProgressModal ) {
		return {
			type: actionsTypes.SET_CONTINUE_PROGRESS_MODAL,
			payload: continueProgressModal,
		};
	},

	setWebsiteTypeAIStep( websiteType ) {
		return {
			type: actionsTypes.SET_WEBSITE_TYPE_AI_STEP,
			payload: websiteType,
		};
	},

	setWebsiteLanguageAIStep( siteLanguage ) {
		return {
			type: actionsTypes.SET_WEBSITE_LANGUAGE_AI_STEP,
			payload: siteLanguage,
		};
	},

	setWebsiteNameAIStep( websiteName ) {
		return {
			type: actionsTypes.SET_WEBSITE_NAME_AI_STEP,
			payload: websiteName,
		};
	},

	setWebsiteDetailsAIStep( websiteDetails ) {
		return {
			type: actionsTypes.SET_WEBSITE_DETAILS_AI_STEP,
			payload: websiteDetails,
		};
	},

	setWebsiteKeywordsAIStep( websiteKeywords ) {
		return {
			type: actionsTypes.SET_WEBSITE_KEYWORDS_AI_STEP,
			payload: websiteKeywords,
		};
	},

	setWebsiteImagesAIStep( websiteImages ) {
		return {
			type: actionsTypes.SET_WEBSITE_IMAGES_AI_STEP,
			payload: websiteImages,
		};
	},

	setWebsiteImagesPreSelectedAIStep( websiteImagesPreSelected ) {
		return {
			type: actionsTypes.SET_WEBSITE_IMAGES_PRE_SELECTED_AI_STEP,
			payload: websiteImagesPreSelected,
		};
	},

	resetKeywordsImagesAIStep() {
		return {
			type: actionsTypes.RESET_KEYWORDS_IMAGES_AI_STEP,
		};
	},

	setWebsiteContactAIStep( websiteContact ) {
		return {
			type: actionsTypes.SET_WEBSITE_CONTACT_AI_STEP,
			payload: websiteContact,
		};
	},

	setWebsiteTemplatesAIStep( templateList ) {
		return {
			type: actionsTypes.SET_WEBSITE_TEMPLATES_AI_STEP,
			payload: templateList,
		};
	},

	setWebsiteTemplateKeywords( templateKeywords ) {
		return {
			type: actionsTypes.SET_WEBSITE_TEMPLATE_KEYWORDS,
			payload: templateKeywords,
		};
	},

	setWebsiteTemplateSearchResultsAIStep( templateSearchResults ) {
		const uuidSet = new Set(); // Set to keep track of unique UUIDs

		templateSearchResults.forEach( ( result ) => {
			result.designs = result?.designs?.filter( ( template ) => {
				if ( ! uuidSet.has( template.uuid ) ) {
					uuidSet.add( template.uuid );
					return true;
				}
				return false;
			} );
		} );

		return {
			type: actionsTypes.SET_WEBSITE_TEMPLATE_RESULTS_AI_STEP,
			payload: templateSearchResults,
		};
	},

	setWebsiteSelectedTemplateAIStep( selectedTemplate ) {
		return {
			type: actionsTypes.SET_WEBSITE_SELECTED_TEMPLATE_AI_STEP,
			payload: selectedTemplate,
		};
	},
	setWebsiteInfoAIStep( websiteInfo ) {
		return {
			type: actionsTypes.SET_WEBSITE_DATA_AI_STEP,
			payload: websiteInfo,
		};
	},

	resetOnboardingAISteps() {
		return {
			type: actionsTypes.RESET_ONBOARDING_AI_STEPS,
		};
	},

	setOnboardingAIDetails( onboardingAI ) {
		return {
			type: actionsTypes.SET_ONBOARDING_AI_DETAILS,
			payload: onboardingAI,
		};
	},

	toggleDisableAiContent( value ) {
		return {
			type: actionsTypes.TOGGLE_DISABLE_AI_CONTENT,
			...( !! value && { payload: value } ),
		};
	},

	toggleDisableLivePreview( value ) {
		return {
			type: actionsTypes.TOGGLE_DISABLE_LIVE_PREVIEW,
			...( !! value && { payload: value } ),
		};
	},

	dynamicContentSyncStart( ...value ) {
		return {
			type: actionsTypes.DYNAMIC_CONTENT_SYNC_START,
			payload: value,
		};
	},

	dynamicContentSyncComplete( ...value ) {
		return {
			type: actionsTypes.DYNAMIC_CONTENT_SYNC_COMPLETE,
			payload: value,
		};
	},

	dynamicContentReSyncStatus() {
		return {
			type: actionsTypes.DYNAMIC_CONTENT_RESYNC_STATUS,
		};
	},

	dynamicContentFlagSet( key, value ) {
		return {
			type: actionsTypes.DYNAMIC_CONTENT_FLAG_SET,
			payload: { key, value },
		};
	},

	dynamicContentFlagReset( type, flags ) {
		const payload = {
			type,
		};

		if ( flags ) {
			payload.flags = flags;
		}

		return {
			type: actionsTypes.DYNAMIC_CONTENT_FLAGS_RESET,
			payload,
		};
	},

	setAllBlocksData( payload ) {
		return {
			type: 'SET_ALL_BLOCKS',
			payload,
		};
	},

	setRegeneratingContentCategory( regeneratingContentCategory ) {
		return {
			type: 'SET_REGENERATING_CONTENT_CATEGORY',
			regeneratingContentCategory,
		};
	},

	setImportInProgress( value ) {
		return {
			type: actionsTypes.SET_IMPORT_IN_PROGRESS,
			payload: value,
		};
	},

	setSpecAiTogglePopup( specAiTogglePopup ) {
		return {
			type: 'SET_SPEC_AI_TOGGLE_POPUP',
			specAiTogglePopup,
		};
	},

	setShowPagesOnboarding() {
		return {
			type: actionsTypes.SET_SHOW_PAGES_ONBOARDING,
		};
	},

	setCreditsDetails( payload ) {
		return {
			type: actionsTypes.SET_CREDITS_DETAILS,
			payload: objSnakeToCamelCase( payload ),
		};
	},

	setIsNewUserOnboarding() {
		return {
			type: actionsTypes.SET_IS_NEW_USER_ONBOARDING,
		};
	},

	toggleUpdateOnboardingImages() {
		return {
			type: actionsTypes.TOGGLE_UPDATE_ONBOARDING_IMAGES,
		};
	},

	storeSiteFeatures( payload ) {
		return {
			type: actionsTypes.STORE_SITE_FEATURES,
			payload,
		};
	},

	setSiteFeatures( payload ) {
		return {
			type: actionsTypes.SET_SITE_FEATURES,
			payload,
		};
	},

	setLoadingNextStep( payload ) {
		return {
			type: actionsTypes.LOADING_NEXT_STEP,
			payload,
		};
	},
};

export default actions;
