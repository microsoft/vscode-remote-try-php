import { memo } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { STORE_KEY } from '../store';
import Modal from './modal';
import { SirenColorfulIcon } from '../ui/icons';
import ModalTitle from './modal-title';
import { __ } from '@wordpress/i18n';
import ToggleSwitch from './toggle-switch';
import Button from './button';

const PreBuildConfirmModal = ( { open, setOpen, startBuilding } ) => {
	const { reset } = useSelect( ( select ) => {
		const { getImportSiteProgressData } = select( STORE_KEY );
		return {
			...getImportSiteProgressData(),
		};
	}, [] );
	const { updateImportAiSiteData } = useDispatch( STORE_KEY );

	const handleChange = () => {
		updateImportAiSiteData( { reset: ! reset } );
	};

	const handleStartBuilding = () => {
		if ( typeof startBuilding !== 'function' ) {
			return;
		}
		setOpen( false );
		startBuilding();
	};

	return (
		<Modal open={ open } setOpen={ setOpen } className="sm:w-[27.5rem]">
			<ModalTitle>
				<SirenColorfulIcon className="w-6 h-6 text-alert-success" />
				<h5 className="text-lg text-zip-app-heading">
					{ __( 'Hold On!', 'ai-builder' ) }
				</h5>
			</ModalTitle>
			<p className="!mt-3 text-sm leading-5 font-normal text-zip-body-text">
				{ __(
					"It looks like you already have a website made with Starter Templates. Clicking the 'Start Building' button will recreate the site, and all previous data will be overridden.",
					'ai-builder'
				) }
			</p>
			<div className="space-y-5">
				<div className="p-5 border border-solid border-border-primary rounded-md grid grid-cols-[1fr_min-content] gap-4">
					<div className="space-y-1">
						<h6 className="text-sm leading-5 text-zip-app-heading">
							{ __(
								'Maintain previous/old data?',
								'ai-builder'
							) }
						</h6>
						<p className="text-sm leading-5 font-normal text-zip-body-text">
							{ __(
								'Enabling this option will maintain your old Starter Templates data, including content and images. Enable it to confirm.',
								'ai-builder'
							) }
						</p>
					</div>
					<div className="flex items-center justify-center">
						<ToggleSwitch
							onChange={ handleChange }
							value={ ! reset }
							variant="light"
						/>
					</div>
				</div>
				<div className="space-y-4">
					<Button
						className="w-full shadow-lg"
						variant="primary"
						onClick={ handleStartBuilding }
					>
						{ __( 'Start Building', 'ai-builder' ) }
					</Button>
					<Button
						className="w-fit px-2 py-0 mx-auto text-accent-st"
						variant="blank"
						onClick={ () => setOpen( false ) }
					>
						{ __( 'Back', 'ai-builder' ) }
					</Button>
				</div>
			</div>
		</Modal>
	);
};

export default memo( PreBuildConfirmModal );
