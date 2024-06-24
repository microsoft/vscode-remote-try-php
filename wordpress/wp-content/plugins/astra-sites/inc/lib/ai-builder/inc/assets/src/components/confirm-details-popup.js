import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import Modal from './modal';
import Button from './button';
import { STORE_KEY } from '../store';
import { classNames } from '../helpers';
import Divider from './divider';
import LoadingSpinner from './loading-spinner';

const ConfirmationModal = ( { open, setOpen, onClickGenerate } ) => {
	const {
		// businessType: { name: businessType },
		businessName,
		businessDetails,
		businessContact: { email, phone, address },
	} = useSelect( ( select ) => {
		const { getAIStepData, getOnboardingAI, getAllPatternsCategories } =
			select( STORE_KEY );

		return {
			...getAIStepData(),
			isNewUser: getOnboardingAI()?.isNewUser,
			allPatternsCategories: getAllPatternsCategories(),
		};
	}, [] );

	const [ isLoading, setIsLoading ] = useState( false );

	const [ showFullDescription, setShowFullDescription ] = useState( false );

	const handleShowFullDescription = () => {
		setShowFullDescription( true );
	};

	return (
		<Modal width="640" open={ open } setOpen={ setOpen }>
			<div className="font-sans">
				<h1 className="font-bold">
					{ __(
						"Congratulations, you're almost there!",
						'ai-builder'
					) }{ ' ' }
					🎉
				</h1>
				<div className="pt-2 text-base text-app-text">
					{ __(
						"Before we hit the final button, let's quickly double-check everything.",
						'ai-builder'
					) }
				</div>
			</div>
			<div className="mt-4 custom-confirmation-modal-scrollbar border border-solid border-gray-300 rounded-md p-2 ">
				<div className="space-y-4 p-3 overflow-y-auto max-h-[19rem]">
					<div className="">
						<div className="font-bold leading-6 text-base">
							{ __( 'Business Name:', 'ai-builder' ) }
						</div>
						<div className="text-app-heading">{ businessName }</div>
					</div>
					<Divider className={ 'mt-0' } />
					<div className="">
						<div className="font-bold leading-6 text-base">
							{ __( 'Business Description:', 'ai-builder' ) }
						</div>
						{ businessDetails &&
						businessDetails > 140 &&
						! showFullDescription ? (
							<>
								<div className="text-app-heading">
									{ businessDetails.slice( 0, 140 ) }
									... { '' }
									<button
										className="text-blue-500 hover:underline cursor-pointer"
										onClick={ handleShowFullDescription }
									>
										{ __( 'Show More', 'ai-builder' ) }
									</button>
								</div>
							</>
						) : (
							<div className="text-app-heading">
								{ businessDetails }
							</div>
						) }
					</div>
					{ !! ( email || phone || address ) && (
						<>
							<Divider className={ 'mt-0' } />

							<div>
								<div className="font-bold leading-6 text-base">
									{ __( 'Contact Details:', 'ai-builder' ) }
								</div>
								<div className="text-app-heading">
									{ email }
								</div>
								<div className="text-app-heading">
									{ phone }
								</div>
								<div className="text-app-heading">
									{ address }
								</div>
							</div>
						</>
					) }
				</div>
			</div>

			<div className="mt-4 space-y-4 text-center">
				<Button
					className={ classNames( 'w-full min-w-fit min-h-[45px]' ) }
					variant="primary"
					hasSuffixIcon={ isLoading }
					onClick={ ( e ) => {
						setIsLoading( true );
						onClickGenerate( e );
					} }
				>
					{ isLoading ? (
						<LoadingSpinner />
					) : (
						__( 'Yes! Build This Website.', 'ai-builder' )
					) }
				</Button>

				<Button
					className={ classNames( 'w-full min-h-[45px]' ) }
					variant="white"
					onClick={ () => setOpen( false ) }
				>
					{ __( 'Back', 'ai-builder' ) }
				</Button>
			</div>
		</Modal>
	);
};

export default ConfirmationModal;
