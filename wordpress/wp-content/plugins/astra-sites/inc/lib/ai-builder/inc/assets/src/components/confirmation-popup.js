import Modal from './modal';
import Button from './button';
import { ExclamationCircleIcon } from '@heroicons/react/24/outline';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import LoadingSpinner from './loading-spinner';
import { classNames } from '../helpers';

const ConfirmationPopup = ( {
	open,
	setOpen,
	title,
	description,
	confirmBtnTitle = __( 'Confirm', 'ai-builder' ),
	cancelBtnTitle = __( 'Cancel', 'ai-builder' ),
	onClickConfirm,
	onClickCancel,
	variant = 'danger',
} ) => {
	const [ loading, setLoading ] = useState( false );
	const variants = {
		iconBackground: {
			info: 'bg-alert-info-bg',
			danger: 'bg-alert-error-bg',
		},
		icon: {
			info: <ExclamationCircleIcon className="w-8 h-8 text-alert-info" />,
			danger: (
				<ExclamationCircleIcon className="w-8 h-8 text-alert-error" />
			),
		},
		confirmButton: {
			info: { className: '' },
			danger: {
				className: 'bg-alert-error border-alert-error text-white',
			},
		},
	};

	const handleClickConfirm = async ( event ) => {
		if ( loading ) {
			return;
		}
		setLoading( true );
		if ( typeof onClickConfirm !== 'function' ) {
			return;
		}
		await onClickConfirm( event );
	};

	const handleClickCancel = ( event ) => {
		if ( typeof onClickCancel !== 'function' ) {
			return;
		}
		onClickCancel( event, false );
	};

	const updateWidth = ( node ) => {
		if ( ! node ) {
			return;
		}
		setTimeout( () => {
			if ( node.offsetWidth === 0 ) {
				return;
			}
			const width = node.offsetWidth;
			node.style.width = `${ width }px`;
		}, 350 );
	};

	return (
		<Modal
			open={ open }
			setOpen={ setOpen }
			overlayClassName="bg-border-secondary/80"
			width={ 640 }
		>
			<div className="flex items-start justify-start gap-4">
				<span
					className={ classNames(
						'w-auto h-auto flex items-center justify-center p-3 bg-alert-error-bg rounded-full',
						variants.iconBackground[ variant ]
					) }
				>
					{ variants.icon[ variant ] ?? variants.icon.danger }
				</span>
				<div className="space-y-6">
					<div className="space-y-2">
						<h5 className="flex items-center text-2xl font-semibold leading-8 text-heading-text space-x-3 !text-[1.375rem] m-0">
							{ title }
						</h5>
						<p className="m-0 text-base font-normal text-body-text">
							{ description }
						</p>
					</div>
					<div className="flex items-center justify-start gap-3">
						<Button
							ref={ updateWidth }
							className={ classNames(
								variants.confirmButton[ variant ].className,
								'text-base'
							) }
							variant="primary"
							onClick={ handleClickConfirm }
						>
							{ loading ? <LoadingSpinner /> : confirmBtnTitle }
						</Button>
						<Button
							className="bg-white border-border-primary text-nav-inactive text-base"
							variant="primary"
							onClick={ handleClickCancel }
						>
							{ cancelBtnTitle }
						</Button>
					</div>
				</div>
			</div>
		</Modal>
	);
};

export default ConfirmationPopup;
