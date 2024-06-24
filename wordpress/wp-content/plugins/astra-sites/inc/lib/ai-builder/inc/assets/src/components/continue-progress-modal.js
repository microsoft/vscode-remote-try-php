import { ClipboardIcon } from '@heroicons/react/24/outline';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { STORE_KEY } from '../store';
import { removeLocalStorageItem } from '../helpers';
import { defaultOnboardingAIState } from '../store/reducer';
import Modal from './modal';
import Button from './button';

const ContinueProgressModal = () => {
	const { setContinueProgressModal, setWebsiteOnboardingAIDetails } =
		useDispatch( STORE_KEY );

	const { continueProgressModal } = useSelect( ( select ) => {
		const { getContinueProgressModalInfo } = select( STORE_KEY );
		return {
			continueProgressModal: getContinueProgressModalInfo(),
		};
	}, [] );

	const handleStartOver = () => {
		removeLocalStorageItem( 'ai-builder-onboarding-details' );
		setWebsiteOnboardingAIDetails( defaultOnboardingAIState );
		setContinueProgressModal( { open: false } );
	};

	const handleContinue = () => {
		setContinueProgressModal( { open: false } );
	};

	return (
		<Modal
			open={ continueProgressModal?.open }
			setOpen={ ( toggle, type ) => {
				if ( type === 'close-icon' ) {
					handleContinue();
				}
			} }
			width={ 480 }
			height="280"
			overflowHidden={ false }
			className={ 'px-8 pt-8 pb-8 font-sans' }
		>
			<div>
				<div className="flex items-center gap-3">
					<ClipboardIcon className="w-8 h-8 text-accent-st" />
					<div className="font-bold text-2xl leading-8 text-zip-app-heading">
						{ __( 'Resume your last session?', 'ai-builder' ) }
					</div>
				</div>

				<div className="mt-5">
					<div className="text-zip-body-text text-base font-normal leading-6">
						{ `It appears that your previous website building session was interrupted. Would you like to pick up where you left off?` }
					</div>
					<div className="flex items-center gap-3 justify-center mt-8">
						<Button
							type="submit"
							variant="primary"
							size="medium"
							className="min-w-[206px] text-sm font-semibold leading-5 px-5"
							onClick={ handleContinue }
						>
							{ __( 'Resume Previous Session', 'ai-builder' ) }
						</Button>
						<Button
							variant="white"
							size="medium"
							onClick={ handleStartOver }
							className="min-w-[206px] text-sm font-semibold leading-5"
						>
							{ __( 'Start Over', 'ai-builder' ) }
						</Button>
					</div>
				</div>
			</div>
		</Modal>
	);
};

export default ContinueProgressModal;
