/**
 * Get HEX color and return RGBA. Default return RGB color.
 *
 * @param {string} color - The color string.
 * @return {boolean} opacity The inline CSS class.
 */

function hexToRgba( color, opacity ) {
	if ( ! color ) {
		return '';
	}

	if ( 'undefined' === typeof opacity || '' === opacity ) {
		opacity = 100;
	}

	color = color.replace( '#', '' );

	opacity = typeof opacity !== 'undefined' ? opacity / 100 : 1;

	// Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	color = color.replace( shorthandRegex, function ( m, r, g, b ) {
		return r + r + g + g + b + b;
	} );

	const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( color );

	const parsed_color = result
		? {
				r: parseInt( result[ 1 ], 16 ),
				g: parseInt( result[ 2 ], 16 ),
				b: parseInt( result[ 3 ], 16 ),
		  }
		: null;

	if ( parsed_color ) {
		return 'rgba(' + parsed_color.r + ',' + parsed_color.g + ',' + parsed_color.b + ',' + opacity + ')';
	}

	return '';
}

export default hexToRgba;
