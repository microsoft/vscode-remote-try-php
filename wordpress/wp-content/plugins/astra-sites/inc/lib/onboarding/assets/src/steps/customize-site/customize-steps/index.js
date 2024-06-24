import EcommerceSelections from './ecommerce-selections';
import EcommerceSelectionsControls from './ecommerce-selections/controls';
import ClassicPreview from '../classic-preview';

export const CustomizeSteps = [
	{
		content: EcommerceSelections,
		controls: EcommerceSelectionsControls,
		class: 'customize-ecommerce-selections',
	},
	{
		content: ClassicPreview,
		class: 'customize-business-logo',
	},
];
