const updatingBlock = ['core/group'];

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'astra/meta/groupLayoutSettings',
	(settings, name) => {
		if (!updatingBlock.includes(name)) {
			return settings;
		}

		const newSettings = {
			...settings,
			supports: {
				...(settings.supports || {}),
				layout: {
					...(settings.supports.layout || {}),
					allowEditing: true,
					allowSwitching: false,
					allowInheriting: true,
				},
				__experimentalLayout: {
					...(settings.supports.__experimentalLayout || {}),
					allowEditing: true,
					allowSwitching: false,
					allowInheriting: true,
				},
			},
		};
		return newSettings;
	},
	20
);

// Get the block editor's data module.
const { dispatch } = wp.data;

// Create a function to set the default align attribute
function setWooDefaultAlignments() {
	const checkoutBlocks = wp.blocks.getBlockTypes().some(block => block.name === 'woocommerce/checkout');
	const cartBlocks = wp.blocks.getBlockTypes().some(block => block.name === 'woocommerce/cart');

	if ( checkoutBlocks ) {
	const checkoutBlock = wp.data.select('core/block-editor').getBlocks().find(block => block.name === 'woocommerce/checkout');
		if (checkoutBlock && checkoutBlock.attributes.align !== 'none') {
			const checkoutClientId = checkoutBlock.clientId;
			const checkoutLocalStorageKey = 'hasCheckoutBlockInserted';
			const checkoutLocalStorageData = JSON.parse(localStorage.getItem(checkoutLocalStorageKey)) || {};

			if ( ! checkoutLocalStorageData[checkoutClientId] ) {
				const updatedCheckoutAttributes = { ...checkoutBlock.attributes, align: 'none' };
				dispatch('core/block-editor').updateBlockAttributes(checkoutClientId, updatedCheckoutAttributes);

				checkoutLocalStorageData[checkoutClientId] = true;
				localStorage.setItem(checkoutLocalStorageKey, JSON.stringify(checkoutLocalStorageData));
			}
		}
	}

	if ( cartBlocks ) {
	const cartBlock = wp.data.select('core/block-editor').getBlocks().find(block => block.name === 'woocommerce/cart');
		if (cartBlock && cartBlock.attributes.align !== 'none') {
			const cartClientId = cartBlock.clientId;
			const cartLocalStorageKey = 'hasCartBlockInserted';
			const cartLocalStorageData = JSON.parse(localStorage.getItem(cartLocalStorageKey)) || {};

			if ( ! cartLocalStorageData[cartClientId] ) {
				const updatedCartAttributes = { ...cartBlock.attributes, align: 'none' };
				dispatch('core/block-editor').updateBlockAttributes(cartBlock.clientId, updatedCartAttributes);

				cartLocalStorageData[cartClientId] = true;
				localStorage.setItem(cartLocalStorageKey, JSON.stringify(cartLocalStorageData));
			}
		}
	}
}

// Listen for the first insertion of a WooCommerce block
wp.data.subscribe(() => {
	setWooDefaultAlignments();
});
