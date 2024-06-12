import unescape from 'lodash';
import { select, dispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

export const _unescape = ( title = '' ) => {
	// WordPress encoded chars.
	title = title.replace( '&#038;', '&' );
	title = title.replace( '&amp;', '&' );

	// Unescape all charactors.
	title = unescape( title );

	return title.__wrapped__;
};

export const getBlocks = () => {
	const result = {
		patterns: [],
		wireframes: [],
	};

	const { allBlocks } = astraSitesVars;

	for ( const blockId in allBlocks ) {
		const wireframe = allBlocks[ blockId ].wireframe || {};

		if ( Object.keys( wireframe ).length ) {
			result.wireframes.push( allBlocks[ blockId ] );
		} else {
			result.patterns.push( allBlocks[ blockId ] );
		}
	}

	return result;
};

export const getBlocksPages = () => {
	const result = [];

	const { allBlocksPages } = astraSitesVars;

	for ( const blockId in allBlocksPages ) {
		result.push( allBlocksPages[ blockId ] );
	}

	return result;
};

export const getPatterns = () => {
	const items = getBlocks();
	return items.patterns;
};

export const getWireframes = () => {
	const items = getBlocks();
	return items.wireframes;
};

export const savePostIfSpectraInactive = async () => {
	const currentPostId = select( 'core/editor' )?.getCurrentPostId();
	if ( currentPostId ) {
		let message;
		try {
			message = __(
				'Installed the required plugin. The page will be saved and refreshed.',
				'astra-sites'
			);
			displayNotice( 'success', message );
			await dispatch( 'core/editor' ).savePost( currentPostId );
			window.location.reload();
		} catch ( error ) {
			message = sprintf(
				/* translators: %s: error message */
				__( `Error saving the page: %s`, 'astra-sites' ),
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

export const getDefaultBlockPalette = () => {
	return astraSitesVars?.block_color_palette?.[ 'style-1' ];
};

export const getDefaultPagePalette = () => {
	return astraSitesVars.page_color_palette[ 'style-1' ];
};

export const getActiveBlockPaletteSlug = () => {
	return 'style-1';
};

export const generateContentForAllCategories = async (
	allPatternsCategories,
	setDynamicContent,
	dynamicContentFlagSet,
	setCurrentCategory,
	setCreditsDetails,
	type
) => {
	const succeeded = [];
	let isLastCat = false;
	for ( const [ index, item ] of allPatternsCategories.entries() ) {
		if ( ! item?.id ) {
			continue;
		}
		setCurrentCategory( item );
		try {
			if ( index === allPatternsCategories.length - 1 ) {
				isLastCat = true;
			}

			const catFormData = new window.FormData();
			catFormData.append( 'action', 'ast-block-templates-regenerate' );
			catFormData.append(
				'security',
				astraSitesVars.ai_content_ajax_nonce
			);
			catFormData.append( 'category', item.id );
			catFormData.append( 'regenerate', false );
			catFormData.append( 'block_type', type );
			catFormData.append( 'is_last_category', isLastCat );
			const response = await apiFetch( {
				url: astraSitesVars.ajax_url,
				method: 'POST',
				body: catFormData,
			} );
			if ( response.success ) {
				setDynamicContent( response.data.data );
				dynamicContentFlagSet( item.id, true );
				setCreditsDetails( response.data.spec_credit_details );
				succeeded.push( true );
			}
		} catch ( error ) {
			succeeded.push( false );
		}
	}

	return succeeded.some( ( item ) => !! item );
};
