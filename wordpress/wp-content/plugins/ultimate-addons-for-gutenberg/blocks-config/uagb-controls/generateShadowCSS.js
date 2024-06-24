import generateCSSUnit from '@Controls/generateCSSUnit';

/**
 * Generate the Box Shadow or Text Shadow CSS.
 *
 * For Text Shadow CSS:
 * { spread, position } should not be sent as params during the function call.
 * { spreadUnit } will have no effect.
 *
 * For Box/Text Shadow Hover CSS:
 * { altColor } should be set as the attribute used for { color } in Box/Text Shadow Normal CSS.
 *
 * @param {Object} shadowProperties                       The object of properties used to generate the Box/Text Shadow CSS.
 * @param {number|undefined} shadowProperties.horizontal  The horizontal value.
 * @param {number|undefined} shadowProperties.vertical    The vertical value.
 * @param {number|undefined} shadowProperties.blur        The blur value.
 * @param {number|undefined} shadowProperties.spread      The spread value.
 * @param {string} shadowProperties.horizontalUnit        The horizontal unit, defaults to 'px'.
 * @param {string} shadowProperties.verticalUnit          The vertical unit, defaults to 'px'.
 * @param {string} shadowProperties.blurUnit              The blur unit, defaults to 'px'.
 * @param {string} shadowProperties.spreadUnit            The spread unit, defaults to 'px'.
 * @param {string|undefined} shadowProperties.color       The shadow color.
 * @param {string} shadowProperties.position              The inset/outset position.
 * @param {string} shadowProperties.altColor              An alternate color to use for hover if color is undefined.
 * @return {string}                                       The generated css, or empty string if required properties aren't set.
 *
 * @since 2.5.0
 */
const generateShadowCSS = ( shadowProperties ) => {
	let {
		horizontal,
		vertical,
		blur,
		spread = undefined,
		horizontalUnit = 'px',
		verticalUnit = 'px',
		blurUnit = 'px',
		spreadUnit = 'px',
		color,
		position = 'outset',
		altColor = '',
	} = shadowProperties;

	// Although optional, color is required for Sarafi on PC. Return early if color isn't set.
	if ( ! color && ! altColor ) {
		return '';
	}

	// Get the CSS units for the number properties.
	horizontal = generateCSSUnit( horizontal, horizontalUnit );
	if ( '' === horizontal ) {
		horizontal = 0;
	}

	vertical = generateCSSUnit( vertical, verticalUnit );
	if ( '' === vertical ) {
		vertical = 0;
	}

	blur = generateCSSUnit( blur, blurUnit );
	if ( '' === blur ) {
		blur = 0;
	}

	spread = generateCSSUnit( spread, spreadUnit );
	if ( '' === spread ) {
		spread = 0;
	}

	// If all numeric unit values are exactly 0, don't render the CSS.
	if ( 0 === horizontal && 0 === vertical && 0 === blur && 0 === spread ) {
		return '';
	}

	// Return the CSS with horizontal, vertical, blur, and color - and conditionally render spread and position.
	return `${ horizontal } ${ vertical } ${ blur }${ spread ? ` ${ spread }` : '' } ${ color ? color : altColor }${
		'outset' === position ? '' : ` ${ position }`
	}`;
};

export default generateShadowCSS;
