import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { STORE_KEY } from '../store';
import LoadingSpinner from './loading-spinner';
import { classNames } from '../utils/helpers';
import Button from './button';

const NavigationButtons = ( {
	continueButtonText = __( 'Continue', 'ai-builder' ),
	onClickContinue,
	onClickPrevious,
	onClickSkip,
	disableContinue,
	loading = false,
	hideContinue = false,
	className,
	skipButtonText = __( 'Skip Step', 'ai-builder' ),
} ) => {
	const { setLoadingNextStep } = useDispatch( STORE_KEY );
	const { loadingNextStep } = useSelect( ( select ) => {
		const { getLoadingNextStep } = select( STORE_KEY );

		return {
			loadingNextStep: getLoadingNextStep(),
		};
	}, [] );

	const handleOnClick = async ( event, onClickFunction ) => {
		if ( loadingNextStep ) {
			return;
		}
		setLoadingNextStep( true );
		if ( typeof onClickFunction === 'function' ) {
			await onClickFunction( event );
		}
		setLoadingNextStep( false );
	};

	const handleOnClickContinue = ( event ) =>
		handleOnClick( event, onClickContinue );
	const handleOnClickPrevious = ( event ) =>
		handleOnClick( event, onClickPrevious );
	const handleOnClickSkip = ( event ) => handleOnClick( event, onClickSkip );

	useEffect( () => {
		if ( loadingNextStep === loading ) {
			return;
		}
		setLoadingNextStep( loading );
	}, [ loading ] );

	return (
		<div
			className={ classNames(
				'w-full flex items-center gap-4 flex-wrap md:flex-nowrap',
				className
			) }
		>
			<div className="flex gap-4">
				{ ! hideContinue && (
					<Button
						type="submit"
						className="min-w-[9.375rem] h-[3.125rem]"
						onClick={ handleOnClickContinue }
						variant="primary"
						hasSuffixIcon={ ! loadingNextStep && ! loading }
						disabled={ disableContinue }
					>
						{ loadingNextStep || loading ? (
							<LoadingSpinner />
						) : (
							<>
								<span>{ continueButtonText }</span>
								<ArrowRightIcon className="w-5 h-5" />
							</>
						) }
					</Button>
				) }
				{ typeof onClickPrevious === 'function' && (
					<Button
						type="button"
						className="h-[3.125rem]"
						onClick={ handleOnClickPrevious }
						variant="white"
					>
						<span>{ __( 'Previous Step', 'ai-builder' ) }</span>
					</Button>
				) }
			</div>
			{ typeof onClickSkip === 'function' && (
				<Button
					type="button"
					className="h-[3.125rem] mr-auto ml-0 md:mr-0 md:ml-auto text-secondary-text"
					onClick={ handleOnClickSkip }
					variant="blank"
				>
					<span>{ skipButtonText }</span>
				</Button>
			) }
		</div>
	);
};

export default NavigationButtons;
