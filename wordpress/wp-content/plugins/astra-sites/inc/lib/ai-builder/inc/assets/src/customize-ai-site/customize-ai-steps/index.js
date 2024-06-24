import BusinessLogo from './business-logo-ai';
import BusinessLogoControls from './business-logo-ai/controls';
import SiteColors from './site-colors-typography-ai';
import SiteColorsControls from './site-colors-typography-ai/controls';

export const CustomizeAiSteps = [
	{
		content: BusinessLogo,
		controls: BusinessLogoControls,
		class: 'customize-business-logo',
	},
	{
		content: SiteColors,
		controls: SiteColorsControls,
		actions: null,
		class: 'customize-typography-colors',
	},
];
