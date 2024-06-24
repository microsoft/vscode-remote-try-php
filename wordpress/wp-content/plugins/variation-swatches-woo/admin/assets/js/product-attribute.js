( function ( $ ) {
	showSelectedOptionDescription();

	$( '.cfvsw-product-attribute-shape select' ).on( 'change', function () {
		showSelectedOptionDescription( $( 'option:selected', this ).val() );
	} );

	function showSelectedOptionDescription( selected = '' ) {
		selected =
			'' === selected
				? $( '.cfvsw-product-attribute-shape' ).find( 'select' ).val()
				: selected;
		$( '.cfvsw-product-attribute-shape' )
			.find( '.description' )
			.each( function () {
				if ( $( this ).hasClass( selected ) ) {
					$( this ).show();
				} else {
					$( this ).hide();
				}
			} );

		showSizeInput( selected );
	}

	const attr_type = $( '#attribute_type' ).prev().parent();

	$( attr_type )
		.find( 'p.description' )
		.html( cfvsw_admin_options.type_description );

	if ( $( '.cfvsw-attribute-section' ).length ) {
		$( '.cfvsw-attribute-section' ).after( attr_type );
	}

	$( '#attribute_type' ).on( 'change', function () {
		toggleShapeOptions( $( this ).val() );
		$( '#cfvsw_product_attribute_shape' )
			.val( 'default' )
			.trigger( 'change' );
	} );

	function showSizeInput( value ) {
		switch ( value ) {
			case 'default':
				$(
					'#cfvsw_product_attribute_size, .cfvsw-product-attribute-size, #cfvsw_product_attribute_height, .cfvsw-product-attribute-height, #cfvsw_product_attribute_width, .cfvsw-product-attribute-width'
				).hide();
				break;
			case 'custom':
				$(
					'#cfvsw_product_attribute_size, .cfvsw-product-attribute-size'
				).hide();
				$(
					'#cfvsw_product_attribute_height, .cfvsw-product-attribute-height, #cfvsw_product_attribute_width, .cfvsw-product-attribute-width'
				).show();
				break;

			default:
				$(
					'#cfvsw_product_attribute_size, .cfvsw-product-attribute-size'
				).show();
				$(
					'#cfvsw_product_attribute_height, .cfvsw-product-attribute-height, #cfvsw_product_attribute_width, .cfvsw-product-attribute-width'
				).hide();
				break;
		}
	}

	function toggleShapeOptions( type ) {
		if (
			'label' === type &&
			$( "#cfvsw_product_attribute_shape option[value='custom']" )
				.length < 1
		) {
			$( '#cfvsw_product_attribute_shape' ).append(
				'<option value=custom>Custom</option>'
			);
		} else {
			$(
				"#cfvsw_product_attribute_shape option[value='custom']"
			).remove();
		}
	}

	$( document ).ready( function () {
		if ( $( '.woocommerce-layout__activity-panel' ).length ) {
			$(
				'<button type="button" role="tab" aria-selected="false" aria-controls="activity-panel-activity" id="activity-panel-tab-swatches-settings" class="components-button woocommerce-layout__activity-panel-tab">' +
					'<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' +
					cfvsw_admin_options.swatches_label +
					'</button>'
			).prependTo(
				$(
					'.woocommerce-layout__activity-panel .woocommerce-layout__activity-panel-tabs'
				)
			);
		}

		$( document ).on(
			'click',
			'#activity-panel-tab-swatches-settings',
			function () {
				window.open( cfvsw_admin_options.settings_url, '_blank' );
			}
		);
	} );
} )( jQuery );
