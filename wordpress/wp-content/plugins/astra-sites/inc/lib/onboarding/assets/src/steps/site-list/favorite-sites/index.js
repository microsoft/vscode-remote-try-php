/** External Dependencies */
import React, { useState, useEffect } from 'react';

/** Internal Dependencies */
import { useStateValue } from '../../../store/store';
import SiteGrid from '../sites-grid';
import NoFavoriteSites from '../no-favorite-sites';
import RelatedSites from '../related-sites';
import { useFilteredSites } from '../index';
import { getGridItem } from '../../../utils/functions';

const FavoriteSites = () => {
	const [ siteData, setSiteData ] = useState( {
		skeleton: true,
		allFavorites: [],
	} );
	const [ { favoriteSiteIDs, onMyFavorite } ] = useStateValue();
	const allFilteredSites = useFilteredSites();

	useEffect( () => {
		setSiteData( {
			...siteData,
			skeleton: true,
		} );

		const allFavorites = [];
		if ( onMyFavorite && Object.keys( allFilteredSites ).length > 0 ) {
			for ( const siteId in allFilteredSites ) {
				if (
					favoriteSiteIDs.length &&
					favoriteSiteIDs.includes( siteId )
				) {
					const gridItem = getGridItem( allFilteredSites[ siteId ] );
					allFavorites.push( gridItem );
				}
			}
		}

		setSiteData( {
			...siteData,
			allFavorites,
			skeleton: false,
		} );
	}, [ favoriteSiteIDs, onMyFavorite ] );

	return (
		<>
			{ siteData.skeleton ? (
				<div className="st-sites-grid st-sites-favorites-grid">
					<SiteGrid skeleton={ siteData.skeleton } />
				</div>
			) : (
				<>
					{ siteData.allFavorites.length ? (
						<div className="st-sites-grid">
							<SiteGrid sites={ siteData.allFavorites } />
						</div>
					) : (
						<>
							<NoFavoriteSites />
							<RelatedSites sites={ allFilteredSites } />
						</>
					) }
				</>
			) }
		</>
	);
};

export default FavoriteSites;
