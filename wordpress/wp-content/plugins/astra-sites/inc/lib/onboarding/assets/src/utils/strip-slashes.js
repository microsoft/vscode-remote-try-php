/**
 * Strip slashes from a string.
 *
 * @param {string} str String to strip slashes from.
 * @return {string} String with slashes stripped.
 */
export const stripSlashes = ( str ) => {
	return str.replace( /^\/+|\/+$/g, '' );
};
