UAGBForms = {
	getElement: ( id ) => {
		// Check if the script has run once already on the given element (required for homepage sidebar usage case).
		const getJsELement = document.querySelector( `${ id }:not(.uagb-activated-script)` );
		if ( ! getJsELement ) return null;

		// Ensures that the script only runs once on the given element (required for homepage sidebar usage case).
		getJsELement.classList.add( 'uagb-activated-script' );
		return getJsELement;
	},

	init( attr, id, post_id ) {
		const scope = UAGBForms.getElement( id );
		if ( ! scope ) {
			return;
		}

		const form = scope.querySelector( '.uagb-forms-main-form' );

		const phoneinput = form.querySelectorAll( '.uagb-forms-phone-input' );

		if ( phoneinput.length !== 0 ) {
			for ( let i = 0; i < phoneinput.length; i++ ) {
				phoneinput[ i ].addEventListener( 'keypress', function ( e ) {
					const charCode = e.which ? e.which : e.keyCode;
					if ( charCode === 45 ) {
						return true;
					}
					if ( charCode > 31 && ( charCode < 48 || charCode > 57 ) ) {
						return false;
					}
					return true;
				} );
			}
		}
		const toggleinput = form.querySelectorAll( '.uagb-forms-toggle-input' );

		if ( toggleinput.length !== 0 ) {
			for ( let j = 0; j < toggleinput.length; j++ ) {
				toggleinput[ j ].addEventListener( 'change', function () {
					if ( toggleinput[ j ].checked ) {
						const truestate = toggleinput[ j ].getAttribute( 'data-truestate' );
						toggleinput[ j ].setAttribute( 'value', truestate );
					} else {
						const falsestate = toggleinput[ j ].getAttribute( 'data-falsestate' );
						toggleinput[ j ].setAttribute( 'value', falsestate );
					}
				} );
			}
		}

		// validation for checkbox if required.
		const requiredCheckboxes = scope.querySelectorAll( '.uagb-forms-checkbox-wrap' );
		if ( requiredCheckboxes.length !== 0 ) {
			for ( let k = 0; k < requiredCheckboxes.length; k++ ) {
				const checkboxes = requiredCheckboxes[ k ].querySelectorAll( 'input[type=checkbox]' );

				if ( checkboxes.length > 0 ) {
					for ( let l = 0; l < checkboxes.length; l++ ) {
						checkboxes[ l ].addEventListener( 'change', function () {
							const isChecked = checkboxes[ l ].checked;
							const name = checkboxes[ l ].getAttribute( 'name' );

							const check = document.querySelectorAll( '[name="' + name + '"]' );
							for ( let i = 0; i < check.length; i++ ) {
								if ( isChecked ) {
									check[ i ].required = false;
								} else {
									check[ i ].required = true;
								}
							}
						} );
					}
				}
			}
		}

		let reCaptchaSiteKeyV2 = '',
			reCaptchaSiteKeyV3 = '';

		//append recaptcha js when enabled.
		if ( attr.reCaptchaEnable === true && attr.reCaptchaType === 'v2' ) {
			reCaptchaSiteKeyV2 = uagb_forms_data.recaptcha_site_key_v2;

			if ( reCaptchaSiteKeyV2 ) {
				if ( null === document.querySelector( '.uagb-forms-field-set' ).getAttribute( 'data-sitekey' ) ) {
					document.querySelector( '.g-recaptcha ' ).setAttribute( 'data-sitekey', reCaptchaSiteKeyV2 );
				}

				const recaptchaLink = document.createElement( 'script' );
				recaptchaLink.type = 'text/javascript';
				recaptchaLink.src = 'https://www.google.com/recaptcha/api.js';
				document.head.appendChild( recaptchaLink );
			}
		} else if ( attr.reCaptchaEnable === true && attr.reCaptchaType === 'v3' ) {
			reCaptchaSiteKeyV3 = uagb_forms_data.recaptcha_site_key_v3;

			if ( reCaptchaSiteKeyV3 ) {
				if ( attr.hidereCaptchaBatch ) {
					setTimeout( function(){
						const badge = document.getElementsByClassName( 'grecaptcha-badge' )[0];
						if( badge ){
							badge.style.visibility = 'hidden';
						}
					}, 500 );
				}
				const api = document.createElement( 'script' );
				api.type = 'text/javascript';
				api.src = 'https://www.google.com/recaptcha/api.js?render=' + reCaptchaSiteKeyV3;
				document.head.appendChild( api );
			}
		}

		//Ready Classes.
		const formscope = document.getElementsByClassName( 'uagb-block-' + attr.block_id );
		if ( formscope?.[ 0 ] ) {
			const formWrapper = formscope[ 0 ].children;
			const sibling = formWrapper[ 0 ].children;

			for ( let index = 0; index < sibling.length; index++ ) {
				if (
					sibling[ index ].classList.contains( 'uag-col-2' ) &&
					sibling[ index + 1 ].classList.contains( 'uag-col-2' )
				) {
					const div = document.createElement( 'div' );
					div.className = 'uag-col-2-wrap uag-col-wrap-' + index;
					sibling[ index + 1 ].after( div );
					const wrapper_div = formscope[ 0 ].getElementsByClassName( 'uag-col-wrap-' + index );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
				}

				if (
					sibling[ index ].classList.contains( 'uag-col-3' ) &&
					sibling[ index + 1 ].classList.contains( 'uag-col-3' ) &&
					sibling[ index + 2 ].classList.contains( 'uag-col-3' )
				) {
					const div = document.createElement( 'div' );
					div.className = 'uag-col-3-wrap uag-col-wrap-' + index;
					sibling[ index + 2 ].after( div );
					const wrapper_div = formscope[ 0 ].getElementsByClassName( 'uag-col-wrap-' + index );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
				}

				if (
					sibling[ index ].classList.contains( 'uag-col-4' ) &&
					sibling[ index + 1 ].classList.contains( 'uag-col-4' ) &&
					sibling[ index + 2 ].classList.contains( 'uag-col-4' ) &&
					sibling[ index + 3 ].classList.contains( 'uag-col-4' )
				) {
					const div = document.createElement( 'div' );
					div.className = 'uag-col-4-wrap uag-col-wrap-' + index;
					sibling[ index + 3 ].after( div );
					const wrapper_div = formscope[ 0 ].getElementsByClassName( 'uag-col-wrap-' + index );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
					wrapper_div[ 0 ].appendChild( sibling[ index ] );
				}
			}
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( attr.reCaptchaEnable === true && attr.reCaptchaType === 'v3' && reCaptchaSiteKeyV3 ) {
				if ( document.getElementsByClassName( 'grecaptcha-logo' ).length === 0 ) {
					document.querySelector( '.uagb-form-reacaptcha-error-' + attr.block_id ).innerHTML =
						'<p style="color:red !important" class="error-captcha">Invalid Google reCAPTCHA Site Key.</p>';
					return false;
				}

				// eslint-disable-next-line no-undef
				grecaptcha.ready( function () {
					// eslint-disable-next-line no-undef
					grecaptcha.execute( reCaptchaSiteKeyV3, { action: 'submit' } ).then( function ( token ) {
						if ( token ) {
							if ( document.getElementsByClassName( 'uagb-forms-recaptcha' ).length !== 0 ) {
								document.getElementById( 'g-recaptcha-response' ).value = token;

								window.UAGBForms._formSubmit(
									e,
									form,
									attr,
									reCaptchaSiteKeyV2,
									reCaptchaSiteKeyV3,
									post_id
								);
							} else {
								document.querySelector( '.uagb-form-reacaptcha-error-' + attr.block_id ).innerHTML =
									'<p style="color:red !important" class="error-captcha">Google reCAPTCHA Response not found.</p>';
								return false;
							}
						}
					} );
				} );
			} else {
				window.UAGBForms._formSubmit( e, this, attr, reCaptchaSiteKeyV2, reCaptchaSiteKeyV3, post_id );
			}
		} );
	},

	_formSubmit( e, form, attr, reCaptchaSiteKeyV2, reCaptchaSiteKeyV3, post_id ) {
		e.preventDefault();

		let captcha_response;

		if ( '' === attr.afterSubmitToEmail || null === attr.afterSubmitToEmail ) {
			const hideForm = document.querySelector( '[name="uagb-form-' + attr.block_id + '"]' );
			hideForm.style.display = 'none';

			const errorMsg = document.querySelector( '.uagb-forms-failed-message-' + attr.block_id );
			errorMsg?.classList?.remove( 'uagb-forms-submit-message-hide' );
			errorMsg?.classList?.add( 'uagb-forms-failed-message' );
			return false;
		}

		if ( attr.reCaptchaEnable === true ) {
			if ( attr.reCaptchaType === 'v2' && reCaptchaSiteKeyV2 ) {
				if ( document.getElementsByClassName( 'uagb-forms-recaptcha' ).length !== 0 ) {
					captcha_response = document.getElementById( 'g-recaptcha-response' ).value;

					if ( ! captcha_response ) {
						document.querySelector( '.uagb-form-reacaptcha-error-' + attr.block_id ).innerHTML =
							'<p style="color:red !important" class="error-captcha">' + attr.captchaMessage + '</p>';
						return false;
					}
					document.querySelector( '.uagb-form-reacaptcha-error-' + attr.block_id ).innerHTML = '';
				} else {
					document.querySelector( '.uagb-form-reacaptcha-error-' + attr.block_id ).innerHTML =
						'<p style="color:red !important" class="error-captcha"> Google reCAPTCHA Response not found.</p>';
					return false;
				}
			} else if ( attr.reCaptchaType === 'v3' && reCaptchaSiteKeyV3 ) {
				captcha_response = document.getElementById( 'g-recaptcha-response' ).value;
			}
		}

		const originalSerialized = window.UAGBForms._serializeIt( form );

		const postData = {};
		postData.id = attr.block_id;
		for ( let i = 0; i < originalSerialized.length; i++ ) {
			const inputname = document.getElementById( originalSerialized[ i ].name );

			if ( originalSerialized[ i ].name.endsWith( '[]' ) ) {
				const name = originalSerialized[ i ].name.replace( /[\[\]']+/g, '' );
				//For checkbox element
				if ( ! ( name in postData ) ) {
					postData[ name ] = [];
				}
				postData[ name ].push( originalSerialized[ i ].value );
			} else if ( inputname !== null ) {
				postData[ inputname.innerHTML ] = originalSerialized[ i ].value;
			}

			const hiddenField = document.getElementById( 'hidden' );

			if ( hiddenField !== null && hiddenField !== undefined ) {
				postData[ hiddenField.getAttribute( 'name' ) ] = hiddenField.getAttribute( 'value' );
			}
		}

		// eslint-disable-next-line no-undef
		fetch( uagb_forms_data.ajax_url, {
			method: 'POST',
			headers: new Headers( { 'Content-Type': 'application/x-www-form-urlencoded' } ), // eslint-disable-line no-undef
			body: new URLSearchParams( {
				action: 'uagb_process_forms',
				nonce: uagb_forms_data.uagb_forms_ajax_nonce,
				form_data: JSON.stringify( postData ),
				sendAfterSubmitEmail: attr.sendAfterSubmitEmail,
				captcha_version: attr.reCaptchaType,
				captcha_response,
				post_id,
				block_id: attr.block_id,
			} ),
		} )
			.then( ( resp ) => resp.json() )
			.then( function ( data ) {
				const hideForm = document.querySelector( '[name="uagb-form-' + attr.block_id + '"]' );
				hideForm.style.display = 'none';
				if ( 200 === data.data ) {
					if ( 'message' === attr.confirmationType ) {
						const errorMsg = document.querySelector( '.uagb-forms-success-message-' + attr.block_id );
						errorMsg.classList.remove( 'uagb-forms-submit-message-hide' );
						errorMsg.classList.add( 'uagb-forms-success-message' );
					}

					if ( 'url' === attr.confirmationType ) {
						window.location.replace( attr.confirmationUrl );
					}
				} else if ( 400 === data.data ) {
					if ( 'message' === attr.confirmationType ) {
						const successMsg = document.querySelector( '.uagb-forms-failed-message-' + attr.block_id );
						successMsg.classList.remove( 'uagb-forms-submit-message-hide' );
						successMsg.classList.add( 'uagb-forms-failed-message' );
					}
				}
			} )
			.catch( function ( error ) {
				console.log( JSON.stringify( error ) ); // eslint-disable-line no-console
			} );
	},

	_serializeIt( form ) {
		return Array.apply( 0, form.elements )
			.map( ( x ) =>
				( ( obj ) =>
					// eslint-disable-next-line no-nested-ternary
					x.type === 'radio' || x.type === 'checkbox' ? ( x.checked ? obj : null ) : obj )( {
					name: x.name,
					value: x.value,
				} )
			)
			.filter( ( x ) => x );
	},
};
