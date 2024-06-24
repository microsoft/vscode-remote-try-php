import {
	createContext,
	memo,
	useContext,
	useMemo,
	isValidElement,
	Children,
	cloneElement,
} from 'react';
import { motion, LayoutGroup } from 'framer-motion';
import { classNames } from '../helpers';

const ButtonGroupContext = createContext( {} );
ButtonGroupContext.displayName = 'ButtonGroupContext';
const useButtonGroupState = () => useContext( ButtonGroupContext );

const nonElementChildrenTypes = [ 'string', 'number' ];

function ButtonGroup( { value, by = 'id', onChange, children } ) {
	const layoutGroupId = useMemo(
		() => Math.random().toString( 16 ).substring( 3 ),
		[]
	);

	const renderChildren = Children.map( children, ( child, index ) => {
		if ( isValidElement( child ) ) {
			return cloneElement( child, { index } );
		}
		return child;
	} );

	const handleChange = ( newValue ) => () => {
		if ( typeof onChange !== 'function' ) {
			console.error( 'ButtonGroup: onChange prop must be a function' );
			return;
		}

		onChange( newValue );
	};

	const contextValue = useMemo(
		() => ( {
			onChange: handleChange,
			selectedValue: value,
			by,
			lastItemIndex: Children.count( children ) - 1,
		} ),
		[ onChange, value, children ]
	);

	return (
		<div className="isolate inline-flex rounded-md shadow-sm border border-solid border-zip-dark-theme-border divide-solid divide-x divide-zip-dark-theme-border">
			<LayoutGroup id={ `button-group-${ layoutGroupId }` }>
				<ButtonGroupContext.Provider value={ contextValue }>
					{ renderChildren }
				</ButtonGroupContext.Provider>
			</LayoutGroup>
		</div>
	);
}
ButtonGroup = memo( ButtonGroup );

ButtonGroup.ButtonItem = ( {
	children,
	value,
	className,
	index,
	...props
} ) => {
	const { onChange, lastItemIndex, selectedValue, by } =
		// eslint-disable-next-line react-hooks/rules-of-hooks
		useButtonGroupState();
	const isSelected = selectedValue?.[ by ] === value?.[ by ];

	const renderChildren = Children.map( children, ( child ) => {
		if ( nonElementChildrenTypes.includes( typeof child ) ) {
			return <span className="z-10">{ children }</span>;
		}
		if ( isValidElement( child ) ) {
			const existingClassName = child.props.className;
			return cloneElement( child, {
				className: classNames( 'z-10', existingClassName ),
			} );
		}
		return child;
	} );

	return (
		<button
			type="button"
			className={ classNames(
				index === 0 && 'rounded-l-md',
				index === lastItemIndex && 'rounded-r-md',
				index !== 0 && '-ml-px',
				'relative w-auto h-auto flex justify-center items-center bg-zip-dark-theme-bg p-2 text-sm font-normal text-zip-dark-theme-icon-active focus:outline-none focus-visible:outline-none border-0 shadow-sm cursor-pointer active:outline-none z-auto transition-colors ease-out duration-[250ms]',
				isSelected &&
					'text-zip-dark-theme-heading bg-zip-dark-theme-bg cursor-default z-[1]',
				isSelected && index <= lastItemIndex && '!border-transparent',
				className
			) }
			onClick={ onChange( value ) }
			{ ...props }
		>
			{ renderChildren }
			{ isSelected && (
				<motion.span
					layoutDependency={ value }
					className="bg-zip-dark-theme-content-background rounded absolute inset-0 z-0"
					layoutId="active-mode"
					transition={ {
						layout: {
							duration: 0.25,
							ease: 'easeOut',
						},
					} }
				/>
			) }
		</button>
	);
};
ButtonGroup.ButtonItem = memo( ButtonGroup.ButtonItem );

export default ButtonGroup;
