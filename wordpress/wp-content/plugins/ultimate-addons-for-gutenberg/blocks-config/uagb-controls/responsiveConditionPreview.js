const responsiveConditionPreview = ( props ) => {
	// Desktop.
	const element = document.getElementById( 'block-' + props.clientId );

	const desktopStyle = document.getElementById( props.clientId + '-desktop-hide-block' );
	if ( props.attributes.UAGHideDesktop ) {
		if ( null !== element && undefined !== element ) {
			if ( null === desktopStyle || undefined === desktopStyle ) {
				const $style = document.createElement( 'style' );
				$style.setAttribute( 'id', props.clientId + '-desktop-hide-block' );

				$style.innerHTML =
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-desktop{ background:repeating-linear-gradient(125deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 2px, transparent 9px); border: 1px solid rgba(0, 0, 0, 0.15); } ';
				$style.innerHTML +=
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-desktop:before{ content: ""; display: block; position: absolute; top: 0; left: 0; width: 100%; height: 100%;  background-color: rgba(255, 255, 255, 0.6); z-index: 9997; } ';

				document.head.appendChild( $style );
			}
		}
	} else if ( null !== desktopStyle && undefined !== desktopStyle ) {
		desktopStyle.remove();
	}
	const tabletPreview = document.getElementsByClassName( 'is-tablet-preview' );
	const mobilePreview = document.getElementsByClassName( 'is-mobile-preview' );

	if ( 0 !== tabletPreview.length || 0 !== mobilePreview.length ) {
		const preview = tabletPreview[ 0 ] || mobilePreview[ 0 ];

		let iframe = false;

		if ( preview ) {
			iframe = preview.getElementsByTagName( 'iframe' )[ 0 ];
		}

		const iframeDocument = iframe?.contentWindow.document || iframe?.contentDocument;

		if ( ! iframe || ! iframeDocument ) {
			return;
		}
		const iframeTabletElement = iframeDocument.getElementById( props.clientId + '-tablet-hide-block' );

		if ( props.attributes.UAGHideTab ) {
			if ( null === iframeTabletElement || undefined === iframeTabletElement ) {
				const $style = document.createElement( 'style' );
				$style.setAttribute( 'id', props.clientId + '-tablet-hide-block' );

				$style.innerHTML =
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-tablet{ background:repeating-linear-gradient(125deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 2px, transparent 9px); border: 1px solid rgba(0, 0, 0, 0.15); } ';
				$style.innerHTML +=
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-tablet:before{ content: ""; display: block; position: absolute; top: 0; left: 0; width: 100%; height: 100%;  background-color: rgba(255, 255, 255, 0.6); z-index: 9997; } ';

				setTimeout( () => {
					iframeDocument.head.appendChild( $style );
				}, 500 );
			}
		} else if ( null !== iframeTabletElement && undefined !== iframeTabletElement ) {
			iframeTabletElement.remove();
		}
		const iframeMobileElement = iframeDocument.getElementById( props.clientId + '-mobile-hide-block' );
		if ( props.attributes.UAGHideMob ) {
			if ( null === iframeMobileElement || undefined === iframeMobileElement ) {
				const $style = document.createElement( 'style' );
				$style.setAttribute( 'id', props.clientId + '-mobile-hide-block' );

				$style.innerHTML =
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-mobile{ background:repeating-linear-gradient(125deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 1px, transparent 2px, transparent 9px); border: 1px solid rgba(0, 0, 0, 0.15); } ';
				$style.innerHTML +=
					'.uagb-block-' +
					props.clientId.substr( 0, 8 ) +
					'.uagb-editor-preview-mode-mobile:before{ content: ""; display: block; position: absolute; top: 0; left: 0; width: 100%; height: 100%;  background-color: rgba(255, 255, 255, 0.6); z-index: 9997; } ';

				setTimeout( () => {
					iframeDocument.head.appendChild( $style );
				}, 500 );
			}
		} else if ( null !== iframeMobileElement && undefined !== iframeMobileElement ) {
			iframeMobileElement.remove();
		}
	}
};

export default responsiveConditionPreview;
