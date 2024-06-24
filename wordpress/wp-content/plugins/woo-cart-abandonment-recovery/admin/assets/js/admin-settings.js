( function ( $ ) {
	CartAbandonmentSettings = {
		init() {
			$( '#wcf_ca_custom_filter_from' )
				.datepicker( {
					dateFormat: 'yy-mm-dd',
					maxDate: '0',
					onClose( selectedDate ) {
						jQuery( '#wcf_ca_custom_filter_to' ).datepicker(
							'option',
							'minDate',
							selectedDate
						);
					},
				} )
				.attr( 'readonly', 'readonly' )
				.css( 'background', 'white' );

			$( '#wcf_ca_custom_filter_to' )
				.datepicker( {
					dateFormat: 'yy-mm-dd',
					maxDate: '0',
					onClose( selectedDate ) {
						jQuery( '#wcf_ca_custom_filter_from' ).datepicker(
							'option',
							'maxDate',
							selectedDate
						);
					},
				} )
				.attr( 'readonly', 'readonly' )
				.css( 'background', 'white' );

			$( '#wcf_ca_custom_filter' ).on( 'click', function () {
				const from = $( '#wcf_ca_custom_filter_from' ).val().trim();
				const to = $( '#wcf_ca_custom_filter_to' ).val().trim();
				let url = window.location.search;
				url =
					url +
					'&from_date=' +
					from +
					'&to_date=' +
					to +
					'&filter=custom';
				window.location.href = url;
			} );

			$( '#wcf_search_id_submit' ).on( 'click', function () {
				const search = $( '#wcf_search_id_search_input' ).val().trim();
				window.location.href =
					window.location.search + '&search_term=' + search;
			} );

			// Hide initially.
			$(
				'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry, #wcf_ca_zapier_cart_abandoned_webhook, #wcf_ca_coupon_code_status, #wcf_ca_gdpr_message'
			)
				.closest( 'tr' )
				.hide();

			if ( $( '#wcf_ca_gdpr_status:checked' ).length ) {
				$( '#wcf_ca_gdpr_message' ).closest( 'tr' ).show();
			}

			if ( $( '#wcf_ca_zapier_tracking_status:checked' ).length ) {
				$(
					'#wcf_ca_zapier_cart_abandoned_webhook, #wcf_ca_coupon_code_status'
				)
					.closest( 'tr' )
					.show();
			}

			if (
				$( '#wcf_ca_coupon_code_status:checked' ).length &&
				$( '#wcf_ca_zapier_tracking_status:checked' ).length
			) {
				$(
					'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry'
				)
					.closest( 'tr' )
					.show();
			}

			$( '#wcf_ca_coupon_code_status' ).on( 'click', function () {
				if ( ! $( '#wcf_ca_coupon_code_status:checked' ).length ) {
					$(
						'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry'
					)
						.closest( 'tr' )
						.fadeOut();
				} else {
					$(
						'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry'
					)
						.closest( 'tr' )
						.fadeIn();
				}
			} );

			$( '#wcf_ca_gdpr_status' ).on( 'click', function () {
				if ( ! $( '#wcf_ca_gdpr_status:checked' ).length ) {
					$( '#wcf_ca_gdpr_message' ).closest( 'tr' ).fadeOut();
				} else {
					$( '#wcf_ca_gdpr_message' ).closest( 'tr' ).fadeIn();
				}
			} );

			$( '#wcf_ca_zapier_tracking_status' ).on( 'click', function () {
				if ( ! $( '#wcf_ca_zapier_tracking_status:checked' ).length ) {
					$(
						'#wcf_ca_zapier_cart_abandoned_webhook, #wcf_ca_coupon_code_status'
					)
						.closest( 'tr' )
						.fadeOut();
				} else {
					$(
						'#wcf_ca_zapier_cart_abandoned_webhook, #wcf_ca_coupon_code_status'
					)
						.closest( 'tr' )
						.fadeIn();
				}

				if (
					$( '#wcf_ca_coupon_code_status:checked' ).length &&
					$( '#wcf_ca_zapier_tracking_status:checked' ).length
				) {
					$(
						'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry'
					)
						.closest( 'tr' )
						.fadeIn();
				} else {
					$(
						'#wcf_ca_discount_type, #wcf_ca_coupon_amount, #wcf_ca_coupon_expiry'
					)
						.closest( 'tr' )
						.fadeOut();
				}
			} );

			if (
				! $( '#wcf_ca_send_recovery_report_emails_to_admin:checked' )
					.length
			) {
				$( '#wcf_ca_admin_email' ).closest( 'tr' ).hide();
			}
			$( '#wcf_ca_send_recovery_report_emails_to_admin' ).on(
				'click',
				function () {
					if (
						! $(
							'#wcf_ca_send_recovery_report_emails_to_admin:checked'
						).length
					) {
						$( '#wcf_ca_admin_email' ).closest( 'tr' ).fadeOut();
					} else {
						$( '#wcf_ca_admin_email' ).closest( 'tr' ).fadeIn();
					}
				}
			);
		},
	};

	ZapierSettings = {
		init() {
			$( document ).on(
				'click',
				'#wcf_ca_trigger_web_hook_abandoned_btn',
				{ order_status: 'abandoned' },
				ZapierSettings.zapier_trigger_sample
			);
		},
		zapier_trigger_sample( event ) {
			const zapier_webhook_url = $(
				'#wcf_ca_zapier_cart_' + event.data.order_status + '_webhook'
			)
				.val()
				.trim();

			if ( ! zapier_webhook_url.length ) {
				$( '#wcf_ca_' + event.data.order_status + '_btn_message' )
					.text( wcf_ca_details.strings.verify_url_error )
					.fadeIn()
					.css( 'color', '#dc3232' )
					.delay( 2000 )
					.fadeOut();
				return;
			}

			$( '#wcf_ca_' + event.data.order_status + '_btn_message' )
				.text( wcf_ca_details.strings.trigger_process )
				.fadeIn();

			if ( $.trim( zapier_webhook_url ) !== '' ) {
				const sample_data = {
					first_name: wcf_ca_details.name,
					last_name: wcf_ca_details.surname,
					email: wcf_ca_details.email,
					phone: wcf_ca_details.phone,
					order_status: event.data.order_status,
					checkout_url:
						window.location.origin +
						'/checkout/?wcf_ac_token=something',
					coupon_code: 'abcgefgh',
					product_names: 'Product1, Product2 & Product3',
					cart_total: wcf_ca_details.woo_currency_symbol + '20',
					product_table:
						'<table align= left; cellpadding="10" cellspacing="0" style="float: none; border: 1px solid #e5e5e5;"> <tr align="center"> <th style="color: #636363; border: 1px solid #e5e5e5;">Item</th> <th style="color: #636363; border: 1px solid #e5e5e5;">Name</th> <th style="color: #636363; border: 1px solid #e5e5e5;">Quantity</th> <th style="color: #636363; border: 1px solid #e5e5e5;">Price</th> <th style="color: #636363; border: 1px solid #e5e5e5;">Line Subtotal</th> </tr> <tr style=color: #636363; border: 1px solid #e5e5e5; align="center"> <td style="color: #636363; border: 1px solid #e5e5e5;"><img class="demo_img" style="height: 42px; width: 42px;" src="#"></td> <td style="color: #636363; border: 1px solid #e5e5e5;">Product1</td> <td style="color: #636363; border: 1px solid #e5e5e5;"> 1 </td> <td style="color: #636363; border: 1px solid #e5e5e5;">&pound;85.00</td> <td style="color: #636363; border: 1px solid #e5e5e5;" >&pound;85.00</td> </tr> </table>',
				};
				$.ajax( {
					url: zapier_webhook_url,
					type: 'POST',
					data: sample_data,
					success( data ) {
						const response = ZapierSettings.handle_zapier_response(
							data
						);
						if ( response ) {
							$(
								'#wcf_ca_' +
									event.data.order_status +
									'_btn_message'
							)
								.text( wcf_ca_details.strings.trigger_success )
								.css( 'color', '#46b450' );
						} else {
							$(
								'#wcf_ca_' +
									event.data.order_status +
									'_btn_message'
							)
								.text( wcf_ca_details.strings.trigger_failed )
								.css( 'color', '#dc3232' );
						}
						$(
							'#wcf_ca_' +
								event.data.order_status +
								'_btn_message'
						)
							.fadeIn()
							.delay( 2000 )
							.fadeOut();
					},
					error() {
						$(
							'#wcf_ca_' +
								event.data.order_status +
								'_btn_message'
						)
							.text( wcf_ca_details.strings.trigger_failed )
							.css( 'color', '#dc3232' );
					},
				} );
			} else {
				$( 'wcf_ca' + event.data.order_status + '_btn_message' )
					.text( wcf_ca_details.strings.verify_url )
					.fadeIn()
					.delay( 2000 )
					.fadeOut();
			}
		},
		handle_zapier_response( data ) {
			let status = false;
			if (
				typeof data === 'object' &&
				[ 'success', 'accepted' ].includes( data.status )
			) {
				status = true;
			} else if ( typeof data === 'string' ) {
				const resp_string = data.toLowerCase();

				if ( [ 'success', 'accepted' ].includes( resp_string ) ) {
					status = true;
				}
			}

			return status;
		},
	};

	ToolTipHover = {
		init() {
			$( '.wcf-ca-report-table-row .wcf-ca-icon-row' ).on(
				'hover',
				function () {
					$( this )
						.find( '.wcf-ca-tooltip-text' )
						.toggleClass( 'display_tool_tip' );
				}
			);
		},
	};

	$( function () {
		CartAbandonmentSettings.init();
		ZapierSettings.init();
		ToolTipHover.init();
	} );
} )( jQuery );
