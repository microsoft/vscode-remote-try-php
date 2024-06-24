import React from 'react';
import { Logo as SiteLogo } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import './style.scss';
import { whiteLabelEnabled, getWhileLabelName } from '../../utils/functions';
const { imageDir } = starterTemplates;

const Logo = () => {
	return (
		<div className="branding-wrap">
			{ whiteLabelEnabled() ? (
				<h3>{ getWhileLabelName() }</h3>
			) : (
				<SiteLogo
					className="ist-logo"
					src={ `${ imageDir }logo.svg` }
					alt={ __( 'Starter Templates', 'astra-sites' ) }
					onClick={ () =>
						window.open( astraSitesVars.st_page_url, '_self' )
					}
				/>
			) }
		</div>
	);
};

export default Logo;
