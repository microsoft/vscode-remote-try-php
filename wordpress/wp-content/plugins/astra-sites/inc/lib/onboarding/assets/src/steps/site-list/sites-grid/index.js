import React, { useState, useEffect } from 'react';
import { Grid } from '@brainstormforce/starter-templates-components';
import {
	getDemo,
	checkRequiredPlugins,
	checkFileSystemPermissions,
} from '../../import-site/import-utils';
import { useStateValue } from '../../../store/store';
import { getGridItem } from '../../../utils/functions';

const SiteGrid = ( { sites } ) => {
	const sitesData = sites ? sites : {};
	const storedState = useStateValue();
	const [ { favoriteSiteIDs, currentIndex }, dispatch ] = storedState;
	const [ columns, setColumns ] = useState( 4 );

	const allSites = [];

	if ( Object.keys( sitesData ).length ) {
		for ( const siteId in sitesData ) {
			const site = sitesData[ siteId ];
			if (
				site.related_ecommerce_template !== undefined &&
				site.related_ecommerce_template !== '' &&
				site.ecommerce_parent_template !== undefined &&
				site.ecommerce_parent_template !== ''
			) {
				// If ecommerce_parent_template is not empty, skip adding the site to allSites.
				continue;
			}
			const gridItem = getGridItem( site );
			allSites.push( gridItem );
		}
	}

	const quickToggleFavorites = ( siteId, favoriteStatus ) => {
		let favoriteIds = favoriteSiteIDs;
		if ( favoriteStatus && ! favoriteIds.includes( siteId ) ) {
			favoriteIds.push( siteId );
		} else {
			favoriteIds = favoriteSiteIDs.filter(
				( existingId ) => existingId !== siteId
			);
		}

		dispatch( {
			type: 'set',
			favoriteSiteIDs: favoriteIds,
		} );
	};

	const toggleFavorites = async ( event, item, favoriteStatus ) => {
		try {
			event.preventDefault();

			const siteId = `id-${ item.id }`;

			// Quick toggle the favorites.
			quickToggleFavorites( siteId, favoriteStatus );

			// Dispatch toggle favorite.
			const formData = new FormData();
			formData.append( 'action', 'astra-sites-favorite' );
			formData.append( 'is_favorite', favoriteStatus );
			formData.append( 'site_id', siteId );
			formData.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
			const resonse = await fetch( ajaxurl, {
				method: 'post',
				body: formData,
			} );
			const data = await resonse.json();

			// Toggle fail so unset favorite.
			if ( ! data.success ) {
				quickToggleFavorites( siteId, false );
			}
		} catch ( err ) {
			// Do nothing
		}
	};

	useEffect( () => {
		const handleResize = () => {
			// Update the number of columns based on the screen width
			if ( window.innerWidth <= 768 ) {
				setColumns( 2 ); // Adjust for smaller screens
			} else if ( window.innerWidth > 768 && window.innerWidth <= 1024 ) {
				setColumns( 3 );
			} else {
				setColumns( 4 ); // Default for larger screens
			}
		};

		// Listen for window resize events
		window.addEventListener( 'resize', handleResize );

		// Call handleResize initially to set the correct number of columns.
		handleResize();

		// Clean up the event listener on component unmount
		return () => window.removeEventListener( 'resize', handleResize );
	}, [] );

	return (
		<Grid
			column={ columns }
			options={ allSites }
			hasFavorite
			onFavoriteClick={ toggleFavorites }
			favoriteList={ favoriteSiteIDs }
			onClick={ async ( event, item ) => {
				event.stopPropagation();
				dispatch( {
					type: 'set',
					currentIndex: currentIndex + 1,
					selectedTemplateName: item.title,
					selectedTemplateID: item.id,
					selectedTemplateType: item[ 'astra-sites-type' ],
				} );
				await getDemo( item.id, storedState );
				await checkRequiredPlugins( storedState );
				checkFileSystemPermissions( storedState );
			} }
		/>
	);
};

export default SiteGrid;
