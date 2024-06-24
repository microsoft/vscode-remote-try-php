import { useEffect, useReducer } from 'react';
import ButtonGroup from './button-group';
import ColorPicker from './color-picker';
import tinyColor from 'tinycolor2';
import { useStateValue } from '../../../store/store';
import { sendPostMessage as dispatchPostMessage } from '../utils/helpers';
import { sprintf, __ } from '@wordpress/i18n';

const colorSchemes = [
	{
		id: 'light',
		name: __( 'Light', 'astra-sites' ),
	},
	{
		id: 'dark',
		name: __( 'Dark', 'astra-sites' ),
	},
];

const backgroundSaturations = [
	{
		id: 'muted',
		name: __( 'Muted', 'astra-sites' ),
	},
	{
		id: 'normal',
		name: __( 'Normal', 'astra-sites' ),
	},
	{
		id: 'vibrant',
		name: __( 'Vibrant', 'astra-sites' ),
	},
];

const brightnessLevels = [
	{
		id: 1,
		name: 1,
	},
	{
		id: 2,
		name: 2,
	},
	{
		id: 3,
		name: 3,
	},
];

const generateColorPalette = ( color, scheme, bgSaturation, brightness ) => {
	const primaryColor = tinyColor( color.hex );
	const white = tinyColor( '#ffffff' );

	let brightnessLevel = 0;
	switch ( brightness.id ) {
		case 1:
			brightnessLevel = 25;
			break;
		case 2:
			brightnessLevel = 35;
			break;
		case 3:
			brightnessLevel = 45;
			break;
		default:
			brightnessLevel = 0;
			break;
	}

	let bgSaturationLevel = 0;
	switch ( bgSaturation.id ) {
		case 'muted':
			bgSaturationLevel = 100;
			break;
		case 'normal':
			bgSaturationLevel = 20;
			break;
		case 'vibrant':
			bgSaturationLevel = 0;
			break;
		default:
			bgSaturationLevel = 0;
			break;
	}

	let colorPalette;
	if ( scheme?.id === 'dark' ) {
		colorPalette = [
			primaryColor.toHexString(),
			primaryColor.clone().darken( 15 ).toHexString(),
			white.toHexString(),
			white.clone().darken( 4 ).toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 65 - brightnessLevel )
				.toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 80 - brightnessLevel )
				.toHexString(),
			white.clone().darken( 65 ).toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 85 - brightnessLevel )
				.toHexString(),
			white.clone().darken( 85 ).toHexString(),
		];
	} else {
		colorPalette = [
			primaryColor.toHexString(),
			primaryColor.clone().darken( 15 ).toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 80 )
				.toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 65 )
				.toHexString(),
			primaryColor
				.clone()
				.lighten( 40 )
				.desaturate( bgSaturationLevel )
				.toHexString(),
			white.toHexString(),
			primaryColor.clone().lighten( 38 ).toHexString(),
			primaryColor
				.clone()
				.desaturate( bgSaturationLevel )
				.darken( 85 )
				.toHexString(),
			white.clone().darken( 85 ).toHexString(),
		];
	}

	return colorPalette;
};

const sendPostMessage = ( data ) => {
	dispatchPostMessage( data, 'astra-starter-templates-preview' );
};

const CustomColorPalette = () => {
	const [ { aiActivePallette, defaultPalette }, dispatch ] = useStateValue();
	const defaultColors = generateColorPalette(
		defaultPalette.colors[ 0 ],
		colorSchemes[ 0 ],
		backgroundSaturations[ 0 ],
		brightnessLevels[ 0 ]
	);
	const activePaletteColors = aiActivePallette?.colors ?? [];

	const [ customColorState, setCustomColorState ] = useReducer(
			( state, newState ) => ( { ...state, ...newState } ),
			{
				color: {
					hex:
						aiActivePallette?.colors[ 0 ] ??
						defaultPalette?.colors[ 0 ] ??
						'#74A84A',
				},
				colors: activePaletteColors ?? defaultColors ?? [],
				scheme: colorSchemes[ 0 ],
				backgroundSaturation: backgroundSaturations[ 0 ],
				brightnessLevel: brightnessLevels[ 0 ],
			}
		),
		{ color, scheme, backgroundSaturation, brightnessLevel } =
			customColorState;

	const setColor = ( colorValue ) => {
		const palette = generateColorPalette(
			colorValue,
			scheme,
			backgroundSaturation,
			brightnessLevel
		);
		setCustomColorState( { color: colorValue, colors: palette } );
	};

	const setColorScheme = ( schemeValue ) => {
		setCustomColorState( { scheme: schemeValue } );
	};

	const setBackgroundSaturation = ( bgSaturationValue ) => {
		setCustomColorState( { backgroundSaturation: bgSaturationValue } );
	};

	const setBrightnessLevel = ( brightnessValue ) => {
		setCustomColorState( { brightnessLevel: brightnessValue } );
	};

	const isPassedAccessibility = ( colorValue, colorScheme ) => {
		const hexValue = tinyColor( colorValue ).toHexString();
		if ( colorScheme === 'light' ) {
			return tinyColor.isReadable( hexValue, '#FFFFFF' );
		}
		return tinyColor.isReadable( hexValue, '#000000' );
	};

	useEffect( () => {
		const palette = generateColorPalette(
			color,
			scheme,
			backgroundSaturation,
			brightnessLevel
		);
		sendPostMessage( {
			param: 'colorPalette',
			data: { colors: palette },
		} );
		dispatch( {
			type: 'set',
			aiActivePallette: {
				...aiActivePallette,
				colors: palette,
			},
		} );
	}, [ customColorState ] );

	return (
		<div className="space-y-4">
			<div className="space-y-4">
				{ /* Primary Color */ }
				<div className="flex items-center justify-between gap-3">
					<span className="text-zip-dark-theme-heading text-sm font-semibold">
						Primary Color
					</span>
					<ColorPicker onChange={ setColor } value={ color }>
						<div
							className="w-[30px] h-[20px] border border-solid border-white border-opacity-[0.12]"
							style={ { background: color.hex } }
						/>
					</ColorPicker>
				</div>
				{ /* Contrast ratio warning */ }
				{ ! isPassedAccessibility( color.hex, scheme.id ) && (
					<div className="px-3 py-2 bg-zip-dark-theme-content-background">
						<p className="!text-xs !font-normal !text-zip-dark-theme-body">
							{ sprintf(
								/* translators: %1$s: light or dark, %2$s: brighter or darker */
								__(
									'This color is not suitable for reading on %1$s backgrounds. Consider making it slightly %2$s.',
									'astra-sites'
								),
								scheme.id === 'dark' ? 'dark' : 'light',
								scheme.id === 'dark' ? 'brighter' : 'darker'
							) }
						</p>
					</div>
				) }
				{ /* Colors preview */ }
				<div className="w-full h-[25px] grid grid-cols-9 auto-rows-auto border border-solid border-zip-dark-theme-border rounded overflow-clip">
					{ generateColorPalette(
						color,
						scheme,
						backgroundSaturation,
						brightnessLevel
					).map( ( colorValue, indx ) => (
						<div
							key={ `${ indx }-${ colorValue }` }
							className="w-full h-full"
							style={ { background: colorValue } }
						/>
					) ) }
				</div>
			</div>
			<div className="space-y-3">
				{ /* Style */ }
				<div className="flex items-center justify-between gap-3">
					<span className="text-zip-dark-theme-heading text-sm font-normal">
						{ __( 'Style', 'astra-sites' ) }
					</span>
					<ButtonGroup onChange={ setColorScheme } value={ scheme }>
						{ colorSchemes.map( ( schemeItem ) => (
							<ButtonGroup.ButtonItem
								key={ schemeItem.id }
								className="px-2 py-1"
								value={ schemeItem }
							>
								{ schemeItem.name }
							</ButtonGroup.ButtonItem>
						) ) }
					</ButtonGroup>
				</div>
				{ /* Saturation */ }
				<div className="flex items-center justify-between gap-3">
					<span className="text-zip-dark-theme-heading text-sm font-normal">
						{ __( 'Saturation', 'astra-sites' ) }
					</span>
					<ButtonGroup
						onChange={ setBackgroundSaturation }
						value={ backgroundSaturation }
					>
						{ backgroundSaturations.map( ( saturationItem ) => (
							<ButtonGroup.ButtonItem
								key={ saturationItem.id }
								className="px-2 py-1"
								value={ saturationItem }
							>
								{ saturationItem.name }
							</ButtonGroup.ButtonItem>
						) ) }
					</ButtonGroup>
				</div>
				{ /* Brightness */ }
				{ scheme.id === 'dark' && (
					<div className="flex items-center justify-between gap-3">
						<span className="text-zip-dark-theme-heading text-sm font-normal">
							{ __( 'Brightness', 'astra-sites' ) }
						</span>
						<ButtonGroup
							onChange={ setBrightnessLevel }
							value={ brightnessLevel }
						>
							{ brightnessLevels.map( ( brightnessItem ) => (
								<ButtonGroup.ButtonItem
									key={ brightnessItem.id }
									className="w-7 h-7 px-2 py-1 justify-center"
									value={ brightnessItem }
								>
									{ brightnessItem.name }
								</ButtonGroup.ButtonItem>
							) ) }
						</ButtonGroup>
					</div>
				) }
			</div>
		</div>
	);
};

export default CustomColorPalette;
