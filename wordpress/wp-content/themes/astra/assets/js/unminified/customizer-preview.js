/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 *
 * @package Astra
 */

/**
 * Generate font size in PX & REM
 */
function astra_font_size_rem( size, with_rem, device ) {

	var css = '';

	if( size != '' ) {

		var device = ( typeof device != undefined ) ? device : 'desktop';

		// font size with 'px'.
		css = 'font-size: ' + size + 'px;';

		// font size with 'rem'.
		if ( with_rem ) {
			var body_font_size = wp.customize( 'astra-settings[font-size-body]' ).get();

			body_font_size['desktop'] 	= ( body_font_size['desktop'] != '' ) ? body_font_size['desktop'] : 15;
			body_font_size['tablet'] 	= ( body_font_size['tablet'] != '' ) ? body_font_size['tablet'] : body_font_size['desktop'];
			body_font_size['mobile'] 	= ( body_font_size['mobile'] != '' ) ? body_font_size['mobile'] : body_font_size['tablet'];

			css += 'font-size: ' + ( size / body_font_size[device] ) + 'rem;';
		}
	}

	return css;
}

/**
 * Refresh customizer iframe.
 */
function astra_refresh_customizer( control ) {
	wp.customize( control, function( value ) {
		value.bind( function( value ) {
			wp.customize.preview.send( 'refresh' );
		} );
	} );
}

/**
 * Apply CSS for the element
 */
function astra_color_responsive_css( addon, control, css_property, selector ) {

	wp.customize( control, function( value ) {
		value.bind( function( value ) {
			if ( value.desktop || value.mobile || value.tablet ) {
				// Remove <style> first!
				control = control.replace( '[', '-' );
				control = control.replace( ']', '' );
				jQuery( 'style#' + control + '-' + addon ).remove();

				var DeskVal = '',
					TabletFontVal = '',
					MobileVal = '';

				if ( '' != value.desktop ) {
					DeskVal = css_property + ': ' + value.desktop;
				}
				if ( '' != value.tablet ) {
					TabletFontVal = css_property + ': ' + value.tablet;
				}
				if ( '' != value.mobile ) {
					MobileVal = css_property + ': ' + value.mobile;
				}

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + addon + '">'
					+ selector + '	{ ' + DeskVal + ' }'
					+ '@media (max-width: 768px) {' + selector + '	{ ' + TabletFontVal + ' } }'
					+ '@media (max-width: 544px) {' + selector + '	{ ' + MobileVal + ' } }'
					+ '</style>'
				);

			} else {
				jQuery( 'style#' + control + '-' + addon ).remove();
			}

		} );
	} );
}


/**
 * Responsive Font Size CSS
 */
function astra_responsive_font_size( control, selector ) {

	wp.customize( control, function( value ) {
		value.bind( function( value ) {

			if ( value.desktop || value.mobile || value.tablet ) {
				// Remove <style> first!
				control = control.replace( '[', '-' );
				control = control.replace( ']', '' );

				var fontSize = '',
					tabletFontSize = '',
					mobileFontSize = '',
					css_property = 'font-size';

				jQuery( 'style#' + control + '-' + css_property ).remove();

				if ( '' != value.desktop ) {
					fontSize = 'font-size: ' + value.desktop + ( undefined == value['desktop-unit'] ? 'px' : value['desktop-unit'] );
				}
				if ( '' != value.tablet ) {
					tabletFontSize = 'font-size: ' + value.tablet + ( undefined == value['tablet-unit'] ? 'px' : value['tablet-unit'] );
				}
				if ( '' != value.mobile ) {
					mobileFontSize = 'font-size: ' + value.mobile + ( undefined == value['mobile-unit'] ? 'px' : value['mobile-unit'] );
				}

				if( value['desktop-unit'] == 'px' ) {
					fontSize = astra_font_size_rem( value.desktop, true, 'desktop' );
				}

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + css_property + '">'
					+ selector + '	{ ' + fontSize + ' }'
					+ '@media (max-width: 768px) {' + selector + '	{ ' + tabletFontSize + ' } }'
					+ '@media (max-width: 544px) {' + selector + '	{ ' + mobileFontSize + ' } }'
					+ '</style>'
				);

			} else {
				jQuery( 'style#' + control  + '-font-size' ).remove();
			}

		} );
	} );
}

/**
 * Font extras control dynamic CSS.
 *
 * @since x.x.x
 */
function astra_font_extras_css( control, selector ) {
	wp.customize( 'astra-settings[' + control + ']', function( value ) {
		value.bind( function( data ) {
			if ( data ) {
				// Remove <style> first!
				jQuery( 'style#' + control ).remove();

				let dynamicStyle = selector + ' { line-height : ' + data['line-height'] + data['line-height-unit'] + ";";
					dynamicStyle += 'letter-spacing : ' + data['letter-spacing'] + data['letter-spacing-unit'] + ";" ;
					dynamicStyle += 'text-decoration : ' + data['text-decoration'] + ";";
					dynamicStyle += 'text-transform : ' + data['text-transform']  + ';}' ;

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '">'
					+ dynamicStyle + '</style>'
				);

			} else {
				jQuery( 'style#' + control ).remove();
			}
		} );
	} );
}

/**
 * Responsive Spacing CSS
 */
function astra_responsive_spacing( control, selector, type, side ) {

	wp.customize( control, function( value ) {
		value.bind( function( value ) {
			var sidesString = "";
			var spacingType = "padding";
			if ( value.desktop.top || value.desktop.right || value.desktop.bottom || value.desktop.left || value.tablet.top || value.tablet.right || value.tablet.bottom || value.tablet.left || value.mobile.top || value.mobile.right || value.mobile.bottom || value.mobile.left ) {
				if ( typeof side != undefined ) {
					sidesString = side + "";
					// Replace comma character with dash, necessary to separate out spacing dimensions.
					sidesString = sidesString.replace(/,/g , "-");
				}
				if ( typeof type != undefined ) {
					spacingType = type + "";
				}
				// Remove <style> first!
				control = control.replace( '[', '-' );
				control = control.replace( ']', '' );
				jQuery( 'style#' + control + '-' + spacingType + '-' + sidesString ).remove();

				var desktopPadding = '',
					tabletPadding = '',
					mobilePadding = '';

				var paddingSide = ( typeof side != undefined ) ? side : [ 'top','bottom','right','left' ];

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['desktop'][sideValue] ) {
						desktopPadding += spacingType + '-' + sideValue +': ' + value['desktop'][sideValue] + value['desktop-unit'] +';';
					}
				});

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['tablet'][sideValue] ) {
						tabletPadding += spacingType + '-' + sideValue +': ' + value['tablet'][sideValue] + value['tablet-unit'] +';';
					}
				});

				jQuery.each(paddingSide, function( index, sideValue ){
					if ( '' != value['mobile'][sideValue] ) {
						mobilePadding += spacingType + '-' + sideValue +': ' + value['mobile'][sideValue] + value['mobile-unit'] +';';
					}
				});

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + spacingType + '-' + sidesString + '">'
					+ selector + '	{ ' + desktopPadding +' }'
					+ '@media (max-width: 768px) {' + selector + '	{ ' + tabletPadding + ' } }'
					+ '@media (max-width: 544px) {' + selector + '	{ ' + mobilePadding + ' } }'
					+ '</style>'
				);

			} else {
				wp.customize.preview.send( 'refresh' );
				jQuery( 'style#' + control + '-' + spacingType + '-' + sidesString ).remove();
			}

		} );
	} );
}

/**
 * CSS
 */
function astra_css_font_size( control, selector ) {

	wp.customize( control, function( value ) {
		value.bind( function( size ) {

			if ( size ) {

				// Remove <style> first!
				control = control.replace( '[', '-' );
				control = control.replace( ']', '' );
				jQuery( 'style#' + control ).remove();

				var fontSize = 'font-size: ' + size;
				if ( ! isNaN( size ) || size.indexOf( 'px' ) >= 0 ) {
					size = size.replace( 'px', '' );
					fontSize = astra_font_size_rem( size, true );
				}

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '">'
					+ selector + '	{ ' + fontSize + ' }'
					+ '</style>'
				);

			} else {

				jQuery( 'style#' + control ).remove();
			}

		} );
	} );
}

/**
 * Return get_hexdec()
 */
function get_hexdec( hex ) {
	var hexString = hex.toString( 16 );
	return parseInt( hexString, 16 );
}

/**
 * Apply CSS for the element
 */
function astra_css( control, css_property, selector, unit, important = false ) {

	wp.customize( control, function( value ) {
		value.bind( function( new_value ) {

			// Remove <style> first!
			control = control.replace( '[', '-' );
			control = control.replace( ']', '' );

			if ( new_value || 0 === new_value ) {

				/**
				 *	If ( unit == 'url' ) then = url('{VALUE}')
				 *	If ( unit == 'px' ) then = {VALUE}px
				 *	If ( unit == 'em' ) then = {VALUE}em
				 *	If ( unit == 'rem' ) then = {VALUE}rem.
				 */
				if ( 'undefined' != typeof unit) {

					if ( 'url' === unit ) {
						new_value = 'url(' + new_value + ')';
					} else {
						new_value = new_value + unit;
					}
				}

				const imp_css_prop = important ? ' !important' : '';

				// Remove old.
				jQuery( 'style#' + control + '-' + css_property ).remove();

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + css_property + '">'
					+ selector + '	{ ' + css_property + ': ' + new_value + imp_css_prop + ' }'
					+ '</style>'
				);

				if( 'unset' === new_value ){
					jQuery( 'style#' + control + '-' + css_property ).remove();
				}

			} else {
				// Remove old.
				jQuery( 'style#' + control + '-' + css_property ).remove();
			}

		} );
	} );
}


/**
 * Dynamic Internal/Embedded Style for a Control
 */
function astra_add_dynamic_css( control, style ) {
	control = control.replace( '[', '-' );
	control = control.replace( ']', '' );
	jQuery( 'style#' + control ).remove();

	jQuery( 'head' ).append(
		'<style id="' + control + '">' + style + '</style>'
	);
}

/**
 * Generate background_obj CSS
 */
function astra_background_obj_css( wp_customize, bg_obj, ctrl_name, style ) {

	var gen_bg_css 	= '';
	var bg_img		= bg_obj['background-image'];
	var bg_color	= bg_obj['background-color'];

	if( '' === bg_color && '' === bg_img ) {
		jQuery( 'style#' + ctrl_name ).remove();
	}else{
		if( undefined !== bg_obj['background-type'] && '' !== bg_obj['background-type'] ) {

			if ( ( 'color' === bg_obj['background-type'] ) ) {

				if ( '' !== bg_img && '' !== bg_color && undefined !== bg_color && 'unset' !== bg_color ) {

					gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_color + ', ' + bg_color + '), url(' + bg_img + ');';
				}  else if ( undefined === bg_img || '' === bg_img || 'unset' === bg_img ) {

					gen_bg_css = 'background-color: ' + bg_color + ';';

				}
			} else if ( 'image' === bg_obj['background-type'] ) {
				if ( '' !== bg_img ) {
					if ( 'overlay-type' in bg_obj && 'none' !== bg_obj['overlay-type'] ) {
						let overlay_color	= 'overlay-color' in bg_obj ? bg_obj['overlay-color'] : '';
						let overlay_opacity	= 'overlay-opacity' in bg_obj ? bg_obj['overlay-opacity'] : '';
						let overlay_gradient	= 'overlay-gradient' in bg_obj ? bg_obj['overlay-gradient'] : '';
						if ( 'classic' === bg_obj['overlay-type'] && '' !== overlay_color ) {
							if ( '' !== overlay_opacity ) {
								wp.customize.preview.send( 'refresh' );
								return;
							}
							gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_obj['overlay-color'] + ', ' + bg_obj['overlay-color'] + '), url(' + bg_img + ');';
						} else if ( 'gradient' === bg_obj['overlay-type'] && '' !== overlay_gradient ) {
							gen_bg_css = 'background-image: ' + overlay_gradient + ', url(' + bg_img + ');';
						} else {
							gen_bg_css = 'background-image: url(' + bg_img + ');';
						}
					} else {
						gen_bg_css = 'background-image: url(' + bg_img + ');';
					}
				}
			} else if ( 'gradient' === bg_obj['background-type'] ) {
				if ( '' !== bg_color && 'unset' !== bg_color ) {
					gen_bg_css = 'background-image: ' + bg_color + ';';
				}
			}
		}

		if ( '' !== bg_img ) {

			gen_bg_css += 'background-repeat: ' + bg_obj['background-repeat'] + ';';
			gen_bg_css += 'background-position: ' + bg_obj['background-position'] + ';';
			gen_bg_css += 'background-size: ' + bg_obj['background-size'] + ';';
			gen_bg_css += 'background-attachment: ' + bg_obj['background-attachment'] + ';';
		}

		var dynamicStyle = style.replace( "{{css}}", gen_bg_css );

		astra_add_dynamic_css( ctrl_name, dynamicStyle );
	}
}

/*
* Generate Font Family CSS
*/
function astra_generate_outside_font_family_css( control, selector ) {
	wp.customize( control, function (value) {
		value.bind( function ( value, oldValue ) {

			var cssProperty = 'font-family';
			var link = '';

			var fontName = value.split(",")[0];
			// Replace ' character with space, necessary to separate out font prop value.
			fontName = fontName.replace(/'/g, '');

			// Remove <style> first!
			control = control.replace( '[', '-' );
			control = control.replace( ']', '' );

			jQuery('style#' + control + '-' + cssProperty ).remove();

			if ( fontName in astraCustomizer.googleFonts ) {
				// Remove old.

				var fontName = fontName.split(' ').join('+');

				jQuery('link#' + control).remove();
				link = '<link id="' + control + '" href="https://fonts.googleapis.com/css?family=' + fontName + '"  rel="stylesheet">';
			}

			// Concat and append new <style> and <link>.
			jQuery('head').append(
				'<style id="' + control + '-' + cssProperty + '">'
				+ selector + '	{ ' + cssProperty + ': ' + value + ' }'
				+ '</style>'
				+ link
			);
		});
	});
}

/**
 * Apply Advanced CSS for the element
 *
 * @param string section Section ID.
 * @param string selector Base Selector.
 */
function astra_builder_advanced_css( section, selector ) {

    var tablet_break_point    = 921,
		mobile_break_point    = 544;

    // Padding.
    wp.customize( 'astra-settings[' + section + '-padding]', function( value ) {
        value.bind( function( padding ) {

			if( ! padding.hasOwnProperty('desktop') ) {
				return
			}

            if(
                padding.desktop.bottom != '' || padding.desktop.top != '' || padding.desktop.left != '' || padding.desktop.right != '' ||
                padding.tablet.bottom != '' || padding.tablet.top != '' || padding.tablet.left != '' || padding.tablet.right != '' ||
                padding.mobile.bottom != '' || padding.mobile.top != '' || padding.mobile.left != '' || padding.mobile.right != ''
            ) {
                var dynamicStyle = '';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'padding-left: ' + padding['desktop']['left'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['desktop']['right'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['desktop']['top'] + padding['desktop-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['desktop']['bottom'] + padding['desktop-unit'] + ';';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + tablet_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'padding-left: ' + padding['tablet']['left'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['tablet']['right'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['tablet']['top'] + padding['tablet-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['tablet']['bottom'] + padding['tablet-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'padding-left: ' + padding['mobile']['left'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-right: ' + padding['mobile']['right'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-top: ' + padding['mobile']['top'] + padding['mobile-unit'] + ';';
                dynamicStyle += 'padding-bottom: ' + padding['mobile']['bottom'] + padding['mobile-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( section + '-padding-toggle-button', dynamicStyle );
            } else {
                astra_add_dynamic_css( section + '-padding-toggle-button', '' );
            }
        } );
    } );

    // Margin.
    wp.customize( 'astra-settings[' + section + '-margin]', function( value ) {
        value.bind( function( margin ) {

        	if( ! margin.hasOwnProperty('desktop') ) {
        		return
			}

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
                dynamicStyle += 'margin-top: ' + margin['tablet']['top'] + margin['tablet-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['tablet']['bottom'] + margin['tablet-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';

                dynamicStyle +=  '@media (max-width: ' + mobile_break_point + 'px) {';
                dynamicStyle += selector + ' {';
                dynamicStyle += 'margin-left: ' + margin['mobile']['left'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-right: ' + margin['mobile']['right'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-top: ' + margin['mobile']['top'] + margin['mobile-unit'] + ';';
                dynamicStyle += 'margin-bottom: ' + margin['mobile']['bottom'] + margin['mobile-unit'] + ';';
                dynamicStyle += '} ';
                dynamicStyle += '} ';
                astra_add_dynamic_css( section + '-margin-toggle-button', dynamicStyle );
            } else {
                astra_add_dynamic_css( section + '-margin-toggle-button', '' );
            }
        } );
    } );
}

/**
 * Apply Advanced CSS for the element
 *
 * @param string section Section ID.
 * @param string selector Base Selector.
 */
function astra_border_spacing_advanced_css( section, selector ) {
	wp.customize( 'astra-settings[' + section + '-border-width]', function( setting ) {
		setting.bind( function( border ) {
			var dynamicStyle = selector + ' {';
			dynamicStyle += border.top ? 'border-top-style: solid;' : '';
			dynamicStyle += border.right ? 'border-right-style: solid;' : '';
			dynamicStyle += border.bottom ? 'border-bottom-style: solid;' : '';
			dynamicStyle += border.left ? 'border-left-style: solid;' : '';
			dynamicStyle += 'border-top-width:'  + border.top + 'px;';
			dynamicStyle += 'border-right-width:'  + border.right + 'px;';
			dynamicStyle += 'border-left-width:'   + border.left + 'px;';
			dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
			dynamicStyle += '} ';
			astra_add_dynamic_css( 'astra-settings[' + section + '-border-width]', dynamicStyle );
		} );
	} );
	wp.customize( 'astra-settings[' + section + '-border-radius]', function( setting ) {
		setting.bind( function( border ) {
			if ( border.top === '' && border.right === '' && border.bottom === '' || border.left === '' ) {
				wp.customize.preview.send( 'refresh' );
				return;
			}
			var dynamicStyle = selector + ' {';
			dynamicStyle += 'border-top-left-radius:'  + border.top + 'px;';
			dynamicStyle += 'border-top-right-radius:'  + border.right + 'px;';
			dynamicStyle += 'border-bottom-left-radius:'   + border.left + 'px;';
			dynamicStyle += 'border-bottom-right-radius:'   + border.bottom + 'px;';
			dynamicStyle += '} ';
			astra_add_dynamic_css( 'astra-settings[' + section + '-border-radius]', dynamicStyle );
		} );
	} );
	astra_css( 'astra-settings[' + section + '-border-color]', 'border-color', selector );
	astra_builder_advanced_css( section, selector );
}

/*
* Generate Font Weight CSS
*/
function astra_generate_font_weight_css( font_control, control, css_property, selector ) {
	wp.customize( control, function( value ) {
		value.bind( function( new_value ) {

			control = control.replace( '[', '-' );
			control = control.replace( ']', '' );
			var link = '';

			if ( new_value ) {

				/**
				 *	If ( unit == 'url' ) then = url('{VALUE}')
				 *	If ( unit == 'px' ) then = {VALUE}px
				 *	If ( unit == 'em' ) then = {VALUE}em
				 *	If ( unit == 'rem' ) then = {VALUE}rem.
				 */
				if ( 'undefined' != typeof unit) {

					if ( 'url' === unit ) {
						new_value = 'url(' + new_value + ')';
					} else {
						new_value = new_value + unit;
					}
				}

				var fontName = wp.customize._value[font_control]._value;
				fontName = fontName.split(',');
				fontName = fontName[0].replace( /'/g, '' );

				// Remove old.
				jQuery( 'style#' + control + '-' + css_property ).remove();

				if ( fontName in astraCustomizer.googleFonts ) {
					// Remove old.

					jQuery('#' + font_control).remove();
					if( new_value === "inherit" ) {
						link = '<link id="' + font_control + '" href="https://fonts.googleapis.com/css?family=' + fontName + '"  rel="stylesheet">';
					} else {
						link = '<link id="' + font_control + '" href="https://fonts.googleapis.com/css?family=' + fontName + '%3A' + new_value + '"  rel="stylesheet">';
					}
				}

				// Concat and append new <style>.
				jQuery( 'head' ).append(
					'<style id="' + control + '-' + css_property + '">'
					+ selector + '	{ ' + css_property + ': ' + new_value + ' }'
					+ '</style>'
					+ link
				);

			} else {
				// Remove old.
				jQuery( 'style#' + control ).remove();
			}

		} );
	});
}

/**
 * Apply CSS for the element
 */
function astra_apply_responsive_background_css( control, selector, device, singleColorSelector, addon ) {
	wp.customize( control, function( value ) {
		value.bind( function( bg_obj ) {

			addon = addon || '';
			singleColorSelector = singleColorSelector || '';

			addon = ( addon ) ? addon : 'header';

			control = control.replace( '[', '-' );
			control = control.replace( ']', '' );

			if( '' === bg_obj[device] || undefined === bg_obj[device] ){
				return;
			}

			var gen_bg_css 	= '';
			var bg_img		= bg_obj[device]['background-image'];
			var bg_tab_img	= bg_obj['tablet']['background-image'];
			var bg_desk_img	= bg_obj['desktop']['background-image'];
			var bg_color	= bg_obj[device]['background-color'];
			var tablet_css  = ( bg_obj['tablet']['background-image'] ) ? true : false;
			var desktop_css = ( bg_obj['desktop']['background-image'] ) ? true : false;

			if( undefined !== bg_obj[device]['background-type'] && '' !== bg_obj[device]['background-type'] ) {
				if ( ( 'color' === bg_obj[device]['background-type'] ) ) {
					if ( '' !== bg_img && '' !== bg_color && undefined !== bg_color && 'unset' !== bg_color ) {
						gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_color + ', ' + bg_color + '), url(' + bg_img + ');';
					} else if ( 'mobile' === device ) {
						if ( desktop_css ) {
							gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_color + ', ' + bg_color + '), url(' + bg_desk_img + ');';
						} else if ( tablet_css ) {
							gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_color + ', ' + bg_color + '), url(' + bg_tab_img + ');';
						} else {
							gen_bg_css = 'background-color: ' + bg_color + ';';
							gen_bg_css += 'background-image: none;';
						}
					} else if ( 'tablet' === device ) {
						if ( desktop_css ) {
							gen_bg_css = 'background-image: linear-gradient(to right, ' + bg_color + ', ' + bg_color + '), url(' + bg_desk_img + ');';
						} else {
							gen_bg_css = 'background-color: ' + bg_color + ';';
							gen_bg_css += 'background-image: none;';
						}
					} else if ( undefined === bg_img || '' === bg_img ) {
						gen_bg_css = 'background-color: ' + bg_color + ';';
						gen_bg_css += 'background-image: none;';
					}
				} else if ( 'image' === bg_obj[device]['background-type'] ) {
					if ( '' !== bg_img ) {
						if ( undefined !== bg_obj[device]['overlay-type'] && 'none' !== bg_obj[device]['overlay-type'] ) {
							let overlay_color	= undefined !== bg_obj[device]['overlay-color'] ? bg_obj[device]['overlay-color'] : '';
							let overlay_opacity	= undefined !== bg_obj[device]['overlay-opacity'] ? bg_obj[device]['overlay-opacity'] : '';
							let overlay_gradient	= undefined !== bg_obj[device]['overlay-gradient'] ? bg_obj[device]['overlay-gradient'] : '';
							if ( 'classic' === bg_obj[device]['overlay-type'] && '' !== overlay_color ) {
								if ( '' !== overlay_opacity ) {
									wp.customize.preview.send( 'refresh' );
									return;
								}
								gen_bg_css = 'background-image: linear-gradient(to right, ' + overlay_color + ', ' + overlay_color + '), url(' + bg_img + ');';
							} else if ( 'gradient' === bg_obj[device]['overlay-type'] && '' !== overlay_gradient ) {
								gen_bg_css = 'background-image: ' + overlay_gradient + ', url(' + bg_img + ');';
							} else {
								gen_bg_css = 'background-image: url(' + bg_img + ');';
							}
						} else {
							gen_bg_css = 'background-image: url(' + bg_img + ');';
						}
					}
				} else if ( 'gradient' === bg_obj[device]['background-type'] ) {
					if ( '' !== bg_color && 'unset' !== bg_color ) {
						gen_bg_css = 'background-image: ' + bg_color + ';';
					}
				}
			}

			if ( '' !== bg_img ) {

				gen_bg_css += 'background-repeat: ' + bg_obj[device]['background-repeat'] + ';';
				gen_bg_css += 'background-position: ' + bg_obj[device]['background-position'] + ';';
				gen_bg_css += 'background-size: ' + bg_obj[device]['background-size'] + ';';
				gen_bg_css += 'background-attachment: ' + bg_obj[device]['background-attachment'] + ';';
			}

			// Remove old.
			jQuery( 'style#' + control + '-' + device + '-' + addon ).remove();

			if ( 'desktop' == device ) {
				var dynamicStyle = '<style id="' + control + '-' + device + '-' + addon + '">'
					+ selector + '	{ ' + gen_bg_css + ' }'
				+ '</style>'
			}
			if ( 'tablet' == device ) {
				var dynamicStyle = '<style id="' + control + '-' + device + '-' + addon + '">'
					+ '@media (max-width: 768px) {' + selector + '	{ ' + gen_bg_css + ' } }'
				+ '</style>'
			}
			if ( 'mobile' == device ) {
				var dynamicStyle = '<style id="' + control + '-' + device + '-' + addon + '">'
					+ '@media (max-width: 544px) {' + selector + '	{ ' + gen_bg_css + ' } }'
				+ '</style>'
			}

			// Concat and append new <style>.
			jQuery( 'head' ).append(
				dynamicStyle
			);
		});
	});
}

function getChangedKey( value, other ) {

	value = isJsonString(value) ? JSON.parse(value) : value;
	other = isJsonString(other) ? JSON.parse(other) : other;

	// Compare two items
	var compare = function ( item1, item2 ) {

		// Get the object type
		var itemType = Object.prototype.toString.call(item1);

		// If an object or array, compare recursively
		if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
			if ('string' == typeof getChangedKey(item1, item2)) {
				return false;
			}
		}

		// Otherwise, do a simple comparison
		else {

			// If the two items are not the same type, return false
			if (itemType !== Object.prototype.toString.call(item2)) return false;

			// Else if it's a function, convert to a string and compare
			// Otherwise, just compare
			if (itemType === '[object Function]') {
				if (item1.toString() !== item2.toString()) return false;
			} else {
				if (item1 !== item2) return false;
			}

		}
	};

	for ( var key in value ) {
		if ( other.hasOwnProperty(key) && value.hasOwnProperty(key) ) {
			if ( compare( value[key], other[key] ) === false ) return key;
		} else {
			return key;
		}
	}

	// If nothing failed, return true
	return true;

}

function isJsonString( str ) {

	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

function hasWordPressWidgetBlockEditor() {
	return astraCustomizer.has_block_editor_support || false;
}

( function( $ ) {

	/*
	 * Site Identity Logo Width
	 */
	wp.customize( 'astra-settings[ast-header-responsive-logo-width]', function( setting ) {
		setting.bind( function( logo_width ) {
			let tabletBreakPoint    = astraCustomizer.tablet_breakpoint || 921,
				mobileBreakPoint    = astraCustomizer.mobile_breakpoint || 544;

			if ( logo_width['desktop'] != '' || logo_width['tablet'] != '' || logo_width['mobile'] != '' ) {
				var dynamicStyle = `#masthead .site-logo-img .custom-logo-link img {
					max-width: ${logo_width['desktop']}px;
					width: ${logo_width['desktop']}px;
				}
				@media( max-width: ${tabletBreakPoint}px ) {
					#masthead .site-logo-img .custom-logo-link img {
						max-width: ${logo_width['tablet']}px;
						width: ${logo_width['tablet']}px;
					}
					#masthead .site-logo-img img {
						max-width: ${logo_width['tablet']}px;
						width: ${logo_width['tablet']}px;
					}
				}
				@media( max-width: ${mobileBreakPoint}px ) {
					#masthead .site-logo-img .custom-logo-link img {
						max-width: ${logo_width['mobile']}px;
						width: ${logo_width['mobile']}px;
					}
					#masthead .site-logo-img img {
						max-width: ${logo_width['mobile']}px;
						max-height: ${logo_width['mobile']}px;
					}
				}`;
				astra_add_dynamic_css( 'ast-header-responsive-logo-width', dynamicStyle );

				var mobileLogoStyle = '.ast-header-break-point #masthead .site-logo-img .custom-mobile-logo-link img { max-width: ' + logo_width['tablet'] + 'px; } @media( max-width: ' + tabletBreakPoint + 'px ) { .ast-header-break-point #masthead .site-logo-img .custom-mobile-logo-link img { max-width: ' + logo_width['tablet'] + 'px; }  @media( max-width: ' + mobileBreakPoint + 'px ) { .ast-header-break-point #masthead .site-logo-img .custom-mobile-logo-link img { max-width: ' + logo_width['mobile'] + 'px; }';
				astra_add_dynamic_css( 'mobile-header-logo-width', mobileLogoStyle );

				/**
				 * Logo SVG Icon Stylings.
				 */
				var logoSVGIconStyles = '#masthead .ast-logo-svg-icon svg { width: ' + logo_width['desktop'] + 'px; } @media( max-width: ' + tabletBreakPoint + 'px ) { #masthead .ast-logo-svg-icon svg { width: ' + logo_width['tablet'] + 'px; } #masthead .ast-logo-svg-icon svg { width: ' + logo_width['tablet'] + 'px; } } @media( max-width: ' + mobileBreakPoint + 'px ) { #masthead .ast-logo-svg-icon svg { width: ' + logo_width['mobile'] + 'px; }' + '#masthead .ast-logo-svg-icon svg { width: ' + logo_width['mobile'] + 'px; } }';
				astra_add_dynamic_css( 'ast-header-responsive-logo-svg-icon-width', logoSVGIconStyles );
			} else {
				wp.customize.preview.send( 'refresh' );
			}
		} );
	} );

	/*
	 * Responsive Logo Visibility
	 */
	wp.customize( 'astra-settings[display-site-title-responsive]', function( setting ) {
		setting.bind( function( logo_visibility ) {
			var desktopTitleVisibility  = ( logo_visibility['desktop'] ) ? 'block' : 'none';
			var tabletTitleVisibility  = ( logo_visibility['tablet'] ) ? 'block' : 'none';
			var mobileTitleVisibility  = ( logo_visibility['mobile'] ) ? 'block' : 'none';
			var tabletBreakPoint    = astraBuilderPreview.tablet_break_point || 921,
				mobileBreakPoint    = astraBuilderPreview.mobile_break_point || 544;
			var dynamicStyle = '.ast-site-title-wrap .site-title { display: ' + desktopTitleVisibility + ';} @media( max-width: ' + tabletBreakPoint + 'px) { .ast-site-title-wrap .site-title { display: ' + tabletTitleVisibility + ';} } @media( max-width: ' + mobileBreakPoint + 'px) { .ast-site-title-wrap .site-title { display: ' + mobileTitleVisibility + ';} }';
			astra_add_dynamic_css( 'display-site-title-responsive', dynamicStyle );
		} );
	} );

	/*
	 * Responsive Tagline Visibility
	 */
	wp.customize( 'astra-settings[display-site-tagline-responsive]', function( setting ) {
		setting.bind( function( tagline_visibility ) {
			var desktopTaglineVisibility  = ( tagline_visibility['desktop'] ) ? 'block' : 'none';
			var tabletTaglineVisibility  = ( tagline_visibility['tablet'] ) ? 'block' : 'none';
			var mobileTaglineVisibility  = ( tagline_visibility['mobile'] ) ? 'block' : 'none';
			var tabletBreakPoint    = astraBuilderPreview.tablet_break_point || 768,
				mobileBreakPoint    = astraBuilderPreview.mobile_break_point || 544;
			var dynamicStyle = '.ast-site-title-wrap .site-description { display: ' + desktopTaglineVisibility + ';} @media( max-width: ' + tabletBreakPoint + 'px) { .ast-site-title-wrap .site-description { display: ' + tabletTaglineVisibility + ';} } @media( max-width: ' + mobileBreakPoint + 'px) { .ast-site-title-wrap .site-description { display: ' + mobileTaglineVisibility + ';} }';
			astra_add_dynamic_css( 'display-site-tagline-responsive', dynamicStyle );
		} );
	} );

	/*
	 * Full width layout
	 */
	wp.customize( 'astra-settings[site-content-width]', function( setting ) {
		setting.bind( function( width ) {
				var dynamicStyle = '@media (min-width: 554px) {';
				dynamicStyle += '.ast-container, .fl-builder #content .entry-header { max-width: ' + ( 40 + parseInt( width ) ) + 'px } ';
				dynamicStyle += '}';
				if (  jQuery( 'body' ).hasClass( 'ast-page-builder-template' ) ) {
					dynamicStyle += '@media (min-width: 554px) {';
					dynamicStyle += '.ast-page-builder-template .comments-area { max-width: ' + ( 40 + parseInt( width ) ) + 'px } ';
					dynamicStyle += '}';
				}

				astra_add_dynamic_css( 'site-content-width', dynamicStyle );

		} );
	} );

	/*
	 * Full width layout
	 */
	wp.customize( 'astra-settings[header-main-menu-label]', function( setting ) {
		setting.bind( function( label ) {
			if( $('button.main-header-menu-toggle .mobile-menu-wrap .mobile-menu').length > 0 ) {
				if ( label != '' ) {
					$('button.main-header-menu-toggle .mobile-menu-wrap .mobile-menu').text(label);
				} else {
					$('button.main-header-menu-toggle .mobile-menu-wrap').remove();
				}
			} else {
				var html = $('button.main-header-menu-toggle').html();
				if( '' != label ) {
					html += '<div class="mobile-menu-wrap"><span class="mobile-menu">'+ label +'</span> </div>';
				}
				$('button.main-header-menu-toggle').html( html )
			}
		} );
	} );

	/**
	 * Apply content bg responsive css with specified selector.
	 * @param {string} selector
	 * @returns {void}
	 */
	const apply_content_bg = ( selector ) => {
		astra_apply_responsive_background_css( 'astra-settings[content-bg-obj-responsive]', selector, 'desktop' );
		astra_apply_responsive_background_css( 'astra-settings[content-bg-obj-responsive]', selector, 'tablet' );
		astra_apply_responsive_background_css( 'astra-settings[content-bg-obj-responsive]', selector, 'mobile' );
	}

	/*
	 * Layout Body Background
	 */
	astra_apply_responsive_background_css( 'astra-settings[site-layout-outside-bg-obj-responsive]', 'body, .ast-separate-container', 'desktop' );
	astra_apply_responsive_background_css( 'astra-settings[site-layout-outside-bg-obj-responsive]', 'body, .ast-separate-container', 'tablet' );
	astra_apply_responsive_background_css( 'astra-settings[site-layout-outside-bg-obj-responsive]', 'body, .ast-separate-container', 'mobile' );

	if( astraCustomizer.is_content_bg_option_to_load ) {

		var content_layout = astraCustomizer.content_layout;
		var site_layout    = astraCustomizer.site_layout;
		var blog_grid = (typeof ( wp.customize._value['astra-settings[blog-grid]'] ) != 'undefined') ? wp.customize._value['astra-settings[blog-grid]']._value : 1;
		var blog_layout = (typeof ( wp.customize._value['astra-settings[blog-layout]'] ) != 'undefined') ? wp.customize._value['astra-settings[blog-layout]']._value : 'blog-layout-1';

		var dynamicSelector = '.ast-separate-container .ast-article-single:not(.ast-related-post), .ast-separate-container .comments-area .comment-respond,.ast-separate-container .comments-area .ast-comment-list li, .ast-separate-container .ast-woocommerce-container, .ast-separate-container .error-404, .ast-separate-container .no-results, .single.ast-separate-container .site-main .ast-author-meta, .ast-separate-container .related-posts, .ast-separate-container .comments-count-wrapper, .ast-separate-container .comments-area .comments-title, .ast-single-related-posts-container, .ast-plain-container, .site-content article .ast-article-inner';

		if( 'blog-layout-1' == blog_layout && 1 != blog_grid ) {
			dynamicSelector   += ', .ast-separate-container .ast-article-inner';
		} else {
			dynamicSelector   += ', .ast-separate-container .ast-article-post';
		}

		/**
		 * Content background color
		 */
		if( 'boxed-container' == content_layout ) {
			// Case: Container -> Boxed, Site-Layout -> Any.
			dynamicSelector   += ', .ast-separate-container.ast-two-container #secondary .widget';
			apply_content_bg(dynamicSelector);
		}
		else if ( 'content-boxed-container' == content_layout ) {
			// Case: Container -> Content-Boxed, Site-Layout -> Any.
			apply_content_bg(dynamicSelector);
		} else if ( astraCustomizer.apply_content_bg_fullwidth_layouts && ( 'ast-box-layout' === site_layout || 'ast-padded-layout' === site_layout ) && ( 'plain-container' === content_layout ) ) {
			// Case: Container -> FW Contained, Site-Layout -> Max, Padded.
			var fullWidthLayoutSelector   = '.ast-box-layout.ast-plain-container .site-content, .ast-padded-layout.ast-plain-container .site-content';
			apply_content_bg(fullWidthLayoutSelector);
		}
		else if ( astraCustomizer.apply_content_bg_fullwidth_layouts && ('plain-container' === content_layout ) ) {
			// Case: Container -> FW Contained, Site-Layout -> Full-Width, Theme default.
			dynamicSelector   += ', .ast-plain-container .site-content';
			apply_content_bg(dynamicSelector);
		}
		else if ( astraCustomizer.apply_content_bg_fullwidth_layouts && ( 'page-builder' == content_layout ) && ( 'ast-box-layout' !== site_layout && 'ast-padded-layout' !== site_layout ) ) {
			// Case: Container -> FW Stretched, Site-Layout -> Full-Width, Theme default.
			dynamicSelector   += ', .ast-page-builder-template .site-content';
			apply_content_bg(dynamicSelector);
		}
	}

	wp.customize( 'astra-settings[content-bg-obj-responsive]', function( setting ) {
		setting.bind( function( color ) {
			if ( 'narrow-container' == content_layout ) {
				wp.customize.preview.send( 'refresh' );
			}
		} );
	} );

	/*
	 * Blog Custom Width
	 */
	wp.customize( 'astra-settings[blog-max-width]', function( setting ) {
		setting.bind( function( width ) {

			var dynamicStyle = '@media all and ( min-width: 921px ) {';

			if ( ! jQuery( 'body' ).hasClass( 'ast-woo-shop-archive' ) ) {
			dynamicStyle += '.blog .site-content > .ast-container,.archive .site-content > .ast-container{ max-width: ' + (  parseInt( width ) ) + 'px } ';
			}

			if (  jQuery( 'body' ).hasClass( 'ast-fluid-width-layout' ) ) {
				dynamicStyle += '.blog .site-content > .ast-container,.archive .site-content > .ast-container{ padding-left:20px; padding-right:20px; } ';
			}
				dynamicStyle += '}';
				astra_add_dynamic_css( 'blog-max-width', dynamicStyle );

		} );
	} );

	/*
	 * Single Blog Custom Width
	 */
	wp.customize( 'astra-settings[blog-single-max-width]', function( setting ) {
		setting.bind( function( width ) {

				var dynamicStyle = '@media all and ( min-width: 921px ) {';

				dynamicStyle += '.single-post .site-content > .ast-container{ max-width: ' + ( 40 + parseInt( width ) ) + 'px } ';

			if (  jQuery( 'body' ).hasClass( 'ast-fluid-width-layout' ) ) {
				dynamicStyle += '.single-post .site-content > .ast-container{ padding-left:20px; padding-right:20px; } ';
			}
				dynamicStyle += '}';
				astra_add_dynamic_css( 'blog-single-max-width', dynamicStyle );

		} );
	} );

	/*
	 * EDD Archive Custom Width
	 */
	wp.customize( 'astra-settings[edd-archive-max-width]', function( setting ) {
		setting.bind( function( width ) {

				var dynamicStyle = '.ast-edd-archive-page .site-content > .ast-container { max-width: ' + parseInt( width ) + 'px } ';

				astra_add_dynamic_css( 'edd-archive-max-width', dynamicStyle );

		} );
	} );

	/**
	 * Primary Width Option
	 */
	wp.customize( 'astra-settings[site-sidebar-width]', function( setting ) {
		setting.bind( function( width ) {

			if ( ! jQuery( 'body' ).hasClass( 'ast-no-sidebar' ) && width >= 15 && width <= 50 ) {

				var dynamicStyle = '@media (min-width: 769px) {';

				dynamicStyle += '#primary { width: ' + ( 100 - parseInt( width ) ) + '% } ';
				dynamicStyle += '#secondary { width: ' + width + '% } ';
				dynamicStyle += '}';

				astra_add_dynamic_css( 'site-sidebar-width', dynamicStyle );
			}

		} );
	} );

	/**
	 * Header Bottom Border
	 */
	wp.customize( 'astra-settings[header-main-sep]', function( setting ) {
		setting.bind( function( border ) {

			var dynamicStyle = 'body.ast-header-break-point .main-header-bar { border-bottom-width: ' + border + 'px }';

			dynamicStyle += '.ast-desktop .main-header-bar {';
			dynamicStyle += 'border-bottom-width: ' + border + 'px';
			dynamicStyle += '}';

			astra_add_dynamic_css( 'header-main-sep', dynamicStyle );

		} );
	} );

	/**
	 * Small Footer Top Border
	 */
	wp.customize( 'astra-settings[footer-sml-divider]', function( value ) {
		value.bind( function( border_width ) {
			jQuery( '.ast-small-footer' ).css( 'border-top-width', border_width + 'px' );
		} );
	} );

	/**
	 * Footer Widget Top Border
	 */
	wp.customize( 'astra-settings[footer-adv-border-width]', function( value ) {
		value.bind( function( border_width ) {
			jQuery( '.footer-adv .footer-adv-overlay' ).css( 'border-top-width', border_width + 'px' );
		} );
	} );


	wp.customize( 'astra-settings[footer-adv-border-color]', function( value ) {
		value.bind( function( border_color ) {
			jQuery( '.footer-adv .footer-adv-overlay' ).css( 'border-top-color', border_color );
		} );
	} );


	/**
	 * Small Footer Top Border Color
	 */
	wp.customize( 'astra-settings[footer-sml-divider-color]', function( value ) {
		value.bind( function( border_color ) {
			jQuery( '.ast-small-footer' ).css( 'border-top-color', border_color );
		} );
	} );

	// WooCommerce global button compatibility for new users only.
	const woo_btn_normal_sector = astraCustomizer.astra_woo_btn_global_compatibility ? ', .woocommerce a.button, .woocommerce .widget_price_filter .button, .woocommerce button.button, [CLASS*="wc-block"] button' : '';
	const woo_btn_hover_sector = astraCustomizer.astra_woo_btn_global_compatibility ? ', .woocommerce a.button:hover, .woocommerce .widget_price_filter .button:hover, .woocommerce button.button:hover, [CLASS*="wc-block"] button:hover' : '';

	/**
	 * Button Border Radius Fields
	 */
	wp.customize( 'astra-settings[button-radius-fields]', function( setting ) {
		setting.bind( function( border ) {
			let tabletBreakPoint    = astraBuilderPreview.tablet_break_point || 768,
				mobileBreakPoint    = astraBuilderPreview.mobile_break_point || 544;
			let search_button_selector = hasWordPressWidgetBlockEditor() ? ', form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' : '' ;
			let lmsButtonSelectors = ', body #ld_course_list .btn, body a.btn-blue, body a.btn-blue:visited, body a#quiz_continue_link, body .btn-join, body .learndash_checkout_buttons input.btn-join[type="button"], body #btn-join, body .learndash_checkout_buttons input.btn-join[type="submit"], body .wpProQuiz_content .wpProQuiz_button2, a.llms-button-primary, .llms-button-secondary, .llms-button-action, .llms-field-button, .llms-button-action.large';

			let globalSelector = '.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button:not(.is-style-outline) .wp-block-button__link' + astraCustomizer.v4_2_2_core_form_btns_styling +
			lmsButtonSelectors + search_button_selector + woo_btn_normal_sector;
			if ( document.body.classList.contains('edd-page' ) ) {
				globalSelector += ', .ast-edd-site-header-cart .widget_edd_cart_widget .edd_checkout a, .widget_edd_cart_widget .edd_checkout a ';
			}

			let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
					+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
					+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

			dynamicStyle += '@media (max-width: ' + tabletBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
					+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
					+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

			dynamicStyle += '@media (max-width: ' + mobileBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
					+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
					+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

			astra_add_dynamic_css( 'button-radius', dynamicStyle );

		} );
	} );

	/**
	 * Button Border Radius Fields
	 */
	wp.customize( 'astra-settings[secondary-button-radius-fields]', function( setting ) {
		setting.bind( function( border ) {
			let tabletBreakPoint    = astraBuilderPreview.tablet_break_point || 768,
				mobileBreakPoint    = astraBuilderPreview.mobile_break_point || 544;

			if( "" === border ) {
				return;
			}

			let globalSelector = '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button';

			let dynamicStyle = globalSelector + '{ border-top-left-radius :' + border['desktop']['top'] + border['desktop-unit']
					+ '; border-bottom-right-radius :' + border['desktop']['bottom'] + border['desktop-unit'] + '; border-bottom-left-radius :'
					+ border['desktop']['left'] + border['desktop-unit'] + '; border-top-right-radius :' + border['desktop']['right'] + border['desktop-unit'] + '; } ';

			dynamicStyle += '@media (max-width: ' + tabletBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['tablet']['top'] + border['tablet-unit']
					+ '; border-bottom-right-radius :' + border['tablet']['bottom'] + border['tablet-unit'] + '; border-bottom-left-radius :'
					+ border['tablet']['left'] + border['tablet-unit'] + '; border-top-right-radius :' + border['tablet']['right'] + border['tablet-unit'] + '; } } ';

			dynamicStyle += '@media (max-width: ' + mobileBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + border['mobile']['top'] + border['mobile-unit']
					+ '; border-bottom-right-radius :' + border['mobile']['bottom'] + border['mobile-unit'] + '; border-bottom-left-radius :'
					+ border['mobile']['left'] + border['mobile-unit'] + '; border-top-right-radius :' + border['mobile']['right'] + border['mobile-unit'] + '; } } ';

			astra_add_dynamic_css( 'secondary-button-radius', dynamicStyle );

		} );
	} );

	/**
	 * Header Bottom Border width
	 */
	wp.customize( 'astra-settings[header-main-sep]', function( value ) {
		value.bind( function( border ) {

			var dynamicStyle = ' body.ast-header-break-point .main-header-bar { border-bottom-width: ' + border + 'px } ';

			dynamicStyle += '.ast-desktop .main-header-bar {';
			dynamicStyle += 'border-bottom-width: ' + border + 'px';
			dynamicStyle += '}';

			astra_add_dynamic_css( 'header-main-sep', dynamicStyle );

		} );
	} );

	/**
	 * Header Bottom Border color
	 */
	wp.customize( 'astra-settings[header-main-sep-color]', function( value ) {
		value.bind( function( color ) {
			if (color == '') {
				wp.customize.preview.send( 'refresh' );
			}

			if ( color ) {

				var dynamicStyle = ' .ast-desktop .main-header-bar { border-bottom-color: ' + color + '; } ';
					dynamicStyle += ' body.ast-header-break-point .main-header-bar { border-bottom-color: ' + color + '; } ';

				astra_add_dynamic_css( 'header-main-sep-color', dynamicStyle );
			}

		} );
	} );

	/**
	 * Primary Toggle Button Color
	 */
	wp.customize( 'astra-settings[mobile-header-toggle-btn-style-color]', function( setting ) {
		setting.bind( function( toggle_button_color ) {
			if ( toggle_button_color != '' ) {
				if( jQuery( '.menu-toggle' ).hasClass( 'ast-mobile-menu-buttons-fill' ) ) {
					var dynamicStyle = '.ast-header-break-point .ast-mobile-menu-buttons-fill.menu-toggle { background: ' + toggle_button_color + '}';
				}
				else if( jQuery( '.menu-toggle' ).hasClass( 'ast-mobile-menu-buttons-outline' ) ) {
					var dynamicStyle = '.ast-header-break-point .ast-mobile-menu-buttons-outline.menu-toggle { border: 1px solid ' + toggle_button_color + '; color: ' + toggle_button_color + '}';
				}
				else {
					var dynamicStyle = '.ast-header-break-point .ast-mobile-menu-buttons-minimal.menu-toggle { color: ' + toggle_button_color + '}';
				}
				astra_add_dynamic_css( 'primary-toggle-button-color', dynamicStyle );
			}
			else{
				wp.customize.preview.send( 'refresh' );
			}
		});
	});


	astra_responsive_font_size( 'astra-settings[font-size-site-tagline]', '.site-header .site-description' );
	astra_responsive_font_size( 'astra-settings[font-size-site-title]', '.site-title' );

	astra_responsive_font_size( 'astra-settings[font-size-page-title]', 'body:not(.ast-single-post) .entry-title' );
	astra_responsive_font_size( 'astra-settings[font-size-post-meta]', '.entry-meta, .read-more' );
	astra_responsive_font_size( 'astra-settings[font-size-post-tax]', '.ast-blog-single-element.ast-taxonomy-container a' );

	astra_refresh_customizer( 'astra-settings[blog-meta-category-style]' );
	astra_refresh_customizer( 'astra-settings[blog-category-style]' );
	astra_refresh_customizer( 'astra-settings[blog-tag-style]' );
	astra_refresh_customizer( 'astra-settings[blog-post-meta-divider-type]' );
	astra_refresh_customizer( 'astra-settings[blog-meta-tag-style]' );
	astra_refresh_customizer( 'astra-settings[blog-post-content]' );

	wp.customize( 'astra-settings[post-card-border-radius]', function( setting ) {
		setting.bind( function( border ) {
			let desktopTop = border['desktop']['top'] ? border['desktop']['top'] : 0,
				desktopBottom = border['desktop']['bottom'] ? border['desktop']['bottom'] : 0,
				desktopLeft = border['desktop']['left'] ? border['desktop']['left'] : 0,
				desktopRight = border['desktop']['right'] ? border['desktop']['right'] : 0,
				tabletTop = border['tablet']['top'] ? border['tablet']['top'] : 0,
				tabletBottom = border['tablet']['bottom'] ? border['tablet']['bottom'] : 0,
				tabletLeft = border['tablet']['left'] ? border['tablet']['left'] : 0,
				tabletRight = border['tablet']['right'] ? border['tablet']['right'] : 0,
				mobileTop = border['mobile']['top'] ? border['mobile']['top'] : 0,
				mobileBottom = border['mobile']['bottom'] ? border['mobile']['bottom'] : 0,
				mobileLeft = border['mobile']['left'] ? border['mobile']['left'] : 0,
				mobileRight = border['mobile']['right'] ? border['mobile']['right'] : 0;

			let tabletBreakPoint    = astraBuilderPreview.tablet_break_point || 921,
				mobileBreakPoint    = astraBuilderPreview.mobile_break_point || 544,
				blog_layout = (typeof ( wp.customize._value['astra-settings[blog-layout]'] ) != 'undefined') ? wp.customize._value['astra-settings[blog-layout]']._value : 'blog-layout-1';

			let globalSelector = 'blog-layout-5' === blog_layout ? '.archive .ast-article-post, .blog .ast-article-post, .archive .ast-article-post:hover, .blog .ast-article-post:hover' : '.archive .ast-article-post .ast-article-inner, .blog .ast-article-post .ast-article-inner, .archive .ast-article-post .ast-article-inner:hover, .blog .ast-article-post .ast-article-inner:hover';

			let dynamicStyle = globalSelector + '{ border-top-left-radius :' + desktopTop + border['desktop-unit']
					+ '; border-bottom-right-radius :' + desktopBottom + border['desktop-unit'] + '; border-bottom-left-radius :'
					+ desktopLeft + border['desktop-unit'] + '; border-top-right-radius :' + desktopRight + border['desktop-unit'] + '; } ';

			dynamicStyle += '@media (max-width: ' + tabletBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + tabletTop + border['tablet-unit']
					+ '; border-bottom-right-radius :' + tabletBottom + border['tablet-unit'] + '; border-bottom-left-radius :'
					+ tabletLeft + border['tablet-unit'] + '; border-top-right-radius :' + tabletRight + border['tablet-unit'] + '; } } ';

			dynamicStyle += '@media (max-width: ' + mobileBreakPoint + 'px) { ' + globalSelector + '{ border-top-left-radius :' + mobileTop + border['mobile-unit']
					+ '; border-bottom-right-radius :' + mobileBottom + border['mobile-unit'] + '; border-bottom-left-radius :'
					+ mobileLeft + border['mobile-unit'] + '; border-top-right-radius :' + mobileRight + border['mobile-unit'] + '; } } ';

			astra_add_dynamic_css( 'post-card-border-radius', dynamicStyle );
		} );
	} );

	astra_css( 'astra-settings[post-card-featured-overlay]', 'background-color', '.ast-blog-layout-6-grid .ast-article-inner .post-thumb::after' );

	// Check if anchors should be loaded in the CSS for headings.
	if (true == astraCustomizer.includeAnchorsInHeadindsCss) {
		astra_responsive_font_size('astra-settings[font-size-h1]', 'h1, .entry-content h1, .entry-content h1 a');
		astra_responsive_font_size('astra-settings[font-size-h2]', 'h2, .entry-content h2, .entry-content h2 a');
		astra_responsive_font_size('astra-settings[font-size-h3]', 'h3, .entry-content h3, .entry-content h3 a');
		astra_responsive_font_size('astra-settings[font-size-h4]', 'h4, .entry-content h4, .entry-content h4 a');
		astra_responsive_font_size('astra-settings[font-size-h5]', 'h5, .entry-content h5, .entry-content h5 a');
		astra_responsive_font_size('astra-settings[font-size-h6]', 'h6, .entry-content h6, .entry-content h6 a');
	} else {
		astra_responsive_font_size('astra-settings[font-size-h1]', 'h1, .entry-content h1');
		astra_responsive_font_size('astra-settings[font-size-h2]', 'h2, .entry-content h2');
		astra_responsive_font_size('astra-settings[font-size-h3]', 'h3, .entry-content h3');
		astra_responsive_font_size('astra-settings[font-size-h4]', 'h4, .entry-content h4');
		astra_responsive_font_size('astra-settings[font-size-h5]', 'h5, .entry-content h5');
		astra_responsive_font_size('astra-settings[font-size-h6]', 'h6, .entry-content h6');
	}

	// paragraph margin bottom.
	wp.customize( 'astra-settings[para-margin-bottom]', function( value ) {
		value.bind( function( marginBottom ) {
			if ( marginBottom == '' ) {
				wp.customize.preview.send( 'refresh' );
			}

			if ( marginBottom ) {
				var dynamicStyle = ' p, .entry-content p { margin-bottom: ' + marginBottom + 'em; } ';
				astra_add_dynamic_css( 'para-margin-bottom', dynamicStyle );
			}

		} );
	} );

	// Add preview underline in customizer for content links.
	wp.customize( 'astra-settings[underline-content-links]', function( setting ) {
		setting.bind( function( value ) {
			var dynamicStyle = '';
			if ( value ) {
				dynamicStyle = '.ast-single-post .entry-content a, .ast-comment-content a:not(.ast-comment-edit-reply-wrap a), .woocommerce-js .woocommerce-product-details__short-description a { text-decoration: underline; } ';
				astra_add_dynamic_css( 'underline-content-links', dynamicStyle );
			} else {
				dynamicStyle = '.ast-single-post .entry-content a, .ast-comment-content a:not(.ast-comment-edit-reply-wrap a), .woocommerce-js .woocommerce-product-details__short-description a { text-decoration: unset; } ';
				astra_add_dynamic_css( 'underline-content-links', dynamicStyle );
			}
		} );
	} );

	if ( astraCustomizer.page_builder_button_style_css ) {
		if (true == astraCustomizer.includeAnchorsInHeadindsCss) {
			if ( 'color-typo' == astraCustomizer.elementor_default_color_font_setting || 'typo' == astraCustomizer.elementor_default_color_font_setting ) {
				astra_css('astra-settings[headings-line-height]', 'line-height', '.elementor-widget-heading h1.elementor-heading-title, .elementor-widget-heading h2.elementor-heading-title, .elementor-widget-heading h3.elementor-heading-title, .elementor-widget-heading h4.elementor-heading-title, .elementor-widget-heading h5.elementor-heading-title, .elementor-widget-heading h6.elementor-heading-title');
			}
			astra_css('astra-settings[headings-line-height]', 'line-height', 'h1, .entry-content h1, .entry-content h1 a, h2, .entry-content h2, .entry-content h2 a, h3, .entry-content h3, .entry-content h3 a, h4, .entry-content h4, .entry-content h4 a, h5, .entry-content h5, .entry-content h5 a, h6, .entry-content h6, .entry-content h6 a, .site-title, .site-title a');
		} else {
			if ( 'color-typo' == astraCustomizer.elementor_default_color_font_setting || 'typo' == astraCustomizer.elementor_default_color_font_setting ) {
				astra_css('astra-settings[headings-line-height]', 'line-height', '.elementor-widget-heading h1.elementor-heading-title, .elementor-widget-heading h2.elementor-heading-title, .elementor-widget-heading h3.elementor-heading-title, .elementor-widget-heading h4.elementor-heading-title, .elementor-widget-heading h5.elementor-heading-title, .elementor-widget-heading h6.elementor-heading-title');
			}
			astra_css('astra-settings[headings-line-height]', 'line-height', 'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a');
		}
	} else {
		if (true == astraCustomizer.includeAnchorsInHeadindsCss) {
			astra_css('astra-settings[headings-line-height]', 'line-height', 'h1, .entry-content h1, .entry-content h1 a, h2, .entry-content h2, .entry-content h2 a, h3, .entry-content h3, .entry-content h3 a, h4, .entry-content h4, .entry-content h4 a, h5, .entry-content h5, .entry-content h5 a, h6, .entry-content h6, .entry-content h6 a, .site-title, .site-title a');
		} else {
			astra_css('astra-settings[headings-line-height]', 'line-height', 'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a');
		}
	}

	// Check if anchors should be loaded in the CSS for headings.
	if (true == astraCustomizer.includeAnchorsInHeadindsCss) {
		astra_generate_outside_font_family_css('astra-settings[headings-font-family]', 'h1, .entry-content h1, .entry-content h1 a, h2, .entry-content h2, .entry-content h2 a, h3, .entry-content h3, .entry-content h3 a, h4, .entry-content h4, .entry-content h4 a, h5, .entry-content h5, .entry-content h5 a, h6, .entry-content h6, .entry-content h6 a, .site-title, .site-title a');
		astra_generate_font_weight_css( 'astra-settings[headings-font-family]', 'astra-settings[headings-font-weight]', 'font-weight', 'h1, .entry-content h1, .entry-content h1 a, h2, .entry-content h2, .entry-content h2 a, h3, .entry-content h3, .entry-content h3 a, h4, .entry-content h4, .entry-content h4 a, h5, .entry-content h5, .entry-content h5 a, h6, .entry-content h6, .entry-content h6 a, .site-title, .site-title a' );
		astra_font_extras_css( 'headings-font-extras', '.entry-content h1, .entry-content h1 a, h2, .entry-content h2, .entry-content h2 a, h3, .entry-content h3, .entry-content h3 a, h4, .entry-content h4, .entry-content h4 a, h5, .entry-content h5, .entry-content h5 a, h6, .entry-content h6, .entry-content h6 a, .site-title, .site-title a' );

	} else {
		astra_generate_outside_font_family_css('astra-settings[headings-font-family]', 'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a');
		astra_generate_font_weight_css( 'astra-settings[headings-font-family]', 'astra-settings[headings-font-weight]', 'font-weight', 'h1, .entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a' );
		astra_font_extras_css( 'headings-font-extras', '.entry-content h1, h2, .entry-content h2, h3, .entry-content h3, h4, .entry-content h4, h5, .entry-content h5, h6, .entry-content h6, .site-title, .site-title a' );
	}

	// Global link
	astra_css( 'astra-settings[link-color]', 'color', '.entry-meta, .entry-meta *');
	astra_css( 'astra-settings[link-h-color]', 'color', '.read-more a:hover, .entry-meta a:hover, .entry-meta a:hover *');

	// Footer Bar.
	astra_css( 'astra-settings[footer-color]', 'color', '.ast-small-footer' );
	astra_css( 'astra-settings[footer-link-color]', 'color', '.ast-small-footer a' );
	astra_css( 'astra-settings[footer-link-h-color]', 'color', '.ast-small-footer a:hover' );

	// Footer Bar background.
	wp.customize( 'astra-settings[footer-bg-obj]', function( value ) {
		value.bind( function( bg_obj ) {
			var dynamicStyle = ' .ast-small-footer > .ast-footer-overlay { {{css}} }';
			astra_background_obj_css( wp.customize, bg_obj, 'footer-bg-obj', dynamicStyle );
		} );
	} );

	// Footer Widgets.
	astra_css( 'astra-settings[footer-adv-wgt-title-color]', 'color', '.footer-adv .widget-title, .footer-adv .widget-title a' );
	astra_css( 'astra-settings[footer-adv-text-color]', 'color', '.footer-adv' );
	astra_css( 'astra-settings[footer-adv-link-color]', 'color', '.footer-adv a' );
	astra_css( 'astra-settings[footer-adv-link-h-color]', 'color', '.footer-adv a:hover, .footer-adv .no-widget-text a:hover, .footer-adv a:focus, .footer-adv .no-widget-text a:focus' );

	// Footer Widget background.
	wp.customize( 'astra-settings[footer-adv-bg-obj]', function( value ) {
		value.bind( function( bg_obj ) {
			var dynamicStyle = ' .footer-adv-overlay { {{css}} }';
			astra_background_obj_css( wp.customize, bg_obj, 'footer-adv-bg-obj', dynamicStyle );
		} );
	} );

	/*
	 * Woocommerce Shop Archive Custom Width
	 */
	wp.customize( 'astra-settings[shop-archive-max-width]', function( setting ) {
		setting.bind( function( width ) {

			var dynamicStyle = '@media all and ( min-width: 921px ) {';

			dynamicStyle += '.ast-woo-shop-archive .site-content > .ast-container{ max-width: ' + (  parseInt( width ) ) + 'px } ';

			if (  jQuery( 'body' ).hasClass( 'ast-fluid-width-layout' ) ) {
				dynamicStyle += '.ast-woo-shop-archive .site-content > .ast-container{ padding-left:20px; padding-right:20px; } ';
			}
				dynamicStyle += '}';
				astra_add_dynamic_css( 'shop-archive-max-width', dynamicStyle );

		} );
	} );

	//[1] Primary Menu Toggle Button Style.
	wp.customize( 'astra-settings[mobile-header-toggle-btn-style]', function( setting ) {
		setting.bind( function( icon_style ) {
			var icon_color = wp.customize('astra-settings[mobile-header-toggle-btn-color]').get();

			if ( '' === icon_color && 'fill' === icon_style ) {
				var dynamicStyle = ' [data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-toggle-icon .ast-mobile-svg { fill: #ffffff; } ';
				astra_add_dynamic_css( 'mobile-header-toggle-btn-style', dynamicStyle );
			} else {
				astra_add_dynamic_css( 'mobile-header-toggle-btn-style', '' );
			}

			var buttons = $(document).find('.ast-mobile-menu-buttons .menu-toggle');
			buttons.removeClass('ast-mobile-menu-buttons-default ast-mobile-menu-buttons-fill ast-mobile-menu-buttons-outline');
			buttons.removeClass('ast-mobile-menu-buttons-default ast-mobile-menu-buttons-fill ast-mobile-menu-buttons-minimal');
			buttons.addClass( 'ast-mobile-menu-buttons-' + icon_style );

			var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();
			var defaultColor = '#ffffff';
			var toggleButtonColor = wp.customize( 'astra-settings[mobile-header-toggle-btn-color]' ).get();

			if ( 'fill' !== icon_style ) {
				defaultColor = themeColor
			}

			var iconColor = defaultColor;

			if ( '' !== toggleButtonColor && undefined !== toggleButtonColor && null !== toggleButtonColor ) {
				iconColor = toggleButtonColor
			}

			var dynamicStyle = '[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-toggle-icon .ast-mobile-svg {';
			dynamicStyle += 'fill: ' + iconColor + ';';
			dynamicStyle +='}';

			dynamicStyle += '[data-section="section-header-mobile-trigger"] .ast-button-wrap .mobile-menu-wrap .mobile-menu {';
			dynamicStyle += 'color: ' + iconColor + ';';
			dynamicStyle +='}';

			dynamicStyle += '[data-section="section-header-mobile-trigger"] .ast-button-wrap .ast-mobile-menu-trigger-fill, [data-section="section-header-mobile-trigger"] .ast-button-wrap .ast-mobile-menu-trigger-minimal {';
			dynamicStyle += 'color: ' + iconColor + ';';
			dynamicStyle += 'border: none;';
			dynamicStyle +='}';

			dynamicStyle += '[data-section="section-header-mobile-trigger"] .ast-button-wrap .ast-mobile-menu-trigger-outline {';
			dynamicStyle += 'color: ' + iconColor + ';';
			dynamicStyle +='}';
			;
			astra_add_dynamic_css( 'mobile-header-toggle-btn-style', dynamicStyle );

		} );
	} );

	//[1] Toggle Button Border Radius.
	wp.customize( 'astra-settings[mobile-header-toggle-btn-border-radius]', function( setting ) {
		setting.bind( function( border ) {

			var dynamicStyle = '.ast-header-break-point .main-header-bar .ast-button-wrap .menu-toggle { border-radius: ' + ( parseInt( border ) ) + 'px } ';
			astra_add_dynamic_css( 'mobile-header-toggle-btn-border-radius', dynamicStyle );

		} );
	} );

	/**
	 * Primary Submenu border
	 */
	wp.customize( 'astra-settings[primary-submenu-border]', function( value ) {
		value.bind( function( border ) {
			var color = wp.customize( 'astra-settings[primary-submenu-b-color]' ).get();

			if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {

				var dynamicStyle = '.ast-desktop .main-header-menu.submenu-with-border .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'border-top-width:'   + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += 'border-color:'        + color + ';';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';

					dynamicStyle += '.ast-desktop .main-header-menu.submenu-with-border .sub-menu .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'top:-'   + border.top + 'px;';
					dynamicStyle += '}';

					// Submenu items goes outside?
					dynamicStyle += '@media (min-width: 769px){';
					dynamicStyle += '.main-header-menu .sub-menu .menu-item.ast-left-align-sub-menu:hover > .sub-menu, .main-header-menu .sub-menu .menu-item.ast-left-align-sub-menu.focus > .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'margin-left:-'   + ( +border.left + +border.right ) + 'px;';
					dynamicStyle += '}';
					dynamicStyle += '}';

				astra_add_dynamic_css( 'primary-submenu-border', dynamicStyle );
			} else {
				wp.customize.preview.send( 'refresh' );
			}
		} );
	} );
	/**
	 * Primary Submenu border COlor
	 */
	wp.customize( 'astra-settings[primary-submenu-b-color]', function( value ) {
		value.bind( function( color ) {
			var border = wp.customize( 'astra-settings[primary-submenu-border]' ).get();
			if ( '' != color ) {
				if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {

					var dynamicStyle = '.ast-desktop .main-header-menu.submenu-with-border .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'border-top-width:'   + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += 'border-color:'        + color + ';';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';

					dynamicStyle += '.ast-desktop .main-header-menu.submenu-with-border .sub-menu .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'top:-'   + border.top + 'px;';
					dynamicStyle += '}';

					// Submenu items goes outside?
					dynamicStyle += '@media (min-width: 769px){';
					dynamicStyle += '.main-header-menu .sub-menu .menu-item.ast-left-align-sub-menu:hover > .sub-menu, .main-header-menu .sub-menu .menu-item.ast-left-align-sub-menu.focus > .sub-menu';
					dynamicStyle += '{';
					dynamicStyle += 'margin-left:-'   + ( +border.left + +border.right ) + 'px;';
					dynamicStyle += '}';
					dynamicStyle += '}';

					astra_add_dynamic_css( 'primary-submenu-border-color', dynamicStyle );
				}
			} else {
				wp.customize.preview.send( 'refresh' );
			}
		} );
	} );


	/**
	 * Primary Submenu border COlor
	 */
	wp.customize('astra-settings[primary-submenu-item-b-color]', function (value) {
		value.bind(function (color) {
			var insideBorder = wp.customize('astra-settings[primary-submenu-item-border]').get();
			if ('' != color) {
				if ( true == insideBorder ) {

					var dynamicStyle = '';

					dynamicStyle += '.ast-desktop .main-header-menu.submenu-with-border .sub-menu .menu-link';
					dynamicStyle += '{';
					dynamicStyle += 'border-bottom-width:' + ( ( true === insideBorder ) ? '1px;' : '0px;' );
					dynamicStyle += 'border-color:' + color + ';';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';


					astra_add_dynamic_css('primary-submenu-item-b-color', dynamicStyle);
				}
			} else {
				wp.customize.preview.send('refresh');
			}
		});
	});

	/**
	 * Primary Submenu border COlor
	 */
	wp.customize( 'astra-settings[primary-submenu-item-border]', function( value ) {
		value.bind( function( border ) {
			var color = wp.customize( 'astra-settings[primary-submenu-item-b-color]' ).get();

			if( true === border  ) {
				var dynamicStyle = '.ast-desktop .main-header-menu.submenu-with-border .sub-menu .menu-link';
					dynamicStyle += '{';
					dynamicStyle += 'border-bottom-width:' + ( ( true === border ) ? '1px;' : '0px;' );
					dynamicStyle += 'border-color:'        + color + ';';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';

				astra_add_dynamic_css( 'primary-submenu-item-border', dynamicStyle );
			} else {
				wp.customize.preview.send( 'refresh' );
			}

		} );
	} );

	astra_css( 'astra-settings[header-main-rt-section-button-text-color]', 'color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );
	astra_css( 'astra-settings[header-main-rt-section-button-back-color]', 'background-color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );
	astra_css( 'astra-settings[header-main-rt-section-button-text-h-color]', 'color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button:hover' );
	astra_css( 'astra-settings[header-main-rt-section-button-back-h-color]', 'background-color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button:hover' );
	astra_css( 'astra-settings[header-main-rt-section-button-border-radius]', 'border-radius', '.main-header-bar .ast-container .button-custom-menu-item .ast-custom-button-link .ast-custom-button', 'px' );
	astra_css( 'astra-settings[header-main-rt-section-button-border-color]', 'border-color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );
	astra_css( 'astra-settings[header-main-rt-section-button-border-h-color]', 'border-color', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button:hover' );
	astra_responsive_spacing( 'astra-settings[header-main-rt-section-button-padding]','.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button', 'padding', ['top', 'right', 'bottom', 'left' ] );

	// Header custom button - Typography preview stylings.
	astra_generate_outside_font_family_css( 'astra-settings[primary-header-button-font-family]', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );

	astra_responsive_font_size( 'astra-settings[primary-header-button-font-size]', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );

	astra_css( 'astra-settings[primary-header-button-font-weight]', 'font-weight', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );

	astra_css( 'astra-settings[primary-header-button-line-height]', 'line-height', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );

	astra_css( 'astra-settings[primary-header-button-text-transform]', 'text-transform', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button' );

	astra_css( 'astra-settings[primary-header-button-letter-spacing]', 'letter-spacing', '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button, .ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button', 'px' );

	/**
	 * Custom Button border
	 */
	wp.customize( 'astra-settings[header-main-rt-section-button-border-size]', function( value ) {
		value.bind( function( border ) {
			if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {
				var dynamicStyle = '.main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button';
					dynamicStyle += '{';
					dynamicStyle += 'border-top-width:'  + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';

				astra_add_dynamic_css( 'header-main-rt-section-button-border-size', dynamicStyle );
			}
		} );
	} );

	var ele_border_radius_selector = '';
	var ele_border_width_selector  = '';
	var ele_padding_selector       = '';

	var v4_2_2_btns_styling_hover = '';
	var v4_2_2_btns_styling_focus = '';
	if ( astraCustomizer.v4_2_2_core_form_btns_styling && '' != astraCustomizer.v4_2_2_core_form_btns_styling ) {
		v4_2_2_btns_styling_hover = ', #comment .submit:hover, .search .search-submit:hover';
		v4_2_2_btns_styling_focus = ', #comment .submit:focus, .search .search-submit:focus';
	}

	if ( astraCustomizer.page_builder_button_style_css ) {
		if ( 'color-typo' == astraCustomizer.elementor_default_color_font_setting || 'color' == astraCustomizer.elementor_default_color_font_setting || 'font' == astraCustomizer.elementor_default_color_font_setting ) {
			ele_border_radius_selector = ', .elementor-button-wrapper .elementor-button.elementor-size-sm, .elementor-button-wrapper .elementor-button.elementor-size-xs, .elementor-button-wrapper .elementor-button.elementor-size-md, .elementor-button-wrapper .elementor-button.elementor-size-lg, .elementor-button-wrapper .elementor-button.elementor-size-xl, .elementor-button-wrapper .elementor-button';
			ele_border_width_selector = ', .elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';
			ele_padding_selector = ', .elementor-button-wrapper .elementor-button.elementor-size-sm, .elementor-button-wrapper .elementor-button.elementor-size-xs, .elementor-button-wrapper .elementor-button.elementor-size-md, .elementor-button-wrapper .elementor-button.elementor-size-lg, .elementor-button-wrapper .elementor-button.elementor-size-xl, .elementor-button-wrapper .elementor-button';
		}
	}

	/**
	 * Button border
	 */
	wp.customize( 'astra-settings[theme-button-border-group-border-size]', function( value ) {
		value.bind( function( border ) {

			var search_button_selector = hasWordPressWidgetBlockEditor() ? ', form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button, .woocommerce a.button' : '';
			var dynamicStyle = '.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_border_width_selector + search_button_selector + woo_btn_normal_sector;

			if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {
				if( astraCustomizer.gb_outline_buttons_patterns_support && ! astraCustomizer.updated_gb_outline_button_patterns ) {
					wp.customize.preview.send( 'refresh' );
				} else {
						dynamicStyle += '{';
						dynamicStyle += 'border-top-width:'  + border.top + 'px;';
						dynamicStyle += 'border-right-width:'  + border.right + 'px;';
						dynamicStyle += 'border-left-width:'   + border.left + 'px;';
						dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
						dynamicStyle += 'border-style: solid;';
						dynamicStyle += '}';

					astra_add_dynamic_css( 'theme-button-border-group-border-size', dynamicStyle );
				}
			} else {

				if( astraCustomizer.gb_outline_buttons_patterns_support && ! astraCustomizer.updated_gb_outline_button_patterns ) {
					wp.customize.preview.send( 'refresh' );
				} else {
						dynamicStyle += '{';
						dynamicStyle += 'border-top-width: 0;';
						dynamicStyle += 'border-right-width: 0;';
						dynamicStyle += 'border-left-width: 0;';
						dynamicStyle += 'border-bottom-width: 0;';
						dynamicStyle += '}';

					astra_add_dynamic_css( 'theme-button-border-group-border-size', dynamicStyle );
				}
			}
		} );
	} );

	wp.customize( 'astra-settings[secondary-theme-button-border-group-border-size]', function( value ) {
		value.bind( function( border ) {

			var dynamicStyle = '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button';

			if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {
				if( astraCustomizer.gb_outline_buttons_patterns_support && ! astraCustomizer.updated_gb_outline_button_patterns ) {
					wp.customize.preview.send( 'refresh' );
				} else {
						dynamicStyle += '{';
						dynamicStyle += 'border-top-width:'  + border.top + 'px;';
						dynamicStyle += 'border-right-width:'  + border.right + 'px;';
						dynamicStyle += 'border-left-width:'   + border.left + 'px;';
						dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
						dynamicStyle += 'border-style: solid;';
						dynamicStyle += '}';

					astra_add_dynamic_css( 'secondary-theme-button-border-group-border-size', dynamicStyle );
				}
			} else {

				if( astraCustomizer.gb_outline_buttons_patterns_support && ! astraCustomizer.updated_gb_outline_button_patterns ) {
					wp.customize.preview.send( 'refresh' );
				} else {
						dynamicStyle += '{';
						dynamicStyle += 'border-top-width: 0;';
						dynamicStyle += 'border-right-width: 0;';
						dynamicStyle += 'border-left-width: 0;';
						dynamicStyle += 'border-bottom-width: 0;';
						dynamicStyle += '}';

					astra_add_dynamic_css( 'secondary-theme-button-border-group-border-size', dynamicStyle );
				}
			}
		} );
	} );

	var search_button_selector = hasWordPressWidgetBlockEditor() ? ', form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button' : '' ;

	// Primary button padding.
	astra_responsive_spacing( 'astra-settings[theme-button-padding]','.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .woocommerce a.button, .woocommerce button.button, .woocommerce .product a.button, .woocommerce .woocommerce-message a.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce input.button,.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled], .wp-block-button .wp-block-button__link' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_padding_selector + search_button_selector, 'padding', [ 'top', 'bottom' ] );

	astra_responsive_spacing( 'astra-settings[theme-button-padding]','.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .woocommerce a.button, .woocommerce button.button, .woocommerce .product a.button, .woocommerce .woocommerce-message a.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce input.button,.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled], .wp-block-button .wp-block-button__link' + astraCustomizer.v4_2_2_core_form_btns_styling + ele_padding_selector + search_button_selector, 'padding', [ 'left', 'right' ] );

	// Secondary button padding.
	astra_responsive_spacing( 'astra-settings[secondary-theme-button-padding]','.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button', 'padding', [ 'top', 'bottom' ] );

	astra_responsive_spacing( 'astra-settings[secondary-theme-button-padding]','.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button', 'padding', [ 'left', 'right' ] );

	/**
	 * Button border
	 */
	wp.customize( 'astra-settings[transparent-header-button-border-group]', function( value ) {
		value.bind( function( value ) {

			var optionValue = JSON.parse(value);
			var border =  optionValue['header-main-rt-trans-section-button-border-size'];

			if( '' != border.top || '' != border.right || '' != border.bottom || '' != border.left ) {
				var dynamicStyle = '.ast-theme-transparent-header .main-header-bar .button-custom-menu-item .ast-custom-button-link .ast-custom-button';
					dynamicStyle += '{';
					dynamicStyle += 'border-top-width:'  + border.top + 'px;';
					dynamicStyle += 'border-right-width:'  + border.right + 'px;';
					dynamicStyle += 'border-left-width:'   + border.left + 'px;';
					dynamicStyle += 'border-bottom-width:'   + border.bottom + 'px;';
					dynamicStyle += 'border-style: solid;';
					dynamicStyle += '}';

				astra_add_dynamic_css( 'header-main-rt-trans-section-button-border-size', dynamicStyle );
			}
		} );
	} );

	// Site Title - Font family
	astra_generate_outside_font_family_css( 'astra-settings[font-family-site-title]', '.site-title, .site-title a' );

	// Site Title - Font Weight
	astra_generate_font_weight_css( 'astra-settings[font-family-site-title]', 'astra-settings[font-weight-site-title]', 'font-weight', '.site-title, .site-title a' );

	// Site Title - Font Size
	astra_responsive_font_size( 'astra-settings[font-size-site-title]', '.site-title, .site-title a' );
	astra_font_extras_css( 'font-extras-site-title', '.site-title, .site-title a' );

	// Site tagline - Font family
	astra_generate_outside_font_family_css( 'astra-settings[font-family-site-tagline]', '.site-header .site-description' );

	// Site Tagline - Font Weight
	astra_generate_font_weight_css( 'astra-settings[font-family-site-tagline]', 'astra-settings[font-weight-site-tagline]', 'font-weight', '.site-header .site-description' );

	// Site Tagline - Font Size
	astra_responsive_font_size( 'astra-settings[font-size-site-tagline]', '.site-header .site-description' );

	// Site Tagline - Line Height
	astra_css( 'astra-settings[line-height-site-tagline]', 'line-height', '.site-header .site-description' );

	// Site Tagline - Text Transform
	astra_css( 'astra-settings[text-transform-site-tagline]', 'text-transform', '.site-header .site-description' );

	var search_button_selector = hasWordPressWidgetBlockEditor() ? ', form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button, .woocommerce a.button' : woo_btn_normal_sector ;
	var search_button_hover_selector = hasWordPressWidgetBlockEditor() ? ', form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button:hover, form[CLASS*="wp-block-search__"].wp-block-search .wp-block-search__inside-wrapper .wp-block-search__button:focus, .woocommerce a.button:hover' : woo_btn_hover_sector ;

	//Compatibility with wordpress 6.1.1
	const global_builder_btn = '.wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color), .wp-block-buttons .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color)';
	const global_builder_btn_hover = ', .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:focus, .wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color):hover, .wp-block-buttons .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color):hover';

	if ( astraCustomizer.page_builder_button_style_css ) {

		var btn_color_ele = '';
		var btn_bg_color_ele = '';
		var btn_h_color_ele = '';
		var btn_bg_h_color_ele = '';
		var btn_border_color_ele = '';
		var btn_border_h_color_ele = '';

		if ( 'color-typo' == astraCustomizer.elementor_default_color_font_setting || 'color' == astraCustomizer.elementor_default_color_font_setting ) {
			// Theme Button - Text Color
			btn_color_ele = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';

			// Theme Button - Background Color
			btn_bg_color_ele = ',.elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';

			// Theme Button - Text Hover Color
			btn_h_color_ele = ',.elementor-button-wrapper .elementor-button:hover, .elementor-button-wrapper .elementor-button:focus';

			// Theme Button - Background Hover Color
			btn_bg_h_color_ele = ',.elementor-button-wrapper .elementor-button:hover, .elementor-button-wrapper .elementor-button:focus';

			// Theme Button - Border Color
			btn_border_color_ele = ', .elementor-button-wrapper .elementor-button, .elementor-button-wrapper .elementor-button:visited';

			// Theme Button - Border Hover Color
			btn_border_h_color_ele = ',.elementor-button-wrapper .elementor-button:hover, .elementor-button-wrapper .elementor-button:focus';
		}

		var btnSelector = '.menu-toggle, button, .ast-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button:not(.is-style-outline) .wp-block-button__link, .ast-custom-button' + astraCustomizer.v4_2_2_core_form_btns_styling + btn_bg_color_ele + search_button_selector + woo_btn_normal_sector;

		// Secondary button selectors.
		var btnSecondarySelector = '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button';
		var btnSecondaryColorSelector = '.wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link.wp-element-button:not(.has-text-color), .wp-block-buttons .wp-block-button.wp-block-button__link.wp-element-button.is-style-outline:not(.has-text-color), .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button';

		astraHandleButtonPresetPreview( btnSelector );

		// Theme Button - Text Hover Color
		astra_css( 'astra-settings[button-h-color]', 'color', 'button:focus, .menu-toggle:hover, button:hover, .ast-button:hover, .button:hover, input[type=reset]:hover, input[type=reset]:focus, input#submit:hover, input#submit:focus, input[type="button"]:hover, input[type="button"]:focus, input[type="submit"]:hover, input[type="submit"]:focus, .wp-block-button .wp-block-button__link:hover, .wp-block-button .wp-block-button__link:focus, .ast-custom-button:hover, .ast-custom-button:focus, .wp-block-button .uagb-buttons-repeater.wp-block-button__link:hover' + v4_2_2_btns_styling_hover + btn_h_color_ele + search_button_hover_selector + global_builder_btn_hover, '', true );

		// Theme Secondary Button - Text Hover Color
		astra_css( 'astra-settings[secondary-button-h-color]', 'color', '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:focus, .wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color):hover, .wp-block-buttons .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color):hover, .ast-outline:hover, .ast-outline:focus, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button:hover', '', true );

		// Theme Button - Background Hover Color
		astra_css( 'astra-settings[button-bg-h-color]', 'background-color', 'button:focus, .menu-toggle:hover, button:hover, .ast-button:hover, .button:hover, input[type=reset]:hover, input[type=reset]:focus, input#submit:hover, input#submit:focus, input[type="button"]:hover, input[type="button"]:focus, input[type="submit"]:hover, input[type="submit"]:focus, .wp-block-button .wp-block-button__link:hover, .wp-block-button .wp-block-button__link:focus, .ast-custom-button:hover, .ast-custom-button:focus, .wp-block-button .uagb-buttons-repeater.wp-block-button__link:hover' + v4_2_2_btns_styling_hover + btn_bg_h_color_ele + search_button_hover_selector + global_builder_btn_hover + woo_btn_hover_sector );

		// Theme Secondary Button - Background Hover Color
		astra_css( 'astra-settings[secondary-button-bg-h-color]', 'background-color', '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:focus, .wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color):hover, .wp-block-buttons .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color):hover, .ast-outline-button:hover, .ast-outline-button:focus, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button:hover' );

		astra_css( 'astra-settings[theme-button-border-group-border-color]', 'border-color', '.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .wp-block-button .wp-block-button__link' + astraCustomizer.v4_2_2_core_form_btns_styling + btn_border_color_ele + search_button_selector + ', ' + global_builder_btn + woo_btn_normal_sector );

		// Theme Secondary Button - Border Color
		astra_css( 'astra-settings[secondary-theme-button-border-group-border-color]', 'border-color', '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link.wp-element-button, .ast-outline-button, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button' );

		// Theme Button - Border Hover Color
		astra_css( 'astra-settings[theme-button-border-group-border-h-color]', 'border-color', 'button:focus, .menu-toggle:hover, button:hover, .ast-button:hover, .ast-custom-button:hover, .button:hover, input[type=reset]:hover, input[type=reset]:focus, input#submit:hover, input#submit:focus, input[type="button"]:hover, input[type="button"]:focus, input[type="submit"]:hover, input[type="submit"]:focus, .wp-block-button .wp-block-button__link:hover, .wp-block-button .wp-block-button__link:focus' + v4_2_2_btns_styling_hover + v4_2_2_btns_styling_focus + btn_border_h_color_ele + search_button_hover_selector + global_builder_btn_hover + woo_btn_hover_sector );

		// Theme Secondary Button - Border Hover Color
		astra_css( 'astra-settings[secondary-theme-button-border-group-border-h-color]', 'border-color', '.wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:hover, .wp-block-buttons .wp-block-button.is-style-outline .wp-block-button__link:focus, .wp-block-buttons .wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color):hover, .wp-block-buttons .wp-block-button.wp-block-button__link.is-style-outline:not(.has-text-color):hover, .ast-outline-button:hover, .ast-outline-button:focus, .wp-block-uagb-buttons-child .uagb-buttons-repeater.ast-outline-button:hover' );
	} else {

		var btnSelector = '.menu-toggle, button, .ast-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .ast-custom-button' + search_button_selector + woo_btn_normal_sector;

		astraHandleButtonPresetPreview( btnSelector );

		// Theme Button - Border Color
		astra_css( 'astra-settings[button-bg-color]', 'border-color', '.menu-toggle, button, .ast-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .ast-custom-button' + search_button_selector );

		// Theme Button - Text Hover Color
		astra_css( 'astra-settings[button-h-color]', 'color', 'button:focus, .menu-toggle:hover, button:hover, .ast-button:hover, .button:hover, input[type=reset]:hover, input[type=reset]:focus, input#submit:hover, input#submit:focus, input[type="button"]:hover, input[type="button"]:focus, input[type="submit"]:hover, input[type="submit"]:focus, .ast-custom-button:hover, .ast-custom-button:focus' + search_button_hover_selector );

		// Theme Button - Background Hover Color
		astra_css( 'astra-settings[button-bg-h-color]', 'background-color', 'button:focus, .menu-toggle:hover, button:hover, .ast-button:hover, .button:hover, input[type=reset]:hover, input[type=reset]:focus, input#submit:hover, input#submit:focus, input[type="button"]:hover, input[type="button"]:focus, input[type="submit"]:hover, input[type="submit"]:focus, .ast-custom-button:hover, .ast-custom-button:focus' + search_button_hover_selector );

		astra_responsive_spacing( 'astra-settings[theme-button-padding]','.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .woocommerce a.button, .woocommerce button.button, .woocommerce .product a.button, .woocommerce .woocommerce-message a.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce input.button,.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled]' + astraCustomizer.v4_2_2_core_form_btns_styling + search_button_selector, 'padding', [ 'top', 'bottom' ] );
		astra_responsive_spacing( 'astra-settings[theme-button-padding]','.menu-toggle, button, .ast-button, .ast-custom-button, .button, input#submit, input[type="button"], input[type="submit"], input[type="reset"], .woocommerce a.button, .woocommerce button.button, .woocommerce .product a.button, .woocommerce .woocommerce-message a.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce input.button,.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled]' + astraCustomizer.v4_2_2_core_form_btns_styling + search_button_selector, 'padding', [ 'left', 'right' ] );
	}

	// Global custom event which triggers when partial refresh occurs.
	wp.customize.bind('preview-ready', function () {

		wp.customize.selectiveRefresh.bind('render-partials-response', function (response) {

			if( response.contents.hasOwnProperty('astra-settings[footer-desktop-items]')
				|| ( ! ( response.contents.hasOwnProperty('astra-settings[header-desktop-items]')
					|| response.contents.hasOwnProperty('astra-settings[header-mobile-items]') ) ) ) {
				return false;
			}

			setTimeout( function () {
				document.dispatchEvent( new CustomEvent( "astLayoutWidthChanged",  { "detail": { 'response' : response } }) );
			}, 10 );

		});

		wp.customize.selectiveRefresh.bind('partial-content-rendered', function (response) {

			if( response.partial.id.includes("footer") ) {
				return false;
			}

			sessionStorage.setItem('astPartialContentRendered', true);
			document.dispatchEvent( new CustomEvent( "astPartialContentRendered",  { "detail": { 'response' : response } }) );

		});

		wp.customize.selectiveRefresh.bind('partial-content-rendered', function (response) {

			wp.customize.preview.send( 'AstraBuilderPartialContentRendered', response );

		});

		wp.customize.preview.bind( 'astPreviewDeviceChanged', function( device ) {
			document.dispatchEvent( new CustomEvent( "astPreviewDeviceChanged",  { "detail": device }) );

		} );

	})

	/**
	 * Related Posts Query Arguments - Customizer preview support.
	 */
	 wp.customize( 'astra-settings[related-posts-based-on]', function( setting ) {
		setting.bind( function() {
			wp.customize.preview.send( 'refresh' );
		} );
	} );
	wp.customize( 'astra-settings[related-posts-order-by]', function( setting ) {
		setting.bind( function() {
			wp.customize.preview.send( 'refresh' );
		} );
	} );
	wp.customize( 'astra-settings[related-posts-order]', function( setting ) {
		setting.bind( function() {
			wp.customize.preview.send( 'refresh' );
		} );
	} );

	/**
	 * Related Posts color stylings.
	 */
	astra_css( 'astra-settings[related-posts-text-color]', 'color', '.ast-related-post-content .entry-header .ast-related-post-title a, .ast-related-post-content .ast-related-post-excerpt' );
	astra_css( 'astra-settings[related-posts-meta-color]', 'color', '.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' );
	astra_css( 'astra-settings[related-posts-title-color]', 'color', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );
	astra_css( 'astra-settings[related-posts-background-color]', 'background-color', '.ast-single-related-posts-container' );
	astra_css( 'astra-settings[related-posts-link-color]', 'color', '.ast-related-post-content .ast-related-post-cta a' );
	astra_css( 'astra-settings[related-posts-link-hover-color]', 'color', '.ast-related-post-content .ast-related-post-cta a:hover' );
	astra_css( 'astra-settings[related-posts-meta-link-hover-color]', 'color', '.ast-related-post-content .entry-meta a:hover, .ast-related-post-content .entry-meta span a span:hover' );

	// Related Posts - Customizer preview for Post Title.
	astra_generate_outside_font_family_css( 'astra-settings[related-posts-title-font-family]', '.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' );
	astra_css( 'astra-settings[related-posts-title-font-weight]', 'font-weight', '.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' );
	astra_responsive_font_size( 'astra-settings[related-posts-title-font-size]', '.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' );
	astra_font_extras_css( 'related-posts-title-font-extras', '.ast-related-post-content .entry-header .ast-related-post-title, .ast-related-post-content .entry-header .ast-related-post-title a' );

	// Related Posts - Customizer preview for Section Title.
	astra_generate_outside_font_family_css( 'astra-settings[related-posts-section-title-font-family]', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );
	astra_css( 'astra-settings[related-posts-section-title-font-weight]', 'font-weight', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );
	astra_responsive_font_size( 'astra-settings[related-posts-section-title-font-size]', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );
	astra_css( 'astra-settings[releted-posts-title-alignment]', 'text-align', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );
	astra_font_extras_css( 'related-posts-section-title-font-extras', '.ast-single-related-posts-container .ast-related-posts-title-section .ast-related-posts-title' );

	// Related Posts - Customizer preview for Post Meta.
	astra_generate_outside_font_family_css( 'astra-settings[related-posts-meta-font-family]', '.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' );
	astra_css( 'astra-settings[related-posts-meta-font-weight]', 'font-weight', '.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' );
	astra_responsive_font_size( 'astra-settings[related-posts-meta-font-size]', '.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' );
	astra_font_extras_css( 'related-posts-meta-font-extras', '.ast-related-post-content .entry-meta, .ast-related-post-content .entry-meta *' );

	// Related Posts - Customizer preview for Post Content.
	astra_generate_outside_font_family_css( 'astra-settings[related-posts-content-font-family]', '.ast-related-post-content .ast-related-post-excerpt' );
	astra_css( 'astra-settings[related-posts-content-font-weight]', 'font-weight', '.ast-related-post-content .ast-related-post-excerpt' );
	astra_responsive_font_size( 'astra-settings[related-posts-content-font-size]', '.ast-related-post-content .ast-related-post-excerpt' );
	astra_font_extras_css( 'related-posts-content-font-extras', '.ast-related-post-content .ast-related-post-excerpt' );

	astra_border_spacing_advanced_css( 'ast-sub-section-related-posts', 'body .site .ast-single-related-posts-container' );
	astra_border_spacing_advanced_css( 'ast-sub-section-comments', 'body .site .comments-area' );

	// Title Color.
    astra_css(
        'astra-settings[header-color-site-title]',
        'color',
        '.ast-site-identity .site-title a, .ast-site-identity .site-title'
    );

    // Title Hover Color.
    astra_css(
        'astra-settings[header-color-h-site-title]',
        'color',
        '.ast-site-identity .site-title a:hover, .ast-site-identity .site-title:hover'
    );

	// Logo SVG Icon Color.
    astra_css(
        'astra-settings[logo-svg-icon-color]',
        'fill',
        'header .ast-logo-svg-icon svg'
    );

    // Logo SVG Icon Hover Color.
    astra_css(
        'astra-settings[logo-svg-icon-hover-color]',
        'fill',
        'header .ast-logo-svg-icon:hover svg'
    );

	// Tagline Color.
    astra_css(
        'astra-settings[header-color-site-tagline]',
        'color',
        '.ast-site-identity .site-description'
    );

	wp.customize( 'astra-settings[related-posts-author-avatar-size]', function( value ) {
		value.bind( function( size ) {
			var dynamicStyle = '';
			dynamicStyle +=  '.ast-related-post-content .ast-author-avatar img {';
			dynamicStyle += 'width: ' + size + 'px;';
			dynamicStyle += 'height: ' + size + 'px;';
			dynamicStyle += '} ';

			astra_add_dynamic_css( 'related-posts-author-avatar-size', dynamicStyle );
		} );
	} );

	astra_refresh_customizer( 'astra-settings[related-metadata-separator]' );
	astra_refresh_customizer( 'astra-settings[related-posts-image-ratio-type]' );
	astra_refresh_customizer( 'astra-settings[related-posts-image-ratio-pre-scale]' );
	astra_refresh_customizer( 'astra-settings[related-posts-image-custom-scale-width]' );
	astra_refresh_customizer( 'astra-settings[related-posts-image-custom-scale-height]' );
	astra_refresh_customizer( 'astra-settings[related-posts-image-size]' );
	astra_refresh_customizer( 'astra-settings[related-posts-author-prefix-label]' );
	astra_refresh_customizer( 'astra-settings[related-posts-author-avatar]' );
	astra_refresh_customizer( 'astra-settings[related-posts-meta-date-type]' );
	astra_refresh_customizer( 'astra-settings[related-posts-date-format]' );
	astra_refresh_customizer( 'astra-settings[related-posts-category-style]' );
	astra_refresh_customizer( 'astra-settings[related-posts-tag-style]' );

	function astraHandleButtonPresetPreview( btnSelector ) {

		wp.customize( 'astra-settings[button-preset-style]', function( setting ) {
			setting.bind( function( value ) {

				var buttonBGColor   = wp.customize( 'astra-settings[button-bg-color]' ).get();
				var buttonTextColor = wp.customize( 'astra-settings[button-color]' ).get();
				var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();

				if( 'button_04' === value || 'button_05' === value || 'button_06' === value ) {

					var buttonBorderColor = wp.customize( 'astra-settings[theme-button-border-group-border-color]' ).get();

					if( '' === buttonBorderColor ) {
						jQuery( 'style#astra-settings-theme-button-border-group-border-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-theme-button-border-group-border-color">'
							+ btnSelector + '	{ border-color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor && '' !== buttonBGColor ) {
						jQuery( 'style#astra-settings-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-button-outline-preset-color">'
							+ btnSelector + '	{ color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor && '' === buttonBGColor ) {

						jQuery( 'style#astra-settings-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-button-outline-preset-color">'
							+ btnSelector + '	{ color: ' + themeColor + ' }'
							+ '</style>'
						);
					}

					// Remove old.
					jQuery( 'style#astra-settings-button-preset-style-background-color' ).remove();

					// Concat and append new <style>.
					jQuery( 'head' ).append(
						'<style id="astra-settings-button-preset-style-background-color">'
						+ btnSelector + '	{ background: transparent }'
						+ '</style>'
					);
				}
				else {

					jQuery( 'style#astra-settings-button-bg-color-background-color' ).remove();
					jQuery( 'style#astra-settings-button-outline-preset-color' ).remove();

					if( '' === buttonTextColor && '' === buttonBGColor ) {

						jQuery( 'head' ).append(
							'<style id="astra-settings-button-bg-color-background-color">'
							+ btnSelector + '	{ background-color: ' + themeColor + ' }'
							+ '</style>'
						);

					} else {

						// Set background color for button to theme color when value is empty.
						buttonBGColor = ( '' != buttonBGColor ) ? buttonBGColor : themeColor;

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-button-bg-color-background-color">'
							+ btnSelector + '	{ background-color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor ) {

						// Set button text color to white when value is empty.
						jQuery( 'head' ).append(
							'<style id="astra-settings-button-outline-preset-color">'
							+ btnSelector + '	{ color: #FFFFFF; }'
							+ '</style>'
						);

						jQuery( 'style#astra-settings-button-color-color' ).remove();
					}
				}

			} );
		} );

		wp.customize( 'astra-settings[secondary-button-preset-style]', function( setting ) {

			var btnBgColorSelector = '.wp-block-buttons .wp-block-button .wp-block-button__link.is-style-outline:not(.has-background), .wp-block-buttons .wp-block-button.is-style-outline>.wp-block-button__link:not(.has-background), .ast-outline-button';
			setting.bind( function( value ) {

				var buttonBGColor   = wp.customize( 'astra-settings[secondary-button-bg-color]' ).get();
				var buttonTextColor = wp.customize( 'astra-settings[secondary-button-color]' ).get();
				var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();

				if( 'button_04' === value || 'button_05' === value || 'button_06' === value ) {

					var buttonBorderColor = wp.customize( 'astra-settings[secondary-theme-button-border-group-border-color]' ).get();

					if( '' === buttonBorderColor ) {
						jQuery( 'style#astra-settings-secondary-theme-button-border-group-border-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-theme-button-border-group-border-color">'
							+ btnSecondarySelector + '	{ border-color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor && '' !== buttonBGColor ) {
						jQuery( 'style#astra-settings-secondary-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-outline-preset-color">'
							+ btnSecondaryColorSelector + '	{ color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor && '' === buttonBGColor ) {

						jQuery( 'style#astra-settings-secondary-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-outline-preset-color">'
							+ btnSecondaryColorSelector + '	{ color: ' + themeColor + ' }'
							+ '</style>'
						);
					}

					// Remove old.
					jQuery( 'style#astra-settings-secondary-button-preset-style-background-color' ).remove();

					// Concat and append new <style>.
					jQuery( 'head' ).append(
						'<style id="astra-settings-secondary-button-preset-style-background-color">'
						+ btnBgColorSelector + '	{ background: transparent }'
						+ '</style>'
					);
				} else {

					jQuery( 'style#astra-settings-secondary-button-bg-color-background-color' ).remove();
					jQuery( 'style#astra-settings-secondary-button-outline-preset-color' ).remove();

					if( '' === buttonTextColor && '' === buttonBGColor ) {

						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-bg-color-background-color">'
							+ btnBgColorSelector + '	{ background-color: ' + themeColor + ' }'
							+ '</style>'
						);

					} else {

						// Set background color for button to theme color when value is empty.
						buttonBGColor = ( '' != buttonBGColor ) ? buttonBGColor : themeColor;

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-bg-color-background-color">'
							+ btnBgColorSelector + '	{ background-color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor ) {

						// Set button text color to white when value is empty.
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-outline-preset-color">'
							+ btnSecondaryColorSelector + '	{ color: #FFFFFF; }'
							+ '</style>'
						);

						jQuery( 'style#astra-settings-secondary-button-color-color' ).remove();
					}

					// Clear preset.
					if ( '' === value ) {
						wp.customize.preview.send( 'refresh' );
					}
				}

			} );
		} );

		wp.customize( 'astra-settings[button-color]', function( setting ) {
			setting.bind( function( value ) {
				if( '' === value ) {

					var buttonPreset = wp.customize( 'astra-settings[button-preset-style]' ).get();

					// If button has outline preset.
					if( 'button_04' === buttonPreset || 'button_05' === buttonPreset || 'button_06' === buttonPreset ) {

						var buttonBGColor   = wp.customize( 'astra-settings[button-bg-color]' ).get();

						jQuery( 'style#astra-settings-button-outline-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-button-color-color">'
							+ btnSelector + '	{ color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					}
					 else {
						jQuery( 'style#astra-settings-button-color-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-button-color-color">'
							+ btnSelector + '	{ color: #FFFFFF }'
							+ '</style>'
						);
					}

				} else {

					jQuery( 'style#astra-settings-button-color-color' ).remove();

					// Theme Button - Background Color
					jQuery( 'head' ).append(
						'<style id="astra-settings-button-color-color">'
						+ btnSelector + '	{ color: ' + value + ' }'
						+ '</style>'
					);
				}

			} );
		} );

		wp.customize( 'astra-settings[secondary-button-color]', function( setting ) {
			setting.bind( function( value ) {
				if( '' === value ) {

					var buttonPreset = wp.customize( 'astra-settings[secondary-button-preset-style]' ).get();
					var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();

					// If button has outline preset.
					if( 'button_04' === buttonPreset || 'button_05' === buttonPreset || 'button_06' === buttonPreset ) {

						var buttonBGColor   = wp.customize( 'astra-settings[secondary-button-bg-color]' ).get();

						jQuery( 'style#astra-settings-secondary-button-color-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-button-color-color">'
							+ btnSecondaryColorSelector + '	{ color: ' + buttonBGColor + ' }'
							+ '</style>'
						);
					} else {
						jQuery( 'style#astra-settings-button-color-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-color-color">'
							+ btnSecondaryColorSelector + '	{ color: ' + themeColor + ' }'
							+ '</style>'
						);
					}

				} else {

					jQuery( 'style#astra-settings-secondary-button-color-color' ).remove();

					// Theme Button - Background Color
					jQuery( 'head' ).append(
						'<style id="astra-settings-secondary-button-color-color">'
						+ btnSecondaryColorSelector + '	{ color: ' + value + ' }'
						+ '</style>'
					);
				}

			} );
		} );

		wp.customize( 'astra-settings[button-bg-color]', function( setting ) {
			setting.bind( function( value ) {

				var buttonPreset = wp.customize( 'astra-settings[button-preset-style]' ).get();
				var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();
				var buttonTextColor = wp.customize( 'astra-settings[button-color]' ).get();

				// If button has outline preset.
				if( 'button_04' === buttonPreset || 'button_05' === buttonPreset || 'button_06' === buttonPreset ) {

					var buttonTextColor = wp.customize( 'astra-settings[button-color]' ).get();
					var buttonBorderColor = wp.customize( 'astra-settings[theme-button-border-group-border-color]' ).get();

					if( '' === buttonBorderColor ) {
						// Theme Button - Background Color
						jQuery( 'style#astra-settings-theme-button-border-group-border-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-theme-button-border-group-border-color">'
							+ btnSelector + '	{ border-color: ' + value + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor ) {
						jQuery( 'style#astra-settings-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-button-outline-preset-color">'
							+ btnSelector + '	{ color: ' + value + ' }'
							+ '</style>'
						);
					}

				}
				 else {
					jQuery( 'style#astra-settings-button-bg-color-background-color' ).remove();
					jQuery( 'style#astra-settings-button-outline-preset-color' ).remove();

					// Set background color for button to theme color when value is empty.
					value = ( '' != value ) ? value : themeColor;

					if( '' === buttonTextColor ) {

						jQuery( 'head' ).append(
							'<style id="astra-settings-button-outline-preset-color">'
							+ btnSelector + '	{ color: #FFFFFF; }'
							+ '</style>'
						);
					}

					// Theme Button - Background Color
					jQuery( 'head' ).append(
						'<style id="stra-settings-button-bg-color-background-color">'
						+ btnSelector + '	{ background-color: ' + value + ' }'
						+ '</style>'
					);
				}
			} );
		} );

		wp.customize( 'astra-settings[secondary-button-bg-color]', function( setting ) {
			var btnBgColorSelector = '.wp-block-buttons .wp-block-button .wp-block-button__link.is-style-outline:not(.has-background), .wp-block-buttons .wp-block-button.is-style-outline>.wp-block-button__link:not(.has-background), .ast-outline-button';

			setting.bind( function( value ) {
				var buttonPreset = wp.customize( 'astra-settings[secondary-button-preset-style]' ).get();
				var themeColor = wp.customize( 'astra-settings[theme-color]' ).get();
				var buttonTextColor = wp.customize( 'astra-settings[secondary-button-color]' ).get();

				var buttonTextColor = wp.customize( 'astra-settings[secondary-button-color]' ).get();
				var buttonBorderColor = wp.customize( 'astra-settings[secondary-theme-button-border-group-border-color]' ).get();

				// If button has outline preset.
				if( 'button_04' === buttonPreset || 'button_05' === buttonPreset || 'button_06' === buttonPreset ) {

					var buttonTextColor = wp.customize( 'astra-settings[secondary-button-color]' ).get();
					var buttonBorderColor = wp.customize( 'astra-settings[secondary-theme-button-border-group-border-color]' ).get();

					if( '' === buttonBorderColor ) {

						// Theme Button - Background Color
						jQuery( 'style#astra-settings-secondary-theme-button-border-group-border-color' ).remove();

						// Theme Button - Background Color
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-theme-button-border-group-border-color">'
							+ btnSecondarySelector + '	{ border-color: ' + value + ' }'
							+ '</style>'
						);
					}

					if( '' === buttonTextColor ) {
						jQuery( 'style#astra-settings-secondary-button-outline-preset-color' ).remove();

						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-outline-preset-color">'
							+ btnSecondaryColorSelector + '	{ color: ' + value + ' }'
							+ '</style>'
						);
					}

					jQuery( 'style#astra-settings-secondary-button-bg-color-background-color' ).remove();

					jQuery( 'head' ).append(
						'<style id="astra-settings-secondary-button-bg-color-background-color">'
						+ btnSecondarySelector + '	{ background-color: transparent }'
						+ '</style>'
					);

				} else if ( 'button_01' === buttonPreset || 'button_02' === buttonPreset || 'button_03' === buttonPreset ) {
					jQuery( 'style#astra-settings-secondary-button-outline-preset-color' ).remove();

					jQuery( 'style#astra-settings-secondary-button-bg-color-background-color' ).remove();

					jQuery( 'head' ).append(
						'<style id="astra-settings-secondary-button-bg-color-background-color">'
						+ btnSecondarySelector + '	{ background-color: ' + value + ' }'
						+ '</style>'
					);

					// Set background color for button to theme color when value is empty.
					value = ( '' != value ) ? value : themeColor;

					if( '' === buttonTextColor ) {

						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-button-outline-preset-color">'
							+ btnSecondaryColorSelector + '	{ color: #FFFFFF; }'
							+ '</style>'
						);
					}
				} else {
					var buttonTextColor = wp.customize( 'astra-settings[secondary-button-color]' ).get();
					var buttonBorderColor = wp.customize( 'astra-settings[secondary-theme-button-border-group-border-color]' ).get();

					if ( '' === buttonBorderColor ) {
						jQuery( 'head' ).append(
							'<style id="astra-settings-secondary-theme-button-border-group-border-color">'
							+ btnSecondarySelector + '	{ border-color: ' + value + ' }'
							+ '</style>'
						);
					}

					jQuery( 'style#astra-settings-secondary-button-bg-color-background-color' ).remove();

					jQuery( 'head' ).append(
						'<style id="astra-settings-secondary-button-bg-color-background-color">'
						+ btnSecondarySelector + '	{ background-color: ' + value + ' }'
						+ '</style>'
					);
				}
			} );
		} );

		/**
		 * Cart Count Color.
		 */
		wp.customize( 'astra-settings[woo-header-cart-product-count-color]', function( setting ) {
			setting.bind( function( color ) {
				if( color ) {
					var dynamicStyle = '.ast-menu-cart-outline .ast-cart-menu-wrap .count, .ast-site-header-cart .ast-addon-cart-wrap i.astra-icon:after { color: ' + color + '; } ';
					astra_add_dynamic_css( 'woo-header-cart-product-count-color', dynamicStyle );
				} else {
					wp.customize.preview.send( 'refresh' );
				}
			} );
		} );

		/**
		 * Cart Count Color Hover.
		 */
		wp.customize( 'astra-settings[woo-header-cart-product-count-h-color]', function( setting ) {
			setting.bind( function( color ) {
				if( color ) {
					var dynamicStyle = '.ast-site-header-cart .ast-site-header-cart-li:hover .ast-cart-menu-wrap .count .ast-count-text, .ast-site-header-cart .ast-site-header-cart-li:hover .ast-addon-cart-wrap i.astra-icon:after { color: ' + color + '; } ';
					astra_add_dynamic_css( 'woo-header-cart-product-count-h-color', dynamicStyle );
				} else {
					wp.customize.preview.send( 'refresh' );
				}

			} );
		} );

		wp.customize('astra-settings[single-product-cart-button-width]', function (value) {
			value.bind(function (size) {
				var tablet_break_point = astraBuilderPreview.tablet_break_point || 768,
				mobile_break_point = astraBuilderPreview.mobile_break_point || 544;
				if (size.desktop != '' || size.tablet != '' || size.mobile != '') {
					var dynamicStyle = '';
					dynamicStyle += '.woocommerce div.product form.cart .button.single_add_to_cart_button {';
					dynamicStyle += 'width: ' + size.desktop + '%' + ';';
					dynamicStyle += '} ';
					dynamicStyle += '@media (max-width: ' + tablet_break_point + 'px) {';
					dynamicStyle += '.woocommerce div.product form.cart .button.single_add_to_cart_button {';
					dynamicStyle += 'width: ' + size.tablet + '%' + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';

					dynamicStyle += '@media (max-width: ' + mobile_break_point + 'px) {';
					dynamicStyle += '.woocommerce div.product form.cart .button.single_add_to_cart_button {';
					dynamicStyle += 'width: ' + size.mobile + '%' + ';';
					dynamicStyle += '} ';
					dynamicStyle += '} ';
					astra_add_dynamic_css('header-woo-cart-icon-size', dynamicStyle);
				} else {
					wp.customize.preview.send( 'refresh' );
				}
			});
		});

		// Single product Sticky add to cart.
		const astraStickyAddToCartBtnColor = '.woocommerce .ast-sticky-add-to-cart .button.alt';
		const astraStickyAddToCartBtnHover = '.woocommerce .ast-sticky-add-to-cart .button.alt:hover';

		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-n-color]', 'color', astraStickyAddToCartBtnColor );
		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-h-color]', 'color', astraStickyAddToCartBtnHover );

		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-bg-n-color]', 'background', astraStickyAddToCartBtnColor );
		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-bg-h-color]', 'background', astraStickyAddToCartBtnHover );

		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-bg-n-color]', 'border-color',astraStickyAddToCartBtnColor );
		astra_css( 'astra-settings[single-product-sticky-add-to-cart-btn-bg-h-color]', 'border-color', astraStickyAddToCartBtnHover );

		astra_css( 'astra-settings[single-product-sticky-add-to-cart-text-color]', 'color', '.ast-sticky-add-to-cart .ast-container .ast-sticky-add-to-cart-content' );
		astra_css( 'astra-settings[single-product-sticky-add-to-cart-bg-color]', 'background-color', '.ast-sticky-add-to-cart');

		wp.customize( 'astra-settings[single-product-sticky-add-to-cart-position]', function( setting ) {
			setting.bind( function( position  ) {

				var dynamicStyle = '';

				if( 'top' === position ) {
					dynamicStyle += 'div.ast-sticky-add-to-cart{';
					dynamicStyle += 'top: 0;';
					dynamicStyle += 'bottom: initial;';
					dynamicStyle += 'transform: translate(0, -100%);';
					dynamicStyle += 'box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.1), 0px 1px 9px rgba(0, 0, 0, 0.06);';
					dynamicStyle += 'opacity: 0';
					dynamicStyle += '}';
				} else {
					dynamicStyle += 'div.ast-sticky-add-to-cart{';
					dynamicStyle += 'bottom: 0;';
					dynamicStyle += 'top: initial;';
					dynamicStyle += 'transform: translate(0, 100%);';
					dynamicStyle += 'box-shadow: 0px -1px 10px rgba(0, 0, 0, 0.1), 0px -1px 9px rgba(0, 0, 0, 0.06);';
					dynamicStyle += 'opacity: 0';
					dynamicStyle += '}';
				}

				astra_add_dynamic_css( 'sticky-add-to-cart-position', dynamicStyle );
			} );
		} );

		wp.customize( 'astra-settings[single-product-payment-icon-color]', function( setting ) {
			setting.bind( function( value ) {
				wp.customize.preview.send( 'refresh' );
			} );
		} );

		wp.customize( 'astra-settings[single-product-payment-text]', function( setting ) {
			setting.bind( function( text ) {
				const paymentText = document.querySelector('.ast-single-product-payments legend');
				if( paymentText ) {
					paymentText.textContent = text;
				}
			} );
		} );

		wp.customize( 'astra-settings[single-product-payment-list]', function( setting ) {
			setting.bind( function( value ) {
				wp.customize.preview.send( 'refresh' );
			} );
		} );

	}

} )( jQuery );
