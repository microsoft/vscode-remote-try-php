/**
 * Created by dagan on 07/04/2014.
 */
'use strict';

window.XgUtils =
	window.XgUtils ||
	( function () {
		function extend( object, defaultObject ) {
			const result = defaultObject || {};
			let key;
			for ( key in object ) {
				if ( object.hasOwnProperty( key ) ) {
					result[ key ] = object[ key ];
				}
			}
			return result;
		}

		//public interface
		return {
			extend,
		};
	} )();

window.xsLocalStorage =
	window.xsLocalStorage ||
	( function () {
		const MESSAGE_NAMESPACE = 'cross-domain-local-message-uag';
		let options = {
			iframeId: 'cross-domain-iframe-uag',
			iframeUrl: undefined,
			initCallback() {},
		};
		let requestId = -1;
		let iframe;
		const requests = {};
		let wasInit = false;
		let iframeReady = true;

		function applyCallback( data ) {
			if ( requests[ data.id ] ) {
				requests[ data.id ]( data );
				delete requests[ data.id ];
			}
		}

		function receiveMessage( event ) {
			let data;
			try {
				data = JSON.parse( event.data );
			} catch ( err ) {
				//not our message, can ignore
			}
			if ( data && data.namespace === MESSAGE_NAMESPACE ) {
				if ( data.id === 'iframe-ready' ) {
					iframeReady = true;
					options.initCallback();
				} else {
					applyCallback( data );
				}
			}
		}

		function buildMessage( action, key, value, callback ) {
			requestId++;
			requests[ requestId ] = callback;
			const data = {
				namespace: MESSAGE_NAMESPACE,
				id: requestId,
				action,
				key,
				value,
			};
			iframe?.contentWindow.postMessage( JSON.stringify( data ), '*' );
		}

		function init( customOptions ) {
			/* eslint-disable no-undef */
			options = XgUtils.extend( customOptions, options );
			const temp = document.createElement( 'div' );

			if ( window.addEventListener ) {
				window.addEventListener( 'message', receiveMessage, false );
			} else {
				window.attachEvent( 'onmessage', receiveMessage );
			}

			temp.innerHTML =
				'<iframe id="' + options.iframeId + '" src=' + options.iframeUrl + ' style="display: none;"></iframe>';
			document.body.appendChild( temp );
			iframe = document.getElementById( options.iframeId );
		}

		function isApiReady() {
			if ( ! wasInit ) {
				return false;
			}
			if ( ! iframeReady ) {
				return false;
			}
			return true;
		}

		function isDomReady() {
			return document.readyState === 'complete';
		}

		return {
			//callback is optional for cases you use the api before window load.
			init( customOptions ) {
				if ( ! customOptions.iframeUrl ) {
					throw 'Please specify the iframe URL';
				}
				if ( wasInit ) {
					return;
				}
				wasInit = true;
				if ( isDomReady() ) {
					init( customOptions );
				} else if ( document.addEventListener ) {
					// All browsers expect IE < 9
					document.addEventListener( 'readystatechange', function () {
						if ( isDomReady() ) {
							init( customOptions );
						}
					} );
				} else {
					// IE < 9
					document.attachEvent( 'readystatechange', function () {
						if ( isDomReady() ) {
							init( customOptions );
						}
					} );
				}
			},
			setItem( key, value, callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'set', key, value, callback );
			},

			getItem( key, callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'get', key, null, callback );
			},
			removeItem( key, callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'remove', key, null, callback );
			},
			key( index, callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'key', index, null, callback );
			},
			getSize( callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'size', null, null, callback );
			},
			getLength( callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'length', null, null, callback );
			},
			clear( callback ) {
				if ( ! isApiReady() ) {
					return;
				}
				buildMessage( 'clear', null, null, callback );
			},
			wasInit() {
				return wasInit;
			},
		};
	} )();
