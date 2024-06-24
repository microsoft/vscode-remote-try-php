import Tippy from '@tippyjs/react';

const Tooltip = ( { children, content, offset, placement = 'top' } ) => {
	return content ? (
		<Tippy
			arrow
			content={ content }
			className="zw-tooltip zw-xs-normal bg-app-tooltip px-0.5 py-1.5 flex items-center justify-left text-justify"
			offset={ offset } // [x,y]
			placement={ placement }
		>
			{ children }
		</Tippy>
	) : (
		<div>{ children }</div>
	);
};

export default Tooltip;
