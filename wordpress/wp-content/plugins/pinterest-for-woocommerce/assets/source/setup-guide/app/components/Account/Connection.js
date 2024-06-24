/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@woocommerce/components';
import { getNewPath } from '@woocommerce/navigation';
import { recordEvent } from '@woocommerce/tracks';
import {
	Button,
	CardBody,
	Flex,
	FlexItem,
	FlexBlock,
	Modal,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { useCreateNotice } from '../../helpers/effects';

const PinterestLogo = () => {
	return (
		<img
			src={
				wcSettings.pinterest_for_woocommerce.pluginUrl +
				'/assets/images/pinterest-logo.svg'
			}
			alt=""
		/>
	);
};

/**
 * Clicking on "Connect" Pinterest account button.
 *
 * @event wcadmin_pfw_account_connect_button_click
 */
/**
 * Clicking on "Disconnect" Pinterest account button.
 *
 * @event wcadmin_pfw_account_disconnect_button_click
 * @property {string} context `'settings' | 'wizard'` In which context it was used?
 */
/**
 * Opening a modal.
 *
 * @event wcadmin_pfw_modal_open
 * @property {string} name Which modal is it?
 * @property {string} context `'settings' | 'wizard'` In which context it was used?
 */
/**
 * Closing a modal.
 *
 * @event wcadmin_pfw_modal_closed
 * @property {string} name Which modal is it?
 * @property {string} context `'settings' | 'wizard'` In which context it was used?
 * @property {string} action
 * 				`confirm` - When the final "Yes, I'm sure" button is clicked.
 * 				`dismiss` -  When the modal is dismissed by clicking on "x", "cancel", overlay, or by pressing a keystroke.
 */

/**
 * Pinterest account connection component.
 *
 * This renders the body of `SetupAccount` card, to connect or disconnect Pinterest account.
 *
 * @fires wcadmin_pfw_account_connect_button_click
 * @fires wcadmin_pfw_account_disconnect_button_click with the given `{ context }`
 * @fires wcadmin_pfw_modal_open with `{ name: 'account-disconnection', … }`
 * @fires wcadmin_pfw_modal_closed with `{ name: 'account-disconnection', … }`
 * @param {Object} props React props.
 * @param {boolean} props.isConnected
 * @param {Function} props.setIsConnected
 * @param {Object} props.accountData
 * @param {string} props.context Context in which the component is used, to be forwarded to fired Track Events.
 * @return {JSX.Element} Rendered element.
 */
const AccountConnection = ( {
	isConnected,
	setIsConnected,
	accountData,
	context,
} ) => {
	const createNotice = useCreateNotice();
	const modalName = 'account-disconnection';

	const [ isConfirmationModalOpen, setIsConfirmationModalOpen ] = useState(
		false
	);

	const openConfirmationModal = () => {
		recordEvent( 'pfw_account_disconnect_button_click', { context } );
		setIsConfirmationModalOpen( true );
		recordEvent( 'pfw_modal_open', { context, name: modalName } );
	};

	const closeConfirmationModal = ( event, isConfirmed ) => {
		setIsConfirmationModalOpen( false );

		recordEvent( 'pfw_modal_closed', {
			action: isConfirmed ? 'confirm' : 'dismiss',
			context,
			name: modalName,
		} );
	};

	const renderConfirmationModal = () => {
		return (
			<Modal
				title={
					<>{ __( 'Are you sure?', 'pinterest-for-woocommerce' ) }</>
				}
				onRequestClose={ closeConfirmationModal }
				className="woocommerce-setup-guide__step-modal"
			>
				<div className="woocommerce-setup-guide__step-modal__wrapper">
					<p>
						{ __(
							'Are you sure you want to disconnect this account?',
							'pinterest-for-woocommerce'
						) }
					</p>
					<div className="woocommerce-setup-guide__step-modal__buttons">
						<Button
							isDestructive
							isSecondary
							onClick={ handleDisconnectAccount }
						>
							{ __(
								"Yes, I'm sure",
								'pinterest-for-woocommerce'
							) }
						</Button>
						<Button isTertiary onClick={ closeConfirmationModal }>
							{ __( 'Cancel', 'pinterest-for-woocommerce' ) }
						</Button>
					</div>
				</div>
			</Modal>
		);
	};

	const handleDisconnectAccount = async () => {
		closeConfirmationModal( undefined, true );

		try {
			await apiFetch( {
				path:
					wcSettings.pinterest_for_woocommerce.apiRoute +
					'/auth_disconnect',
				method: 'POST',
			} );

			setIsConnected( false );

			// Force reload WC admin page to initiate the relevant dependencies of the Dashboard page.
			const path = getNewPath( {}, '/pinterest/landing', {} );

			window.location = new URL( wcSettings.adminUrl + path );
		} catch ( error ) {
			createNotice(
				'error',
				error.message ||
					__(
						'There was a problem while trying to disconnect.',
						'pinterest-for-woocommerce'
					)
			);
		}
	};

	return (
		<CardBody size="large">
			<Flex direction="row" className="connection-info">
				<FlexItem className="logo">
					<PinterestLogo />
				</FlexItem>
				{ isConnected === true ? ( // eslint-disable-line no-nested-ternary --- Code is reasonable readable
					<>
						<FlexBlock className="account-label">
							{ accountData?.id ? (
								<Text variant="body">
									{ accountData.username }

									<span className="account-type">
										{ ' - (' }
										{ accountData.is_partner
											? __(
													'Business account',
													'pinterest-for-woocommerce'
											  )
											: __(
													'Personal account',
													'pinterest-for-woocommerce'
											  ) }
										{ ')' }
									</span>
								</Text>
							) : (
								<div className="connection-info__placeholder"></div>
							) }
						</FlexBlock>

						<FlexItem>
							<Button
								isLink
								isDestructive
								onClick={ openConfirmationModal }
							>
								{ __(
									'Disconnect',
									'pinterest-for-woocommerce'
								) }
							</Button>
						</FlexItem>
					</>
				) : isConnected === false ? (
					<>
						<FlexBlock>
							<Text variant="subtitle">
								{ __(
									'Connect your Pinterest Account',
									'pinterest-for-woocommerce'
								) }
							</Text>
						</FlexBlock>

						<FlexItem>
							<Button
								isSecondary
								href={
									wcSettings.pinterest_for_woocommerce
										.serviceLoginUrl
								}
								onClick={ () =>
									recordEvent(
										'pfw_account_connect_button_click'
									)
								}
							>
								{ __( 'Connect', 'pinterest-for-woocommerce' ) }
							</Button>
						</FlexItem>
					</>
				) : (
					<Spinner className="connection-info__preloader" />
				) }
			</Flex>
			{ isConfirmationModalOpen && renderConfirmationModal() }
		</CardBody>
	);
};

export default AccountConnection;
