const getUAGEditorStateLocalStorage = ( key = false ) => {
	if ( ! window.localStorage ) {
		return null;
	}

	if ( ! key ) {
		return localStorage;
	}

	const uagLastOpenedSettingState = localStorage.getItem( key );

	if ( uagLastOpenedSettingState ) {
		return JSON.parse( uagLastOpenedSettingState );
	}

	return null;
};

export default getUAGEditorStateLocalStorage;
