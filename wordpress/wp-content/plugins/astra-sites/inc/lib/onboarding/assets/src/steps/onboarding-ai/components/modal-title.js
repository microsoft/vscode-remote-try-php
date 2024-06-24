import { classNames } from '../utils/helpers';

const ModalTitle = ( { children, className } ) => {
	return (
		<div
			className={ classNames(
				'flex items-center text-2xl font-semibold leading-8 text-heading-text space-x-3',
				className
			) }
		>
			{ children }
		</div>
	);
};

export default ModalTitle;
