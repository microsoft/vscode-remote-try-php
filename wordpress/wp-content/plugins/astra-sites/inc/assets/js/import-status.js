( function ( $ ) {
	AstraSitesImportStatus = {
		timer: null,
		ajax_in_process: false,
		current_step: null,
		interval: $( '.astra-sites-import-screen' ).length ? 1000 : 10000,

		/**
		 * Init
		 */
		init: function () {
			this.start();
		},

		/**
		 * Start
		 */
		start: function () {
			AstraSitesImportStatus.timer = setInterval(
				AstraSitesImportStatus.check_status,
				AstraSitesImportStatus.interval
			);
		},

		/**
		 * Check Status
		 */
		check_status: function () {
			if ( false === AstraSitesImportStatus.ajax_in_process ) {
				AstraSitesImportStatus.ajax_in_process = true;
				AstraSitesImportStatus._ajax_request();
			}
		},

		/**
		 * Ajax Request
		 */
		_ajax_request: function () {
			$.ajax( {
				url: AstraSitesImportStatusVars.ajaxurl,
				type: 'POST',
				data: {
					action: 'astra_sites_check_import_status',
					_ajax_nonce: AstraSitesImportStatusVars._ajax_nonce,
				},
			} )
				.done( function ( result ) {
					AstraSitesImportStatus.ajax_in_process = false;

					// Admin Bar UI markup.
					if (
						'complete' === result.data.response.step ||
						'fail' === result.data.response.step
					) {
						AstraSitesImportStatus.stop();

						var response_message =
							'<span class="dashicons dashicons-no-alt"></span> Site Import Failed';
						if ( 'complete' === result.data.response.step ) {
							response_message =
								'<span class="dashicons dashicons-yes"></span>' +
								response_message;
						}

						$( '#astra-sites-import-status-admin-bar' ).html(
							response_message
						);
					} else {
						$( '#astra-sites-import-status-admin-bar' ).html(
							'<span class="loading"></span>' +
								result.data.response.message
						);
					}

					// Admin page UI markup.
					var currentStep = $(
						'.import-step[data-step="' +
							result.data.response.step +
							'"]'
					);
					if ( currentStep.length ) {
						if (
							'complete' === result.data.response.step ||
							'fail' === result.data.response.step
						) {
							$( '.import-step' )
								.removeClass( 'processing' )
								.addClass( 'success' );
						} else if (
							AstraSitesImportStatus.current_step !==
							result.data.response.step
						) {
							AstraSitesImportStatus.current_step =
								result.data.response.step;

							currentStep
								.prevAll()
								.removeClass( 'processing' )
								.addClass( 'success' );
							currentStep.addClass( 'processing' );
						}
					}
				} )
				.fail( function ( err ) {
					AstraSitesImportStatus.ajax_in_process = false;

					// Stop.
					AstraSitesImportStatus.stop();
				} );
		},

		/**
		 * Step
		 */
		stop: function () {
			clearInterval( AstraSitesImportStatus.timer );
		},
	};

	/**
	 * Initialize AstraSitesImportStatus
	 */
	$( function () {
		AstraSitesImportStatus.init();
	} );
} )( jQuery );
