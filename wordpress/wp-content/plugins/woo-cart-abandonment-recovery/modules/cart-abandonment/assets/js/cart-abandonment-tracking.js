( function ( $ ) {
	let timer;
	const wcf_cart_abandonment = {
		init() {
			if (
				wcf_ca_vars._show_gdpr_message &&
				! $( '#wcf_cf_gdpr_message_block' ).length
			) {
				$( '#billing_email' ).after(
					"<span id='wcf_cf_gdpr_message_block'> <span style='font-size: xx-small'> " +
						wcf_ca_vars._gdpr_message +
						" <a style='cursor: pointer' id='wcf_ca_gdpr_no_thanks'> " +
						wcf_ca_vars._gdpr_nothanks_msg +
						' </a></span></span>'
				);
			}

			$( document ).on(
				'keyup keypress change',
				'#billing_email, #billing_phone, input.input-text, textarea.input-text, select',
				this._getCheckoutData
			);

			$( '#wcf_ca_gdpr_no_thanks' ).on( 'click', function () {
				wcf_cart_abandonment._set_cookie();
			} );

			$( document.body ).on( 'updated_checkout', function () {
				wcf_cart_abandonment._getCheckoutData();
			} );

			$( function () {
				setTimeout( function () {
					wcf_cart_abandonment._getCheckoutData();
				}, 800 );
			} );
		},

		_set_cookie() {
			const data = {
				wcf_ca_skip_track_data: true,
				action: 'cartflows_skip_cart_tracking_gdpr',
				security: wcf_ca_vars._gdpr_nonce,
			};

			jQuery.post( wcf_ca_vars.ajaxurl, data, function ( response ) {
				if ( response.success ) {
					$( '#wcf_cf_gdpr_message_block' )
						.empty()
						.append(
							"<span style='font-size: xx-small'>" +
								wcf_ca_vars._gdpr_after_no_thanks_msg +
								'</span>'
						)
						.delay( 5000 )
						.fadeOut();
				}
			} );
		},

		_validate_email( value ) {
			let valid = true;
			if ( value.indexOf( '@' ) === -1 ) {
				valid = false;
			} else {
				const parts = value.split( '@' );
				const domain = parts[ 1 ];
				if ( domain.indexOf( '.' ) === -1 ) {
					valid = false;
				} else {
					const domainParts = domain.split( '.' );
					const ext = domainParts[ 1 ];
					if ( ext.length > 14 || ext.length < 2 ) {
						valid = false;
					}
				}
			}
			return valid;
		},

		_getCheckoutData() {
			const wcf_email = jQuery( '#billing_email' ).val();

			if ( typeof wcf_email === 'undefined' ) {
				return;
			}

			let wcf_phone = jQuery( '#billing_phone' ).val();
			const atposition = wcf_email.indexOf( '@' );
			const dotposition = wcf_email.lastIndexOf( '.' );

			if ( typeof wcf_phone === 'undefined' || wcf_phone === null ) {
				//If phone number field does not exist on the Checkout form
				wcf_phone = '';
			}

			clearTimeout( timer );

			if (
				! (
					atposition < 1 ||
					dotposition < atposition + 2 ||
					dotposition + 2 >= wcf_email.length
				) ||
				wcf_phone.length >= 1
			) {
				//Checking if the email field is valid or phone number is longer than 1 digit
				//If Email or Phone valid
				const wcf_name = jQuery( '#billing_first_name' ).val();
				const wcf_surname = jQuery( '#billing_last_name' ).val();
				wcf_phone = jQuery( '#billing_phone' ).val();
				const wcf_country = jQuery( '#billing_country' ).val();
				const wcf_city = jQuery( '#billing_city' ).val();

				//Other fields used for "Remember user input" function
				const wcf_billing_company = jQuery( '#billing_company' ).val();
				const wcf_billing_address_1 = jQuery(
					'#billing_address_1'
				).val();
				const wcf_billing_address_2 = jQuery(
					'#billing_address_2'
				).val();
				const wcf_billing_state = jQuery( '#billing_state' ).val();
				const wcf_billing_postcode = jQuery(
					'#billing_postcode'
				).val();
				const wcf_shipping_first_name = jQuery(
					'#shipping_first_name'
				).val();
				const wcf_shipping_last_name = jQuery(
					'#shipping_last_name'
				).val();
				const wcf_shipping_company = jQuery(
					'#shipping_company'
				).val();
				const wcf_shipping_country = jQuery(
					'#shipping_country'
				).val();
				const wcf_shipping_address_1 = jQuery(
					'#shipping_address_1'
				).val();
				const wcf_shipping_address_2 = jQuery(
					'#shipping_address_2'
				).val();
				const wcf_shipping_city = jQuery( '#shipping_city' ).val();
				const wcf_shipping_state = jQuery( '#shipping_state' ).val();
				const wcf_shipping_postcode = jQuery(
					'#shipping_postcode'
				).val();
				const wcf_order_comments = jQuery( '#order_comments' ).val();

				const data = {
					action: 'cartflows_save_cart_abandonment_data',
					wcf_email,
					wcf_name,
					wcf_surname,
					wcf_phone,
					wcf_country,
					wcf_city,
					wcf_billing_company,
					wcf_billing_address_1,
					wcf_billing_address_2,
					wcf_billing_state,
					wcf_billing_postcode,
					wcf_shipping_first_name,
					wcf_shipping_last_name,
					wcf_shipping_company,
					wcf_shipping_country,
					wcf_shipping_address_1,
					wcf_shipping_address_2,
					wcf_shipping_city,
					wcf_shipping_state,
					wcf_shipping_postcode,
					wcf_order_comments,
					security: wcf_ca_vars._nonce,
					wcf_post_id: wcf_ca_vars._post_id,
				};

				timer = setTimeout( function () {
					if (
						wcf_cart_abandonment._validate_email( data.wcf_email )
					) {
						jQuery.post(
							wcf_ca_vars.ajaxurl,
							data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
							function () {
								// success response
							}
						);
					}
				}, 500 );
			} else {
				//console.log("Not a valid e-mail or phone address");
			}
		},
	};

	wcf_cart_abandonment.init();
} )( jQuery );
