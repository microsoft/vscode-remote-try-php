/** global wp, astraSitesVars; */
import {
	getPatterns,
	getWireframes,
	getDefaultBlockPalette,
	getDefaultPagePalette,
	getBlocksPages,
} from '../utils/functions';
import {
	getFromSessionStorage,
	getPatternsWithCategories,
	getPagesWithCategories,
	updateSequenceByCategory,
	objSnakeToCamelCase,
} from '../utils/helpers';
import { SESSION_STORAGE_KEY } from '../utils/constants';
import {
	filterPatterns,
	filterWireframes,
	filterBlocksPages,
	filterBlocks,
} from '../utils/filter-blocks';
import { filterPages } from '../utils/filter-pages';
import { getLocalStorageItem } from '../helpers';
import { TOTAL_STEPS } from '../onboarding-ai';
import * as actionTypes from './action-types';
import { omit } from 'lodash';

const aiStepValues = astraSitesVars?.business_details;
const patternsAndCategories = getPatternsWithCategories(
	getPatterns(),
	astraSitesVars?.allCategories
);
const pagesAndCategories = getPagesWithCategories(
	getBlocksPages(),
	astraSitesVars?.allCategories
);

const { selectedImages } = getFromSessionStorage( SESSION_STORAGE_KEY, {} );

export const defaultOnboardingAIState = {
	showOnboarding: false,
	updateImages: false,
	currentStep: astraSitesVars?.zip_token_exists ? 2 : 1,
	isNewUser: !! astraSitesVars?.is_new_user,
	stepData: {
		token: aiStepValues?.token || '',
		businessType: aiStepValues?.business_category || '',
		siteLanguage: 'en',
		businessName: aiStepValues?.business_name || '',
		businessDetails: aiStepValues?.business_description || '',
		keywords: aiStepValues?.image_keywords || [],
		selectedImages: !! selectedImages?.length
			? selectedImages
			: [ ...( aiStepValues?.images ?? [] ) ],
		imagesPreSelected: !! aiStepValues?.images || false,
		businessContact: {
			phone: aiStepValues?.business_phone || '',
			email: aiStepValues?.business_email || '',
			address: aiStepValues?.business_address || '',
			socialMedia: aiStepValues?.social_profiles || [],
		},
		templateKeywords: aiStepValues?.template_keywords || [],
		templateList: aiStepValues?.templateList || [],
		selectedTemplate: aiStepValues?.selectedTemplate || '',
		templateSearchResults: aiStepValues?.templateSearchResults || '',
		descriptionListStore: {
			list: [],
			currentPage: 0,
		},
		siteFeatures: [],
	},
	websiteInfo: aiStepValues?.websiteInfo || {},
	websiteVersionList: [],
	selectedWebsiteVersion: null,
	limitExceedModal: {
		open: false,
	},
	continueProgressModal: {
		open: false,
	},
	loadingNextStep: false,
};

const keysToIgnore = [ 'limitExceedModal' ];
// Saved AI onboarding state.
let savedAiOnboardingState = getLocalStorageItem( 'ai-onboarding-details' );
if ( savedAiOnboardingState ) {
	savedAiOnboardingState = omit( savedAiOnboardingState, keysToIgnore );
	savedAiOnboardingState = {
		...defaultOnboardingAIState,
		...savedAiOnboardingState,
	};
}

if (
	savedAiOnboardingState?.currentStep === 1 &&
	astraSitesVars?.zip_token_exists
) {
	savedAiOnboardingState.currentStep = 2;
}

export const initialState = {
	// Popup.
	togglePopup: false,

	// Sites, Pages, and Blocks Data.
	allPatternsAndPages: {
		patterns: patternsAndCategories.patterns,
		pages: pagesAndCategories.pages,
	},
	allBlocks: astraSitesVars?.allBlocks,
	allPatterns: patternsAndCategories.patterns,
	allPatternsCategories: patternsAndCategories.categories,
	allBlocksPages: pagesAndCategories.pages,
	allPagesCategories: pagesAndCategories.categories,
	allWireframes: getWireframes(),
	allSites: astraSitesVars?.allSites,
	allPages: [],
	dynamicContent: astraSitesVars?.dynamic_content,
	allCategories: astraSitesVars?.allCategories,
	favorites: astraSitesVars?.favorites,
	dynamicContentSyncStatus: {
		pages: false,
		patterns: false,
	},
	dynamicContentReSyncStatus: false,
	dynamicContentSyncFlags: {
		patterns: Object.fromEntries(
			patternsAndCategories.categories.map( ( item ) => [
				item.id,
				false,
			] )
		),
		pages: Object.fromEntries(
			pagesAndCategories.categories.map( ( item ) => [ item.id, false ] )
		),
	},
	currentCategory: {
		pages: {},
		patterns: {},
	},

	// Pages content generation onboarding flag.
	showPagesOnboarding: astraSitesVars?.show_pages_onboarding,

	// Credits.
	credits: {
		flatRates: objSnakeToCamelCase( astraSitesVars?.flat_rates ),
		...astraSitesVars?.spec_credit_details,
	},

	// Screen.
	currentScreen: 'all-blocks-grid',
	previousScreen: '',

	// Filter blocks by:
	filterBlocksByCategory: '',
	filterBlocksByColor: '',
	filterBlocksBySearchTerm: '',

	// Filter blocks pages by:
	filterBlocksPagesByCategory: '',
	filterBlocksPagesByColor: '',
	filterBlocksPagesBySearchTerm: '',

	// Filter pages by:
	filterPagesByPageType: '',
	filterPagesBySearchTerm: '',

	// Preview.
	pagePreview: {},
	sitePreview: {},
	fullWidthPagePreview: {},
	fullWidthBlockPreview: {},

	// Notice.
	notice: {},

	// Import Item Info.
	importItemInfo: {},

	// Dynamic Popup.
	displayDynamicPopup: false,

	// Color Palette.
	activeBlockPaletteSlug: 'default',
	activePagePaletteSlug: 'default',
	defaultBlockPalette: getDefaultBlockPalette(),
	defaultPagePalette: getDefaultPagePalette(),
	activeBlockPalette: {},
	activePagePalette: {},

	// Onboarding AI.
	onboardingAI: savedAiOnboardingState ?? defaultOnboardingAIState,

	// Settings.
	disableAi: !! astraSitesVars?.disable_ai,
	disablePreview: !! astraSitesVars?.disable_preview,
	regeneratingContentCategory: null,

	// Import is in progress flag.
	importInProgress: false,
	specAITogglePopup: false,
};

const reducer = ( state = initialState, action ) => {
	if ( action.type === 'SET_DISPLAY_DYNAMIC_POPUP' ) {
		return { ...state, displayDynamicPopup: action.displayDynamicPopup };
	} else if ( action.type === 'SET_SPEC_AI_TOGGLE_POPUP' ) {
		return {
			...state,
			specAITogglePopup: ! state.specAITogglePopup,
		};
	} else if ( action.type === 'SET_STATE' ) {
		return { ...state, ...action.state };
	} else if ( action.type === 'FULL_WIDTH_PAGE_PREVIEW' ) {
		return { ...state, fullWidthPagePreview: action.fullWidthPagePreview };
	} else if ( action.type === 'SET_CURRENT_CATEGORY' ) {
		const { type, category } = action.payload;
		const currentCategory = { ...state.currentCategory };

		return {
			...state,
			currentCategory: {
				...currentCategory,
				[ type ]: category,
			},
		};
	} else if ( action.type === 'FULL_WIDTH_BLOCK_PREVIEW' ) {
		return {
			...state,
			fullWidthBlockPreview: action.fullWidthBlockPreview,
		};
	} else if ( action.type === 'SET_IMPORT_ITEM_INFO' ) {
		return {
			...state,
			importItemInfo: action.importItemInfo,
		};
	} else if ( action.type === 'SET_NOTICE' ) {
		return {
			...state,
			notice: action.notice,
		};
	} else if ( action.type === 'SET_SEARCH_PAGE_PREVIEW' ) {
		let siteData = [];
		const siteID = action.item[ 'site-ID' ] || '';
		if ( siteID ) {
			siteData = state.allSites.filter( ( site ) => siteID === site.ID );
			if ( siteData ) {
				siteData = siteData[ 0 ];
			}
		}

		return {
			...state,
			sitePreview: siteData,
			pagePreview: action.item,
		};
	} else if ( action.type === 'SET_CURRENT_SCREEN' ) {
		const previousScreen =
			action.currentScreen === 'all-sites-grid' ||
			action.currentScree === 'all-blocks-grid'
				? ''
				: state.currentScreen;
		return {
			...state,
			currentScreen: action.currentScreen,
			previousScreen,
		};
	} else if ( action.type === 'SET_PREVIOUS_SCREEN' ) {
		return { ...state, previousScreen: action.previousScreen };
	} else if ( action.type === 'TOGGLE_POPUP' ) {
		const item = 'gt-current-screen-' + astraSitesVars?.site_host;
		const screen =
			localStorage.getItem( item ) !== 'all-single-site-pages'
				? localStorage.getItem( item )
				: '';

		return {
			...initialState,
			currentScreen: screen || initialState.currentScreen,
			togglePopup: ! state.togglePopup,
			filterBlocksByCategory: state.filterBlocksByCategory,
			filterBlocksByColor: state.filterBlocksByColor,
			allPatterns: state.allPatterns,
			allBlocksPages: state.allBlocksPages,
			allWireframes: state.allWireframes,
			filterBlocksBySearchTerm: state.filterBlocksBySearchTerm,
			activePalette: state.activePalette,

			filterBlocksPagesByCategory: state.filterBlocksPagesByCategory,
			filterBlocksPagesByColor: state.filterBlocksPagesByColor,
			filterBlocksPagesBySearchTerm: state.filterBlocksPagesBySearchTerm,

			// Keep the dynamic content and onboarding AI data.
			dynamicContent: { ...state.dynamicContent },
			onboardingAI: { ...state.onboardingAI },

			// Settings
			disableAi: state.disableAi,
			disablePreview: state.disablePreview,

			// Pages content generation onboarding flag.
			showPagesOnboarding: state.showPagesOnboarding,
		};
	} else if ( action.type === 'SET_ONBOARDING_AI_POPUP' ) {
		const updatedData = { ...state.onboardingAI };
		updatedData.showOnboarding = ! state.onboardingAI.showOnboarding;
		return { ...state, onboardingAI: updatedData };
	} else if ( action.type === 'SET_SITE_PREVIEW' ) {
		return { ...state, sitePreview: action.sitePreview };
	} else if ( action.type === 'SET_PAGE_PREVIEW' ) {
		return { ...state, pagePreview: action.pagePreview };
	} else if ( action.type === 'SET_FULL_PREVIEW' ) {
		return { ...state, fullWidthPreview: action.fullWidthPreview };
	} else if ( action.type === 'SET_DEFAULT_BLOCK_PALETTE' ) {
		return { ...state, defaultBlockPalette: action.defaultBlockPalette };
	} else if ( action.type === 'SET_DEFAULT_PAGE_PALETTE' ) {
		return { ...state, defaultPagePalette: action.defaultPagePalette };
	} else if ( action.type === 'SET_ACTIVE_BLOCK_PALETTE' ) {
		return { ...state, activeBlockPalette: action.activeBlockPalette };
	} else if ( action.type === 'SET_ACTIVE_BLOCK_PALETTE_SLUG' ) {
		return {
			...state,
			activeBlockPaletteSlug: action.activeBlockPaletteSlug,
		};
	} else if ( action.type === 'SET_ACTIVE_PAGE_PALETTE' ) {
		return { ...state, activePagePalette: action.activePagePalette };
	} else if ( action.type === 'SET_ACTIVE_PAGE_PALETTE_SLUG' ) {
		return {
			...state,
			activePagePaletteSlug: action.activePagePaletteSlug,
		};
	} else if ( action.type === 'SET_FILTER_BLOCKS_BY_CATEGORY' ) {
		const newState = {
			...state,
			filterBlocksBySearchTerm: '',
			filterBlocksByCategory: action.filterBlocksByCategory,
		};
		if ( state.currentScreen === 'all-wireframe-grid' ) {
			newState.allWireframes = filterWireframes(
				'',
				action.filterBlocksByCategory,
				'',
				state.filterBlocksByColor
			);
		} else {
			newState.allPatterns = filterBlocks(
				'',
				action.filterBlocksByCategory,
				'',
				state.filterBlocksByColor,
				state.allPatternsAndPages.patterns,
				state.favorites,
				'block'
			);
		}

		return newState;
	} else if ( action.type === 'SET_FILTER_BLOCKS_BY_COLOR' ) {
		const newState = {
			...state,
			filterBlocksBySearchTerm: '',
			filterBlocksByColor: action.filterBlocksByColor,
		};

		if ( state.currentScreen === 'all-wireframe-grid' ) {
			newState.allWireframes = filterWireframes(
				'',
				state.filterBlocksByCategory,
				'',
				action.filterBlocksByColor
			);
		} else {
			newState.allPatterns = filterPatterns(
				'',
				state.filterBlocksByCategory,
				'',
				action.filterBlocksByColor,
				state.favorites
			);
		}

		return newState;
	}

	if ( action.type === 'SET_FILTER_BLOCKS_BY_SEARCH_TERM' ) {
		const newState = {
			...state,
			filterBlocksByColor: '',
			filterBlocksByCategory: '',
			filterBlocksBySearchTerm: action.filterBlocksBySearchTerm,
		};

		if ( state.currentScreen === 'all-wireframe-grid' ) {
			newState.allWireframes = filterWireframes(
				action.filterBlocksBySearchTerm,
				'',
				action.filterBlocksBySearchTerm,
				''
			);
		} else {
			newState.allPatterns = filterBlocks(
				action.filterBlocksBySearchTerm,
				newState.filterBlocksByCategory,
				'',
				'',
				state.allPatternsAndPages.patterns,
				state.favorites,
				'block'
			);
		}

		return newState;
	} else if ( action.type === 'SET_FILTER_BLOCKS_PAGES_BY_CATEGORY' ) {
		const newState = {
			...state,
			filterBlocksPagesBySearchTerm: '',
			filterBlocksPagesByCategory: action.filterBlocksPagesByCategory,
		};

		newState.allBlocksPages = filterBlocks(
			newState.filterBlocksPagesBySearchTerm,
			action.filterBlocksPagesByCategory,
			'',
			'',
			state.allPatternsAndPages.pages,
			state.favorites,
			'page'
		);

		return newState;
	} else if ( action.type === 'SET_FILTER_BLOCKS_PAGES_BY_COLOR' ) {
		const newState = {
			...state,
			filterBlocksPagesBySearchTerm: '',
			filterBlocksPagesByColor: action.filterBlocksPagesByColor,
		};

		newState.allBlocksPages = filterBlocksPages(
			'',
			state.filterBlocksPagesByCategory,
			'',
			action.filterBlocksPagesByColor,
			state.favorites
		);

		return newState;
	} else if ( action.type === 'SET_FILTER_BLOCKS_PAGES_BY_SEARCH_TERM' ) {
		const newState = {
			...state,
			filterBlocksPagesByColor: '',
			filterBlocksPagesByCategory: '',
			filterBlocksPagesBySearchTerm: action.filterBlocksPagesBySearchTerm,
		};

		newState.allBlocksPages = filterBlocks(
			action.filterBlocksPagesBySearchTerm,
			newState.filterBlocksPagesByCategory,
			'',
			'',
			state.allPatternsAndPages.pages,
			state.favorites,
			'page'
		);

		return newState;
	} else if ( action.type === 'SET_FILTER_PAGES_BY_SEARCH_TERM' ) {
		if ( action.filterPagesBySearchTerm.length ) {
			return {
				...state,
				allPages: filterPages(
					action.filterPagesBySearchTerm
					// state.filterPagesByColor,
					// action.filterPagesByCategory,
					// state.filterPagesBySearchTerm
				),
				filterPagesBySearchTerm: action.filterPagesBySearchTerm,
			};
		}

		return {
			...state,
			allPages: [],
			filterPagesBySearchTerm: action.filterPagesBySearchTerm,
		};
	} else if ( action.type === actionTypes.TOGGLE_ONBOARDING_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				showOnboarding:
					actionTypes?.payload ?? ! state.onboardingAI.showOnboarding,
				currentStep: 1,
				updateImages: false,
			},
		};
	} else if ( action.type === actionTypes.TOGGLE_DISABLE_LIVE_PREVIEW ) {
		return {
			...state,
			disablePreview: actionTypes?.payload ?? ! state.disablePreview,
		};
	} else if ( action.type === actionTypes.TOGGLE_DISABLE_AI_CONTENT ) {
		return {
			...state,
			disableAi: actionTypes?.payload ?? ! state.disableAi,
		};
	} else if ( action.type === actionTypes.SET_NEXT_AI_STEP ) {
		const nextStep = state.onboardingAI.currentStep + 1;
		if ( nextStep > TOTAL_STEPS ) {
			return state;
		}

		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				currentStep: nextStep,
			},
		};
	} else if ( action.type === actionTypes.SET_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				currentStep: action.step,
			},
		};
	} else if ( action.type === actionTypes.SET_PREVIOUS_AI_STEP ) {
		const previousStep = state.onboardingAI.currentStep - 1;
		if ( previousStep < 0 || previousStep === 0 ) {
			return state;
		}

		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				currentStep: previousStep,
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_TYPE_LIST_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					businessTypeList: action.payload,
				},
			},
		};
	} else if (
		action.type === actionTypes.SET_WEBSITE_LANGUAGE_LIST_AI_STEP
	) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					siteLanguageList: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_VERSION_LIST ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				websiteVersionList: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_SELECTED_WEBSITE_VERSION ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				selectedWebsiteVersion: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_LIMIT_EXCEED_MODAL ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				limitExceedModal: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_AUTHENTICATION_ERROR_MODAL ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				authenticationErrorModal: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_CONTINUE_PROGRESS_MODAL ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				continueProgressModal: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_TYPE_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					businessType: action.payload,
				},
				limitExceedModal: {
					...state.onboardingAI.limitExceedModal,
					limitExceedModal: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_LANGUAGE_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					siteLanguage: action.payload,
				},
				limitExceedModal: {
					...state.onboardingAI.limitExceedModal,
					limitExceedModal: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_NAME_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					businessName: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_DETAILS_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					businessDetails: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_CONTACT_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					businessContact: action.payload,
				},
			},
		};
	} else if (
		action.type === actionTypes.SET_WEBSITE_ONBOARDING_AI_DETAILS
	) {
		return {
			...state,
			onboardingAI: {
				...action.payload,
				continueProgressModal: state.onboardingAI.continueProgressModal, // prevent this function from overriding continueProgressModal
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_TEMPLATES_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					templateList: action.payload,
				},
			},
		};
	} else if (
		action.type === actionTypes.SET_WEBSITE_TEMPLATE_RESULTS_AI_STEP
	) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					templateSearchResults: action.payload,
				},
			},
		};
	} else if (
		action.type === actionTypes.SET_WEBSITE_SELECTED_TEMPLATE_AI_STEP
	) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					selectedTemplate: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_DATA_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				websiteInfo: action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_KEYWORDS_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					keywords: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_WEBSITE_IMAGES_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					selectedImages: action.payload,
				},
			},
		};
	} else if (
		action.type === actionTypes.SET_WEBSITE_IMAGES_PRE_SELECTED_AI_STEP
	) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					imagesPreSelected: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.RESET_KEYWORDS_IMAGES_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					keywords: [],
					selectedImages: [],
					imagesPreSelected: false,
				},
			},
		};
	} else if ( action.type === actionTypes.SET_OPEN_AI_API_KEY_AI_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					token: action.payload,
				},
			},
		};
	} else if ( action.type === actionTypes.RESET_ONBOARDING_AI_STEPS ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				currentStep: 1,
				stepData: {
					token: '',
					businessType: '',
					businessName: '',
					businessDetails: '',
					keywords: [],
					selectedImages: [],
					imagesPreSelected: false,
					businessContact: {
						phone: '',
						email: '',
						address: '',
						socialMedia: [],
					},
				},
			},
		};
	} else if ( action.type === 'SET_ONBOARDING_AI_DETAILS' ) {
		return {
			...state,
			onboardingAI: action.payload,
		};
	} else if ( action.type === 'SET_ALL_PAGES' ) {
		return {
			...state,
			allPages: action.allPages,
		};
	} else if ( action.type === 'SET_ALL_PATTERNS' ) {
		return {
			...state,
			allPatterns: action.allPatterns,
		};
	} else if ( action.type === 'SET_ALL_CATEGORIES' ) {
		return {
			...state,
			allCategories: action.allCategories,
		};
	} else if ( action.type === 'SET_DYNAMIC_CONTENT' ) {
		return {
			...state,
			dynamicContent: action.dynamicContent,
		};
	} else if ( action.type === 'SET_FAVORITES' ) {
		const {
			currentScreen,
			filterBlocksPagesByCategory,
			filterBlocksByCategory,
		} = state;
		const newState = {
			...state,
			favorites: action.favorites,
		};
		if (
			filterBlocksByCategory !== 'favorite' &&
			filterBlocksPagesByCategory !== 'favorite'
		) {
			return newState;
		}

		if ( currentScreen === 'all-blocks-grid' ) {
			newState.allPatterns = filterPatterns(
				'',
				'favorite',
				'',
				state.filterBlocksByColor,
				action.favorites
			);
		}

		if ( currentScreen === 'all-block-pages-grid' ) {
			newState.allBlocksPages = filterBlocksPages(
				'',
				'favorite',
				'',
				state.filterBlocksPagesByColor,
				action.favorites
			);
		}

		return newState;
	} else if ( action.type === actionTypes.DYNAMIC_CONTENT_SYNC_START ) {
		const dynamicContentSyncStatus = { ...state.dynamicContentSyncStatus };
		return {
			...state,
			dynamicContentSyncStatus: !! action.payload?.length
				? action.payload.reduce( ( acc, item ) => {
						acc[ item ] = true;
						return acc;
				  }, dynamicContentSyncStatus )
				: { pages: true, patterns: true },
		};
	} else if ( action.type === actionTypes.DYNAMIC_CONTENT_SYNC_COMPLETE ) {
		const dynamicContentSyncStatus = { ...state.dynamicContentSyncStatus };
		return {
			...state,
			dynamicContentSyncStatus: !! action.payload?.length
				? action.payload.reduce( ( acc, item ) => {
						acc[ item ] = false;
						return acc;
				  }, dynamicContentSyncStatus )
				: { pages: false, patterns: false },
		};
	} else if ( action.type === actionTypes.DYNAMIC_CONTENT_RESYNC_STATUS ) {
		return {
			...state,
			dynamicContentReSyncStatus: ! state.dynamicContentReSyncStatus,
		};
	} else if ( action.type === actionTypes.DYNAMIC_CONTENT_FLAG_SET ) {
		const dynamicContentSyncFlags = { ...state.dynamicContentSyncFlags };

		if (
			dynamicContentSyncFlags.patterns?.hasOwnProperty(
				action.payload.key
			)
		) {
			dynamicContentSyncFlags.patterns[ action.payload.key ] =
				action.payload.value;
		}
		if (
			dynamicContentSyncFlags.pages?.hasOwnProperty( action.payload.key )
		) {
			dynamicContentSyncFlags.pages[ action.payload.key ] =
				action.payload.value;
		}

		return {
			...state,
			dynamicContentSyncFlags,
		};
	} else if ( action.type === actionTypes.DYNAMIC_CONTENT_FLAGS_RESET ) {
		const { dynamicContentSyncFlags } = state;

		if ( action.payload?.flags ) {
			dynamicContentSyncFlags[ action.payload.type ] = Object.fromEntries(
				action.payload.flags.map( ( item ) => [ item, false ] )
			);
		}

		if (
			! action.payload.flags &&
			patternsAndCategories.categories.length !==
				dynamicContentSyncFlags[ action.payload.type ]?.length
		) {
			dynamicContentSyncFlags[ action.payload.type ] = Object.fromEntries(
				patternsAndCategories.categories.map( ( item ) => [
					item.id,
					false,
				] )
			);
		}

		Object.keys( dynamicContentSyncFlags[ action.payload.type ] ).forEach(
			( key ) => {
				dynamicContentSyncFlags[ action.payload.type ][ key ] = false;
			}
		);

		return {
			...state,
			dynamicContentSyncFlags: { ...dynamicContentSyncFlags },
		};
	} else if ( action.type === 'SET_ALL_BLOCKS' ) {
		let { blocks: patterns, blocks_pages: pages } = action.payload;

		patterns = updateSequenceByCategory(
			patterns,
			state.allPatternsCategories,
			'block'
		);
		pages = updateSequenceByCategory(
			pages,
			state.allPagesCategories,
			'page'
		);

		return {
			...state,
			allPatternsAndPages: { patterns, pages },
			allPatterns: filterBlocks(
				state.filterBlocksBySearchTerm,
				state.filterBlocksByCategory,
				'',
				'',
				patterns,
				state.favorites,
				'block'
			),
			allBlocksPages: filterBlocks(
				state.filterBlocksPagesBySearchTerm,
				state.filterBlocksPagesByCategory,
				'',
				'',
				pages,
				state.favorites,
				'page'
			),
		};
	} else if ( action.type === 'SET_REGENERATING_CONTENT_CATEGORY' ) {
		return {
			...state,
			regeneratingContentCategory: action.regeneratingContentCategory,
		};
	} else if ( action.type === actionTypes.SET_IMPORT_IN_PROGRESS ) {
		return {
			...state,
			importInProgress: action.payload,
		};
	}

	if ( actionTypes.SET_SHOW_PAGES_ONBOARDING === action.type ) {
		return {
			...state,
			showPagesOnboarding: false,
		};
	} else if ( action.type === actionTypes.SET_CREDITS_DETAILS ) {
		return {
			...state,
			credits: {
				...state.credits,
				...action.payload,
			},
		};
	} else if ( action.type === actionTypes.SET_IS_NEW_USER_ONBOARDING ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				isNewUser: false,
			},
		};
	} else if ( action.type === actionTypes.TOGGLE_UPDATE_ONBOARDING_IMAGES ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				showOnboarding: ! state.onboardingAI.showOnboarding,
				updateImages: ! state.onboardingAI.updateImages,
				currentStep: ! state.onboardingAI.updateImages ? 6 : 1,
			},
		};
	}

	if ( action.type === actionTypes.STORE_SITE_FEATURES ) {
		const stepData = { ...state.onboardingAI.stepData };
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...stepData,
					siteFeatures: action.payload,
				},
			},
		};
	}

	if ( action.type === actionTypes.SET_SITE_FEATURES ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					siteFeatures: state.onboardingAI.stepData.siteFeatures.map(
						( item ) => {
							if ( item.id === action.payload ) {
								return {
									...item,
									enabled: ! item.enabled,
								};
							}
							return item;
						}
					),
				},
			},
		};
	}

	if ( action.type === actionTypes.SET_WEBSITE_TEMPLATE_KEYWORDS ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				stepData: {
					...state.onboardingAI.stepData,
					templateKeywords: action.payload,
				},
			},
		};
	}

	if ( action.type === actionTypes.LOADING_NEXT_STEP ) {
		return {
			...state,
			onboardingAI: {
				...state.onboardingAI,
				loadingNextStep: action.payload,
			},
		};
	}

	return state;
};

export default reducer;
