( function ( $ ) {
	/**
	 * Admin
	 *
	 * @since x.x.x
	 */
	StarterTemplatesAdmin = {
		/**
		 * Initializes Events.
		 *
		 * @since x.x.x
		 * @method init
		 */
		init: function () {
			this._bind();
		},

		/**
		 * Binds events for the BSF Quick Links
		 *
		 * @since x.x.x
		 * @access private
		 * @method _bind
		 */
		_bind: function () {
			$( window ).on(
				'scroll',
				StarterTemplatesAdmin._addCustomCTAInfobar
			);
			$( document ).on(
				'astra-sites-change-page-builder',
				StarterTemplatesAdmin._changeCTALink
			);
		},

		_changeCTALink: function ( event ) {
			if ( AstraSitesAdmin.default_cta_link ) {
				$( '.astra-sites-cta-link' ).attr(
					'href',
					AstraSitesAdmin.default_cta_link
				);
			}

			if ( AstraSitesAdmin.quick_corner_cta_link ) {
				$( '.bsf-quick-link-item-upgrade' ).attr(
					'href',
					AstraSitesAdmin.quick_corner_cta_link
				);
			}
		},

		/**
		 * Show Custom CTA on scroll.
		 */
		_addCustomCTAInfobar: function () {
			var scroll = $( window ).scrollTop();

			if ( scroll > 70 ) {
				$( '.astra-sites-custom-cta-wrap' ).addClass( 'show' );
			} else {
				$( '.astra-sites-custom-cta-wrap' ).removeClass( 'show' );
			}
		},
	};

	$( function () {
		StarterTemplatesAdmin.init();
	} );
} )( jQuery );
