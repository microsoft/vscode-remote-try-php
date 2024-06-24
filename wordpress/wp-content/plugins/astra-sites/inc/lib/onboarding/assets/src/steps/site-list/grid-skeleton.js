import React from 'react';
import { useState, useEffect } from '@wordpress/element';
import { Skeleton } from '@mui/material';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';

const ROW_COUNT = 2;

const GridSkeleton = () => {
	const [ columns, setColumns ] = useState( 4 ); // Default number of columns

	useEffect( () => {
		const handleResize = () => {
			// Update the number of columns based on the screen width
			if ( window.innerWidth <= 768 ) {
				setColumns( 2 ); // Adjust for smaller screens
			} else if ( window.innerWidth > 768 && window.innerWidth <= 1024 ) {
				setColumns( 3 );
			} else {
				setColumns( 4 ); // Default for larger screens
			}
		};

		// Listen for window resize events
		window.addEventListener( 'resize', handleResize );

		// Call handleResize initially to set the correct number of columns.
		handleResize();

		// Clean up the event listener on component unmount
		return () => window.removeEventListener( 'resize', handleResize );
	}, [] );

	// Create an array of Skeleton components based on the number of columns
	const skeletonItems = [];
	for ( let i = 0; i < columns * ROW_COUNT; i++ ) {
		skeletonItems.push(
			<Skeleton
				key={ i }
				variant="rect"
				height={ 380 }
				animation="wave"
			/>
		);
	}

	return (
		<div className="st-grid-skeleton">
			<Grid container>
				<Grid item xs={ 12 }>
					<Box
						p="0"
						display="grid"
						gap="40px"
						gridTemplateColumns={ `repeat(${ columns }, 1fr)` }
					>
						{ skeletonItems }
					</Box>
				</Grid>
			</Grid>
		</div>
	);
};

export default GridSkeleton;
