/** External Dependencies */
import React from 'react';
import { __ } from '@wordpress/i18n';

/** Internal Dependencies */
import './style.scss';

const NoFavoriteSites = () => {
	return (
		<div className="st-no-favorites">
			<h3>
				{ __(
					'No favorites added. Press the heart icon to add templates as favorites.',
					'astra-sites'
				) }
			</h3>
		</div>
	);
};

export default NoFavoriteSites;
