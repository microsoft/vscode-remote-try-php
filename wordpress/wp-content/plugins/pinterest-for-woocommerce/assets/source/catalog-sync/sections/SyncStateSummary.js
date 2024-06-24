/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	SummaryList,
	SummaryNumber,
	SummaryListPlaceholder,
} from '@woocommerce/components';

const SyncStateSummary = ( { overview } ) => {
	const getItems = ( data ) => {
		const defaultValue = __( 'N/A', 'pinterest-for-woocommerce' );

		return [
			<SummaryNumber
				key="active"
				value={ data?.total ?? defaultValue }
				label={ __( 'Active', 'pinterest-for-woocommerce' ) }
			/>,
			<SummaryNumber
				key="not-synced"
				value={ data?.not_synced ?? defaultValue }
				label={ __( 'Not synced', 'pinterest-for-woocommerce' ) }
			/>,
			<SummaryNumber
				key="with-warnings"
				value={ data?.warnings ?? defaultValue }
				label={ __( 'Warnings', 'pinterest-for-woocommerce' ) }
			/>,
			<SummaryNumber
				key="with-errors"
				value={ data?.errors ?? defaultValue }
				label={ __( 'Errors', 'pinterest-for-woocommerce' ) }
			/>,
		];
	};

	return overview ? (
		<SummaryList>{ () => getItems( overview ) }</SummaryList>
	) : (
		<SummaryListPlaceholder numberOfItems={ 4 } />
	);
};

export default SyncStateSummary;
