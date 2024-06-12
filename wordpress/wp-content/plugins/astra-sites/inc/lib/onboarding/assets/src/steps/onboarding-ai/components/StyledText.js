import React from 'react';

const StyledText = ( { text } ) => {
	return (
		<span className="text-center relative">
			<span className="bg-gradient-to-r from-gradient-color-1/50 via-gradient-color-2/50 to-gradient-color-3/50  bg-[length:100%_6px] bg-no-repeat bg-bottom pb-0 ">
				{ text }
			</span>
		</span>
	);
};

export default StyledText;
