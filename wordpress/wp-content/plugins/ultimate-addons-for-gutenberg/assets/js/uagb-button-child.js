UAGBButtonChild = {
	init( $selector ) {
		const block = document.querySelector( $selector );
		if ( ! block ) {
			return;
		}

		block.addEventListener( 'focusin', () => {
			document.addEventListener( 'keydown', this.handleKeyDown );
		} );

		block.addEventListener( 'focusout', () => {
			document.removeEventListener( 'keydown', this.handleKeyDown );
		} );
	},
	handleKeyDown( e ) {
		if ( e.key === ' ' || e.key === 'Spacebar' ) {
			// Checks if the target is an <a> tag with the  uagb specific class
			if ( e.target.tagName === 'A' && e.target.classList.contains( 'uagb-buttons-repeater' ) ) {
				e.preventDefault();
				e.target.click();
			}
		}
	},
};
