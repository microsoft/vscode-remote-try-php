/*global fetch*/

/**
 * Disable the Save to Pinterest button if the Chrome extension is detected.
 */
// eslint-disable-next-line @wordpress/no-global-event-listener
window.addEventListener( 'load', function () {
	const disableSaveButton = () => {
		document
			.querySelectorAll( '.pinterest-for-woocommerce-image-wrapper' )
			.forEach( function ( button ) {
				button.style.display = 'none';
			} );
	};

	const isChromeExtensionDetected = () => {
		fetch(
			`chrome-extension://gpdjojdkbbmdfjfahjcgigfpmkopogic/html/save.html`
		)
			.then( () => disableSaveButton() )
			.catch( () => false );
	};

	isChromeExtensionDetected();
} );
