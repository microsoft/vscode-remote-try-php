const AddGBSStylesDom = ( globalBlockStyleId, styleText ) => {

    const putStyleInHead = ( current_document, rawStyle, tagId ) => {
        if ( ! rawStyle ) return;
        tagId = 'spectra-gbs-' + globalBlockStyleId;

        const isExistTag = current_document.getElementById( tagId );
        if( ! isExistTag ){
            const node = document.createElement( 'style' )
            node.setAttribute( 'id', tagId );
            node.textContent = rawStyle;
            current_document.head.appendChild( node )
        }else{
            isExistTag.textContent = rawStyle
        }
    }

    putStyleInHead( document, styleText, globalBlockStyleId );


	setTimeout( () => {
		const getAllIFrames = document.getElementsByTagName( 'iframe' );
		if ( ! getAllIFrames?.length ) {
			return;
		}

		for ( const iterateIFrames of getAllIFrames ) {
			const iframeDocument = iterateIFrames?.contentWindow.document || iterateIFrames?.contentDocument;
			if( ! iframeDocument?.head ){
				continue;
			}

            putStyleInHead( iframeDocument, styleText, globalBlockStyleId );
		} // Loop end.
	} );
};

export default AddGBSStylesDom;