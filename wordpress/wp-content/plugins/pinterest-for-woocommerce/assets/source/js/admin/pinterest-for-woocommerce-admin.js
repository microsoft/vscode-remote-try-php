/* global jQuery */

( function ( $ ) {
	'use strict';

	function hideTabs( event, productType ) {
		$( '.hide_if_' + productType ).hide();
	}

	$( document ).on( 'woocommerce-product-type-change', 'body', hideTabs );

	$( document ).ready( function () {
		if ( $( '#product-type' ).length > 0 ) {
			hideTabs( false, $( '#product-type' ).val() );
		}
	} );
} )( jQuery );
