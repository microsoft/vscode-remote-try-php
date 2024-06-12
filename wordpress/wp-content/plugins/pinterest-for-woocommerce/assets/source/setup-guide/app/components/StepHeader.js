/**
 * External dependencies
 */
import { __experimentalText as Text } from '@wordpress/components'; // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core

const StepHeader = ( { title, subtitle, description } ) => {
	return (
		<div className="woocommerce-setup-guide__step-header">
			{ subtitle && (
				<div className="woocommerce-setup-guide__step-header__subtitle">
					<Text variant="subtitle.small">{ subtitle }</Text>
				</div>
			) }

			{ title && (
				<div className="woocommerce-setup-guide__step-header__title">
					<Text variant="title.large">{ title }</Text>
				</div>
			) }

			{ description && (
				<div className="woocommerce-setup-guide__step-header__description">
					<Text variant="body">{ description }</Text>
				</div>
			) }
		</div>
	);
};

export default StepHeader;
