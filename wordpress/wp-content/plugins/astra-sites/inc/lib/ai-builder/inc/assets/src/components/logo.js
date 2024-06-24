import { __ } from '@wordpress/i18n';
import { whiteLabelEnabled, getWhileLabelName } from '../utils/functions';
const { imageDir, logoUrlLight } = aiBuilderVars;
const url = logoUrlLight || `${ imageDir }logo.svg`;

const Logo = () => {
	return (
		<div className="branding-wrap">
			{ whiteLabelEnabled() ? (
				<h3>{ getWhileLabelName() }</h3>
			) : (
				<a
					className="flex items-center justify-center w-11 h-11"
					href="#"
					rel="noopener noreferrer"
				>
					<img
						className="w-full h-full"
						src={ url }
						alt={ __( 'Starter Templates', 'ai-builder' ) }
					/>
				</a>
			) }
		</div>
	);
};

export default Logo;
