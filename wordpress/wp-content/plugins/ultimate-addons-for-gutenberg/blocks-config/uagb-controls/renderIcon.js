/**
 * Set inline CSS class.
 *
 * @param {Object} props - The block object.
 * @return {Array} The inline CSS class.
 */

import parseSVG from './parseIcon';
function renderSVG( svg, setAttributes = false, extraProps = {} ) {
	svg = parseSVG( svg );
	let fontAwesome;
	// Load Polyfiller Array if needed.
	if ( 0 !== uagb_blocks_info.font_awesome_5_polyfill.length ) {
		fontAwesome = uagb_blocks_info.uagb_svg_icons[ svg ];
		if ( ! fontAwesome ) {
			fontAwesome = uagb_blocks_info.uagb_svg_icons[ uagb_blocks_info.font_awesome_5_polyfill?.[ svg ] ];
		}
	}

	if ( ! fontAwesome ) {
		return null;
	}
	
	const fontAwesomeSvg = fontAwesome.svg?.brands ? fontAwesome.svg.brands : fontAwesome.svg.solid;

	const viewBox = `0 0 ${fontAwesomeSvg.width} ${fontAwesomeSvg.height}`;
	const path = fontAwesomeSvg.path;

	let align = null;

	switch ( svg ) {
		case 'align-center':
			align = { fillRule:'evenodd', clipRule:'evenodd', d : 'M4 2H14V0H4V2ZM0 7H18V5H0V7ZM4 12H14V10H4V12Z' };
			break;
		case 'align-left':
			align = { fillRule:'evenodd', clipRule:'evenodd', d : 'M10 2H0V0H10V2ZM18 7H0V5H18V7ZM10 12H0V10H10V12Z' };
			break;
		case 'align-right':
			align = { fillRule:'evenodd', clipRule:'evenodd', d : 'M8 2H18V0H8V2ZM0 7H18V5H0V7ZM8 12H18V10H8V12Z' };
			break;
		case 'align-justify':
			align = { d : 'M0 0H18V2H0V0ZM0 5.00001H18V7.00001H0V5.00001ZM0 10H18V12H0V10Z' };
			break;
	}

	if ( align ) {
		return <svg width="18" height="12" viewBox="0 0 18 12" xmlns="http://www.w3.org/2000/svg">
			<path { ...align } />
		</svg>
	}

	return ! setAttributes || 'not_set' === setAttributes ? (
		<svg xmlns="https://www.w3.org/2000/svg" viewBox={ viewBox } {...extraProps}>
			<path d={ path }></path>
		</svg>
	) : (
		<svg width="20" height="20" xmlns="https://www.w3.org/2000/svg" viewBox={ viewBox } {...extraProps}>
			<path d={ path }></path>
		</svg>
	);
}

export default renderSVG;
