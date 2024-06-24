import Button from '../components/button';
import { useNavigateSteps } from '../router';
import { __ } from '@wordpress/i18n';
import Header from '../components/header';

const NotFound404 = () => {
	const { navigateTo } = useNavigateSteps();

	const handleBackToMainScreen = () => {
		navigateTo( {
			to: '/',
		} );
	};

	return (
		<div className="h-screen w-full bg-st-background-secondary grid grid-cols-1 grid-rows-[80px_1fr]">
			<Header />
			<div className="grid grid-cols-1 auto-rows-min gap-4 w-full max-w-[590px] my-32 mx-auto text-center px-5 lg:px-0">
				<h1 className="text-heading-text">404</h1>
				<div className="space-y-3">
					<h3>
						{ __(
							'The requested URL was not found.',
							'ai-builder'
						) }
					</h3>
					<p className="text-zip-body-text">
						{ __(
							'The URL may have been typed incorrectly. Or it might be a broken or outdated link.',
							'ai-builder'
						) }
					</p>
				</div>
				<Button
					type="button"
					variant="primary"
					className="mt-4 w-fit mx-auto"
					isSmall
					onClick={ handleBackToMainScreen }
				>
					{ __( 'Back to Main Screen', 'ai-builder' ) }
				</Button>
			</div>
		</div>
	);
};

export default NotFound404;
