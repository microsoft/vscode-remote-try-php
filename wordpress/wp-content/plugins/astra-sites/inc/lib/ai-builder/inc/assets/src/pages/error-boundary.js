import { __, sprintf } from '@wordpress/i18n';
import Button from '../components/button';
import { useDispatch } from '@wordpress/data';
import { ExclamationTriangleColorfulIcon } from '../ui/icons';
import { Component } from 'react';
import { STORE_KEY } from '../store';

class ErrorBoundary extends Component {
	constructor( props ) {
		super( props );
		this.state = { hasError: false };
	}

	static getDerivedStateFromError() {
		// Update state so the next render will show the fallback UI.
		return { hasError: true };
	}

	componentDidCatch( error, errorInfo ) {
		console.error( 'ErrorBoundary caught an error', error, errorInfo );
	}

	retryStep = () => {};
	handleClosePopUop = ( event ) => {
		event?.preventDefault();
		event?.stopPropagation();
		window.location.href = `${ aiBuilderVars.adminUrl }`;
	};
	render() {
		if ( this.state.hasError ) {
			return (
				<div className="h-screen w-full grid grid-cols-1 grid-rows-[80px_1fr]">
					<div className="grid grid-cols-1 auto-rows-min gap-4 w-full max-w-[590px] my-32 mx-auto text-center px-5 lg:px-0">
						<div className="space-y-3">
							<ExclamationTriangleColorfulIcon className="mx-auto w-6 h-6" />
							<h5>
								{ __(
									'Oops , Something went wrong!',
									'ai-builder'
								) }
							</h5>
							<p
								className="text-zip-body-text"
								dangerouslySetInnerHTML={ {
									__html: sprintf(
										/* translators: %1$s: Contact us link */
										__(
											'There was a problem processing the request. Please try again. If this error continues please contact our <a href="%1$s">support team</a>.',
											'ai-builder'
										),
										`${ aiBuilderVars.supportLink }`
									),
								} }
							></p>
						</div>
						<div className="flex justify-center space-x-4">
							<Button
								type="button"
								variant="primary"
								className="mt-4 w-fit mx-auto"
								isSmall
								onClick={ this.handleClosePopUop }
							>
								{ __( 'Back to Main Screen', 'ai-builder' ) }
							</Button>
						</div>
					</div>
				</div>
			);
		}
		return this.props.children;
	}
}

// Functional wrapper component to use the dispatch hook
const ErrorBoundaryWrapper = ( props ) => {
	const { toggleOnboardingAIStep } = useDispatch( STORE_KEY );

	return (
		<ErrorBoundary
			{ ...props }
			toggleOnboardingAIStep={ toggleOnboardingAIStep }
		/>
	);
};

export default ErrorBoundaryWrapper;
