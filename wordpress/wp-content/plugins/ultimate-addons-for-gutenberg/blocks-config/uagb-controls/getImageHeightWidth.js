async function getImageHeightWidth( url, setAttributes, onlyHas = null ) {
	// onlyHas is an object with the following properties:
	// onlyHas: {
	//     type: 'width' || 'height',
	//     value: attributeValue,
	// }
	/* eslint-disable no-undef */
	const img = new Image();
	img.addEventListener( 'load', function () {
		const imgTagWidth =
			'height' === onlyHas?.type
				? parseInt( ( onlyHas.value * this?.naturalWidth ) / this?.naturalHeight )
				: this?.naturalWidth;
		const imgTagHeight =
			'width' === onlyHas?.type
				? parseInt( ( onlyHas.value * this?.naturalHeight ) / this?.naturalWidth )
				: this?.naturalHeight;
		setAttributes( {
			// eslint-disable-next-line no-nested-ternary
			imgTagHeight: isNaN( imgTagHeight ) ? ( onlyHas !== null ? onlyHas?.value : imgTagHeight ) : imgTagHeight,
		} );
		setAttributes( {
			// eslint-disable-next-line no-nested-ternary
			imgTagWidth: isNaN( imgTagWidth ) ? ( onlyHas !== null ? onlyHas?.value : imgTagWidth ) : imgTagWidth,
		} );
	} );
	img.src = url;
}
export default getImageHeightWidth;
