/**
 * Customizer controls
 *
 * @package Astra
 */

( function( $ ) {

	/* Internal shorthand */
	var api = wp.customize;

	/**
	 * Helper class for the main Customizer interface.
	 *
	 * @since 1.0.0
	 * @class ASTCustomizer
	 */
	ASTCustomizer = {

		controls	: {},

		/**
		 * Initializes our custom logic for the Customizer.
		 *
		 * @since 1.0.0
		 * @method init
		 */
		init: function()
		{
			ASTCustomizer._initToggles();
			ASTCustomizer._initSubControlsToggle();
		},

		/**
		 * Initializes the logic for showing and hiding controls
		 * when a setting changes.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _initToggles
		 */
		_initToggles: function()
		{
			// Trigger the Adv Tab Click trigger.
			ASTControlTrigger.triggerHook( 'astra-toggle-control', api );

			// Loop through each setting.
			$.each( ASTCustomizerToggles, function( settingId, toggles ) {

				// Get the setting object.
				api( settingId, function( setting ) {

					// Loop though the toggles for the setting.
					$.each( toggles, function( i, toggle ) {

						// Loop through the controls for the toggle.
						$.each( toggle.controls, function( k, controlId ) {

							// Get the control object.
							api.control( controlId, function( control ) {

								// Define the visibility callback.
								var visibility = function( to ) {
									control.container.toggle( toggle.callback( to ) );
								};

								// Init visibility.
								visibility( setting.get() );

								// Bind the visibility callback to the setting.
								setting.bind( visibility );
							});
						});
					});
				});

			});
		},

		/**
		 * Toggle sub control visibility CSS.
		 *
		 * @since x.x.x
		 */
		subControlsToggleCSS( controlValue, dependents ) {
			$.each( dependents, function( controlOption, dependentSubControls ) {
				$.each( dependentSubControls, function( dependentIndex, subControl ) {
					// Remove old.
					jQuery( 'style#ast-sub-control-' + subControl ).remove();
					// Add new.
					if ( controlValue !== controlOption ) {
						// Concat and append new <style>.
						jQuery( 'head' ).append(
							'<style id="ast-sub-control-' + subControl + '">' +
							'#customize-control-' + subControl + '	{ display: none; }' +
							'</style>'
						);
					}
				});
			});
		},

		/**
		 * Initializes the logic for showing and hiding sub controls
		 * when a setting changes.
		 *
		 * @since x.x.x
		 * @access private
		 * @method _initSubControlsToggle
		 * @return void
		 */
		_initSubControlsToggle: function()
		{
			document.addEventListener('AstraToggleSubControls', function (e) {
				let subControlData = e.detail;
				ASTCustomizer.subControlsToggleCSS( subControlData.controlValue, subControlData.dependents );
			});
		}
	};

	$( function() { ASTCustomizer.init(); } );

})( jQuery );


( function( api ) {
    // Extends our custom astra-pro section.
    api.sectionConstructor['astra-pro'] = api.Section.extend( {
        // No events for this type of section.
        attachEvents: function () {},
        // Always make the section active.
        isContextuallyActive: function () {
            return true;
        }
    } );
} )( wp.customize );
