/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, __experimentalText as Text } from '@wordpress/components'; // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core

/**
 * Step overview.
 *
 * Used to provide a description aside of a card in setup step.
 *
 * @param {Object} props React props.
 * @param {string} [props.title] Step's title.
 * @param {string} [props.description] Detailed description.
 * @param {Object} [props.readMore] Properties of the "Read more" {@link Button}, if it's to be displayed. Leave `undefined` or falsy, to do not render any button.
 * @return {JSX.Element} Rendered element.
 */
const StepOverview = ( { title, description, readMore } ) => {
	return (
		<div className="woocommerce-setup-guide__step-overview">
			{ title && (
				<div className="woocommerce-setup-guide__step-overview__title">
					<Text variant="subtitle">{ title }</Text>
				</div>
			) }

			{ description && (
				<div className="woocommerce-setup-guide__step-overview__description">
					<Text variant="body">{ description }</Text>
				</div>
			) }

			{ readMore && (
				<div className="woocommerce-setup-guide__step-overview__link">
					<Button isLink target="_blank" { ...readMore }>
						{ __( 'Read more' ) }
					</Button>
				</div>
			) }
		</div>
	);
};

export default StepOverview;
