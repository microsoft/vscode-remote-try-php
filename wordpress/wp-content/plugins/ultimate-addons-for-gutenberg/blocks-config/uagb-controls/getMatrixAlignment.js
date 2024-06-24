export default function getMatrixAlignment( attribute, position, format = '' ) {
	let requiredAlignment = attribute.split( ' ' )[ position - 1 ];
	switch ( format ) {
		case 'flex':
			switch ( requiredAlignment ) {
				case 'top':
				case 'left':
					requiredAlignment = 'flex-start';
					break;
				case 'bottom':
				case 'right':
					requiredAlignment = 'flex-end';
					break;
			}
			break;
	}
	return requiredAlignment;
}
