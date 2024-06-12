import React, { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../../../store/store';
import ChangeTemplate from '../../../../components/change-template';

const BusinessLogo = () => {
	const [ {}, dispatch ] = useStateValue();
	useEffect( () => {
		dispatch( {
			type: 'set',
			designStep: 1,
		} );
	}, [] );

	return (
		<>
			<ChangeTemplate />
			<div className="customizer-header">
				<div className="header-name">
					<h3 className="ist-customizer-heading">
						{ __( 'Logo', 'astra-sites' ) }
					</h3>
					<p className="screen-description">
						{ __(
							`Choose a logo for your site. You can update it anytime later.`,
							'astra-sites'
						) }
					</p>
				</div>
			</div>
		</>
	);
};

export default BusinessLogo;
