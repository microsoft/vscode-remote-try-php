import React from 'react';
import { useNavigate } from 'react-router-dom';
import { CategoryList } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import { setURLParmsValue } from '../../../utils/url-params';
import { useStateValue } from '../../../store/store';

const SiteCategory = () => {
	const [ { siteCategory }, dispatch ] = useStateValue();

	const getCategoryLimit = () => {
		if ( window.outerWidth >= 1116 ) {
			return 9;
		} else if ( 1116 > window.outerWidth && 950 <= window.outerWidth ) {
			return 6;
		} else if ( window.outerWidth < 950 && window.outerWidth > 751 ) {
			return 5;
		} else if ( window.outerWidth < 750 && window.outerWidth > 601 ) {
			return 4;
		} else if ( window.outerWidth < 600 && window.outerWidth > 451 ) {
			return 3;
		}
		return 2;
	};

	const MAX_LIMIT = getCategoryLimit();

	const allCategories = [
		{
			id: '',
			name: __( 'All', 'astra-sites' ),
			slug: '',
		},
		{
			id: '1',
			name: __( 'Blog', 'astra-sites' ),
			slug: 'Blog',
		},
		{
			id: '2',
			name: __( 'eCommerce', 'astra-sites' ),
			slug: 'eCommerce',
		},
		{
			id: '3',
			name: __( 'eLearning', 'astra-sites' ),
			slug: 'eLearning',
		},
		{
			id: '4',
			name: __( 'Restaurant', 'astra-sites' ),
			slug: 'Restaurant',
		},
		{
			id: '5',
			name: __( 'Agency', 'astra-sites' ),
			slug: 'Agency',
		},
		{
			id: '6',
			name: __( 'Local Business', 'astra-sites' ),
			slug: 'Local Business',
		},
		{
			id: '7',
			name: __( 'Professional', 'astra-sites' ),
			slug: 'Professional',
		},
	];
	const history = useNavigate();
	return (
		<div className="st-category-filter">
			<CategoryList
				limit={ MAX_LIMIT }
				value={ siteCategory.id }
				options={ allCategories }
				onClick={ ( event, category ) => {
					dispatch( {
						type: 'set',
						siteCategory: category,
						siteSearchTerm:
							category.name !== 'All' ? category.name : '',
						onMyFavorite: false,
					} );
					const urlParam = setURLParmsValue(
						's',
						category.name !== 'All' ? category.name : ''
					);
					history( `?${ urlParam }` );
					document.querySelector( '.stc-search-input' ).focus();
				} }
			/>
		</div>
	);
};

export default SiteCategory;
