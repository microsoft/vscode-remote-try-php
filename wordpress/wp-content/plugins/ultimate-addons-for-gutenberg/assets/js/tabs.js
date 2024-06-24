UAGBTabs = {
	init( $selector ) {
		const tabsWrap = document.querySelectorAll( $selector );
		if ( ! tabsWrap ) {
			return;
		}

		for ( let i = 0; i < tabsWrap.length; i++ ) {
			UAGBTabs.addEvents( tabsWrap[ i ], $selector );
		}
	},
	addEvents( tabsWrap, $selector ) {
		// Tabs wrap has two child elements, one is tabs list (.uagb-tabs__panel) and another is tabs body (.uagb-tabs__body-wrap).
		const tabsWrapChildren = tabsWrap.children;

		// Verify if tabsWrapChildren has two child elements.
		if ( 2 !== tabsWrapChildren.length ) {
			return;
		}

		const tabActive = tabsWrap.getAttribute( 'data-tab-active' );

		// Select tabs list (.uagb-tabs__panel) from tabsWrapChildren.
		const tabLi = tabsWrapChildren[0].querySelectorAll( 'li.uagb-tab' );

		// Select tabs body (.uagb-tabs__body-wrap) from tabsWrapChildren and children will be tab body container (.uagb-tabs__body-container).
		const tabBody = tabsWrapChildren[1].children;

		// Set initial active class to Tabs body.
		tabBody[ tabActive ].classList.add( 'uagb-tabs-body__active' );

		// Set initial active class to Tabs li.
		tabLi[ tabActive ].classList.add( 'uagb-tabs__active' );

		for ( let i = 0; i < tabLi.length; i++ ) {
			const tabsAnchor = tabLi[ i ].getElementsByTagName( 'a' )[ 0 ];

			// Set initial li ids.
			tabLi[ i ].setAttribute( 'id', 'uagb-tabs__tab' + i );

			// Set initial aria attributes true for anchor tags.
			tabsAnchor.setAttribute( 'aria-selected', true );

			if ( ! tabLi[ i ].classList.contains( 'uagb-tabs__active' ) ) {
				// Set aria attributes for anchor tags as false where needed.
				tabsAnchor.setAttribute( 'aria-selected', false );
			}

			// Set initial data attribute for anchor tags.
			tabsAnchor.setAttribute( 'data-tab', i );

			tabsAnchor.mainWrapClass = $selector;
			// Add Click event listener
			tabsAnchor.addEventListener( 'click', function ( e ) {
				UAGBTabs.tabClickEvent( e, this, this.parentElement );
			} );
		}
	},
	tabClickEvent( e, tabName, selectedLi ) {
		e.preventDefault();

		const tabId = tabName.getAttribute( 'data-tab' );
		const tabPanel = selectedLi.closest( '.uagb-tabs__panel' );

		const tabContainer = tabName.closest( '.uagb-tabs__wrap' );

		const tabBodyWrap = tabContainer.querySelector( '.uagb-tabs__body-wrap' );
		
		const tabBodyChildren = tabBodyWrap.children;
		const tabSelectedBody = UAGBTabs.getChildrenWithClass( tabBodyChildren, 'uagb-inner-tab-' + tabId );

		const allLi = tabPanel.querySelectorAll( 'a.uagb-tabs-list' );

		// Remove old li active class.
		tabPanel.querySelector( '.uagb-tabs__active' )?.classList.remove( 'uagb-tabs__active' );

		//Remove old tab body active class.
		UAGBTabs.getChildrenWithClass( tabBodyChildren, 'uagb-tabs-body__active' )?.classList.remove( 'uagb-tabs-body__active' );

		// Set aria-selected attribute as false for old active tab.
		for ( let i = 0; i < allLi.length; i++ ) {
			allLi[ i ].setAttribute( 'aria-selected', false );
		}

		// Set selected li active class.
		selectedLi.classList.add( 'uagb-tabs__active' );

		// Set aria-selected attribute as true for new active tab.
		tabName.setAttribute( 'aria-selected', true );

		// Set selected tab body active class.
		tabSelectedBody?.classList.add( 'uagb-tabs-body__active' );

		// Set aria-hidden attribute false for selected tab body.
		tabSelectedBody?.setAttribute( 'aria-hidden', false );

		// Set aria-hidden attribute true for all unselected tab body.
		for ( let i = 0; i < tabBodyChildren.length; i++ ) {
			// If tabBodyChildren[i] has .uagb-inner-tab-' + tabId + ' then continue.
			if ( tabBodyChildren[ i ].classList.contains( 'uagb-inner-tab-' + tabId ) ) {
				continue;
			}

			tabBodyChildren[ i ].setAttribute( 'aria-hidden', true );
		}
	},
	anchorTabId( $selector ) {
		const tabsHash = window.location.hash;

		if ( '' !== tabsHash && /^#uagb-tabs__tab\d$/.test( tabsHash ) ) {
			const mainWrapClass = $selector;
			const tabId = escape( tabsHash.substring( 1 ) );
			const selectedLi = document.querySelector( '#' + tabId );
			const topPos = selectedLi.getBoundingClientRect().top + window.pageYOffset;
			window.scrollTo( {
				top: topPos,
				behavior: 'smooth',
			} );
			const tabNum = selectedLi.querySelector( 'a.uagb-tabs-list' ).getAttribute( 'data-tab' );
			const listPanel = selectedLi.closest( '.uagb-tabs__panel' );
			const tabSelectedBody = document.querySelector(
				mainWrapClass + ' > .uagb-tabs__body-wrap > .uagb-inner-tab-' + tabNum
			);
			const tabUnselectedBody = document.querySelectorAll(
				mainWrapClass +
					' > .uagb-tabs__body-wrap > .uagb-tabs__body-container:not(.uagb-inner-tab-' +
					tabNum +
					')'
			);
			const allLi = selectedLi.querySelectorAll( 'a.uagb-tabs-list' );
			const selectedAnchor = selectedLi.querySelector( 'a.uagb-tabs-list' );

			// Remove old li active class.
			listPanel.querySelector( '.uagb-tabs__active' ).classList.remove( 'uagb-tabs__active' );

			// Remove old tab body active class.
			document
				.querySelector( mainWrapClass + ' > .uagb-tabs__body-wrap > .uagb-tabs-body__active' )
				.classList.remove( 'uagb-tabs-body__active' );

			// Set aria-selected attribute as flase for old active tab.
			for ( let i = 0; i < allLi.length; i++ ) {
				allLi[ i ].setAttribute( 'aria-selected', false );
			}

			// Set selected li active class.
			selectedLi.classList.add( 'uagb-tabs__active' );

			// Set aria-selected attribute as true for new active tab.
			selectedAnchor.setAttribute( 'aria-selected', true );

			// Set selected tab body active class.
			tabSelectedBody.classList.add( 'uagb-tabs-body__active' );

			// Set aria-hidden attribute false for selected tab body.
			tabSelectedBody.setAttribute( 'aria-hidden', false );

			// Set aria-hidden attribute true for all unselected tab body.
			for ( let i = 0; i < tabUnselectedBody.length; i++ ) {
				tabUnselectedBody[ i ].setAttribute( 'aria-hidden', true );
			}
		}
	},
	getChildrenWithClass( children, className ) {
		let child = null;
		for ( let i = 0; i < children.length; i++ ) {
			if ( children[ i ].classList.contains( className ) ) {
				child = children[ i ];
				break;
			}
		}
		return child;
	}
};
