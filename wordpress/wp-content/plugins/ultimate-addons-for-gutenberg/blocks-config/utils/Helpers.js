/**
 * Get Image Sizes and return an array of Size.
 *
 * @param {Object} sizes - The sizes object.
 * @return {Object} sizeArr - The sizeArr object.
 */

export function getImageSize( sizes ) {
	const sizeArr = [];
	for ( const size in sizes ) {
		if ( sizes.hasOwnProperty( size ) ) {
			const p = { value: size, label: size };
			sizeArr.push( p );
		}
	}
	return sizeArr;
}

export function getIdFromString( label ) {
	return label
		? label
				.toLowerCase()
				.replace( /[^a-zA-Z ]/g, '' )
				.replace( /\s+/g, '-' )
		: '';
}

export function getPanelIdFromRef( ref ) {
	if ( ref.current ) {
		const parentElement = ref.current.parentElement.closest( '.components-panel__body' );
		if ( parentElement && parentElement.querySelector( '.components-panel__body-title' ) ) {
			return getIdFromString( parentElement.querySelector( '.components-panel__body-title' ).textContent );
		}
	}
	return null;
}

export function getNumber( input ) {
    if ( input.includes( '#' ) ) {
        // Handeling special case for padding controls
        return '';
    }
    const regex = /\d+(\.\d+)?/;
    const match = input.match( regex );

    if ( match ) {
        const numberString = match[0];
        const isFloat = numberString.includes( '.' );
        return isFloat ? parseFloat( numberString ) : parseInt( numberString, 10 );
    }
    return parseInt( '' );
}

export function getUnit( input ) {
    if ( typeof input !== 'string' ) {
        return 'px';
    }
    const regex = /(px|em|rem|%)/;
    const match = input.match( regex );
    if ( match ) {
        const unit = match[0];
        if ( ['px', 'em', '%'].includes( unit ) ) {
            return unit;
        } else if ( unit === 'rem' ) {
            return 'em';
        }
    }
    return 'px';
}

export function getUnitDimension( input ) {
    const regex = /(px|%)$/;
    const match = input.match( regex );
    if ( match ) {
        return match[1];
    }
    return 'px';
}

export function convertToPixel( lengthString ) {
    const regex = /\bspacing\s*\|\s*(\d+)\b/;
    const noUnitSlider = lengthString.match( regex );
    if( noUnitSlider ){
        return parseInt( noUnitSlider[1] );
    }
    const match = lengthString.match( /^(\d+(\.\d+)?)\s*(px|rem|em)$/i );
    return match ? parseFloat( match[1] ) * ( match[3].toLowerCase() === 'rem' || match[3].toLowerCase() === 'em' ? 16 : 1 ) : 10;
}

export function parseHeightAttributes( value ) {
    const parts = value ? value.split( '|' ) : [];
    const variablePart = parts.length === 3 ? parts[2].trim() : '';
  
    return variablePart;
  }
  

export const uagbClassNames = ( classes ) => ( classes.filter( Boolean ).join( ' ' ) );

/**
 * Check if object is empty.
 * 
 * @param {Object} obj - The object.
 * @return {boolean} - The result.
 */
export const isEmptyObject = ( obj ) => Object.keys( obj ).length === 0 && obj.constructor === Object;

/**
 * This variable is used as a placeholder kind of value which is used to identify the attribute is a GBS style attribute.
 */
export const GBS_RANDOM_NUMBER = 0.001020304;

/**
 * A function to check if an object is not empty.
 *
 * @function
 *
 * @param {Object} obj - The object to check.
 *
 * @return {boolean} Returns true if the object is not empty, otherwise returns false.
 */
export const isObjectNotEmpty = ( obj ) => {
	return (
		obj &&
		Object.keys( obj ).length > 0 &&
		Object.getPrototypeOf( obj ) === Object.prototype
	);
}

export const uagbDeepClone = ( arrayOrObject ) => JSON.parse( JSON.stringify( arrayOrObject ) );

export const updateUAGDay = ( UAGDay, value ) => {
	const filteredArray = UAGDay.filter( ( i ) => i !== value );
	return filteredArray?.length > 0 ? filteredArray : undefined;
};

/**
 * Retrieves the value at a specified path within an object.
 *
 * This function allows you to access nested properties of an object using a dot-separated path
 * or an array of keys. If the specified path is not valid or the property does not exist,
 * the function returns a default value.
 *
 * @param {Object} getObjectValue - The object from which to retrieve the value.
 * @param {string|Array} path - The path to the desired property, specified as a dot-separated string
 *                             or an array of keys.
 * @param {*} defaultValue - The value to return if the specified path is not valid or the property
 *                          does not exist. This value is returned when the path traversal encounters
 *                          an undefined or null property.
 * @return {*} - The value at the specified path, or the default value if the path is not valid
 *               or the property does not exist.
 *
 * @example
 * const obj = { a: { b: { c: 42 } } };
 *
 * // Using a dot-separated string as the path
 * const value = get(obj, 'a.b.c'); // Returns 42
 *
 * // Using an array of keys as the path
 * const valueArray = get(obj, ['a', 'b', 'c']); // Returns 42
 *
 * // Providing a default value
 * const nonExistentValue = get(obj, 'x.y.z', 'Default'); // Returns 'Default'
 */
export const uagbGetValue = ( getObjectValue, path, defaultValue ) => {
	const keys = Array.isArray( path ) ? path : path.split( '.' );
	let result = getObjectValue;

	for ( const key of keys ) {
		if ( result?.hasOwnProperty( key ) ) {
			result = result[key];
		} else {
			return defaultValue;
		}
	}
	return result;
};

/**
 * Check if current page is customizer page.
 * 
 * @return {boolean} - The result.
 */
export const isCustomizerPage = () => {
    // We need to run this script only on customizer page.
    if ( ! window.location.href.includes( '/customize.php' ) ) {
        return false;
    }

    if ( ! window?.wp?.customize ) {
        return false;
    }

    return true;
}
