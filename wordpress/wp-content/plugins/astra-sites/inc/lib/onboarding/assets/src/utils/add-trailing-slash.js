/**
 * Add trailing slash to a string.
 *
 * @param {string} string string to add trailing slash
 * @return {string} string with trailing slash
 */
export const addTrailingSlash = ( string ) => {
	return string.endsWith( '/' ) ? string : string + '/';
};
