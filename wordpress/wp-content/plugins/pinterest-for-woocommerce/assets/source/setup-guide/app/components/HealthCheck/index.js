/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import { useEffect, useState, useCallback } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
	Notice,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.scss';
import { useSettingsSelect, useCreateNotice } from '../../helpers/effects';
import { DISAPPROVAL_COPY_STATES } from '../../constants';

/**
 * Render the list of the disapproval reasons.
 *
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'merchant-guidelines', context: 'merchant-disapproval-reasons' }`
 *
 * @param {Object} props React props
 * @param {Array} props.reasons
 * @return { JSX.Element } The rendered element
 */
const FormattedReasons = ( { reasons } ) => {
	if ( reasons === undefined ) {
		return null;
	}

	const formattedReasons = reasons.map(
		( reason ) =>
			undefined !== DISAPPROVAL_COPY_STATES[ reason ] && (
				<li key={ reason }>{ DISAPPROVAL_COPY_STATES[ reason ] }</li>
			)
	);

	if ( ! formattedReasons ) {
		return null;
	}

	return <ul className="ul-square">{ formattedReasons }</ul>;
};

const HealthCheck = () => {
	const createNotice = useCreateNotice();
	const appSettings = useSettingsSelect();
	const [ healthStatus, setHealthStatus ] = useState();

	const checkHealth = useCallback( async () => {
		try {
			const results = await apiFetch( {
				path:
					wcSettings.pinterest_for_woocommerce.apiRoute + '/health/',
				method: 'GET',
			} );

			setHealthStatus( results );
		} catch ( error ) {
			createNotice(
				'error',
				error.message ||
					__(
						'Couldn’t retrieve the health status of your account.',
						'pinterest-for-woocommerce'
					)
			);
		}
	}, [ createNotice ] );

	useEffect( () => {
		checkHealth();
	}, [ checkHealth ] );

	if ( healthStatus === undefined || healthStatus.status === 'approved' ) {
		return null;
	}

	const notices = {
		pending_initial_configuration: {
			status: 'warning',
			message: __(
				'The feed is being configured. Depending on the number of products this may take a while as the feed needs to be fully generated before it is been sent to Pinterest for registration. You can check the status of the generation process in the Catalog tab.',
				'pinterest-for-woocommerce'
			),
			dismissible: false,
		},
		pending: {
			status: 'warning',
			message: __(
				'Please hold on tight as your account is pending approval from Pinterest. This may take up to 5 business days.',
				'pinterest-for-woocommerce'
			),
			dismissible: false,
		},
		declined: {
			status: 'error',
			message: __(
				'Your merchant account is disapproved.',
				'pinterest-for-woocommerce'
			),
			body: __(
				'If you have a valid reason for appealing a merchant review decision (such as having corrected the violations that resulted in the disapproval), you can submit an appeal.',
				'pinterest-for-woocommerce'
			),
			reasons: healthStatus.reasons,
			dismissible: false,
			actions: [
				{
					label: __(
						'Submit an appeal',
						'pinterest-for-woocommerce'
					),
					url: addQueryArgs(
						wcSettings.pinterest_for_woocommerce.pinterestLinks
							.appealDeclinedMerchant,
						{ advertiserId: appSettings.tracking_advertiser }
					),
				},
			],
		},
		appeal_pending: {
			status: 'warning',
			dismissible: false,
			message: __(
				'Your merchant account is disapproved.',
				'pinterest-for-woocommerce'
			),
			body: __(
				'Please hold on tight as there is an Appeal pending for your Pinterest account.',
				'pinterest-for-woocommerce'
			),
		},
		merchant_connected_diff_platform: {
			status: 'error',
			dismissible: false,
			message: __(
				'Unable to upload catalog.',
				'pinterest-for-woocommerce'
			),
			body: __(
				'It looks like your Pinterest business account is connected to another e-commerce platform. Only one platform can be linked to a business account. To upload your catalog, disconnect your business account from the other platform and try again.',
				'pinterest-for-woocommerce'
			),
		},
		merchant_locale_not_valid: {
			status: 'error',
			dismissible: false,
			message: __(
				'Unable to register feed.',
				'pinterest-for-woocommerce'
			),
			body: __(
				'It looks like your WordPress language settings are not supported by Pinterest.',
				'pinterest-for-woocommerce'
			),
		},
		error: {
			status: 'error',
			dismissible: false,
		},
	};

	const notice = notices[ healthStatus.status ] || notices.error;

	if ( notice.status === 'error' && ! notice.message ) {
		notice.message =
			healthStatus.message ||
			__(
				'Could not fetch account status.',
				'pinterest-for-woocommerce'
			);
	}

	return (
		<Notice
			status={ notice.status }
			isDismissible={ notice.dismissible }
			actions={ notice.actions || [] }
			className="pinterest-for-woocommerce-healthcheck-notice"
		>
			<Text variant="titleSmall">{ notice.message }</Text>
			<FormattedReasons reasons={ notice.reasons } />
			{ notice.body }
		</Notice>
	);
};

export default HealthCheck;
