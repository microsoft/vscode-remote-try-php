import { select } from '@wordpress/data';

const scrollBlockToView = () => {
	// Scroll the view to the selected block after device type change in editor.
	const { getSelectedBlock } = select( 'core/block-editor' );
	const selectedBlockID = getSelectedBlock()?.clientId ? `block-${ getSelectedBlock()?.clientId }` : false;

	if ( ! selectedBlockID ) {
		return;
	}

	setTimeout( () => {
		const currentDocument = getCurrentDocument();
		const selectedBlockElementToScroll = currentDocument.getElementById( selectedBlockID );

		if ( selectedBlockElementToScroll ) {
			selectedBlockElementToScroll.scrollIntoView( { behavior: 'smooth', block: 'center', inline: 'center' } );
		}
	}, 500 );
};

const getCurrentDocument = () => {
	const tabletPreview = document.getElementsByClassName( 'is-tablet-preview' );
	const mobilePreview = document.getElementsByClassName( 'is-mobile-preview' );
	if ( 0 !== tabletPreview.length || 0 !== mobilePreview.length ) {
		const preview = tabletPreview[ 0 ] || mobilePreview[ 0 ];

		let iframe = false;

		if ( preview ) {
			iframe = preview.getElementsByTagName( 'iframe' )[ 0 ];
		}

		const iframeDocument = iframe?.contentWindow.document || iframe?.contentDocument;
		if ( iframeDocument ) {
			return iframeDocument;
		}
	}

	return document;
};

export default scrollBlockToView;
