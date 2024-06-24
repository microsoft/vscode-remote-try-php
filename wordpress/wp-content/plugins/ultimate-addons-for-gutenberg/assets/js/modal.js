document.addEventListener( 'UAGModalEditor', function ( e ) {
	UAGBModal.init( '.uagb-block-' + e.detail.block_id, true );
} );
document.addEventListener( 'AstraQuickViewForModal', function ( e ) {
	UAGBModal.init( e.detail.class_name, false );
} );
window.UAGBModal = {
	init( mainSelector, isAdmin ) {
		const document_element = UAGBModal._getDocumentElement();
		const modalWrapper = document_element.querySelectorAll( mainSelector );
		const siteEditTheme = document_element.getElementsByClassName( 'edit-site' );
		const pageTemplate = document_element.getElementsByClassName( 'block-editor-iframe__body' );

		if ( modalWrapper?.length ) {
			for ( const modalWrapperEl of modalWrapper ) {
				const modalTrigger = modalWrapperEl.querySelector( '.uagb-modal-trigger' );
				const closeOverlayClick = modalWrapperEl.dataset.overlayclick;
				if ( modalTrigger ) {
					modalTrigger.style.pointerEvents = 'auto';

					const innerModal = modalWrapperEl?.querySelector( '.uagb-modal-popup' );
					if ( ! innerModal ) {
						continue;
					}

					if ( ! isAdmin ) {
						document_element.body?.appendChild( innerModal );
					}

					const bodyWrap = document_element.querySelector( 'body' );
					if ( ! bodyWrap ) {
						continue;
					}

					modalTrigger.addEventListener( 'click', function ( e ) {
						e.preventDefault();
						if ( ! innerModal.classList.contains( 'active' ) ) {
							innerModal.classList.add( 'active' );
							// Once this modal is active, create a focusable element to add focus onto the modal and then remove it.
							const focusElement = document.createElement( 'button' );
							focusElement.style.position = 'absolute';
							focusElement.style.opacity = '0';
							const modalFocus = innerModal.insertBefore( focusElement, innerModal.firstChild );
							modalFocus.focus();
							modalFocus.remove();
							if (
								! bodyWrap.classList.contains( 'hide-scroll' ) &&
								! siteEditTheme?.length &&
								! pageTemplate?.length &&
								! bodyWrap.classList.contains( 'wp-admin' )
							) {
								bodyWrap.classList.add( 'hide-scroll' );
							}
						}
					} );
					if ( '.uagb-modal-wrapper' === mainSelector ) { // When we get mainSelector as a uagb-modal-wrapper from AstraQuickViewForModal event we get null for closeModal. So avoid this we need to use uagb-modal-popup as mainSelector.
						mainSelector = '.uagb-modal-popup';
					}
					const closeModal = innerModal.querySelector( `${ mainSelector } .uagb-modal-popup-close` );
					if ( closeModal ) {
						closeModal.addEventListener( 'click', function () {
							if ( innerModal.classList.contains( 'active' ) ) {
								innerModal.classList.remove( 'active' );
								modalTrigger?.focus();
							}
							if ( bodyWrap.classList.contains( 'hide-scroll' ) ) {
								UAGBModal.closeModalScrollCheck( bodyWrap, document_element );
							}
						} );
					}

					if ( 'disable' !== closeOverlayClick ) {
						innerModal.addEventListener( 'click', function ( e ) {
							if (
								'enable' === closeOverlayClick &&
								innerModal.classList.contains( 'active' ) &&
								! innerModal.querySelector( '.uagb-modal-popup-wrap' ).contains( e.target )
							) {
								innerModal.classList.remove( 'active' );
							}
							if ( bodyWrap.classList.contains( 'hide-scroll' ) ) {
								UAGBModal.closeModalScrollCheck( bodyWrap, document_element );
							}
						} );
					}

					document.addEventListener( 'keyup', function ( e ) {
						const closeOnEsc = modalWrapperEl.dataset.escpress;
						if ( 27 === e.keyCode && 'enable' === closeOnEsc ) {
							if ( innerModal.classList.contains( 'active' ) ) {
								innerModal.classList.remove( 'active' );
								modalTrigger?.focus();
							}
							if ( bodyWrap.classList.contains( 'hide-scroll' ) ) {
								UAGBModal.closeModalScrollCheck( bodyWrap, document_element );
							}
						}
					} );
				}
			}
		}
	},
	// Get the Document element if it's inside an iFrame.
	_getDocumentElement() {
		let document_element = document;
		const getEditorIframe = document.querySelectorAll( 'iframe[name="editor-canvas"]' );
		if ( getEditorIframe?.length ) {
			const iframeDocument = getEditorIframe[0]?.contentWindow?.document || getEditorIframe[0]?.contentDocument;
			if ( iframeDocument ) {
				document_element = iframeDocument;
			}
		}
		return document_element;
	},
	// Close the Modal and check if the Scrollbar needs to be reactivated.
	closeModalScrollCheck( bodyWrapper, document_element ) {
		const allActiveModals = document_element.querySelectorAll( '.uagb-modal-popup.active' );
		if ( ! allActiveModals?.length ) {
			bodyWrapper.classList.remove( 'hide-scroll' );
		}
	},
};
