const addBlockEditorDynamicStyles = () => {
	setTimeout( () => {
		const getAllIFrames = document.getElementsByTagName( 'iframe' );
		if ( ! getAllIFrames?.length ) {
			return;
		}

		const cloneLinkTag = ( linkId ) => {
			const getTag = document.getElementById( linkId );
			return getTag ? getTag.cloneNode( true ) : false;
		}

		const cloneStyleTag = ( styleId ) => {
			const getStyleTag = document.getElementById( styleId );
			return getStyleTag ? getStyleTag.textContent : false;
		}
		
		const dashiconsCss = cloneLinkTag( 'dashicons-css' );
		const blockCssCss = cloneLinkTag( 'uagb-block-css-css' );
		const slickStyle = cloneLinkTag( 'uagb-slick-css-css' );
		const swiperStyle = cloneLinkTag( 'uagb-swiper-css-css' );
		const aosStyle = cloneLinkTag( 'uagb-aos-css-css' );

		const editorStyle = cloneStyleTag( 'uagb-editor-styles' );
		const editorProStyle = cloneStyleTag( 'spectra-pro-editor-styles' );
		const spacingStyle = cloneStyleTag( 'uagb-blocks-editor-spacing-style' );
		const editorCustomStyle = cloneStyleTag( 'uagb-blocks-editor-custom-css' );

		for ( const iterateIFrames of getAllIFrames ) {
			const iframeDocument = iterateIFrames?.contentWindow.document || iterateIFrames?.contentDocument;
			if( ! iframeDocument?.head ){
				continue;
			}

			const copyLinkTag = ( clonedTag, tagId ) => {
				if ( ! clonedTag ) return;
				const isExistTag = iframeDocument.getElementById( tagId );
				if ( isExistTag ) return;
				iframeDocument.head.appendChild( clonedTag );
			}

			const copyStyleTag = ( clonedTag, tagId ) => {
				if ( ! clonedTag ) return;
				const isExistTag = iframeDocument.getElementById( tagId );
				if( ! isExistTag ){
					const node = document.createElement( 'style' )
					node.setAttribute( 'id', tagId );
					node.textContent = clonedTag;
					iframeDocument.head.appendChild( node )
				}else{
					isExistTag.textContent = clonedTag
				}
			}

			copyLinkTag( blockCssCss, 'uagb-block-css-css' );
			copyLinkTag( dashiconsCss, 'dashicons-css' );
			copyLinkTag( slickStyle, 'uagb-slick-css-css' );
			copyLinkTag( swiperStyle, 'uagb-swiper-css-css' );
			copyLinkTag( aosStyle, 'uagb-aos-css-css' );

			copyStyleTag( editorStyle, 'uagb-editor-styles' );
			copyStyleTag( editorProStyle, 'spectra-pro-editor-styles' );
			copyStyleTag( spacingStyle, 'uagb-blocks-editor-spacing-style' );
			copyStyleTag( editorCustomStyle, 'uagb-blocks-editor-custom-css' );
		} // Loop end.
	} );
};

export default addBlockEditorDynamicStyles;