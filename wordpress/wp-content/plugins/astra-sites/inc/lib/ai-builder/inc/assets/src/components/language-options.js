import { Fragment, useEffect, useState } from '@wordpress/element';
import { Listbox, Transition } from '@headlessui/react';
import {
	ChevronDownIcon,
	MagnifyingGlassIcon,
	InformationCircleIcon,
} from '@heroicons/react/24/outline';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { CheckCircleColorfulIcon } from '../ui/icons';
import { classNames } from '../utils/helpers';
import usePopper from '../hooks/use-popper';
import Tooltip from './tooltip';
import { STORE_KEY } from '../store';

const LanguageOptions = ( {
	onSelect,
	value,
	showLabel,
	classNameChild = 'py-3 pl-4 pr-12',
	classNameParent = 'mt-2',
	placement = 'right',
	label = 'This website will be in',
	tooltipText = '',
} ) => {
	const { siteLanguageList } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	const [ selected, setSelected ] = useState(
		siteLanguageList.find( ( lang ) => lang.code === 'en' )
	);
	const [ query, setQuery ] = useState( '' );

	// This is to automatically adjust the height of the dropdown
	let placementValue = 'bottom-end';

	switch ( placement ) {
		case 'left':
			placementValue = 'bottom-start';
			break;
		case 'right':
			placementValue = 'bottom-end';
			break;
		default:
			placementValue = 'bottom-end';
	}

	const [ referenceRef, popperRef ] = usePopper( {
		placement: placementValue,
		modifiers: [ { name: 'offset', options: { offset: [ 0, 0 ] } } ],
	} );

	const handleSelectOption = ( option ) => {
		setSelected( option );
		if ( typeof onSelect === 'function' ) {
			onSelect( option );
		}
	};

	const handleSearch = ( event ) => {
		setQuery( event.target.value );
	};

	const filteredLanguages = siteLanguageList.filter( ( lang ) =>
		lang.name.toLowerCase().includes( query.toLowerCase() )
	);

	useEffect( () => {
		if ( ! value ) {
			return;
		}
		if ( value.code === selected.code ) {
			return;
		}
		if ( typeof value === 'string' ) {
			setSelected(
				siteLanguageList.find( ( lang ) => lang.code === value )
			);
		} else {
			setSelected( value );
		}
	}, [ value ] );

	return (
		<Listbox value={ selected } onChange={ handleSelectOption }>
			{ ( { open } ) => (
				<>
					{ showLabel && (
						<Listbox.Label className="text-base font-semibold flex leading-6 text-zip-app-heading">
							{ tooltipText && (
								<div className="mr-1 pt-0.5">
									<Tooltip content={ tooltipText }>
										<InformationCircleIcon className="w-4 h-4" />
									</Tooltip>
								</div>
							) }
							{ label }
						</Listbox.Label>
					) }
					<div
						className={ classNames( 'relative', classNameParent ) }
					>
						<Listbox.Button
							ref={ referenceRef }
							className={ classNames(
								'min-h-[48px] relative w-full cursor-default rounded-md bg-white text-zip-app-heading shadow-sm border border-solid border-border-tertiary focus:border-accent-st active:border-accent-st ring-1 ring-inset ring-transparent focus:outline-none active:outline-none focus:ring-accent-st',
								'text-base font-normal text-left leading-6'
							) }
						>
							<div
								className={ classNames(
									'inline-flex items-center gap-2 w-full max-w-container relative',
									classNameChild
								) }
							>
								<span className="min-w-fit uppercase text-center text-sm text-zip-app-heading font-semibold leading-5">
									{ selected.code }
								</span>
								<span className="!shrink-0 w-px h-[14px] bg-border-tertiary" />
								<span className="w-full text-base font-normal leading-6 block truncate">
									{ selected.name }{ ' ' }
									{ selected.code === 'en' && '(Default)' }
								</span>
							</div>
							<span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
								<ChevronDownIcon
									className="h-5 w-5 text-zip-app-heading"
									aria-hidden="true"
								/>
							</span>
						</Listbox.Button>

						<Transition
							show={ open }
							as={ Fragment }
							leave="transition ease-in duration-100"
							leaveFrom="opacity-100"
							leaveTo="opacity-0"
						>
							<Listbox.Options
								ref={ popperRef }
								className={ classNames(
									'w-full absolute z-10 !mt-1 !mb-1 rounded-md bg-white p-4 space-y-3 text-base font-normal leading-6 shadow-xl border-0 ring-0 focus:outline-none'
								) }
							>
								<div className="group relative flex flex-1 rounded-md bg-white focus-within:ring-1 focus-within:outline-none focus-within:ring-accent-st border border-solid border-border-tertiary shadow-sm focus-within:border-accent-st transition duration-150 ease-in-out">
									<label
										htmlFor="search-field"
										className="sr-only"
									>
										{ __( 'Search', 'ai-builder' ) }
									</label>
									<MagnifyingGlassIcon
										className="pointer-events-none absolute inset-y-0 left-2.5 h-full w-5 text-app-inactive-icon group-focus-within:text-app-active-icon transition duration-150 ease-in-out"
										aria-hidden="true"
									/>
									<input
										ref={ ( node ) => {
											if ( node ) {
												node.focus();
											}
										} }
										className="appearance-none text-base h-[2.625rem] block w-full !border-0 py-0 !pl-10 pr-5 text-zip-app-heading placeholder:!text-zip-app-inactive-icon focus:ring-0 sm:text-sm bg-transparent focus:outline-none focus:!shadow-none focus:!border-0 focus-within:!border-0"
										placeholder="Search Language"
										type="search"
										value={ query }
										onChange={ handleSearch }
										name="search"
									/>
								</div>

								<div
									className={ classNames(
										'max-h-60 w-full overflow-x-hidden overflow-y-auto space-y-1',
										'[&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-md [&::-webkit-scrollbar-thumb]:bg-dark-app-background/20 [&::-webkit-scrollbar-thumb:hover]:bg-dark-app-background/30 [&::-webkit-scrollbar-track]:bg-white [&::-webkit-scrollbar-track]:my-1 [&::-webkit-scrollbar-track]:rounded-md scroll-p-0'
									) }
								>
									{ filteredLanguages.length > 0 &&
										filteredLanguages.map( ( language ) => (
											<Listbox.Option
												key={ language.code }
												as={ Fragment }
												value={ language }
											>
												{ ( { active } ) => (
													<div
														className={ classNames(
															'w-full max-w-container relative flex items-center justify-between cursor-default select-none py-2 pl-3 pr-2 rounded',
															selected.code ===
																language.code
																? 'bg-alert-info-bg'
																: active &&
																		'bg-alert-info-bg'
														) }
													>
														<div className="w-full flex items-center gap-2">
															<span className="min-w-fit uppercase text-center text-sm text-zip-app-heading font-semibold leading-5">
																{
																	language.code
																}
															</span>
															<span className="w-px h-[14px] bg-border-tertiary !shrink-0" />
															<span
																className={ classNames(
																	'w-full truncate font-normal text-base leading-6',
																	selected.code ===
																		language.code
																		? 'text-zip-app-heading'
																		: 'text-app-text'
																) }
															>
																{
																	language.name
																}
															</span>
														</div>

														{ selected.code ===
															language.code && (
															<span
																className={ classNames(
																	'absolute inset-y-0 right-0 flex items-center pr-4'
																) }
															>
																<CheckCircleColorfulIcon
																	className="h-6 w-6"
																	aria-hidden="true"
																/>
															</span>
														) }
													</div>
												) }
											</Listbox.Option>
										) ) }

									{ filteredLanguages.length === 0 && (
										<div className="relative cursor-default select-none py-2 px-4 text-base font-normal text-app-text">
											{ __(
												'Nothing found',
												'ai-builder'
											) }
										</div>
									) }
								</div>
							</Listbox.Options>
						</Transition>
					</div>
				</>
			) }
		</Listbox>
	);
};

export default LanguageOptions;
