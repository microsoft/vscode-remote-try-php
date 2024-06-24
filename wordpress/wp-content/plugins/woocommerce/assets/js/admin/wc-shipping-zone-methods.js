/* global shippingZoneMethodsLocalizeScript, ajaxurl */
( function( $, data, wp, ajaxurl ) {
	$( function() {
		var $table          = $( '.wc-shipping-zone-methods' ),
			$tbody          = $( '.wc-shipping-zone-method-rows' ),
			$save_button    = $( '.wc-shipping-zone-method-save' ),
			$row_template   = wp.template( 'wc-shipping-zone-method-row' ),
			$blank_template = wp.template( 'wc-shipping-zone-method-row-blank' ),

			// Backbone model
			ShippingMethod       = Backbone.Model.extend({
				changes: {},
				logChanges: function( changedRows ) {
					var changes = this.changes || {};

					_.each( changedRows.methods, function( row, id ) {
						changes.methods = changes.methods || { methods : {} };
						changes.methods[ id ] = _.extend( changes.methods[ id ] || { instance_id : id }, row );
					} );

					if ( typeof changedRows.zone_name !== 'undefined' ) {
						changes.zone_name = changedRows.zone_name;
					}

					if ( typeof changedRows.zone_locations !== 'undefined' ) {
						changes.zone_locations = changedRows.zone_locations;
					}

					if ( typeof changedRows.zone_postcodes !== 'undefined' ) {
						changes.zone_postcodes = changedRows.zone_postcodes;
					}

					this.changes = changes;
					this.trigger( 'change:methods' );
				},
				save: function() {
					// Special handling for an empty 'zone_locations' array, which jQuery filters out during $.post().
					var changes = _.clone( this.changes );
					if ( _.has( changes, 'zone_locations' ) && _.isEmpty( changes.zone_locations ) ) {
						changes.zone_locations = [''];
					}

					$.post(
						ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=woocommerce_shipping_zone_methods_save_changes',
						{
							wc_shipping_zones_nonce : data.wc_shipping_zones_nonce,
							changes                 : changes,
							zone_id                 : data.zone_id
						},
						this.onSaveResponse,
						'json'
					);
				},
				onSaveResponse: function( response, textStatus ) {
					if ( 'success' === textStatus ) {
						if ( response.success ) {
							if ( response.data.zone_id !== data.zone_id ) {
								data.zone_id = response.data.zone_id;
								if ( window.history.pushState ) {
									window.history.pushState(
										{},
										'',
										'admin.php?page=wc-settings&tab=shipping&zone_id=' + response.data.zone_id
									);
								}
							}
							shippingMethod.set( 'methods', response.data.methods );
							shippingMethod.trigger( 'change:methods' );
							shippingMethod.changes = {};
							shippingMethod.trigger( 'saved:methods' );

							// Overrides the onbeforeunload callback added by settings.js.
							window.onbeforeunload = null;
						} else {
							window.alert( data.strings.save_failed );
						}
					}
				}
			} ),

			// Backbone view
			ShippingMethodView = Backbone.View.extend({
				rowTemplate: $row_template,
				initialize: function() {
					this.listenTo( this.model, 'change:methods', this.setUnloadConfirmation );
					this.listenTo( this.model, 'saved:methods', this.clearUnloadConfirmation );
					this.listenTo( this.model, 'saved:methods', this.render );
					this.listenTo( this.model, 'rerender', this.render );
					$tbody.on( 'change', { view: this }, this.updateModelOnChange );
					$tbody.on( 'sortupdate', { view: this }, this.updateModelOnSort );
					$( window ).on( 'beforeunload', { view: this }, this.unloadConfirmation );
					$save_button.on( 'click', { view: this }, this.onSubmit );

					$( document.body ).on(
						'input change',
						'#zone_name, #zone_locations, #zone_postcodes',
						{ view: this },
						this.onUpdateZone
					);
					$( document.body ).on( 'click', '.wc-shipping-zone-method-settings', { view: this }, this.onConfigureShippingMethod );
					$( document.body ).on( 'click', '.wc-shipping-zone-add-method', { view: this }, this.onAddShippingMethod );
					$( document.body ).on( 'wc_backbone_modal_response', this.onConfigureShippingMethodSubmitted );
					$( document.body ).on( 'wc_region_picker_update', this.onUpdateZoneRegionPicker );
					$( document.body ).on( 'wc_backbone_modal_next_response', this.onAddShippingMethodSubmitted );
					$( document.body ).on( 'wc_backbone_modal_before_remove', this.onCloseConfigureShippingMethod );
					$( document.body ).on( 'wc_backbone_modal_back_response', this.onConfigureShippingMethodBack );
					$( document.body ).on( 'change', '.wc-shipping-zone-method-selector select', this.onChangeShippingMethodSelector );
					$( document.body ).on( 'click', '.wc-shipping-zone-postcodes-toggle', this.onTogglePostcodes );
					$( document.body ).on( 'wc_backbone_modal_validation', { view: this }, this.validateFormArguments );
					$( document.body ).on( 'wc_backbone_modal_loaded', { view: this }, this.onModalLoaded );
				},
				onUpdateZoneRegionPicker: function( event ) {
					var value = event.detail,
						attribute = 'zone_locations',
						changes   = {};

						changes[ attribute ] = value;
						shippingMethodView.model.set( attribute, value );
						shippingMethodView.model.logChanges( changes );
				},
				onUpdateZone: function( event ) {
					var view      = event.data.view,
						model     = view.model,
						value     = $( this ).val(),
						$target   = $( event.target ),
						attribute = $target.data( 'attribute' ),
						changes   = {};

					event.preventDefault();

					changes[ attribute ] = value;
					model.set( attribute, value );
					model.logChanges( changes );
					view.render();
				},
				block: function() {
					$( this.el ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				unblock: function() {
					$( this.el ).unblock();
				},
				render: function() {
					var methods     = _.indexBy( this.model.get( 'methods' ), 'instance_id' ),
						zone_name   = this.model.get( 'zone_name' ),
						view        = this;

					// Set name.
					$('.wc-shipping-zone-name').text( zone_name ? zone_name : data.strings.default_zone_name );

					// Blank out the contents.
					this.$el.empty();
					this.unblock();

					if ( _.size( methods ) ) {
						// Sort methods
						methods = _.sortBy( methods, function( method ) {
							return parseInt( method.method_order, 10 );
						} );

						// Populate $tbody with the current methods
						$.each( methods, function( id, rowData ) {
							if ( 'yes' === rowData.enabled ) {
								rowData.enabled_icon = '<span class="woocommerce-input-toggle woocommerce-input-toggle--enabled">' +
									data.strings.yes +
									'</span>';
							} else {
								rowData.enabled_icon = '<span class="woocommerce-input-toggle woocommerce-input-toggle--disabled">' +
									data.strings.no +
									'</span>';
							}

							view.$el.append( view.rowTemplate( rowData ) );

							var $tr = view.$el.find( 'tr[data-id="' + rowData.instance_id + '"]');

							if ( ! rowData.has_settings ) {
								$tr
									.find( '.wc-shipping-zone-method-title > a' )
									.replaceWith('<span>' + $tr.find( '.wc-shipping-zone-method-title > a' ).text() + '</span>' );
								var $del = $tr.find( '.wc-shipping-zone-method-delete' );
								$tr.find( '.wc-shipping-zone-method-title .row-actions' ).empty().html($del);
							}
						} );

						// Make the rows function
						this.$el.find( '.wc-shipping-zone-method-delete' ).on( 'click', { view: this }, this.onDeleteRow );
						this.$el.find( '.wc-shipping-zone-method-enabled a').on( 'click', { view: this }, this.onToggleEnabled );
					} else {
						view.$el.append( $blank_template );
					}

					this.initTooltips();
				},
				initTooltips: function() {
					$( '#tiptip_holder' ).removeAttr( 'style' );
					$( '#tiptip_arrow' ).removeAttr( 'style' );
					$( '.tips' ).tipTip({ 'attribute': 'data-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 50 });
				},
				onSubmit: function( event ) {
					$save_button.addClass( 'is-busy' );
					event.data.view.block();
					event.data.view.model.save();
					event.preventDefault();
				},
				onDeleteRow: function( event ) {
					var view    = event.data.view,
						model   = view.model,
						methods   = _.indexBy( model.get( 'methods' ), 'instance_id' ),
						changes = {},
						instance_id = $( this ).closest( 'tr' ).data( 'id' );

					event.preventDefault();

					if ( window.confirm( data.strings.delete_shipping_method_confirmation ) ) {
						shippingMethodView.block();

						// Add method to zone via ajax call
						$.post( {
							url: ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?') + 'action=woocommerce_shipping_zone_remove_method',
							data: {
								wc_shipping_zones_nonce: data.wc_shipping_zones_nonce,
								instance_id: instance_id,
								zone_id: data.zone_id,
							},
							success: function( { data } ) {
								delete methods[instance_id];
								changes.methods = changes.methods || data.methods;
								model.set('methods', methods);
								model.logChanges( changes );
								view.clearUnloadConfirmation();
								view.render();
								shippingMethodView.unblock();
							},
							error: function( jqXHR, textStatus, errorThrown ) {
								window.alert( data.strings.remove_method_failed );
								shippingMethodView.unblock();
							},
							dataType: 'json'
						});
					}
				},
				onToggleEnabled: function( event ) {
					var view        = event.data.view,
						$target     = $( event.target ),
						model       = view.model,
						methods     = _.indexBy( model.get( 'methods' ), 'instance_id' ),
						instance_id = $target.closest( 'tr' ).data( 'id' ),
						enabled     = $target.closest( 'tr' ).data( 'enabled' ) === 'yes' ? 'no' : 'yes',
						changes     = {};

					event.preventDefault();
					methods[ instance_id ].enabled = enabled;
					changes.methods = changes.methods || { methods : {} };
					changes.methods[ instance_id ] = _.extend( changes.methods[ instance_id ] || {}, { enabled : enabled } );
					model.set( 'methods', methods );
					model.logChanges( changes );
					view.render();
				},
				setUnloadConfirmation: function() {
					this.needsUnloadConfirm = true;
					$save_button.prop( 'disabled', false );
					$save_button.removeClass( 'is-busy' );
				},
				clearUnloadConfirmation: function() {
					this.needsUnloadConfirm = false;
					$save_button.attr( 'disabled', 'disabled' );
				},
				unloadConfirmation: function( event ) {
					if ( event.data.view.needsUnloadConfirm ) {
						event.returnValue = data.strings.unload_confirmation_msg;
						window.event.returnValue = data.strings.unload_confirmation_msg;
						return data.strings.unload_confirmation_msg;
					}
				},
				updateModelOnChange: function( event ) {
					var model     = event.data.view.model,
						$target   = $( event.target ),
						instance_id   = $target.closest( 'tr' ).data( 'id' ),
						attribute = $target.data( 'attribute' ),
						value     = $target.val(),
						methods   = _.indexBy( model.get( 'methods' ), 'instance_id' ),
						changes = {};

					if ( methods[ instance_id ][ attribute ] !== value ) {
						changes.methods[ instance_id ] = {};
						changes.methods[ instance_id ][ attribute ] = value;
						methods[ instance_id ][ attribute ]   = value;
					}

					model.logChanges( changes );
				},
				updateModelOnSort: function( event ) {
					var view         = event.data.view,
						model        = view.model,
						methods        = _.indexBy( model.get( 'methods' ), 'instance_id' ),
						changes      = {};

					_.each( methods, function( method ) {
						var old_position = parseInt( method.method_order, 10 );
						var new_position = parseInt( $table.find( 'tr[data-id="' + method.instance_id + '"]').index() + 1, 10 );

						if ( old_position !== new_position ) {
							methods[ method.instance_id ].method_order = new_position;
							changes.methods = changes.methods || { methods : {} };
							changes.methods[ method.instance_id ] = _.extend(
								changes.methods[ method.instance_id ] || {}, { method_order : new_position }
							);
						}
					} );

					if ( _.size( changes ) ) {
						model.logChanges( changes );
					}
				},
				onConfigureShippingMethod: function( event ) {
					var instance_id = $( this ).closest( 'tr' ).data( 'id' ),
						model       = event.data.view.model,
						methods     = _.indexBy( model.get( 'methods' ), 'instance_id' ),
						method      = methods[ instance_id ];

					// Only load modal if supported.
					if ( ! method.settings_html ) {
						return true;
					}

					event.preventDefault();

					method.settings_html = shippingMethodView.reformatSettingsHTML( method.settings_html );

					$( this ).WCBackboneModal({
						template : 'wc-modal-shipping-method-settings',
						variable : {
							instance_id : instance_id,
							method      : method,
							status	    : 'existing'
						},
						data : {
							instance_id : instance_id,
							method      : method,
							status	    : 'existing'
						}
					});

					shippingMethodView.highlightOnFocus( '.wc-shipping-modal-price' );

					$( document.body ).trigger( 'init_tooltips' );
				},
				onConfigureShippingMethodSubmitted: function( event, target, posted_data ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						shippingMethodView.block();

						// Save method settings via ajax call
						$.post(
							ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=woocommerce_shipping_zone_methods_save_settings',
							{
								wc_shipping_zones_nonce : data.wc_shipping_zones_nonce,
								instance_id             : posted_data.instance_id,
								data                    : posted_data
							},
							function( response, textStatus ) {
								if ( 'success' === textStatus && response.success ) {
									$( 'table.wc-shipping-zone-methods' ).parent().find( '#woocommerce_errors' ).remove();

									// If there were errors, prepend the form.
									if ( response.data.errors.length > 0 ) {
										shippingMethodView.showErrors( response.data.errors );
									}

									// Method was saved. Re-render.
									if ( _.size( shippingMethodView.model.changes ) ) {
										shippingMethodView.model.save();
									} else {
										shippingMethodView.model.onSaveResponse( response, textStatus );
									}
								} else {
									window.alert( data.strings.save_failed );
									shippingMethodView.unblock();
								}
							},
							'json'
						);
					}
				},
				onConfigureShippingMethodBack: function( event, target ) {
					if ( 'wc-modal-shipping-method-settings' === target ) {
						shippingMethodView.onAddShippingMethod( event );
					}
				},
				showErrors: function( errors ) {
					var error_html = '<div id="woocommerce_errors" class="error notice is-dismissible">';

					$( errors ).each( function( index, value ) {
						error_html = error_html + '<p>' + value + '</p>';
					} );
					error_html = error_html + '</div>';

					$( 'table.wc-shipping-zone-methods' ).before( error_html );
				},
				highlightOnFocus: function( query ) {
					const inputs = $( query );
					inputs.focus( function() {
						$( this ).select();
					} );
				},
				onAddShippingMethod: function( event ) {
					event.preventDefault();

					$( this ).WCBackboneModal({
						template : 'wc-modal-add-shipping-method',
						variable : {
							zone_id : data.zone_id
						}
					});

					$( '.wc-shipping-zone-method-selector select' ).trigger( 'change' );

					$('.wc-shipping-zone-method-input input').change( function() {
						const selected = $('.wc-shipping-zone-method-input input:checked');
						const id = selected.attr( 'id' );
						const description = $( `#${ id }-description` );
						const descriptions = $( '.wc-shipping-zone-method-input-help-text' );
						descriptions.css( 'display', 'none' );
						description.css( 'display', 'block' );
					});
				},
				/**
				 * The settings HTML is controlled and built by the settings api, so in order to refactor the 
				 * markup, it needs to be manipulated here.
				 */
				reformatSettingsHTML: function( html ) {
					const formattingFunctions = [
						this.replaceHTMLTables,
						this.moveAdvancedCostsHelpTip,
						this.moveHTMLHelpTips,
						this.addCurrencySymbol
					];

					return formattingFunctions.reduce( ( formattedHTML, fn ) => {
						return fn( formattedHTML );
					}, html );
				},
				moveAdvancedCostsHelpTip: function( html ) {
					const htmlContent = $( html );
					const advancedCostsHelpTip = htmlContent.find( '#wc-shipping-advanced-costs-help-text' );
					advancedCostsHelpTip.addClass( 'wc-shipping-zone-method-fields-help-text' );

					const input = htmlContent.find( '#woocommerce_flat_rate_cost' );
					const fieldset = input.closest( 'fieldset' );
					advancedCostsHelpTip.appendTo( fieldset );

					return htmlContent.prop( 'outerHTML' );
				},
				addCurrencySymbol: function( html ) {
					if ( ! window.wc.ShippingCurrencyContext || ! window.wc.ShippingCurrencyNumberFormat ) {
						return html;
					}
					const htmlContent = $( html );
					const priceInputs = htmlContent.find( '.wc-shipping-modal-price' );
					const config = window.wc.ShippingCurrencyContext.getCurrencyConfig();
					const { symbol, symbolPosition } = config;

					priceInputs.addClass( `wc-shipping-currency-size-${ symbol.length }` );
					priceInputs.addClass( `wc-shipping-currency-position-${ symbolPosition }` );
					priceInputs.before(
						`<div class="wc-shipping-zone-method-currency wc-shipping-currency-position-${ symbolPosition }">${ symbol }</div>`
					);

					priceInputs.each( ( i ) => {
						const priceInput = $( priceInputs[ i ] );
						const value = priceInput.attr( 'value' );
						const formattedValue = window.wc.ShippingCurrencyNumberFormat( config, value );
						priceInput.attr( 'value', formattedValue );
					} );

					return htmlContent.prop( 'outerHTML' );
				},
				moveHTMLHelpTips: function( html ) {
					// These help tips aren't moved.
					const helpTipsToRetain = [
						'woocommerce_flat_rate_cost',
						'woocommerce_flat_rate_no_class_cost',
						'woocommerce_flat_rate_class_cost_'
					];

					const htmlContent = $( html );
					const labels = htmlContent.find( 'label' );
					labels.each( ( i ) => {
						const label = $( labels[ i ] );
						const helpTip = label.find( '.woocommerce-help-tip' );

						if ( helpTip.length === 0 ) {
							return;
						}

						const id = label.attr( 'for' );

						if ( helpTipsToRetain.some( ( tip ) => id.includes( tip ) ) ) {
							const helpTip = htmlContent.find( `label[for=${ id }] span.woocommerce-help-tip` );
							helpTip.addClass( 'wc-shipping-visible-help-text' );
							return;
						}

						// woocommerce_free_shipping_ignore_discounts gets a helpTip appended to its label.
						// Otherwise, add the text as the last element in the fieldset.
						if ( id === 'woocommerce_free_shipping_ignore_discounts' ) {
							const input = htmlContent.find( `#${ id }` );
							const fieldset = input.closest( 'fieldset' );
							const inputLabel = fieldset.find( 'label' );
							inputLabel.append( helpTip );
						} else {
							const text = helpTip.data( 'tip' );
							const input = htmlContent.find( `#${ id }` );
							const fieldset = input.closest( 'fieldset' );

							if ( fieldset.length && fieldset.find( '.wc-shipping-zone-method-fields-help-text' ).length === 0 ) {
								fieldset.append( `<div class="wc-shipping-zone-method-fields-help-text">${ text }</div>` );
							}
						}

						// Coupon discounts doesn't get a title on Free Shipping.
						if ( label.text().trim() === 'Coupons discounts' ) {
							label.text( '' );
						}

					} );

					return htmlContent.prop( 'outerHTML' );
				},
				replaceHTMLTables: function ( html ) {
					// Wrap the html content in a div
					const htmlContent = $( '<div>' + html + '</div>' );

					// `<table class="form-table" />` elements added by the Settings API need to be removed. 
					// Modern browsers won't interpret other table elements like `td` not in a `table`, so 
					// Removing the `table` is sufficient.
					const innerTables = htmlContent.find( 'table.form-table' );
					innerTables.each( ( i ) => {
						const table = $( innerTables[ i ] );
						const div = $( '<div class="wc-shipping-zone-method-fields" />' );
						div.html( table.html() );
						table.replaceWith( div );
					} );

					return htmlContent.prop('outerHTML');
				},
				onAddShippingMethodSubmitted: function( event, target, posted_data, closeModal ) {
					if ( 'wc-modal-add-shipping-method' === target ) {
						shippingMethodView.block();

						$('#btn-next').addClass( 'is-busy' );

						// Add method to zone via ajax call
						$.post( ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=woocommerce_shipping_zone_add_method', {
							wc_shipping_zones_nonce : data.wc_shipping_zones_nonce,
							method_id               : posted_data.add_method_id,
							zone_id                 : data.zone_id
						}, function( response, textStatus ) {
							if ( 'success' === textStatus && response.success ) {
								if ( response.data.zone_id !== data.zone_id ) {
									data.zone_id = response.data.zone_id;
									if ( window.history.pushState ) {
										window.history.pushState(
											{},
											'',
											'admin.php?page=wc-settings&tab=shipping&zone_id=' + response.data.zone_id
										);
									}
								}

								// Avoid triggering a rerender here because we don't want to show the method
								// in the table in case merchant doesn't finish flow.
								
								shippingMethodView.model.set( 'methods', response.data.methods );

								// Close original modal
								closeModal();
							}
							var instance_id = response.data.instance_id, 
							    method      = response.data.methods[ instance_id ];

							shippingMethodView.unblock();

							if ( method.settings_html ) {
								method.settings_html = shippingMethodView.reformatSettingsHTML( method.settings_html );

								// Pop up next modal
								$( this ).WCBackboneModal({
									template : 'wc-modal-shipping-method-settings',
									variable : {
										instance_id : instance_id,
										method      : method,
										status	    : 'new'
									},
									data : {
										instance_id : instance_id,
										method      : method,
										status	    : 'new'
									}
								});

								shippingMethodView.highlightOnFocus( '.wc-shipping-modal-price' );
							} else {
								shippingMethodView.model.trigger( 'change:methods' );
								shippingMethodView.model.trigger( 'saved:methods' );
							}
		
							$( document.body ).trigger( 'init_tooltips' );
						}, 'json' );
					}
				},
				// Free Shipping has hidden field elements depending on data values.
				possiblyHideFreeShippingRequirements: function( data ) {
					if ( Object.keys( data ).includes( 'woocommerce_free_shipping_requires' ) ) {
						const shouldHideRequirements = data.woocommerce_free_shipping_requires === null || 
							data.woocommerce_free_shipping_requires === '' || 
							data.woocommerce_free_shipping_requires === 'coupon';

						const select = $( '#woocommerce_free_shipping_requires' );
						const fieldset = select.closest( 'fieldset' );
						const allOtherLabelElementsAfter = fieldset.nextAll( 'label' );
						const allOtherFieldsetElementsAfter = fieldset.nextAll( 'fieldset' );

						allOtherLabelElementsAfter.each( ( i ) => {
							$( allOtherLabelElementsAfter[ i ] ).css( 'display', shouldHideRequirements ? 'none' : 'block' );
						} );

						allOtherFieldsetElementsAfter.each( ( i ) => {
							$( allOtherFieldsetElementsAfter[ i ] ).css( 'display', shouldHideRequirements ? 'none' : 'block' );
						} );
					}
				},
				onModalLoaded: function( event, target ) {
					if ( target === 'wc-modal-shipping-method-settings' ) {
						const select = $( '#woocommerce_free_shipping_requires' );
						if ( select.length > 0 ) {
							event.data.view.possiblyHideFreeShippingRequirements( { woocommerce_free_shipping_requires: select.val() } );
						}

						event.data.view.possiblyAddShippingClassLink( event );
					}
				},
				possiblyAddShippingClassLink: function( event ) {
					const article = $( 'article.wc-modal-shipping-method-settings' );
					const shippingClassesCount = article.data( 'shipping-classes-count' );
					const status = article.data( 'status' );
					const instance_id = article.data( 'id' );
					const model = event.data.view.model;
					const methods = _.indexBy( model.get( 'methods' ), 'instance_id' );
					const method = methods[ instance_id ];

					if ( method.id === 'flat_rate' && shippingClassesCount === 0 ) {
						const link = article.find( '.wc-shipping-method-add-class-costs' );
						link.css( 'display', 'block' );
					}
				},
				validateFormArguments: function( event, target, data ) {
					if ( target === 'wc-modal-add-shipping-method' ) {
						if ( data.add_method_id ) {
							const nextButton = document.getElementById( 'btn-next' );
							nextButton.disabled = false;
							nextButton.classList.remove( 'disabled' );
						}
					} else if ( target === 'wc-modal-shipping-method-settings' ) {
						event.data.view.possiblyHideFreeShippingRequirements( data );
					}
				},
				onCloseConfigureShippingMethod: function( event, target, post_data, addButtonCalled ) {
					if ( target === 'wc-modal-shipping-method-settings' ) {
						var btnData = $( '#btn-ok' ).data();

						if ( ! addButtonCalled && btnData && btnData.status === 'new' ) {
							shippingMethodView.block();

							var view    = shippingMethodView,
								model   = view.model,
								methods   = _.indexBy( model.get( 'methods' ), 'instance_id' ),
								changes = {},
								instance_id = post_data.instance_id;

							// Remove method to zone via ajax call
							$.post( {
								url: ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?') + 'action=woocommerce_shipping_zone_remove_method',
								data: {
									wc_shipping_zones_nonce: data.wc_shipping_zones_nonce,
									instance_id: instance_id,
									zone_id: data.zone_id,
								},
								success: function( { data } ) {
									delete methods[instance_id];
									changes.methods = changes.methods || data.methods;
									model.set('methods', methods);
									model.logChanges( changes );
									view.clearUnloadConfirmation();
									view.render();
									shippingMethodView.unblock();
								},
								error: function( jqXHR, textStatus, errorThrown ) {
									window.alert( data.strings.remove_method_failed );
									shippingMethodView.unblock();
								},
								dataType: 'json'
							});
						}
					}
				},
				onChangeShippingMethodSelector: function() {
					var description = $( this ).find( 'option:selected' ).data( 'description' );
					$( this ).parent().find( '.wc-shipping-zone-method-description' ).remove();
					$( this ).after( '<div class="wc-shipping-zone-method-description">' + description + '</div>' );
				},
				onTogglePostcodes: function( event ) {
					event.preventDefault();
					var $tr = $( this ).closest( 'tr');
					$tr.find( '.wc-shipping-zone-postcodes' ).show();
					$tr.find( '.wc-shipping-zone-postcodes-toggle' ).hide();
				}
			} ),
			shippingMethod = new ShippingMethod({
				methods: data.methods,
				zone_name: data.zone_name
			} ),
			shippingMethodView = new ShippingMethodView({
				model:    shippingMethod,
				el:       $tbody
			} );

		shippingMethodView.render();

		$tbody.sortable({
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			handle: 'td.wc-shipping-zone-method-sort',
			scrollSensitivity: 40
		});
	});
})( jQuery, shippingZoneMethodsLocalizeScript, wp, ajaxurl );
