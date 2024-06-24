import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../../../store/store';
import ColorPalettes from '../../../../components/color-palettes/color-palettes';
import {
	sendPostMessage,
	getDefaultColorPalette,
	getColorScheme,
} from '../../../../utils/functions';
import ICONS from '../../../../../icons';
import TypographyWrapper from './typography';
import { DARK_PALETTES, LIGHT_PALETTES } from './colors';

const SiteColorsControls = () => {
	const [ { activePaletteSlug, templateResponse, builder }, dispatch ] =
		useStateValue();
	const [ defaultPalette, setDefaultPalette ] = useState( [] );
	const [ colorScheme, setColorScheme ] = useState( LIGHT_PALETTES );

	const onPaletteChange = ( event, palette ) => {
		if ( ! palette ) {
			return;
		}

		dispatch( {
			type: 'set',
			activePaletteSlug: palette.slug,
			activePalette: palette,
		} );

		sendPostMessage( {
			param: 'colorPalette',
			data: palette,
		} );
	};

	useEffect( () => {
		const defaultPaletteValues = getDefaultColorPalette( templateResponse );
		setDefaultPalette( defaultPaletteValues );
		let scheme =
			'light' === getColorScheme( templateResponse )
				? LIGHT_PALETTES
				: DARK_PALETTES;

		const customColors =
			templateResponse?.[ 'astra-custom-palettes' ] || [];
		if ( customColors.length && customColors.length % 2 === 0 ) {
			let colors = customColors;

			const customColorsSet = [];
			colors.map( ( value ) => {
				const obj = {
					slug: value.slug,
					title: value.slug,
				};
				const sampleColors = [ ...scheme[ 0 ].colors ];
				sampleColors[ 0 ] = value.colors[ 0 ];
				sampleColors[ 1 ] = value.colors[ 1 ];
				obj.colors = sampleColors;
				customColorsSet.push( obj );
				return customColorsSet;
			} );
			colors = [ ...customColorsSet, ...scheme ];
			colors.map( ( value, i ) => {
				colors[ i ].title = 'Style' + ( i + 1 );
				colors[ i ].slug = 'style-' + ( i + 1 );
				return colors;
			} );

			scheme = colors;
		}

		setColorScheme( scheme );
	}, [ templateResponse ] );

	const resetColorPlallete = ( e ) => {
		onPaletteChange( e, Object.values( defaultPalette )[ 0 ] );
	};

	const ColorWrapper = () => {
		if ( builder === 'beaver-builder' || builder === 'brizy' ) {
			return null;
		}

		return (
			<div className="colors-section">
				<div className="d-flex-space-between">
					<h4>{ __( 'Change Colors', 'astra-sites' ) }</h4>
					<div
						className={ `customize-reset-btn ${
							activePaletteSlug === 'default'
								? 'disabled'
								: 'active'
						}` }
						onClick={ resetColorPlallete }
					>
						{ ICONS.reset }
					</div>
				</div>
				{ defaultPalette ? (
					<>
						<ColorPalettes
							selected={ activePaletteSlug }
							options={ defaultPalette }
							onChange={ ( event, palette ) => {
								onPaletteChange( event, palette );
							} }
							tabIndex="0"
							type="default"
						/>
					</>
				) : (
					''
				) }

				<ColorPalettes
					selected={ activePaletteSlug }
					options={ colorScheme }
					onChange={ ( event, palette ) => {
						onPaletteChange( event, palette );
					} }
					tabIndex="0"
					type="others"
				/>
			</div>
		);
	};
	return (
		<>
			<ColorWrapper />
			<TypographyWrapper />
		</>
	);
};

export default SiteColorsControls;
