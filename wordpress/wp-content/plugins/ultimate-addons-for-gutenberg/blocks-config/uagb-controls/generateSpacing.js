/* eslint-disable no-nested-ternary */
import generateCSSUnit from './generateCSSUnit';

export default function generateSpacing( unit, top, right = NaN, bottom = NaN, left = NaN ) {
	return ! Number.isNaN( right )
		? ! Number.isNaN( bottom )
			? ! Number.isNaN( left )
				? `${ generateCSSUnit( top, unit ) } ${ generateCSSUnit( right, unit ) } ${ generateCSSUnit(
						bottom,
						unit
				  ) } ${ generateCSSUnit( left, unit ) }`
				: `${ generateCSSUnit( top, unit ) } ${ generateCSSUnit( right, unit ) } ${ generateCSSUnit(
						bottom,
						unit
				  ) }`
			: `${ generateCSSUnit( top, unit ) } ${ generateCSSUnit( right, unit ) }`
		: generateCSSUnit( top, unit );
}
