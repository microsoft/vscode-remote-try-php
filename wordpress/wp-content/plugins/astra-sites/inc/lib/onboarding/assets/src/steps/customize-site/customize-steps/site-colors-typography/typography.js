import React, { useEffect, useState } from 'react';
import { useStateValue } from '../../../../store/store';
import { FontSelector } from '../../../../components/index';
import {
	sendPostMessage,
	getDefaultTypography,
	getHeadingFonts,
} from '../../../../utils/functions';
import { FONTS } from './other-fonts';

const getFontName = ( fontName, inheritFont ) => {
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

const TypographyWrapper = () => {
	const [ { typographyIndex, templateResponse }, dispatch ] = useStateValue();
	let [ fonts, setFonts ] = useState( FONTS );

	const head = getHeadingFonts( templateResponse );
	const [ headingFonts ] = useState( head );

	/**
	 * Add selected demo typograply as default typography
	 */
	useEffect( () => {
		const googleFontsURL = document.getElementById( 'google-fonts-url' );

		if ( templateResponse !== null ) {
			const defaultFonts = [];
			const defaultTypography = getDefaultTypography( templateResponse );
			defaultFonts.push( defaultTypography );

			if ( ! document.getElementById( 'google-fonts-domain' ) ) {
				const node = document.createElement( 'link' );
				node.id = 'google-fonts-domain';
				node.setAttribute( 'rel', 'preconnect' );
				node.setAttribute( 'href', 'https://fonts.gstatic.com' );
				document.head.appendChild( node );
			}

			// Removes existing Google fonts URL.
			if ( !! googleFontsURL ) {
				googleFontsURL.remove();
			}

			const node = document.createElement( 'link' );
			node.id = 'google-fonts-url';
			node.setAttribute( 'rel', 'stylesheet' );

			const fontsName = [];

			let bodyFont = defaultTypography[ 'body-font-family' ] || '';
			let bodyFontWeight =
				parseInt( defaultTypography[ 'body-font-weight' ] ) || '';
			if ( bodyFontWeight ) {
				bodyFontWeight = `:wght@${ bodyFontWeight }`;
			}

			if ( bodyFont ) {
				bodyFont = getFontName( bodyFont );
				bodyFont =
					undefined !== bodyFont
						? bodyFont.replace( ' ', '+' )
						: bodyFont;
				fontsName.push( `family=${ bodyFont }${ bodyFontWeight }` );
			}

			let headingFont = defaultTypography[ 'headings-font-family' ] || '';
			let headingFontWeight =
				parseInt( defaultTypography[ 'headings-font-weight' ] ) || '';

			if ( headingFontWeight ) {
				headingFontWeight = `:wght@${ headingFontWeight }`;
			}

			if ( headingFont ) {
				headingFont = getFontName( headingFont, bodyFont );
				headingFont =
					undefined !== headingFont
						? headingFont.replace( ' ', '+' )
						: headingFont;
				fontsName.push(
					`family=${ headingFont }${ headingFontWeight }`
				);
			}

			let otherFontsString = '';
			if ( !! fonts ) {
				for ( const font of fonts ) {
					const fontHeading = getFontName(
						font[ 'headings-font-family' ]
					).replaceAll( ' ', '+' );
					const fontHeadingWeight = font[ 'headings-font-weight' ];

					const fontBody = getFontName(
						font[ 'body-font-family' ]
					).replaceAll( ' ', '+' );
					const fontBodyWeight = font[ 'body-font-weight' ];

					otherFontsString += `&family=${ fontHeading }:wght@${ fontHeadingWeight }&family=${ fontBody }:wght@${ fontBodyWeight }`;
				}
				otherFontsString = otherFontsString.replace( /[&]{1}$/i, '' );
			}

			// Add Google fonts URL.
			if ( fontsName ) {
				const fontUrl = `https://fonts.googleapis.com/css2?${ fontsName.join(
					'&'
				) }${ otherFontsString }&display=swap`;

				node.setAttribute( 'href', fontUrl );
				document.head.insertAdjacentElement( 'afterbegin', node );
			}

			if ( 0 === typographyIndex ) {
				sendPreview( defaultTypography );
				dispatch( {
					type: 'set',
					typography: defaultTypography,
				} );
			}

			// Set default font.
			fonts = defaultFonts.concat( fonts );

			setFonts( fonts );
		}
	}, [ templateResponse ] );

	const sendPreview = ( typography ) => {
		const newTypography = headingFonts
			? { ...typography, ...headingFonts }
			: typography;

		sendPostMessage( {
			param: 'siteTypography',
			data: JSON.parse( JSON.stringify( newTypography ) ),
		} );
	};

	return (
		<div className="typography-section">
			<FontSelector
				selected={ typographyIndex }
				options={ fonts }
				onSelect={ ( event, selectedFont ) => {
					const typography = fonts[ selectedFont ] || fonts[ 0 ];
					sendPreview( typography );
					dispatch( {
						type: 'set',
						typographyIndex: selectedFont,
						typography,
					} );
				} }
			/>
		</div>
	);
};

export default TypographyWrapper;
