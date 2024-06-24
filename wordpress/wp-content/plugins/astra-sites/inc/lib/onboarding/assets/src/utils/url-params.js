export const getURLParmsValue = ( url, key ) => {
	const urlParams = new URLSearchParams( url );
	return urlParams.get( key ) || '';
};

export const setURLParmsValue = ( key, value ) => {
	const urlParams = new URLSearchParams( window.location.search );
	if ( value ) {
		urlParams.set( key, value );
	} else {
		urlParams.delete( key );
	}

	return urlParams.toString();
};
