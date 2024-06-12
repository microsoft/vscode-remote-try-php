import React, { useRef, useEffect } from 'react';
import { Search } from '@brainstormforce/starter-templates-components';
import { useNavigate } from 'react-router-dom';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useStateValue } from '../../../store/store';
import './style.scss';
import { setURLParmsValue } from '../../../utils/url-params';
import { useFilteredSites } from '..';

const SiteSearch = ( { setSiteData } ) => {
	const [
		{
			siteSearchTerm,
			searchTerms,
			searchTermsWithCount,
			builder,
			siteType,
			stagingConnected,
		},
		dispatch,
	] = useStateValue();
	const allFilteredSites = useFilteredSites();
	const history = useNavigate();

	const collectTerms = ( count ) => {
		const term = siteSearchTerm.toLowerCase();
		const allTerms = searchTerms;
		const allTermsWithCount = searchTermsWithCount;
		// Skip blank words and words smaller than 3 characters.
		if ( '' === term || term.length < 3 ) {
			return;
		}

		if ( ! searchTerms.includes( term ) ) {
			allTerms.push( term );
			allTermsWithCount.push( {
				term,
				count,
			} );
			dispatch( {
				type: 'set',
				searchTerms: allTerms,
				searchTermsWithCount: allTermsWithCount,
			} );
		}
	};

	const ref = useRef();
	const parentRef = useRef();

	const handleScroll = ( event ) => {
		event.preventDefault();

		if ( ref && parentRef ) {
			const header = document.querySelector( '.site-list-header' );
			let topCross = 0;
			if ( header && header.clientHeight ) {
				topCross = header.clientHeight;
			}

			// Remove the search box height too.
			topCross = topCross - ref.current.clientHeight;

			// Check the search wrapper scrool top.
			const parentTop =
				parentRef.current.getBoundingClientRect().top || 0;
			if ( parentTop <= topCross ) {
				document.body.classList.add( 'st-search-box-fixed' );
			} else {
				document.body.classList.remove( 'st-search-box-fixed' );
			}
		}
	};

	useEffect( () => {
		document
			.querySelector( '.step-content' )
			?.addEventListener( 'scroll', handleScroll );
		return () =>
			document
				.querySelector( '.step-content' )
				?.removeEventListener( 'scroll', handleScroll );
	}, [] );

	const onSearchKeyUp = ( event ) => {
		event.preventDefault();
		const content = document.querySelector( '.st-templates-content' );
		const top = content
			? parseInt( content.getBoundingClientRect().top )
			: 0;
		if (
			top < 0 &&
			32 !== event.keyCode &&
			16 !== event.keyCode &&
			17 !== event.keyCode &&
			18 !== event.keyCode
		) {
			const header = document.querySelector( '.site-list-header' );
			const headerHeight = header ? parseInt( header.clientHeight ) : 0;
			document.querySelector( '.step-content' ).scrollTo( {
				behavior: 'smooth',
				left: 0,
				top: content.offsetTop - headerHeight - 20,
			} );
		}
	};
	return (
		<div className="st-search-box-wrap" ref={ parentRef }>
			<div className="st-search-filter st-search-box" ref={ ref }>
				<Search
					apiUrl={ `${ astraSitesVars.ApiDomain }wp-json/starter-templates/v2/sites-search/?search=${ siteSearchTerm }&page-builder=${ builder }&type=${ siteType }${ stagingConnected }` }
					beforeSearchResult={ () => {
						if ( ! siteSearchTerm ) {
							return;
						}
						setSiteData( {
							gridSkeleton: true,
						} );
					} }
					onSearchResult={ ( response ) => {
						if ( ! siteSearchTerm ) {
							setSiteData( {
								gridSkeleton: false,
							} );
							return;
						}
						const results = [];
						if ( response.success ) {
							if ( response.ids.length ) {
								for ( const id of response.ids ) {
									if ( allFilteredSites[ id ] ) {
										const selectedTemplate =
											allFilteredSites[ id ];
										if (
											selectedTemplate.related_ecommerce_template !==
												undefined &&
											selectedTemplate.related_ecommerce_template !==
												'' &&
											selectedTemplate.ecommerce_parent_template !==
												undefined &&
											selectedTemplate.ecommerce_parent_template !==
												''
										) {
											// If ecommerce_parent_template is not empty, skip adding the site to allSites.
											continue;
										}
										results[ id ] = allFilteredSites[ id ];
									}
								}
							}
						}

						collectTerms( Object.keys( results ).length );

						setSiteData( {
							sites: results,
							gridSkeleton: false,
						} );
					} }
					value={ decodeEntities( siteSearchTerm ) }
					placeholder={ __(
						'Search for Starter Templates',
						'astra-sites'
					) }
					onSearch={ ( event, newSearchTerm ) => {
						const newSiteData = {
							gridSkeleton: true,
						};

						if ( ! newSearchTerm ) {
							newSiteData.sites = allFilteredSites;
						}

						setSiteData( newSiteData );

						dispatch( {
							type: 'set',
							siteSearchTerm: newSearchTerm,
							onMyFavorite: false,
							siteBusinessType: '',
							selectedMegaMenu: '',
							siteType: '',
							siteOrder: 'popular',
						} );
						const urlParam = setURLParmsValue( 's', newSearchTerm );
						history( `?${ urlParam }` );
					} }
					onKeyUp={ onSearchKeyUp }
				/>
			</div>
		</div>
	);
};

export default SiteSearch;
