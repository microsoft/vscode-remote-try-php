/**
 * HTML Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string html_count HTML Count.
 *
 */
function astra_builder_html_css( builder_type = 'header', html_count ) {

    for ( var index = 1; index <= html_count; index++ ) {

		let selector = ( 'header' === builder_type ) ? '.ast-header-html-' + index : '.footer-widget-area[data-section="section-fb-html-' + index + '"]';

		let section = ( 'header' === builder_type ) ? 'section-hb-html-' + index : 'section-fb-html-' + index;

		var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
			mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

        // HTML color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-color',
            'astra-settings[' + builder_type + '-html-' + index + 'color]',
            'color',
            selector + ' .ast-builder-html-element'
		);

		// Link color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-l-color',
            'astra-settings[' + builder_type + '-html-' + index + 'link-color]',
            'color',
            selector + ' .ast-builder-html-element a'
		);

		// Link Hover color.
        astra_color_responsive_css(
			builder_type + '-html-' + index + '-l-h-color',
            'astra-settings[' + builder_type + '-html-' + index + 'link-h-color]',
            'color',
            selector + ' .ast-builder-html-element a:hover'
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

        // Margin.
		wp.customize( 'astra-settings[' + section + '-margin]', function( value ) {
			value.bind( function( margin ) {
				if(
					margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
					margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
					margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
				) {
					var dynamicStyle = '';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';

					dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';

					dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
					dynamicStyle += selector + ' {';
					dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
					dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
					dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
					dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';
					astra_add_dynamic_css( section + '-margin-toggle-button', dynamicStyle );
				}
			} );
		} );

		// Typography CSS Generation.
		astra_responsive_font_size(
			'astra-settings[font-size-' + section + ']',
			selector + ' .ast-builder-html-element'
		);
    }
}

/**
 * Button Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string button_count Button Count.
 *
 */
function astra_builder_button_css( builder_type = 'header', button_count ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	for ( var index = 1; index <= button_count; index++ ) {

		var section = ( 'header' === builder_type ) ? 'section-hb-button-' + index : 'section-fb-button-' + index;
		var context = ( 'header' === builder_type ) ? 'hb' : 'fb';
		var prefix = 'button' + index;
		var selector = '.ast-' + builder_type + '-button-' + index + ' .ast-builder-button-wrap';
		var button_selector = '.ast-' + builder_type + '-button-' + index + '[data-section*="section-' + context + '-button-"] .ast-builder-button-wrap';

		astra_css( 'flex', 'display', '.ast-' + builder_type + '-button-' + index + '[data-section="' + section + '"]' );

		// Button Text Color.
		astra_color_responsive_css(
			context + '-button-color',
			'astra-settings[' + builder_type + '-' + prefix + '-text-color]',
			'color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-text-h-color]',
			'color',
			selector + ':hover .ast-custom-button'
		);

		// Button Background Color.
		astra_color_responsive_css(
			context + '-button-bg-color',
			'astra-settings[' + builder_type + '-' + prefix + '-back-color]',
			'background-color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-bg-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-back-h-color]',
			'background-color',
			selector + ':hover .ast-custom-button'
		);

		// Button Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-' + prefix + '-font-size]',
			button_selector + ' .ast-custom-button'
		);
		astra_generate_outside_font_family_css(
			'astra-settings[' + builder_type + '-' + prefix + '-font-family]',
			button_selector + ' .ast-custom-button'
		);
		astra_generate_font_weight_css(
			'astra-settings[' + builder_type + '-' + prefix + '-font-family]',
			'astra-settings[' + builder_type + '-' + prefix + '-font-weight]',
			'font-weight',
			button_selector + ' .ast-custom-button'
		);

		astra_font_extras_css( builder_type + '-' + prefix + '-font-extras', button_selector + ' .ast-custom-button' );

		// Border Color.
		astra_color_responsive_css(
			context + '-button-border-color',
			'astra-settings[' + builder_type + '-' + prefix + '-border-color]',
			'border-color',
			selector + ' .ast-custom-button'
		);
		astra_color_responsive_css(
			context + '-button-border-color-h',
			'astra-settings[' + builder_type + '-' + prefix + '-border-h-color]',
			'border-color',
			selector + ' .ast-custom-button:hover'
		);

		// Advanced CSS Generation.
		astra_builder_advanced_css( section, button_selector + ' .ast-custom-button' );

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

		(function (index) {
			// Builder Type Border Radius Fields
			wp.customize('astra-settings[' + builder_type + '-button' + index + '-border-radius-fields]', function (setting) {
				setting.bind(function (border) {
					let globalSelector = '.ast-' + builder_type + '-button-'+ index +' .ast-custom-button';
					let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
							+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
							+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

					dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
							+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
							+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

					dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
							+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
							+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

					astra_add_dynamic_css( 'astra-settings[' + builder_type + '-button' + index + '-border-radius-fields]', dynamicStyle);
				});
			});
			wp.customize( 'astra-settings[' + builder_type + '-button'+ index +'-border-size]', function( setting ) {
				setting.bind( function( border ) {
					var dynamicStyle = '.ast-' + builder_type + '-button-'+ index +' .ast-custom-button {';
					dynamicStyle += 'border-top-width:'  + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += '} ';
					astra_add_dynamic_css( 'astra-settings[' + builder_type + '-button'+ index +'-border-size]', dynamicStyle );
				} );
			} );

			if( 'footer' == builder_type ) {
				wp.customize( 'astra-settings[footer-button-'+ index +'-alignment]', function( value ) {
					value.bind( function( alignment ) {

						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							dynamicStyle += '.ast-footer-button-'+ index +'[data-section="section-fb-button-'+ index +'"] {';
							dynamicStyle += 'justify-content: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-button-'+ index +'-alignment', dynamicStyle );
						}
					} );
				} );
			}
		})(index);
	}
}

/**
 * Social Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string section Section.
 *
 */
function astra_builder_social_css( builder_type = 'header', social_count ) {

	var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	for ( var index = 1; index <= social_count; index++ ) {

		let selector = '.ast-' + builder_type + '-social-' + index + '-wrap';
		let section = ( 'header' === builder_type ) ? 'section-hb-social-icons-' + index : 'section-fb-social-icons-' + index;
		var context = ( 'header' === builder_type ) ? 'hb' : 'fb';
		var visibility_selector = '.ast-builder-layout-element[data-section="' + section + '"]';

		// Icon Color.
		astra_color_responsive_css(
			context + '-soc-color',
			'astra-settings[' + builder_type + '-social-' + index + '-color]',
			'fill',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element svg'
		);

		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element .social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover'
		);

		astra_color_responsive_css(
			context + '-soc-label-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover .social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-svg-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-h-color]',
			'fill',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover svg'
		);

		// Icon Background Color.
		astra_color_responsive_css(
			context + '-soc-bg-color',
			'astra-settings[' + builder_type + '-social-' + index + '-bg-color]',
			'background-color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element'
		);

		astra_color_responsive_css(
			context + '-soc-bg-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-bg-h-color]',
			'background-color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover'
		);

		// Icon Label Color.
		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-label-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element span.social-item-label'
		);

		astra_color_responsive_css(
			context + '-soc-label-color-h',
			'astra-settings[' + builder_type + '-social-' + index + '-label-h-color]',
			'color',
			selector + ' .ast-social-color-type-custom .ast-builder-social-element:hover span.social-item-label'
		);

		// Icon Background Space.
		astra_css(
			'astra-settings[' + builder_type + '-social-' + index + '-bg-space]',
			'padding',
			selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element',
			'px'
		);

		// Icon Brand Color.
		astra_color_responsive_css(
			context + '-soc-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-color]',
			'fill',
			selector + ' .ast-social-color-type-official svg'
		);

		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-color]',
			'color',
			selector + ' .ast-social-color-type-official .social-item-label'
		);

		// Icon Label Brand Color.
		astra_color_responsive_css(
			context + '-soc-label-color',
			'astra-settings[' + builder_type + '-social-' + index + '-brand-label-color]',
			'color',
			selector + ' .ast-social-color-type-official span.social-item-label'
		);

		// Icon Border Radius Fields
		wp.customize('astra-settings[' + builder_type + '-social-' + index + '-radius-fields]', function (setting) {
			setting.bind(function (border) {

				let globalSelector = selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element';
				let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
						+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
						+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

				dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
						+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
						+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

				dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
						+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
						+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

				astra_add_dynamic_css( builder_type + '-social-' + index + '-radius-fields', dynamicStyle);
			});
		});

		// Typography CSS Generation.
		astra_responsive_font_size(
			'astra-settings[font-size-' + section + ']',
			selector
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, visibility_selector, 'block' );

		// Icon Spacing.
		(function( index ) {
			// Icon Size.
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-size]', function( value ) {
				value.bind( function( size ) {

					if( size.desktop != '' || size.tablet != '' || size.mobile != '' ) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.desktop + 'px;';
						dynamicStyle += 'width: ' + size.desktop + 'px;';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.tablet + 'px;';
						dynamicStyle += 'width: ' + size.tablet + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element svg {';
						dynamicStyle += 'height: ' + size.mobile + 'px;';
						dynamicStyle += 'width: ' + size.mobile + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						astra_add_dynamic_css( builder_type + '-social-' + index + '-size', dynamicStyle );
					}
				} );
			} );


			// Icon Space.
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-space]', function( value ) {
				value.bind( function( spacing ) {
					var space = '';
					var dynamicStyle = '';
					if ( spacing.desktop != '' ) {
						space = spacing.desktop/2;
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
					}

					if ( spacing.tablet != '' ) {
						space = spacing.tablet/2;
						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
					}

					if ( spacing.mobile != '' ) {
						space = spacing.mobile/2;
						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element {';
						dynamicStyle += 'margin-left: ' + space + 'px;';
						dynamicStyle += 'margin-right: ' + space + 'px;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:first-child {';
						dynamicStyle += 'margin-left: 0;';
						dynamicStyle += '} ';
						dynamicStyle += selector + ' .' + builder_type + '-social-inner-wrap .ast-builder-social-element:last-child {';
						dynamicStyle += 'margin-right: 0;';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
					}

					astra_add_dynamic_css( builder_type + '-social-icons-icon-space', dynamicStyle );
				} );
			} );

			// Color Type - Custom/Official
			wp.customize( 'astra-settings[' + builder_type + '-social-' + index + '-color-type]', function ( value ) {
				value.bind( function ( newval ) {

					var side_class = 'ast-social-color-type-' + newval;

					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').removeClass( 'ast-social-color-type-custom' );
					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').removeClass( 'ast-social-color-type-official' );
					jQuery('.ast-' + builder_type + '-social-' + index + '-wrap .' + builder_type + '-social-inner-wrap').addClass( side_class );
				} );
			} );

			// Margin.
			wp.customize( 'astra-settings[' + section + '-margin]', function( value ) {
				value.bind( function( margin ) {
					if(
						margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
						margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
						margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
					) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
						astra_add_dynamic_css( section + '-margin', dynamicStyle );
					}
				} );
			} );

			if ( 'footer' === builder_type ) {
				// Alignment.
				wp.customize( 'astra-settings[footer-social-' + index + '-alignment]', function( value ) {
					value.bind( function( alignment ) {
						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							dynamicStyle += '[data-section="section-fb-social-icons-' + index + '"] .footer-social-inner-wrap {';
							dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-social-' + index + '-alignment', dynamicStyle );
						}
					} );
				} );

			}
		})( index );
	}
}

/**
 * Widget Component CSS.
 *
 * @param string builder_type Builder Type.
 * @param string section Section.
 *
 */
function astra_builder_widget_css( builder_type = 'header' ) {

	var tablet_break_point    = AstraBuilderWidgetData.tablet_break_point || 768,
        mobile_break_point    = AstraBuilderWidgetData.mobile_break_point || 544;

	let widget_count = 'header' === builder_type ? AstraBuilderWidgetData.header_widget_count: AstraBuilderWidgetData.footer_widget_count;

	for ( var index = 1; index <= widget_count; index++ ) {

		var selector = '.' + builder_type + '-widget-area[data-section="sidebar-widgets-' + builder_type + '-widget-' + index + '"]';

		var section = AstraBuilderWidgetData.has_block_editor ? 'astra-sidebar-widgets-' + builder_type + '-widget-' + index : 'sidebar-widgets-' + builder_type + '-widget-' + index;

		// Widget Content Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-color]',
			'color',
			 ( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner' : selector + ' .' + builder_type + '-widget-area-inner'
		);

		// Widget Link Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-link-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-link-color]',
			'color',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner a' : selector + ' .' + builder_type + '-widget-area-inner a'
		);

		// Widget Link Hover Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-link-h-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-link-h-color]',
			'color',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner a:hover' : selector + ' .' + builder_type + '-widget-area-inner a:hover'
		);

		// Widget Title Color.
		astra_color_responsive_css(
			builder_type + '-widget-' + index + '-title-color',
			'astra-settings[' + builder_type + '-widget-' + index + '-title-color]',
			'color',
			selector + ' .widget-title, ' + selector + ' h1, ' + selector + ' .widget-area h1, ' + selector + ' h2, ' + selector + ' .widget-area h2, ' + selector + ' h3, ' + selector + ' .widget-area h3, ' + selector + ' h4, ' + selector + ' .widget-area h4, ' + selector + ' h5, ' + selector + ' .widget-area h5, ' + selector + ' h6, ' + selector + ' .widget-area h6'
		);

		// Widget Title Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-widget-' + index + '-font-size]',
			selector + ' .widget-title, ' + selector + ' h1, ' + selector + ' .widget-area h1, ' + selector + ' h2, ' + selector + ' .widget-area h2, ' + selector + ' h3, ' + selector + ' .widget-area h3, ' + selector + ' h4, ' + selector + ' .widget-area h4, ' + selector + ' h5, ' + selector + ' .widget-area h5, ' + selector + ' h6, ' + selector + ' .widget-area h6'
		);

		// Widget Content Typography.
		astra_responsive_font_size(
			'astra-settings[' + builder_type + '-widget-' + index + '-content-font-size]',
			( AstraBuilderWidgetData.is_flex_based_css ) ? selector + '.' + builder_type + '-widget-area-inner' : selector + ' .' + builder_type + '-widget-area-inner'
		);

		// Advanced Visibility CSS Generation.
		astra_builder_visibility_css( section, selector, 'block' );

		(function (index) {

			var marginControl = AstraBuilderWidgetData.has_block_editor ? 'astra-sidebar-widgets-' + builder_type + '-widget-' + index + '-margin' : 'sidebar-widgets-' + builder_type + '-widget-' + index + '-margin';

			wp.customize( 'astra-settings[' + marginControl + ']', function( value ) {
				value.bind( function( margin ) {
					var selector = '.' + builder_type + '-widget-area[data-section="sidebar-widgets-' + builder_type + '-widget-' + index + '"]';
					if(
						margin.desktop.bottom != '' || margin.desktop.top != '' || margin.desktop.left != '' || margin.desktop.right != '' ||
						margin.tablet.bottom != '' || margin.tablet.top != '' || margin.tablet.left != '' || margin.tablet.right != '' ||
						margin.mobile.bottom != '' || margin.mobile.top != '' || margin.mobile.left != '' || margin.mobile.right != ''
					) {
						var dynamicStyle = '';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['desktop']['left'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['desktop']['right'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['desktop']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['desktop']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['tablet']['left'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['tablet']['right'] + margin['tablet-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';

						dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
						dynamicStyle += selector + ' {';
						dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
						dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['desktop-unit'] + ';';
						dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['desktop-unit'] + ';';
						dynamicStyle += '} ';
						dynamicStyle += '} ';
						astra_add_dynamic_css( 'sidebar-widgets-' + builder_type + '-widget-' + index + '-margin', dynamicStyle );
					}
				} );
			} );

			if ( 'footer' === builder_type ) {

				wp.customize( 'astra-settings[footer-widget-alignment-' + index + ']', function( value ) {
					value.bind( function( alignment ) {
						if( alignment.desktop != '' || alignment.tablet != '' || alignment.mobile != '' ) {
							var dynamicStyle = '';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['desktop'] + ';';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['tablet'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
							if( AstraBuilderWidgetData.is_flex_based_css ){
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"].footer-widget-area-inner {';
							}else{
								dynamicStyle += '.footer-widget-area[data-section="sidebar-widgets-footer-widget-' + index + '"] .footer-widget-area-inner {';
							}
							dynamicStyle += 'text-align: ' + alignment['mobile'] + ';';
							dynamicStyle += '} ';
							dynamicStyle += '} ';

							astra_add_dynamic_css( 'footer-widget-alignment-' + index, dynamicStyle );
						}
					} );
				} );

			}
		})(index);

	}

}

/**
 * Apply Visibility CSS for the element
 *
 * @param string section Section ID.
 * @param string selector Base Selector.
 * @param string default_property default CSS property.
 */
function astra_builder_visibility_css( section, selector, default_property = 'flex' ) {

    var tablet_break_point    = astraBuilderPreview.tablet_break_point || 768,
		mobile_break_point    = astraBuilderPreview.mobile_break_point || 544;

	wp.customize( 'astra-settings[' + section + '-visibility-responsive]', function( setting ) {
		setting.bind( function( visibility ) {

			let dynamicStyle = '';
			let is_desktop = ( ! visibility['desktop'] ) ? 'none' : default_property ;
			let is_tablet = ( ! visibility['tablet'] ) ? 'none' : default_property ;
			let is_mobile = ( ! visibility['mobile'] ) ? 'none' : default_property ;

			dynamicStyle += selector + ' {';
			dynamicStyle += 'display: ' + is_desktop + ';';
			dynamicStyle += '} ';

			dynamicStyle +=  '@media (min-width: ' + mobile_break_point + 'px) and (max-width: ' + tablet_break_point + 'px) {';
			dynamicStyle += '.ast-header-break-point ' + selector + ' {';
			dynamicStyle += 'display: ' + is_tablet + ';';
			dynamicStyle += '} ';
			dynamicStyle += '} ';

			dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
			dynamicStyle += '.ast-header-break-point ' + selector + ' {';
			dynamicStyle += 'display: ' + is_mobile + ';';
			dynamicStyle += '} ';
			dynamicStyle += '} ';

			astra_add_dynamic_css( section + '-visibility-responsive', dynamicStyle );
		} );

	} );
}
