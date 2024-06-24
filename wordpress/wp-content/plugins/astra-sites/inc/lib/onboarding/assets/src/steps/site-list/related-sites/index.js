// External Dependencies.
import React from 'react';
import { __ } from '@wordpress/i18n';
// Internal Dependencies.
import SiteGrid from '../sites-grid';
import { useStateValue } from '../../../store/store';
import { useFilteredSites } from '..';

const RelatedSites = ( { sites } ) => {
	const [ { siteSearchTerm } ] = useStateValue();
	const allFilteredSites = useFilteredSites();

	let defaultSites = sites;

	if ( siteSearchTerm ) {
		const relatedSites = {
			multipurpose: {},
			top20: {},
		};

		for ( const siteId in allFilteredSites ) {
			const site = allFilteredSites[ siteId ];
			if ( Object.values( site.categories ).includes( 'multipurpose' ) ) {
				relatedSites.multipurpose[ siteId ] = site;
			} else if ( Object.keys( relatedSites.top20 ).length <= 20 ) {
				relatedSites.top20[ siteId ] = site;
			}
		}

		defaultSites = Object.assign(
			relatedSites.multipurpose,
			relatedSites.top20
		);
	}

	return (
		<>
			<div className="st-sites-grid st-related-sites-grid">
				<div className="st-sites-found-message">
					{ __( 'Other suggested Starter Templates', 'astra-sites' ) }
				</div>
				<SiteGrid sites={ defaultSites } />
			</div>
		</>
	);
};

export default RelatedSites;
