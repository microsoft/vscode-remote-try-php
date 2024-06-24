/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { getHistory, getQuery, onQueryChange } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import { REPORTS_STORE_NAME } from '../data';
import SyncIssuesTable from './SyncIssuesTable';

const SyncIssues = () => {
	const [ query, setQuery ] = useState( getQuery() );
	const feedIssues = useSelect( ( select ) =>
		select( REPORTS_STORE_NAME ).getFeedIssues( query )
	);
	const isRequesting = useSelect( ( select ) =>
		select( REPORTS_STORE_NAME ).isRequesting()
	);

	if ( ! feedIssues?.lines?.length ) {
		return null;
	}

	getHistory().listen( () => {
		setQuery( getQuery() );
	} );

	if ( ! query.paged ) {
		query.paged = 1;
	}

	if ( ! query.per_page ) {
		query.per_page = 25;
	}

	return (
		<SyncIssuesTable
			issues={ feedIssues?.lines }
			query={ query }
			totalRows={ feedIssues?.total_rows ?? 0 }
			isRequesting={ isRequesting }
			onQueryChange={ onQueryChange }
		/>
	);
};

export default SyncIssues;
