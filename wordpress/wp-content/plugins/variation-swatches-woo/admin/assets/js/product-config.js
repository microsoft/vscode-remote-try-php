( function ( $ ) {
	const SWATCHES_PRODUCT = {
		init() {
			SWATCHES_PRODUCT.Events();
			SWATCHES_PRODUCT.addSubmenuIcon();
			$( '.cfvsw-attribute-item-color' ).wpColorPicker();
		},
		addSubmenuIcon: () => {
			const getAnchor = $( 'li.cfvsw_tab_options.cfvsw_tab_tab a' );
			const getColor = getAnchor.css( 'color' );
			if ( 0 === $( '#cfvsw-wc-panel-icon-style' ).length ) {
				$( 'html > head' ).append(
					`<style id='cfvsw-wc-panel-icon-style'>li.cfvsw_tab_options.cfvsw_tab_tab a svg{fill:${ getColor };}</style>`
				);
			}
			getAnchor.prepend( SWATCHES_PRODUCT.icon );
			getAnchor.mouseenter( function () {
				const getStyle = $( '#cfvsw-wc-panel-icon-style' );
				if ( getStyle.attr( 'color-hover' ) ) {
					return;
				}
				const anchor = $( this );
				setTimeout( () => {
					getStyle.append(
						`li.cfvsw_tab_options.cfvsw_tab_tab a:hover svg{fill:${ anchor.css(
							'color'
						) };}`
					);
					getStyle.attr( 'color-hover', 1 );
				}, 50 );
			} );
		},
		icon:
			'<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 35 35" fill="none"><path d="M21.533 2.19997C20.276 3.42697 19.494 5.13397 19.494 7.02897V18.253C19.494 19.902 18.15 21.246 16.501 21.246H8.976C7.327 21.246 5.984 19.902 5.984 18.253C5.984 16.604 7.327 15.259 8.976 15.259H17.226V9.27397H8.976C8.47 9.27397 7.969 9.31497 7.479 9.39497C3.235 10.111 0 13.807 0 18.253C0 22.162 2.499 25.486 5.984 26.715C6.465 26.885 6.965 27.026 7.479 27.102C7.969 27.192 8.47 27.237 8.976 27.237H16.501C17.014 27.237 17.511 27.192 17.997 27.102C18.51 27.026 19.011 26.885 19.495 26.715C22.98 25.486 25.48 22.162 25.48 18.253V15.259C28.789 15.259 31.467 12.58 31.467 9.27497H25.48V7.02797C25.48 6.61297 25.816 6.28297 26.225 6.28297H27.725C29.55 6.28297 31.209 5.55997 32.422 4.37497C33.509 3.31597 34.244 1.89097 34.416 0.292969H26.226C24.406 0.293969 22.747 1.02197 21.533 2.19997Z"/><path xmlns="http://www.w3.org/2000/svg" d="M8.97601 28.7271C7.32501 28.7271 5.98401 30.0631 5.98401 31.7151C5.98401 33.3651 7.32501 34.7071 8.97601 34.7071C10.63 34.7071 11.971 33.3651 11.971 31.7131C11.971 30.0631 10.629 28.7271 8.97601 28.7271Z"/><path xmlns="http://www.w3.org/2000/svg" d="M17.953 28.7271C16.3 28.7271 14.965 30.0631 14.965 31.7151C14.965 33.3651 16.3 34.7071 17.953 34.7071C19.611 34.7071 20.946 33.3651 20.946 31.7131C20.945 30.0631 19.611 28.7271 17.953 28.7271Z"/></svg>',
		ajx: ( data ) => {
			const ajaxObj = {
				method: 'POST',
				url: cfvsw_swatches_product.ajax_url,
				data,
				dataType: 'json',
				processData: false,
				contentType: false,
			};
			return jQuery.ajax( ajaxObj );
		},
		addSectionLoader: ( section = null ) => {
			if ( section ) {
				section.block( {
					message: null,
					overlayCSS: {
						background: '#dddddd',
						opacity: 0.5,
					},
				} );
			}
		},
		openCloseArrow() {
			const handle = $( this );
			const parentContainer = handle.closest( '.cfvsw-metabox' );
			// Check if select box not blank.
			if ( parentContainer.hasClass( 'cfvsw-term-box' ) ) {
				const getHiddenInput = parentContainer
					.closest( '.cfvsw-attribute-itemes' )
					.closest( '.cfvsw-metabox' )
					.find( '.cfvsw-term-parent-attr' )
					.val();
				if ( '' === getHiddenInput ) {
					return;
				}
			}
			const metaboxContent = parentContainer.children(
				'.cfvsw-metabox-content'
			);
			if ( ! parentContainer.hasClass( 'cfvsw_open' ) ) {
				parentContainer.addClass( 'cfvsw_open' );
				metaboxContent.slideDown();
			} else {
				parentContainer.removeClass( 'cfvsw_open' );
				metaboxContent.slideUp();
			}
		},
		changeSelect() {
			const currenctSelect = $( this );
			const getContainer = currenctSelect.closest(
				'.cfvsw-attribute-wrapper'
			);
			const value = currenctSelect.val();
			const getNameAttr = currenctSelect.attr( 'data-name' );
			const getHiddenInput = getContainer.find(
				`input[name="${ getNameAttr }"]`
			);
			getHiddenInput.val( value );

			// If value blank then disable terms sections.
			const getDisableAttr = getContainer.attr( 'attr-value-unvailable' );
			if ( '' === value && ! getDisableAttr ) {
				getContainer.attr( 'attr-value-unvailable', 1 );
			} else if ( '' !== value && getDisableAttr ) {
				getContainer.removeAttr( 'attr-value-unvailable' );
			}

			// Taxonomy hide show.
			if (
				! currenctSelect.hasClass( 'cfvsw-inside-wrapper-hide-show' )
			) {
				return;
			}
			getContainer.find( '.cfvsw-attribute-item-container' ).hide();
			if ( [ 'color', 'image', 'label' ].includes( value ) ) {
				getContainer.find( `[data-container="${ value }"]` ).show();
			}
		},
		saveSwatches() {
			const button = $( this );
			const container = button.closest( '.cfvsw-swatches-settings' );
			const action = container.find( 'input[name="swatches_action"]' );
			if ( action.length && action.length > 0 ) {
				const actionValue = action.val();
				if ( ! actionValue || '' === actionValue ) {
					return;
				}
				SWATCHES_PRODUCT.addSectionLoader( container );
				const getSection = button
					.closest( '.cfvsw-swatches-settings' )
					.find( '.cfvsw-swatches-input-section' );
				const getSectionHtml = getSection.clone();
				const form = $( '<form></form>' );
				form.hide();
				form.append( getSectionHtml );
				$( 'body' ).append( form );
				setTimeout( () => {
					const formData = new FormData( form[ 0 ] );
					formData.append( 'action', actionValue );
					form.remove();
					const putContent = SWATCHES_PRODUCT.ajx( formData );
					putContent.success( function ( response ) {
						container.unblock();
						if ( response.data.message ) {
							SWATCHES_PRODUCT.setSuccessMessage(
								response.data.message
							);
						}
					} );
				}, 100 );
			}
		},
		update_swatches_reset_data: ( toDo = 'update' ) => {
			// Collect data.
			const getWrapper = $( '.cfvsw-swatches-settings' );
			if (
				! getWrapper ||
				! getWrapper.length ||
				! getWrapper.length > 0
			) {
				return;
			}
			const getInputContainer = getWrapper.find(
				'.cfvsw-swatches-input-section'
			);
			const putTemplate = getInputContainer.find(
				'.cfvsw-swatches-taxonomy-section'
			);
			const getButtonContainer = getWrapper.find(
				'.cfvsw-save-reset-swatches'
			);

			const getProductId = getInputContainer
				.children( 'input[name="product_id"]' )
				.val();
			const security = getInputContainer
				.children( 'input[name="security"]' )
				.val();
			if (
				getProductId &&
				'' !== getProductId &&
				security &&
				'' !== security &&
				putTemplate.length > 0
			) {
				const formData = new FormData();
				formData.append( 'security', security );
				formData.append( 'product_id', getProductId );
				if ( 'reset' === toDo ) {
					formData.append(
						'action',
						'cfvsw_reset_product_swatches_data'
					);
				} else {
					formData.append(
						'action',
						'cfvsw_update_product_swatches_data'
					);
				}
				SWATCHES_PRODUCT.addSectionLoader( getWrapper );
				const putContent = SWATCHES_PRODUCT.ajx( formData );
				putContent.success( function ( response ) {
					getWrapper.unblock();
					if (
						response.success &&
						response.data &&
						response.data.template &&
						'' !== response.data.template
					) {
						putTemplate.html( response.data.template );
						SWATCHES_PRODUCT.setSuccessMessage(
							response.data.message
						);
						$( '.wc-enhanced-select' ).select2( {
							minimumResultsForSearch: Infinity,
						} );
						$( '.cfvsw-attribute-item-color' ).wpColorPicker();
						if ( getButtonContainer.hasClass( 'hidden-buttons' ) ) {
							getButtonContainer.removeClass( 'hidden-buttons' );
						}
					}
					// When no attribute available.
					if ( '' === response.data.template ) {
						putTemplate.html(
							`<p class="cfvsw-swatches-no-visible-attr">${ response.data.message }</p>`
						);
						getButtonContainer.addClass( 'hidden-buttons' );
					}
				} );
			}
		},
		setSuccessMessage: ( message ) => {
			const warpper = $( '.cfvsw-swatches-settings-notice' );
			warpper.addClass( 'notice notice-success' );
			warpper.children( 'p' ).html( message );
			warpper.show();
			setTimeout( () => {
				warpper.slideUp();
			}, 2000 );
		},
		removeSwatchesImage() {
			const removeBtn = $( this );
			const getContainer = removeBtn.closest( '.field-image' );
			const previewImage = getContainer.find( '.cfvsw-image-preview' );
			getContainer
				.find( '.cfvsw-image-preview' )
				.attr( 'src', previewImage.attr( 'data-placeholder-image' ) );
			getContainer.find( '.cfvsw-save-image' ).val( '' );
			removeBtn.hide();
		},
		Events() {
			$( document ).on(
				'change',
				'.cfvsw-attribute-type-select',
				SWATCHES_PRODUCT.changeSelect
			);
			// Save swatches.
			$( document ).on(
				'click',
				'.cfvsw-save-swatches',
				SWATCHES_PRODUCT.saveSwatches
			);
			// Reset swatches.
			$( document ).on( 'click', '.cfvsw-reset-swatches', function () {
				SWATCHES_PRODUCT.update_swatches_reset_data( 'reset' );
			} );
			// Update swatches in reload.
			$( 'body' ).on( 'reload', function () {
				SWATCHES_PRODUCT.update_swatches_reset_data();
			} );
			// Remove image.
			$( document ).on(
				'click',
				'.cfvsw_remove_image_attr_item',
				SWATCHES_PRODUCT.removeSwatchesImage
			);
			$( document ).on(
				'click',
				'.cfvsw-metabox-handle',
				SWATCHES_PRODUCT.openCloseArrow
			);
			// Image Upload.
			let fileFrame;
			let currentButton;
			$( document ).on(
				'click',
				'.cfvsw_upload_image_attr_item',
				function ( event ) {
					currentButton = $( this );
					event.preventDefault();
					// If the media frame already exists, reopen it.
					if ( fileFrame ) {
						// Open frame.
						fileFrame.open();
						return;
					}
					// Create the media frame.
					fileFrame = wp.media.frames.fileFrame = wp.media( {
						title: cfvsw_swatches_product.image_upload_text.title,
						button: {
							text:
								cfvsw_swatches_product.image_upload_text
									.button_title,
						},
						multiple: false, // Set to true to allow multiple files to be selected
					} );
					// When an image is selected, run a callback.
					fileFrame.on( 'select', function () {
						// We set multiple to false so only get one image from the uploader
						const attachment = fileFrame
							.state()
							.get( 'selection' )
							.first()
							.toJSON();
						const attachmentUrl = attachment.url;
						const getContainer = currentButton.closest(
							'.field-image'
						);
						getContainer
							.find( '.cfvsw-image-preview' )
							.attr( 'src', attachmentUrl );

						getContainer
							.find( '.cfvsw-save-image' )
							.val( attachment.id );
						getContainer
							.find( '.cfvsw_remove_image_attr_item' )
							.show();
					} );
					// Finally, open the modal
					fileFrame.open();
				}
			);
		},
	};
	SWATCHES_PRODUCT.init();
} )( jQuery );
