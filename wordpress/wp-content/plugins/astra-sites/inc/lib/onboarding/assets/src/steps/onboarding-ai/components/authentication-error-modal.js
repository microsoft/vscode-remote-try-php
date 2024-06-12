import { useSelect, useDispatch } from '@wordpress/data';
import { ExclamationTriangleColorfulIcon } from '../../ui/icons';
import { STORE_KEY } from '../store';
import Modal from './modal';
import ModalTitle from './modal-title';
import Button from './button';
import { __ } from '@wordpress/i18n';
import LoadingSpinner from './loading-spinner';
import { useCallback, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const AuthenticationErrorModal = ( { onOpenChange } ) => {
	const { setAuthenticationErrorModal } = useDispatch( STORE_KEY );
	const [ loading, setLoading ] = useState( false );

	const { authenticationErrorModal } = useSelect( ( select ) => {
		const { getAuthenticationErrorModalInfo } = select( STORE_KEY );

		return {
			authenticationErrorModal: getAuthenticationErrorModalInfo(),
		};
	} );

	const handleRevoke = async () => {
		try {
			const response = await apiFetch( {
				path: '/zipwp/v1/revoke-access',
				method: 'POST',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
					'content-type': 'application/json',
				},
			} );
			if ( response.success ) {
				setAuthenticationErrorModal( {
					...authenticationErrorModal,
					open: false,
				} );
				return true;
			}
			return false;
		} catch ( error ) {
			// TODO: Handle error.
			console.log( error );
		} finally {
			setLoading( false );
		}
	};

	const handleRevokeAndCreateAuth = useCallback( async () => {
		if ( loading ) {
			return;
		}
		setLoading( true );
		const success = await handleRevoke();
		if ( success ) {
			const url =
				wpApiSettings?.zipwp_auth?.screen_url +
				'?type=token&redirect_url=' +
				wpApiSettings?.zipwp_auth?.redirect_url +
				'&ask=/register' +
				( wpApiSettings?.zipwp_auth?.partner_id
					? '&aff=' + wpApiSettings?.zipwp_auth?.partner_id
					: '' );
			window.location.href = url;
		}
	}, [] );

	const handleRevokeAndExit = useCallback( async () => {
		const success = await handleRevoke();
		if ( success ) {
			window.location.reload();
		}
	}, [] );

	const displayMessage = (
		<span>
			{ __(
				'Please check your username and password for the account, and try to reconnect your Zip account.',
				'astra-sites'
			) }
		</span>
	);

	return (
		<Modal
			open={ authenticationErrorModal?.open }
			setOpen={ ( toggle ) => {
				if ( typeof onOpenChange === 'function' ) {
					onOpenChange( toggle );
				}

				setAuthenticationErrorModal( {
					...authenticationErrorModal,
					open: toggle,
				} );
			} }
			width={ 464 }
			height="200"
			overflowHidden={ false }
			hideCloseIcon={ true }
			className="p-0"
			isErrorModal={ true }
			autoClose={ false }
		>
			<div className="px-5 py-5 space-y-5">
				<ModalTitle>
					<ExclamationTriangleColorfulIcon className="w-6 h-6" />
					<span className="text-xl font-bold leading-7">
						{ __( 'Authentication failed', 'astra-sites' ) }
					</span>
				</ModalTitle>
				<div className="space-y-7">
					<div className="text-zip-body-text text-base">
						<div className="text-base font-normal leading-5">
							{ displayMessage }
						</div>
					</div>
					<div className="flex items-center justify-start gap-3">
						<Button
							variant="primary"
							className="text-base"
							onClick={ handleRevokeAndCreateAuth }
						>
							{ loading ? (
								<LoadingSpinner />
							) : (
								__( 'Retry', 'astra-sites' )
							) }
						</Button>
						<Button
							className="bg-white border-border-primary text-zip-app-heading text-base"
							variant="primary"
							onClick={ handleRevokeAndExit }
						>
							{ __( 'Exit', 'astra-sites' ) }
						</Button>
					</div>
				</div>
			</div>
			<div className="flex w-full h-auto px-4 py-3 gap-2 bg-gray-100 rounded-b-lg">
				<span className="text-zip-body-text text-base font-normal leading-5">
					{ __( 'Need help?', 'astra-sites' ) }
					<a
						href={ starterTemplates.supportLink }
						className="text-accent-st ml-1"
						target="_blank"
						rel="noopener noreferrer"
					>
						{ __( 'Contact Support', 'astra-sites' ) }
					</a>
				</span>
			</div>
		</Modal>
	);
};

export default AuthenticationErrorModal;
