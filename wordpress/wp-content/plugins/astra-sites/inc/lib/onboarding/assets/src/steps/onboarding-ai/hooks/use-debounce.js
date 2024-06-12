const { useState, useEffect, useCallback, useMemo } = wp.element;

/**
 * useDebounce hook to delay the execution of a function by a specified delay time.
 *
 * @param {any}      value    - The value to be debounced.
 * @param {number}   delay    - The delay duration in milliseconds.
 * @param {Function} callback - A callback function to be invoked after the delay.
 */
export const useDebounce = ( value, delay, callback = null ) => {
	const [ debouncedValue, setDebouncedValue ] = useState( value );

	const debouncedCallback = useCallback(
		( callbackValue ) => {
			if ( callback && typeof callback === 'function' ) {
				callback( callbackValue );
			}
			setDebouncedValue( callbackValue );
		},
		[ callback ]
	);

	useEffect( () => {
		const handler = setTimeout( () => {
			debouncedCallback( value );
		}, delay );

		// Cleanup logic to clear the timer when the component unmounts or the value/delay changes
		return () => {
			clearTimeout( handler );
		};
	}, [ value, delay, debouncedCallback ] );

	return useMemo( () => debouncedValue, [ debouncedValue ] );
};

export const useDebounceWithCancel = ( value, delay, callback = null ) => {
	const [ debouncedValue, setDebouncedValue ] = useState( value );
	let handler = null;

	const debouncedCallback = useCallback(
		( _value ) => {
			if ( callback && typeof callback === 'function' ) {
				callback( _value );
			}
			setDebouncedValue( _value );
		},
		[ callback ]
	);

	useEffect( () => {
		handler = setTimeout( () => {
			debouncedCallback( value );
		}, delay );

		// Cleanup logic to clear the timer when the component unmounts or the value/delay changes
		return () => {
			clearTimeout( handler );
		};
	}, [ value, delay, debouncedCallback ] );

	const cancel = () => {
		clearTimeout( handler );
	};

	return [ debouncedValue, cancel ];
};
