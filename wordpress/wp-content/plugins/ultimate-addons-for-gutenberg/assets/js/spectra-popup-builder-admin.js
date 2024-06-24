// Spectra Popup JS Actions Needed in the Admin CPT Page.

// Click Event to Enable or Disable Related Popup.
const UAGBToggelSwitch = ( event ) => {
	const element = event.target;
	// If the current toggle is on, this is false - else this is true.
	const updatedStatus = element.classList.contains( 'spectra-popup-builder__switch--active' ) ? 'false' : 'true';

	const mediaData = new FormData();
	mediaData.append( 'action', 'uag_update_popup_status' );
	mediaData.append( 'nonce', uagb_popup_builder_admin.uagb_popup_builder_admin_nonce );
	mediaData.append( 'post_id', element.dataset.post_id );
	mediaData.append( 'enabled', updatedStatus );

	fetch( uagb_popup_builder_admin.ajax_url, {
		method: 'POST',
		credentials: 'same-origin',
		body: mediaData,
	} )
	.then( ( resp ) => resp.json() )
	.then( ( data ) => {
		if ( false === data.success ) {
			return;
		}
		// If the API Fetch was successful, invert the toggle.
		if ( 'false' === updatedStatus ) {
			element.classList.remove( 'spectra-popup-builder__switch--active' );
		} else {
			element.classList.add( 'spectra-popup-builder__switch--active' );
		}
	} );
}

// Bind Related Click Events on Load.
document.addEventListener( 'DOMContentLoaded', () => {
	// Bind all the Toggles.
	const spectraToggles = document.querySelectorAll( '.spectra-popup-builder__switch' );
	for ( let spectraToggleCount = 0; spectraToggleCount < spectraToggles.length; spectraToggleCount++ ) {
		spectraToggles[ spectraToggleCount ].addEventListener( 'click', ( event ) => UAGBToggelSwitch( event ), false );
	}
} );
