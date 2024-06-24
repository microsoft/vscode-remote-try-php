import React from 'react';
import Button from '../../button/button';
import { useStateValue } from '../../../store/store';

const PreviousStepButton = ( { children, customizeStep } ) => {
	const [ { currentIndex }, dispatch ] = useStateValue();
	return (
		<Button
			type="hero"
			onClick={ () => {
				if ( true !== customizeStep ) {
					dispatch( {
						type: 'set',
						currentIndex: currentIndex - 1,
					} );
				}
			} }
		>
			{ children }
		</Button>
	);
};

export default PreviousStepButton;
