import React from 'react';
import { Tooltip } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import Button from '../../components/button/button';
import { useStateValue } from '../../store/store';
import './style.scss';
import PreviousStepLink from '../../components/util/previous-step-link/index';
import ICONS from '../../../icons';

const List = ( { className, options, onSelect, selected, type } ) => {
	const handleKeyPress = ( e, id ) => {
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
			onSelect( e, id );
		}
	};

	return (
		<ul className={ `ist-font-selector ${ className }` }>
			{ Object.keys( options ).map( ( index ) => {
				const bodyFont =
					getFontName( options[ index ][ 'body-font-family' ] ) || '';
				const headingFont =
					getFontName(
						options[ index ][ 'headings-font-family' ],
						bodyFont
					) || '';
				const bodyFontWeight = options[ index ][ 'body-font-weight' ];
				const headingFontWeight =
					options[ index ][ 'headings-font-weight' ];
				const id = options[ index ].id;
				return (
					<Tooltip
						content={
							type === 'other'
								? `${ headingFont } / ${ bodyFont }`
								: null
						}
						key={ id }
					>
						<li
							className={ `ist-font ${
								id === selected ? 'active' : ''
							}` }
							key={ id }
							onClick={ ( event ) => {
								onSelect( event, id );
							} }
							tabIndex="0"
							role="presentation"
							onKeyDown={ ( event ) => {
								handleKeyPress( event, id );
							} }
						>
							{
								<>
									{ type === 'default' && (
										<>
											<span
												style={ {
													fontFamily: headingFont,
													fontWeight:
														headingFontWeight,
												} }
												className="heading-font-preview"
											>
												{ headingFont }
											</span>
											<span className="font-separator">
												/
											</span>
											<span
												style={ {
													fontFamily: bodyFont,
													fontWeight: bodyFontWeight,
												} }
												className="body-font-preview"
											>
												{ bodyFont }
											</span>
										</>
									) }
									{ type === 'other' && (
										<>
											<span
												style={ {
													fontFamily: headingFont,
													fontWeight:
														headingFontWeight,
												} }
												className="heading-font-preview"
											>
												A
											</span>
											<span
												style={ {
													fontFamily: bodyFont,
													fontWeight: bodyFontWeight,
												} }
												className="body-font-preview"
											>
												a
											</span>
										</>
									) }
								</>
							}
						</li>
					</Tooltip>
				);
			} ) }
		</ul>
	);
};

export const getFontName = ( fontName, inheritFont ) => {
	if ( ! fontName ) {
		return '';
	}

	if ( fontName ) {
		const matches = fontName.match( /'([^']+)'/ );

		if ( matches ) {
			return matches[ 1 ];
		} else if ( 'inherit' === fontName ) {
			return inheritFont;
		}

		return fontName;
	}

	if ( inheritFont ) {
		return inheritFont;
	}
};

const FontSelector = ( { options, onSelect, selected } ) => {
	const [
		{
			currentIndex,
			currentCustomizeIndex,
			templateResponse,
			licenseStatus,
			importError,
			typographyIndex,
		},
		dispatch,
	] = useStateValue();

	const fonts = options.map( ( font, index ) => {
		font.id = index;
		return font;
	} );
	const defaultFonts = fonts.filter( ( font ) => font.default );
	const otherFonts = fonts.filter( ( font ) => ! font.default );
	let premiumTemplate = false;

	const nextStep = () => {
		if ( ! importError ) {
			premiumTemplate = 'free' !== templateResponse[ 'astra-site-type' ];

			if ( premiumTemplate && ! licenseStatus ) {
				if ( astraSitesVars.isPro ) {
					dispatch( {
						type: 'set',
						validateLicenseStatus: true,
						currentCustomizeIndex: currentCustomizeIndex + 1,
					} );
				} else {
					dispatch( {
						type: 'set',
						currentCustomizeIndex: currentCustomizeIndex + 1,
					} );
				}
			} else {
				dispatch( {
					type: 'set',
					currentIndex: currentIndex + 1,
				} );
			}
		}
	};

	const lastStep = () => {
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex - 1,
		} );
	};

	const resetTypography = ( event ) => {
		onSelect( event, defaultFonts[ 0 ].id );
	};

	return (
		<>
			<div className="d-flex-space-between">
				<h4 className="ist-default-fonts-heading">
					{ __( 'Change Fonts', 'astra-sites' ) }
				</h4>
				<div
					className={ `customize-reset-btn ${
						typographyIndex === 0 ? 'disabled' : 'active'
					}` }
					onClick={ resetTypography }
				>
					{ ICONS.reset }
				</div>
			</div>
			<List
				className="ist-default-fonts"
				options={ defaultFonts }
				onSelect={ onSelect }
				selected={ selected }
				type="default"
			/>
			<List
				className="ist-other-fonts"
				options={ otherFonts }
				onSelect={ onSelect }
				selected={ selected }
				type="other"
			/>

			<Button className="ist-button" onClick={ nextStep } after>
				{ __( 'Continue', 'astra-sites' ) }
			</Button>
			<PreviousStepLink customizeStep={ true } onClick={ lastStep }>
				{ __( 'Back', 'astra-sites' ) }
			</PreviousStepLink>
		</>
	);
};

export default FontSelector;
