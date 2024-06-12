const UAGBBlockPositioning = {
	// Initialize the required positioning functionality.
	init( attr, selector ) {
		const element = document.querySelector( selector );
		if ( element?.classList.contains( 'uagb-position__sticky' ) ) {
			UAGBBlockPositioning.handleSticky( element, attr );
		}
	},

	// Function to handle the sticky positioned element.
	handleSticky( element, attr ) {
		// Add the Adminbar height if needed.
		const getAdminbarHeight = () => {
			const adminBar = document.querySelector( '#wpadminbar' );
			return adminBar?.offsetHeight || 0;
		}

		// Create a filler element for sticky.
		const createStickyFiller = ( elementNode, elementDimensions, elementParent ) => {
			const fillerElement = document.createElement( 'div' );
			fillerElement.style.height = `${ elementDimensions.height }px`;
			// If the sticky element is not restricted to the parent container, then set the width and margin.
			if ( ! elementParent ) {
				fillerElement.style.width = `${ elementDimensions.width }px`;
				fillerElement.style.marginTop = '0';
			// If the sticky element is restricted to the parent container, then set the maxWidth as was intended for the stuck element.
			} else {
				const elementStyles = window.getComputedStyle( elementNode );
				fillerElement.style.width = '100%';
				fillerElement.style.maxWidth = elementStyles.getPropertyValue( 'max-width' ) || `${ elementDimensions.width }px`;
				fillerElement.style.padding = elementStyles.getPropertyValue( 'padding' ) || 0;
				fillerElement.style.margin = elementStyles.getPropertyValue( 'margin' ) || 0;
				fillerElement.style.border = elementStyles.getPropertyValue( 'border' ) || 0;
				fillerElement.style.borderColor = 'transparent';
			}
			return fillerElement;
		};

		// Add the animation attributes to the element and refresh the animations if this was an animated element.
		const applyAnimationData = () => {
			if ( 'undefined' === typeof AOS || ! attr?.UAGAnimationType ) {
				return;
			}
			element.dataset.aos = attr?.UAGAnimationType;
			element.dataset.aosDuration = attr?.UAGAnimationTime;
			element.dataset.aosDelay = attr?.UAGAnimationDelay;
			element.dataset.aosEasing = attr?.UAGAnimationEasing;
			element.dataset.aosOnce = true;
			setTimeout( () => {
				AOS.refreshHard();
			}, 100 );
		}

		// Get the dimensions of the sticky element.
		const stickyDimensions = element.getBoundingClientRect();
		const parentContainer = ! attr?.isBlockRootParent ? element.parentElement : null;
		const fillerElement = createStickyFiller( element, stickyDimensions, parentContainer );
		let haltAt, haltAtPosition, scrollPosition, parentRect;

		// Create the ParentHaltAt and ParentInnerPositions variables.
		const parentHaltAt = { top: 0, bottom: 0 };
		const parentInnerPositions = { top: 0, right: 0, bottom: 0, left: 0 };
		if ( attr?.UAGStickyRestricted ) {
			parentRect = parentContainer.getBoundingClientRect();
			const parentStyles = window.getComputedStyle( parentContainer );
			parentInnerPositions.top = parseInt( parentStyles.getPropertyValue( 'padding-top' ) || 0, 10 );
			parentInnerPositions.bottom = parseInt( parentStyles.getPropertyValue( 'padding-bottom' ) || 0, 10 );
			// To calculate the top position needed for the inner sticky container:
			// Start with the parent's top position.
			// Add the current scroll offset.
			// Add the parent's top padding if any.
			parentHaltAt.top = parentRect.top + ( window.pageYOffset || 0 ) + parentInnerPositions.top;
			// To calculate the top position needed for the inner sticky container:
			// Start with the parent's bottom position.
			// Add the current scroll offset.
			// Subtract the parent's bottom padding if any.
			// Subtract the sticky element's height.
			// Subtract the adminbar height if it exists.
			// Subtract the offset.
			parentHaltAt.bottom = parentRect.bottom + ( window.pageYOffset || 0 ) - parentInnerPositions.bottom - stickyDimensions.height - getAdminbarHeight() - ( attr?.UAGStickyOffset || 0 );
		}

		// Handle the sticky element when it is positioned at the bottom, else handle top.
		if ( 'bottom' === attr?.UAGStickyLocation ) {
			// Stop whn the scroll makes the entire element visible on the current screen.
			haltAt = stickyDimensions.top + ( window.pageYOffset || 0 ) - window.innerHeight + stickyDimensions.height + ( attr?.UAGStickyOffset || 0 );
			// Position the element to the bottom, considering the adminbar.
			haltAtPosition = `${ ( attr?.UAGStickyOffset || 0 ) }px`;

			// Attach to the bottom if needed on load.
			window.addEventListener( 'load', () => {
				scrollPosition = ( window.pageYOffset !== undefined ) ? window.pageYOffset : document.body.scrollTop;
				if ( scrollPosition <= haltAt && ! element.classList.contains( 'uagb-position__sticky--stuck' ) ) {
					element.parentNode.insertBefore( fillerElement, element );
					element.classList.add( 'uagb-position__sticky--stuck' );
					element.style.bottom = `calc(${ haltAtPosition } - ${ window.innerHeight }px)`;
					element.style.left = `${ stickyDimensions.left }px`;
					element.style.width = `${ stickyDimensions.width }px`;
					element.style.zIndex = '999';
					setTimeout( () => {
						element.style.bottom = haltAtPosition;
					} , 50 );
				}

				// Check if this sticky container was animated.
				applyAnimationData();
			} );

			// Check when this needsto be stuck on the bottom, and when it doesn't.
			window.addEventListener( 'scroll', () => {
				scrollPosition = ( window.pageYOffset !== undefined ) ? window.pageYOffset : document.body.scrollTop;
				if ( scrollPosition <= haltAt ) {
					if ( ! element.classList.contains( 'uagb-position__sticky--stuck' ) ) {
						element.parentNode.insertBefore( fillerElement, element );
						element.classList.add( 'uagb-position__sticky--stuck' );
						element.style.bottom = haltAtPosition;
						element.style.left = `${ stickyDimensions.left }px`;
						element.style.width = `${ stickyDimensions.width }px`;
						element.style.zIndex = '999';
					}
				} else if ( scrollPosition > haltAt && element.classList.contains( 'uagb-position__sticky--stuck' ) ) {
					element.parentNode.removeChild( fillerElement );
					element.classList.remove( 'uagb-position__sticky--stuck' );
					element.style.bottom = '';
					element.style.left = '';
					element.style.width = '';
					element.style.zIndex = '';
				}
			} );
		} else {
			// Stop whn the scroll is at the top of the element.
			haltAt = stickyDimensions.top + ( window.pageYOffset || 0 ) - getAdminbarHeight() - ( attr?.UAGStickyOffset || 0 );
			// Position the element to the top, considering the adminbar.
			haltAtPosition = `${ getAdminbarHeight() + ( attr?.UAGStickyOffset || 0 ) }px`;

			// Attach to the top if needed on load.
			window.addEventListener( 'load', () => {
				scrollPosition = ( window.pageYOffset !== undefined ) ? window.pageYOffset : document.body.scrollTop;
				if ( scrollPosition >= haltAt && ! element.classList.contains( 'uagb-position__sticky--stuck' ) ) {
					element.parentNode.insertBefore( fillerElement, element );
					// Add and Remove Opacity for Sticky Containers.
					element.classList.add( 'uagb-position__sticky--stuck' );
					// If this restricted container has crossed the bottom of the parent container on load, then restrict it.
					if ( attr?.UAGStickyRestricted && scrollPosition >= parentHaltAt.bottom ) {
						element.classList.remove( 'uagb-position__sticky--stuck' );
						element.classList.add( 'uagb-position__sticky--restricted' );
						element.style.top = '';
						element.style.bottom = `${ parentInnerPositions.bottom }px`;
						element.style.left = `${ fillerElement?.offsetLeft || 0 }px`;
					// Else, just stick it to the top and transition it to the halt position.
					} else {
						element.style.top = `calc(${ haltAtPosition } - ${ window.innerHeight }px)`
						element.style.left = `${ stickyDimensions.left }px`;
						element.style.top = haltAtPosition;
						
					}					
					element.style.width = `${ stickyDimensions.width }px`;
					element.style.zIndex = '999';
				}

				// Check if this sticky container was animated.
				applyAnimationData();
			} );


			// Check when this needsto be stuck on the top, and when it doesn't.
			window.addEventListener( 'scroll', () => {
				scrollPosition = ( window.pageYOffset !== undefined ) ? window.pageYOffset : document.body.scrollTop;
				// If the scroll position is greater than the current sticky height.
				if ( scrollPosition >= haltAt ) {
					// If the sticky class doesn't yet exist, add the filler and the sticky class.
					if ( ! element.classList.contains( 'uagb-position__sticky--stuck' ) &&  ! element.classList.contains( 'uagb-position__sticky--restricted' ) ) {
						element.parentNode.insertBefore( fillerElement, element );
						element.classList.add( 'uagb-position__sticky--stuck' );
						element.style.top = haltAtPosition;
						element.style.left = `${ stickyDimensions.left }px`;
						element.style.width = `${ stickyDimensions.width }px`;
						element.style.zIndex = '999';
					// Else if the container is struck and the scroll is at the parent bottom, restrict it there.
					} else if ( attr?.UAGStickyRestricted && ! element.classList.contains( 'uagb-position__sticky--restricted' ) && scrollPosition >= parentHaltAt.bottom ) {
						element.classList.remove( 'uagb-position__sticky--stuck' );
						element.classList.add( 'uagb-position__sticky--restricted' );
						element.style.top = '';
						element.style.bottom = `${ parentInnerPositions.bottom }px`;
						element.style.left = `${ fillerElement?.offsetLeft || 0 }px`;
					// Else if the container is already restricted and the scroll has returned above the parent bottom, stick it again.
					} else if ( element.classList.contains( 'uagb-position__sticky--restricted' ) && scrollPosition < parentHaltAt.bottom ) {
						element.classList.remove( 'uagb-position__sticky--restricted' );
						element.classList.add( 'uagb-position__sticky--stuck' );
						element.style.top = haltAtPosition;
						element.style.bottom = '';
						element.style.left = `${ stickyDimensions.left }px`;
						element.style.width = `${ stickyDimensions.width }px`;
						element.style.zIndex = '999';
					}
				} else if ( scrollPosition < haltAt && element.classList.contains( 'uagb-position__sticky--stuck' ) ) {
					element.parentNode.removeChild( fillerElement );
					element.classList.remove( 'uagb-position__sticky--stuck' );
					element.style.top = '';
					element.style.left = '';
					element.style.width = '';
					element.style.zIndex = '';
				}
			} );
		}
	},
};
