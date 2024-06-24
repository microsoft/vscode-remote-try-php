(function($){

	AstraWidgetListIcons = {

		/**
		 * Init
		 */
		init: function()
		{
			this._bind();
			this._init_toggle_settings();
		},
		
		/**
		 * Binds events
		 */
		_bind: function()
		{
			$( document ).on('widget-updated widget-added', AstraWidgetListIcons._init_toggle_settings );
			$( document ).on('change', '.astra-widget-list-icons-fields .astra-widget-field-imageoricon', AstraWidgetListIcons._toggle_settings );
			$( document ).on('click', '.astra-widget-list-icons-fields .astra-repeater-container .actions', AstraWidgetListIcons._init_toggle_settings );
			$( document ).on('change', '.astra-widget-list-icons-fields .astra-widget-field-divider', AstraWidgetListIcons._toggle_divider_settings );

		},

		_init_toggle_settings: function() {
			$( '.astra-widget-list-icons-fields .astra-repeater-sortable .astra-repeater-field' ).each(function(index, el) {
				var parent = $( el );
				var image = parent.find( '.astra-widget-field-imageoricon' ).find('option:selected').val() || '';
				var divider = parent.find( '.astra-widget-field-divider' ).find('option:selected').val() || '';

				if( image === 'image' ) {
					parent.find('.astra-field-image-wrapper').show();
					parent.find('.astra-widget-icon-selector').hide();
				} else {
					parent.find('.astra-widget-icon-selector').show();
					parent.find('.astra-field-image-wrapper').hide();
				}

				if( divider === 'yes' ) {
					parent.find('.astra-widget-field-divider_weight').show();
					parent.find('.astra-widget-field-divider_style').show();
					parent.find('.astra-widget-field-divider_color').show();
				} else {
					parent.find('.astra-widget-field-divider_weight').hide();
					parent.find('.astra-widget-field-divider_style').hide();
					parent.find('.astra-widget-field-divider_color').hide();
				}
			});
		},

		_toggle_settings: function() {
			var image = $( this ).find('option:selected').val() || '';
			var parent = $( this ).closest('.astra-widget-list-icons-fields');

			if( image === 'image' ) {
				parent.find('.astra-field-image-wrapper').show();
				parent.find('.astra-widget-icon-selector').hide();
			} else {
				parent.find('.astra-widget-icon-selector').show();
				parent.find('.astra-field-image-wrapper').hide();
			}
		},

		_toggle_divider_settings: function() {
			var divider = $( this ).find('option:selected').val() || '';
			var parent  = $( this ).closest('.astra-widget-list-icons-fields');

			if( divider === 'yes' ) {
				parent.find('.astra-widget-field-divider_weight').show();
				parent.find('.astra-widget-field-divider_style').show();
				parent.find('.astra-widget-field-divider_color').show();
			} else {
				parent.find('.astra-widget-field-divider_weight').hide();
				parent.find('.astra-widget-field-divider_style').hide();
				parent.find('.astra-widget-field-divider_color').hide();
			}
		}

	};

	/**
	 * Initialization
	 */
	$(function(){
		AstraWidgetListIcons.init();
	});

})(jQuery);