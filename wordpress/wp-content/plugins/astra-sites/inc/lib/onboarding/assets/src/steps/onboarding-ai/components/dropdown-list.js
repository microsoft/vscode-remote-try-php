import { Children, Fragment, cloneElement, isValidElement, memo } from 'react';
import { Listbox, Transition } from '@headlessui/react';
import { classNames } from '../helpers';

// eslint-disable-next-line func-style
function DropdownList( { value, onChange, by = 'id', children } ) {
	return (
		<Listbox value={ value } onChange={ onChange } by={ by }>
			{ ( { open } ) => {
				if ( typeof children === 'function' ) {
					return children( { open } );
				}

				if (
					typeof children !== 'string' &&
					isValidElement( children )
				) {
					return Children.map( children, ( child ) => {
						if ( isValidElement( child ) ) {
							const childProps = { ...child.props };
							return cloneElement( child, {
								...childProps,
								open,
							} );
						}
					} );
				}

				return children;
			} }
		</Listbox>
	);
}

DropdownList = memo( DropdownList );

DropdownList.Label = ( { className, children } ) => (
	<Listbox.Label
		className={ classNames(
			'block text-sm font-medium leading-6 text-gray-900',
			className
		) }
	>
		{ children }
	</Listbox.Label>
);
DropdownList.Label = memo( DropdownList.Label );

DropdownList.Button = ( { className, children } ) => (
	<Listbox.Button
		className={ classNames(
			'relative w-full cursor-default rounded-md bg-white py-2.5 pl-3 pr-10 text-left text-gray-900 shadow-sm border-0 focus:outline-none sm:text-sm sm:leading-6',
			className
		) }
	>
		{ children }
	</Listbox.Button>
);
DropdownList.Button = memo( DropdownList.Button );

DropdownList.Options = ( { open, className, children } ) => (
	<Transition
		show={ open }
		as={ Fragment }
		leave="transition ease-in duration-100"
		leaveFrom="opacity-100"
		leaveTo="opacity-0"
	>
		<Listbox.Options
			className={ classNames(
				'absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white p-2 shadow-lg focus:outline-none text-sm',
				className
			) }
		>
			{ children }
		</Listbox.Options>
	</Transition>
);
DropdownList.Options = memo( DropdownList.Options );

DropdownList.Option = ( { value, className, children } ) => {
	return (
		<Listbox.Option
			className={ ( { active, selected } ) =>
				classNames(
					'relative cursor-default select-none rounded py-2 pl-2 pr-9 m-0',
					typeof className === 'function'
						? className( { active, selected } )
						: className
				)
			}
			value={ value }
		>
			{ ( { selected, active } ) => {
				if ( typeof children === 'function' ) {
					return children( { selected, active } );
				}

				if (
					typeof children !== 'string' &&
					isValidElement( children )
				) {
					return Children.map( children, ( child ) => {
						if ( isValidElement( child ) ) {
							const childProps = { ...child.props };
							return cloneElement( child, {
								...childProps,
								selected,
								active,
							} );
						}
					} );
				}

				return children;
			} }
		</Listbox.Option>
	);
};
DropdownList.Option = memo( DropdownList.Option );

export default DropdownList;
