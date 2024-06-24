/**
 * External dependencies
 */
import classnames from 'classnames';
import { Icon } from '@wordpress/components';
import { Spinner } from '@woocommerce/components';

/**
 * Internal dependencies
 */
import { LABEL_STATUS as STATUS } from '../../constants';
import './style.scss';

const compClassName = 'pfw-status-label';

const statusStyleName = {
	[ STATUS.PENDING ]: `${ compClassName }--status-pending`,
	[ STATUS.SUCCESS ]: `${ compClassName }--status-success`,
};

function getStatusPrefix( status ) {
	switch ( status ) {
		case STATUS.PENDING:
			return <Spinner />;
		case STATUS.SUCCESS:
			return <Icon icon="yes-alt" />;
	}
}

/**
 * Renders a text with a prefix graphic marking.
 *
 * @param {Object} props React props.
 * @param {import('../../constants').LABEL_STATUS} props.status Label status.
 * @param {string} props.text Label text.
 */
export default function StatusLabel( { status, text } ) {
	const className = classnames( compClassName, statusStyleName[ status ] );
	return (
		<div className={ className }>
			{ getStatusPrefix( status ) }
			{ text }
		</div>
	);
}
