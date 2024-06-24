import unescape from 'lodash';
import { select, dispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';

const { imageDir, supportLink } = aiBuilderVars;

export const _unescape = ( title = '' ) => {
	// WordPress encoded chars.
	title = title.replace( '&#038;', '&' );
	title = title.replace( '&amp;', '&' );

	// Unescape all charactors.
	title = unescape( title );

	return title.__wrapped__;
};

export const savePostIfSpectraInactive = async () => {
	const currentPostId = select( 'core/editor' )?.getCurrentPostId();
	if ( currentPostId ) {
		let message;
		try {
			message = __(
				'Installed the required plugin. The page will be saved and refreshed.',
				'ai-builder'
			);
			displayNotice( 'success', message );
			await dispatch( 'core/editor' ).savePost( currentPostId );
			window.location.reload();
		} catch ( error ) {
			message = sprintf(
				/* translators: %s: error message */
				__( `Error saving the page: %s`, 'ai-builder' ),
				error
			);
			displayNotice( 'error', message );
		}
	}
};

const displayNotice = ( status, message ) => {
	( function ( wp ) {
		wp.data.dispatch( 'core/notices' ).createNotice(
			status, // Can be one of: success, info, warning, error.
			message, // Text string to display.
			{
				isDismissible: true, // Whether the user can dismiss the notice.
			}
		);
	} )( window.wp );
};

export const whiteLabelEnabled = () => {
	return aiBuilderVars.isWhiteLabeled ? true : false;
};

export const getWhileLabelName = () => {
	return aiBuilderVars.whiteLabelName;
};

export const getWhiteLabelAuthorUrl = () => {
	return aiBuilderVars.whiteLabelUrl;
};

export const isPro = () => {
	return aiBuilderVars.isPro;
};

export const getProUrl = () => {
	return aiBuilderVars.getProURL;
};

export const sendPostMessage = ( data ) => {
	// console.log( 'sendPostMessage' );
	const frame = document.getElementById( 'astra-starter-templates-preview' );
	if ( ! frame ) {
		return;
	}

	frame.contentWindow.postMessage(
		{
			call: 'starterTemplatePreviewDispatch',
			value: data,
		},
		'*'
	);
};

export const getDataUri = ( url, callback ) => {
	const image = new Image();

	image.onload = function () {
		const canvas = document.createElement( 'canvas' );
		canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
		canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

		canvas.getContext( '2d' ).drawImage( this, 0, 0 );

		// ... or get as Data URI
		callback( canvas.toDataURL( 'image/png' ) );
	};

	image.src = url;
};

export const storeCurrentState = ( currentState ) => {
	try {
		localStorage.setItem(
			'starter-templates-onboarding',
			JSON.stringify( currentState )
		);
	} catch ( err ) {
		return false;
	}
};

export const getStoredState = () => {
	return JSON.parse( localStorage.getItem( 'starter-templates-onboarding' ) );
};

export const getDefaultColorPalette = ( demo ) => {
	let defaultPaletteValues = [];

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const globalPalette =
				customizerData[ 'astra-settings' ][ 'global-color-palette' ]
					.palette || [];

			if ( globalPalette ) {
				defaultPaletteValues = [
					{
						slug: 'default',
						title: __( 'Original', 'ai-builder' ),
						colors: globalPalette,
					},
				];
			}
		}
	}
	return defaultPaletteValues;
};

export const getDefaultTypography = ( demo ) => {
	let defaultTypography = {};

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const customizerSettings = customizerData[ 'astra-settings' ] || [];
			const headingFontFamily =
				customizerSettings[ 'headings-font-family' ];

			defaultTypography = {
				default: true,
				'body-font-family': customizerSettings[ 'body-font-family' ],
				'body-font-variant': customizerSettings[ 'body-font-variant' ],
				'body-font-weight': customizerSettings[ 'body-font-weight' ],
				'font-size-body': customizerSettings[ 'font-size-body' ],
				'body-line-height': customizerSettings[ 'body-line-height' ],
				'headings-font-family': headingFontFamily,
				'headings-font-weight':
					customizerSettings[ 'headings-font-weight' ],
				'headings-line-height':
					customizerSettings[ 'headings-line-height' ],
				'headings-font-variant':
					customizerSettings[ 'headings-font-variant' ],
			};
		}
	}
	return defaultTypography;
};

export const getHeadingFonts = ( demo ) => {
	const headingFonts = {};

	const headingsTags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

	if ( demo && 'astra-site-customizer-data' in demo ) {
		const customizerData = demo[ 'astra-site-customizer-data' ] || '';
		if ( customizerData ) {
			const customizerSettings = customizerData[ 'astra-settings' ] || [];

			headingsTags.forEach( ( tag ) => {
				headingFonts[ 'font-family-' + tag ] =
					customizerSettings[ `font-family-${ tag }` ];
				headingFonts[ 'font-weight-' + tag ] =
					customizerSettings[ `font-weight-${ tag }` ];
				headingFonts[ 'text-transform-' + tag ] =
					customizerSettings[ `text-transform-${ tag }` ];
				headingFonts[ 'line-height-' + tag ] =
					customizerSettings[ `line-height-${ tag }` ];
			} );
		}
	}
	return headingFonts;
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

export const getColorScheme = ( demo ) => {
	let colorScheme = 'light';

	if (
		demo &&
		'astra-site-color-scheme' in demo &&
		'' !== demo[ 'astra-site-color-scheme' ]
	) {
		colorScheme = demo[ 'astra-site-color-scheme' ];
	}
	return colorScheme;
};

export const getAllSites = () => {
	return aiBuilderVars.all_sites;
};

export const getSupportLink = ( templateId, subject ) => {
	return `${ supportLink }&template-id=${ templateId }&subject=${ subject }`;
};

export const getGridItem = ( site ) => {
	let imageUrl = site[ 'thumbnail-image-url' ] || '';
	if ( '' === imageUrl && false === whiteLabelEnabled() ) {
		if ( aiBuilderVars.default_page_builder === 'fse' ) {
			imageUrl = `${ imageDir }spectra-placeholder.png`;
		} else {
			imageUrl = `${ imageDir }placeholder.png`;
		}
	}

	return {
		id: site.id,
		image: imageUrl,
		title: decodeEntities( site.title ),
		badge:
			'free' !== site[ 'astra-sites-type' ]
				? __( 'Premium', 'ai-builder' )
				: '',
		...site,
	};
};

export const getTotalTime = ( value ) => {
	const hours = Math.floor( value / 60 / 60 );
	const minutes = Math.floor( value / 60 ) - hours * 60;
	const seconds = value % 60;

	if ( minutes ) {
		return minutes + '.' + seconds;
	}

	return '0.' + seconds;
};
