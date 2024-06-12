let loadStatus = true;

window.UAGBPostCarousel = {
	_setHeight( scope ) {
		if ( scope.length > 0 ) {
			const postWrapper = scope[ 0 ].querySelectorAll( '.slick-slide' ),
				postActive = scope[ 0 ].querySelectorAll( '.slick-slide.slick-active' );
			let maxHeight = -1,
				wrapperHeight = -1,
				postActiveHeight = -1;
			Object.keys( postActive ).forEach( ( key ) => {
				const thisHeight = postActive[ key ].offsetHeight,
					blogPost = postActive[ key ].querySelector( '.uagb-post__inner-wrap' ),
					blogPostHeight = blogPost?.offsetHeight;

				if ( maxHeight < blogPostHeight ) {
					maxHeight = blogPostHeight;
					postActiveHeight = maxHeight + 15;
				}

				if ( wrapperHeight < thisHeight ) {
					wrapperHeight = thisHeight;
				}
			} );

			Object.keys( postActive ).forEach( ( key ) => {
				const selector = postActive[ key ].querySelector( '.uagb-post__inner-wrap' );
				if ( selector ) {
					selector.style.height = maxHeight + 'px';
				}
			} );

			let selector = scope[ 0 ].querySelector( '.slick-list' );
			if ( selector ) {
				selector.style.height = postActiveHeight + 'px';
			}
			maxHeight = -1;
			wrapperHeight = -1;
			Object.keys( postWrapper ).forEach( ( key ) => {
				const $this = postWrapper[ key ];
				if ( $this.classList.contains( 'slick-active' ) ) {
					return true;
				}

				selector = $this.querySelector( '.uagb-post__inner-wrap' );
				const blogPostHeight = selector?.offsetHeight;
				if ( blogPostHeight ) {
					selector.style.height = blogPostHeight + 'px';
				}
			} );
		}
	},
	_unSetHeight( scope ) {
		if ( scope.length > 0 ) {
			const postWrapper = scope[ 0 ].querySelectorAll( '.slick-slide' ),
				postActive = scope[ 0 ].querySelectorAll( '.slick-slide.slick-active' );

			Object.keys( postActive ).forEach( ( key ) => {
				const selector = postActive[ key ].querySelector( '.uagb-post__inner-wrap' );
				selector.style.height = 'auto';
			} );

			Object.keys( postActive ).forEach( ( key ) => {
				const $this = postWrapper[ key ];
				if ( $this.classList.contains( 'slick-active' ) ) {
					return true;
				}
				const selector = $this.querySelector( '.uagb-post__inner-wrap' );
				selector.style.height = 'auto';
			} );
		}
	},
};

window.UAGBPostMasonry = {
	_init( $attr, $selector ) {
		let count = 2;
		const windowHeight50 = window.innerHeight / 1.25;
		let $scope = document.querySelector( $selector );
		const loader = $scope?.querySelectorAll( '.uagb-post-inf-loader' );
		if ( 'none' !== $attr.paginationType && 'scroll' === $attr.paginationEventType ) {
			window.addEventListener( 'scroll', function () {
				let postItems = $scope.querySelector( '.uagb-post__items' );

				if ( ! postItems ) {
					postItems = $scope;
				}

				const boundingClientRect = postItems.lastElementChild.getBoundingClientRect();

				const offsetTop = boundingClientRect.top + window.scrollY;

				if ( window.pageYOffset + windowHeight50 >= offsetTop ) {
					const $args = {
						page_number: count,
					};
					const total = $scope.getAttribute( 'data-total' );
					if ( true === loadStatus ) {
						if ( count <= total ) {
							if ( loader.length > 0 ) {
								loader[ 0 ].style.display = 'none';
							}
							window.UAGBPostMasonry._callAjax( $scope, $args, $attr, loader, false, count );
							count++;
							loadStatus = false;
						}
					}
				}
			} );
		}

		if ( 'button' === $attr.paginationEventType ) {
			if ( $scope?.querySelector( '.uagb-post-pagination-button' ) ) {
				$scope.style.marginBottom = '40px';

				$scope.querySelector( '.uagb-post-pagination-button' ).onclick = function () {
					$scope = this.closest( '.uagb-post-grid' );
					const total = $scope.getAttribute( 'data-total' );
					const $args = {
						total,
						page_number: count,
					};
					$scope.querySelector( '.uagb-post__load-more-wrap' ).style.display = 'none';
					if ( true === loadStatus ) {
						if ( count <= total ) {
							if ( loader.length > 0 ) {
								loader[ 0 ].style.display = 'none';
							}
							$scope.querySelector( '.uagb-post__load-more-wrap' ).style.display = 'block';
							window.UAGBPostMasonry._callAjax( $scope, $args, $attr, loader, true, count );
							count++;
							loadStatus = false;
						}
					}
				};
			}
		}
	},
	createElementFromHTML( htmlString ) {
		const HTMLElement = document.createElement( 'div' );
		HTMLElement.innerHTML = htmlString.trim();

		// Change this to div.childNodes to support multiple top-level nodes
		return HTMLElement;
	},
	_callAjax( $scope, $obj, $attr, loader, append = false, count ) {
		const PostData = new FormData(); // eslint-disable-line no-undef

		PostData.append( 'action', 'uagb_get_posts' );
		PostData.append( 'nonce', uagb_data.uagb_masonry_ajax_nonce );
		PostData.append( 'page_number', $obj.page_number );
		PostData.append( 'attr', JSON.stringify( $attr ) );

		// eslint-disable-next-line no-undef
		fetch( uagb_data.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			body: PostData,
		} )
			.then( ( resp ) => resp.json() )
			.then( function ( data ) {
				let element = $scope.querySelector( '.is-masonry' );

				if ( ! element ) {
					element = $scope;
				}

				setTimeout( function () {
					// eslint-disable-next-line no-undef
					const isotope = new Isotope( element, {
						itemSelector: 'article',
					} );

					isotope.insert( window.UAGBPostMasonry.createElementFromHTML( data.data ) );
					loadStatus = true;

					if ( loader.length > 0 ) {
						loader[ 0 ].style.display = 'none';
					}

					if ( true === append ) {
						$scope.querySelector( '.uagb-post__load-more-wrap' ).style.display = 'block';
					}

					if ( count === parseInt( $obj.total ) ) {
						$scope.querySelector( '.uagb-post__load-more-wrap' ).style.display = 'none';
					}
					// This CSS is for Post BG Image Spacing
					const articles = document.querySelectorAll(
						'.uagb-post__image-position-background .uagb-post__inner-wrap'
					);

					for ( const article of articles ) {
						const articleWidth = article.offsetWidth;
						const rowGap = $attr.rowGap;
						const imageWidth = 100 - ( rowGap / articleWidth ) * 100;
						const image = article.getElementsByClassName( 'uagb-post__image' );
						if ( image[ 0 ] ) {
							image[ 0 ].style.width = imageWidth + '%';
							image[ 0 ].style.marginLeft = rowGap / 2 + 'px';
						}
					}
				}, 500 );
			} )
			.catch( function ( error ) {
				console.log( JSON.stringify( error ) ); // eslint-disable-line no-console
			} );
	},
};
window.UAGBPostGrid = {
	_callAjax( $attr, $page_number, block_id ) {

		// Create new FormData object with necessary data to send in AJAX call.
		const PostData = new FormData();
		PostData.append( 'action', 'uagb_post_pagination_grid' );
		PostData.append( 'nonce', uagb_data.uagb_grid_ajax_nonce );
		PostData.append( 'page_number', $page_number );
		PostData.append( 'attr', JSON.stringify( $attr ) );

		// Send AJAX call with PostData object.
		fetch( uagb_data.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			body: PostData,
		  } )
		  .then( ( resp ) => resp.json() )
		  .then( function( data ) { 

			// Get the relevant DOM elements to replace.
			const grid_element = document.querySelector( '.uagb-block-'+ block_id );
			if( ! grid_element ) {	
				return;
			}

			// Remove the old elements and replace them with the updated markup received from the AJAX response.
			const html = data.data.replace( /\n|\t/g, '' );
			grid_element.outerHTML = html;

			// Get the new block ID to use for future pagination requests.
			const new_blockId = html.match( /uagb-block-([\w-]+)/ )?.[1] || '';
			addClickListeners( new_blockId );
			
		} );

		function addClickListeners( new_blockId ) {

			// Add click event listener to each pagination link in the updated markup.
			const elements = document.querySelectorAll( `.uagb-post-grid.uagb-block-${new_blockId} .uagb-post-pagination-wrap a` );
			elements.forEach( element => {
				element.addEventListener( 'click', event => {

					// Prevent default link behavior and extract the new page number to send in the next AJAX call
					event.preventDefault();
					const link = event.target.getAttribute( 'href' ).match( /admin-ajax.*/ )?.[0] || '';
					const pageNumber = link.match( /\d+/ )?.[0] || 1;
					
					// Call _callAjax again with updated page number and block ID
					window.UAGBPostGrid._callAjax( $attr, parseInt( pageNumber ), new_blockId );

				} );
			} );

		}
	}
};

// Set Carousel Height for Customiser.
// eslint-disable-next-line no-unused-vars
function uagb_carousel_height( id ) {
	const wrap = document.querySelector( '#wpwrap .is-carousel.uagb-block-' + id );
	if ( wrap ) {
		window.UAGBPostCarousel._setHeight( wrap );
	}
}

// Unset Carousel Height for Customiser.
// eslint-disable-next-line no-unused-vars
function uagb_carousel_unset_height( id ) {
	const wrap = document.querySelector( '#wpwrap .is-carousel.uagb-block-' + id );
	if ( wrap ) {
		window.UAGBPostCarousel._unSetHeight( wrap );
	}
}
