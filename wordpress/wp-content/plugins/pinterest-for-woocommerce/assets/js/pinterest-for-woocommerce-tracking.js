/*global pintrk*/

/**
 * Bind to search form submit in order to track the search event.
 * Site search should use <form role='search'> to integrate with Pinterest for WooCommerce search event automatically.
 */
// eslint-disable-next-line @wordpress/no-global-event-listener
window.addEventListener( 'load', function () {
	document
		.querySelectorAll( "form[role='search']" )
		.forEach( function ( form ) {
			form.addEventListener( 'submit', function () {
				if ( typeof pintrk !== 'function' ) {
					return;
				}

				const searchBox = form.querySelector( "input[type='search']" );

				if ( searchBox ) {
					pintrk( 'track', 'search', {
						search_query: searchBox.value,
					} );
				}
			} );
		} );
} );

//# sourceMappingURL=../source/_maps/js/pinterest-for-woocommerce-tracking.js.map
