import { Menu, Transition } from '@headlessui/react';
import { Fragment } from '@wordpress/element';
import usePopper from '../hooks/use-popper';

const Dropdown = ( {
	placement = 'right',
	width = 'w-48',
	contentClassName = 'py-1 bg-white',
	trigger,
	offset = [ 0, 0 ],
	children,
	disabled = false,
	mainClassName = '',
} ) => {
	let placementValue = 'bottom-end';
	switch ( placement ) {
		case 'left':
			placementValue = 'bottom-start';
			break;
		case 'right':
			placementValue = 'bottom-end';
			break;
		case 'top-start':
			placementValue = 'top-start';
			break;
		default:
			placementValue = 'bottom-end';
	}
	const [ triggerPopper, container ] = usePopper( {
		placement: placementValue,
		strategy: 'fixed',
		modifiers: [ { name: 'offset', options: { offset } } ],
	} );

	switch ( width?.toString() ) {
		case '48':
			width = 'w-48';
			break;
		case '60':
			width = 'w-60';
			break;
		case '72.5':
			width = 'w-[18.25rem]';
			break;
		case '80':
			width = 'w-80';
			break;
		default:
			width = !! width ? width : 'w-48';
	}

	return (
		<Menu as="div" className={ `relative ${ mainClassName }` }>
			{ ( { open } ) => (
				<>
					<Menu.Button
						ref={ triggerPopper }
						as={ Fragment }
						disabled={ disabled }
					>
						{ trigger }
					</Menu.Button>

					<div ref={ container } className="z-50">
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
								className={ `my-2 ${ width } rounded-md shadow-lg` }
							>
								<Menu.Items
									className={ `rounded-md focus:outline-none ring-1 ring-black ring-opacity-5 ${ contentClassName }` }
								>
									{ children }
								</Menu.Items>
							</div>
						</Transition>
					</div>
				</>
			) }
		</Menu>
	);
};

Dropdown.Item = Menu.Item;

export default Dropdown;
