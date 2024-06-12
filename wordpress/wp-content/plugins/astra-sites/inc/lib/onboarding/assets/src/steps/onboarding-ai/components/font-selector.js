import { memo, useEffect, useState } from 'react';
import { ArrowPathIcon, ChevronDownIcon } from '@heroicons/react/24/outline';
import { useSelect } from '@wordpress/data';
import { classNames } from '../helpers';
import { FONTS } from '../../customize-site/customize-steps/site-colors-typography/other-fonts';
import { getHeadingFonts } from '../../../utils/functions';
import { getFontName } from '../customize-ai-site/customize-ai-steps/site-colors-typography-ai/font-selector';
import { useStateValue } from '../../../store/store';
import { sendPostMessage as dispatchPostMessage } from '../utils/helpers';
import { STORE_KEY } from '../store';
import DropdownList from './dropdown-list';
import { __ } from '@wordpress/i18n';

const FontSelector = () => {
	const [ { aiActiveTypography }, dispatch ] = useStateValue();
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
		)?.design_defaults,
		templateResponse = selectedTemplateItem?.typography;

	const [ fonts, setFonts ] = useState(
		FONTS.map( ( font, index ) => ( { ...font, id: index } ) )
	);
	const headingFonts = getHeadingFonts( templateResponse );

	const selectedHeadingFont =
			getFontName( aiActiveTypography?.[ 'headings-font-family' ] ) || '',
		selectedBodyFont =
			getFontName( aiActiveTypography?.[ 'body-font-family' ] ) || '';

	/**
	 * Add selected demo typography as default typography
	 */
	useEffect( () => {
		const googleFontsURL = document.getElementById( 'google-fonts-url' );

		if ( templateResponse ) {
			const defaultFonts = [];
			const defaultTypography = templateResponse;
			defaultFonts.push( defaultTypography );

			if ( ! document.getElementById( 'google-fonts-domain' ) ) {
				const node = document.createElement( 'link' );
				node.id = 'google-fonts-domain';
				node.setAttribute( 'rel', 'preconnect' );
				node.setAttribute( 'href', 'https://fonts.gstatic.com' );
				document.head.appendChild( node );
			}

			// Removes existing Google fonts URL.
			if ( !! googleFontsURL ) {
				googleFontsURL.remove();
			}

			const node = document.createElement( 'link' );
			node.id = 'google-fonts-url';
			node.setAttribute( 'rel', 'stylesheet' );

			const fontsName = [];

			let bodyFont = defaultTypography[ 'body-font-family' ] || '';
			let bodyFontWeight =
				parseInt( defaultTypography[ 'body-font-weight' ] ) || '';
			if ( bodyFontWeight ) {
				bodyFontWeight = `:wght@${ bodyFontWeight }`;
			}

			if ( bodyFont ) {
				bodyFont = getFontName( bodyFont );
				bodyFont =
					undefined !== bodyFont
						? bodyFont.replace( ' ', '+' )
						: bodyFont;
				fontsName.push( `family=${ bodyFont }${ bodyFontWeight }` );
			}

			let headingFont = defaultTypography[ 'headings-font-family' ] || '';
			let headingFontWeight =
				parseInt( defaultTypography[ 'headings-font-weight' ] ) || '';

			if ( headingFontWeight ) {
				headingFontWeight = `:wght@${ headingFontWeight }`;
			}

			if ( headingFont ) {
				headingFont = getFontName( headingFont, bodyFont );
				headingFont =
					undefined !== headingFont
						? headingFont.replace( ' ', '+' )
						: headingFont;
				fontsName.push(
					`family=${ headingFont }${ headingFontWeight }`
				);
			}

			let otherFontsString = '';
			if ( !! fonts ) {
				for ( const font of fonts ) {
					const fontHeading = getFontName(
						font[ 'headings-font-family' ]
					).replaceAll( ' ', '+' );
					const fontHeadingWeight = font[ 'headings-font-weight' ];

					const fontBody = getFontName(
						font[ 'body-font-family' ]
					).replaceAll( ' ', '+' );
					const fontBodyWeight = font[ 'body-font-weight' ];

					otherFontsString += `&family=${ fontHeading }:wght@${ fontHeadingWeight }&family=${ fontBody }:wght@${ fontBodyWeight }`;
				}
				otherFontsString = otherFontsString.replace( /[&]{1}$/i, '' );
			}

			// Add Google fonts URL.
			if ( fontsName ) {
				const fontUrl = `https://fonts.googleapis.com/css2?${ fontsName.join(
					'&'
				) }${ otherFontsString }&display=swap`;

				node.setAttribute( 'href', fontUrl );
				document.head.insertAdjacentElement( 'afterbegin', node );
			}

			// Add default font.
			const allFonts = defaultFonts
				.map( ( defaultItem, indx ) => {
					const item = { ...defaultItem };
					item.id = item?.id ?? `default-${ indx }`;
					return item;
				} )
				.concat( fonts );

			if ( ! aiActiveTypography ) {
				dispatch( {
					type: 'set',
					aiActiveTypography: allFonts[ 0 ],
				} );
			}

			setFonts( allFonts );
		}
	}, [] );

	const sendPostMessage = ( data ) => {
		dispatchPostMessage( data, 'astra-starter-templates-preview' );
	};

	const handleChange = ( typography ) => {
		const newTypography = headingFonts
			? { ...typography, ...headingFonts }
			: typography;

		sendPostMessage( {
			param: 'siteTypography',
			data: JSON.parse( JSON.stringify( newTypography ) ),
		} );

		dispatch( {
			type: 'set',
			aiActiveTypography: typography,
		} );
	};

	const handleReset = () => {
		const defaultTypography = fonts[ 0 ];
		sendPostMessage( {
			param: 'siteTypography',
			data: JSON.parse( JSON.stringify( defaultTypography ) ),
		} );
		dispatch( {
			type: 'set',
			aiActiveTypography: defaultTypography,
		} );
	};

	return (
		<DropdownList
			value={ aiActiveTypography }
			onChange={ handleChange }
			by="id"
		>
			{ ( { open } ) => (
				<>
					<div className="flex items-center justify-between">
						<DropdownList.Label className="text-zip-dark-theme-heading text-sm font-semibold">
							{ __( 'Font Pair', 'astra-sites' ) }
						</DropdownList.Label>
						<button
							key="reset-to-default-fonts"
							className={ classNames(
								'inline-flex p-px items-center justify-center text-zip-dark-theme-content-background border-0 bg-transparent focus:outline-none transition-colors duration-200 ease-in-out',
								! aiActiveTypography?.default &&
									'text-zip-app-inactive-icon cursor-pointer'
							) }
							{ ...( ! aiActiveTypography?.default && {
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
						<DropdownList.Button className="text-sm text-zip-dark-theme-heading font-normal bg-transparent border border-solid border-zip-dark-theme-border">
							<div className="flex justify-start items-center gap-1">
								<span
									className="inline-block h-full truncate"
									style={ {
										fontFamily: selectedHeadingFont,
									} }
								>
									{ selectedHeadingFont }
								</span>
								<span className="text-zip-app-inactive-icon">
									/
								</span>
								<span
									className="inline-block h-full truncate"
									style={ {
										fontFamily: selectedBodyFont,
									} }
								>
									{ selectedBodyFont }
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
							{ fonts.map( ( font ) => {
								const bodyFont =
									getFontName( font[ 'body-font-family' ] ) ||
									'';
								const headingFont =
									getFontName(
										font[ 'headings-font-family' ],
										bodyFont
									) || '';
								return (
									<DropdownList.Option
										key={ font.id }
										value={ font }
										className={ ( { active, selected } ) =>
											classNames(
												'flex justify-start items-center gap-1 text-body-text text-base font-normal',
												selected &&
													'bg-zip-app-light-bg',
												active && 'bg-zip-app-light-bg'
											)
										}
									>
										<span
											className="truncate"
											style={ {
												fontFamily: headingFont,
											} }
										>
											{ headingFont }
										</span>
										<span className="text-zip-app-inactive-icon">
											/
										</span>
										<span
											className="truncate"
											style={ {
												fontFamily: bodyFont,
											} }
										>
											{ bodyFont }
										</span>
									</DropdownList.Option>
								);
							} ) }
						</DropdownList.Options>
					</div>
				</>
			) }
		</DropdownList>
	);
};

export default memo( FontSelector );
