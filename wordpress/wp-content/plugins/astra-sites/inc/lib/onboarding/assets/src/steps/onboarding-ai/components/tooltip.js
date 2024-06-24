import React from 'react';
import Tippy from '@tippyjs/react';
import '../../../variables.scss';

const Tooltip = ( {
	children,
	content,
	offset,
	placement = 'top',
	interactive = false,
	delay = [ 300, 0 ],
} ) => {
	return content ? (
		<Tippy
			arrow
			content={ content }
			className="zw-tooltip zw-xs-normal flex items-center justify-left"
			offset={ offset } // [x,y]
			placement={ placement }
			interactive={ interactive }
			delay={ delay }
		>
			{ children }
		</Tippy>
	) : (
		<div>{ children }</div>
	);
};

export default Tooltip;
