import { useRef, useCallback, useMemo } from '@wordpress/element';
import { createPopper } from '@popperjs/core';

const usePopper = ( options ) => {
	const reference = useRef( null );
	const popper = useRef( null );

	const cleanupCallback = useRef( () => {} );

	const instantiatePopper = useCallback( () => {
		if ( ! reference.current ) {
			return;
		}
		if ( ! popper.current ) {
			return;
		}

		if ( cleanupCallback.current ) {
			cleanupCallback.current();
		}

		cleanupCallback.current = createPopper(
			reference.current,
			popper.current,
			options
		).destroy;
	}, [ reference, popper, cleanupCallback, options ] );

	return useMemo(
		() => [
			( referenceDomNode ) => {
				reference.current = referenceDomNode;
				instantiatePopper();
			},
			( popperDomNode ) => {
				popper.current = popperDomNode;
				instantiatePopper();
			},
		],
		[ reference, popper, instantiatePopper ]
	);
};

export default usePopper;
