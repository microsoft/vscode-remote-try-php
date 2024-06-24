import { memo, renderToString } from '@wordpress/element';
import Modal from './modal';
import { ExclamationTriangleColorfulIcon } from '../ui/icons';
import ModalTitle from './modal-title';
import { __, sprintf } from '@wordpress/i18n';
import Button from './button';

const supportLink = (
	<a
		href="https://wpastra.com/contact"
		target="_blank"
		className="text-accent-st"
		rel="noreferrer"
	>
		{ __( 'here', 'ai-builder' ) }
	</a>
);

const InformPreviousErrorModal = ( {
	open,
	setOpen,
	onConfirm,
	errorString,
} ) => {
	const handleBack = () => {
		if ( typeof setOpen !== 'function' ) {
			return;
		}
		setOpen( false );
	};

	const handleConfirm = () => {
		if ( typeof onConfirm !== 'function' ) {
			return;
		}
		onConfirm();
	};

	return (
		<Modal
			open={ open }
			setOpen={ setOpen }
			className="sm:w-full sm:max-w-lg"
		>
			<ModalTitle>
				<ExclamationTriangleColorfulIcon className="w-6 h-6 text-alert-success" />
				<h5 className="text-lg text-zip-app-heading">
					{ __( 'Problem Detected in Site Creation!', 'ai-builder' ) }
				</h5>
			</ModalTitle>
			<p className="!mt-3 text-sm leading-5 font-normal text-zip-body-text">
				{ __(
					'We encountered the following errors while creating your site:',
					'ai-builder'
				) }
			</p>
			<div className="space-y-5">
				<div className="mb-5 text-zip-body-text text-sm font-normal leading-6 bg-gray-100 p-4 max-h-[250px] max-w-full overflow-auto border border-solid border-border-primary rounded-md">
					{ /* Errors */ }
					{ errorString || 'Not enough information to display.' }
				</div>

				<p
					className="!mt-3 text-sm leading-5 font-normal text-zip-body-text"
					dangerouslySetInnerHTML={ {
						__html: sprintf(
							// translators: %s: support link
							__(
								'If you proceed without resolving these issues, you may encounter the same errors again, which could exhaust your AI site creation attempts. Please address the errors before continuing. For assistance, reach out to us %1$s. Do you still want to continue?',
								'ai-builder'
							),
							renderToString( supportLink )
						),
					} }
				></p>

				<div className="space-y-4">
					<Button
						className="w-full shadow-lg"
						variant="primary"
						onClick={ handleConfirm }
					>
						{ __( 'Continue Building', 'ai-builder' ) }
					</Button>
					<Button
						className="w-fit px-2 py-0 mx-auto text-accent-st"
						variant="blank"
						onClick={ handleBack }
					>
						{ __( 'Back', 'ai-builder' ) }
					</Button>
				</div>
			</div>
		</Modal>
	);
};

export default memo( InformPreviousErrorModal );
