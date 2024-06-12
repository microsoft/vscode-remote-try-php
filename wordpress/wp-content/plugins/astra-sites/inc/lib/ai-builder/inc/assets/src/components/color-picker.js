import { Popover } from '@headlessui/react';
import usePopper from '../hooks/use-popper';
import { ChromePicker as Chrome } from 'react-color';
import { Swatch } from 'react-color/lib/components/common/Swatch';

const defaultSwatches = [
	'#A66E84',
	'#5F6243',
	'#4975B1',
	'#B66855',
	'#6E6C82',
	'#72A549',
	'#964976',
	'#5A5DD2',
	'#B34443',
];

const ColorPicker = ( {
	value,
	onChange,
	placement = 'top-start',
	children,
} ) => {
	const [ reference, popperReference ] = usePopper( {
		placement,
		modifiers: [ { name: 'offset', options: { offset: [ 0, 10 ] } } ],
	} );

	const handleOnChange = ( color ) => {
		if ( typeof onChange !== 'function' ) {
			return;
		}
		onChange( color );
	};

	return (
		<Popover className="relative flex items-center">
			<Popover.Button
				ref={ reference }
				className="m-0 p-0 bg-transparent cursor-pointer border-none focus:outline-none"
			>
				{ children }
			</Popover.Button>
			<Popover.Panel
				ref={ popperReference }
				className="absolute z-10 bg-white rounded-sm overflow-hidden"
			>
				<Chrome
					className="!w-[276px] [&>:nth-child(2)>:nth-child(2)>:nth-child(2)>div]:!-mt-px !shadow-none"
					color={ value }
					onChange={ handleOnChange }
					disableAlpha
				/>
				<hr className="border-b border-t-0 border-solid border-border-primary mt-0 mb-2 mx-4" />
				<div className="px-4 pb-3 flex items-center flex-wrap gap-2 justify-start">
					{ defaultSwatches.map( ( color ) => (
						<Swatch
							key={ color }
							style={ { width: '20px', height: '20px' } }
							color={ color }
							onClick={ () => handleOnChange( { hex: color } ) }
						/>
					) ) }
				</div>
			</Popover.Panel>
		</Popover>
	);
};

export default ColorPicker;
