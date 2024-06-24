import { Dialog, Transition } from '@headlessui/react';
import { XMarkIcon } from '@heroicons/react/24/outline';
import { Fragment } from '@wordpress/element';
import { classNames } from '../utils/helpers';

const Modal = ( {
	open = false,
	setOpen,
	width = 520,
	overflowHidden = true,
	children,
	hideCloseIcon = false,
	className,
	isErrorModal = false,
	autoClose = true,
} ) => {
	let modalWidth = 'max-w-[35rem]';

	switch ( width?.toString() ) {
		case '640':
			modalWidth = 'sm:max-w-[40rem]';
			break;
		case '520':
			modalWidth = 'sm:max-w-[32.5rem]';
			break;
		case '464':
			modalWidth = 'sm:max-w-[29rem]';
			break;
		case '480':
			modalWidth = 'sm:max-w-[30rem]';
			break;
		case '300':
			modalWidth = 'sm:max-w-[24rem]';
			break;
		default:
			modalWidth = 'sm:max-w-[35rem]';
			break;
	}

	return (
		<Transition.Root show={ open || false } as={ Fragment }>
			<Dialog
				as="div"
				className="spectra-ai relative z-[99999999]"
				onClose={ ! autoClose ? () => {} : setOpen }
			>
				<Transition.Child
					as={ Fragment }
					enter="ease-out duration-300"
					enterFrom="opacity-0"
					enterTo="opacity-100"
					leave="ease-in duration-200"
					leaveFrom="opacity-100"
					leaveTo="opacity-0"
				>
					<div className="fixed inset-0 bg-zip-app-heading bg-opacity-75 transition-opacity backdrop-blur opacity-100" />
				</Transition.Child>

				<div className="fixed inset-0 z-10 overflow-y-auto">
					<div className="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
						<Transition.Child
							as={ Fragment }
							enter="ease-out duration-300"
							enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
							enterTo="opacity-100 translate-y-0 sm:scale-100"
							leave="ease-in duration-200"
							leaveFrom="opacity-100 translate-y-0 sm:scale-100"
							leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
						>
							<Dialog.Panel
								className={ classNames(
									'relative w-full transform rounded-lg bg-white px-8 pt-8 pb-6 text-left shadow-xl transition-all sm:my-8 sm:w-full',
									modalWidth,
									overflowHidden && 'overflow-hidden',
									className
								) }
							>
								<div className="absolute right-0 top-0 pr-3 pt-3 block">
									{ ! hideCloseIcon && (
										<button
											type="button"
											className="rounded-md bg-white text-zip-app-inactive-icon hover:text-nav-active outline-none border-0 focus:outline-none transition duration-150 ease-in-out cursor-pointer"
											onClick={ () =>
												setOpen( false, 'close-icon' )
											}
										>
											<span className="sr-only">
												Close
											</span>
											<XMarkIcon
												className="h-5 w-5"
												aria-hidden="true"
											/>
										</button>
									) }
								</div>
								<div
									className={ classNames(
										'font-sans',
										! isErrorModal && 'space-y-6'
									) }
								>
									{ /* Modal Body */ }
									{ children }
								</div>
							</Dialog.Panel>
						</Transition.Child>
					</div>
				</div>
			</Dialog>
		</Transition.Root>
	);
};

export default Modal;
