/**
 * AJAX Request Queue
 *
 * - add()
 * - remove()
 * - run()
 * - stop()
 *
 * @since 2.0.0
 */

const AstBlockTemplatesAjaxQueue = ( function () {
	let requests = [];

	return {
		/**
		 * Add AJAX request
		 *
		 * @param {string} opt selected opt.
		 * @since 2.0.0
		 */
		add( opt ) {
			requests.push( opt );
		},

		/**
		 * Remove AJAX request
		 *
		 * @param {string} opt selected opt.
		 * @since 2.0.0
		 */
		remove( opt ) {
			if ( jQuery.inArray( opt, requests ) > -1 ) {
				requests.splice( jQuery.inArray( opt, requests ), 1 );
			}
		},

		/**
		 * Run / Process AJAX request
		 *
		 * @since 2.0.0
		 */
		run() {
			const self = this;
			let oriSuc;

			if ( requests.length ) {
				oriSuc = requests[ 0 ].complete;

				requests[ 0 ].complete = function () {
					if ( typeof oriSuc === 'function' ) {
						oriSuc();
					}
					requests.shift();
					self.run.apply( self, [] );
				};

				jQuery.ajax( requests[ 0 ] );
			} else {
				self.tid = setTimeout( function () {
					self.run.apply( self, [] );
				}, 1000 );
			}
		},

		/**
		 * Stop AJAX request
		 *
		 * @since 2.0.0
		 */
		stop() {
			requests = [];
			clearTimeout( this.tid );
		},
	};
}() );

( function ( $ ) {
	const AstBlockTemplates = {
		remaining_install_plugins: 0,
		remaining_active_plugins: 0,

		init() {
			this._bind();
		},

		/**
		 * Bind
		 */
		_bind() {
			//Page builder installation & save option
			$( document )
				// .on(
				// 	'click',
				// 	'.install-required-plugins',
				// 	AstBlockTemplates._install_required_plugins
				// )
				.on( 'wp-plugin-installing', AstBlockTemplates._pluginInstalling )
				.on( 'wp-plugin-install-error', AstBlockTemplates._installError )
				.on(
					'wp-plugin-install-success',
					AstBlockTemplates._installSuccess
				);
		},

		_installPlugin( plugin_slug ) {
			if (
				wp.updates.shouldRequestFilesystemCredentials &&
				! wp.updates.ajaxLocked
			) {
				wp.updates.requestFilesystemCredentials( event );

				$document.on( 'credential-modal-cancel', function () {
					const $message = $( '.install-now.updating-message' );

					$message
						.removeClass( 'updating-message' )
						.text( wp.updates.l10n.installNow );

					wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
				} );
			}

			wp.updates.queue.push( {
				action: 'install-plugin', // Required action.
				data: {
					slug: plugin_slug,
				},
			} );

			// Required to set queue.
			wp.updates.queueChecker();
		},

		_activatePlugin( plugin_init, plugin_slug ) {
			$.ajax( {
				url: ajaxurl,
				method: 'POST',
				data: {
					action: '',
					plugin_slug,
					plugin_init,
					security: '',
				},
			} )
				.done( function ( response ) {
					if ( response.success ) {
						console.log( plugin_slug + ' activated' );
						// trigger_event();
					} else {
						console.log(
							'Error: ' + response.data && response.data.message
								? response.data.message
								: 'Plugin not activated'
						);
					}
				} )
				.fail( function () {
					console.log( 'activation error' );
				} );
		},

		/**
		 * Installing Plugin
		 *
		 * @param {Object} event event data.
		 */
		_pluginInstalling( event ) {
			event.preventDefault();
			console.log( 'Installing..' );
		},

		/**
		 * Install Error
		 *
		 * @param {Object} event event data.
		 */
		_installError( event ) {
			event.preventDefault();
			console.log( 'Install Error!' );
		},

		/**
		 * Install Success
		 *
		 * @param {Object} event event data.
		 * @param {Array}  args  args data.
		 */
		_installSuccess( event, args ) {
			event.preventDefault();
			const plugin_init = args.slug + '/' + args.slug + '.php';
			const plugin_slug = args.slug;

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function () {
				AstBlockTemplates._activatePlugin( plugin_init, plugin_slug );
			}, 1500 );
		},
	};

	function trigger_event() {
		const custom_event = new Event( 'ast-plugins-install-success' );
		document.dispatchEvent( custom_event );
	}

	$( function () {
		AstBlockTemplates.init();
	} );
}( jQuery ) );
