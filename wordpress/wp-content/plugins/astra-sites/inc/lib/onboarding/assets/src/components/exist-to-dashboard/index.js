// External dependencies.
import React from 'react';
import Tooltip from '../../steps/onboarding-ai/components/tooltip';
import { __ } from '@wordpress/i18n';

// Internal dependencies.
import ICONS from '../../../icons';
import './style.scss';
const { adminUrl } = starterTemplates;

const ExitToDashboard = () => {
	return (
		<a className="st-exit-to-dashboard" href={ adminUrl }>
			<Tooltip content={ __( 'Exit to Dashboard', 'astra-sites' ) }>
				{ ICONS.remove }
			</Tooltip>
		</a>
	);
};

export default ExitToDashboard;
