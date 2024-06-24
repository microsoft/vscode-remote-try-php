( function ( $ ) {
	let fileFrame;

	$( document ).ready( function () {
		$( '.cfvsw_upload_image_button' ).on( 'click', function ( event ) {
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( fileFrame ) {
				// Open frame
				fileFrame.open();
				return;
			}

			// Create the media frame.
			fileFrame = wp.media.frames.fileFrame = wp.media( {
				title: cfvsw_swatches_term_meta.image_upload_text.title,
				button: {
					text:
						cfvsw_swatches_term_meta.image_upload_text.button_title,
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
				$( '.cfvsw-image-preview' )
					.attr( 'src', attachmentUrl )
					.css( 'width', 'auto' )
					.show();
				$( '.cfvsw_remove_image_button' ).show();
				$( '.cfvsw_product_attribute_image' ).val( attachmentUrl );
			} );

			// Finally, open the modal
			fileFrame.open();
		} );

		$( '.cfvsw_remove_image_button' ).on( 'click', function () {
			$( '.cfvsw_product_attribute_image' ).val( '' );
			$( '.cfvsw-image-preview' ).attr( 'src', '' ).hide();
			$( '.cfvsw_remove_image_button' ).hide();
		} );

		$( '.cfvsw_color' ).wpColorPicker();
	} );

	$( document ).ajaxSuccess( function ( event, xhr, settings ) {
		//Check ajax action of request that succeeded
		if ( -1 === settings.data.indexOf( 'action=add-tag' ) ) {
			return;
		}
		const params = settings.data.split( '&' );
		const data = [];
		$.map( params, function ( val ) {
			const temp = val.split( '=' );
			data[ temp[ 0 ] ] = temp[ 1 ];
		} );
		if ( data.action === 'add-tag' ) {
			$( '.cfvsw_product_attribute_image' ).val( '' );
			$( '.cfvsw-image-preview' ).attr( 'src', '' ).hide();
			$( '.cfvsw_remove_image_button' ).hide();
			$( '.cfvsw_product_attribute_color .wp-picker-clear' ).trigger(
				'click'
			);
			$( '.cfvsw_product_attribute_color' ).trigger( 'click' );
		}

		if ( 'undefined' !== typeof data.cfvsw_image ) {
			$( '.wp-list-table #the-list' )
				.find( 'tr:first' )
				.find( 'th' )
				.after(
					'<td class="preview column-preview" data-colname="Preview"><img class="cfvsw-preview" src="' +
						decodeURIComponent( data.cfvsw_image ) +
						'" width="44px" height="44px"></td>'
				);
		}

		if ( 'undefined' !== typeof data.cfvsw_color ) {
			$( '.wp-list-table #the-list' )
				.find( 'tr:first' )
				.find( 'th' )
				.after(
					'<td class="preview column-preview" data-colname="Preview"><div class="cfvsw-preview" style="background-color:' +
						decodeURIComponent( data.cfvsw_color ) +
						';width:30px;height:30px;"></div></td>'
				);
		}
	} );
} )( jQuery );
