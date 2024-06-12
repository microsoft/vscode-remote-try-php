import React from 'react';

const StyledText = ( { text } ) => {
	return (
		<span className="relative">
			<span className="">{ text }</span>
			<span className="text-transparent gradient-border-bottom left-0 bottom-[1px] absolute">
				{ text }
			</span>
		</span>
	);
};

export default StyledText;
