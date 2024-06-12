export default function getPrecisePercentage( divisions ) {
	const matchedPercent = parseFloat( ( 100 / divisions ).toString().match( /^-?\d+(?:\.\d{0,2})?/ )[ 0 ] );
	return `${ matchedPercent }%`;
}
