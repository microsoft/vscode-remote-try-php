/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	Flex,
	Modal,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Ads Onboarding Modal.
 *
 * @param {Object} options
 * @param {Function} options.onCloseModal Action to call when the modal gets closed.
 * @param {Object} options.children Children of the component.
 *
 * @return {JSX.Element} rendered component
 */
const OnboardingModal = ( { onCloseModal, children } ) => {
	return (
		<Modal
			icon={
				<img
					src={
						wcSettings.pinterest_for_woocommerce.pluginUrl +
						'/assets/images/onboarding_success_modal_header.svg'
					}
					alt="Gift banner"
				/>
			}
			onRequestClose={ onCloseModal }
			className="pinterest-for-woocommerce-catalog-sync__onboarding-generic-modal"
		>
			<Text variant="title.small">
				{ __(
					'You have successfully set up your Pinterest integration.',
					'pinterest-for-woocommerce'
				) }
			</Text>
			<Text variant="body.large">
				{ __(
					'You have successfully set up your Pinterest integration! Your product catalog is being synced and reviewed. This could take up to 2 days.',
					'pinterest-for-woocommerce'
				) }
			</Text>
			{ children }
			<Flex direction="row" justify="flex-end">
				<Button isPrimary onClick={ onCloseModal }>
					{ __( 'View catalog', 'pinterest-for-woocommerce' ) }
				</Button>
			</Flex>
		</Modal>
	);
};

export default OnboardingModal;
