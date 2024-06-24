import React, { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../../../store/store';
import ChangeTemplate from '../../../../components/change-template';

const SiteColors = () => {
	const [ { builder }, dispatch ] = useStateValue();
	useEffect( () => {
		dispatch( {
			type: 'set',
			designStep: 2,
		} );
	}, [] );

	return (
		<>
			<ChangeTemplate />
			<div className="customizer-header">
				<div className="header-name">
					<h3 className="ist-customizer-heading">
						{ builder === 'beaver-builder' || builder === 'brizy'
							? __( 'Fonts', 'astra-sites' )
							: __( 'Colors & Fonts', 'astra-sites' ) }
					</h3>
					<p className="screen-description">
						{ __(
							'Choose colors and fonts for your site. You can update them anytime later.',
							'astra-sites'
						) }
					</p>
				</div>
			</div>
		</>
	);
};

export default SiteColors;
