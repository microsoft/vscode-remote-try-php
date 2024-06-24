import domReady from '@wordpress/dom-ready';
import { withinIframe, getStorgeData } from './utils';

const callFromKey = 'starterTemplatePreviewDispatch';
// const storageDataKey = 'starter-templates-zip-iframe-preview-data';
// const callFromKey = 'starterTemplatesPreviewDispatch';
const storageDataKey = 'starter-templates-iframe-preview-data';

const getDefaultLogo = () => {
	let defaultLogoURL = '';
	const defaultLogoEl = document.querySelector( '.site-logo-img img' );

	if ( defaultLogoEl ) {
		defaultLogoURL = defaultLogoEl.src;
	}
	return defaultLogoURL;
};

let defaultTemplateLogoURL = getDefaultLogo();

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

const addGoogleFontTags = ( typography ) => {
	if ( ! typography ) {
		return;
	}

	// Create Preconnect URL.
	if ( ! document.getElementById( 'google-fonts-domain' ) ) {
		const node = document.createElement( 'link' );
		node.id = 'google-fonts-domain';
		node.setAttribute( 'rel', 'preconnect' );
		node.setAttribute( 'href', 'https://fonts.gstatic.com' );
		document.head.appendChild( node );
	}

	// Create GoogleFonts URL.
	let fontLinkNode = document.getElementById( 'st-previw-google-fonts-url' );

	if ( ! fontLinkNode ) {
		fontLinkNode = document.createElement( 'link' );
		fontLinkNode.id = 'st-previw-google-fonts-url';
		fontLinkNode.setAttribute( 'rel', 'stylesheet' );
		document.head.appendChild( fontLinkNode );
	}

	const fonts = [];
	let bodyFont = typography[ 'body-font-family' ] || '';
	let bodyFontWeight = parseInt( typography[ 'body-font-weight' ] ) || '';
	if ( bodyFontWeight ) {
		bodyFontWeight = `:wght@${ bodyFontWeight }`;
	}
	if ( bodyFont ) {
		bodyFont = getFontName( bodyFont );
		bodyFont = bodyFont.replace( ' ', '+' );
		fonts.push( `family=${ bodyFont }${ bodyFontWeight }` );
	}

	let headingFont = typography[ 'headings-font-family' ] || '';
	let headingFontWeight =
		parseInt( typography[ 'headings-font-weight' ] ) || '';
	if ( headingFontWeight ) {
		headingFontWeight = `:wght@${ headingFontWeight }`;
	}
	if ( headingFont ) {
		headingFont = getFontName( headingFont, bodyFont );
		headingFont = headingFont.replace( ' ', '+' );
		fonts.push( `family=${ headingFont }${ headingFontWeight }` );
	}

	const fontUrl = `https://fonts.googleapis.com/css2?${ fonts.join(
		'&'
	) }&display=swap`;

	fontLinkNode.setAttribute( 'href', fontUrl );
};

const addTypographyCss = ( typography ) => {
	if ( ! typography ) {
		return;
	}

	const headingsTags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
	// let styleNode = document.getElementById( 'starter-templates-zip-typography' );
	let styleNode = document.getElementById( 'starter-templates-typography' );

	if ( ! styleNode ) {
		styleNode = document.createElement( 'style' );
		styleNode.id = 'starter-templates-typography';
		styleNode.setAttribute( 'rel', 'stylesheet' );
		document.head.appendChild( styleNode );
	}

	let css = '';

	css +=
		'body, button, input, select, textarea, .ast-button, .ast-custom-button {';
	css += '	font-family: ' + typography[ 'body-font-family' ] + ';';
	css += '	font-weight: ' + typography[ 'body-font-weight' ] + ';';
	css +=
		'	font-size: ' +
		typography[ 'font-size-body' ].desktop +
		typography[ 'font-size-body' ][ 'desktop-unit' ] +
		';';
	css += '	line-height: ' + typography[ 'body-line-height' ] + ';';
	css += '}';
	css +=
		'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a {';
	css += '	font-family: ' + typography[ 'headings-font-family' ] + ';';
	css += '	line-height: ' + typography[ 'headings-line-height' ] + ';';
	css += '	font-weight: ' + typography[ 'headings-font-weight' ] + ';';
	css += '}';

	headingsTags.forEach( ( tag ) => {
		const fontFamily =
			typography[ 'font-family-' + tag ] === 'inherit'
				? typography[ 'headings-font-family' ]
				: typography[ 'font-family-' + tag ];
		const fontWeight =
			typography[ 'font-weight-' + tag ] === 'inherit'
				? typography[ 'headings-font-weight' ]
				: typography[ 'font-weight-' + tag ];

		let heading_css = '';

		if ( fontFamily !== undefined && '' !== fontFamily ) {
			heading_css += `${ tag }, .entry-content ${ tag } {`;
			heading_css += '	font-family: ' + fontFamily + ';';
		}

		if (
			undefined !== typography[ 'line-height-' + tag ] &&
			typography[ 'line-height-' + tag ] !== ''
		) {
			heading_css +=
				'	line-height: ' + typography[ 'line-height-' + tag ] + ';';
		}

		if ( fontWeight !== undefined && '' !== fontWeight ) {
			heading_css += '	font-weight: ' + fontWeight + ';';
		}

		css += heading_css !== '' ? heading_css + '}' : '';
	} );

	styleNode.innerHTML = css;
};
/* eslint-disable */
const hideSiteTitleElement = ( isShowTitle ) => {
	const desktopHeader = document.getElementById( 'ast-desktop-header' );
	const siteIdentityContainer =
		desktopHeader &&
		desktopHeader.querySelectorAll( '.ast-site-identity' )[ 0 ];
	const siteTitleElems =
		siteIdentityContainer &&
		siteIdentityContainer.querySelectorAll( '.ast-site-title-wrap' )[ 0 ];

	if ( siteTitleElems ) {
		if ( isShowTitle ) {
			siteTitleElems.style.display = 'block';
		} else {
			siteTitleElems.style.display = 'none';
		}
	}
};
/* eslint-enable */
const setPreviewValues = ( eventData ) => {
	const param = eventData.value.param;

	switch ( param ) {
		case 'siteLogo':
			const logoElement =
				document.querySelectorAll( '.site-logo-img img' );

			if ( defaultTemplateLogoURL === '' ) {
				defaultTemplateLogoURL = getDefaultLogo();
			}
			let logoURL = eventData.value.data.url || defaultTemplateLogoURL;
			logoURL = eventData.value.data.dataUri || logoURL;

			if ( 0 === logoElement.length && '' !== logoURL ) {
				const logoSpan = document.createElement( 'span' );
				logoSpan.classList.add( 'site-logo-img' );

				const anchorElem = document.createElement( 'a' );
				anchorElem.setAttribute( 'class', 'custom-logo-link' );
				anchorElem.setAttribute( 'href', '#' );
				anchorElem.setAttribute( 'aria-current', 'page' );

				logoSpan.appendChild( anchorElem );

				const imgElem = document.createElement( 'img' );
				imgElem.classList.add( 'custom-logo' );
				imgElem.setAttribute( 'src', logoURL );

				anchorElem.appendChild( imgElem );

				const desktopHeader =
					document.getElementById( 'ast-desktop-header' );
				const siteIdentityContainer =
					desktopHeader.querySelectorAll( '.ast-site-identity' )[ 0 ];

				const siteTitleElems = siteIdentityContainer.querySelectorAll(
					'.ast-site-title-wrap'
				)[ 0 ];
				siteIdentityContainer.insertBefore( logoSpan, siteTitleElems );

				// Hide site title wrap when logo is uploaded.
				// if ( siteTitleElems ) {
				// 	siteTitleElems.style.display = 'none';
				// }

				const width = eventData.value.data.width || '';
				if ( '' !== width ) {
					imgElem.style.width = width + 'px';
					imgElem.style.maxWidth = width + 'px';
				}
			} else if ( '' !== logoURL ) {
				// eslint-disable-next-line no-unused-vars
				for ( const [ key, element ] of Object.entries(
					logoElement
				) ) {
					// Remove srcset and set logo image src.
					element.removeAttribute( 'srcset' );
					element.setAttribute( 'src', logoURL );
					const width = eventData.value.data.width;
					if ( '' !== width ) {
						element.style.width = width + 'px';
						element.style.maxWidth = width + 'px';
					}
				}
				// hideSiteTitleElement();
			}

			break;

		case 'colorPalette':
			const colorPalette = eventData.value.data.colors || [];
			const paletteStylePrefix =
				starter_templates_zip_preview.AstColorPaletteVarPrefix;
			const paletteEleStylePrefix =
				starter_templates_zip_preview.AstEleColorPaletteVarPrefix;
			// If colorPalette is empty, remove and reset to default from the template.
			if ( colorPalette.length === 0 ) {
				document
					.querySelector( 'body' )
					.classList.remove( 'starter-templates-preview-palette' );

				const styleSheetPalette = document.getElementsByClassName(
					'starter-templates-preview-palette'
				);

				// .classList.remove( 'starter-templates-zip-preview-palette' );
				// const styleSheetPalette = document.getElementsByClassName(
				// 	'starter-templates-zip-preview-palette'

				if ( styleSheetPalette.length > 0 ) {
					styleSheetPalette[ 0 ].remove();
				}

				return;
			}
			document
				.querySelector( 'body' )
				// .classList.add( 'starter-templates-zip-preview-palette' );
				.classList.add( 'starter-templates-preview-palette' );
			// Set CSS variables for palette.
			const colorPaletteStyleSheet = Object.entries( colorPalette )
				.map( ( paletteItem, index ) => {
					return [
						`--e-global-color-${ paletteEleStylePrefix[
							index
						].replace( /-/g, '' ) }: ${ paletteItem[ 1 ] };`,
						`${ paletteStylePrefix }${ index }: ${ paletteItem[ 1 ] };`,
					];
				} )
				.map( ( item ) => item.join( '' ) )
				.join( '' );
			let styleTag = document.getElementById(
				'starter-templates-preview-palette-css'
			);
			if ( ! styleTag ) {
				styleTag = document.createElement( 'style' );
				// styleTag.id = 'starter-templates-zip-preview-palette-css';
				styleTag.id = 'starter-templates-preview-palette-css';
				styleTag.setAttribute( 'rel', 'stylesheet' );
				document.head.appendChild( styleTag );
			}
			// styleTag.innerHTML = `.starter-templates-zip-preview-palette{ ${ colorPaletteStyleSheet } }`;

			styleTag.innerHTML = `.starter-templates-preview-palette{ ${ colorPaletteStyleSheet } }`;

			break;

		case 'siteTypography':
			// If typography is not set, then remove the already added typograhy from the DOM.
			if ( ! Object.keys( eventData.value.data ).length ) {
				const styleSheetTypography = document.getElementById(
					// 'starter-templates-zip-typography'
					'starter-templates-typography'
				);

				if ( styleSheetTypography ) {
					styleSheetTypography.remove();
				}

				return;
			}

			addGoogleFontTags( eventData.value.data );
			addTypographyCss( eventData.value.data );
			break;

		case 'siteTitle':
			hideSiteTitleElement( eventData.value.data );
			break;

		case 'clearPreviewAssets':
			const styleSheetTypography = document.getElementById(
				// 'starter-templates-zip-typography'
				'starter-templates-typography'
			);
			if ( styleSheetTypography ) {
				styleSheetTypography.remove();
			}

			document
				.querySelector( 'body' )
				// 	.classList.remove( 'starter-templates-zip-preview-palette' );

				.classList.remove( 'starter-templates-preview-palette' );

			// const styleSheetPalette = document.getElementsByClassName(
			// 	'starter-templates-zip-preview-palette' );
			const styleSheetPalette = document.getElementsByClassName(
				'starter-templates-preview-palette'
			);

			if ( styleSheetPalette.length > 0 ) {
				styleSheetPalette[ 0 ].remove();
			}

			break;

		case 'completeOnboarding':
			// localStorage.removeItem( storageDataKey );
			localStorage.removeItem( 'starter-templates-iframe-preview-data' );
	}
};

// eslint-disable-next-line
window.addEventListener(
	'message',
	function ( event ) {
		if ( ! withinIframe() ) {
			return;
		}

		console.log( 'addEventListener message: ', event );

		if (
			typeof event.data === 'object' &&
			callFromKey === event.data.call
		) {
			const eventData = event.data;
			let storageData = JSON.parse(
				localStorage.getItem( storageDataKey )
			);

			// if storageData is not set yet, set it to empty values.
			if ( storageData === null ) {
				storageData = {};
				storageData.data = {};
			}

			storageData.data[ eventData.value.param ] = eventData.value.data;

			// Add curent URL to event data.
			delete storageData.data.clearPreviewAssets;
			eventData.url = window.location.origin;
			storageData.url = window.location.origin;

			// If it's a cleanStorage message, Clear the local storage and render the cleared data.
			// If logo is set in the starter templates customizer, then that will be rendered.
			if ( eventData.value.param === 'cleanStorage' ) {
				delete storageData.data.cleanStorage;
				storageData.data.siteLogo = eventData.value.data;
				storageData.data.colorPalette = {};
				storageData.data.siteTypography = {};

				Object.keys( storageData.data ).map( ( key ) =>
					setPreviewValues( {
						value: {
							param: key,
							data: storageData.data[ key ],
						},
					} )
				);
			} else {
				setPreviewValues( eventData );
			}

			localStorage.setItem(
				storageDataKey,
				JSON.stringify( storageData )
			);
		}
	},
	false
);

domReady( () => {
	if ( ! withinIframe() ) {
		return;
	}

	const styleTag = document.createElement( 'style' );
	styleTag.id = 'starter-templates-logo-css';
	document.getElementsByTagName( 'head' )[ 0 ].appendChild( styleTag );
	styleTag.innerHTML = `.site-logo-img img { transition: unset; } #wpadminbar { display: none; } html{  margin-top: 0 !important; }}`;

	const storageData = getStorgeData( storageDataKey );

	if ( !! storageData ) {
		Object.keys( storageData.data ).map( ( key ) =>
			setPreviewValues( {
				value: {
					param: key,
					data: storageData.data[ key ],
				},
			} )
		);
	}
} );
