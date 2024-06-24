export const withinIframe = () => {
	if ( window.location.href !== window.parent.location.href ) {
		return true;
	}
	return false;
};

export const getStorgeData = ( key ) => {
	return JSON.parse( localStorage.getItem( key ) );
};

export const shouldAddPreviewParam = ( element ) => {
	const link = element.href;

	if ( link === '' ) {
		return false;
	}

	if ( ! link.includes( window.location.origin ) ) {
		return false;
	}

	if ( link.includes( 'wp-admin' ) ) {
		return false;
	}

	if ( link.includes( '.php' ) ) {
		return false;
	}

	if ( link.includes( 'customize' ) ) {
		return false;
	}

	return true;
};
