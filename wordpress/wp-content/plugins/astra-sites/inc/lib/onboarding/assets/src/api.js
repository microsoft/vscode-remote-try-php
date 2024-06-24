import cookieAuthApi from './wordpress-rest-api-cookie-auth';
const config = {
	rest_url: window.wpApiSettings.root,
	credentials: { nonce: window.wpApiSettings.nonce },
};

export default new cookieAuthApi( config );
