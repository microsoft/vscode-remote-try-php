const maybeGetColorForVariable = ( color ) => {
	// This external condition handles the following color format:
	// `var(--paletteColor7)`
	if ( color && color.includes( 'var' ) ) {
		const style = window.getComputedStyle( document.body );

		// Slice off `var(` and the slice off the `)` bracket at the end.
		color = color.slice( 4 ).slice( 0, -1 );

		// This nested condition handles the following color format:
		// `var(--paletteColor7, #FBFBFB)`
		if ( color.includes( ',' ) ) {
			color = color.split( ',' ).pop().trim();

			return color;
		}

		color = style.getPropertyValue( color ).trim();
	}
	return color;
};

export default maybeGetColorForVariable;
