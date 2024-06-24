import qs from 'qs';

export const parseResponse = ( resp ) =>
	resp.json().then( ( data ) => {
		if ( resp.ok ) {
			// Expose response via a getter, which avoids copying.
			Object.defineProperty( data, 'getResponse', {
				get: () => () => resp,
			} );
			return data;
		}

		// Build an error
		const err = new Error( data.message );
		err.code = data.code;
		err.data = data.data;
		throw err;
	} );

export default class {
	constructor( config ) {
		this.url = config.rest_url ? config.rest_url : config.url + 'wp-json';
		this.url = this.url.replace( /\/$/, '' );
		this.credentials = Object.assign( {}, config.credentials );
		this.config = config;
	}

	getAuthorizationHeader() {
		if ( ! this.credentials.nonce ) {
			return {};
		}

		return { 'X-WP-Nonce': this.config.credentials.nonce };
	}

	authorize() {
		return Promise.resolve( 'Success' );
	}

	saveCredentials() {
		// no op.
	}

	removeCredentials() {
		// no op.
	}

	hasCredentials() {
		return true;
	}

	restoreCredentials() {
		return this;
	}

	get( url, data ) {
		return this.request( 'GET', url, data );
	}

	post( url, data ) {
		return this.request( 'POST', url, data );
	}

	del( url, data ) {
		return this.request( 'DELETE', url, data );
	}

	request( method, url, data = null ) {
		if ( url.indexOf( 'http' ) !== 0 ) {
			url = this.url + url;
		}

		if ( data ) {
			url += `?${ decodeURIComponent( qs.stringify( data ) ) }`;
			data = null;
		}

		let headers = { Accept: 'application/json' };

		/**
		 * Only attach the oauth headers if we have a nonce
		 */
		if ( this.credentials.nonce ) {
			headers = {
				...headers,
				...this.getAuthorizationHeader(),
			};
		}

		const opts = {
			method,
			headers,
			credentials: 'include',
			body:
				[ 'GET', 'HEAD' ].indexOf( method ) > -1
					? null
					: qs.stringify( data ),
		};

		return fetch( url, opts ).then( parseResponse );
	}

	fetch( url, options ) {
		// Make URL absolute
		const relUrl = url[ 0 ] === '/' ? url.substring( 1 ) : url;
		const absUrl = new URL( relUrl, this.url + '/' );

		// Clone options
		const actualOptions = {
			headers: {},
			credentials: 'include',
			...options,
		};

		/**
		 * Only attach the oauth headers if we have a nonce
		 */
		if ( this.credentials.nonce ) {
			actualOptions.headers = {
				...actualOptions.headers,
				...this.getAuthorizationHeader(),
			};
		}

		return fetch( absUrl, actualOptions );
	}
}
