import { memo, useEffect, useState } from '@wordpress/element';
import { ArrowPathIcon } from '@heroicons/react/24/outline';
import { useSelect, useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { classNames } from '../helpers';
import { LIGHT_PALETTES, DARK_PALETTES } from '../ui/colors';
import { sendPostMessage as dispatchPostMessage } from '../utils/helpers';
import { STORE_KEY } from '../store';
import { TilesIcon } from '../ui/icons';

const getColorScheme = ( value ) => {
	if ( Array.isArray( value ) ) {
		return value.length > 0 ? DARK_PALETTES : LIGHT_PALETTES;
	}

	return ! value ? LIGHT_PALETTES : DARK_PALETTES;
};

const ColorPalettes = () => {
	const {
		stepData: {
			selectedTemplate,
			templateList,
			activeColorPalette: selectedPalette,
		},
	} = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );

		return {
			stepData: getAIStepData(),
		};
	}, [] );
	const { setWebsiteColorPalette, setDefaultColorPalette } =
		useDispatch( STORE_KEY );
	const selectedTemplateItem = templateList?.find(
		( item ) => item?.uuid === selectedTemplate
	)?.design_defaults;
	const [ colorScheme, setColorScheme ] = useState(
		getColorScheme( selectedTemplateItem?.color_scheme )
	);

	const sendPostMessage = ( data ) => {
		dispatchPostMessage( data, 'astra-starter-templates-preview' );
	};

	const handleChange = ( palette ) => () => {
		if ( selectedPalette.slug === palette.slug ) {
			return;
		}
		sendPostMessage( {
			param: 'colorPalette',
			data: palette,
		} );
		setWebsiteColorPalette( palette );
	};

	useEffect( () => {
		const defaultColorPalettes = !! selectedTemplateItem
			? Object.values( selectedTemplateItem.color_palette ).filter(
					( item ) => Array.isArray( item )
			  )
			: [];
		const defaultPaletteValues = !! selectedTemplateItem
			? defaultColorPalettes.map( ( palette, index ) => ( {
					id: `default-${ index }`,
					slug: 'default',
					title:
						defaultColorPalettes.length > 1
							? sprintf(
									/* translators: %s: index */
									__( `Original %1$s`, 'ai-builder' ),
									index + 1
							  )
							: __( 'Original', 'ai-builder' ),
					colors: palette,
			  } ) )
			: [];
		const scheme = getColorScheme( selectedTemplateItem?.color_scheme );

		setColorScheme( [
			...defaultPaletteValues,
			...scheme,
			{
				slug: 'custom',
				title: __( 'Custom', 'ai-builder' ),
				colors: [],
			},
		] );
		if ( ! selectedPalette ) {
			setDefaultColorPalette( defaultPaletteValues[ 0 ] );
		}
	}, [] );

	const handleReset = () => {
		const defaultPalette = colorScheme[ 0 ];
		sendPostMessage( {
			param: 'colorPalette',
			data: defaultPalette,
		} );
		setWebsiteColorPalette( defaultPalette );
	};

	return (
		<div className="space-y-2">
			<div className="flex items-center justify-between">
				<p className="text-zip-dark-theme-heading text-sm">
					<span className="font-semibold">
						{ __( 'Color Palette', 'ai-builder' ) }:{ ' ' }
					</span>
					<span>{ selectedPalette?.title }</span>
				</p>
				<button
					key="reset-to-default-colors"
					className={ classNames(
						'inline-flex p-px items-center justify-center text-zip-dark-theme-content-background border-0 bg-transparent focus:outline-none transition-colors duration-200 ease-in-out',
						selectedPalette?.slug !== 'default' &&
							'text-zip-app-inactive-icon cursor-pointer'
					) }
					{ ...( selectedPalette?.slug !== 'default' && {
						onClick: handleReset,
					} ) }
				>
					<ArrowPathIcon
						className="w-[0.875rem] h-[0.875rem]"
						strokeWidth={ 2 }
					/>
				</button>
			</div>
			<div className="grid grid-cols-5 gap-3 auto-rows-[36px]">
				{ colorScheme.map( ( colorPalette ) => (
					<div
						key={ colorPalette.slug }
						className={ classNames(
							'flex justify-center items-center gap-3 text-body-text rounded-md border border-solid border-zip-dark-theme-border h-9 w-full cursor-pointer',
							selectedPalette?.slug === colorPalette.slug &&
								'outline-1 outline outline-offset-2 outline-outline-color'
						) }
						onClick={ handleChange( colorPalette ) }
					>
						{ !! colorPalette?.colors?.length && (
							<div
								className="w-full h-full flex items-center justify-center gap-1 rounded-md"
								style={ {
									background: colorPalette?.colors?.[ 5 ],
								} }
							>
								<span
									className="inline-block w-[14px] h-[14px] rounded-full shrink-0"
									style={ {
										background: colorPalette?.colors?.[ 0 ],
									} }
								/>
								<span
									className="inline-block w-[14px] h-[14px] rounded-full shrink-0"
									style={ {
										background: colorPalette?.colors?.[ 1 ],
									} }
								/>
							</div>
						) }
						{ ! colorPalette?.colors?.length && (
							<TilesIcon className="!shrink-0 w-full h-full rounded-md" />
						) }
					</div>
				) ) }
			</div>
		</div>
	);
};

export default memo( ColorPalettes );
