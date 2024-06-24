import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const SiteColors = () => {
	const [ { builder }, dispatch ] = [ {}, () => {} ]; // Remove this line.
	useEffect( () => {
		dispatch( {
			type: 'set',
			designStep: 2,
		} );
	}, [] );

	return (
		<>
			{ /* <ChangeTemplate /> */ }
			<div className="customizer-header">
				<div className="header-name">
					<h3 className="ist-customizer-heading">
						{ builder === 'beaver-builder' || builder === 'brizy'
							? __( 'Fonts', 'ai-builder' )
							: __( 'Colors & Fonts', 'ai-builder' ) }
					</h3>
					<p className="screen-description">
						{ __(
							'Choose colors and fonts for your site. You can update them anytime later.',
							'ai-builder'
						) }
					</p>
				</div>
			</div>
		</>
	);
};

export default SiteColors;
