( function ( $ ) {
	AstraSitesInstallTheme = {
		/**
		 * Init
		 */
		init() {
			this._auto_close_notice();
			this._bind();
		},

		/**
		 * Binds events for the Astra Sites.
		 *
		 * @since 1.3.2
		 *
		 * @access private
		 * @function _bind
		 */
		_bind() {
			$( document ).on(
				'click',
				'.astra-sites-theme-not-installed',
				AstraSitesInstallTheme._install_and_activate
			);
			$( document ).on(
				'click',
				'.astra-sites-theme-installed-but-inactive',
				AstraSitesInstallTheme._activateTheme
			);
			$( document ).on(
				'wp-theme-install-success',
				AstraSitesInstallTheme._activateTheme
			);
		},

		/**
		 * Close Getting Started Notice
		 *
		 * @param {Object} event
		 */
		_auto_close_notice() {
			if ( $( '.astra-sites-getting-started-btn' ).length ) {
				$.ajax( {
					url: aiBuilderVars.ajax_url,
					type: 'POST',
					data: {
						action: 'astra-sites-getting-started-notice',
						_ajax_nonce: aiBuilderVars._ajax_nonce,
					},
				} ).done( function () {} );
			}
		},

		/**
		 * Activate Theme
		 *
		 * @param  event
		 * @param  response
		 * @since 1.3.2
		 */
		_activateTheme( event, response ) {
			event.preventDefault();

			$( '#astra-theme-activation-nag a' ).addClass( 'processing' );

			if ( response ) {
				$( '#astra-theme-activation-nag a' ).text(
					aiBuilderVars.installed
				);
			} else {
				$( '#astra-theme-activation-nag a' ).text(
					aiBuilderVars.activating
				);
			}

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function () {
				$.ajax( {
					url: aiBuilderVars.ajax_url,
					type: 'POST',
					data: {
						action: 'astra-sites-activate_theme',
						_ajax_nonce: aiBuilderVars._ajax_nonce,
					},
				} ).done( function ( result ) {
					if ( result.success ) {
						$( '.astra-sites-theme-action-link' )
							.parent()
							.html( aiBuilderVars.activated + ' 🎉' );
					}
				} );
			}, 3000 );
		},

		/**
		 * Install and activate
		 *
		 * @since 1.3.2
		 *
		 * @param {Object} event Current event.
		 */
		_install_and_activate( event ) {
			event.preventDefault();
			const theme_slug = $( this ).data( 'theme-slug' ) || '';
			const btn = $( event.target );

			if ( btn.hasClass( 'processing' ) ) {
				return;
			}

			btn.text( aiBuilderVars.installing ).addClass( 'processing' );

			if (
				wp.updates.shouldRequestFilesystemCredentials &&
				! wp.updates.ajaxLocked
			) {
				wp.updates.requestFilesystemCredentials( event );
			}

			wp.updates.installTheme( {
				slug: theme_slug,
			} );
		},
	};

	/**
	 * Initialize
	 */
	$( function () {
		AstraSitesInstallTheme.init();
	} );
} )( jQuery );
