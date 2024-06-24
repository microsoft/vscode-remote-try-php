/*global woocommerce_admin_meta_boxes */
jQuery( function ( $ ) {
	let isPageUnloading = false;

	$( window ).on( 'beforeunload', function () {
		isPageUnloading = true;
	} );

	// Scroll to first checked category
	// https://github.com/scribu/wp-category-checklist-tree/blob/d1c3c1f449e1144542efa17dde84a9f52ade1739/category-checklist-tree.php
	$( function () {
		$( '[id$="-all"] > ul.categorychecklist' ).each( function () {
			var $list = $( this );
			var $firstChecked = $list.find( ':checked' ).first();

			if ( ! $firstChecked.length ) {
				return;
			}

			var pos_first = $list.find( 'input' ).position().top;
			var pos_checked = $firstChecked.position().top;

			$list
				.closest( '.tabs-panel' )
				.scrollTop( pos_checked - pos_first + 5 );
		} );
	} );

	// Prevent enter submitting post form.
	$( '#upsell_product_data' ).on( 'keypress', function ( e ) {
		if ( e.keyCode === 13 ) {
			return false;
		}
	} );

	// Type box.
	if ( $( 'body' ).hasClass( 'wc-wp-version-gte-55' ) ) {
		$( '.type_box' ).appendTo( '#woocommerce-product-data .hndle' );
	} else {
		$( '.type_box' ).appendTo( '#woocommerce-product-data .hndle span' );
	}

	$( function () {
		var woocommerce_product_data = $( '#woocommerce-product-data' );

		// Prevent inputs in meta box headings opening/closing contents.
		woocommerce_product_data.find( '.hndle' ).off( 'click.postboxes' );

		woocommerce_product_data.on( 'click', '.hndle', function ( event ) {
			// If the user clicks on some form input inside the h3 the box should not be toggled.
			if (
				$( event.target ).filter( 'input, option, label, select' )
					.length
			) {
				return;
			}

			if ( woocommerce_product_data.hasClass( 'closed' ) ) {
				woocommerce_product_data.removeClass( 'closed' );
			} else {
				woocommerce_product_data.addClass( 'closed' );
			}
		} );
	} );

	// Catalog Visibility.
	$( '#catalog-visibility' )
		.find( '.edit-catalog-visibility' )
		.on( 'click', function () {
			if ( $( '#catalog-visibility-select' ).is( ':hidden' ) ) {
				$( '#catalog-visibility-select' ).slideDown( 'fast' );
				$( this ).hide();
			}
			return false;
		} );
	$( '#catalog-visibility' )
		.find( '.save-post-visibility' )
		.on( 'click', function () {
			$( '#catalog-visibility-select' ).slideUp( 'fast' );
			$( '#catalog-visibility' )
				.find( '.edit-catalog-visibility' )
				.show();

			var label = $( 'input[name=_visibility]:checked' ).attr(
				'data-label'
			);

			if ( $( 'input[name=_featured]' ).is( ':checked' ) ) {
				label =
					label + ', ' + woocommerce_admin_meta_boxes.featured_label;
				$( 'input[name=_featured]' ).attr( 'checked', 'checked' );
			}

			$( '#catalog-visibility-display' ).text( label );
			return false;
		} );
	$( '#catalog-visibility' )
		.find( '.cancel-post-visibility' )
		.on( 'click', function () {
			$( '#catalog-visibility-select' ).slideUp( 'fast' );
			$( '#catalog-visibility' )
				.find( '.edit-catalog-visibility' )
				.show();

			var current_visibility = $( '#current_visibility' ).val();
			var current_featured = $( '#current_featured' ).val();

			$( 'input[name=_visibility]' ).prop( 'checked', false );
			$(
				'input[name=_visibility][value=' + current_visibility + ']'
			).attr( 'checked', 'checked' );

			var label = $( 'input[name=_visibility]:checked' ).attr(
				'data-label'
			);

			if ( 'yes' === current_featured ) {
				label =
					label + ', ' + woocommerce_admin_meta_boxes.featured_label;
				$( 'input[name=_featured]' ).attr( 'checked', 'checked' );
			} else {
				$( 'input[name=_featured]' ).prop( 'checked', false );
			}

			$( '#catalog-visibility-display' ).text( label );
			return false;
		} );

	// Product type specific options.
	$( 'select#product-type' )
		.on( 'change', function () {
			// Get value.
			var select_val = $( this ).val();

			if ( 'variable' === select_val ) {
				$( 'input#_manage_stock' ).trigger( 'change' );
				$( 'input#_downloadable' ).prop( 'checked', false );
				$( 'input#_virtual' ).prop( 'checked', false );
			} else if ( 'grouped' === select_val ) {
				$( 'input#_downloadable' ).prop( 'checked', false );
				$( 'input#_virtual' ).prop( 'checked', false );
			} else if ( 'external' === select_val ) {
				$( 'input#_downloadable' ).prop( 'checked', false );
				$( 'input#_virtual' ).prop( 'checked', false );
			}

			show_and_hide_panels();
			change_product_type_tip( get_product_tip_content( select_val ) );

			$( 'ul.wc-tabs li:visible' ).eq( 0 ).find( 'a' ).trigger( 'click' );

			$( document.body ).trigger(
				'woocommerce-product-type-change',
				select_val,
				$( this )
			);
		} )
		.trigger( 'change' );

	$( 'input#_downloadable' ).on( 'change', function () {
		show_and_hide_panels();
	} );

	$( 'input#_virtual' ).on( 'change', function () {
		show_and_hide_panels();

		// If user enables virtual while on shipping tab, switch to general tab.
		if (
			$( this ).is( ':checked' ) &&
			$( '.shipping_options.shipping_tab' ).hasClass( 'active' )
		) {
			$( '.general_options.general_tab > a' ).trigger( 'click' );
		}
	} );

	function change_product_type_tip( content ) {
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.woocommerce-product-type-tip' )
		.attr( 'tabindex', '0' )
		.attr( 'aria-label', $( '<div />' ).html( content ).text() ) // Remove HTML tags.
		.tipTip( {
			attribute: 'data-tip',
			content: content,
			fadeIn: 50,
			fadeOut: 50,
			delay: 200,
			keepAlive: true,
		} );
	}

	function get_product_tip_content( product_type ) {
		switch ( product_type ) {
			case 'simple':
				return woocommerce_admin_meta_boxes.i18n_product_simple_tip;
			case 'grouped':
				return woocommerce_admin_meta_boxes.i18n_product_grouped_tip;
			case 'external':
				return woocommerce_admin_meta_boxes.i18n_product_external_tip;
			case 'variable':
				return woocommerce_admin_meta_boxes.i18n_product_variable_tip;
			default:
				return woocommerce_admin_meta_boxes.i18n_product_other_tip;
		}
	}

	function show_and_hide_controls( context ) {
		var product_type = $( 'select#product-type' ).val();
		var is_virtual = $( 'input#_virtual:checked' ).length;
		var is_downloadable = $( 'input#_downloadable:checked' ).length;

		// Hide/Show all with rules.
		var hide_classes = '.hide_if_downloadable, .hide_if_virtual';
		var show_classes = '.show_if_downloadable, .show_if_virtual';

		$.each(
			woocommerce_admin_meta_boxes.product_types,
			function ( index, value ) {
				hide_classes = hide_classes + ', .hide_if_' + value;
				show_classes = show_classes + ', .show_if_' + value;
			}
		);

		$( hide_classes, context ).show();
		$( show_classes, context ).hide();

		// Shows rules.
		if ( is_downloadable ) {
			$( '.show_if_downloadable', context ).show();
		}
		if ( is_virtual ) {
			$( '.show_if_virtual', context ).show();
		}

		$( '.show_if_' + product_type, context ).show();

		// Hide rules.
		if ( is_downloadable ) {
			$( '.hide_if_downloadable', context ).hide();
		}
		if ( is_virtual ) {
			$( '.hide_if_virtual', context ).hide();
		}

		$( '.hide_if_' + product_type, context ).hide();
	}

	function show_and_hide_panels() {
		show_and_hide_controls();

		$( 'input#_manage_stock' ).trigger( 'change' );

		// Hide empty panels/tabs after display.
		$( '.woocommerce_options_panel' ).each( function () {
			var $children = $( this ).children( '.options_group' );

			if ( 0 === $children.length ) {
				return;
			}

			var $invisible = $children.filter( function () {
				return 'none' === $( this ).css( 'display' );
			} );

			// Hide panel.
			if ( $invisible.length === $children.length ) {
				var $id = $( this ).prop( 'id' );
				$( '.product_data_tabs' )
					.find( 'li a[href="#' + $id + '"]' )
					.parent()
					.hide();
			}
		} );
	}

	// Sale price schedule.
	$( '.sale_price_dates_fields' ).each( function () {
		var $these_sale_dates = $( this );
		var sale_schedule_set = false;
		var $wrap = $these_sale_dates.closest( 'div, table' );

		$these_sale_dates.find( 'input' ).each( function () {
			if ( '' !== $( this ).val() ) {
				sale_schedule_set = true;
			}
		} );

		if ( sale_schedule_set ) {
			$wrap.find( '.sale_schedule' ).hide();
			$wrap.find( '.sale_price_dates_fields' ).show();
		} else {
			$wrap.find( '.sale_schedule' ).show();
			$wrap.find( '.sale_price_dates_fields' ).hide();
		}
	} );

	$( '#woocommerce-product-data' ).on(
		'click',
		'.sale_schedule',
		function () {
			var $wrap = $( this ).closest( 'div, table' );

			$( this ).hide();
			$wrap.find( '.cancel_sale_schedule' ).show();
			$wrap.find( '.sale_price_dates_fields' ).show();

			return false;
		}
	);
	$( '#woocommerce-product-data' ).on(
		'click',
		'.cancel_sale_schedule',
		function () {
			var $wrap = $( this ).closest( 'div, table' );

			$( this ).hide();
			$wrap.find( '.sale_schedule' ).show();
			$wrap.find( '.sale_price_dates_fields' ).hide();
			$wrap.find( '.sale_price_dates_fields' ).find( 'input' ).val( '' );

			return false;
		}
	);

	// File inputs.
	$( '#woocommerce-product-data' ).on(
		'click',
		'.downloadable_files a.insert',
		function () {
			$( this )
				.closest( '.downloadable_files' )
				.find( 'tbody' )
				.append( $( this ).data( 'row' ) );
			return false;
		}
	);
	$( '#woocommerce-product-data' ).on(
		'click',
		'.downloadable_files a.delete',
		function () {
			$( this ).closest( 'tr' ).remove();
			return false;
		}
	);

	// Stock options.
	function show_or_hide_stock_management_fields(
		isStockManagementEnabled,
		productType
	) {
		const $stockManagementFields = $( '.stock_fields' );
		const $stockStatusField = $( '.stock_status_field' );

		$stockManagementFields.toggle( isStockManagementEnabled );
		$stockStatusField.toggle(
			! isStockManagementEnabled &&
				// do not show stock status field if it should be hidden for the product type
				! $stockStatusField.is( '.hide_if_' + productType )
		);
	}

	$( 'input#_manage_stock' )
		.on( 'change', function () {
			const isStockManagementEnabled = $( this ).is( ':checked' );
			const productType = $( 'select#product-type' ).val();

			show_or_hide_stock_management_fields(
				isStockManagementEnabled,
				productType
			);

			$( 'input.variable_manage_stock' ).trigger( 'change' );
		} )
		.trigger( 'change' );

	// Date picker fields.
	function date_picker_select( datepicker ) {
		var option = $( datepicker ).next().is( '.hasDatepicker' )
				? 'minDate'
				: 'maxDate',
			otherDateField =
				'minDate' === option
					? $( datepicker ).next()
					: $( datepicker ).prev(),
			date = $( datepicker ).datepicker( 'getDate' );

		$( otherDateField ).datepicker( 'option', option, date );
		$( datepicker ).trigger( 'change' );
	}

	$( '.sale_price_dates_fields' ).each( function () {
		$( this )
			.find( 'input' )
			.datepicker( {
				defaultDate: '',
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				onSelect: function () {
					date_picker_select( $( this ) );
				},
			} );
		$( this )
			.find( 'input' )
			.each( function () {
				date_picker_select( $( this ) );
			} );
	} );

	// Set up attributes, if current page has the attributes list.
	const $product_attributes = $( '.product_attributes' );
	if ( $product_attributes.length === 1 ) {
		// When the attributes tab is shown, add an empty attribute to be filled out by the user.
		$( '#product_attributes' ).on( 'woocommerce_tab_shown', function() {
			remove_blank_custom_attribute_if_no_other_attributes();

			const woocommerce_attribute_items = $product_attributes.find( '.woocommerce_attribute' ).get();

			// If the product has no attributes, add an empty attribute to be filled out by the user.
			if ( woocommerce_attribute_items.length === 0  ) {
				add_custom_attribute_to_list();
			}
		} );

		const woocommerce_attribute_items = $product_attributes.find( '.woocommerce_attribute' ).get();

		// Sort the attributes by their position.
		woocommerce_attribute_items.sort( function ( a, b ) {
			var compA = parseInt( $( a ).attr( 'rel' ), 10 );
			var compB = parseInt( $( b ).attr( 'rel' ), 10 );
			return compA < compB ? -1 : compA > compB ? 1 : 0;
		} );

		$( woocommerce_attribute_items ).each( function ( index, el ) {
			$product_attributes.append( el );
		} );
	}

	function update_attribute_row_indexes() {
		$( '.product_attributes .woocommerce_attribute' ).each( function (
			index,
			el
		) {
			$( '.attribute_position', el ).val(
				parseInt(
					$( el ).index(
						'.product_attributes .woocommerce_attribute'
					),
					10
				)
			);
		} );
	}

	var selectedAttributes = [];
	$( '.product_attributes .woocommerce_attribute' ).each( function (
		index,
		el
	) {
		if (
			$( el ).css( 'display' ) !== 'none' &&
			$( el ).is( '.taxonomy' )
		) {
			selectedAttributes.push( $( el ).data( 'taxonomy' ) );
			$( 'select.attribute_taxonomy' )
				.find( 'option[value="' + $( el ).data( 'taxonomy' ) + '"]' )
				.attr( 'disabled', 'disabled' );
		}

		if ( 'undefined' === $(el).attr( 'data-taxonomy' ) || false === $(el).attr( 'data-taxonomy' ) || '' === $(el).attr( 'data-taxonomy' ) ) {
			add_placeholder_to_attribute_values_field( $(el) );

			$( '.woocommerce_attribute input.woocommerce_attribute_used_for_variations' ).on( 'change', function() {
				add_placeholder_to_attribute_values_field( $(el) );
			} );
		}
	} );
	$( 'select.wc-attribute-search' ).data(
		'disabled-items',
		selectedAttributes
	);

	function get_new_attribute_list_item_html(
		indexInList,
		globalAttributeId
	) {
		return new Promise( function ( resolve, reject ) {
			$.post( {
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: {
					action: 'woocommerce_add_attribute',
					product_type: $( '#product-type' ).val(),
					taxonomy: globalAttributeId ? globalAttributeId : '',
					i: indexInList,
					security: woocommerce_admin_meta_boxes.add_attribute_nonce,
				},
				success: function ( newAttributeListItemHtml ) {
					resolve( newAttributeListItemHtml );
				},
				error: function ( jqXHR, textStatus, errorThrown ) {
					reject( { jqXHR, textStatus, errorThrown } );
				},
			} );
		} );
	}

	function block_attributes_tab_container() {
		const $attributesTabContainer = $( '#product_attributes' );

		$attributesTabContainer.block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6,
			},
		} );
	}

	function unblock_attributes_tab_container() {
		const $attributesTabContainer = $( '#product_attributes' );
		$attributesTabContainer.unblock();
	}

	function toggle_expansion_of_attribute_list_item( $attributeListItem ) {
		$attributeListItem.find( 'h3' ).trigger( 'click' );
	}

	function add_placeholder_to_attribute_values_field( $attributeListItem ) {

		var $used_for_variations_checkbox = $attributeListItem.find( 'input.woocommerce_attribute_used_for_variations' );

		if ( $used_for_variations_checkbox.length && $used_for_variations_checkbox.is( ':checked' ) ) {
			$attributeListItem.find( 'textarea' ).attr( 'placeholder', woocommerce_admin_meta_boxes.i18n_attributes_used_for_variations_placeholder );
		} else {
			$attributeListItem.find( 'textarea' ).attr( 'placeholder', woocommerce_admin_meta_boxes.i18n_attributes_default_placeholder );
		}
	}

	function init_select_controls() {
		$( document.body ).trigger( 'wc-enhanced-select-init' );
	}

	async function add_attribute_to_list( globalAttributeId ) {
		try {
			block_attributes_tab_container();

			const numberOfAttributesInList = $(
				'.product_attributes .woocommerce_attribute'
			).length;
			const newAttributeListItemHtml =
				await get_new_attribute_list_item_html(
					numberOfAttributesInList,
					globalAttributeId
				);

			const $attributesListContainer = $(
				'#product_attributes .product_attributes'
			);

			const $attributeListItem = $( newAttributeListItemHtml ).appendTo(
				$attributesListContainer
			);

			show_and_hide_controls( $attributeListItem );

			init_select_controls(); // make sure any new select controls in the new list item are initialized

			update_attribute_row_indexes();

			toggle_expansion_of_attribute_list_item( $attributeListItem );

			// Conditionally change the placeholder of product-level Attributes depending on the value of the "Use for variations" checkbox.
			if ( 'undefined' === typeof globalAttributeId ) {
				add_placeholder_to_attribute_values_field( $attributeListItem );

				$( '.woocommerce_attribute input.woocommerce_attribute_used_for_variations' ).on( 'change', function() {
					add_placeholder_to_attribute_values_field( $(this).closest( '.woocommerce_attribute' ) );
				} );
			}

			$( document.body ).trigger( 'woocommerce_added_attribute' );

			jQuery.maybe_disable_save_button();
		} catch ( error ) {
			if ( isPageUnloading ) {
				// If the page is unloading, the outstanding ajax fetch may fail in Firefox (and possible other browsers, too).
				// We don't want to show an error message in this case, because it was caused by the user leaving the page.
				return;
			}

			alert( woocommerce_admin_meta_boxes.i18n_add_attribute_error_notice );
			throw error;
		} finally {
			unblock_attributes_tab_container();
		}
	}

	function add_global_attribute_to_list( globalAttributeId ) {
		add_attribute_to_list( globalAttributeId );
	}

	function add_custom_attribute_to_list() {
		add_attribute_to_list();
	}

	function add_if_not_exists( arr, item ) {
		return arr.includes( item ) ? attr : [ ...arr, item ];
	}

	function disable_in_attribute_search( selectedAttributes ) {
		$( 'select.wc-attribute-search' ).data(
			'disabled-items',
			selectedAttributes
		);
	}

	function remove_blank_custom_attribute_if_no_other_attributes() {
		const $attributes = $( '.product_attributes .woocommerce_attribute' );

		if ( $attributes.length === 1 ) {
			const $attribute = $attributes.first();

			const $attributeName = $attribute.find(
				'input[name="attribute_names[0]"]'
			);
			const $attributeValue = $attribute.find(
				'input[name="attribute_values[0]"]'
			);

			if ( ! $attributeName.val() && ! $attributeValue.val() ) {
				$attribute.remove();
			}
		}
	}

	// Handle the Attributes onboarding dismissible notice.
	// If users dismiss the notice, never show it again.
	if ( localStorage.getItem('attributes-notice-dismissed' ) ) {
		$( '#product_attributes .notice' ).hide();
	}

	$( '#product_attributes .notice.woocommerce-message button' ).on( 'click', function( e ) {
		$( '#product_attributes .notice' ).hide();
		localStorage.setItem( 'attributes-notice-dismissed', 'true');
	} );

	$( 'select.wc-attribute-search' ).on( 'select2:select', function ( e ) {
		const attributeId = e && e.params && e.params.data && e.params.data.id;

		if ( attributeId ) {
			remove_blank_custom_attribute_if_no_other_attributes();

			add_global_attribute_to_list( attributeId );

			selectedAttributes = add_if_not_exists(
				selectedAttributes,
				attributeId
			);
			disable_in_attribute_search( selectedAttributes );
		}

		$( this ).val( null );
		$( this ).trigger( 'change' );

		return false;
	} );

	// Add rows.

	$( 'button.add_custom_attribute' ).on( 'click', function () {
		add_custom_attribute_to_list();

		return false;
	} );

	$( '.product_attributes' ).on( 'blur', 'input.attribute_name', function () {
		var $inputElement = $( this );
		var text = $inputElement.val();
		var $attribute = $inputElement
			.closest( '.woocommerce_attribute' )
			.find( 'strong.attribute_name' );
		if ( text === '' ) {
			$attribute
				.addClass( 'placeholder' )
				.text(
					woocommerce_admin_meta_boxes.i18n_attribute_name_placeholder
				);
		} else {
			$attribute.removeClass( 'placeholder' ).text( text );
		}
	} );

	$( '.product_attributes' ).on(
		'click',
		'button.select_all_attributes',
		function () {
			$( '.product_attributes' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );

			var $wrapper = $( this ).closest( '.woocommerce_attribute' );
			var attribute = $wrapper.data( 'taxonomy' );

			var data = {
				action: 'woocommerce_json_search_taxonomy_terms',
				taxonomy: attribute,
				security: wc_enhanced_select_params.search_taxonomy_terms_nonce,
			};

			$.get(
				woocommerce_admin_meta_boxes.ajax_url,
				data,
				function ( response ) {
					if ( response.errors ) {
						// Error.
						window.alert( response.errors );
					} else if ( response && response.length > 0 ) {
						// Success.
						response.forEach( function ( term ) {
							const currentItem = $wrapper.find(
								'select.attribute_values option[value="' +
									term.term_id +
									'"]'
							);
							if ( currentItem && currentItem.length > 0 ) {
								currentItem.prop( 'selected', 'selected' );
							} else {
								$wrapper
									.find( 'select.attribute_values' )
									.append(
										'<option value="' +
											term.term_id +
											'" selected="selected">' +
											term.name +
											'</option>'
									);
							}
						} );
						$wrapper
							.find( 'select.attribute_values' )
							.trigger( 'change' );
					}

					$( '.product_attributes' ).unblock();
				}
			);
			return false;
		}
	);

	$( '.product_attributes' ).on(
		'click',
		'button.select_no_attributes',
		function () {
			$( this )
				.closest( 'td' )
				.find( 'select option' )
				.prop( 'selected', false );
			$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
			return false;
		}
	);

	$( '#product_attributes' ).on(
		'click',
		'.product_attributes .remove_row',
		function () {
			var $parent = $( this ).parent().parent();
			var isUsedForVariations = $parent
				.find( 'input[name^="attribute_variation"]' )
				.is( ':visible:checked' );

			if (
				! isUsedForVariations ||
				window.confirm(
					woocommerce_admin_meta_boxes.i18n_remove_used_attribute_confirmation_message
				)
			) {
				if ( $parent.is( '.taxonomy' ) ) {
					$parent.find( 'select, input[type=text]' ).val( '' );
					$parent.hide();
					$( 'select.attribute_taxonomy' )
						.find(
							'option[value="' + $parent.data( 'taxonomy' ) + '"]'
						)
						.prop( 'disabled', false );
					selectedAttributes = selectedAttributes.filter(
						( attr ) => attr !== $parent.data( 'taxonomy' )
					);
					$( 'select.wc-attribute-search' ).data(
						'disabled-items',
						selectedAttributes
					);
				} else {
					$parent.find( 'select, input[type=text]' ).val( '' );
					$parent.hide();
					update_attribute_row_indexes();
				}

				$parent.remove();

				window.wcTracks.recordEvent( 'product_attributes_buttons', {
					action: 'remove_attribute',
				} );

				jQuery.maybe_disable_save_button();
			}
			return false;
		}
	);

	// Attribute ordering.
	$( '.product_attributes' ).sortable( {
		items: '.woocommerce_attribute',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function ( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function ( event, ui ) {
			ui.item.removeAttr( 'style' );
			update_attribute_row_indexes();
		},
	} );

	// Add a new attribute (via ajax).
	$( '.product_attributes' ).on(
		'click',
		'button.add_new_attribute',
		function ( event ) {
			// prevent form submission but allow event propagation
			event.preventDefault();

			$( '.product_attributes' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );

			var $wrapper = $( this ).closest( '.woocommerce_attribute' );
			var attribute = $wrapper.data( 'taxonomy' );
			var new_attribute_name = window.prompt(
				woocommerce_admin_meta_boxes.new_attribute_prompt
			);

			if ( new_attribute_name ) {
				var data = {
					action: 'woocommerce_add_new_attribute',
					taxonomy: attribute,
					term: new_attribute_name,
					security: woocommerce_admin_meta_boxes.add_attribute_nonce,
				};

				$.post(
					woocommerce_admin_meta_boxes.ajax_url,
					data,
					function ( response ) {
						if ( response.error ) {
							// Error.
							window.alert( response.error );
						} else if ( response.slug ) {
							// Success.
							$wrapper
								.find( 'select.attribute_values' )
								.append(
									'<option value="' +
										response.term_id +
										'" selected="selected">' +
										response.name +
										'</option>'
								);
							$wrapper
								.find( 'select.attribute_values' )
								.trigger( 'change' );
						}

						$( '.product_attributes' ).unblock();
					}
				);
			} else {
				$( '.product_attributes' ).unblock();
			}
		}
	);

	// Save attributes and update variations.
	$( '.save_attributes' ).on( 'click', function ( event ) {
		if ( $( this ).hasClass( 'disabled' ) ) {
			event.preventDefault();
			return;
		}
		$( '.product_attributes' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6,
			},
		} );

		var original_data = $( '.product_attributes' ).find(
			'input, select, textarea'
		);
		var data = {
			post_id: woocommerce_admin_meta_boxes.post_id,
			product_type: $( '#product-type' ).val(),
			data: original_data.serialize(),
			action: 'woocommerce_save_attributes',
			security: woocommerce_admin_meta_boxes.save_attributes_nonce,
		};

		$.post(
			woocommerce_admin_meta_boxes.ajax_url,
			data,
			function ( response ) {
				if ( response.error ) {
					// Error.
					window.alert( response.error );
				} else if ( response.data ) {
					// Success.
					$( '.product_attributes' ).html( response.data.html );
					$( '.product_attributes' ).unblock();

					// Hide the 'Used for variations' checkbox if not viewing a variable product
					show_and_hide_panels();

					// Make sure the dropdown is not disabled for empty value attributes.
					$( 'select.attribute_taxonomy' )
						.find( 'option' )
						.prop( 'disabled', false );

					var newSelectedAttributes = [];
					$( '.product_attributes .woocommerce_attribute' ).each(
						function ( index, el ) {
							if (
								$( el ).css( 'display' ) !== 'none' &&
								$( el ).is( '.taxonomy' )
							) {
								newSelectedAttributes.push(
									$( el ).data( 'taxonomy' )
								);
								$( 'select.attribute_taxonomy' )
									.find(
										'option[value="' +
											$( el ).data( 'taxonomy' ) +
											'"]'
									)
									.prop( 'disabled', true );
							}
						}
					);
					selectedAttributes = newSelectedAttributes;
					$( 'select.wc-attribute-search' ).data(
						'disabled-items',
						newSelectedAttributes
					);

					// Reload variations panel.
					var this_page = window.location.toString();
					this_page = this_page.replace(
						'post-new.php?',
						'post.php?post=' +
							woocommerce_admin_meta_boxes.post_id +
							'&action=edit&'
					);

					$( '#variable_product_options' ).load(
						this_page + ' #variable_product_options_inner',
						function () {
							$( '#variable_product_options' ).trigger(
								'reload'
							);
						}
					);

					$( document.body ).trigger(
						'woocommerce_attributes_saved'
					);
				}
			}
		);
	} );

	// Go to attributes tab when clicking on link in variations message
	$( document.body ).on(
		'click',
		'#variable_product_options .add-attributes-message a[href="#product_attributes"]',
		function () {
			$(
				'#woocommerce-product-data .attribute_tab a[href="#product_attributes"]'
			).trigger( 'click' );
			return false;
		}
	);

	// Uploading files.
	var downloadable_file_frame;
	var file_path_field;

	$( document.body ).on( 'click', '.upload_file_button', function ( event ) {
		var $el = $( this );

		file_path_field = $el.closest( 'tr' ).find( 'td.file_url input' );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( downloadable_file_frame ) {
			downloadable_file_frame.open();
			return;
		}

		var downloadable_file_states = [
			// Main states.
			new wp.media.controller.Library( {
				library: wp.media.query(),
				multiple: true,
				title: $el.data( 'choose' ),
				priority: 20,
				filterable: 'uploaded',
			} ),
		];

		// Create the media frame.
		downloadable_file_frame = wp.media.frames.downloadable_file = wp.media(
			{
				// Set the title of the modal.
				title: $el.data( 'choose' ),
				library: {
					type: '',
				},
				button: {
					text: $el.data( 'update' ),
				},
				multiple: true,
				states: downloadable_file_states,
			}
		);

		// When an image is selected, run a callback.
		downloadable_file_frame.on( 'select', function () {
			var file_path = '';
			var selection = downloadable_file_frame.state().get( 'selection' );

			selection.map( function ( attachment ) {
				attachment = attachment.toJSON();
				if ( attachment.url ) {
					file_path = attachment.url;
				}
			} );

			file_path_field.val( file_path ).trigger( 'change' );
		} );

		// Set post to 0 and set our custom type.
		downloadable_file_frame.on( 'ready', function () {
			downloadable_file_frame.uploader.options.uploader.params = {
				type: 'downloadable_product',
			};
		} );

		// Finally, open the modal.
		downloadable_file_frame.open();
	} );

	// Download ordering.
	$( '.downloadable_files tbody' ).sortable( {
		items: 'tr',
		cursor: 'move',
		axis: 'y',
		handle: 'td.sort',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
	} );

	// Product gallery file uploads.
	var product_gallery_frame;
	var $image_gallery_ids = $( '#product_image_gallery' );
	var $product_images = $( '#product_images_container' ).find(
		'ul.product_images'
	);

	$( '.add_product_images' ).on( 'click', 'a', function ( event ) {
		var $el = $( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( product_gallery_frame ) {
			product_gallery_frame.open();
			return;
		}

		// Create the media frame.
		product_gallery_frame = wp.media.frames.product_gallery = wp.media( {
			// Set the title of the modal.
			title: $el.data( 'choose' ),
			button: {
				text: $el.data( 'update' ),
			},
			states: [
				new wp.media.controller.Library( {
					title: $el.data( 'choose' ),
					filterable: 'all',
					multiple: true,
				} ),
			],
		} );

		// When an image is selected, run a callback.
		product_gallery_frame.on( 'select', function () {
			var selection = product_gallery_frame.state().get( 'selection' );
			var attachment_ids = $image_gallery_ids.val();

			selection.map( function ( attachment ) {
				attachment = attachment.toJSON();

				if ( attachment.id ) {
					attachment_ids = attachment_ids
						? attachment_ids + ',' + attachment.id
						: attachment.id;
					var attachment_image =
						attachment.sizes && attachment.sizes.thumbnail
							? attachment.sizes.thumbnail.url
							: attachment.url;

					$product_images.append(
						'<li class="image" data-attachment_id="' +
							attachment.id +
							'"><img src="' +
							attachment_image +
							'" /><ul class="actions"><li><a href="#" class="delete" title="' +
							$el.data( 'delete' ) +
							'">' +
							$el.data( 'text' ) +
							'</a></li></ul></li>'
					);
				}
			} );

			$image_gallery_ids.val( attachment_ids );
		} );

		// Finally, open the modal.
		product_gallery_frame.open();
	} );

	// Image ordering.
	$product_images.sortable( {
		items: 'li.image',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function ( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function ( event, ui ) {
			ui.item.removeAttr( 'style' );
		},
		update: function () {
			var attachment_ids = '';

			$( '#product_images_container' )
				.find( 'ul li.image' )
				.css( 'cursor', 'default' )
				.each( function () {
					var attachment_id = $( this ).attr( 'data-attachment_id' );
					attachment_ids = attachment_ids + attachment_id + ',';
				} );

			$image_gallery_ids.val( attachment_ids );
		},
	} );

	// Remove images.
	$( '#product_images_container' ).on( 'click', 'a.delete', function () {
		$( this ).closest( 'li.image' ).remove();

		var attachment_ids = '';

		$( '#product_images_container' )
			.find( 'ul li.image' )
			.css( 'cursor', 'default' )
			.each( function () {
				var attachment_id = $( this ).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			} );

		$image_gallery_ids.val( attachment_ids );

		// Remove any lingering tooltips.
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );

		return false;
	} );

	// Add a descriptive tooltip to the product description editor
	$( '#wp-content-media-buttons' )
		.append( '<span class="woocommerce-help-tip" tabindex="0"></span>' )
		.find( '.woocommerce-help-tip' )
		.attr( 'tabindex', '0' )
		.attr( 'for', 'content' )
		.attr(
			'aria-label',
			woocommerce_admin_meta_boxes.i18n_product_description_tip
		)
		.tipTip( {
			attribute: 'data-tip',
			content: woocommerce_admin_meta_boxes.i18n_product_description_tip,
			fadeIn: 50,
			fadeOut: 50,
			delay: 200,
			keepAlive: true,
		} );

	// Add a descriptive tooltip to the product short description meta box title
	$( '#postexcerpt > .postbox-header > .hndle' )
		.append( '<span class="woocommerce-help-tip"></span>' )
		.find( '.woocommerce-help-tip' )
		.attr( 'tabindex', '0' )
		.attr(
			'aria-label',
			woocommerce_admin_meta_boxes.i18n_product_short_description_tip
		)
		.tipTip( {
			attribute: 'data-tip',
			content:
				woocommerce_admin_meta_boxes.i18n_product_short_description_tip,
			fadeIn: 50,
			fadeOut: 50,
			delay: 200,
			keepAlive: true,
		} );

	// add a tooltip to the right of the product image meta box "Set product image" and "Add product gallery images"
	const setProductImageLink = $( '#set-post-thumbnail' );
	const tooltipMarkup = `<span class="woocommerce-help-tip" tabindex="0" aria-label="${ woocommerce_admin_meta_boxes.i18n_product_image_tip }"></span>`;
	const tooltipData = {
		attribute: 'data-tip',
		content: woocommerce_admin_meta_boxes.i18n_product_image_tip,
		fadeIn: 50,
		fadeOut: 50,
		delay: 200,
		keepAlive: true,
	};

	if ( setProductImageLink ) {
		$( tooltipMarkup )
			.insertAfter( setProductImageLink )
			.tipTip( tooltipData );
	}

	const addProductImagesLink = $( '.add_product_images > a' );

	if ( addProductImagesLink ) {
		$( tooltipMarkup )
			.insertAfter( addProductImagesLink )
			.tipTip( tooltipData );
	}
} );
