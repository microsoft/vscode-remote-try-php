/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/components';
import { TableCard } from '@woocommerce/components';

const SyncIssuesTable = ( {
	issues,
	totalRows,
	query,
	isRequesting,
	onQueryChange,
} ) => {
	const defaultHeaderAttributes = {
		isLeftAligned: true,
		isSortable: false,
	};

	const headers = [
		{
			key: 'type',
			label: __( 'Type', 'pinterest-for-woocommerce' ),
			...defaultHeaderAttributes,
		},
		{
			key: 'affected-product',
			label: __( 'Affected Product', 'pinterest-for-woocommerce' ),
			...defaultHeaderAttributes,
		},
		{
			key: 'issue',
			label: __( 'Issue', 'pinterest-for-woocommerce' ),
			...defaultHeaderAttributes,
		},
		{ key: 'edit', ...defaultHeaderAttributes },
	];

	const getRows = ( data ) => {
		const statuses = {
			success: 'success',
			warning: 'warning',
			error: 'error',
		};

		const icons = {
			success: 'yes-alt',
			warning: 'warning',
			error: 'warning',
		};

		return data.map( ( row ) => {
			return [
				{
					display: (
						<Icon
							icon={ icons[ row.status ] }
							className={ `${ statuses[ row.status ] }-text` }
						/>
					),
				},
				{ display: row.product_name },
				{ display: row.issue_description },
				{
					display: (
						<a
							href={ row.product_edit_link }
							target="_blank"
							rel="noreferrer"
						>
							{ __( 'Edit', 'pinterest-for-woocommerce' ) }
						</a>
					),
				},
			];
		} );
	};

	return (
		<TableCard
			className="pinterest-for-woocommerce-catalog-sync__issues"
			title={ __( 'Issues', 'pinterest-for-woocommerce' ) }
			rows={ issues && getRows( issues ) }
			headers={ headers }
			showMenu={ false }
			query={ query }
			rowsPerPage={ query.per_page }
			totalRows={ totalRows }
			isLoading={ ! issues || isRequesting }
			onQueryChange={ onQueryChange }
		/>
	);
};

export default SyncIssuesTable;
