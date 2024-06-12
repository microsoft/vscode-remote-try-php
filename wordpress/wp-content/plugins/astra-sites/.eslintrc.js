module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended-with-formatting' ],
	rules: {
		camelcase: 'off',
		'import/no-unresolved': 'off',
		'no-console': 'off',
		'no-alert': 'off',
		'jsx-a11y/click-events-have-key-events': 'off',
		'jsx-a11y/label-has-for': 'off',
		'jsx-a11y/no-static-element-interactions': 'off',
		'jsx-a11y/anchor-is-valid': 'off',
		'jsx-a11y/label-has-associated-control': 'off',
		'jsx-a11y/no-onchange': 'off',
		'@wordpress/no-global-event-listener': 'off',
		'space-before-function-paren': 'off',
		'wrap-iife': 'off',
		'react/jsx-indent-props': 'off',
		//Neet to fix below rule
		'react-hooks/exhaustive-deps': 'off',
		'no-mixed-spaces-and-tabs': 'off',
		'no-mixed-operators': 'off',
		'react/jsx-indent': 'off',
		'jsdoc/valid-types': 'off',
		indent: 'off',
		'@wordpress/i18n-text-domain': [
			'error',
			{
				allowedTextDomain: 'astra-sites',
			},
		],
	},
	overrides: [
		{
			files: [ 'tests/e2e/**/*.js' ],
			extends: [
				'plugin:@wordpress/eslint-plugin/test-e2e',
				'plugin:jest/all',
			],
			settings: {
				jest: {
					version: 26,
				},
			},
			rules: {
				'jest/prefer-lowercase-title': [
					'error',
					{
						ignore: [ 'describe' ],
					},
				],
				'jest/no-hooks': 'off',
				'jest/prefer-expect-assertions': 'off',
				'jest/prefer-inline-snapshots': 'off',
			},
		},
	],
	parserOptions: {
		requireConfigFile: false,
		babelOptions: {
			presets: [ '@wordpress/babel-preset-default' ],
		},
	},
	globals: {
		starterTemplates: true,
		astraSitesVars: true,
		ajaxurl: true,
		starterTemplatesPreview: true,
		jQuery: true,
		wpApiSettings: true,
		starter_templates_zip_preview: true,
		localStorage: true,
		sessionStorage: true,
		navigator: true,
		Image: true,
		requestAnimationFrame: true,
		EventSource: true,
		ResizeObserver: true,
	},
};
