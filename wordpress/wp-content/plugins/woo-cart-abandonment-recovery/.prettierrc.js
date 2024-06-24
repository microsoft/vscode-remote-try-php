// Import the default config file and expose it in the project root.
// Useful for editor integrations.

const config = require( '@wordpress/prettier-config' );
config.overrides = [
	{
		files: [ '*.css' ],
		options: {
			printWidth: 500,
			singleQuote: false,
		},
	},
];
module.exports = config;
