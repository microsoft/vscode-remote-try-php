import { memo } from '@wordpress/element';
import Modal from './modal';
import { PremiumAiIcon } from '../ui/icons';
import ModalTitle from './modal-title';
import { __, sprintf } from '@wordpress/i18n';
import Button from './button';

const PremiumConfirmModal = ( { open, setOpen } ) => {
	const handleStartBuilding = () => {
		window.open( 'https://app.zipwp.com/founders-deal', '_blank' );
	};
	return (
		<Modal open={ open } setOpen={ setOpen } className="sm:w-[27.5rem]">
			<ModalTitle>
				<PremiumAiIcon className="w-5 h-5" />
				<h5 className="text-xl text-zip-app-heading font-semibold">
					{ __( "You're almost there!", 'ai-builder' ) }
				</h5>
			</ModalTitle>
			<p
				className="!mt-3 text-base leading-6 font-normal text-zip-body-text"
				dangerouslySetInnerHTML={ {
					__html: sprintf(
						/* translators: %s: span tag */
						__(
							"You've chosen a %1$s Access this design and all others when you upgrade.",
							'ai-builder'
						),
						"<span class='text-zip-app-heading font-semibold'>Premium Design.</span>"
					),
				} }
			/>

			<div className="space-y-5">
				<Button
					className="w-full shadow-lg"
					variant="primary"
					onClick={ handleStartBuilding }
				>
					{ __( 'Unlock the Access', 'ai-builder' ) }
				</Button>
				<Button
					className="w-fit px-2 py-0 mx-auto text-accent-st"
					variant="blank"
					onClick={ () => setOpen( false ) }
				>
					{ __( 'Back', 'ai-builder' ) }
				</Button>
			</div>
		</Modal>
	);
};

export default memo( PremiumConfirmModal );
