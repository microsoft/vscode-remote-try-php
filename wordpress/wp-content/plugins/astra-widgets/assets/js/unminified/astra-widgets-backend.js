(function($){

	AstraWidgets = {

		/**
		 * Init
		 */
		init: function()
		{
			this._init_colorpicker();
			this._init_repeater();
			this._getMarkup();
			this._bind();
		},
		_init_colorpicker: function() {
			$('.astra-widget-field-color .astra-widget-color > input').not('[id*="__i__"]').wpColorPicker({
				change: function (event, ui) {
					$( event.target ).val( ui.color.toString() );
      				$( event.target ).trigger('change');
				}
			});
		},

		/**
		 * Binds events
		 */
		_bind: function()
		{
			$( document ).on('widget-updated widget-added', AstraWidgets._reinit_controls );
			$( document ).on('click', '.astra-select-icon', AstraWidgets._icon_selector );
			$( document ).on('click', '.astra-widget-icon', AstraWidgets._set_icon );

			// Bind repeater events.
			$( document ).on('click', '.astra-repeater-sortable .clone', AstraWidgets._repeater_clone);
			$( document ).on('click', '.astra-repeater-sortable .remove', AstraWidgets._repeater_remove);
			$( document ).on('click', '.astra-repeater-field .actions', AstraWidgets._repeater_toggle_open);
			$( document ).on('click', '.astra-repeater .add-new-btn', AstraWidgets._add_new );
			$( document ).on('click', '.widget-control-save', AstraWidgets._repeater_reinit );
			$( document ).on('click', '.astra-repeater-field .astra-select-image', AstraWidgets._repeater_add_image_field );
			$( document ).on('click', '.astra-repeater-field .astra-remove-image', AstraWidgets._repeater_remove_image_field );
			$( document ).on('input', '.astra-repeater-field [data-field-id="title"]', AstraWidgets._repeater_set_title );
			$( document ).on('keyup', '.astra-repeater-field .search-icon', AstraWidgets._searchFuntionality );
			$( document ).on('click', '.astra-repeater-field .astra-select-icon', AstraWidgets._showIconsMarkup );
		},

		_reinit_controls: function() {
			AstraWidgets._init_colorpicker();
			AstraWidgets._init_repeater();
		},
		_getMarkup: function() {
			var font_awesome = fontAwesomeIcons.font_awesome;
			var font_awesome_markup  = '<input type="search" placeholder="Search icon..." class="search-icon">';
			    font_awesome_markup += '<ul class="astra-widget-icons-list">';
			for (var key in font_awesome) {
				if (font_awesome.hasOwnProperty(key)) {
					var fontAwesome = font_awesome[key];
					var viewbox_array = ( fontAwesome['svg'].hasOwnProperty("brands") ) ? fontAwesome['svg']['brands']['viewBox'] : fontAwesome['svg']['solid']['viewBox'];
					var path = ( fontAwesome['svg'].hasOwnProperty("brands") ) ? fontAwesome['svg']['brands']['path'] : fontAwesome['svg']['solid']['path'];
					var viewBox = viewbox_array.join( ' ' );
					var terms = fontAwesome['search']['terms'].join( ' ' );
					fontAwesome['search']['terms'].push( key );
					fontAwesome['search']['terms'].push( fontAwesome['styles']['0'] );
					font_awesome_markup += '<li class="astra-widget-icon ' + key + '" data-search-terms="' + terms + '" data-font="'+key+'" data-viewbox="'+viewBox+'" data-path="'+path+'">';
					font_awesome_markup += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="'+viewBox+'"><path d="'+path+'"></path></svg>';
					font_awesome_markup += '</li>';
				}
			}
			font_awesome_markup += '</ul>';
			return font_awesome_markup;
		},
		_showIconsMarkup: function() {

			font_awesome_markup = AstraWidgets._getMarkup();
			if( $(this).hasClass( 'open' ) ) {
				$(this).parents('.astra-widget-icon-selector').find('.astra-icons-list-wrap').append( font_awesome_markup );
			} else {
				$(this).parents('.astra-widget-icon-selector').find('.astra-widget-icons-list').remove();
				$(this).parents('.astra-widget-icon-selector').find('.search-icon').remove();
			}

		},
		_icon_selector: function(event) {
			var parent = $(this).parents('.astra-widget-icon-selector');
			parent.find('.astra-icons-list-wrap').slideToggle();
			$(this).toggleClass( 'open' );
		},

		_set_icon: function(event) {
			var parent               	= $(this).parents('.astra-widget-icon-selector');
			var selected_icon_font   	= $(this).attr('data-font') || '';
			var icon_selector  		 	= parent.find( '.astra-widget-icon.' + selected_icon_font  );
			var current_icon_preview 	= parent.find('.astra-selected-icon');
			var current_icon_input   	= parent.find('.selected-icon');
			var icon_selector_path	 	= $(this).attr('data-path');
			var icon_selector_viewbox	= $(this).attr('data-viewbox');
			var icon_selector_svg	 	= icon_selector.html();

			// current_icon_preview.removeClass();

			current_icon_preview.html( icon_selector_svg );

			parent.find('.astra-widget-icons-list .astra-widget-icon').removeClass( 'selected' );
			icon_selector.addClass( 'selected' );

			if( $(this).closest('.astra-repeater-field').find('.selected-icon').data('icon-visible') === 'yes' ) {
				$(this).closest('.astra-repeater-field').find('.title').attr('class','title');
				$(this).closest('.astra-repeater-field').find('.title').addClass( selected_icon_font );
			}

			iconObj = {
			    'name':selected_icon_font,
			    'path':icon_selector_path,
			    'viewbox': icon_selector_viewbox
			 };
			var icon_data = JSON.stringify(iconObj);

			current_icon_input.val( icon_data );

			// Trigger the change event.
	 		parent.find('.selected-icon').trigger( 'change' );
		},

		_searchFuntionality: function() {

		    // Declare variables
		    var input, filter, ul, li, a, i;
		    input = this;
		    filter = input.value.toUpperCase();
		    ul = $(this).parents('.astra-icons-list-wrap').find(".astra-widget-icons-list")[0];
		    setTimeout( function() {
			    li = ul.getElementsByTagName('li');

			    // Loop through all list items, and hide those who don't match the search query
			    for (i = 0; i < li.length; i++) {
			        search = $(li[i]).data('search-terms');
			        if( search ) {
				            if ( search.toUpperCase().indexOf( filter ) > -1 ) {
					            li[i].style.display = "";
					        } else {
					            li[i].style.display = "none";
					        }
			        }
			    }
        	}, 300 );
		},

		/**
		 * Repeater remove image field.
		 *
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		_repeater_remove_image_field: function(event) {
			if( confirm('Do you want to remove this image?') ) {
				var self 	= $(this);
				var parent 	= self.parents('.astra-repeater-field');
				parent.find('.astra-field-image-preview').html('');
				parent.find('.astra-field-image-preview img').attr('src', '' );
				parent.find('.astra-field-image-preview-id').val( '' );
				parent.find('.astra-image-url').val( '' );
				parent.find('.astra-image-alt').val( '' );
				parent.find('.astra-image-title').val( '' );
				parent.find('.astra-image-size-select, .astra-image-width').hide();
				parent.find('.astra-select-image.button').css("display", "inline-block");
			}
		},

		/**
		 * Repeater add image field
		 *
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		_repeater_add_image_field: function(event) {

			var self 	= $(this);
			var parent 	= self.parents('.astra-repeater-field');

			var frame = wp.media({
				title: 'Select or Upload Image',
				button: {
					text: 'Choose Image'
				},
				library: {
					type: 'image'
				},
				multiple: false,
			});

			// Handle results from media manager.
			frame.on('close',function( ) {
				var attachments = frame.state().get('selection').toJSON();

				if( $.isEmptyObject( attachments ) ) {
					return;
				}

				if( attachments[0].sizes.hasOwnProperty('medium') ) {
					var url = attachments[0].sizes.medium.url;
				} else if( attachments[0].sizes.hasOwnProperty('thumbnail') ) {
					var url = attachments[0].sizes.thumbnail.url;
				} else {
					var url = attachments[0].sizes.full.url;
				}

				if( parent.find('.astra-remove-image').length > 0 ) {
					parent.find('.astra-field-image-preview img').attr('src', url );
				} else {
					parent.find('.astra-field-image-preview').append('<img src="'+url+'" /><span class="astra-remove-image button">Remove</span>');
				}
				parent.find('.astra-image-url').val( attachments[0].url );
				parent.find('.astra-image-alt').val( attachments[0].alt );
				parent.find('.astra-image-title').val( attachments[0].title );

				parent.find('.astra-field-image-preview-id').val( attachments[0].id );
				parent.find('.astra-select-image.button').css("display", "none");


				parent.find('.astra-image-size-select, .astra-image-width').show();

				// Trigger the change event.
	 			parent.find('input').trigger( 'change' );
			});

			frame.open();
			return false;
		},

		/**
		 * Return substring.
		 *
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		_get_sub_string: function( val ) {

 			var str = '';
			if( val.length > 24 ) {
				var str = '..';
			}

			val = val.substring(0,24) + str;

			return val;
		},

		/**
		 * Repeater set title.
		 *
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		_repeater_set_title: function( e ) {
			var val = $( this ).val() || '';
			val = AstraWidgets._get_sub_string( val );

			$(this).closest('.astra-repeater-field').find('.title').text( val )
		},

		/**
		 * Repeater reinit
		 *
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		_repeater_reinit: function( e ) {
			$('.astra-repeater-sortable').sortable();
		},

		/**
		 * Repeater add new
		 *
		 * @param {[type]} e [description]
		 */
		_add_new: function( e ) {
			e.preventDefault();

			var selector    = $(this),
				parent      = selector.closest('.astra-repeater'),
				length      = $('.astra-repeater-field').length || 0,
				fields      = parent.find( '.astra-repeater-fields' ).html(),
				title       = parent.find( '.astra-repeater-fields' ).attr('title') || '',
				repeater_id = parent.find( '.astra-repeater-fields' ).attr('data-id') || '';

			fields = fields.replace('][][', ']['+length+'][');

			var item  = '<div class="astra-repeater-field">';
				item += '	<div class="actions">';
				item += '	<span class="index">'+length+'</span>';
				item += '		<span class="dashicons dashicons-move"></span>';
				item += '	<span class="title">'+title+'</span>';
				item += '		<span class="dashicons dashicons-admin-page clone"></span>';
				item += '		<span class="dashicons dashicons-trash remove"></span>';
				item += '		<span class="dashicons toggle-arrow"></span>';
				item += '	</div>';
				item += '	<div class="markukp">';
				item += 		fields
				item += '	</div>';
				item += '</div>';

	 		parent.find('.astra-repeater-sortable').append( item );

	 		// Set repeater fields names.
	 		AstraWidgets._set_repeater_names();
		},

		/**
		 * Repeater set names
		 */
		_set_repeater_names: function() {
	 		$('.astra-repeater').each(function(repeaterIndex, repeaterEl) {
	 			var repeater_id = $(repeaterEl).find( '.astra-repeater-fields' ).attr('data-id') || '';
	        	$(repeaterEl).find('.astra-repeater-sortable').find('.astra-repeater-field').each(function(repeaterFieldIndex, repeaterFieldEl){
		        	$(repeaterFieldEl).find(':input').each(function(currentElindex, currentEl){

						var field_id   = $(currentEl).attr('data-field-id') || '';
						var field_name = repeater_id + '['+repeaterFieldIndex+'][' + field_id + ']';

						// Show index.
						$(repeaterFieldEl).find('.index').text( repeaterFieldIndex );

		        		// Set new name.
		        		$(currentEl).attr('name', field_name);
					});
				});
	 		});
	 	},

	 	/**
	 	 * Repeater Toggle Open
	 	 *
	 	 * @param  {[type]} e [description]
	 	 * @return {[type]}   [description]
	 	 */
		_repeater_toggle_open: function(e) {
	    	e.preventDefault();

	    	// Toggle on click on move icon & title too.
	    	if( ( e.target === this ) || $( e.target ).hasClass('title') || $( e.target ).hasClass('dashicons-move') ) {
	    		$( this ).parents('.astra-repeater-field').toggleClass('field-open');
		    	$( this ).parents('.astra-repeater-field').find('.markukp').slideToggle();
	    	}
	    },

	    /**
	     * Repeater clone
	     *
	     * @param  {[type]} e [description]
	     * @return {[type]}   [description]
	     */
		_repeater_clone: function(e) {
			e.preventDefault();

			var $item    = $( this ),
				parent   = $item.closest('.astra-repeater'),
				fields   = parent.find( '.astra-repeater-fields' ).html(),
				copyItem = $( $item ).closest('.astra-repeater-field').clone();

	    	copyItem.insertAfter( $item.closest('.astra-repeater-field') );

	    	$('.astra-repeater-sortable').sortable();

	    	// Trigger the change event.
	 		parent.find('input').trigger( 'change' );

	    	// Set repeater fields names.
	    	AstraWidgets._set_repeater_names();
		},

		/**
		 * Repeater remove
		 *
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		_repeater_remove: function( e ) {
	    	e.preventDefault();

	    	var $item 	= $( this );
	    	var parent 	= $item.closest('.astra-repeater');
			var title 	= $item.parent().find('.title').html();
			var str 	= '';

			if( title.length > 0 ) {
				str = title;
			} else {
				str = 'this field';
			}

	    	if( confirm( 'Are you sure you want to delete ' + str + '?' ) ) {
	    		$item.closest('.astra-repeater-field').remove();
	    	}

	    	// Set repeater fields names.
	 		AstraWidgets._set_repeater_names();

	 		// Trigger the change event.
	 		parent.find('input').trigger( 'change' );
	    },

	    /**
	     * Repeater init
	     *
	     * @return {[type]} [description]
	     */
		_init_repeater: function()
		{
			$('.astra-repeater-sortable').sortable({
		        cursor: 'move',
		        stop: function( event, ui ) {
		        	// Set repeater fields names.
	 				AstraWidgets._set_repeater_names();

	 				// Trigger the change event.
	 				ui.item.find('input').trigger( 'change' );
		        },
		    });

		    // Set repeater fields names.
	 		AstraWidgets._set_repeater_names();

		    if( $('.astra-repeater-field').length ) {
				$('.astra-repeater-field').each(function(index, el) {
					var title = $( el ).find('[data-field-id="title"]' ).val() || '';
					var icon = $( el ).find('[data-field-id="icon"]' ).val() || '';

					title = AstraWidgets._get_sub_string( title );

			    	$(el).find('.title').text( title );

					if( $(el).find('.selected-icon').data('icon-visible') === 'yes' ) {
						$(el).find('.title').addClass( icon );
					}
				});
		    }
		}

	};

	/**
	 * Initialization
	 */
	$(function(){
		AstraWidgets.init();
	});

})(jQuery);
