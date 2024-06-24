// Steps Pages
import { __ } from '@wordpress/i18n';
import GetStarted from '../pages/authorize-account';
import BusinessDetails from '../pages/business-details';
import DescribeBusiness from '../pages/describe-business';
import BusinessContact from '../pages/business-contact';
import Images from '../pages/images';
import SelectTemplate from '../pages/select-template';
import Features from '../pages/features';
import ImportAiSite from '../pages/import-ai-site';
import BuildDone from '../pages/done';

// Steps
const steps = [
	{
		path: '/',
		component: GetStarted,
		layoutConfig: {
			hideSidebar: true,
			hideCloseIcon: true,
			hideStep: true,
			hideCredits: true,
		},
		requiredStates: [],
	},
	{
		path: '/lets-start',
		component: BusinessDetails,
		layoutConfig: {
			stepNumber: 1,
			name: __( 'Let’s Start', 'ai-builder' ),
			description: __( 'Name, language & type', 'ai-builder' ),
			screen: 'type',
			hideCredits: false,
		},
		requiredStates: [ 'businessType', 'businessName' ],
	},
	{
		path: '/description',
		component: DescribeBusiness,
		layoutConfig: {
			stepNumber: 2,
			name: __( 'Describe', 'ai-builder' ),
			description: __( 'Some details please', 'ai-builder' ),
			screen: 'details',
			hideCredits: false,
		},
		requiredStates: [ 'businessDetails', 'keywords' ],
	},
	{
		path: '/contact-details',
		component: BusinessContact,
		layoutConfig: {
			stepNumber: 3,
			name: __( 'Contact', 'ai-builder' ),
			description: __( 'How can people get in touch', 'ai-builder' ),
			screen: 'contact-details',
			hideCredits: false,
		},
		requiredStates: [],
	},
	{
		path: '/select-images',
		component: Images,
		layoutConfig: {
			stepNumber: 4,
			name: __( 'Select Images', 'ai-builder' ),
			description: __( 'Select relevant images as needed', 'ai-builder' ),
			screen: 'images',
			contentClassName:
				'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',
			hideCredits: false,
		},
		requiredStates: [ 'templateKeywords' ],
	},
	{
		path: '/design',
		component: SelectTemplate,
		layoutConfig: {
			stepNumber: 5,
			name: __( 'Design', 'ai-builder' ),
			description: __(
				'Choose a structure for your website',
				'ai-builder'
			),
			screen: 'template',
			contentClassName:
				'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',
			hideCredits: false,
		},
		requiredStates: [ 'selectedTemplate' ],
	},
	{
		path: '/features',
		component: Features,
		layoutConfig: {
			stepNumber: 6,
			name: __( 'Features', 'ai-builder' ),
			description: __( 'Select features as you need', 'ai-builder' ),
			screen: 'done',
			contentClassName:
				'px-0 pt-0 md:px-0 md:pt-0 lg:px-0 lg:pt-0 xl:px-0 xl:pt-0',
			hideCredits: false,
		},
		requiredStates: [ 'websiteInfo' ],
	},
	{
		path: '/building-website',
		component: ImportAiSite,
		layoutConfig: {
			stepNumber: 8,
			name: __( 'Done', 'ai-builder' ),
			description: __( 'Your website is ready!', 'ai-builder' ),
			screen: 'done',
			hideStep: true,
			hideCredits: true,
		},
		requiredStates: [],
	},
	{
		path: '/done',
		component: BuildDone,
		layoutConfig: {
			name: __( 'Done', 'ai-builder' ),
			description: __(
				'Congratulations! Your website is ready!',
				'ai-builder'
			),
			screen: 'done',
			contentClassName: 'pt-0 md:pt-0 lg:pt-0 xl:pt-0',
			hideStep: true,
			hideCredits: true,
		},
		requiredStates: [],
	},
];

export const TOTAL_STEPS = steps.length;

export default Object.seal( steps );
