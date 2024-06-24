import { unionBy } from 'lodash';

export const filterPages = ( title /* , color, category, tag */ ) => {
	// All Pages.
	let items = [];
	for ( const index in astraSitesVars.allSites ) {
		const singleSite = astraSitesVars.allSites[ index ];
		const pages = singleSite.pages || {};
		if ( Object.values( pages ).length ) {
			for ( const pageID in pages ) {
				// pages[pageID].ID = pageID;
				pages[ pageID ][ 'site-ID' ] = singleSite.ID;
				pages[ pageID ][ 'site-title' ] = singleSite.title;
				items.push( pages[ pageID ] );
			}
		}
	}

	// Filter by title.
	let filterByTitle = [];
	if ( title ) {
		filterByTitle = items.filter( ( item ) =>
			item.title.toLowerCase().includes( title.toLowerCase() )
		);
	}

	// Filter by site title.
	let filterBySiteTitle = [];
	if ( title ) {
		filterBySiteTitle = items.filter( ( item ) =>
			item[ 'site-title' ].toLowerCase().includes( title.toLowerCase() )
		);
	}

	// Filter by tags.
	let filterByTag = [];
	if ( title ) {
		filterByTag = items.filter( ( item ) => {
			if ( 'tag' in item ) {
				const tags = Object.values( item.tag ) || [];
				// Have any tags?
				if ( tags.length ) {
					for ( const tagIndex in tags ) {
						// Found any tag then return true.
						if (
							tags[ tagIndex ]
								.toLowerCase()
								.includes( title.toLowerCase() )
						) {
							return true;
						}
					}
				}

				// Not have found any matching tag,
				// So return false.
				return false;
			}

			return true;
		} );
	}

	// CASE: Combine title and tag search results.
	if ( title ) {
		items = unionBy( filterByTitle, filterByTag, filterBySiteTitle, 'ID' );
	}

	return items;
};
