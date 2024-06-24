import React, { Fragment } from 'react';
import { InformationCircleIcon } from '@heroicons/react/24/outline';
import { Menu, Transition } from '@headlessui/react';
import { CheckIcon } from '@heroicons/react/24/solid';
import usePopper from '../hooks/use-popper';
import '../../../variables.scss';
import { sprintf, __ } from '@wordpress/i18n';

const TemplateInfo = ( { template, position } ) => {
	const [ triggerPopper, container ] = usePopper( {
		placement: 'top-end',
		strategy: 'fixed',
		modifiers: [ { name: 'offset', options: { offset: [ 0, 6 ] } } ],
	} );

	return (
		<div className="absolute bottom-0  w-full h-14 flex items-center justify-between bg-white px-5 shadow-template-info border-t border-b-0 border-x-0 border-solid border-border-tertiary">
			<div className="zw-base-semibold text-app-heading capitalize select-none">
				{ /* { template?.name || template?.domain?.split( '.' )?.[ 0 ] } */ }
				{ position ? `Option ${ position }` : '' }
			</div>
			<div className="flex gap-4">
				<Menu as="div" className="relative">
					{ ( { open, close } ) => (
						<>
							<Menu.Button ref={ triggerPopper } as={ Fragment }>
								<InformationCircleIcon
									ref={ triggerPopper }
									className="w-6 h-6 cursor-pointer text-app-active-icon"
								/>
							</Menu.Button>

							<div
								ref={ container }
								className="z-50 bg-tooltip text-zip-dark-theme-heading rounded-md"
							>
								<Transition
									show={ open }
									as={ Fragment }
									enter="transition ease-out duration-200"
									enterFrom="transform opacity-0 scale-95"
									enterTo="transform opacity-100 scale-100"
									leave="transition ease-in duration-75"
									leaveFrom="transform opacity-100 scale-100"
									leaveTo="transform opacity-0 scale-95"
								>
									<div
										className="z-50 w-[11.5rem] bg-app-tooltip rounded-md text-dark-app-heading p-3 zw-sm-medium text-zip-dark-theme-heading font-medium"
										onClick={ close }
									>
										{ template?.pages?.length ? (
											<div>
												<div>
													{ __(
														'Pages included:',
														'astra-sites'
													) }
												</div>
												<div className="flex flex-col gap-1 mt-1.5 font-normal">
													{ template.pages.map(
														( page ) => (
															<div
																key={
																	page.post_title
																}
																className="flex items-center gap-2"
															>
																<CheckIcon className="w-3 h-3 text-app-inactive-icon" />
																<div className="text-sm text-zip-dark-theme-heading">
																	{
																		page.post_title
																	}
																</div>
															</div>
														)
													) }
												</div>
											</div>
										) : (
											<div>
												{ sprintf(
													/* translators: %s: Page count */
													__(
														'Page count: %s',
														'astra-sites'
													),
													template.pagesCount
												) }
											</div>
										) }
									</div>
								</Transition>{ ' ' }
							</div>
						</>
					) }
				</Menu>
			</div>
		</div>
	);
};

export default TemplateInfo;
