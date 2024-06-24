(function($){

	AstraWidgetSocialProfiles = {

		/**
		 * Init
		 */
		init: function()
		{
			this._init_toggle_settings();
			this._bind();
		},
		
		/**
		 * Binds events
		 */
		_bind: function()
		{
			$( document ).on('widget-updated widget-added', AstraWidgetSocialProfiles._init_toggle_settings );
			$( document ).on('change', '.astra-widget-social-profiles-fields .astra-widget-field-icon-style', AstraWidgetSocialProfiles._toggle_settings );
			$( document ).on('change', '.astra-widget-social-profiles-fields .astra-widget-field-color-type', AstraWidgetSocialProfiles._toggle_settings );
		},

		_init_toggle_settings: function() {
			$( '.astra-widget-social-profiles-fields' ).each(function(index, el) {
				var parent 		= $( el );
				var style 		= parent.find( '.astra-widget-field-icon-style' ).find('option:selected').val() || '';
				var color_type 	= parent.find( '.astra-widget-field-color-type' ).find('option:selected').val() || '';


				if( color_type === 'official-color' ) {
					parent.find('.astra-widget-field-icon-color').hide();
					parent.find('.astra-widget-field-icon-hover-color').hide();
					parent.find('.astra-widget-field-bg-hover-color').hide();
					parent.find('.astra-widget-field-bg-color').hide();
				} else {
					parent.find('.astra-widget-field-icon-color').show();
					parent.find('.astra-widget-field-icon-hover-color').show();
					parent.find('.astra-widget-field-bg-hover-color').show();
					parent.find('.astra-widget-field-bg-color').show();
					
					if( style === 'simple' ) {
						parent.find('.astra-widget-field-bg-hover-color').hide();
						parent.find('.astra-widget-field-bg-color').hide();
					} else {
						parent.find('.astra-widget-field-bg-hover-color').show();
						parent.find('.astra-widget-field-bg-color').show();
					}
				}
			
			});
		},

		_toggle_settings: function() {
			var style = $( this ).find('option:selected').val() || '';
			var parent = $( this ).closest('.astra-widget-social-profiles-fields');
			var color_type 	= parent.find( '.astra-widget-field-color-type' ).find('option:selected').val() || '';


			if( color_type === 'official-color' ) {
				parent.find('.astra-widget-field-icon-color').hide();
				parent.find('.astra-widget-field-icon-hover-color').hide();
				parent.find('.astra-widget-field-bg-hover-color').hide();
				parent.find('.astra-widget-field-bg-color').hide();
			} else {
				parent.find('.astra-widget-field-icon-color').show();
				parent.find('.astra-widget-field-icon-hover-color').show();
				parent.find('.astra-widget-field-bg-hover-color').show();
				parent.find('.astra-widget-field-bg-color').show();
				
				if( style === 'simple' ) {
					parent.find('.astra-widget-field-bg-hover-color').hide();
					parent.find('.astra-widget-field-bg-color').hide();
				} else {
					parent.find('.astra-widget-field-bg-hover-color').show();
					parent.find('.astra-widget-field-bg-color').show();
				}
			}
		}

	};

	/**
	 * Initialization
	 */
	$(function(){
		AstraWidgetSocialProfiles.init();
	});

})(jQuery);