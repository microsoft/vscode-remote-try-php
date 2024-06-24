import { getFromSessionStorage } from '../utils/helpers';
import { SESSION_STORAGE_KEY } from '../utils/constants';
import { getLocalStorageItem } from '../helpers';
import * as actionTypes from './action-types';
import { omit } from 'lodash';

export const siteLogoDefault = {
	id: '',
	thumbnail: '',
	url: '',
	width: 120,
};

const aiStepValues = aiBuilderVars?.business_details;

const { selectedImages } = getFromSessionStorage( SESSION_STORAGE_KEY, {} );

export const defaultOnboardingAIState = {
	stepData: {
		tokenExists: aiBuilderVars?.zip_token_exists || '',
		businessType: aiStepValues?.business_category || '',
		siteLanguage: 'en',
		businessName: aiStepValues?.business_name || '',
		businessDetails: aiStepValues?.business_description || '',
		keywords: aiStepValues?.image_keyword || [],
		selectedImages: !! selectedImages?.length
			? selectedImages
			: aiStepValues.images || [],
		imagesPreSelected:
			!! aiStepValues?.images?.landscape?.length ||
			!! aiStepValues?.images?.portrait?.length ||
			false,
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
		siteLogo: siteLogoDefault,
		activeColorPalette: null,
		activeTypography: null,
		defaultColorPalette: null,
	},
	websiteInfo: aiStepValues?.websiteInfo || {},
	websiteVersionList: [],
	limitExceedModal: {
		open: false,
	},
	continueProgressModal: {
		open: false,
	},
	importSiteProgressData: {
		builder: 'gutenberg',
		templateId: '',
		templateResponse: {},
		requiredPlugins: [],
		tryAgainCount: 0,
		pluginInstallationAttempts: 0,
		reset: 'yes' === aiBuilderVars.firstImportStatus ? true : false,
		themeStatus: false,
		importStatusLog: '',
		importStatus: '',
		xmlImportDone: false,
		requiredPluginsDone: false,
		notInstalledList: [],
		notActivatedList: [],
		resetData: [],
		importStart: false,
		importEnd: false,
		importPercent: 0,
		importError: false,
		importErrorMessages: {
			primaryText: '',
			secondaryText: '',
			errorCode: '',
			errorText: '',
			solutionText: '',
			tryAgain: false,
		},
		importErrorResponse: [],
		importTimeTaken: {},
		customizerImportFlag: true,
		themeActivateFlag: true,
		widgetImportFlag: true,
		contentImportFlag: true,
		analyticsFlag: aiBuilderVars.analytics !== 'yes' ? true : false,
		shownRequirementOnce: false,
		createSiteStatus: false,
	},
	loadingNextStep: false,
};

const keysToIgnore = [ 'limitExceedModal' ];
// Saved AI onboarding state.
let savedAiOnboardingState = getLocalStorageItem(
	'ai-builder-onboarding-details'
);
if ( savedAiOnboardingState ) {
	savedAiOnboardingState = omit( savedAiOnboardingState, keysToIgnore );
	savedAiOnboardingState = {
		...defaultOnboardingAIState,
		...savedAiOnboardingState,
	};
}

// if (
// 	savedAiOnboardingState?.currentStep === 1 &&
// 	aiBuilderVars?.zip_token_exists
// ) {
// 	savedAiOnboardingState.currentStep = 2;
// }

export const initialState = {
	// Credits.
	/* credits: {
		flatRates: objSnakeToCamelCase( aiBuilderVars?.flat_rates ),
		...aiBuilderVars?.spec_credit_details,
	}, */

	// Onboarding AI.
	...( savedAiOnboardingState ?? defaultOnboardingAIState ),
};

const reducer = ( state = initialState, action ) => {
	switch ( action.type ) {
		case actionTypes.SET_WEBSITE_TYPE_LIST_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					businessTypeList: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_LANGUAGE_LIST_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					siteLanguageList: action.payload,
				},
			};
		case actionTypes.SET_LIMIT_EXCEED_MODAL:
			return {
				...state,
				limitExceedModal: action.payload,
			};
		case actionTypes.SET_CONTINUE_PROGRESS_MODAL:
			return {
				...state,
				continueProgressModal: action.payload,
			};
		case actionTypes.SET_WEBSITE_TYPE_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					businessType: action.payload,
				},
				limitExceedModal: {
					...state.limitExceedModal,
					limitExceedModal: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_LANGUAGE_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					siteLanguage: action.payload,
				},
				limitExceedModal: {
					...state.limitExceedModal,
					limitExceedModal: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_NAME_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					businessName: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_DETAILS_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					businessDetails: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_CONTACT_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					businessContact: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_ONBOARDING_AI_DETAILS:
			return {
				...state,
				...action.payload,
				continueProgressModal: state.continueProgressModal, // prevent this function from overriding continueProgressModal
			};
		case actionTypes.SET_WEBSITE_TEMPLATES_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					templateList: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_TEMPLATE_RESULTS_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					templateSearchResults: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_SELECTED_TEMPLATE_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					selectedTemplate: action.payload,
				},
			};
		case actionTypes.SET_SELECTED_TEMPLATE_IS_PREMIUM:
			return {
				...state,
				stepData: {
					...state.stepData,
					selectedTemplateIsPremium: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_DATA_AI_STEP:
			return {
				...state,
				websiteInfo: action.payload,
			};
		case actionTypes.SET_WEBSITE_KEYWORDS_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					keywords: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_IMAGES_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					selectedImages: action.payload,
				},
			};
		case actionTypes.RESET_KEYWORDS_IMAGES_AI_STEP:
			return {
				...state,
				stepData: {
					...state.stepData,
					keywords: [],
					selectedImages: [],
					imagesPreSelected: false,
				},
			};
		case actionTypes.RESET_ONBOARDING_AI_STEPS:
			return {
				...state,
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
			};
		case 'SET_ONBOARDING_AI_DETAILS':
			return {
				...state,
				...action.payload,
			};
		case actionTypes.SET_CREDITS_DETAILS:
			return {
				...state,
				credits: {
					...state.credits,
					...action.payload,
				},
			};
		case actionTypes.TOGGLE_UPDATE_ONBOARDING_IMAGES:
			return {
				...state,
				showOnboarding: ! state.showOnboarding,
				updateImages: ! state.updateImages,
				currentStep: ! state.updateImages ? 6 : 1,
			};
		case actionTypes.STORE_SITE_FEATURES:
			const stepData = { ...state.stepData };
			return {
				...state,
				stepData: {
					...stepData,
					siteFeatures: action.payload,
				},
			};
		case actionTypes.SET_SITE_FEATURES:
			return {
				...state,
				stepData: {
					...state.stepData,
					siteFeatures: state.stepData.siteFeatures.map( ( item ) => {
						if ( item.id === action.payload ) {
							return {
								...item,
								enabled: ! item.enabled,
							};
						}
						return item;
					} ),
				},
			};
		case actionTypes.SET_WEBSITE_TEMPLATE_KEYWORDS:
			return {
				...state,
				stepData: {
					...state.stepData,
					templateKeywords: action.payload,
				},
			};
		case actionTypes.SET_DYNAMIC_CONTENT:
			return {
				...state,
				dynamicContent: action.dynamicContent,
			};
		case actionTypes.SET_WEBSITE_LOGO:
			return {
				...state,
				stepData: {
					...state.stepData,
					siteLogo: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_COLOR_PALETTE:
			return {
				...state,
				stepData: {
					...state.stepData,
					activeColorPalette: action.payload,
				},
			};
		case actionTypes.SET_WEBSITE_TYPOGRAPHY:
			return {
				...state,
				stepData: {
					...state.stepData,
					activeTypography: action.payload,
				},
			};
		case actionTypes.UPDATE_IMPORT_AI_SITE_DATA:
			return {
				...state,
				importSiteProgressData: {
					...state.importSiteProgressData,
					...action.payload,
				},
			};
		case actionTypes.SET_DEFAULT_COLOR_PALETTE:
			return {
				...state,
				stepData: {
					...state.stepData,
					activeColorPalette: action.payload,
					defaultColorPalette: action.payload,
				},
			};
		case actionTypes.LOADING_NEXT_STEP:
			return {
				...state,
				loadingNextStep: action.payload,
			};
		default:
			return state;
	}
};

export default reducer;
