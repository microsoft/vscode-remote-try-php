/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';

const ThirdPartyTagsNotice = () => {
	if ( ! wcSettings.pinterest_for_woocommerce.conflictingTagsWarning ) {
		return null;
	}

	const warningMessageContent = `<strong>${ __(
		'Potential conflicting plugins detected.',
		'pinterest-for-woocommerce'
	) }</strong> ${
		wcSettings.pinterest_for_woocommerce.conflictingTagsWarning
	}`;

	return (
		<Notice
			status="warning"
			isDismissible={ true }
			className="pinterest-for-woocommerce-healthcheck-notice"
		>
			<span
				dangerouslySetInnerHTML={ {
					__html: warningMessageContent,
				} }
			/>
		</Notice>
	);
};

export default ThirdPartyTagsNotice;
