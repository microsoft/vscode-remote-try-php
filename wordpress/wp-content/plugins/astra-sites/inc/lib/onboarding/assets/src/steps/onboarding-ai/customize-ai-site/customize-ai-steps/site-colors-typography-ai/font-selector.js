import React, { useState } from 'react';
import { Tooltip } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { Button, PreviousStepLink } from '../../../../../components';
import ICONS from '../../../../../../icons';
import { useStateValue } from '../../../../../store/store';
import {
	saveTypography,
	setColorPalettes,
	setSiteLogo,
	setSiteTitle,
} from '../../../../import-site/import-utils';
import LoadingSpinner from '../../../components/loading-spinner';
import { STORE_KEY } from '../../../store';
import { removeLocalStorageItem } from '../../../helpers';
import { initialState } from '../../../store/reducer';

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
							className={ `
						ist-font
						${ id === selected ? 'active' : '' }
						` }
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
	const { setWebsiteOnboardingAIDetails } = useDispatch( STORE_KEY );
	const [
		{
			currentCustomizeIndex,
			typographyIndex,
			siteLogo,
			activePalette,
			typography,
		},
		dispatch,
	] = useStateValue();

	const { businessName } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	const [ isSaving, setIsSaving ] = useState( false );

	const fonts = options.map( ( font, index ) => {
		font.id = index;
		return font;
	} );
	const defaultFonts = fonts.filter( ( font ) => font.default );
	const otherFonts = fonts.filter( ( font ) => ! font.default );
	// let premiumTemplate = false;

	/**
	 * 8. Update the website as per the customizations selected by the user.
	 * The following steps are covered here.
	 *      a. Update Logo
	 *      b. Update Color Palette
	 *      c. Update Typography
	 */
	const customizeWebsite = async () => {
		setIsSaving( true );
		await setSiteLogo( siteLogo );
		await setColorPalettes( JSON.stringify( activePalette ) );
		await setSiteTitle( businessName );
		await saveTypography( typography );

		removeLocalStorageItem( 'ai-onboarding-details' );
		setWebsiteOnboardingAIDetails( initialState.onboardingAI );

		localStorage.removeItem( 'starter-templates-iframe-preview-data' );

		window.location.href = astraSitesVars.siteURL;
	};

	const nextStep = () => {
		customizeWebsite();
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
				{ isSaving ? (
					<LoadingSpinner />
				) : (
					__( 'Save Customizations', 'astra-sites' )
				) }
			</Button>
			<div className="mb-[60px]">
				<PreviousStepLink customizeStep={ true } onClick={ lastStep }>
					{ __( 'Back', 'astra-sites' ) }
				</PreviousStepLink>
			</div>
		</>
	);
};

export default FontSelector;
