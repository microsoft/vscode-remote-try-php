/**
 * This file adds some LIVE to the Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 * @since 3.0.0
 */

( function( $ ) {

	var headingSelectors = 'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6';

	if( astraHeadingColorOptions.maybeApplyHeadingColorForTitle ) {
		headingSelectors += ',.ast-archive-title, .entry-title a';
	}

	/**
	 * Content <h1> to <h6> headings
	 */
	astra_css( 'astra-settings[heading-base-color]', 'color', headingSelectors );


	function headingDynamicCss(slug) {
		let anchorSupport = '';
		let WidthTitleSupport = '';

		// Check if anchors should be loaded in the CSS for headings.
		if( astraCustomizer.includeAnchorsInHeadindsCss ) {
			anchorSupport = ',.entry-content ' + slug + ' a';
		}

		// Add widget title support to font-weight preview CSS.
		if( astraCustomizer.font_weights_widget_title_support ) {
			WidthTitleSupport = ',' + slug + '.widget-title';
		}

		astra_generate_outside_font_family_css( 'astra-settings[font-family-'+ slug +']', slug + ', .entry-content ' + slug + anchorSupport );
		astra_generate_font_weight_css( 'astra-settings[font-family-'+ slug +']', 'astra-settings[font-weight-'+ slug +']', 'font-weight', slug + ', .entry-content ' + slug + anchorSupport + WidthTitleSupport );

		wp.customize( 'astra-settings[font-extras-'+ slug +']', function( value ) {

			value.bind( function( data ) {
				let elementorSupport = '';
				let dynamicStyle = '';

				if ( astraCustomizer.page_builder_button_style_css ) {
					elementorSupport = ',.elementor-widget-heading '+ slug +'.elementor-heading-title';
				}

					// Line Height
					const globalSelectorLineHeight = slug + ', .entry-content '+ slug + elementorSupport + anchorSupport;

					if( data['line-height'] && data['line-height-unit'] ) {
						dynamicStyle += globalSelectorLineHeight + '{';
						dynamicStyle += 'line-height : ' + data['line-height'] + data['line-height-unit'] + ';' ;
						dynamicStyle += '}';
					}

					const globalSelector = slug +', .entry-content ' + slug + anchorSupport;

					if( data['letter-spacing'] || data['text-decoration'] || data['text-transform'] ) {
						dynamicStyle += globalSelector + '{';
						if( data['letter-spacing'] && data['letter-spacing-unit'] ) {
							dynamicStyle += 'letter-spacing : ' + data['letter-spacing'] + data['letter-spacing-unit'] + ";" ;
						}
						if( data['text-decoration'] ) {
							dynamicStyle += 'text-decoration : ' + data['text-decoration'] + ";";
						}
						if( data['text-transform'] ) {
							dynamicStyle += 'text-transform : ' + data['text-transform']  + ';' ;
						}

						dynamicStyle += '}';
					}
					astra_add_dynamic_css( 'font-extras-'+ slug, dynamicStyle );
			});
		});
	}


	headingDynamicCss('h1');
	headingDynamicCss('h2');
	headingDynamicCss('h3');
	headingDynamicCss('h4');
	headingDynamicCss('h5');
	headingDynamicCss('h6');

	let woo_button_attr = '';

	// WooCommerce global button compatibility for new users only.
	if( astraCustomizer.astra_woo_btn_global_compatibility ) {
		woo_button_attr = ', .woocommerce a.button, .woocommerce button.button, .woocommerce .woocommerce-message a.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce input.button,.woocommerce-cart table.cart td.actions .button, .woocommerce form.checkout_coupon .button, .woocommerce #respond input#submit, .wc-block-grid__products .wc-block-grid__product .wp-block-button__link';
	}

	if ( astraCustomizer.page_builder_button_style_css ) {

		var ele_btn_font_family = '';
		var ele_btn_font_weight = '';
		var ele_btn_font_size = '';
		var ele_btn_transform = '';
		var ele_btn_line_height = '';
		var ele_btn_letter_spacing = '';

		if ( 'color-typo' == astraCustomizer.elementor_default_color_font_setting || 'typo' == astraCustomizer.elementor_default_color_font_setting ) {
			// Button Typo
			ele_btn_font_family = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';
			ele_btn_font_weight = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';
			ele_btn_font_size = ',.elementor-button-wrapper .elementor-button.elementor-size-sm, .elementor-button-wrapper .elementor-button.elementor-size-xs, .elementor-button-wrapper .elementor-button.elementor-size-md, .elementor-button-wrapper .elementor-button.elementor-size-lg, .elementor-button-wrapper .elementor-button.elementor-size-xl, .elementor-button-wrapper .elementor-button';
			ele_btn_transform = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';
			ele_btn_line_height = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';
			ele_btn_letter_spacing = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited', 'px';
		}
		// Button Typo
		astra_generate_outside_font_family_css( 'astra-settings[font-family-button]', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_font_family + woo_button_attr );
		astra_generate_font_weight_css( 'astra-settings[font-family-button]', 'astra-settings[font-weight-button]', 'font-weight', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_font_weight + woo_button_attr );
		astra_font_extras_css( 'font-extras-button', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], body .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_transform + woo_button_attr + astraCustomizer.improved_button_selector );

		astra_responsive_font_size( 'astra-settings[font-size-button]', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_font_size + woo_button_attr );
		astra_css( 'astra-settings[theme-btn-line-height]', 'line-height', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_line_height + woo_button_attr );
		astra_css( 'astra-settings[theme-btn-letter-spacing]', 'letter-spacing', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_btn_letter_spacing + woo_button_attr, 'px' );

	} else {
		// Button Typo
		astra_generate_outside_font_family_css( 'astra-settings[font-family-button]', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr );
		astra_generate_font_weight_css( 'astra-settings[font-family-button]', 'astra-settings[font-weight-button]', 'font-weight', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr );
		astra_font_extras_css( 'font-extras-button', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr );
		astra_responsive_font_size( 'astra-settings[font-size-button]', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr );
		astra_css( 'astra-settings[theme-btn-line-height]', 'line-height', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr );
		astra_css( 'astra-settings[theme-btn-letter-spacing]', 'letter-spacing', 'button, .ast-button, .ast-custom-button, input#submit, input[type="button"], input[type="submit"], input[type="reset"]' + astraCustomizer.v4_2_2_core_form_btns_styling + woo_button_attr , 'px' );
	}

	// Secondary button typo.
	let outline_btn_selector = 'body .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, body .ast-outline-button, body .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button';
	astra_generate_outside_font_family_css( 'astra-settings[secondary-font-family-button]', outline_btn_selector );
	astra_generate_font_weight_css( 'astra-settings[secondary-font-family-button]', 'astra-settings[secondary-font-weight-button]', 'font-weight', outline_btn_selector );
	astra_font_extras_css( 'secondary-font-extras-button', outline_btn_selector );
	astra_responsive_font_size( 'astra-settings[secondary-font-size-button]', outline_btn_selector );
	astra_css( 'astra-settings[secondary-theme-btn-line-height]', 'line-height', outline_btn_selector );
	astra_css( 'astra-settings[secondary-theme-btn-letter-spacing]', 'letter-spacing', outline_btn_selector, 'px' );

} )( jQuery );
