// Import the default config file and expose it in the project root.
// Useful for editor integrations.

const config = require( '@wordpress/prettier-config' );

config.overrides = [
	{
		files: [ '*.scss', '*.css' ],
		options: {
			printWidth: 500,
			singleQuote: false,
		},
	},
	{
		files: [ '*.yml' ],
		options: {
			tabWidth: 2,
			useTabs: false,
		},
	},
];

module.exports = config;
