/* eslint-disable no-nested-ternary */
import generateCSSUnit from './generateCSSUnit';

export default function generateBorderRadius( unit, topLeft, topRight = NaN, bottomRight = NaN, bottomLeft = NaN ) {
	return ! Number.isNaN( topRight )
		? ! Number.isNaN( bottomRight )
			? ! Number.isNaN( bottomLeft )
				? `${ generateCSSUnit( topLeft, unit ) } ${ generateCSSUnit( topRight, unit ) } ${ generateCSSUnit(
						bottomRight,
						unit
				  ) } ${ generateCSSUnit( bottomLeft, unit ) }`
				: `${ generateCSSUnit( topLeft, unit ) } ${ generateCSSUnit( topRight, unit ) } ${ generateCSSUnit(
						bottomRight,
						unit
				  ) }`
			: `${ generateCSSUnit( topLeft, unit ) } ${ generateCSSUnit( topRight, unit ) }`
		: generateCSSUnit( topLeft, unit );
}
