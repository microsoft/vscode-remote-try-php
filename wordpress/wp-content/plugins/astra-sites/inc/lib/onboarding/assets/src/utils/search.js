const isAnyWordMatching = ( search, content ) => {
	// Lowarize the search term and the content.
	search = search.toLocaleLowerCase();
	content = content.toLocaleLowerCase();

	// Remove all inner spaces and keep only one space between two words.
	search = search.replace( /\s\s+/g, ' ' ).trim();

	// Add the pipe operator to match any single world.
	search = search.replace( ' ', '|' );

	// Match any word in the content.
	if ( content.match( new RegExp( search, 'g' ) ) ) {
		return true;
	}

	return false;
};

export const getSitesBySearchTerm = function (
	searchTerm,
	type,
	category,
	pageBuilder,
	allSites,
	allCategories,
	allCategoriesAndTags
) {
	searchTerm = searchTerm.toLowerCase().trim();

	const result = {
		tags: [],
		sites: {},
		related: {},
		related_categories: [],
	};

	/**
	 * Get all page builder sites.
	 */
	const singlePageBuilderSites = Object.assign( {}, allSites );
	let sites = {};
	if ( pageBuilder ) {
		for ( const siteId in singlePageBuilderSites ) {
			if (
				singlePageBuilderSites[ siteId ][
					'astra-site-page-builder'
				] === pageBuilder
			) {
				sites[ siteId ] = singlePageBuilderSites[ siteId ];
			}
		}
	} else {
		sites = singlePageBuilderSites;
	}

	/**
	 * Filter sites by site type
	 */
	let newSites = {};
	if ( type ) {
		for ( const siteId in sites ) {
			if ( sites[ siteId ][ 'astra-sites-type' ] === type ) {
				newSites[ siteId ] = sites[ siteId ];
			}
		}

		sites = newSites;
	}

	/**
	 * Filter sites by site category
	 */
	newSites = {};
	if ( category ) {
		for ( const siteId in sites ) {
			if (
				Object.values( sites[ siteId ].categories ).includes( category )
			) {
				newSites[ siteId ] = sites[ siteId ];
			}
		}

		sites = newSites;
	}

	/**
	 * Find in sites.
	 *
	 * Add site in tags.
	 * Add site in sites list.
	 */
	for ( const siteId in sites ) {
		const site = sites[ siteId ];
		let siteExist = false;

		/**
		 * Sites
		 */
		if ( isAnyWordMatching( searchTerm, site.title ) ) {
			siteExist = true;

			/**
			 * Add site title in tag.
			 */
			if ( ! result.tags.includes( site.title ) ) {
				result.tags.push( site.title );
			}

			/**
			 * Add found sites.
			 */
			result.sites[ siteId ] = site;
		}

		/**
		 * Add sites if search term match in tags.
		 */
		if ( site[ 'astra-sites-tag' ] ) {
			for ( const tagId in site[ 'astra-sites-tag' ] ) {
				const tag = site[ 'astra-sites-tag' ][ tagId ].replace(
					'-',
					' '
				);
				if ( isAnyWordMatching( searchTerm, tag ) ) {
					siteExist = true;

					result.sites[ siteId ] = site;
				}
			}
		}

		/**
		 * Add related categories
		 */
		if ( siteExist ) {
			for ( const siteCatId in site.categories ) {
				if (
					! result.related_categories.includes(
						site.categories[ siteCatId ]
					)
				) {
					result.related_categories.push(
						site.categories[ siteCatId ]
					);
				}
			}
		}
	}

	/**
	 * Add additionals.
	 */

	/**
	 * Filter original tags.
	 */
	for ( const cat of allCategoriesAndTags ) {
		if ( cat.name.toLowerCase().includes( searchTerm ) ) {
			/**
			 * Add tag in tags list.
			 */
			result.tags.push( cat.name );

			/**
			 * Add parent tag sites into the related list.
			 */
			if ( allCategories.length ) {
				let parentCatId = cat.id.toString();
				if ( parentCatId.includes( '-' ) ) {
					parentCatId = parseInt( cat.id.split( '-' )[ 0 ] );
				}

				for ( const siteCat of allCategories ) {
					if ( parentCatId === siteCat.id ) {
						if (
							! result.related_categories.includes( siteCat.slug )
						) {
							result.related_categories.push( siteCat.slug );
						}
					}
				}
			}
		}
	}

	/**
	 * Related Sites.
	 */
	for ( const siteId in sites ) {
		const site = sites[ siteId ];
		for ( const siteCatId in site.categories ) {
			if (
				! result.sites[ siteId ] &&
				result.related_categories.includes(
					site.categories[ siteCatId ]
				)
			) {
				result.related[ siteId ] = site;
			}
		}
	}

	/**
	 * Limit tags.
	 */
	if ( result.tags ) {
		result.tags = result.tags.slice( 0, 10 );
	}

	return result;
};
