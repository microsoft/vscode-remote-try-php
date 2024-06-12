/**
 * File sticky-sidebar.js.
 *
 * Feature: Sticky Sidebar
 * Description: Calculates offset for sticky sidebar positioning.
 * @package Astra
 * @since x.x.x
 */
(function () {
    'use strict';
    window.astraStickySidebar = {

        /**
         * Check whether the header type is sticky and active or not.
         */
        isStickyHeaderActive: function( header, headerStick ) {
            return ( headerStick && "0" !== headerStick && null !== header );
        },

        /**
         * Get the top offset from header for sticky sidebar start position.
         */
        getOffset: function () {
            let offset = 0;
            const abvHeader         = document.querySelector('.ast-above-header-bar');
            const primaryHeader     = document.querySelector('.ast-primary-header-bar');
            const blwHeader         = document.querySelector('.ast-below-header-bar');
            const desktopBreakpoint = astra_sticky_sidebar.desktop_breakpoint ? parseInt( astra_sticky_sidebar.desktop_breakpoint ) : 922;
            if ( window.innerWidth >= desktopBreakpoint && ( abvHeader || primaryHeader || blwHeader ) ) {
                if ( document.body.classList.contains( 'admin-bar' ) ) {
					offset += 32;
				}
                if ( astra_sticky_sidebar.sticky_header_addon ) {
                    if ( window.astraStickySidebar.isStickyHeaderActive( abvHeader, astra_sticky_sidebar.header_above_stick ) ) {
                        offset += Math.floor( parseInt( astra_sticky_sidebar.header_above_height.desktop ) );
                    }
                    if ( window.astraStickySidebar.isStickyHeaderActive( primaryHeader, astra_sticky_sidebar.header_main_stick ) ) {
                        offset += Math.floor( parseInt( astra_sticky_sidebar.header_height.desktop ) );
                    }
                    if ( window.astraStickySidebar.isStickyHeaderActive( blwHeader, astra_sticky_sidebar.header_below_stick ) ) {
                        offset += Math.floor( parseInt( astra_sticky_sidebar.header_below_height.desktop ) );
                    }
                }
                return offset;
            }
        },

		/**
		 * Initiate the sticky sidebar.
		 */
		activateStickySidebar: function() {
			if ( ! document.body.classList.contains( 'ast-sticky-sidebar' ) ) {
				return;
			}
			const sidebar = document.querySelector( '#secondary .sidebar-main' );
			if ( sidebar && astra_sticky_sidebar.sticky_sidebar_on ) { 
                const offset  = window.astraStickySidebar.getOffset();
				sidebar.style.top = Math.floor( offset + 50 ) + 'px';
                sidebar.style.maxHeight = 'calc( 100vh - ' + Math.floor( offset + 50 ) + 'px )';
			}
		},

        init: function () {
            // Kick off the sticky sidebar activation.
            window.astraStickySidebar.activateStickySidebar();
        }
    }

	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.astraStickySidebar.init );
	} else {
		// The DOM has already been loaded.
		window.astraStickySidebar.init();
	}
})();