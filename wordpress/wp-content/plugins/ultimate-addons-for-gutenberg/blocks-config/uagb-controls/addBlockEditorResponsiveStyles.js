const addBlockEditorResponsiveStyles = ( clientId, styling, deviceTypeClass ) => {
	// Desktop.
	const findResponsiveElement = document.getElementById( `block-${ clientId }` );

	if ( null !== findResponsiveElement && undefined !== findResponsiveElement ) {
		findResponsiveElement.classList.remove( 'uag-hide-desktop' ); // To remove uag-hide-desktop when toggle click.
		styling.map( ( item ) => {
			if ( item ) {
				findResponsiveElement.classList.remove( item, deviceTypeClass );
				if ( 'uag-hide-desktop' === item ) {
					findResponsiveElement.classList.add( item, deviceTypeClass );
				}
			}
			return findResponsiveElement;
		} );
	}
	// Desktop ends.

	// Tablet / Mobile Starts.
	const tabletPreview = document.getElementsByClassName( 'is-tablet-preview' );
	const mobilePreview = document.getElementsByClassName( 'is-mobile-preview' );

	if ( 0 !== tabletPreview.length || 0 !== mobilePreview.length ) {
		const preview = tabletPreview[ 0 ] || mobilePreview[ 0 ];

		const iframe = preview.getElementsByTagName( 'iframe' )[ 0 ];
		const iframeDocument = iframe.contentWindow.document || iframe.contentDocument;

		const findResponsiveElementInIframe = iframeDocument.getElementById( `block-${ clientId }` );

		if ( null !== findResponsiveElementInIframe && undefined !== findResponsiveElementInIframe ) {
			styling.map( ( item ) => {
				if ( item ) {
					findResponsiveElementInIframe.classList.remove( item, deviceTypeClass );
					if ( 'uag-hide-tab' === item || 'uag-hide-mob' === item ) {
						findResponsiveElementInIframe.classList.add( item, deviceTypeClass );
					}
				}

				return findResponsiveElementInIframe;
			} );
		}
	}
};

export default addBlockEditorResponsiveStyles;
