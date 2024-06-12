import React from 'react';
import './style.scss';

const ColorPalettes = ( { selected, options, onChange, tabIndex, type } ) => {
	const handleKeyPress = ( e, palette ) => {
		e = e || window.event;

		if ( e.keyCode === 38 ) {
			//Up Arrow
			if ( e.target.previousSibling ) {
				e.target.previousSibling.focus();
			}
		} else if ( e.keyCode === 40 ) {
			//Down Arrow
			if ( e.target.nextSibling ) {
				e.target.nextSibling.focus();
			}
		} else if ( e.key === 'Enter' ) {
			//Enter
			onChange( e, palette );
		}
	};

	return (
		<div className={ `ist-color-palettes st-${ type }-style-pallete` }>
			{ Object.values( options ).map( ( palette, paletteIndex ) => {
				const title = palette.title || '';
				const firstColor = palette.colors[ 0 ] || '';
				const secondColor = palette.colors[ 1 ] || '';
				const thirdColor = palette.colors[ 2 ] || '';
				const fourthColor = palette.colors[ 3 ] || '';
				const fifthColor = palette.colors[ 4 ] || '';

				return (
					<div
						key={ paletteIndex }
						className={ `ist-color-palette ${
							palette.slug === selected
								? 'ist-color-palette-active'
								: ''
						}` }
						onClick={ ( event ) => {
							onChange( event, palette );
						} }
						onKeyDown={ ( event ) => {
							handleKeyPress( event, palette );
						} }
						tabIndex={ tabIndex }
					>
						{ type === 'default' && (
							<div className="ist-colors-title">{ title }</div>
						) }
						<div className="ist-colors-list">
							<div
								className="ist-palette-color"
								style={ { backgroundColor: firstColor } }
							/>
							<div
								className="ist-palette-color"
								style={ { backgroundColor: secondColor } }
							/>
							<div
								className="ist-palette-color"
								style={ {
									backgroundColor: thirdColor,
								} }
							/>
							<div
								className="ist-palette-color"
								style={ {
									backgroundColor: fourthColor,
								} }
							/>
							<div
								className="ist-palette-color"
								style={ {
									backgroundColor: fifthColor,
								} }
							/>
						</div>
					</div>
				);
			} ) }
		</div>
	);
};

export default ColorPalettes;
