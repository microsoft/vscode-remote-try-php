( function ( $ ) {
	EmailTemplatesAdmin = {
		init() {
			$( document ).on(
				'click',
				'#wcf_preview_email',
				EmailTemplatesAdmin.send_test_email
			);
			$( document ).on(
				'click',
				'.wcf-ca-switch.wcf-toggle-template-status',
				EmailTemplatesAdmin.toggle_activate_template
			);
			$( document ).on(
				'click',
				'#wcf_ca_delete_coupons',
				EmailTemplatesAdmin.delete_coupons
			);
			$( document ).on(
				'click',
				'#wcf_ca_export_orders',
				EmailTemplatesAdmin.export_orders
			);
			$( document ).on(
				'click',
				'.wcar-switch-grid',
				EmailTemplatesAdmin.toggle_activate_template_on_grid
			);
			const coupon_child_fields =
				'#wcf_email_discount_type, #wcf_email_discount_amount, #wcf_email_coupon_expiry_date, #wcf_free_shipping_coupon, #wcf_auto_coupon_apply, #wcf_individual_use_only';
			$( coupon_child_fields )
				.closest( 'tr' )
				.toggle( $( '#wcf_override_global_coupon' ).is( ':checked' ) );
			$( document ).on(
				'click',
				'#wcf_override_global_coupon',
				function () {
					$( coupon_child_fields )
						.closest( 'tr' )
						.fadeToggle(
							$( '#wcf_override_global_coupon' ).is( ':checked' )
						);
				}
			);
		},

		send_test_email() {
			let email_body = '';
			if (
				jQuery( '#wp-wcf_email_body-wrap' ).hasClass( 'tmce-active' )
			) {
				email_body = tinyMCE.get( 'wcf_email_body' ).getContent();
			} else {
				email_body = jQuery( '#wcf_email_body' ).val();
			}

			const email_subject = $( '#wcf_email_subject' ).val();
			const email_send_to = $( '#wcf_send_test_email' ).val();
			const email_template_id = document.getElementsByName( 'id' )[ 0 ]
				.value;
			const wp_nonce = $( '#_wpnonce' ).val();

			$( this ).next( 'div.error' ).remove();

			if ( ! $.trim( email_body ) ) {
				$( this ).after(
					'<div class="error-message wcf-ca-error-msg"> Email body is required! </div>'
				);
			} else if ( ! $.trim( email_subject ) ) {
				$( this ).after(
					'<div class="error-message wcf-ca-error-msg"> Email subject is required! </div>'
				);
			} else if ( ! $.trim( email_send_to ) ) {
				$( this ).after(
					'<div class="error-message wcf-ca-error-msg"> You must add your email id! </div>'
				);
			} else {
				const data = {
					email_subject,
					email_body,
					email_send_to,
					email_template_id,
					action: 'wcf_ca_preview_email_send',
					security: wp_nonce,
				};
				$( '#wcf_preview_email' )
					.css( 'cursor', 'wait' )
					.attr( 'disabled', true );

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post( ajaxurl, data, function ( response ) {
					$( '#mail_response_msg' ).empty().fadeIn();

					if ( response.success ) {
						const success_string =
							'<strong> Email has been sent successfully! </strong>';
						$( '#mail_response_msg' )
							.css( 'color', 'green' )
							.html( success_string )
							.delay( 3000 )
							.fadeOut();
					} else {
						const error_string =
							'<strong> Email sending failed! Please check your SMTP settings!  </a></strong>';
						$( '#mail_response_msg' )
							.css( 'color', 'red' )
							.html( error_string )
							.delay( 3000 )
							.fadeOut();
					}
					$( '#wcf_preview_email' )
						.css( 'cursor', '' )
						.attr( 'disabled', false );
				} );
			}

			$( '.wcf-ca-error-msg' ).delay( 2000 ).fadeOut();
		},

		delete_coupons() {
			if ( confirm( wcf_ca_localized_vars._confirm_msg ) ) {
				const data = {
					action: 'wcf_ca_delete_garbage_coupons',
					security: wcf_ca_localized_vars._delete_coupon_nonce,
				};
				$( '.wcf-ca-spinner' ).show();

				$( '.wcf-ca-spinner' ).addClass( 'is-active' );
				$( '#wcf_ca_delete_coupons' )
					.css( 'cursor', 'wait' )
					.attr( 'disabled', true );
				$.post( ajaxurl, data, function ( response ) {
					$( '.wcf-ca-response-msg' ).empty().fadeIn();
					if ( response.success ) {
						$( '.wcf-ca-spinner' ).hide();
						$( '.wcf-ca-response-msg' )
							.css( 'color', 'green' )
							.html( response.data )
							.delay( 5000 )
							.fadeOut();
					}

					$( '#wcf_ca_delete_coupons' )
						.css( 'cursor', '' )
						.attr( 'disabled', false );
				} );
			}
		},
		export_orders() {
			if ( confirm( wcf_ca_localized_vars._confirm_msg_export ) ) {
				window.location.href =
					window.location.search +
					'&export_data=true&security=' +
					wcf_ca_localized_vars._export_orders_nonce;
			}
		},
		toggle_activate_template_on_grid() {
			let new_state;
			const $switch = $( this ),
				state = $switch.attr( 'wcf-ca-template-switch' ),
				css = state === 'on' ? 'green' : 'red';

			$.post(
				ajaxurl,
				{
					action: 'activate_email_templates',
					id: $( this ).attr( 'id' ),
					state,
					security: wcf_ca_details.email_toggle_button_nonce,
				},
				function ( response ) {
					$( '#wcf_activate_email_template' ).val(
						new_state === 'on' ? 1 : 0
					);

					$( '.wcar_tmpl_response_msg' ).remove();

					$(
						"<span class='wcar_tmpl_response_msg'> " +
							response.data +
							' </span>'
					)
						.insertAfter( $switch )
						.delay( 2000 )
						.fadeOut()
						.css( 'color', css );
				}
			);
		},

		toggle_activate_template() {
			const $switch = $( this ),
				state = $switch.attr( 'wcf-ca-template-switch' );
			const new_state = state === 'on' ? 'off' : 'on';
			$( '#wcf_activate_email_template' ).val(
				new_state === 'on' ? 1 : 0
			);
			$switch.attr( 'wcf-ca-template-switch', new_state );
		},
	};

	$( function () {
		EmailTemplatesAdmin.init();
	} );
} )( jQuery );
