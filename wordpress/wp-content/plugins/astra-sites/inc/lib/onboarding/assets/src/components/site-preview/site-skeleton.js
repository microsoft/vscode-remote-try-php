import { Skeleton } from '@mui/material';
import Grid from '@mui/material/Grid';
import Box from '@mui/material/Box';
import { classNames } from '../../steps/onboarding-ai/helpers';

const SiteSkeleton = ( { className } ) => {
	return (
		<div className={ classNames( 'site-loading-skeleton', className ) }>
			<Grid container>
				<Grid item xs={ 4 }>
					<Box p={ '3em 8em' } display="flex">
						<Skeleton
							variant="rect"
							width={ 234 }
							height={ 45 }
							animation="wave"
						/>
					</Box>
				</Grid>
				<Grid item xs={ 8 }>
					<Box
						p="3em 8em"
						display="flex"
						justifyContent="flex-end"
						gap="25px"
					>
						<Skeleton
							variant="rect"
							width={ 100 }
							height={ 45 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width={ 100 }
							height={ 45 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width={ 100 }
							height={ 45 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width={ 100 }
							height={ 45 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width={ 200 }
							height={ 45 }
							animation="wave"
						/>
					</Box>
				</Grid>
				<Grid item xs={ 6 }>
					<Box
						p="1rem 2rem 1rem 8rem"
						display="flex"
						flexDirection="column"
						height="100%"
					>
						<Box>
							<Skeleton
								variant="rect"
								height={ 120 }
								animation="wave"
							/>
						</Box>
						<Box m="1em 0">
							<Skeleton
								variant="rect"
								height={ 20 }
								animation="wave"
							/>
						</Box>
						<Box m="1em 0">
							<Skeleton
								variant="rect"
								height={ 20 }
								animation="wave"
							/>
						</Box>
						<Box m="3em 0">
							<Skeleton
								variant="rect"
								width={ 250 }
								height={ 50 }
								animation="wave"
							/>
						</Box>
					</Box>
				</Grid>
				<Grid item xs={ 6 }>
					<Box
						p="1rem 8rem 1rem 2rem"
						display="flex"
						justifyContent="flex-end"
						gap="25px"
					>
						<Skeleton
							variant="rect"
							width={ 516 }
							height={ 320 }
							animation="wave"
						/>
					</Box>
				</Grid>
				<Grid item xs={ 12 }>
					<Box
						p="3em 8em"
						display="flex"
						justifyContent="space-evenly"
						gap="25px"
					>
						<Skeleton
							variant="rect"
							width="100%"
							height={ 320 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width="100%"
							height={ 320 }
							animation="wave"
						/>
						<Skeleton
							variant="rect"
							width="100%"
							height={ 320 }
							animation="wave"
						/>
					</Box>
				</Grid>
			</Grid>
		</div>
	);
};

export default SiteSkeleton;
