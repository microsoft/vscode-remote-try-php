import { classNames } from '../helpers';

const Tile = ( { className, onClick, children } ) => {
	const handleOnClick = ( event ) => {
		if ( typeof onClick === 'function' ) {
			onClick( event );
		}
	};

	return (
		<div
			onClick={ handleOnClick }
			className={ classNames( className ) }
			aria-hidden="true"
		>
			{ children }
		</div>
	);
};

export default Tile;
