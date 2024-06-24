async function checkConnectionStatus()
{
	return {
		isConnected: window.tt4b_script_vars.is_connected,// when accessToken exists
		externalData: {
			business_platform: window.tt4b_script_vars.business_platform,
			external_business_id: window.tt4b_script_vars.external_business_id
		},
	};
}

async function loadProfile()
{
	return {
		bcId: window.tt4b_script_vars.bc_id,
		businessPlatform: 'WOO_COMMERCE',
		bc: {
			id: window.tt4b_script_vars.bc_id,
			name: ""
		},
		advId: window.tt4b_script_vars.adv_id,
		advertiser: {
			id: window.tt4b_script_vars.adv_id,
			name: window.tt4b_script_vars.store_name
		},
		pixelCode: window.tt4b_script_vars.pixel_code,
		pixel: {
			pixelCode: window.tt4b_script_vars.pixel_code,
			pixelName: "",
			// advancedMatching is true if both fields below are true and vice versa,
			// partial true/false cases should be deemed as dirty data.
			advancedMatchingFields: {
				email: window.tt4b_script_vars.advanced_matching,
				phoneNumber: window.tt4b_script_vars.advanced_matching,
			},
		},
		catalogId: window.tt4b_script_vars.catalog_id,
	};
}

async function loadCatalog()
{
	return {
		approved: window.tt4b_script_vars.approved,
		processing: window.tt4b_script_vars.processing,
		rejected: window.tt4b_script_vars.rejected,
	}
}


window.external_data = window.tt4b_script_vars.external_data;
tbp.render(
	{
		baseName: window.tt4b_script_vars.base_uri,
		checkConnectionStatus,
		loadProfile,
		loadCatalog,
	},
	'tiktok-for-business-root'
);
