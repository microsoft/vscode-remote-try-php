import { classNames } from '../helpers';

const LoadingSkeleton = ( { className } ) => {
	return (
		<div
			className={ classNames(
				`w-full h-10 bg-gray-300 rounded animate-pulse`,
				className
			) }
		/>
	);
};

export default LoadingSkeleton;
