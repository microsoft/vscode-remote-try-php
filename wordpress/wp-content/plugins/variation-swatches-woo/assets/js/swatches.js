( function ( $ ) {
	const removeAttrClass = cfvsw_swatches_settings.remove_attr_class;
	const addRemoveWithCommonClass = `${ removeAttrClass } cfvsw-swatches-out-of-stock`;
	const addRemoveDisableClass = removeAttrClass + '-disable';
	const addRemoveDisableClassCommon = `${ addRemoveDisableClass } cfvsw-swatches-disabled`;

	// Disable out of stock attr.
	const SW = {
		init: () => {
			if ( cfvsw_swatches_settings.disable_out_of_stock ) {
				SW.firstTime();
				SW.Events();
			}
		},
		firstTime: () => {
			const getVariationTable = $( 'table.variations' ).not(
				'.cfvsw-variation-disable-logic'
			);
			getVariationTable.addClass( 'cfvsw-variation-disable-logic' );
			getVariationTable.each( function () {
				const table = $( this );
				const getForm = table.closest(
					'[data-product_id][data-product_variations]'
				);
				const getFormData = getForm.data( 'product_variations' );
				SW.swatchesOptions( getForm, getFormData );
			} );
		},
		chooseOption() {
			const option = $( this );
			const getForm = option.closest(
				'[data-product_id][data-product_variations]'
			);
			getForm
				.find( '.disable-to-select' )
				.removeClass( 'disable-to-select' );
			const td = option.closest( 'td' );
			const checkTdSelected = td
				.find( '.cfvsw-hidden-select select' )
				.val();
			if ( '' !== checkTdSelected ) {
				td.addClass( 'disable-to-select' );
			}
			const getFormData = getForm.data( 'product_variations' );
			SW.swatchesOptions( getForm, getFormData );
		},
		getSelectedOptions: ( getForm ) => {
			const getSelectedSwatches = getForm.find(
				'.cfvsw-hidden-select select'
			);
			const selected = {};
			getSelectedSwatches.each( function () {
				const select = $( this );
				const getValue = select.val();
				const getAttrName = select.attr( 'data-attribute_name' );
				if ( '' !== getValue && '' !== getAttrName ) {
					selected[ getAttrName ] = getValue;
				}
			} );
			return selected;
		},
		swatchesOptions: ( getForm, getFormData ) => {
			const getTdAvoidCurrent = getForm
				.find( 'td' )
				.not( '.disable-to-select' );
			const getAllSelect = getTdAvoidCurrent.find(
				'.cfvsw-swatches-container[swatches-attr]'
			);
			if ( ! getAllSelect.length ) {
				return;
			}
			const getSelectedOptions = SW.getSelectedOptions( getForm );
			const findToRemoveClass = getTdAvoidCurrent.find(
				`.${ addRemoveDisableClass }`
			);
			if ( findToRemoveClass.length ) {
				findToRemoveClass.removeClass( addRemoveDisableClassCommon );
			}
			getAllSelect.each( function () {
				const select = $( this );
				const getAttrName = select.attr( 'swatches-attr' );
				if ( '' !== getAttrName ) {
					const findOptions = select.find( '.cfvsw-swatches-option' );
					findOptions.each( function () {
						const optValue = $( this );
						const currentTermSlug = optValue.attr( 'data-slug' );

						if ( currentTermSlug && '' !== currentTermSlug ) {
							const hasStock = SW.checkOptionAvail(
								getAttrName,
								currentTermSlug,
								getSelectedOptions,
								getFormData
							);
							if ( ! hasStock ) {
								optValue.addClass(
									addRemoveDisableClassCommon
								);
							}
						}
					} );
				}
			} );
		},
		checkOptionAvail: (
			getAttrName,
			currentTermSlug,
			getSelectedOptions,
			getFormData
		) => {
			let hasThisSwatch;
			for ( let index = 0; index < getFormData.length; index++ ) {
				const productVariations = getFormData[ index ];
				const { attributes, is_in_stock } = productVariations;
				const passedInRaw = SW.checkInPreRawData(
					getSelectedOptions,
					attributes,
					getAttrName,
					currentTermSlug,
					is_in_stock
				);
				if ( passedInRaw ) {
					hasThisSwatch = is_in_stock;
					break;
				}
			}
			return hasThisSwatch;
		},
		checkInPreRawData: (
			getSelectedOptions,
			attributes,
			getAttrName,
			currentTermSlug,
			is_in_stock
		) => {
			let hasThisSwatch = false;
			// If getSelectedOptions values have.
			const copySelected = { ...getSelectedOptions };
			const selectedKeys = Object.keys( getSelectedOptions );
			const getCurrentIndex = selectedKeys.indexOf( getAttrName );
			const currentObj = {};
			currentObj[ getAttrName ] = currentTermSlug;
			if ( getCurrentIndex >= 0 ) {
				selectedKeys.splice( getCurrentIndex, 1 );
				delete copySelected[ getAttrName ];
			}
			hasThisSwatch = SW.checkInRawData(
				attributes,
				copySelected,
				currentObj,
				is_in_stock
			);
			return hasThisSwatch;
		},
		checkInRawData: ( attribute, selected, currentObj, is_in_stock ) => {
			const cloneAttr = { ...attribute };
			const selectedCurrent = { ...selected, ...currentObj };
			let checkAndAvail = true;
			for ( const checkIsAvail in selectedCurrent ) {
				const value = selectedCurrent[ checkIsAvail ];
				const attrValue = attribute[ checkIsAvail ];
				if ( '' === attrValue ) {
					delete cloneAttr[ checkIsAvail ];
					continue;
				}
				if ( value === attrValue ) {
					delete cloneAttr[ checkIsAvail ];
					continue;
				}
				checkAndAvail = false;
			}

			if ( is_in_stock ) {
				return checkAndAvail;
			}
			// Check when out of stock
			for ( const cloneKey in cloneAttr ) {
				const cloneValue = cloneAttr[ cloneKey ];
				if ( '' !== cloneValue ) {
					checkAndAvail = false;
				}
			}
			return checkAndAvail;
		},
		Events: () => {
			$( document ).on(
				'click',
				'.cfvsw-swatches-container .cfvsw-swatches-option[data-slug]',
				SW.chooseOption
			);
		},
	};

	$( document ).on( 'click', '.cfvsw-swatches-option', function () {
		const swatchesOption = $( this );
		if (
			swatchesOption.hasClass( 'cfvsw-swatches-disabled' ) ||
			swatchesOption.hasClass( 'cfvsw-swatches-out-of-stock' )
		) {
			return;
		}
		onClickSwatchesOption( swatchesOption );
	} );

	$( 'body' ).on(
		'click',
		'.cfvsw_ajax_add_to_cart.cfvsw_variation_found',
		function ( e ) {
			e.preventDefault();
			triggerAddToCart( $( this ) );
		}
	);

	function onClickSwatchesOption( swatch ) {
		if ( swatch.hasClass( 'cfvsw-selected-swatch' ) ) {
			swatch.removeClass( 'cfvsw-selected-swatch' );
			resetPrice( swatch );
			resetThumbnail( swatch );
			resetButtonData( swatch );
		} else {
			const parent = swatch.parent();
			parent.find( '.cfvsw-swatches-option' ).each( function () {
				$( this ).removeClass( 'cfvsw-selected-swatch' );
			} );

			swatch.addClass( 'cfvsw-selected-swatch' );
		}

		updateSelectOption( swatch );
		if ( cfvsw_swatches_settings.html_design !== 'inline' ) {
			updateTitle( swatch );
		}
	}

	function updateSelectOption( swatch ) {
		const value = swatch.hasClass( 'cfvsw-selected-swatch' )
			? swatch.data( 'slug' )
			: '';
		const select = swatch
			.closest( '.cfvsw-swatches-container' )
			.prev()
			.find( 'select' );
		select.val( value ).change();
	}

	function updateTitle( swatch ) {
		const label = swatch.closest( 'tr' ).children( '.label' );
		label.find( '.cfvsw-selected-label' ).remove();

		if ( ! swatch.hasClass( 'cfvsw-selected-swatch' ) ) {
			return;
		}

		label
			.children( 'label' )
			.append( '<span class="cfvsw-selected-label"></span>' );
		label
			.children( 'label' )
			.children( '.cfvsw-selected-label' )
			.html( swatch.data( 'title' ) );
	}

	function triggerAddToCart( variant ) {
		if ( variant.is( '.wc-variation-is-unavailable' ) ) {
			return window.alert( cfvsw_swatches_settings.unavailable_text );
		}
		const productId = variant.data( 'product_id' );
		let variationId = variant.attr( 'data-variation_id' );
		variationId = parseInt( variationId );
		if (
			isNaN( productId ) ||
			productId === 0 ||
			isNaN( variationId ) ||
			variationId === 0
		) {
			return true;
		}
		let variation = variant.attr( 'data-selected_variant' );
		variation = JSON.parse( variation );
		const data = {
			action: 'cfvsw_ajax_add_to_cart',
			security: cfvsw_swatches_settings.ajax_add_to_cart_nonce,
			product_id: productId,
			variation_id: variationId,
			variation,
		};
		$( document.body ).trigger( 'adding_to_cart', [ variant, data ] );
		variant.removeClass( 'added' ).addClass( 'loading' );
		// Ajax add to cart request
		$.ajax( {
			type: 'POST',
			url: cfvsw_swatches_settings.ajax_url,
			data,
			dataType: 'json',
			success( response ) {
				if ( ! response ) {
					return;
				}

				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}

				// Trigger event so themes can refresh other areas.
				$( document.body ).trigger( 'added_to_cart', [
					response.fragments,
					response.cart_hash,
					variant,
				] );
				$( document.body ).trigger( 'update_checkout' );

				variant.removeClass( 'loading' ).addClass( 'added' );
			},
			error( errorThrown ) {
				variant.removeClass( 'loading' );
				console.log( errorThrown );
			},
		} );
	}

	$( document ).on( 'change', '.cfvsw-hidden-select select', function () {
		setTimeout( () => {
			updateSwatchesAvailability();
		}, 1 );
	} );

	$( '.reset_variations' ).on( 'click', function () {
		resetSwatches( $( this ) );
	} );

	// Tooltip.
	$( document ).on(
		{
			mouseenter() {
				const addToTooltip = $( this );
				const tooltip = addToTooltip.data( 'tooltip' );
				if (
					'' === tooltip ||
					'undefined' === typeof tooltip ||
					addToTooltip.hasClass( 'cfvsw-label-option' )
				) {
					return;
				}

				if ( addToTooltip.children( '.cfvsw-tooltip' ).length === 0 ) {
					addToTooltip.prepend(
						`<div class="cfvsw-tooltip"><span class="cfvsw-tooltip-label">${ tooltip }</span></div>`
					);
					$( '.cfvsw-tooltip' ).fadeIn( 500 );
					const swatchHeight = addToTooltip
						.children( '.cfvsw-swatch-inner' )
						.innerHeight();
					$( '.cfvsw-tooltip' ).css( {
						bottom: swatchHeight,
					} );
					if (
						cfvsw_swatches_settings.tooltip_image &&
						addToTooltip.hasClass( 'cfvsw-image-option' )
					) {
						$( '.cfvsw-tooltip' ).prepend(
							"<span class='cfvsw-tooltip-preview'></span>"
						);
						const preview = addToTooltip
							.children( '.cfvsw-swatch-inner' )
							.css( 'backgroundImage' );
						$( '.cfvsw-tooltip' ).css( {
							bottom: swatchHeight - 30,
							padding: '2px',
						} );
						$( '.cfvsw-tooltip-preview' ).css( {
							backgroundImage: preview,
							backgroundSize: 'cover',
						} );
					}
				}
			},
			mouseleave() {
				$( '.cfvsw-tooltip' ).remove();
			},
		},
		'.cfvsw-swatches-option'
	);

	$( document ).on( 'ready', function () {
		setTimeout( () => {
			setSwatchesSelection();
		}, 1 );
		$( '.woocommerce-widget-layered-nav-list' ).each( function () {
			if ( $( this ).find( '.cfvsw-swatches-container' ).length ) {
				$( this ).addClass( 'cfvsw-filters' );
			}
		} );
	} );

	$( '.cfvsw-shop-variations' ).on( 'click', function ( e ) {
		e.preventDefault();
	} );

	$( '.cfvsw-shop-variations .cfvsw-more-link' ).on( 'click', function ( e ) {
		window.location = e.target.href;
	} );

	function updateSwatchesAvailability() {
		$( '.cfvsw-hidden-select select' ).each( function () {
			const availableOptions = [];
			$( this )
				.children( 'option' )
				.each( function () {
					if ( '' !== $( this ).val() ) {
						availableOptions.push( $( this ).val() );
					}
				} );
			$( this )
				.parent()
				.next()
				.find( '.cfvsw-swatches-option' )
				.each( function () {
					if (
						-1 ===
						$.inArray(
							$( this ).attr( 'data-slug' ),
							availableOptions
						)
					) {
						$( this ).addClass( addRemoveWithCommonClass );
					} else {
						$( this ).removeClass( addRemoveWithCommonClass );
					}
				} );
		} );
	}

	function setSwatchesSelection() {
		$( '.cfvsw-hidden-select select' ).each( function () {
			const selected = $( this ).val();
			$( this )
				.parent()
				.next()
				.find( `[data-slug='${ selected }']` )
				.trigger( 'click' );
		} );
	}

	function resetSwatches( resetButton ) {
		$( '.cfvsw-swatches-option' ).each( function () {
			$( this ).removeClass( 'cfvsw-selected-swatch' );
		} );
		$( '.cfvsw-selected-label' ).remove();

		if ( cfvsw_swatches_settings.disable_out_of_stock ) {
			const table = resetButton.closest( 'table' );
			const findDisabledAttr = table.find(
				`.${ addRemoveDisableClass }`
			);
			const findDisableSelect = table.find( '.disable-to-select' );
			if ( findDisableSelect.length ) {
				findDisableSelect.removeClass( 'disable-to-select' );
			}
			if ( findDisabledAttr ) {
				findDisabledAttr.removeClass( addRemoveDisableClassCommon );
			}
			setTimeout( () => {
				SW.firstTime();
			}, 20 );
		}
	}

	function addVariationFunctionality() {
		$( '.cfvsw_variations_form:not(.variation-function-added)' ).each(
			function () {
				const thisForm = $( this );
				thisForm.addClass( 'variation-function-added' );
				thisForm.wc_variation_form();
				thisForm.on( 'found_variation', function ( e, variation ) {
					updateThumbnail( thisForm, variation.image );
					if ( thisForm.attr( 'data-cfvsw-catalog' ) ) {
						return;
					}
					updatePrice( thisForm, variation );
					updatebuttonData( thisForm, variation );
				} );
			}
		);
	}

	$( window ).load( function () {
		addVariationFunctionality();
	} );

	function updateThumbnail( swatch, imageData ) {
		const listItem = swatch.closest( 'li' );
		const thumbnail = listItem.find( 'img:first' );
		if ( 0 === listItem.find( '.cfvsw-original-thumbnail' ).length ) {
			const originalThumbnail = thumbnail.clone();
			thumbnail.after( '<span class="cfvsw-original-thumbnail"></span>' );
			listItem
				.find( '.cfvsw-original-thumbnail' )
				.html( originalThumbnail );
		}
		thumbnail.attr( 'src', imageData.thumb_src );
		thumbnail.attr( 'srcset', '' );
	}

	function resetThumbnail( swatch ) {
		const listItem = swatch.closest( 'li' );
		if ( listItem.find( '.cfvsw-original-thumbnail' ).length ) {
			const thumbnail = listItem.find( 'img:first' );
			thumbnail.replaceWith(
				listItem.find( '.cfvsw-original-thumbnail' ).html()
			);
			listItem.find( '.cfvsw-original-thumbnail' ).remove();
		}
	}

	function updatePrice( swatch, variation ) {
		if ( 0 === variation.price_html.length ) {
			return;
		}
		if ( swatch.parents( 'li' ).find( '.cfvsw-original-price' ).length ) {
			const price = swatch.parents( 'li' ).find( '.price' );
			price.replaceWith( variation.price_html );
		} else {
			const price = swatch.parents( 'li' ).find( '.price' );
			price.after( variation.price_html );
			price.removeClass( 'price' ).addClass( 'cfvsw-original-price' );
		}
	}

	function resetPrice( swatch ) {
		if ( swatch.parents( 'li' ).find( '.cfvsw-original-price' ).length ) {
			swatch.parents( 'li' ).find( '.price' ).remove();
			swatch
				.parents( 'li' )
				.find( '.cfvsw-original-price' )
				.removeClass( 'cfvsw-original-price' )
				.addClass( 'price' );
		}
	}

	function updatebuttonData( variant, variation ) {
		const select = variant.find( '.variations select' );
		const data = {};
		const button = variant
			.parents( 'li' )
			.find( '.cfvsw_ajax_add_to_cart' );

		select.each( function () {
			const attributeName =
				$( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			const value = $( this ).val() || '';
			data[ attributeName ] = value;
		} );

		button.html( button.data( 'add_to_cart_text' ) );
		button.addClass( 'cfvsw_variation_found' );
		button.attr( 'data-variation_id', variation.variation_id );
		button.attr( 'data-selected_variant', JSON.stringify( data ) );
	}

	function resetButtonData( variant ) {
		const button = variant
			.parents( 'li' )
			.find( '.cfvsw_ajax_add_to_cart' );
		button.html( button.data( 'select_options_text' ) );
		button.removeClass( 'cfvsw_variation_found' );
		button.attr( 'data-variation_id', '' );
		button.attr( 'data-selected_variant', '' );
	}

	SW.init();
	document.addEventListener( 'astraInfinitePaginationLoaded', function () {
		SW.firstTime();
		addVariationFunctionality();
	} );

	// Add custom trigger to load swatches.

	// document.dispatchEvent( new CustomEvent("cfvswVariationLoad", { detail: {} }) ); To load variation trigger we need to trigger this.
	document.addEventListener( 'cfvswVariationLoad', function () {
		SW.firstTime();
		addVariationFunctionality();
	} );
} )( jQuery );
