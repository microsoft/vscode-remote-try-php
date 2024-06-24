import React from 'react';
import Button from '../../button/button';
import { useStateValue } from '../../../store/store';

const NextStepButton = ( props ) => {
	const {
		children,
		after,
		before,
		gray,
		large,
		mb1,
		ml1,
		onClick,
		customizeStep,
		disabled,
	} = props;
	const storedState = useStateValue();
	const [ { currentIndex }, dispatch ] = storedState;
	return (
		<Button
			gray={ gray }
			large={ large }
			mb1={ mb1 }
			ml1={ ml1 }
			before={ before }
			after={ after }
			onClick={ ( event ) => {
				if ( true !== customizeStep ) {
					dispatch( {
						type: 'set',
						currentIndex: currentIndex + 1,
					} );
				}

				if ( typeof onClick === 'function' ) {
					onClick( event );
				}
			} }
			disabled={ disabled }
		>
			{ children }
		</Button>
	);
};

export default NextStepButton;
