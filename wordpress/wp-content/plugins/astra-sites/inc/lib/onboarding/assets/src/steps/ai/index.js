import React, { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
// import { useState } from '@wordpress/element';
import { DefaultStep, PreviousStepLink } from '../../components/index';
import { useStateValue } from '../../store/store';
import ZipWPAuthorize from '../../components/zipwp-auth/index';

const { imageDir } = starterTemplates;

const PageBuilder = () => {
	const [ { currentIndex }, dispatch ] = useStateValue();
	// const [ builder, setBuilder ] = useState( 'ai' );

	useEffect( () => {
		const startTime = localStorage.getItem( 'st-import-start' );
		const endTime = localStorage.getItem( 'st-import-end' );

		if ( startTime || endTime ) {
			localStorage.removeItem( 'st-import-start' );
			localStorage.removeItem( 'st-import-end' );
		}
	} );

	const update = ( builder ) => {
		if ( builder !== 'ai' ) {
			dispatch( {
				type: 'set',
				builder,
				currentIndex: currentIndex + 1,
			} );
		}
	};

	const handleKeyPress = ( e, value ) => {
		e = e || window.event;

		if ( e.keyCode === 37 ) {
			//Left Arrow
			if ( e.target.previousSibling ) {
				e.target.previousSibling.focus();
			}
		} else if ( e.keyCode === 39 ) {
			//Right Arrow
			if ( e.target.nextSibling ) {
				e.target.nextSibling.focus();
			}
		} else if ( e.key === 'Enter' ) {
			//Enter
			update( value );
		}
	};

	return (
		<DefaultStep
			content={
				<div className="page-builder-screen-wrap middle-content">
					<h1>{ __( 'Select Page Builder', 'astra-sites' ) }</h1>
					<p className="screen-description">
						{ __(
							'Please choose your preferred page builder from the list below.',
							'astra-sites'
						) }
					</p>
					<div className="page-builder-wrap ist-fadeinUp">
						<div
							className="page-builder-item d-flex-center-align"
							onClick={ () => {
								update( 'ai' );
							} }
							tabIndex="0"
							onKeyDown={ ( event ) =>
								handleKeyPress( event, 'ai' )
							}
						>
							<div className="elementor-image-wrap image-wrap">
								<img
									src={ `${ imageDir }block-editor.svg` }
									alt={ __( 'Block Editor', 'astra-sites' ) }
								/>
							</div>
							<h6>{ __( 'AI Site', 'astra-sites' ) }</h6>
						</div>
						<div
							className="page-builder-item d-flex-center-align"
							onClick={ () => {
								update( 'legacy' );
							} }
							tabIndex="0"
							onKeyDown={ ( event ) =>
								handleKeyPress( event, 'legacy' )
							}
						>
							<div className="beaver-builder-image-wrap image-wrap">
								<img
									src={ `${ imageDir }beaver-builder.svg` }
									alt={ __(
										'Beaver Builder',
										'astra-sites'
									) }
								/>
							</div>
							<h6>{ __( 'Legacy', 'astra-sites' ) }</h6>
						</div>
					</div>
					<div className="zipwp-authorize-wrap">
						{ <ZipWPAuthorize /> }
					</div>
				</div>
			}
			actions={
				<>
					<PreviousStepLink
						before
						customizeStep={ true }
						onClick={ () => {
							window.location.href = starterTemplates.adminUrl;
						} }
					>
						{ __( 'Back', 'astra-sites' ) }
					</PreviousStepLink>
				</>
			}
		/>
	);
};

export default PageBuilder;
