( function ( $ ) {
	DisableAdminNotices = {
		init() {
			$( document ).on(
				'click',
				'.weekly-report-email-notice.wcar-dismissible-notice .notice-dismiss',
				DisableAdminNotices.disable_weekly_report_email_admin_notice
			);
		},

		disable_weekly_report_email_admin_notice( event ) {
			event.preventDefault();
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wcar_disable_weekly_report_email_notice',
					security:
						wcf_ca_notices_vars.weekly_report_email_notice_nonce,
				},
			} )
				.done( function () {} )
				.fail( function () {} )
				.always( function () {} );
		},
	};

	$( function () {
		DisableAdminNotices.init();
	} );
} )( jQuery );
