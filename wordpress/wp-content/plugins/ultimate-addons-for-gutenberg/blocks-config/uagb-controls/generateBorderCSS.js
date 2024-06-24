import generateCSSUnit from '@Controls/generateCSSUnit';
const generateBorderCSS = ( attributes, prefix, deviceType = 'desktop' ) => {
	if ( 'default' !== attributes[ prefix + 'BorderStyle' ] ) {
		switch ( deviceType ) {
			case 'tablet':
				deviceType = 'Tablet';
				break;
			case 'mobile':
				deviceType = 'Mobile';
				break;
			default:
				deviceType = '';
		}

		const borderCSS = {};
		const borderStyle = attributes[ prefix + 'BorderStyle' ];
		const borderColor = attributes[ prefix + 'BorderColor' ];

		const borderTopWidth = generateCSSUnit( attributes[ prefix + 'BorderTopWidth' + deviceType ], 'px' );
		const borderRightWidth = generateCSSUnit( attributes[ prefix + 'BorderRightWidth' + deviceType ], 'px' );
		const borderBottomWidth = generateCSSUnit( attributes[ prefix + 'BorderBottomWidth' + deviceType ], 'px' );
		const borderLeftWidth = generateCSSUnit( attributes[ prefix + 'BorderLeftWidth' + deviceType ], 'px' );

		const unitFallback = attributes[ prefix + 'BorderRadiusUnit' + deviceType ] || 'px';

		const borderTopLeftRadius = generateCSSUnit(
			attributes[ prefix + 'BorderTopLeftRadius' + deviceType ],
			unitFallback
		);
		const borderTopRightRadius = generateCSSUnit(
			attributes[ prefix + 'BorderTopRightRadius' + deviceType ],
			unitFallback
		);
		const borderBottomRightRadius = generateCSSUnit(
			attributes[ prefix + 'BorderBottomRightRadius' + deviceType ],
			unitFallback
		);
		const borderBottomLeftRadius = generateCSSUnit(
			attributes[ prefix + 'BorderBottomLeftRadius' + deviceType ],
			unitFallback
		);

		if ( 'none' !== attributes[ prefix + 'BorderStyle' ] && '' !== attributes[ prefix + 'BorderStyle' ] ) {
			borderCSS[ 'border-top-width' ] = borderTopWidth;
			borderCSS[ 'border-right-width' ] = borderRightWidth;
			borderCSS[ 'border-bottom-width' ] = borderBottomWidth;
			borderCSS[ 'border-left-width' ] = borderLeftWidth;
			borderCSS[ 'border-color' ] = borderColor;
		}
		borderCSS[ 'border-style' ] = borderStyle;
		borderCSS[ 'border-top-left-radius' ] = borderTopLeftRadius;
		borderCSS[ 'border-top-right-radius' ] = borderTopRightRadius;
		borderCSS[ 'border-bottom-right-radius' ] = borderBottomRightRadius;
		borderCSS[ 'border-bottom-left-radius' ] = borderBottomLeftRadius;
		return borderCSS;
	}

	// In case of 'default' border style, we return an empty object.
	return {};
};

export default generateBorderCSS;
