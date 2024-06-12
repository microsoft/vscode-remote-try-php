import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const BusinessLogo = () => {
	const [ {}, dispatch ] = [ {}, () => {} ]; // Remove this line.
	useEffect( () => {
		dispatch( {
			type: 'set',
			designStep: 1,
		} );
	}, [] );

	return (
		<>
			{ /* <ChangeTemplate /> */ }
			<div className="customizer-header">
				<div className="header-name">
					<h3 className="ist-customizer-heading">
						{ __( 'Logo', 'ai-builder' ) }
					</h3>
					<p className="screen-description">
						{ __(
							`Choose a logo for your site. You can update it anytime later.`,
							'ai-builder'
						) }
					</p>
				</div>
			</div>
		</>
	);
};

export default BusinessLogo;
