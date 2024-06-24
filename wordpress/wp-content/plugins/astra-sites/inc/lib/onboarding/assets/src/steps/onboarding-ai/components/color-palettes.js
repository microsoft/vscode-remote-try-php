import { memo, useEffect, useState } from 'react';
import { ArrowPathIcon, ChevronDownIcon } from '@heroicons/react/24/outline';
import { useSelect } from '@wordpress/data';
import { classNames } from '../helpers';
import {
	LIGHT_PALETTES,
	DARK_PALETTES,
} from '../../customize-site/customize-steps/site-colors-typography/colors';
import { useStateValue } from '../../../store/store';
import { sendPostMessage as dispatchPostMessage } from '../utils/helpers';
import { STORE_KEY } from '../store';
import DropdownList from './dropdown-list';
import { Tiles } from '../../ui/icons';
import { __ } from '@wordpress/i18n';

const getColorScheme = ( value ) => {
	if ( Array.isArray( value ) ) {
		return value.length > 0 ? DARK_PALETTES : LIGHT_PALETTES;
	}

	return ! value ? LIGHT_PALETTES : DARK_PALETTES;
};

const ColorPalettes = () => {
	const [ { aiActivePallette: selectedPalette }, dispatch ] = useStateValue();
	const {
		stepData: { selectedTemplate, templateList },
	} = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );

		return {
			stepData: getAIStepData(),
		};
	}, [] );
	const selectedTemplateItem = templateList?.find(
		( item ) => item?.uuid === selectedTemplate
	)?.design_defaults;
	const [ colorScheme, setColorScheme ] = useState(
		getColorScheme( selectedTemplateItem?.color_scheme )
	);

	const sendPostMessage = ( data ) => {
		dispatchPostMessage( data, 'astra-starter-templates-preview' );
	};

	const handleChange = ( palette ) => {
		if ( palette?.slug === selectedPalette?.slug ) {
			return;
		}
		sendPostMessage( {
			param: 'colorPalette',
			data: palette,
		} );
		dispatch( {
			type: 'set',
			aiActivePallette: palette,
		} );
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
							? `Original ${ index + 1 }`
							: 'Original',
					colors: palette,
			  } ) )
			: [];
		const scheme = getColorScheme( selectedTemplateItem?.color_scheme );

		const customColorsOptions = {
			slug: 'custom',
			title: 'Custom',
			colors: [],
		};

		setColorScheme( [
			...defaultPaletteValues,
			...scheme,
			customColorsOptions,
		] );
		if ( ! selectedPalette ) {
			dispatch( {
				type: 'set',
				aiActivePallette: defaultPaletteValues[ 0 ],
				defaultPalette: defaultPaletteValues[ 0 ],
			} );
		}
	}, [] );

	const handleReset = () => {
		const defaultPalette = colorScheme[ 0 ];
		sendPostMessage( {
			param: 'colorPalette',
			data: defaultPalette,
		} );
		dispatch( {
			type: 'set',
			aiActivePallette: defaultPalette,
		} );
	};

	return (
		<DropdownList
			value={ selectedPalette }
			onChange={ handleChange }
			by="slug"
		>
			{ ( { open } ) => (
				<>
					<div className="flex items-center justify-between">
						<DropdownList.Label className="text-zip-dark-theme-heading text-sm font-normal">
							{ __( 'Color Palette', 'astra-sites' ) }
						</DropdownList.Label>
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
					<div className="relative mt-1">
						<DropdownList.Button className="text-sm text-zip-dark-theme-heading font-semibold bg-transparent border border-solid border-zip-dark-theme-border">
							<div className="flex justify-start items-center gap-3">
								<div className="w-[30px] h-5">
									{ selectedPalette?.colors?.length !== 0 ? (
										<>
											<span
												className="inline-block w-[20px] h-full"
												style={ {
													background:
														selectedPalette
															?.colors?.[ 1 ],
												} }
											/>
											<span
												className="inline-block w-[10px] h-full"
												style={ {
													background:
														selectedPalette
															?.colors?.[ 0 ],
												} }
											/>
										</>
									) : (
										<Tiles className="!shrink-0 w-full h-full" />
									) }
								</div>
								<span className="block truncate">
									{ selectedPalette?.title }
								</span>
							</div>
							<span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
								<ChevronDownIcon
									className="h-5 w-5 text-gray-400"
									aria-hidden="true"
								/>
							</span>
						</DropdownList.Button>
						<DropdownList.Options
							open={ open }
							className="!space-y-2"
						>
							{ colorScheme.map( ( colorPalette ) => (
								<DropdownList.Option
									key={ colorPalette.slug }
									value={ colorPalette }
									className={ ( { active, selected } ) =>
										classNames(
											'flex justify-start items-center gap-3 text-body-text',
											selected && 'bg-zip-app-light-bg',
											active && 'bg-zip-app-light-bg'
										)
									}
								>
									{ ( { selected } ) => (
										<>
											<div className="w-[30px] h-5">
												{ colorPalette?.colors
													?.length !== 0 ? (
													<>
														<span
															className="inline-block w-[20px] h-full"
															style={ {
																background:
																	colorPalette
																		?.colors?.[ 1 ],
															} }
														/>
														<span
															className="inline-block w-[10px] h-full"
															style={ {
																background:
																	colorPalette
																		?.colors?.[ 0 ],
															} }
														/>
													</>
												) : (
													<Tiles className="!shrink-0 w-full h-full" />
												) }
											</div>
											<span
												className={ classNames(
													selected
														? 'font-semibold'
														: 'font-normal',
													'block truncate'
												) }
											>
												{ colorPalette?.title }
											</span>
										</>
									) }
								</DropdownList.Option>
							) ) }
						</DropdownList.Options>
					</div>
				</>
			) }
		</DropdownList>
	);
};

export default memo( ColorPalettes );
