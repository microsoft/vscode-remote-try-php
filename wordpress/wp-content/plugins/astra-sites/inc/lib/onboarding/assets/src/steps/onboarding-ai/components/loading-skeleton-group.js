import React from 'react';
import LoadingSkeleton from './loading-skeleton';

const LoadingSkeletonGroup = ( { layout, className } ) => {
	return (
		<div className={ className }>
			{ layout.map( ( classname, index ) => (
				<LoadingSkeleton key={ index } className={ classname } />
			) ) }
		</div>
	);
};

export default LoadingSkeletonGroup;
