function generateCSSUnit( value, unit = '', isImportant = false ) {
	if ( isNaN( value ) || value === '' ) {
		return '';
	}

	return value + unit + ( isImportant ? ' !important' : '' );
}

export default generateCSSUnit;
