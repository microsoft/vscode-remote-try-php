import generateCSSUnit from '@Controls/generateCSSUnit';

const generateBackgroundCSS = ( backgroundAttributes, pseudoElementOverlay = {} ) => {
	const {
		backgroundType,
		backgroundImage,
		backgroundColor,
		gradientValue,
		backgroundRepeat,
		backgroundPosition,
		backgroundSize,
		backgroundAttachment,
		backgroundCustomSize,
		backgroundCustomSizeType,
		backgroundImageColor,
		overlayType,
		overlayOpacity,
		backgroundVideoColor,
		backgroundVideo,
		customPosition,
		centralizedPosition,
		xPosition,
		xPositionType,
		yPosition,
		yPositionType,
		gradientColor1,
		gradientColor2,
		gradientLocation1,
		gradientLocation2,
		gradientType,
		gradientAngle,
		selectGradient,

		//image overlay
		backgroundOverlayImage,
		backgroundOverlayRepeat,
		backgroundOverlayPosition,
		backgroundOverlaySize,
		backgroundOverlayAttachment,
		backgroundCustomOverlaySize,
		backgroundCustomOverlaySizeType,
		customOverlayPosition,
		xPositionOverlay,
		xPositionOverlayType,
		yPositionOverlay,
		yPositionOverlayType,
		blendMode,
		globalBlockStyleId,
	} = backgroundAttributes;

	const bgCSS = {};
	const bgOverlayCSS = {};
	
	const xPositionValue = isNaN( xPosition ) || '' === xPosition ? 0 : xPosition;
	const xPositionTypeValue = undefined !== xPositionType ? xPositionType : '';
	const yPositionValue = isNaN( yPosition ) || '' === yPosition ? 0 : yPosition;
	const yPositionTypeValue = undefined !== yPositionType ? yPositionType : '';

	const xPositionOverlayValue = ( 'number' !== typeof xPositionOverlay ) ? 0 : xPositionOverlay;
	const xPositionOverlayTypeValue = undefined !== xPositionOverlayType ? xPositionOverlayType : '';
	const yPositionOverlayValue = ( 'number' !== typeof yPositionOverlay ) ? 0 : yPositionOverlay;
	const yPositionOverlayTypeValue = undefined !== yPositionOverlayType ? yPositionOverlayType : '';

	const customXPosition = generateCSSUnit( xPositionValue, xPositionTypeValue );
	const customYPosition = generateCSSUnit( yPositionValue, yPositionTypeValue );

	// Handle the Overlay Opacity.
	const applyOverlayOpacity = () => {
		if ( undefined !== overlayOpacity && '' !== overlayOpacity ) {
			bgOverlayCSS.opacity = `${ overlayOpacity }`;
		}
	};

	// Handle the Gradient Properties.
	let gradient;

	switch ( selectGradient ) {
		case 'basic':
			gradient = gradientValue;
			break;
		case 'advanced':
			switch ( gradientType ) {
				case 'linear':
					gradient = `linear-gradient(${ gradientAngle }deg, ${ gradientColor1 } ${ gradientLocation1 }%, ${ gradientColor2 } ${ gradientLocation2 }%)`;
					break;
				case 'radial':
					gradient = `radial-gradient( at center center, ${ gradientColor1 } ${ gradientLocation1 }%, ${ gradientColor2 } ${ gradientLocation2 }%)`;
					break;
				default:
					gradient = '';
					break;
			}
			break;
		default:
			gradient = '';
			break;
	}

	// Handle the Background Size Properties.
	let backgroundSizeValue = backgroundSize;

	if ( 'custom' === backgroundSize ) {
		backgroundSizeValue = backgroundCustomSize + backgroundCustomSizeType;
	}

	// Handle the Background Properties along with Overlay if Needed.
	if ( undefined !== backgroundType && '' !== backgroundType ) {
		if ( 'color' === backgroundType ) {
			if (
				'' !== backgroundColor &&
				undefined !== backgroundColor &&
				'unset' !== backgroundColor &&
				backgroundImage?.url
			) {
				bgCSS[ 'background-image' ] =
					'linear-gradient(to right, ' +
					backgroundColor +
					', ' +
					backgroundColor +
					'), url(' +
					backgroundImage?.url +
					');';
			} else if ( undefined === backgroundImage || '' === backgroundImage || 'unset' === backgroundImage ) {
				bgCSS[ 'background-color' ] = backgroundColor;
			}
			// globalBlockStyleId
			if ( globalBlockStyleId ) {
				// We have added overlay for container block only that's why we are checking for pseudoElementOverlay?.blockName in future we will implement overlay for all blocks then we will remove this condition.
				bgCSS[ 'background-image' ] = `unset;`;
			} 
		} else if ( 'image' === backgroundType ) {
			if (
				'color' === overlayType &&
				'' !== backgroundImageColor &&
				undefined !== backgroundImageColor &&
				'unset' !== backgroundImageColor &&
				backgroundImage?.url
			) {
				if ( pseudoElementOverlay?.hasPseudo ) {
					bgCSS[ 'background-image' ] = `url(${ backgroundImage.url });`;
					bgOverlayCSS.background = backgroundImageColor;
					applyOverlayOpacity();
				} else if ( 'container' === pseudoElementOverlay?.blockName ) {
					// We have added overlay for container block only that's why we are checking for pseudoElementOverlay?.blockName in future we will implement overlay for all blocks then we will remove this condition.
					bgCSS[ 'background-image' ] = `url(${ backgroundImage.url });`;
				} else {
					bgCSS[ 'background-image' ] =
					'linear-gradient(to right, ' +
					backgroundImageColor +
					', ' +
					backgroundImageColor +
					'), url(' +
					backgroundImage.url +
					');';
				}
			}

			if (
				'gradient' === overlayType &&
				gradient &&
				backgroundImage?.url
			) {
				if ( pseudoElementOverlay?.hasPseudo ) {
					bgCSS[ 'background-image' ] = `url(${ backgroundImage.url });`;
					bgOverlayCSS[ 'background-image' ] = gradient;
					applyOverlayOpacity();
				} else if ( 'container' === pseudoElementOverlay?.blockName ) {
					// We have added overlay for container block only that's why we are checking for pseudoElementOverlay?.blockName in future we will implement overlay for all blocks then we will remove this condition.
					bgCSS[ 'background-image' ] = `url(${ backgroundImage.url });`;
				}  else {
					bgCSS[ 'background-image' ] = gradient + ', url(' + backgroundImage?.url + ');';
				}
			}

			if ( ['image', 'none', ''].includes( overlayType ) && backgroundImage?.url ) {
				bgCSS[ 'background-image' ] = 'url(' + backgroundImage?.url + ');';
			}

			bgCSS[ 'background-repeat' ] = backgroundRepeat;

			if ( 'custom' !== customPosition && backgroundPosition?.x && backgroundPosition?.y ) {
				bgCSS[ 'background-position' ] = `${ backgroundPosition?.x * 100 }% ${ backgroundPosition?.y * 100 }%`;
			} else if ( 'custom' === customPosition ) {
				bgCSS[
					'background-position'
				] = centralizedPosition === false ? `${ customXPosition } ${ customYPosition }` : `calc(50% + ${ customXPosition }) calc(50% + ${ customYPosition })` ;
			}

			bgCSS[ 'background-size' ] = backgroundSizeValue;
			bgCSS[ 'background-attachment' ] = backgroundAttachment;
			bgCSS[ 'background-clip' ] = 'padding-box';
		} else if ( 'gradient' === backgroundType ) {
			if ( '' !== gradient && 'unset' !== gradient ) {
				bgCSS.background = gradient;
				bgCSS[ 'background-clip' ] = 'padding-box';
			}
		} else if ( 'video' === backgroundType ) {
			if (
				'color' === overlayType &&
				'' !== backgroundVideo &&
				'' !== backgroundVideoColor &&
				undefined !== backgroundVideoColor &&
				'unset' !== backgroundVideoColor
			) {
				bgCSS.background = backgroundVideoColor;
			}
			if ( 'gradient' === overlayType && '' !== backgroundVideo && backgroundVideo && gradient ) {
				bgCSS[ 'background-image' ] = gradient + ';';
			}
		}
	}

	//Handle background overlay image css
	if ( 'image' === overlayType ) {
		if ( '' !== backgroundOverlayImage && backgroundOverlayImage?.url ) {
			bgOverlayCSS[ 'background-image' ] = `url(${ backgroundOverlayImage.url } );`;
		}

		bgOverlayCSS[ 'background-repeat' ] = backgroundOverlayRepeat;

		if ( 'custom' !== customOverlayPosition && backgroundOverlayPosition?.x && backgroundOverlayPosition?.y ) {
			bgOverlayCSS[ 'background-position' ] = `${ backgroundOverlayPosition.x * 100 }% ${
				backgroundOverlayPosition.y * 100
			}%`;
		} else if ( 'custom' === customOverlayPosition ) {
			bgOverlayCSS[
				'background-position'
			] = `${ xPositionOverlayValue }${ xPositionOverlayTypeValue } ${ yPositionOverlayValue }${ yPositionOverlayTypeValue }`;
		}

		let backgroundOverlaySizeValue = backgroundOverlaySize;

		if ( 'custom' === backgroundOverlaySize ) {
			backgroundOverlaySizeValue = backgroundCustomOverlaySize + backgroundCustomOverlaySizeType;
		}

		bgOverlayCSS[ 'background-size' ] = backgroundOverlaySizeValue;
		bgOverlayCSS[ 'background-attachment' ] = backgroundOverlayAttachment;
		bgOverlayCSS[ 'background-clip' ] = 'padding-box';
		bgOverlayCSS[ 'mix-blend-mode' ] = blendMode;
		bgOverlayCSS.opacity = overlayOpacity;
	}

	return pseudoElementOverlay?.forStyleSheet ? bgOverlayCSS : bgCSS;
}

export default generateBackgroundCSS;
