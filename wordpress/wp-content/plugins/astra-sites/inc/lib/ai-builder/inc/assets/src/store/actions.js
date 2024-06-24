import { objSnakeToCamelCase } from '../utils/helpers';
import * as actionsTypes from './action-types';

const actions = {
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

	setLimitExceedModal( limitExceedModal ) {
		return {
			type: actionsTypes.SET_LIMIT_EXCEED_MODAL,
			payload: limitExceedModal,
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

	setSelectedTemplateIsPremium( selectedTemplateIsPremium ) {
		return {
			type: actionsTypes.SET_SELECTED_TEMPLATE_IS_PREMIUM,
			payload: selectedTemplateIsPremium,
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

	setCreditsDetails( payload ) {
		return {
			type: actionsTypes.SET_CREDITS_DETAILS,
			payload: objSnakeToCamelCase( payload ),
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

	setDynamicContent( dynamicContent ) {
		return {
			type: actionsTypes.SET_DYNAMIC_CONTENT,
			dynamicContent,
		};
	},

	setWebsiteLogo( logo ) {
		return {
			type: actionsTypes.SET_WEBSITE_LOGO,
			payload: logo,
		};
	},

	setWebsiteColorPalette( colorPalette ) {
		return {
			type: actionsTypes.SET_WEBSITE_COLOR_PALETTE,
			payload: colorPalette,
		};
	},

	setDefaultColorPalette( colorPalette ) {
		return {
			type: actionsTypes.SET_DEFAULT_COLOR_PALETTE,
			payload: colorPalette,
		};
	},

	setWebsiteTypography( typography ) {
		return {
			type: actionsTypes.SET_WEBSITE_TYPOGRAPHY,
			payload: typography,
		};
	},

	updateImportAiSiteData( payload ) {
		return {
			type: actionsTypes.UPDATE_IMPORT_AI_SITE_DATA,
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
