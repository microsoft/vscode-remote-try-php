/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since  x.x.x
 */
( function( $ ) {

	astra_css(
		'astra-settings[scroll-to-top-icon-size]',
		'font-size',
		'#ast-scroll-top',
		'px'
	);
	astra_css( 'astra-settings[scroll-to-top-icon-color]', 'color', '#ast-scroll-top' );
	astra_css( 'astra-settings[scroll-to-top-icon-bg-color]', 'background-color', '#ast-scroll-top' );
	astra_css( 'astra-settings[scroll-to-top-icon-h-color]', 'color', '#ast-scroll-top:hover' );
	astra_css( 'astra-settings[scroll-to-top-icon-h-bg-color]', 'background-color', '#ast-scroll-top:hover' );

	// Border Radius Fields for Button.
	wp.customize( 'astra-settings[scroll-to-top-icon-radius-fields]', function( value ) {
		value.bind( function( border ) {
			let tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
			mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;
			let dynamicStyle = '';
			dynamicStyle += ' #ast-scroll-top { border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
				+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
				+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';
			dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) { #ast-scroll-top { border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
				+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
				+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } }';
			dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) { #ast-scroll-top { border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
				+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
				+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } }';
			astra_add_dynamic_css( 'scroll-to-top-icon-radius-fields', dynamicStyle );
		} );
	} );

	// Scroll to top position.
	wp.customize( 'astra-settings[scroll-to-top-icon-position]', function( value ) {
		value.bind( function( position ) {
			jQuery("#ast-scroll-top").removeClass("ast-scroll-to-top-right ast-scroll-to-top-left");
			jQuery("#ast-scroll-top").addClass("ast-scroll-to-top-"+position);
		} );
	} );

} )( jQuery );
